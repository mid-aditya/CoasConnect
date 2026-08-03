package config

import "os"

// Config menyimpan konfigurasi runtime aplikasi.
type Config struct {
	Port   string // Port HTTP server
	DBPath string // Lokasi file database SQLite
}

// Load membaca konfigurasi dari environment variable dengan fallback default.
func Load() Config {
	return Config{
		Port:   getEnv("PORT", "8080"),
		DBPath: getEnv("DB_PATH", "coasconnect.db"),
	}
}

func getEnv(key, fallback string) string {
	if v := os.Getenv(key); v != "" {
		return v
	}
	return fallback
}
