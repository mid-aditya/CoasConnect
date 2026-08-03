// Klien API untuk backend Go CoasConnect.
// Selama development, Vite mem-proxy /api ke http://localhost:8080.

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
  const res = await fetch(path, {
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
