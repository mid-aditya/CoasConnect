package models

import "time"

// Campaign adalah kampanye penjaringan pasien oleh dokter koas: pasien
// mendaftar mengikuti prosedur di kampanye dan dibimbing langsung oleh koas.
// Janji temu & tugas koas dialihkan ke WhatsApp — platform ini hanya untuk
// mencari & mendistribusikan pasien.
type Campaign struct {
	ID          int64     `json:"id"`
	KoasID      int64     `json:"koas_id"`
	Title       string    `json:"title"`
	Description string    `json:"description"`
	Criteria    string    `json:"criteria"`
	Procedure   string    `json:"procedure"`
	Specialty   string    `json:"specialty"`
	Hospital    string    `json:"hospital"`
	Whatsapp    string    `json:"whatsapp"`
	Status      string    `json:"status"` // aktif | tutup
	CreatedAt   time.Time `json:"created_at"`
	UpdatedAt   time.Time `json:"updated_at"`
}

// CampaignView adalah kampanye + identitas koas pemilik (nama & pembimbing).
type CampaignView struct {
	Campaign
	KoasName       string `json:"koas_name"`
	SupervisorName string `json:"supervisor_name,omitempty"`
}
