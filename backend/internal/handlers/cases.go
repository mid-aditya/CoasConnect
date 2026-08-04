package handlers

import (
	"database/sql"
	"net/http"
	"strings"
	"time"

	"coasconnect/backend/internal/middleware"
	"coasconnect/backend/internal/models"
)

// Status kasus perawatan.
const (
	caseAktif   = "aktif"
	casePulih   = "pulih"
	caseSelesai = "selesai"
)

// CaseHandler menangani kasus perawatan (pasien → koas → pembimbing).
type CaseHandler struct {
	db *sql.DB
}

func NewCaseHandler(db *sql.DB) *CaseHandler { return &CaseHandler{db: db} }

const caseSelect = `
SELECT c.id, c.patient_id, c.koas_id, c.supervisor_id, c.complaint, c.status,
       c.created_at, c.updated_at, c.closed_at,
       p.name, k.name, s.name
FROM cases c
JOIN users p ON p.id = c.patient_id
JOIN users k ON k.id = c.koas_id
JOIN users s ON s.id = c.supervisor_id`

type rowScanner interface{ Scan(dest ...any) error }

func scanCase(row rowScanner) (models.CaseView, error) {
	var v models.CaseView
	var closed sql.NullTime
	err := row.Scan(&v.ID, &v.PatientID, &v.KoasID, &v.SupervisorID, &v.Complaint, &v.Status,
		&v.CreatedAt, &v.UpdatedAt, &closed,
		&v.PatientName, &v.KoasName, &v.SupervisorName)
	if closed.Valid {
		v.ClosedAt = &closed.Time
	}
	return v, err
}

// canView memeriksa apakah user berhak melihat kasus (pasien/koas/pembimbing terkait).
func canView(r *http.Request, v models.CaseView) bool {
	uid := middleware.UserID(r.Context())
	switch middleware.UserRole(r.Context()) {
	case "pasien":
		return v.PatientID == uid
	case "koas":
		return v.KoasID == uid
	case "spesialis":
		return v.SupervisorID == uid
	}
	return false
}

// List mengembalikan kasus sesuai peran user.
func (h *CaseHandler) List(w http.ResponseWriter, r *http.Request) {
	uid := middleware.UserID(r.Context())
	var where string
	switch middleware.UserRole(r.Context()) {
	case "pasien":
		where = "c.patient_id = ?"
	case "koas":
		where = "c.koas_id = ?"
	case "spesialis":
		where = "c.supervisor_id = ?"
	default:
		writeError(w, http.StatusForbidden, "akses ditolak")
		return
	}

	rows, err := h.db.QueryContext(r.Context(), caseSelect+" WHERE "+where+" ORDER BY c.id DESC", uid)
	if err != nil {
		writeError(w, http.StatusInternalServerError, "gagal mengambil data kasus")
		return
	}
	defer rows.Close()

	cases := []models.CaseView{}
	for rows.Next() {
		v, err := scanCase(rows)
		if err != nil {
			writeError(w, http.StatusInternalServerError, "gagal membaca data kasus")
			return
		}
		cases = append(cases, v)
	}
	if err := rows.Err(); err != nil {
		writeError(w, http.StatusInternalServerError, "gagal membaca data kasus")
		return
	}

	writeJSON(w, http.StatusOK, map[string]any{"data": cases})
}

// Get menampilkan satu kasus (dengan cek akses).
func (h *CaseHandler) Get(w http.ResponseWriter, r *http.Request) {
	id, ok := parseID(w, r)
	if !ok {
		return
	}
	v, err := scanCase(h.db.QueryRowContext(r.Context(), caseSelect+" WHERE c.id = ?", id))
	if err != nil {
		writeError(w, http.StatusNotFound, "kasus tidak ditemukan")
		return
	}
	if !canView(r, v) {
		writeError(w, http.StatusForbidden, "akses ditolak")
		return
	}
	writeJSON(w, http.StatusOK, map[string]any{"data": v})
}

// Create membuka kasus baru (pasien): pilih dokter koas + keluhan, dan
// otomatis membuat janji temu pertama. Pembimbing = supervisor sang koas.
func (h *CaseHandler) Create(w http.ResponseWriter, r *http.Request) {
	var input struct {
		KoasID      int64  `json:"koas_id"`
		Complaint   string `json:"complaint"`
		ScheduledAt string `json:"scheduled_at"` // RFC3339
	}
	if err := decodeJSON(r, &input); err != nil {
		writeError(w, http.StatusBadRequest, "format body JSON tidak valid")
		return
	}

	input.Complaint = strings.TrimSpace(input.Complaint)
	if input.KoasID <= 0 || input.Complaint == "" {
		writeError(w, http.StatusUnprocessableEntity, "koas_id dan keluhan wajib diisi")
		return
	}
	scheduled, err := time.Parse(time.RFC3339, input.ScheduledAt)
	if err != nil {
		writeError(w, http.StatusUnprocessableEntity, "scheduled_at harus format RFC3339")
		return
	}

	var supervisorID int64
	if err := h.db.QueryRowContext(r.Context(),
		"SELECT supervisor_id FROM users WHERE id = ? AND role = 'koas'", input.KoasID).Scan(&supervisorID); err != nil {
		writeError(w, http.StatusUnprocessableEntity, "dokter koas tidak ditemukan")
		return
	}

	uid := middleware.UserID(r.Context())
	tx, err := h.db.BeginTx(r.Context(), nil)
	if err != nil {
		writeError(w, http.StatusInternalServerError, "gagal membuka kasus")
		return
	}
	defer tx.Rollback()

	res, err := tx.ExecContext(r.Context(),
		"INSERT INTO cases (patient_id, koas_id, supervisor_id, complaint, status) VALUES (?, ?, ?, ?, 'aktif')",
		uid, input.KoasID, supervisorID, input.Complaint)
	if err != nil {
		writeError(w, http.StatusInternalServerError, "gagal menyimpan kasus")
		return
	}
	caseID, _ := res.LastInsertId()

	if _, err := tx.ExecContext(r.Context(),
		"INSERT INTO appointments (case_id, scheduled_at) VALUES (?, ?)", caseID, scheduled); err != nil {
		writeError(w, http.StatusInternalServerError, "gagal menyimpan janji temu")
		return
	}
	if err := tx.Commit(); err != nil {
		writeError(w, http.StatusInternalServerError, "gagal menyimpan kasus")
		return
	}

	v, err := scanCase(h.db.QueryRowContext(r.Context(), caseSelect+" WHERE c.id = ?", caseID))
	if err != nil {
		writeError(w, http.StatusInternalServerError, "gagal membaca kasus baru")
		return
	}
	writeJSON(w, http.StatusCreated, map[string]any{"data": v})
}

// UpdateStatus mengubah status kasus (pulih/selesai) — hanya koas pemilik
// atau spesialis pembimbing.
func (h *CaseHandler) UpdateStatus(w http.ResponseWriter, r *http.Request) {
	id, ok := parseID(w, r)
	if !ok {
		return
	}
	v, err := scanCase(h.db.QueryRowContext(r.Context(), caseSelect+" WHERE c.id = ?", id))
	if err != nil {
		writeError(w, http.StatusNotFound, "kasus tidak ditemukan")
		return
	}

	uid := middleware.UserID(r.Context())
	role := middleware.UserRole(r.Context())
	if !(role == "koas" && v.KoasID == uid) && !(role == "spesialis" && v.SupervisorID == uid) {
		writeError(w, http.StatusForbidden, "akses ditolak")
		return
	}

	var input struct {
		Status string `json:"status"`
	}
	if err := decodeJSON(r, &input); err != nil {
		writeError(w, http.StatusBadRequest, "format body JSON tidak valid")
		return
	}
	if input.Status != casePulih && input.Status != caseSelesai {
		writeError(w, http.StatusUnprocessableEntity, "status harus pulih atau selesai")
		return
	}

	if _, err := h.db.ExecContext(r.Context(),
		"UPDATE cases SET status = ?, updated_at = CURRENT_TIMESTAMP, closed_at = CASE WHEN ? = 'selesai' THEN CURRENT_TIMESTAMP ELSE NULL END WHERE id = ?",
		input.Status, input.Status, id); err != nil {
		writeError(w, http.StatusInternalServerError, "gagal memperbarui status")
		return
	}

	updated, _ := scanCase(h.db.QueryRowContext(r.Context(), caseSelect+" WHERE c.id = ?", id))
	writeJSON(w, http.StatusOK, map[string]any{"data": updated})
}
