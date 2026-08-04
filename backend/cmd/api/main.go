package main

import (
	"log"
	"net/http"

	"coasconnect/backend/internal/config"
	"coasconnect/backend/internal/database"
	"coasconnect/backend/internal/router"
)

func main() {
	cfg := config.Load()

	db, err := database.Open(cfg.DBPath)
	if err != nil {
		log.Fatalf("gagal membuka database: %v", err)
	}
	defer db.Close()

	if err := database.Migrate(db); err != nil {
		log.Fatalf("gagal migrasi database: %v", err)
	}

	if err := database.Seed(db); err != nil {
		log.Fatalf("gagal seed database: %v", err)
	}

	r := router.New(db, cfg)

	addr := ":" + cfg.Port
	log.Printf("CoasConnect API berjalan di http://localhost%s", addr)

	srv := &http.Server{
		Addr:    addr,
		Handler: r,
	}

	if err := srv.ListenAndServe(); err != nil && err != http.ErrServerClosed {
		log.Fatalf("server error: %v", err)
	}
}
