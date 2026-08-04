// Klien API untuk backend Go CoasConnect.
//
// PENTING: saat menguji di HP fisik melalui Expo Go, "localhost" mengacu ke
// HP itu sendiri, bukan komputer Anda. Ganti API_URL dengan IP LAN komputer,
// contoh: http://192.168.1.10:8080
// Jalankan backend dengan flag --host: go run ./cmd/api  (server listen :8080)
export const API_URL = 'http://localhost:8080'

// ponytail: token hanya di memori — hilang saat app ditutup. Cukup untuk dev;
// upgrade ke expo-secure-store saat perlu persist login antar sesi.
let token: string | null = null

export function getToken(): string | null {
  return token
}

export function setToken(t: string | null) {
  token = t
}

export type Role = 'pasien' | 'koas' | 'spesialis'

export interface Health {
  status: string
  service: string
  uptime_s: number
  time: string
}

export interface User {
  id: number
  name: string
  email: string
  role: Role
  supervisor_id?: number
  created_at: string
  updated_at: string
}

export interface CaseView {
  id: number
  patient_id: number
  koas_id: number
  supervisor_id: number
  complaint: string
  status: 'aktif' | 'pulih' | 'selesai'
  patient_name: string
  koas_name: string
  supervisor_name: string
  created_at: string
  updated_at: string
  closed_at: string | null
}

export interface Appointment {
  id: number
  case_id: number
  scheduled_at: string
  status: 'terjadwal' | 'selesai' | 'dibatalkan'
  notes: string
  created_at: string
}

export interface Koas {
  id: number
  name: string
}

export interface AuthResponse {
  data: { user: User; token: string }
}

async function request<T>(path: string, init?: RequestInit): Promise<T> {
  const res = await fetch(`${API_URL}${path}`, {
    headers: {
      'Content-Type': 'application/json',
      ...(getToken() ? { Authorization: `Bearer ${getToken()}` } : {}),
    },
    ...init,
  })

  if (!res.ok) {
    const body = (await res.json().catch(() => null)) as { error?: string } | null
    throw new Error(body?.error ?? `Request gagal (${res.status})`)
  }

  return (await res.json()) as T
}

export function getHealth(): Promise<Health> {
  return request<Health>('/api/v1/health')
}

export function login(email: string, password: string): Promise<AuthResponse> {
  return request<AuthResponse>('/api/v1/auth/login', {
    method: 'POST',
    body: JSON.stringify({ email, password }),
  })
}

export function register(name: string, email: string, password: string): Promise<AuthResponse> {
  return request<AuthResponse>('/api/v1/auth/register', {
    method: 'POST',
    body: JSON.stringify({ name, email, password }),
  })
}

export function getMe(): Promise<{ data: User }> {
  return request<{ data: User }>('/api/v1/auth/me')
}

export function getKoas(): Promise<{ data: Koas[] }> {
  return request<{ data: Koas[] }>('/api/v1/koas')
}

export function createCase(input: {
  koas_id: number
  complaint: string
  scheduled_at: string
}): Promise<{ data: CaseView }> {
  return request<{ data: CaseView }>('/api/v1/cases', {
    method: 'POST',
    body: JSON.stringify(input),
  })
}

export function getCases(): Promise<{ data: CaseView[] }> {
  return request<{ data: CaseView[] }>('/api/v1/cases')
}

export function getAppointments(caseId: number): Promise<{ data: Appointment[] }> {
  return request<{ data: Appointment[] }>(`/api/v1/cases/${caseId}/appointments`)
}

export function createAppointment(caseId: number, scheduledAt: string): Promise<{ data: Appointment }> {
  return request<{ data: Appointment }>(`/api/v1/cases/${caseId}/appointments`, {
    method: 'POST',
    body: JSON.stringify({ scheduled_at: scheduledAt }),
  })
}
