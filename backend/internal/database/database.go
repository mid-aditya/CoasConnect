package database

import (
	"database/sql"
	"fmt"
	"strings"

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
    hospital      TEXT NOT NULL DEFAULT '',
    specialty     TEXT NOT NULL DEFAULT '',
    created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS campaigns (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    koas_id     INTEGER NOT NULL REFERENCES users(id),
    title       TEXT NOT NULL,
    description TEXT NOT NULL DEFAULT '',
    criteria    TEXT NOT NULL DEFAULT '',
    procedure   TEXT NOT NULL DEFAULT '',
    specialty   TEXT NOT NULL DEFAULT '',
    hospital    TEXT NOT NULL DEFAULT '',
    whatsapp    TEXT NOT NULL DEFAULT '',
    status      TEXT NOT NULL DEFAULT 'aktif',
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
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
		"ALTER TABLE users ADD COLUMN hospital TEXT NOT NULL DEFAULT ''",
		"ALTER TABLE users ADD COLUMN specialty TEXT NOT NULL DEFAULT ''",
	} {
		if _, err := db.Exec(alter); err != nil && !strings.Contains(err.Error(), "duplicate column") {
			return fmt.Errorf("migrate users: %w", err)
		}
	}

	// Model lama (kasus + janji temu) diganti kampanye: janji temu & tugas koas
	// kini dialihkan ke WhatsApp. Tabel lama dihapus agar skema tetap bersih.
	for _, drop := range []string{
		"DROP TABLE IF EXISTS appointments",
		"DROP TABLE IF EXISTS cases",
	} {
		if _, err := db.Exec(drop); err != nil {
			return fmt.Errorf("migrate drop lama: %w", err)
		}
	}
	return nil
}

// Seed membuat akun demo dokter koas & spesialis jika belum ada, supaya
// alur kampanye bisa langsung dicoba tanpa akun admin.
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
	if _, err := db.Exec("INSERT INTO users (name, email, password_hash, role, supervisor_id, hospital, specialty) VALUES (?, ?, ?, 'koas', ?, ?, ?)",
		"Andi Pratama (Dokter Koas)", "koas@coasconnect.id", string(hashKoas), supervisorID,
		"RSUD Dr. Soetomo", "Penyakit Dalam"); err != nil {
		return fmt.Errorf("seed koas: %w", err)
	}
	return seedDemoData(db)
}

// seedDemoData mengisi data dummy untuk dashboard koas & spesialis:
// pasien tambahan, satu koas lagi, dan kampanye penjaringan pasien.
// Idempotent: dilewati bila sudah ada kampanye di database.
func seedDemoData(db *sql.DB) error {
	var n int
	if err := db.QueryRow("SELECT COUNT(*) FROM campaigns").Scan(&n); err != nil {
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
	for _, p := range pasien {
		if _, err := db.Exec("INSERT OR IGNORE INTO users (name, email, password_hash, role) VALUES (?, ?, ?, 'pasien')",
			p.name, p.email, string(hashPasien)); err != nil {
			return fmt.Errorf("seed pasien %s: %w", p.email, err)
		}
	}

	// Koas kedua agar dashboard pembimbing punya variasi.
	supervisorID := userIDByEmail(db, "spesialis@coasconnect.id")
	if _, err := db.Exec("INSERT OR IGNORE INTO users (name, email, password_hash, role, supervisor_id) VALUES (?, ?, ?, 'koas', ?)",
		"Budi Utomo (Dokter Koas)", "koas2@coasconnect.id", string(hashKoas), supervisorID); err != nil {
		return fmt.Errorf("seed koas kedua: %w", err)
	}
	// Pastikan profil koas demo terisi (RS & bidang).
	for _, u := range []struct {
		email     string
		hospital  string
		specialty string
	}{
		{"koas@coasconnect.id", "RSUD Dr. Soetomo", "Penyakit Dalam"},
		{"koas2@coasconnect.id", "RSUD Kota Malang", "Anak"},
	} {
		if _, err := db.Exec("UPDATE users SET hospital = ?, specialty = ? WHERE email = ? AND hospital = ''",
			u.hospital, u.specialty, u.email); err != nil {
			return fmt.Errorf("seed profil koas %s: %w", u.email, err)
		}
	}

	koasID := userIDByEmail(db, "koas@coasconnect.id")
	koas2ID := userIDByEmail(db, "koas2@coasconnect.id")

	// Kampanye: koas menawarkan program penjaringan, pasien mendaftar via
	// WhatsApp lalu dibimbing langsung. Janji temu & tugas koas di luar platform.
	campaigns := []struct {
		koasID      int64
		title       string
		description string
		criteria    string
		procedure   string
		specialty   string
		hospital    string
		whatsapp    string
		status      string
	}{
		{koasID, "Program Pendampingan Hipertensi",
			"Pendampingan rutin untuk pasien hipertensi: kontrol tekanan darah, edukasi pola makan, dan evaluasi berkala bersama dokter pembimbing.",
			"Pasien hipertensi usia 40–65 tahun yang ingin kontrol tekanan darah rutin dan edukasi gaya hidup.",
			"1) Hubungi nomor WhatsApp di bawah ini\n2) Screening & konfirmasi kriteria\n3) Pendampingan mingguan selama 4 minggu\n4) Evaluasi hasil & rujukan bila perlu",
			"Penyakit Dalam", "RSUD Dr. Soetomo", "6281234567890", "aktif"},
		{koasID, "Monitoring Diabetes Tipe 2 (DMT2)",
			"Monitoring gula darah dan edukasi diet untuk pasien DMT2, dengan supervisi dokter spesialis penyakit dalam.",
			"Pasien DMT2 yang ingin kontrol rutin gula darah dan siap mengikuti edukasi diet.",
			"1) Chat WhatsApp untuk pendaftaran\n2) Cek riwayat & kondisi awal\n3) Monitoring gula darah + edukasi diet tiap 2 minggu\n4) Evaluasi bersama pembimbing",
			"Penyakit Dalam", "RSUD Dr. Soetomo", "6281234567890", "aktif"},
		{koasID, "Perawatan Nyeri Punggung Bawah",
			"Program edukasi & penanganan awal nyeri punggung bawah kronis.",
			"Pasien dengan nyeri punggung bawah kronis (> 3 bulan) yang ingin penanganan non-operatif.",
			"1) Anamnesis via WhatsApp\n2) Skrining & edukasi postur\n3) Latihan mandiri terpandu\n4) Evaluasi lanjutan",
			"Penyakit Dalam", "RSUD Dr. Soetomo", "6281234567890", "tutup"},
		{koas2ID, "Pendampingan Demam pada Anak",
			"Pendampingan orang tua menangani demam anak di rumah, dengan skrining tanda bahaya dan follow-up.",
			"Orang tua dengan anak usia 1–12 tahun yang mengalami demam berulang tanpa tanda bahaya.",
			"1) Chat WhatsApp\n2) Skrining tanda bahaya\n3) Panduan tatalaksana demam di rumah\n4) Follow-up hari ke-3",
			"Anak", "RSUD Kota Malang", "6281234567891", "aktif"},
		{koas2ID, "Edukasi Gizi & Tumbuh Kembang Balita",
			"Pantau berat badan, ASI, dan MPASI balita bersama dokter koas bidang anak.",
			"Orang tua balita (0–5 tahun) yang ingin memantau tumbuh kembang dan pola gizi.",
			"1) Chat WhatsApp\n2) Isi kuesioner tumbuh kembang\n3) Konsultasi & rencana gizi\n4) Evaluasi bulanan",
			"Anak", "RSUD Kota Malang", "6281234567891", "aktif"},
		{koas2ID, "Manajemen Batuk Pilek pada Anak",
			"Panduan perawatan anak batuk pilek ringan di rumah, tanpa harus ke IGD.",
			"Anak usia 1–10 tahun dengan batuk pilek ringan, tanpa sesak napas.",
			"1) Chat WhatsApp\n2) Skrining gejala\n3) Panduan perawatan di rumah\n4) Follow-up",
			"Anak", "RSUD Kota Malang", "6281234567891", "tutup"},
	}

	for i, c := range campaigns {
		if _, err := db.Exec("INSERT INTO campaigns (koas_id, title, description, criteria, procedure, specialty, hospital, whatsapp, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
			c.koasID, c.title, c.description, c.criteria, c.procedure, c.specialty, c.hospital, c.whatsapp, c.status); err != nil {
			return fmt.Errorf("seed kampanye %d: %w", i, err)
		}
	}
	return nil
}

// userIDByEmail mencari id user dari email (0 bila tidak ditemukan).
func userIDByEmail(db *sql.DB, email string) int64 {
	var id int64
	_ = db.QueryRow("SELECT id FROM users WHERE email = ?", email).Scan(&id)
	return id
}
