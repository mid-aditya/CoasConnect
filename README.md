<div align="center">

# 🌊 CoasConnect

**Menghubungkan pasien dengan dokter koas dalam satu alur perawatan.**

</div>

Monorepo aplikasi CoasConnect — platform monitoring pasien yang ditangani
dokter koas (ko-asisten) di bawah bimbingan dokter spesialis/pembimbing.
Alurnya: pasien membuat janji temu → dokter koas menangani dengan supervisi
spesialis → setiap sesi tercatat → sampai pasien pulih atau dinyatakan selesai.

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
| POST   | `/api/v1/auth/register` | Daftar akun **pasien** + terbitkan token JWT |
| POST   | `/api/v1/auth/login`    | Login + terbitkan token JWT |
| GET    | `/api/v1/auth/me`       | Data user yang login *(butuh token)* |
| GET    | `/api/v1/koas`    | Daftar dokter koas *(butuh token)* |
| POST   | `/api/v1/cases`   | Buka kasus + janji temu pertama *(pasien)* |
| GET    | `/api/v1/cases`   | Daftar kasus sesuai peran *(butuh token)* |
| GET    | `/api/v1/cases/{id}` | Detail kasus *(butuh token)* |
| PATCH  | `/api/v1/cases/{id}` | Ubah status kasus: pulih/selesai *(koas/pembimbing)* |
| GET    | `/api/v1/cases/{id}/appointments` | Sesi monitoring kasus *(butuh token)* |
| POST   | `/api/v1/cases/{id}/appointments` | Janji temu lanjutan *(pasien pemilik)* |
| PATCH  | `/api/v1/appointments/{id}` | Catat hasil sesi *(koas pemilik)* |

Database SQLite dibuat otomatis (`coasconnect.db`) beserta migrasi tabel dan
**akun demo** (dibuat saat pertama kali jalan):

| Akun | Email | Password | Role |
| ---- | ----- | -------- | ---- |
| Pasien (daftar sendiri) | — | — | `pasien` |
| Dokter Koas | `koas@coasconnect.id` | `koas1234` | `koas` |
| Dokter Spesialis | `spesialis@coasconnect.id` | `spesialis123` | `spesialis` |

> **Autentikasi & role:** semua endpoint `/cases`, `/appointments`, `/koas`
> butuh header `Authorization: Bearer <token>`. Token didapat dari
> `/auth/register` atau `/auth/login`. Registrasi selalu membuat akun `pasien`;
> akun `koas`/`spesialis` dibuat seed (belum ada UI admin). Set env
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

- [x] Backend Go + SQLite (kasus, janji temu, role: pasien/koas/spesialis)
- [x] Autentikasi (JWT) + otorisasi per role
- [x] Frontend React (landing + dashboard per role: pasien, koas, pembimbing)
- [x] Mobile React Native (Expo) (alur pasien: janji temu + status kasus)
- [ ] Fitur lanjutan: admin/manajemen akun koas & spesialis
- [ ] Fitur domain: resep, hasil pemeriksaan, notifikasi
- [ ] Deploy (Railway / Render / VPS) + CI/CD
---

Dibuat dengan Go · React · React Native · ❤️
