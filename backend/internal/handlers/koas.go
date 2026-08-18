package handlers

import (
	"database/sql"
	"net/http"
)

// KoasHandler menampilkan daftar dokter koas yang bisa dipilih pasien.
type KoasHandler struct {
	db *sql.DB
}

func NewKoasHandler(db *sql.DB) *KoasHandler { return &KoasHandler{db: db} }

// List mengembalikan daftar dokter koas beserta profil (RS, bidang, pembimbing).
func (h *KoasHandler) List(w http.ResponseWriter, r *http.Request) {
	rows, err := h.db.QueryContext(r.Context(),
		"SELECT id, name, COALESCE(hospital, ''), COALESCE(specialty, ''), supervisor_id FROM users WHERE role = 'koas' ORDER BY name")
	if err != nil {
		writeError(w, http.StatusInternalServerError, "gagal mengambil daftar dokter koas")
		return
	}
	defer rows.Close()

	list := []struct {
		ID           int64  `json:"id"`
		Name         string `json:"name"`
		Hospital     string `json:"hospital"`
		Specialty    string `json:"specialty"`
		SupervisorID *int64 `json:"supervisor_id,omitempty"`
	}{}
	for rows.Next() {
		var item struct {
			ID           int64  `json:"id"`
			Name         string `json:"name"`
			Hospital     string `json:"hospital"`
			Specialty    string `json:"specialty"`
			SupervisorID *int64 `json:"supervisor_id,omitempty"`
		}
		var sup sql.NullInt64
		if err := rows.Scan(&item.ID, &item.Name, &item.Hospital, &item.Specialty, &sup); err != nil {
			writeError(w, http.StatusInternalServerError, "gagal membaca daftar dokter koas")
			return
		}
		if sup.Valid {
			item.SupervisorID = &sup.Int64
		}
		list = append(list, item)
	}
	writeJSON(w, http.StatusOK, map[string]any{"data": list})
}
