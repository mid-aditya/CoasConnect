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

const userIDKey ctxKey = 0

// Auth memverifikasi token JWT (Bearer) dan menyimpan user id ke context.
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

			ctx := context.WithValue(r.Context(), userIDKey, id)
			next.ServeHTTP(w, r.WithContext(ctx))
		})
	}
}

// UserID mengambil id user terautentikasi dari context (0 jika tidak ada).
func UserID(ctx context.Context) int64 {
	id, _ := ctx.Value(userIDKey).(int64)
	return id
}

func writeUnauthorized(w http.ResponseWriter) {
	w.Header().Set("Content-Type", "application/json; charset=utf-8")
	w.WriteHeader(http.StatusUnauthorized)
	_, _ = w.Write([]byte(`{"error":"autentikasi diperlukan"}`))
}
