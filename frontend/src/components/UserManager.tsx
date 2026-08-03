import { useCallback, useEffect, useState, type FormEvent } from 'react'
import { createUser, deleteUser, getUsers, type User } from '../api/client'

export default function UserManager() {
  const [users, setUsers] = useState<User[]>([])
  const [name, setName] = useState('')
  const [email, setEmail] = useState('')
  const [busy, setBusy] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const [notice, setNotice] = useState<string | null>(null)

  const load = useCallback(async () => {
    try {
      const res = await getUsers()
      setUsers(res.data)
    } catch (e) {
      setError(e instanceof Error ? e.message : 'Gagal memuat anggota')
    }
  }, [])

  useEffect(() => {
    load()
  }, [load])

  const onSubmit = async (e: FormEvent) => {
    e.preventDefault()
    setError(null)
    setNotice(null)
    if (!name.trim() || !email.trim()) {
      setError('Nama dan email wajib diisi.')
      return
    }
    setBusy(true)
    try {
      await createUser({ name: name.trim(), email: email.trim() })
      setName('')
      setEmail('')
      setNotice('Anggota baru berhasil ditambahkan ke jaringan.')
      await load()
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Gagal menambahkan anggota')
    } finally {
      setBusy(false)
    }
  }

  const onDelete = async (id: number) => {
    if (!confirm('Hapus anggota ini dari jaringan?')) return
    setError(null)
    setNotice(null)
    try {
      await deleteUser(id)
      setNotice('Anggota dihapus dari jaringan.')
      await load()
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Gagal menghapus anggota')
    }
  }

  return (
    <section id="anggota" className="py-16 bg-sand-100">
      <div className="mx-auto max-w-6xl px-6">
        <div className="grid lg:grid-cols-5 gap-10">
          <div className="lg:col-span-2">
            <p className="text-xs font-semibold uppercase tracking-widest text-coral-500">
              Data nyata · API Go
            </p>
            <h2 className="mt-2 font-display font-700 text-3xl text-ink-900">
              Anggota jaringan
            </h2>
            <p className="mt-3 text-ink-900/60 leading-relaxed">
              Form di samping terhubung langsung ke backend Go melalui endpoint{' '}
              <code className="px-1.5 py-0.5 rounded bg-ink-900/5 ring-1 ring-ink-900/10 text-xs text-ocean-700">
                POST /api/v1/users
              </code>
              . Coba tambahkan anggota baru — datanya tersimpan di SQLite.
            </p>

            <form
              onSubmit={onSubmit}
              className="mt-8 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-ink-900/5 space-y-4"
            >
              <div>
                <label htmlFor="name" className="block text-sm font-medium text-ink-900 mb-1.5">
                  Nama lengkap
                </label>
                <input
                  id="name"
                  value={name}
                  onChange={(e) => setName(e.target.value)}
                  placeholder="cth: Marina Lestari"
                  className="w-full px-4 py-2.5 rounded-xl bg-sand-100 ring-1 ring-ink-900/10 focus:ring-2 focus:ring-aqua-500 outline-none transition-shadow"
                />
              </div>
              <div>
                <label htmlFor="email" className="block text-sm font-medium text-ink-900 mb-1.5">
                  Email
                </label>
                <input
                  id="email"
                  type="email"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  placeholder="cth: marina@coasconnect.id"
                  className="w-full px-4 py-2.5 rounded-xl bg-sand-100 ring-1 ring-ink-900/10 focus:ring-2 focus:ring-aqua-500 outline-none transition-shadow"
                />
              </div>

              {error && (
                <p className="text-sm text-coral-500 bg-coral-500/5 ring-1 ring-coral-500/20 rounded-lg px-3 py-2">
                  {error}
                </p>
              )}
              {notice && (
                <p className="text-sm text-aqua-500 bg-aqua-500/5 ring-1 ring-aqua-500/20 rounded-lg px-3 py-2">
                  {notice}
                </p>
              )}

              <button
                type="submit"
                disabled={busy}
                className="w-full px-4 py-3 rounded-xl bg-ink-900 text-sand-100 font-semibold hover:bg-ink-800 hover:-translate-y-0.5 disabled:opacity-60 disabled:translate-y-0 transition-all"
              >
                {busy ? 'Menyimpan…' : 'Tambah ke jaringan'}
              </button>
            </form>
          </div>

          <div className="lg:col-span-3">
            <div className="rounded-2xl bg-white shadow-sm ring-1 ring-ink-900/5 overflow-hidden">
              <div className="px-5 py-4 border-b border-ink-900/5 flex items-center justify-between">
                <span className="text-sm font-semibold text-ink-900">
                  Daftar anggota{' '}
                  <span className="ml-1 px-2 py-0.5 rounded-full bg-aqua-500/10 text-aqua-500 text-xs font-medium">
                    {users.length}
                  </span>
                </span>
                <button
                  onClick={load}
                  className="text-xs text-ink-900/40 hover:text-aqua-500 transition-colors"
                >
                  Muat ulang
                </button>
              </div>

              {users.length === 0 ? (
                <div className="px-5 py-14 text-center">
                  <p className="font-display text-ink-900/70">Belum ada anggota</p>
                  <p className="text-sm text-ink-900/40 mt-1">
                    Tambahkan anggota pertama melalui form di samping.
                  </p>
                </div>
              ) : (
                <ul className="divide-y divide-ink-900/5">
                  {users.map((u) => (
                    <li key={u.id} className="px-5 py-4 flex items-center gap-4 group">
                      <span className="grid place-items-center w-10 h-10 rounded-full bg-ink-900 text-sand-100 font-display font-700 text-sm shrink-0">
                        {u.name.charAt(0).toUpperCase()}
                      </span>
                      <div className="min-w-0 flex-1">
                        <p className="font-medium text-ink-900 truncate">{u.name}</p>
                        <p className="text-sm text-ink-900/45 truncate">{u.email}</p>
                      </div>
                      <span className="hidden sm:block text-xs text-ink-900/35 shrink-0">
                        #{u.id}
                      </span>
                      <button
                        onClick={() => onDelete(u.id)}
                        aria-label={`Hapus ${u.name}`}
                        className="p-2 rounded-lg text-ink-900/30 hover:text-coral-500 hover:bg-coral-500/10 transition-colors shrink-0"
                      >
                        <svg viewBox="0 0 24 24" className="w-4.5 h-4.5" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round">
                          <path d="M3 6h18M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                        </svg>
                      </button>
                    </li>
                  ))}
                </ul>
              )}
            </div>
          </div>
        </div>
      </div>
    </section>
  )
}
