package models

import "time"

// Case adalah kasus perawatan: pasien ditangani dokter koas dan
// disupervisi dokter spesialis, dari janji temu pertama sampai pulih/selesai.
type Case struct {
	ID           int64      `json:"id"`
	PatientID    int64      `json:"patient_id"`
	KoasID       int64      `json:"koas_id"`
	SupervisorID int64      `json:"supervisor_id"`
	Complaint    string     `json:"complaint"`
	Status       string     `json:"status"` // aktif | pulih | selesai
	CreatedAt    time.Time  `json:"created_at"`
	UpdatedAt    time.Time  `json:"updated_at"`
	ClosedAt     *time.Time `json:"closed_at,omitempty"`
}

// CaseView adalah kasus + nama pemangku peran, untuk listing & detail.
type CaseView struct {
	Case
	PatientName    string `json:"patient_name"`
	KoasName       string `json:"koas_name"`
	SupervisorName string `json:"supervisor_name"`
}

// Appointment adalah janji temu sekaligus sesi monitoring.
type Appointment struct {
	ID          int64     `json:"id"`
	CaseID      int64     `json:"case_id"`
	ScheduledAt time.Time `json:"scheduled_at"`
	Status      string    `json:"status"` // terjadwal | selesai | dibatalkan
	Notes       string    `json:"notes"`
	CreatedAt   time.Time `json:"created_at"`
}
