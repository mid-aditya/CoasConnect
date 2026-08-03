package database

import (
	"database/sql"
	"fmt"

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
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    name       TEXT NOT NULL,
    email      TEXT NOT NULL UNIQUE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);
`
	if _, err := db.Exec(schema); err != nil {
		return fmt.Errorf("migrate users: %w", err)
	}
	return nil
}
