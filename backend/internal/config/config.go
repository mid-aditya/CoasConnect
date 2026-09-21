package config

import (
	"fmt"
	"os"
	"strings"
	"time"
)

// Config menyimpan konfigurasi runtime aplikasi.
type Config struct {
	Port           string
	DBPath         string
	JWTSecret      string
	TokenTTL       time.Duration
	Environment    string
	AllowedOrigins []string
}

// Load membaca konfigurasi dari environment variable dengan fallback default.
func Load() Config {
	origins := []string{}
	for _, origin := range strings.Split(getEnv("ALLOWED_ORIGINS", "http://localhost:5173"), ",") {
		if origin = strings.TrimSpace(origin); origin != "" {
			origins = append(origins, origin)
		}
	}
	return Config{
		Port:           getEnv("PORT", "8080"),
		DBPath:         getEnv("DB_PATH", "coasconnect.db"),
		JWTSecret:      getEnv("JWT_SECRET", "dev-secret-ganti-di-produksi"),
		TokenTTL:       24 * time.Hour,
		Environment:    getEnv("APP_ENV", "development"),
		AllowedOrigins: origins,
	}
}

func getEnv(key, fallback string) string {
	if v := os.Getenv(key); v != "" {
		return v
	}
	return fallback
}

func (c Config) Validate() error {
	if c.Environment == "production" {
		if c.JWTSecret == "" || c.JWTSecret == "dev-secret-ganti-di-produksi" || len(c.JWTSecret) < 32 {
			return fmt.Errorf("JWT_SECRET wajib unik dan minimal 32 karakter di production")
		}
		if len(c.AllowedOrigins) == 0 {
			return fmt.Errorf("ALLOWED_ORIGINS wajib diisi di production")
		}
	}
	return nil
}
