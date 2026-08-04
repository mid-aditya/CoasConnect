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
	authHandler := handlers.NewAuthHandler(db, cfg.JWTSecret, cfg.TokenTTL)
	caseHandler := handlers.NewCaseHandler(db)
	appointmentHandler := handlers.NewAppointmentHandler(db)
	koasHandler := handlers.NewKoasHandler(db)

	r.Route("/api/v1", func(r chi.Router) {
		r.Get("/health", healthHandler.Check)

		r.Post("/auth/register", authHandler.Register)
		r.Post("/auth/login", authHandler.Login)

		// Route berikut butuh autentikasi.
		r.Group(func(r chi.Router) {
			r.Use(middleware.Auth(cfg.JWTSecret))

			r.Get("/auth/me", authHandler.Me)
			r.Get("/koas", koasHandler.List)

			// Hanya pasien yang membuka kasus baru.
			r.Group(func(r chi.Router) {
				r.Use(middleware.RequireRole("pasien"))
				r.Post("/cases", caseHandler.Create)
			})

			r.Get("/cases", caseHandler.List)
			r.Get("/cases/{id}", caseHandler.Get)
			r.Patch("/cases/{id}", caseHandler.UpdateStatus)
			r.Get("/cases/{id}/appointments", appointmentHandler.List)
			r.Post("/cases/{id}/appointments", appointmentHandler.Create)
			r.Patch("/appointments/{id}", appointmentHandler.Update)
		})
	})

	return r
}
