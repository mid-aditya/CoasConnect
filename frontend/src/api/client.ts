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
  hospital: string
  specialty: string
  created_at: string
  updated_at: string
}

export interface CampaignView {
  id: number
  koas_id: number
  title: string
  description: string
  criteria: string
  procedure: string
  specialty: string
  hospital: string
  whatsapp: string
  status: 'aktif' | 'tutup'
  koas_name: string
  supervisor_name: string
  created_at: string
  updated_at: string
}

export interface Koas {
  id: number
  name: string
  hospital: string
  specialty: string
  supervisor_id?: number
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

export function register(
  name: string,
  email: string,
  password: string,
  role: 'pasien' | 'koas' = 'pasien',
  hospital = '',
  specialty = '',
): Promise<AuthResponse> {
  return request<AuthResponse>('/api/v1/auth/register', {
    method: 'POST',
    body: JSON.stringify({ name, email, password, role, hospital, specialty }),
  })
}

export function getMe(): Promise<{ data: User }> {
  return request<{ data: User }>('/api/v1/auth/me')
}

export function getKoas(): Promise<{ data: Koas[] }> {
  return request<{ data: Koas[] }>('/api/v1/koas')
}

export function getCampaigns(): Promise<{ data: CampaignView[] }> {
  return request<{ data: CampaignView[] }>('/api/v1/campaigns')
}

export function getMyCampaigns(): Promise<{ data: CampaignView[] }> {
  return request<{ data: CampaignView[] }>('/api/v1/campaigns?mine=true')
}

export function getCampaign(id: number): Promise<{ data: CampaignView }> {
  return request<{ data: CampaignView }>(`/api/v1/campaigns/${id}`)
}

export function createCampaign(input: {
  title: string
  description: string
  criteria: string
  procedure: string
  whatsapp: string
}): Promise<{ data: CampaignView }> {
  return request<{ data: CampaignView }>('/api/v1/campaigns', {
    method: 'POST',
    body: JSON.stringify(input),
  })
}

export function updateCampaign(
  id: number,
  input: Partial<{
    title: string
    description: string
    criteria: string
    procedure: string
    whatsapp: string
    status: 'aktif' | 'tutup'
  }>,
): Promise<{ data: CampaignView }> {
  return request<{ data: CampaignView }>(`/api/v1/campaigns/${id}`, {
    method: 'PATCH',
    body: JSON.stringify(input),
  })
}
