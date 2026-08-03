package handlers

import (
	"encoding/json"
	"net/http"
	"time"
)

// HealthHandler menangani endpoint health check.
type HealthHandler struct {
	StartedAt time.Time
}

// NewHealthHandler membuat handler health check baru.
func NewHealthHandler() *HealthHandler {
	return &HealthHandler{StartedAt: time.Now()}
}

// Check menampilkan status layanan API.
func (h *HealthHandler) Check(w http.ResponseWriter, r *http.Request) {
	writeJSON(w, http.StatusOK, map[string]any{
		"status":   "ok",
		"service":  "coasconnect-api",
		"uptime_s": int64(time.Since(h.StartedAt).Seconds()),
		"time":     time.Now().UTC().Format(time.RFC3339),
	})
}

// writeJSON menulis response JSON dengan header yang benar.
func writeJSON(w http.ResponseWriter, status int, payload any) {
	w.Header().Set("Content-Type", "application/json; charset=utf-8")
	w.WriteHeader(status)
	_ = json.NewEncoder(w).Encode(payload)
}

// writeError menulis response error JSON.
func writeError(w http.ResponseWriter, status int, message string) {
	writeJSON(w, status, map[string]string{"error": message})
}
