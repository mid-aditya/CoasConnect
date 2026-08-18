package database

import (
	"database/sql"
	"fmt"
	"strings"
	"time"

	"golang.org/x/crypto/bcrypt"

	_ "modernc.org/sqlite" // driver SQLite murni Go (tanpa CGO)
)

// Open membuka koneksi ke database SQLite.
func Open(path string) (*sql.DB, error) {
	dsn := fmt.Sprintf("file:%s?_pragma=journal_mode(WAL)&_pragma=foreign_keys(1)", path)
	db, err := sql.Open("sqlite", dsn)
	if err != nil {
		return nil, fmt.Errorf("sql open: %w", err)
	}

	if err := db.Ping(); err != nil {
		db.Close()
		return nil, fmt.Errorf("sql ping: %w", err)
	}

	return db, nil
}

// Migrate membuat skema tabel jika belum ada.
func Migrate(db *sql.DB) error {
	const schema = `
CREATE TABLE IF NOT EXISTS users (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    name          TEXT NOT NULL,
    email         TEXT NOT NULL UNIQUE,
    password_hash TEXT NOT NULL DEFAULT '',
    role          TEXT NOT NULL DEFAULT 'pasien',
    supervisor_id INTEGER REFERENCES users(id),
    created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS cases (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    patient_id    INTEGER NOT NULL REFERENCES users(id),
    koas_id       INTEGER NOT NULL REFERENCES users(id),
    supervisor_id INTEGER NOT NULL REFERENCES users(id),
    complaint     TEXT NOT NULL,
    status        TEXT NOT NULL DEFAULT 'aktif',
    created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    closed_at     DATETIME
);
CREATE TABLE IF NOT EXISTS appointments (
    id           INTEGER PRIMARY KEY AUTOINCREMENT,
    case_id      INTEGER NOT NULL REFERENCES cases(id),
    scheduled_at DATETIME NOT NULL,
    status       TEXT NOT NULL DEFAULT 'terjadwal',
    notes        TEXT NOT NULL DEFAULT '',
    created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);
`
	if _, err := db.Exec(schema); err != nil {
		return fmt.Errorf("migrate schema: %w", err)
	}

	// Migrasi DB lama (kolom role & supervisor_id belum ada). SQLite tak punya
	// ADD COLUMN IF NOT EXISTS, jadi error "duplicate column" diabaikan.
	for _, alter := range []string{
		"ALTER TABLE users ADD COLUMN role TEXT NOT NULL DEFAULT 'pasien'",
		"ALTER TABLE users ADD COLUMN supervisor_id INTEGER REFERENCES users(id)",
	} {
		if _, err := db.Exec(alter); err != nil && !strings.Contains(err.Error(), "duplicate column") {
			return fmt.Errorf("migrate users: %w", err)
		}
	}
	return nil
}

// Seed membuat akun demo dokter koas & spesialis jika belum ada, supaya
// alur monitoring bisa langsung dicoba tanpa akun admin.
// ponytail: manajemen akun resmi (mis. halaman admin) belum ada — tambahkan
// saat role koas/spesialis perlu dibuat lewat UI.
func Seed(db *sql.DB) error {
	var n int
	if err := db.QueryRow("SELECT COUNT(*) FROM users WHERE role IN ('koas', 'spesialis')").Scan(&n); err != nil {
		return fmt.Errorf("seed count: %w", err)
	}
	if n > 0 {
		return seedDemoData(db)
	}

	hashSpesialis, err := bcrypt.GenerateFromPassword([]byte("spesialis123"), 12)
	if err != nil {
		return err
	}
	res, err := db.Exec("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, 'spesialis')",
		"Dr. Sari Wulandari, Sp.PD (Pembimbing)", "spesialis@coasconnect.id", string(hashSpesialis))
	if err != nil {
		return fmt.Errorf("seed spesialis: %w", err)
	}
	supervisorID, _ := res.LastInsertId()

	hashKoas, err := bcrypt.GenerateFromPassword([]byte("koas1234"), 12)
	if err != nil {
		return err
	}
	if _, err := db.Exec("INSERT INTO users (name, email, password_hash, role, supervisor_id) VALUES (?, ?, ?, 'koas', ?)",
		"Andi Pratama (Dokter Koas)", "koas@coasconnect.id", string(hashKoas), supervisorID); err != nil {
		return fmt.Errorf("seed koas: %w", err)
	}
	return seedDemoData(db)
}

// seedDemoData mengisi data dummy untuk dashboard koas & spesialis:
// pasien tambahan, satu koas lagi, kasus di semua status, dan sesi monitoring.
// Idempotent: dilewati bila sudah ada kasus di database.
func seedDemoData(db *sql.DB) error {
	var n int
	if err := db.QueryRow("SELECT COUNT(*) FROM cases").Scan(&n); err != nil {
		return fmt.Errorf("demo count: %w", err)
	}
	if n > 0 {
		return nil
	}

	hashPasien, err := bcrypt.GenerateFromPassword([]byte("pasien1234"), 12)
	if err != nil {
		return err
	}
	hashKoas, err := bcrypt.GenerateFromPassword([]byte("koas1234"), 12)
	if err != nil {
		return err
	}

	pasien := []struct {
		name  string
		email string
	}{
		{"Budi Santoso", "budi@coasconnect.id"},
		{"Siti Aminah", "siti@coasconnect.id"},
		{"Dewi Lestari", "dewi@coasconnect.id"},
		{"Rudi Hartono", "rudi@coasconnect.id"},
	}
	patientIDs := make([]int64, 0, len(pasien))
	for _, p := range pasien {
		res, err := db.Exec("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, 'pasien')",
			p.name, p.email, string(hashPasien))
		if err != nil {
			return fmt.Errorf("seed pasien %s: %w", p.email, err)
		}
		id, _ := res.LastInsertId()
		patientIDs = append(patientIDs, id)
	}

	// Koas kedua agar dashboard pembimbing punya variasi.
	res, err := db.Exec("INSERT INTO users (name, email, password_hash, role, supervisor_id) VALUES (?, ?, ?, 'koas', 1)",
		"Budi Utomo (Dokter Koas)", "koas2@coasconnect.id", string(hashKoas))
	if err != nil {
		return fmt.Errorf("seed koas kedua: %w", err)
	}
	koas2ID, _ := res.LastInsertId()

	// kasus: [pasien, koas, keluhan, status]
	// koas id 2 = Andi Pratama (seed utama)
	cases := []struct {
		patient   int64
		koas      int64
		complaint string
		status    string
	}{
		{patientIDs[0], 2, "Nyeri ulu hati & kembung sejak 1 minggu, sering sendawa", "aktif"},
		{patientIDs[1], 2, "Demam tinggi sejak 3 hari, batuk berdahak kuning", "aktif"},
		{patientIDs[2], 2, "Hipertensi terkontrol, cek rutin bulanan", "pulih"},
		{patientIDs[3], 2, "Nyeri punggung bawah kronis, sudah 3 bulan", "selesai"},
		{patientIDs[0], koas2ID, "Sakit kepala berdenyut sebelah kanan sejak 2 minggu", "aktif"},
		{patientIDs[1], koas2ID, "Gatal-gatal kemerahan di lengan & paha", "aktif"},
		{patientIDs[2], koas2ID, "DMT2 — cek gula darah & edukasi diet", "pulih"},
		{patientIDs[3], koas2ID, "Keseleo pergelangan kaki kanan, sudah membaik", "selesai"},
	}

	caseIDs := make([]int64, 0, len(cases))
	for i, c := range cases {
		res, err := db.Exec("INSERT INTO cases (patient_id, koas_id, supervisor_id, complaint, status) VALUES (?, ?, 1, ?, ?)",
			c.patient, c.koas, c.complaint, c.status)
		if err != nil {
			return fmt.Errorf("seed kasus %d: %w", i, err)
		}
		id, _ := res.LastInsertId()
		caseIDs = append(caseIDs, id)
	}

	// sesi per kasus: [status, catatan] — jadwal dibuat relatif terhadap hari ini
	// agar dashboard tampak "hidup".
	sessions := [][]struct {
		status string
		notes  string
		dayOff int
	}{
		{{status: "selesai", notes: "Anamnesis: nyeri epigastrium, riwayat makan tidak teratur. Dianjurkan pola makan teratur & obat antasida.", dayOff: -6},
			{status: "terjadwal", notes: "", dayOff: 2}},
		{{status: "selesai", notes: "Demam 38,4°C, ronki basah basal kanan. Diberikan antibiotik & paracetamol, kontrol 5 hari.", dayOff: -4},
			{status: "terjadwal", notes: "", dayOff: 3}},
		{{status: "selesai", notes: "TD 130/85. Obat rutin dilanjutkan, edukasi diet rendah garam.", dayOff: -21},
			{status: "selesai", notes: "TD 125/80 — terkontrol. Direkomendasikan evaluasi lanjutan di poliklinik.", dayOff: -7}},
		{{status: "selesai", notes: "Fisioterapi & obat antiinflamasi. Keluhan berkurang signifikan.", dayOff: -40},
			{status: "selesai", notes: "Nyeri hilang. Pasien dinyatakan selesai, ditutup.", dayOff: -12}},
		{{status: "selesai", notes: "Migrain tanpa aura. Istirahat cukup, hindari pemicu, obat sesuai kebutuhan.", dayOff: -5},
			{status: "terjadwal", notes: "", dayOff: 1}},
		{{status: "selesai", notes: "Urtikaria — kemungkinan alergi makanan. Antihistamin & hindari pemicu.", dayOff: -3},
			{status: "dibatalkan", notes: "Pasien berhalangan, dijadwalkan ulang.", dayOff: 0}},
		{{status: "selesai", notes: "GDS 210 mg/dL. Edukasi diet & metformin, kontrol rutin.", dayOff: -25},
			{status: "selesai", notes: "GDS 145 mg/dL. Perbaikan, lanjutkan pola hidup sehat.", dayOff: -10}},
		{{status: "selesai", notes: "Sprain ringan. Istirahat, kompres dingin, elevasi.", dayOff: -30},
			{status: "selesai", notes: "Pasien dinyatakan selesai, ditutup.", dayOff: -14}},
	}

	now := time.Now()
	for i, appts := range sessions {
		for _, a := range appts {
			scheduled := now.AddDate(0, 0, a.dayOff).Truncate(time.Minute)
			if _, err := db.Exec("INSERT INTO appointments (case_id, scheduled_at, status, notes) VALUES (?, ?, ?, ?)",
				caseIDs[i], scheduled.Format(time.RFC3339), a.status, a.notes); err != nil {
				return fmt.Errorf("seed sesi kasus %d: %w", i, err)
			}
		}
	}
	return nil
}
