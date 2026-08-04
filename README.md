<div align="center">

# 🌊 CoasConnect

**Menghubungkan komunitas pesisir dalam satu jaringan.**

</div>

Monorepo aplikasi CoasConnect — platform konektivitas untuk nelayan, koperasi,
dan pasar pesisir.

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
| POST   | `/api/v1/auth/register` | Daftar akun + terbitkan token JWT |
| POST   | `/api/v1/auth/login`    | Login + terbitkan token JWT |
| GET    | `/api/v1/users`   | Daftar user *(butuh token)* |
| POST   | `/api/v1/users`   | Tambah user *(butuh token)* |
| GET    | `/api/v1/users/{id}` | Detail user *(butuh token)* |
| PUT    | `/api/v1/users/{id}` | Ubah user *(butuh token)* |
| DELETE | `/api/v1/users/{id}` | Hapus user *(butuh token)* |

Database SQLite dibuat otomatis (`coasconnect.db`) beserta migrasi tabel.

> **Autentikasi:** semua endpoint `/users` butuh header `Authorization: Bearer
> <token>`. Token didapat dari `/auth/register` atau `/auth/login`. Set env
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

- [x] Backend Go + SQLite (CRUD user, health check)
- [x] Autentikasi (JWT) — register/login, route user dilindungi
- [x] Frontend React (landing + demo CRUD end-to-end)
- [x] Mobile React Native (Expo) (status API + CRUD)
- [ ] Autentikasi (JWT)
- [ ] Fitur domain: harga pasar, logistik, koperasi
---

Dibuat dengan Go · React · React Native · ❤️
