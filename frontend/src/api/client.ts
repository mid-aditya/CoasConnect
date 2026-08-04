// Klien API untuk backend Go CoasConnect.
// Selama development, Vite mem-proxy /api ke http://localhost:8080.

// ponytail: token disimpan di localStorage (praktis untuk dev/demo).
// Saat produksi, pindah ke httpOnly cookie + CSRF agar tak bisa diakses script lain.
const TOKEN_KEY = 'coasconnect_token'

export function getToken(): string | null {
  return localStorage.getItem(TOKEN_KEY)
}

export function setToken(token: string | null) {
  if (token) localStorage.setItem(TOKEN_KEY, token)
  else localStorage.removeItem(TOKEN_KEY)
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
  const token = getToken()
  const res = await fetch(path, {
    headers: {
      'Content-Type': 'application/json',
      ...(token ? { Authorization: `Bearer ${token}` } : {}),
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

export function updateCaseStatus(id: number, status: string): Promise<{ data: CaseView }> {
  return request<{ data: CaseView }>(`/api/v1/cases/${id}`, {
    method: 'PATCH',
    body: JSON.stringify({ status }),
  })
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

export function updateAppointment(
  id: number,
  input: { status: string; notes: string },
): Promise<{ data: Appointment }> {
  return request<{ data: Appointment }>(`/api/v1/appointments/${id}`, {
    method: 'PATCH',
    body: JSON.stringify(input),
  })
}
