package handlers

import (
	"database/sql"
	"net/http"
	"strings"
	"time"

	"coasconnect/backend/internal/middleware"
	"coasconnect/backend/internal/models"
)

// Status janji temu.
const (
	apptTerjadwal  = "terjadwal"
	apptSelesai    = "selesai"
	apptDibatalkan = "dibatalkan"
)

// AppointmentHandler menangani janji temu / sesi monitoring.
type AppointmentHandler struct {
	db *sql.DB
}

func NewAppointmentHandler(db *sql.DB) *AppointmentHandler { return &AppointmentHandler{db: db} }

// List menampilkan sesi monitoring sebuah kasus (untuk siapa pun yang berhak melihat kasus).
func (h *AppointmentHandler) List(w http.ResponseWriter, r *http.Request) {
	caseID, ok := parseID(w, r)
	if !ok {
		return
	}
	v, err := scanCase(h.db.QueryRowContext(r.Context(), caseSelect+" WHERE c.id = ?", caseID))
	if err != nil {
		writeError(w, http.StatusNotFound, "kasus tidak ditemukan")
		return
	}
	if !canView(r, v) {
		writeError(w, http.StatusForbidden, "akses ditolak")
		return
	}

	rows, err := h.db.QueryContext(r.Context(),
		"SELECT id, case_id, scheduled_at, status, notes, created_at FROM appointments WHERE case_id = ? ORDER BY scheduled_at ASC",
		caseID)
	if err != nil {
		writeError(w, http.StatusInternalServerError, "gagal mengambil data janji temu")
		return
	}
	defer rows.Close()

	appts := []models.Appointment{}
	for rows.Next() {
		var a models.Appointment
		if err := rows.Scan(&a.ID, &a.CaseID, &a.ScheduledAt, &a.Status, &a.Notes, &a.CreatedAt); err != nil {
			writeError(w, http.StatusInternalServerError, "gagal membaca data janji temu")
			return
		}
		appts = append(appts, a)
	}
	writeJSON(w, http.StatusOK, map[string]any{"data": appts})
}

// Create membuat janji temu lanjutan — hanya pasien pemilik kasus.
func (h *AppointmentHandler) Create(w http.ResponseWriter, r *http.Request) {
	caseID, ok := parseID(w, r)
	if !ok {
		return
	}
	v, err := scanCase(h.db.QueryRowContext(r.Context(), caseSelect+" WHERE c.id = ?", caseID))
	if err != nil {
		writeError(w, http.StatusNotFound, "kasus tidak ditemukan")
		return
	}
	if middleware.UserRole(r.Context()) != "pasien" || v.PatientID != middleware.UserID(r.Context()) {
		writeError(w, http.StatusForbidden, "akses ditolak")
		return
	}

	var input struct {
		ScheduledAt string `json:"scheduled_at"` // RFC3339
	}
	if err := decodeJSON(r, &input); err != nil {
		writeError(w, http.StatusBadRequest, "format body JSON tidak valid")
		return
	}
	scheduled, err := time.Parse(time.RFC3339, input.ScheduledAt)
	if err != nil {
		writeError(w, http.StatusUnprocessableEntity, "scheduled_at harus format RFC3339")
		return
	}

	res, err := h.db.ExecContext(r.Context(),
		"INSERT INTO appointments (case_id, scheduled_at) VALUES (?, ?)", caseID, scheduled)
	if err != nil {
		writeError(w, http.StatusInternalServerError, "gagal menyimpan janji temu")
		return
	}
	id, _ := res.LastInsertId()

	var a models.Appointment
	if err := h.db.QueryRowContext(r.Context(),
		"SELECT id, case_id, scheduled_at, status, notes, created_at FROM appointments WHERE id = ?", id).
		Scan(&a.ID, &a.CaseID, &a.ScheduledAt, &a.Status, &a.Notes, &a.CreatedAt); err != nil {
		writeError(w, http.StatusInternalServerError, "gagal membaca janji temu baru")
		return
	}
	writeJSON(w, http.StatusCreated, map[string]any{"data": a})
}

// Update mencatat hasil sesi — hanya koas pemilik kasus.
func (h *AppointmentHandler) Update(w http.ResponseWriter, r *http.Request) {
	id, ok := parseID(w, r)
	if !ok {
		return
	}

	var a models.Appointment
	if err := h.db.QueryRowContext(r.Context(),
		"SELECT id, case_id, scheduled_at, status, notes, created_at FROM appointments WHERE id = ?", id).
		Scan(&a.ID, &a.CaseID, &a.ScheduledAt, &a.Status, &a.Notes, &a.CreatedAt); err != nil {
		writeError(w, http.StatusNotFound, "janji temu tidak ditemukan")
		return
	}

	v, err := scanCase(h.db.QueryRowContext(r.Context(), caseSelect+" WHERE c.id = ?", a.CaseID))
	if err != nil {
		writeError(w, http.StatusNotFound, "kasus tidak ditemukan")
		return
	}
	if middleware.UserRole(r.Context()) != "koas" || v.KoasID != middleware.UserID(r.Context()) {
		writeError(w, http.StatusForbidden, "akses ditolak")
		return
	}

	var input struct {
		Status string `json:"status"`
		Notes  string `json:"notes"`
	}
	if err := decodeJSON(r, &input); err != nil {
		writeError(w, http.StatusBadRequest, "format body JSON tidak valid")
		return
	}
	if input.Status != apptSelesai && input.Status != apptDibatalkan {
		writeError(w, http.StatusUnprocessableEntity, "status harus selesai atau dibatalkan")
		return
	}
	if input.Status == apptSelesai && strings.TrimSpace(input.Notes) == "" {
		writeError(w, http.StatusUnprocessableEntity, "catatan sesi wajib diisi saat menyelesaikan janji temu")
		return
	}

	if _, err := h.db.ExecContext(r.Context(),
		"UPDATE appointments SET status = ?, notes = ? WHERE id = ?", input.Status, strings.TrimSpace(input.Notes), id); err != nil {
		writeError(w, http.StatusInternalServerError, "gagal memperbarui janji temu")
		return
	}

	if err := h.db.QueryRowContext(r.Context(),
		"SELECT id, case_id, scheduled_at, status, notes, created_at FROM appointments WHERE id = ?", id).
		Scan(&a.ID, &a.CaseID, &a.ScheduledAt, &a.Status, &a.Notes, &a.CreatedAt); err != nil {
		writeError(w, http.StatusInternalServerError, "gagal membaca janji temu")
		return
	}
	writeJSON(w, http.StatusOK, map[string]any{"data": a})
}
