package database

import (
	"database/sql"
	"fmt"
	"strings"

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
    created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);
`
	if _, err := db.Exec(schema); err != nil {
		return fmt.Errorf("migrate users: %w", err)
	}

	// Migrasi DB lama (kolom password_hash belum ada). SQLite tak punya
	// ADD COLUMN IF NOT EXISTS, jadi error "duplicate column" diabaikan.
	if _, err := db.Exec("ALTER TABLE users ADD COLUMN password_hash TEXT NOT NULL DEFAULT ''"); err != nil && !strings.Contains(err.Error(), "duplicate column") {
		return fmt.Errorf("migrate users password_hash: %w", err)
	}
	return nil
}
