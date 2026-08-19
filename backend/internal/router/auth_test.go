package router

import (
	"bytes"
	"encoding/json"
	"net/http"
	"net/http/httptest"
	"path/filepath"
	"strconv"
	"testing"
	"time"

	"coasconnect/backend/internal/config"
	"coasconnect/backend/internal/database"
)

// alur inti: register pasien & koas → koas buat kampanye → pasien melihat &
// mendaftar via WhatsApp → koas menutup kampanye. Plus cek akses (401/403).
func TestCampaignFlow(t *testing.T) {
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
	login := func(email, password string) string {
		status, payload := do("POST", "/api/v1/auth/login",
			`{"email":"`+email+`","password":"`+password+`"}`, "")
		if status != http.StatusOK {
			t.Fatalf("login %s: status %d (%v)", email, status, payload)
		}
		return payload["data"].(map[string]any)["token"].(string)
	}

	// Kampanye bersifat publik — tanpa token harus 200.
	if status, _ := do("GET", "/api/v1/campaigns", "", ""); status != http.StatusOK {
		t.Fatalf("campaigns tanpa token: status %d, ingin 200", status)
	}

	// Register pasien → role harus pasien.
	status, payload := do("POST", "/api/v1/auth/register",
		`{"name":"Pasien Uji","email":"pasien@test.id","password":"rahasia123"}`, "")
	if status != http.StatusCreated {
		t.Fatalf("register pasien: status %d (%v)", status, payload)
	}
	user := payload["data"].(map[string]any)["user"].(map[string]any)
	if user["role"] != "pasien" {
		t.Fatalf("role setelah register: %v, ingin pasien", user["role"])
	}
	pasienToken := payload["data"].(map[string]any)["token"].(string)

	// Register koas → role koas + profil RS/bidang tersimpan.
	status, payload = do("POST", "/api/v1/auth/register",
		`{"name":"Koas Uji","email":"koasuji@test.id","password":"rahasia123","role":"koas","hospital":"RSUD Uji","specialty":"Bedah"}`, "")
	if status != http.StatusCreated {
		t.Fatalf("register koas: status %d (%v)", status, payload)
	}
	koasUser := payload["data"].(map[string]any)["user"].(map[string]any)
	if koasUser["role"] != "koas" {
		t.Fatalf("role koas: %v, ingin koas", koasUser["role"])
	}
	if koasUser["hospital"] != "RSUD Uji" || koasUser["specialty"] != "Bedah" {
		t.Fatalf("profil koas tidak tersimpan: %v", koasUser)
	}

	koasToken := login("koas@coasconnect.id", "koas1234")
	koas2Token := login("koas2@coasconnect.id", "koas1234")
	spesialisToken := login("spesialis@coasconnect.id", "spesialis123")

	createBody := `{"title":"Program Uji","description":"Deskripsi uji","criteria":"Kriteria uji","procedure":"1) Chat WA\\n2) Ikuti arahan","whatsapp":"6281234567899"}`

	// Pasien tidak boleh membuat kampanye.
	if status, _ := do("POST", "/api/v1/campaigns", createBody, pasienToken); status != http.StatusForbidden {
		t.Fatalf("pasien buat kampanye: status %d, ingin 403", status)
	}

	// Koas buat kampanye → aktif, RS diambil dari profil (fallback).
	status, payload = do("POST", "/api/v1/campaigns", createBody, koasToken)
	if status != http.StatusCreated {
		t.Fatalf("buat kampanye: status %d (%v)", status, payload)
	}
	kampanye := payload["data"].(map[string]any)
	if kampanye["status"] != "aktif" {
		t.Fatalf("status kampanye baru: %v, ingin aktif", kampanye["status"])
	}
	if kampanye["hospital"] != "RSUD Dr. Soetomo" {
		t.Fatalf("fallback RS dari profil koas gagal: %v", kampanye["hospital"])
	}
	campaignID := int64(kampanye["id"].(float64))

	// Pasien melihat kampanye di daftar umum.
	status, payload = do("GET", "/api/v1/campaigns", "", pasienToken)
	if status != http.StatusOK {
		t.Fatalf("pasien list kampanye: status %d", status)
	}
	if !containsCampaign(payload["data"].([]any), "Program Uji") {
		t.Fatalf("kampanye baru tidak muncul di daftar pasien")
	}

	// Detail kampanye boleh dilihat spesialis.
	status, payload = do("GET", "/api/v1/campaigns/"+itoa(campaignID), "", spesialisToken)
	if status != http.StatusOK {
		t.Fatalf("spesialis lihat detail: status %d", status)
	}
	if payload["data"].(map[string]any)["title"] != "Program Uji" {
		t.Fatalf("detail kampanye salah: %v", payload["data"])
	}

	// Koas lain tidak boleh mengubah kampanye milik orang lain.
	if status, _ := do("PATCH", "/api/v1/campaigns/"+itoa(campaignID), `{"status":"tutup"}`, koas2Token); status != http.StatusForbidden {
		t.Fatalf("koas lain ubah kampanye: status %d, ingin 403", status)
	}
	// Pasien juga tidak boleh (route khusus koas).
	if status, _ := do("PATCH", "/api/v1/campaigns/"+itoa(campaignID), `{"status":"tutup"}`, pasienToken); status != http.StatusForbidden {
		t.Fatalf("pasien ubah kampanye: status %d, ingin 403", status)
	}

	// Pemilik menutup kampanye.
	status, payload = do("PATCH", "/api/v1/campaigns/"+itoa(campaignID), `{"status":"tutup"}`, koasToken)
	if status != http.StatusOK {
		t.Fatalf("tutup kampanye: status %d (%v)", status, payload)
	}
	if payload["data"].(map[string]any)["status"] != "tutup" {
		t.Fatalf("status setelah ditutup: %v", payload["data"])
	}

	// Kampanye tutup tidak muncul lagi di daftar umum pasien.
	status, payload = do("GET", "/api/v1/campaigns", "", pasienToken)
	if status != http.StatusOK || containsCampaign(payload["data"].([]any), "Program Uji") {
		t.Fatalf("kampanye tutup masih muncul di daftar pasien: %v", payload)
	}

	// Koas pemilik masih melihatnya lewat ?mine=true.
	status, payload = do("GET", "/api/v1/campaigns?mine=true", "", koasToken)
	if status != http.StatusOK || !containsCampaign(payload["data"].([]any), "Program Uji") {
		t.Fatalf("kampanye milik koas tidak muncul di ?mine=true: %v", payload)
	}

	// Spesialis melihat kampanye koas di bawah supervisinya (data seed).
	status, payload = do("GET", "/api/v1/campaigns", "", spesialisToken)
	if status != http.StatusOK || len(payload["data"].([]any)) == 0 {
		t.Fatalf("spesialis list kampanye supervisi: status %d (%v)", status, payload)
	}
}

func containsCampaign(list []any, title string) bool {
	for _, item := range list {
		if c, ok := item.(map[string]any); ok && c["title"] == title {
			return true
		}
	}
	return false
}

func itoa(i int64) string {
	return strconv.FormatInt(i, 10)
}
