<div align="center">

# 🌊 CoasConnect

**Menghubungkan pasien dengan dokter koas dalam satu alur perawatan.**

</div>

Monorepo aplikasi CoasConnect — platform penjaringan pasien untuk dokter koas
(ko-asisten). Dokter koas memasang kampanye berisi kriteria & prosedur, pasien
menemukannya lewat web atau mobile, lalu dibimbing langsung oleh koas dengan
supervisi dokter spesialis. Janji temu & tugas koas dialihkan ke WhatsApp —
platform ini fokus mencari & mendistribusikan pasien.

## 🏗️ Arsitektur

| Folder     | Stack                                    | Deskripsi                          |
| ---------- | ---------------------------------------- | ---------------------------------- |
| `backend/` | **Go** (Chi) · SQLite (modernc, tanpa CGO) | REST API JSON                      |
| `frontend/`| **React** (Vite + TypeScript + Tailwind v4) | Web app                            |
| `mobile/`  | **React Native** (Expo + TypeScript)      | Aplikasi mobile                    |

```
┌────────────┐     HTTP/JSON      ┌──────────────┐      ┌──────────────┐
│  frontend  │ ─────────────────▶ │  backend     │      │              │
│  (React)   │ ◀───────────────── │  (Go + Chi)  │─────▶│   SQLite     │
└────────────┘                    └──────┬───────┘      │ (coasconnect.db)│
┌────────────┐     HTTP/JSON      ┌──────┴───────┐      └──────────────┘
│  mobile    │ ─────────────────▶ │  backend     │
│  (Expo)    │ ◀───────────────── │  (sama)      │
└────────────┘                    └──────────────┘
```

> **Catatan:** Versi Laravel/PHP sebelumnya diarsipkan di branch
> `archive/laravel`.

## 🔧 Prasyarat

- **Go 1.22+** — untuk backend
- **Node.js 20+ & npm** — untuk frontend & mobile
- **Expo Go** (app di HP) atau emulator — untuk menjalankan mobile

## 🚀 Menjalankan

### 1. Backend (Go)

```bash
cd backend
cp .env.example .env   # opsional, semua punya default
go mod tidy            # unduh dependency (membuat go.sum)
go run ./cmd/api
```

API tersedia di `http://localhost:8080`.

| Method | Endpoint          | Fungsi            |
| ------ | ----------------- | ----------------- |
| GET    | `/api/v1/health`  | Health check      |
| POST   | `/api/v1/auth/register` | Daftar akun **pasien** atau **koas** (RS & bidang) + token JWT |
| POST   | `/api/v1/auth/login`    | Login + terbitkan token JWT |
| GET    | `/api/v1/auth/me`       | Data user yang login *(butuh token)* |
| GET    | `/api/v1/koas`    | Daftar dokter koas + profil (RS, bidang, pembimbing) *(butuh token)* |
| GET    | `/api/v1/campaigns` | Daftar kampanye sesuai peran *(butuh token)* |
| GET    | `/api/v1/campaigns?mine=true` | Kampanye milik koas yang login *(koas)* |
| GET    | `/api/v1/campaigns/{id}` | Detail kampanye *(butuh token)* |
| POST   | `/api/v1/campaigns` | Pasang kampanye baru *(koas)* |
| PATCH  | `/api/v1/campaigns/{id}` | Ubah kampanye / status aktif-tutup *(koas pemilik)* |

Database SQLite dibuat otomatis (`coasconnect.db`) beserta migrasi tabel dan
**akun demo** (dibuat saat pertama kali jalan):

| Akun | Email | Password | Role |
| ---- | ----- | -------- | ---- |
| Pasien | `budi@coasconnect.id` | `pasien1234` | `pasien` |
| Dokter Koas | `koas@coasconnect.id` | `koas1234` | `koas` |
| Dokter Koas (2) | `koas2@coasconnect.id` | `koas1234` | `koas` |
| Dokter Spesialis | `spesialis@coasconnect.id` | `spesialis123` | `spesialis` |

> **Autentikasi & role:** semua endpoint `/campaigns`, `/koas`, `/auth/me`
> butuh header `Authorization: Bearer <token>`. Token didapat dari
> `/auth/register` atau `/auth/login`. Registrasi menerima `role`
> (`pasien` default | `koas`) plus `hospital` & `specialty` untuk koas.
> Akun `spesialis` dibuat seed (belum ada UI admin). Set env
> `JWT_SECRET` dengan nilai unik sebelum deploy (default hanya untuk dev).

### 2. Frontend (React web)

```bash
cd frontend
npm install
npm run dev
```

Buka `http://localhost:5173`. Vite mem-proxy `/api` ke backend
(`http://localhost:8080`) sehingga tidak perlu konfigurasi tambahan.

### 3. Mobile (React Native / Expo)

```bash
cd mobile
npm install
npx expo start
```

Scan QR code dengan **Expo Go** di HP (atau tekan `a` untuk emulator Android).

> ⚠️ **Penting:** Saat diuji di HP fisik, `localhost` mengacu ke HP itu
> sendiri. Ubah `API_URL` di `mobile/src/api/client.ts` menjadi IP LAN
> komputer Anda, mis. `http://192.168.1.10:8080`, dan pastikan HP serta
> komputer berada di jaringan yang sama. Jalankan backend dengan
> `go run ./cmd/api` (secara default listen di semua interface `:8080`).

## 📦 Build produksi

```bash
# Frontend
cd frontend && npm run build   # output di frontend/dist

# Mobile
cd mobile && npx expo export   # build bundle JS (native build via EAS)
```

## 🧪 Verifikasi

```bash
# Backend — butuh Go
cd backend && go vet ./... && go build ./...

# Frontend
cd frontend && npm run build   # termasuk typecheck (tsc -b)

# Mobile
cd mobile && npx tsc --noEmit
```

## 📁 Struktur repo

```
coasconnect/
├── backend/
│   ├── cmd/api/main.go          # entry point
│   └── internal/
│       ├── config/              # konfigurasi env
│       ├── database/            # koneksi + migrasi SQLite
│       ├── handlers/            # handler HTTP (health, user)
│       ├── middleware/          # logger, recoverer, CORS
│       ├── models/              # model data
│       └── router/              # route chi
├── frontend/
│   └── src/
│       ├── api/client.ts        # klien API
│       └── components/          # komponen React
├── mobile/
│   └── src/
│       ├── api/client.ts        # klien API
│       └── theme.ts             # design token
└── README.md
```

## 🛣️ Roadmap singkat

- [x] Backend Go + SQLite (kampanye, role: pasien/koas/spesialis)
- [x] Autentikasi (JWT) + otorisasi per role (register koas dengan profil RS & bidang)
- [x] Frontend React (landing + dashboard per role: cari kampanye, kelola kampanye, supervisi)
- [x] Data demo lengkap (2 koas, 1 pembimbing, 4 pasien, 6 kampanye)
- [ ] Fitur profiling dokter koas (pembimbing dihubungkan eksplisit)
- [x] Mobile React Native (Expo): alur pasien melihat & mendaftar kampanye, koas memasang & mengelola kampanye
- [ ] Deploy (Railway / Render / VPS) + CI/CD
---

Dibuat dengan Go · React · React Native · ❤️
