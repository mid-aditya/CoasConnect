package handlers

import (
	"database/sql"
	"net/http"
	"strconv"
	"strings"
	"time"

	"github.com/golang-jwt/jwt/v5"
	"golang.org/x/crypto/bcrypt"

	"coasconnect/backend/internal/middleware"
	"coasconnect/backend/internal/models"
)

const minPasswordLen = 8

// AuthHandler menangani registrasi, login, dan penerbitan token JWT.
type AuthHandler struct {
	db        *sql.DB
	jwtSecret []byte
	tokenTTL  time.Duration
}

// NewAuthHandler membuat handler auth.
func NewAuthHandler(db *sql.DB, secret string, ttl time.Duration) *AuthHandler {
	return &AuthHandler{db: db, jwtSecret: []byte(secret), tokenTTL: ttl}
}

// Register membuat user baru (dengan password) dan langsung menerbitkan token.
// Role default pasien; dokter koas bisa mendaftar langsung dengan profil
// RS & bidang (pembimbing diisi kemudian lewat profiling koas).
func (h *AuthHandler) Register(w http.ResponseWriter, r *http.Request) {
	var input struct {
		Name      string `json:"name"`
		Email     string `json:"email"`
		Password  string `json:"password"`
		Role      string `json:"role"` // pasien (default) | koas
		Hospital  string `json:"hospital"`
		Specialty string `json:"specialty"`
	}
	if err := decodeJSON(r, &input); err != nil {
		writeError(w, http.StatusBadRequest, "format body JSON tidak valid")
		return
	}

	input.Name = strings.TrimSpace(input.Name)
	input.Email = strings.TrimSpace(input.Email)
	if input.Name == "" || input.Email == "" {
		writeError(w, http.StatusUnprocessableEntity, "name dan email wajib diisi")
		return
	}
	if len(input.Password) < minPasswordLen {
		writeError(w, http.StatusUnprocessableEntity, "password minimal 8 karakter")
		return
	}

	role := strings.TrimSpace(input.Role)
	if role == "" {
		role = "pasien"
	}
	if role != "pasien" && role != "koas" {
		writeError(w, http.StatusUnprocessableEntity, "role harus pasien atau koas")
		return
	}

	hash, err := bcrypt.GenerateFromPassword([]byte(input.Password), 12)
	if err != nil {
		writeError(w, http.StatusInternalServerError, "gagal memproses password")
		return
	}

	res, err := h.db.ExecContext(r.Context(),
		"INSERT INTO users (name, email, password_hash, role, hospital, specialty) VALUES (?, ?, ?, ?, ?, ?)",
		input.Name, input.Email, string(hash), role,
		strings.TrimSpace(input.Hospital), strings.TrimSpace(input.Specialty))
	if err != nil {
		if isUniqueViolation(err) {
			writeError(w, http.StatusConflict, "email sudah terdaftar")
			return
		}
		writeError(w, http.StatusInternalServerError, "gagal menyimpan user")
		return
	}

	id, _ := res.LastInsertId()
	u, ok := getUserByID(h.db, r, id)
	if !ok {
		writeError(w, http.StatusInternalServerError, "gagal membaca user yang baru dibuat")
		return
	}
	h.writeAuth(w, http.StatusCreated, u)
}

// Login memverifikasi email + password dan menerbitkan token.
func (h *AuthHandler) Login(w http.ResponseWriter, r *http.Request) {
	var input struct {
		Email    string `json:"email"`
		Password string `json:"password"`
	}
	if err := decodeJSON(r, &input); err != nil {
		writeError(w, http.StatusBadRequest, "format body JSON tidak valid")
		return
	}

	u, hash, ok := h.getCredentials(r, strings.TrimSpace(input.Email))
	if !ok {
		writeError(w, http.StatusUnauthorized, "email atau password salah")
		return
	}
	if bcrypt.CompareHashAndPassword([]byte(hash), []byte(input.Password)) != nil {
		writeError(w, http.StatusUnauthorized, "email atau password salah")
		return
	}

	h.writeAuth(w, http.StatusOK, u)
}

// Me mengembalikan data user yang sedang login.
func (h *AuthHandler) Me(w http.ResponseWriter, r *http.Request) {
	u, ok := getUserByID(h.db, r, middleware.UserID(r.Context()))
	if !ok {
		writeError(w, http.StatusUnauthorized, "sesi tidak valid")
		return
	}
	writeJSON(w, http.StatusOK, map[string]any{"data": u})
}

func (h *AuthHandler) getCredentials(r *http.Request, email string) (models.User, string, bool) {
	var u models.User
	var sup sql.NullInt64
	err := h.db.QueryRowContext(r.Context(),
		"SELECT id, name, email, password_hash, role, supervisor_id, hospital, specialty, created_at, updated_at FROM users WHERE email = ?", email).
		Scan(&u.ID, &u.Name, &u.Email, &u.PasswordHash, &u.Role, &sup, &u.Hospital, &u.Specialty, &u.CreatedAt, &u.UpdatedAt)
	if err != nil {
		return u, "", false
	}
	if sup.Valid {
		u.SupervisorID = &sup.Int64
	}
	return u, u.PasswordHash, true
}

// writeAuth mengirim user + token dalam satu response (login & register).
func (h *AuthHandler) writeAuth(w http.ResponseWriter, status int, u models.User) {
	token, err := h.issueToken(u.ID, u.Role)
	if err != nil {
		writeError(w, http.StatusInternalServerError, "gagal menerbitkan token")
		return
	}
	writeJSON(w, status, map[string]any{"data": map[string]any{"user": u, "token": token}})
}

func (h *AuthHandler) issueToken(userID int64, role string) (string, error) {
	now := time.Now()
	claims := jwt.MapClaims{
		"sub":  strconv.FormatInt(userID, 10),
		"role": role,
		"iat":  now.Unix(),
		"exp":  now.Add(h.tokenTTL).Unix(),
	}
	tok := jwt.NewWithClaims(jwt.SigningMethodHS256, claims)
	return tok.SignedString(h.jwtSecret)
}
