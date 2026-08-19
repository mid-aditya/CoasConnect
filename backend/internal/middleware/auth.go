package middleware

import (
	"context"
	"errors"
	"net/http"
	"strconv"
	"strings"

	"github.com/golang-jwt/jwt/v5"
)

type ctxKey int

const (
	userIDKey ctxKey = iota
	userRoleKey
)

// Auth memverifikasi token JWT (Bearer), lalu menyimpan id + role user ke context.
func Auth(secret string) func(http.Handler) http.Handler {
	return func(next http.Handler) http.Handler {
		return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
			hdr := r.Header.Get("Authorization")
			if !strings.HasPrefix(hdr, "Bearer ") {
				writeUnauthorized(w)
				return
			}

			tok, err := jwt.Parse(strings.TrimPrefix(hdr, "Bearer "), func(t *jwt.Token) (any, error) {
				if _, ok := t.Method.(*jwt.SigningMethodHMAC); !ok {
					return nil, errors.New("metode signing tidak dikenal")
				}
				return []byte(secret), nil
			})
			if err != nil || !tok.Valid {
				writeUnauthorized(w)
				return
			}

			sub, err := tok.Claims.GetSubject()
			if err != nil {
				writeUnauthorized(w)
				return
			}
			id, err := strconv.ParseInt(sub, 10, 64)
			if err != nil || id <= 0 {
				writeUnauthorized(w)
				return
			}

			role := ""
			if claims, ok := tok.Claims.(jwt.MapClaims); ok {
				if s, ok := claims["role"].(string); ok {
					role = s
				}
			}

			ctx := context.WithValue(r.Context(), userIDKey, id)
			ctx = context.WithValue(ctx, userRoleKey, role)
			next.ServeHTTP(w, r.WithContext(ctx))
		})
	}
}

// UserID mengambil id user terautentikasi dari context (0 jika tidak ada).
func UserID(ctx context.Context) int64 {
	id, _ := ctx.Value(userIDKey).(int64)
	return id
}

// UserRole mengambil role user terautentikasi dari context.
func UserRole(ctx context.Context) string {
	role, _ := ctx.Value(userRoleKey).(string)
	return role
}

// OptionalAuth seperti Auth, tapi tidak menolak jika token tidak ada.
// Berguna untuk route publik yang tetap ingin mengenali user terautentikasi.
func OptionalAuth(secret string) func(http.Handler) http.Handler {
	return func(next http.Handler) http.Handler {
		return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
			hdr := r.Header.Get("Authorization")
			if !strings.HasPrefix(hdr, "Bearer ") {
				next.ServeHTTP(w, r)
				return
			}

			tok, err := jwt.Parse(strings.TrimPrefix(hdr, "Bearer "), func(t *jwt.Token) (any, error) {
				if _, ok := t.Method.(*jwt.SigningMethodHMAC); !ok {
					return nil, errors.New("metode signing tidak dikenal")
				}
				return []byte(secret), nil
			})
			if err != nil || !tok.Valid {
				next.ServeHTTP(w, r)
				return
			}

			sub, err := tok.Claims.GetSubject()
			if err != nil {
				next.ServeHTTP(w, r)
				return
			}
			id, err := strconv.ParseInt(sub, 10, 64)
			if err != nil || id <= 0 {
				next.ServeHTTP(w, r)
				return
			}

			role := ""
			if claims, ok := tok.Claims.(jwt.MapClaims); ok {
				if s, ok := claims["role"].(string); ok {
					role = s
				}
			}

			ctx := context.WithValue(r.Context(), userIDKey, id)
			ctx = context.WithValue(ctx, userRoleKey, role)
			next.ServeHTTP(w, r.WithContext(ctx))
		})
	}
}

// RequireRole menolak akses jika role user tidak termasuk yang diizinkan.
func RequireRole(roles ...string) func(http.Handler) http.Handler {
	allowed := map[string]bool{}
	for _, r := range roles {
		allowed[r] = true
	}
	return func(next http.Handler) http.Handler {
		return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
			if !allowed[UserRole(r.Context())] {
				w.Header().Set("Content-Type", "application/json; charset=utf-8")
				w.WriteHeader(http.StatusForbidden)
				_, _ = w.Write([]byte(`{"error":"akses ditolak"}`))
				return
			}
			next.ServeHTTP(w, r)
		})
	}
}

func writeUnauthorized(w http.ResponseWriter) {
	w.Header().Set("Content-Type", "application/json; charset=utf-8")
	w.WriteHeader(http.StatusUnauthorized)
	_, _ = w.Write([]byte(`{"error":"autentikasi diperlukan"}`))
}
