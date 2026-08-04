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
		return nil
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
	return nil
}
