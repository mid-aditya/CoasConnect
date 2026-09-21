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

func decodeJSON(r *http.Request, dst any) error {
	defer r.Body.Close()
	return json.NewDecoder(r.Body).Decode(dst)
}

func isUniqueViolation(err error) bool {
	// SQLite melaporkan pelanggaran constraint UNIQUE dengan pesan ini.
	return err != nil && err != sql.ErrNoRows &&
		strings.Contains(strings.ToLower(err.Error()), "unique")
}

func parseID(w http.ResponseWriter, r *http.Request) (int64, bool) {
	id, err := strconv.ParseInt(chi.URLParam(r, "id"), 10, 64)
	if err != nil || id <= 0 {
		writeError(w, http.StatusBadRequest, "id tidak valid")
		return 0, false
	}
	return id, true
}

func getUserByID(db *sql.DB, r *http.Request, id int64) (models.User, bool) {
	var u models.User
	var sup sql.NullInt64
	err := db.QueryRowContext(r.Context(),
		"SELECT id, name, email, password_hash, role, supervisor_id, hospital, specialty, created_at, updated_at FROM users WHERE id = ?", id).
		Scan(&u.ID, &u.Name, &u.Email, &u.PasswordHash, &u.Role, &sup, &u.Hospital, &u.Specialty, &u.CreatedAt, &u.UpdatedAt)
	if err != nil {
		return u, false
	}
	if sup.Valid {
		u.SupervisorID = &sup.Int64
	}
	return u, true
}
