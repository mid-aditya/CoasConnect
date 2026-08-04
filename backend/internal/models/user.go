package models

import "time"

// Peran user di sistem: pasien, dokter koas, atau dokter spesialis (pembimbing).
type User struct {
	ID           int64     `json:"id"`
	Name         string    `json:"name"`
	Email        string    `json:"email"`
	PasswordHash string    `json:"-"` // tidak pernah dikirim ke client
	Role         string    `json:"role"`
	SupervisorID *int64    `json:"supervisor_id,omitempty"` // khusus role koas: pembimbingnya
	CreatedAt    time.Time `json:"created_at"`
	UpdatedAt    time.Time `json:"updated_at"`
}
