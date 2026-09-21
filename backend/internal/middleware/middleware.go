package middleware

import (
	"log"
	"net/http"
	"strconv"
	"strings"
	"sync"
	"time"
)

// SecurityHeaders menambahkan baseline header keamanan HTTP.
func SecurityHeaders(next http.Handler) http.Handler {
	return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		w.Header().Set("X-Content-Type-Options", "nosniff")
		w.Header().Set("X-Frame-Options", "DENY")
		w.Header().Set("Referrer-Policy", "strict-origin-when-cross-origin")
		w.Header().Set("Content-Security-Policy", "default-src 'self'; frame-ancestors 'none'")
		if r.TLS != nil {
			w.Header().Set("Strict-Transport-Security", "max-age=31536000; includeSubDomains")
		}
		next.ServeHTTP(w, r)
	})
}

// BodyLimit membatasi request body agar endpoint tidak mudah dipakai untuk memory DoS.
func BodyLimit(maxBytes int64) func(http.Handler) http.Handler {
	return func(next http.Handler) http.Handler {
		return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
			r.Body = http.MaxBytesReader(w, r.Body, maxBytes)
			next.ServeHTTP(w, r)
		})
	}
}

// RateLimit membatasi request per IP pada endpoint sensitif.
func RateLimit(limit int, window time.Duration) func(http.Handler) http.Handler {
	type entry struct {
		count int
		reset time.Time
	}
	var mu sync.Mutex
	clients := map[string]entry{}
	return func(next http.Handler) http.Handler {
		return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
			key := strings.TrimSpace(strings.Split(r.Header.Get("X-Forwarded-For"), ",")[0])
			if key == "" {
				key = r.RemoteAddr
			}
			now := time.Now()
			mu.Lock()
			e := clients[key]
			if now.After(e.reset) {
				e = entry{reset: now.Add(window)}
			}
			e.count++
			clients[key] = e
			allowed := e.count <= limit
			mu.Unlock()
			if !allowed {
				w.Header().Set("Retry-After", strconv.FormatInt(int64(time.Until(e.reset).Seconds()+1), 10))
				w.Header().Set("Content-Type", "application/json; charset=utf-8")
				w.WriteHeader(http.StatusTooManyRequests)
				_, _ = w.Write([]byte(`{"error":"terlalu banyak percobaan, coba lagi nanti"}`))
				return
			}
			next.ServeHTTP(w, r)
		})
	}
}

// Logger mencatat setiap request masuk.
func Logger(next http.Handler) http.Handler {
	return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		start := time.Now()
		next.ServeHTTP(w, r)
		log.Printf("%s %s %s", r.Method, r.URL.Path, time.Since(start))
	})
}

// Recoverer menangkap panic agar server tidak crash.
func Recoverer(next http.Handler) http.Handler {
	return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		defer func() {
			if rec := recover(); rec != nil {
				log.Printf("panic: %v", rec)
				http.Error(w, `{"error":"internal server error"}`, http.StatusInternalServerError)
			}
		}()
		next.ServeHTTP(w, r)
	})
}

// CORS mengizinkan akses dari origin frontend (web & mobile dev).
func CORS(allowedOrigins []string) func(http.Handler) http.Handler {
	originSet := map[string]bool{}
	for _, o := range allowedOrigins {
		originSet[o] = true
	}

	return func(next http.Handler) http.Handler {
		return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
			origin := r.Header.Get("Origin")
			// Mode "*" mengizinkan semua origin; selain itu hanya origin yang terdaftar.
			switch {
			case originSet["*"]:
				w.Header().Set("Access-Control-Allow-Origin", "*")
			case origin != "" && originSet[origin]:
				w.Header().Set("Access-Control-Allow-Origin", origin)
				w.Header().Set("Access-Control-Allow-Credentials", "true")
				w.Header().Add("Vary", "Origin")
			}
			w.Header().Set("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS")
			w.Header().Set("Access-Control-Allow-Headers", "Content-Type, Authorization, X-Client")
			w.Header().Set("Access-Control-Max-Age", "86400")

			if r.Method == http.MethodOptions {
				w.WriteHeader(http.StatusNoContent)
				return
			}
			next.ServeHTTP(w, r)
		})
	}
}
