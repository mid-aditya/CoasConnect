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
  created_at: string
  updated_at: string
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

export function getUsers(): Promise<{ data: User[] }> {
  return request<{ data: User[] }>('/api/v1/users')
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

export function createUser(input: { name: string; email: string }): Promise<{ data: User }> {
  return request<{ data: User }>('/api/v1/users', {
    method: 'POST',
    body: JSON.stringify(input),
  })
}

export function deleteUser(id: number): Promise<{ message: string }> {
  return request<{ message: string }>(`/api/v1/users/${id}`, { method: 'DELETE' })
}
