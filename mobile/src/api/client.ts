// Klien API untuk backend Go CoasConnect.
//
// PENTING: saat menguji di HP fisik melalui Expo Go, "localhost" mengacu ke
// HP itu sendiri, bukan komputer Anda. Ganti API_URL dengan IP LAN komputer,
// contoh: http://192.168.1.10:8080
// Jalankan backend dengan flag --host: go run ./cmd/api  (server listen :8080)
export const API_URL = 'http://localhost:8080'

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

async function request<T>(path: string, init?: RequestInit): Promise<T> {
  const res = await fetch(`${API_URL}${path}`, {
    headers: { 'Content-Type': 'application/json' },
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

export function createUser(input: { name: string; email: string }): Promise<{ data: User }> {
  return request<{ data: User }>('/api/v1/users', {
    method: 'POST',
    body: JSON.stringify(input),
  })
}

export function deleteUser(id: number): Promise<{ message: string }> {
  return request<{ message: string }>(`/api/v1/users/${id}`, { method: 'DELETE' })
}
