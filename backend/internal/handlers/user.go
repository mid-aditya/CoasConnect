package handlers

import (
	"database/sql"
	"encoding/json"
	"net/http"
	"strconv"
	"strings"

	"github.com/go-chi/chi/v5"

	"coasconnect/backend/internal/models"
)

// UserHandler menyediakan endpoint CRUD untuk user.
type UserHandler struct {
	db *sql.DB
}

// NewUserHandler membuat handler user dengan koneksi database.
func NewUserHandler(db *sql.DB) *UserHandler {
	return &UserHandler{db: db}
}

// List mengembalikan daftar semua user.
func (h *UserHandler) List(w http.ResponseWriter, r *http.Request) {
	rows, err := h.db.QueryContext(r.Context(),
		"SELECT id, name, email, created_at, updated_at FROM users ORDER BY id DESC")
	if err != nil {
		writeError(w, http.StatusInternalServerError, "gagal mengambil data user")
		return
	}
	defer rows.Close()

	users := []models.User{}
	for rows.Next() {
		var u models.User
		if err := rows.Scan(&u.ID, &u.Name, &u.Email, &u.CreatedAt, &u.UpdatedAt); err != nil {
			writeError(w, http.StatusInternalServerError, "gagal membaca data user")
			return
		}
		users = append(users, u)
	}

	if err := rows.Err(); err != nil {
		writeError(w, http.StatusInternalServerError, "gagal membaca data user")
		return
	}

	writeJSON(w, http.StatusOK, map[string]any{"data": users})
}

// Create menambahkan user baru.
func (h *UserHandler) Create(w http.ResponseWriter, r *http.Request) {
	var input struct {
		Name  string `json:"name"`
		Email string `json:"email"`
	}
	if err := decodeJSON(r, &input); err != nil {
		writeError(w, http.StatusBadRequest, "format body JSON tidak valid")
		return
	}

	input.Name = strings.TrimSpace(input.Name)
	input.Email = strings.TrimSpace(input.Email)
	if input.Name == "" || input.Email == "" {
		writeError(w, http.StatusUnprocessableEntity, "name dan email wajib diisi")
		return
	}

	res, err := h.db.ExecContext(r.Context(),
		"INSERT INTO users (name, email) VALUES (?, ?)", input.Name, input.Email)
	if err != nil {
		if isUniqueViolation(err) {
			writeError(w, http.StatusConflict, "email sudah terdaftar")
			return
		}
		writeError(w, http.StatusInternalServerError, "gagal menyimpan user")
		return
	}

	id, _ := res.LastInsertId()
	u, ok := h.getByID(r, id)
	if !ok {
		writeError(w, http.StatusInternalServerError, "gagal membaca user yang baru dibuat")
		return
	}
	writeJSON(w, http.StatusCreated, map[string]any{"data": u})
}

// Get menampilkan satu user berdasarkan ID.
func (h *UserHandler) Get(w http.ResponseWriter, r *http.Request) {
	id, ok := parseID(w, r)
	if !ok {
		return
	}
	u, found := h.getByID(r, id)
	if !found {
		writeError(w, http.StatusNotFound, "user tidak ditemukan")
		return
	}
	writeJSON(w, http.StatusOK, map[string]any{"data": u})
}

// Update mengubah data user berdasarkan ID.
func (h *UserHandler) Update(w http.ResponseWriter, r *http.Request) {
	id, ok := parseID(w, r)
	if !ok {
		return
	}

	var input struct {
		Name  string `json:"name"`
		Email string `json:"email"`
	}
	if err := decodeJSON(r, &input); err != nil {
		writeError(w, http.StatusBadRequest, "format body JSON tidak valid")
		return
	}

	input.Name = strings.TrimSpace(input.Name)
	input.Email = strings.TrimSpace(input.Email)
	if input.Name == "" || input.Email == "" {
		writeError(w, http.StatusUnprocessableEntity, "name dan email wajib diisi")
		return
	}

	res, err := h.db.ExecContext(r.Context(),
		"UPDATE users SET name = ?, email = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?",
		input.Name, input.Email, id)
	if err != nil {
		if isUniqueViolation(err) {
			writeError(w, http.StatusConflict, "email sudah terdaftar")
			return
		}
		writeError(w, http.StatusInternalServerError, "gagal memperbarui user")
		return
	}

	affected, _ := res.RowsAffected()
	if affected == 0 {
		writeError(w, http.StatusNotFound, "user tidak ditemukan")
		return
	}

	u, _ := h.getByID(r, id)
	writeJSON(w, http.StatusOK, map[string]any{"data": u})
}

// Delete menghapus user berdasarkan ID.
func (h *UserHandler) Delete(w http.ResponseWriter, r *http.Request) {
	id, ok := parseID(w, r)
	if !ok {
		return
	}

	res, err := h.db.ExecContext(r.Context(), "DELETE FROM users WHERE id = ?", id)
	if err != nil {
		writeError(w, http.StatusInternalServerError, "gagal menghapus user")
		return
	}

	affected, _ := res.RowsAffected()
	if affected == 0 {
		writeError(w, http.StatusNotFound, "user tidak ditemukan")
		return
	}

	writeJSON(w, http.StatusOK, map[string]any{"message": "user berhasil dihapus"})
}

func (h *UserHandler) getByID(r *http.Request, id int64) (models.User, bool) {
	var u models.User
	err := h.db.QueryRowContext(r.Context(),
		"SELECT id, name, email, created_at, updated_at FROM users WHERE id = ?", id).
		Scan(&u.ID, &u.Name, &u.Email, &u.CreatedAt, &u.UpdatedAt)
	if err != nil {
		return u, false
	}
	return u, true
}

func parseID(w http.ResponseWriter, r *http.Request) (int64, bool) {
	id, err := strconv.ParseInt(chi.URLParam(r, "id"), 10, 64)
	if err != nil || id <= 0 {
		writeError(w, http.StatusBadRequest, "id tidak valid")
		return 0, false
	}
	return id, true
}

func decodeJSON(r *http.Request, dst any) error {
	defer r.Body.Close()
	return json.NewDecoder(r.Body).Decode(dst)
}

func isUniqueViolation(err error) bool {
	// SQLite melaporkan pelanggaran constraint UNIQUE dengan pesan ini.
	return err != nil && err != sql.ErrNoRows &&
		strings.Contains(strings.ToLower(err.Error()), "unique")
}
