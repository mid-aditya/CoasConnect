package router

import (
	"bytes"
	"encoding/json"
	"fmt"
	"net/http"
	"net/http/httptest"
	"path/filepath"
	"testing"
	"time"

	"coasconnect/backend/internal/config"
	"coasconnect/backend/internal/database"
)

// alur inti: register pasien → buat kasus (janji temu pertama) → koas catat
// sesi → status kasus pulih → selesai. Plus cek akses (401/403) di tiap peran.
func TestMonitoringFlow(t *testing.T) {
	db, err := database.Open(filepath.Join(t.TempDir(), "test.db"))
	if err != nil {
		t.Fatal(err)
	}
	defer db.Close()
	if err := database.Migrate(db); err != nil {
		t.Fatal(err)
	}
	if err := database.Seed(db); err != nil {
		t.Fatal(err)
	}

	cfg := config.Config{Port: "0", DBPath: "", JWTSecret: "test-secret", TokenTTL: time.Hour}
	srv := httptest.NewServer(New(db, cfg))
	defer srv.Close()

	do := func(method, path, body, token string) (int, map[string]any) {
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
		return res.StatusCode, payload
	}

	register := func(name, email, password string) string {
		status, payload := do("POST", "/api/v1/auth/register",
			`{"name":"`+name+`","email":"`+email+`","password":"`+password+`"}`, "")
		if status != http.StatusCreated {
			t.Fatalf("register %s: status %d (%v)", email, status, payload)
		}
		data := payload["data"].(map[string]any)
		return data["token"].(string)
	}
	login := func(email, password string) string {
		status, payload := do("POST", "/api/v1/auth/login",
			`{"email":"`+email+`","password":"`+password+`"}`, "")
		if status != http.StatusOK {
			t.Fatalf("login %s: status %d (%v)", email, status, payload)
		}
		return payload["data"].(map[string]any)["token"].(string)
	}

	// Route kasus tanpa token harus 401.
	if status, _ := do("GET", "/api/v1/cases", "", ""); status != http.StatusUnauthorized {
		t.Fatalf("cases tanpa token: status %d, ingin 401", status)
	}

	// Register pasien → role harus pasien.
	status, payload := do("POST", "/api/v1/auth/register",
		`{"name":"Pasien Uji","email":"pasien@test.id","password":"rahasia123"}`, "")
	if status != http.StatusCreated {
		t.Fatalf("register: status %d (%v)", status, payload)
	}
	user := payload["data"].(map[string]any)["user"].(map[string]any)
	if user["role"] != "pasien" {
		t.Fatalf("role setelah register: %v, ingin pasien", user["role"])
	}
	pasienToken := register("Pasien Dua", "pasien2@test.id", "rahasia123")

	koasToken := login("koas@coasconnect.id", "koas1234")
	spesialisToken := login("spesialis@coasconnect.id", "spesialis123")

	// Ambil ID dokter koas yang asli (hasil seed).
	_, payload = do("GET", "/api/v1/koas", "", pasienToken)
	koasList := payload["data"].([]any)
	if len(koasList) == 0 {
		t.Fatal("daftar koas kosong")
	}
	koasID := int64(koasList[0].(map[string]any)["id"].(float64))
	createBody := fmt.Sprintf(`{"koas_id":%d,"complaint":"Demam tinggi","scheduled_at":"2026-08-10T09:00:00+07:00"}`, koasID)

	// Koas login sebagai pasien harus ditolak di POST /cases.
	if status, _ := do("POST", "/api/v1/cases", createBody, koasToken); status != http.StatusForbidden {
		t.Fatalf("koas buat kasus: status %d, ingin 403", status)
	}

	// Pasien buat kasus → kasus aktif + janji temu pertama otomatis.
	status, payload = do("POST", "/api/v1/cases", createBody, pasienToken)
	if status != http.StatusCreated {
		t.Fatalf("buat kasus: status %d (%v)", status, payload)
	}
	kasus := payload["data"].(map[string]any)
	if kasus["status"] != "aktif" {
		t.Fatalf("status kasus baru: %v, ingin aktif", kasus["status"])
	}

	// Koas lihat kasusnya, catat sesi pertama.
	status, payload = do("GET", "/api/v1/cases", "", koasToken)
	if status != http.StatusOK {
		t.Fatalf("koas list kasus: status %d", status)
	}
	list := payload["data"].([]any)
	if len(list) != 1 {
		t.Fatalf("koas melihat %d kasus, ingin 1", len(list))
	}

	status, payload = do("GET", "/api/v1/cases/1/appointments", "", koasToken)
	if status != http.StatusOK {
		t.Fatalf("list janji temu: status %d", status)
	}
	appts := payload["data"].([]any)
	if len(appts) != 1 {
		t.Fatalf("jumlah janji temu: %d, ingin 1", len(appts))
	}
	apptID := int64(appts[0].(map[string]any)["id"].(float64))

	// Catat sesi tanpa catatan harus 422.
	if status, _ := do("PATCH", "/api/v1/appointments/"+itoa(apptID), `{"status":"selesai","notes":""}`, koasToken); status != http.StatusUnprocessableEntity {
		t.Fatalf("selesai tanpa catatan: status %d, ingin 422", status)
	}

	// Pasien tidak boleh mencatat sesi (bukan koas).
	if status, _ := do("PATCH", "/api/v1/appointments/"+itoa(apptID), `{"status":"selesai","notes":"x"}`, pasienToken); status != http.StatusForbidden {
		t.Fatalf("pasien catat sesi: status %d, ingin 403", status)
	}

	// Koas catat sesi dengan catatan.
	if status, _ := do("PATCH", "/api/v1/appointments/"+itoa(apptID),
		`{"status":"selesai","notes":"Demam turun, kontrol minggu depan."}`, koasToken); status != http.StatusOK {
		t.Fatalf("catat sesi: status %d, ingin 200", status)
	}

	// Koas tandai pulih, spesialis tutup kasus.
	if status, _ := do("PATCH", "/api/v1/cases/1", `{"status":"pulih"}`, koasToken); status != http.StatusOK {
		t.Fatalf("tandai pulih: status %d", status)
	}
	if status, _ := do("PATCH", "/api/v1/cases/1", `{"status":"selesai"}`, spesialisToken); status != http.StatusOK {
		t.Fatalf("tutup kasus: status %d", status)
	}
	status, payload = do("GET", "/api/v1/cases/1", "", spesialisToken)
	if status != http.StatusOK || payload["data"].(map[string]any)["status"] != "selesai" {
		t.Fatalf("status akhir kasus: %v", payload)
	}

	// Pasien lain tidak boleh melihat kasus ini.
	pasien3 := register("Pasien Tiga", "pasien3@test.id", "rahasia123")
	if status, _ := do("GET", "/api/v1/cases/1", "", pasien3); status != http.StatusForbidden {
		t.Fatalf("pasien lain lihat kasus: status %d, ingin 403", status)
	}
}

func itoa(i int64) string {
	return string(rune('0' + i)) // cukup untuk id 1 digit di test ini
}
