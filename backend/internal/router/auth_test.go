package router

import (
	"bytes"
	"encoding/json"
	"net/http"
	"net/http/httptest"
	"path/filepath"
	"testing"
	"time"

	"coasconnect/backend/internal/config"
	"coasconnect/backend/internal/database"
)

func TestAuthFlow(t *testing.T) {
	db, err := database.Open(filepath.Join(t.TempDir(), "test.db"))
	if err != nil {
		t.Fatal(err)
	}
	defer db.Close()
	if err := database.Migrate(db); err != nil {
		t.Fatal(err)
	}

	cfg := config.Config{Port: "0", DBPath: "", JWTSecret: "test-secret", TokenTTL: time.Hour}
	srv := httptest.NewServer(New(db, cfg))
	defer srv.Close()

	do := func(method, path, body, token string) (*http.Response, map[string]any) {
		var req *http.Request
		var err error
		if body != "" {
			req, err = http.NewRequest(method, srv.URL+path, bytes.NewBufferString(body))
		} else {
			req, err = http.NewRequest(method, srv.URL+path, nil)
		}
		if err != nil {
			t.Fatalf("%s %s: build request: %v", method, path, err)
		}
		if token != "" {
			req.Header.Set("Authorization", "Bearer "+token)
		}
		res, err := srv.Client().Do(req)
		if err != nil {
			t.Fatalf("%s %s: request: %v", method, path, err)
		}
		defer res.Body.Close()
		var payload map[string]any
		if err := json.NewDecoder(res.Body).Decode(&payload); err != nil {
			t.Fatalf("%s %s: decode body: %v", method, path, err)
		}
		return res, payload
	}

	// Route user tanpa token harus 401.
	res, _ := do("GET", "/api/v1/users", "", "")
	if res.StatusCode != http.StatusUnauthorized {
		t.Fatalf("users tanpa token: status %d, ingin 401", res.StatusCode)
	}

	// Register → otomatis dapat token.
	res, payload := do("POST", "/api/v1/auth/register",
		`{"name":"Uji","email":"uji@test.id","password":"rahasia123"}`, "")
	if res.StatusCode != http.StatusCreated {
		t.Fatalf("register: status %d, ingin 201 (%v)", res.StatusCode, payload)
	}
	data := payload["data"].(map[string]any)
	token := data["token"].(string)
	if token == "" {
		t.Fatal("register tidak mengembalikan token")
	}

	// Login dengan password salah harus 401.
	res, _ = do("POST", "/api/v1/auth/login", `{"email":"uji@test.id","password":"salah123"}`, "")
	if res.StatusCode != http.StatusUnauthorized {
		t.Fatalf("login password salah: status %d, ingin 401", res.StatusCode)
	}

	// Login benar → token baru.
	res, payload = do("POST", "/api/v1/auth/login", `{"email":"uji@test.id","password":"rahasia123"}`, "")
	if res.StatusCode != http.StatusOK {
		t.Fatalf("login: status %d, ingin 200 (%v)", res.StatusCode, payload)
	}

	// Route user dengan token harus 200.
	res, _ = do("GET", "/api/v1/users", "", token)
	if res.StatusCode != http.StatusOK {
		t.Fatalf("users dengan token: status %d, ingin 200", res.StatusCode)
	}
}
