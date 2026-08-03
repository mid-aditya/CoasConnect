package router

import (
	"database/sql"
	"net/http"

	"github.com/go-chi/chi/v5"

	"coasconnect/backend/internal/config"
	"coasconnect/backend/internal/handlers"
	"coasconnect/backend/internal/middleware"
)

// New membangun router HTTP dengan semua route API.
func New(db *sql.DB, cfg config.Config) http.Handler {
	r := chi.NewRouter()

	r.Use(middleware.Recoverer)
	r.Use(middleware.Logger)
	r.Use(middleware.CORS([]string{"*"}))

	r.Get("/", func(w http.ResponseWriter, r *http.Request) {
		w.Header().Set("Content-Type", "application/json")
		_, _ = w.Write([]byte(`{"service":"coasconnect-api","docs":"/api/v1/health"}`))
	})

	healthHandler := handlers.NewHealthHandler()
	userHandler := handlers.NewUserHandler(db)

	r.Route("/api/v1", func(r chi.Router) {
		r.Get("/health", healthHandler.Check)

		r.Route("/users", func(r chi.Router) {
			r.Get("/", userHandler.List)
			r.Post("/", userHandler.Create)
			r.Get("/{id}", userHandler.Get)
			r.Put("/{id}", userHandler.Update)
			r.Delete("/{id}", userHandler.Delete)
		})
	})

	return r
}
