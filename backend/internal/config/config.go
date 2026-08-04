package config

import (
	"os"
	"time"
)

// Config menyimpan konfigurasi runtime aplikasi.
type Config struct {
	Port      string        // Port HTTP server
	DBPath    string        // Lokasi file database SQLite
	JWTSecret string        // Secret untuk menandatangani token JWT
	TokenTTL  time.Duration // Umur token JWT
}

// Load membaca konfigurasi dari environment variable dengan fallback default.
func Load() Config {
	return Config{
		Port:      getEnv("PORT", "8080"),
		DBPath:    getEnv("DB_PATH", "coasconnect.db"),
		JWTSecret: getEnv("JWT_SECRET", "dev-secret-ganti-di-produksi"),
		TokenTTL:  24 * time.Hour,
	}
}

func getEnv(key, fallback string) string {
	if v := os.Getenv(key); v != "" {
		return v
	}
	return fallback
}

// ponytail: JWT_SECRET default dipakai hanya untuk dev. Wajib set env unik saat deploy.
