package handlers

import (
	"database/sql"
	"net/http"
	"strings"

	"coasconnect/backend/internal/middleware"
	"coasconnect/backend/internal/models"
)

// Status kampanye penjaringan pasien.
const (
	campaignAktif = "aktif"
	campaignTutup = "tutup"
)

// CampaignHandler menangani kampanye penjaringan pasien oleh dokter koas.
type CampaignHandler struct {
	db *sql.DB
}

func NewCampaignHandler(db *sql.DB) *CampaignHandler { return &CampaignHandler{db: db} }

const campaignSelect = `
SELECT c.id, c.koas_id, c.title, c.description, c.criteria, c.procedure,
       c.specialty, c.hospital, c.whatsapp, c.status, c.created_at, c.updated_at,
       k.name, COALESCE(s.name, '')
FROM campaigns c
JOIN users k ON k.id = c.koas_id
LEFT JOIN users s ON s.id = k.supervisor_id`

type rowScanner interface{ Scan(dest ...any) error }

func scanCampaign(row rowScanner) (models.CampaignView, error) {
	var v models.CampaignView
	err := row.Scan(&v.ID, &v.KoasID, &v.Title, &v.Description, &v.Criteria, &v.Procedure,
		&v.Specialty, &v.Hospital, &v.Whatsapp, &v.Status, &v.CreatedAt, &v.UpdatedAt,
		&v.KoasName, &v.SupervisorName)
	return v, err
}

// List mengembalikan kampanye sesuai peran: pasien melihat semua yang aktif,
// koas melihat miliknya sendiri dengan ?mine=true, spesialis melihat kampanye
// koas di bawah supervisinya.
func (h *CampaignHandler) List(w http.ResponseWriter, r *http.Request) {
	query := campaignSelect + " WHERE c.status = 'aktif' ORDER BY c.id DESC"
	var args []any

	switch middleware.UserRole(r.Context()) {
	case "koas":
		if r.URL.Query().Get("mine") == "true" {
			query = campaignSelect + " WHERE c.koas_id = ? ORDER BY c.id DESC"
			args = append(args, middleware.UserID(r.Context()))
		}
	case "spesialis":
		query = campaignSelect + " WHERE k.supervisor_id = ? ORDER BY c.id DESC"
		args = append(args, middleware.UserID(r.Context()))
	}

	rows, err := h.db.QueryContext(r.Context(), query, args...)
	if err != nil {
		writeError(w, http.StatusInternalServerError, "gagal mengambil data kampanye")
		return
	}
	defer rows.Close()

	list := []models.CampaignView{}
	for rows.Next() {
		v, err := scanCampaign(rows)
		if err != nil {
			writeError(w, http.StatusInternalServerError, "gagal membaca data kampanye")
			return
		}
		list = append(list, v)
	}
	if err := rows.Err(); err != nil {
		writeError(w, http.StatusInternalServerError, "gagal membaca data kampanye")
		return
	}

	writeJSON(w, http.StatusOK, map[string]any{"data": list})
}

// Get menampilkan satu kampanye (semua role terautentikasi boleh melihat).
func (h *CampaignHandler) Get(w http.ResponseWriter, r *http.Request) {
	id, ok := parseID(w, r)
	if !ok {
		return
	}
	v, err := scanCampaign(h.db.QueryRowContext(r.Context(), campaignSelect+" WHERE c.id = ?", id))
	if err != nil {
		writeError(w, http.StatusNotFound, "kampanye tidak ditemukan")
		return
	}
	writeJSON(w, http.StatusOK, map[string]any{"data": v})
}

// Create membuat kampanye baru — hanya dokter koas (dijaga middleware route).
// RS & bidang diambil dari profil koas bila tidak dikirim.
func (h *CampaignHandler) Create(w http.ResponseWriter, r *http.Request) {
	var input struct {
		Title       string `json:"title"`
		Description string `json:"description"`
		Criteria    string `json:"criteria"`
		Procedure   string `json:"procedure"`
		Specialty   string `json:"specialty"`
		Hospital    string `json:"hospital"`
		Whatsapp    string `json:"whatsapp"`
	}
	if err := decodeJSON(r, &input); err != nil {
		writeError(w, http.StatusBadRequest, "format body JSON tidak valid")
		return
	}

	input.Title = strings.TrimSpace(input.Title)
	input.Description = strings.TrimSpace(input.Description)
	input.Criteria = strings.TrimSpace(input.Criteria)
	input.Procedure = strings.TrimSpace(input.Procedure)
	input.Specialty = strings.TrimSpace(input.Specialty)
	input.Hospital = strings.TrimSpace(input.Hospital)
	input.Whatsapp = strings.TrimSpace(input.Whatsapp)
	if input.Title == "" || input.Criteria == "" {
		writeError(w, http.StatusUnprocessableEntity, "title dan criteria wajib diisi")
		return
	}
	if input.Whatsapp == "" {
		writeError(w, http.StatusUnprocessableEntity, "whatsapp wajib diisi (nomor tujuan pasien mendaftar)")
		return
	}

	// Fallback RS & bidang dari profil koas.
	if input.Hospital == "" || input.Specialty == "" {
		var hospital, specialty string
		_ = h.db.QueryRowContext(r.Context(),
			"SELECT hospital, specialty FROM users WHERE id = ?", middleware.UserID(r.Context())).
			Scan(&hospital, &specialty)
		if input.Hospital == "" {
			input.Hospital = hospital
		}
		if input.Specialty == "" {
			input.Specialty = specialty
		}
	}

	res, err := h.db.ExecContext(r.Context(),
		"INSERT INTO campaigns (koas_id, title, description, criteria, procedure, specialty, hospital, whatsapp, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'aktif')",
		middleware.UserID(r.Context()), input.Title, input.Description, input.Criteria,
		input.Procedure, input.Specialty, input.Hospital, input.Whatsapp)
	if err != nil {
		writeError(w, http.StatusInternalServerError, "gagal menyimpan kampanye")
		return
	}
	id, _ := res.LastInsertId()

	v, err := scanCampaign(h.db.QueryRowContext(r.Context(), campaignSelect+" WHERE c.id = ?", id))
	if err != nil {
		writeError(w, http.StatusInternalServerError, "gagal membaca kampanye baru")
		return
	}
	writeJSON(w, http.StatusCreated, map[string]any{"data": v})
}

// Update mengubah kampanye (termasuk status aktif/tutup) — hanya koas pemilik.
func (h *CampaignHandler) Update(w http.ResponseWriter, r *http.Request) {
	id, ok := parseID(w, r)
	if !ok {
		return
	}

	var cur struct {
		Title, Description, Criteria, Procedure, Specialty, Hospital, Whatsapp, Status string
	}
	err := h.db.QueryRowContext(r.Context(),
		"SELECT title, description, criteria, procedure, specialty, hospital, whatsapp, status FROM campaigns WHERE id = ?", id).
		Scan(&cur.Title, &cur.Description, &cur.Criteria, &cur.Procedure, &cur.Specialty, &cur.Hospital, &cur.Whatsapp, &cur.Status)
	if err != nil {
		writeError(w, http.StatusNotFound, "kampanye tidak ditemukan")
		return
	}

	var koasID int64
	if err := h.db.QueryRowContext(r.Context(), "SELECT koas_id FROM campaigns WHERE id = ?", id).Scan(&koasID); err != nil {
		writeError(w, http.StatusNotFound, "kampanye tidak ditemukan")
		return
	}
	if koasID != middleware.UserID(r.Context()) {
		writeError(w, http.StatusForbidden, "akses ditolak")
		return
	}

	var input struct {
		Title       string `json:"title"`
		Description string `json:"description"`
		Criteria    string `json:"criteria"`
		Procedure   string `json:"procedure"`
		Specialty   string `json:"specialty"`
		Hospital    string `json:"hospital"`
		Whatsapp    string `json:"whatsapp"`
		Status      string `json:"status"`
	}
	if err := decodeJSON(r, &input); err != nil {
		writeError(w, http.StatusBadRequest, "format body JSON tidak valid")
		return
	}
	if input.Status != "" && input.Status != campaignAktif && input.Status != campaignTutup {
		writeError(w, http.StatusUnprocessableEntity, "status harus aktif atau tutup")
		return
	}

	if t := strings.TrimSpace(input.Title); t != "" {
		cur.Title = t
	}
	if t := strings.TrimSpace(input.Description); t != "" {
		cur.Description = t
	}
	if t := strings.TrimSpace(input.Criteria); t != "" {
		cur.Criteria = t
	}
	if t := strings.TrimSpace(input.Procedure); t != "" {
		cur.Procedure = t
	}
	if t := strings.TrimSpace(input.Specialty); t != "" {
		cur.Specialty = t
	}
	if t := strings.TrimSpace(input.Hospital); t != "" {
		cur.Hospital = t
	}
	if t := strings.TrimSpace(input.Whatsapp); t != "" {
		cur.Whatsapp = t
	}
	if input.Status != "" {
		cur.Status = input.Status
	}

	if _, err := h.db.ExecContext(r.Context(),
		"UPDATE campaigns SET title = ?, description = ?, criteria = ?, procedure = ?, specialty = ?, hospital = ?, whatsapp = ?, status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?",
		cur.Title, cur.Description, cur.Criteria, cur.Procedure, cur.Specialty, cur.Hospital, cur.Whatsapp, cur.Status, id); err != nil {
		writeError(w, http.StatusInternalServerError, "gagal memperbarui kampanye")
		return
	}

	v, err := scanCampaign(h.db.QueryRowContext(r.Context(), campaignSelect+" WHERE c.id = ?", id))
	if err != nil {
		writeError(w, http.StatusInternalServerError, "gagal membaca kampanye")
		return
	}
	writeJSON(w, http.StatusOK, map[string]any{"data": v})
}
