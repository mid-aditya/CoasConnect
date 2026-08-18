import { useState, type FormEvent } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { login, register, setToken } from '../api/client'

const DEMO_AKUN = [
  ['Dokter Koas', 'koas@coasconnect.id', 'koas1234'],
  ['Dokter Spesialis', 'spesialis@coasconnect.id', 'spesialis123'],
] as const

export default function Login({ onAuthed }: { onAuthed: () => void }) {
  const navigate = useNavigate()
  const [mode, setMode] = useState<'login' | 'register'>('login')
  const [name, setName] = useState('')
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [busy, setBusy] = useState(false)
  const [error, setError] = useState<string | null>(null)

  const onSubmit = async (e: FormEvent) => {
    e.preventDefault()
    setError(null)
    if (!email.trim() || !password) {
      setError('Email dan password wajib diisi.')
      return
    }
    if (mode === 'register' && !name.trim()) {
      setError('Nama wajib diisi untuk registrasi.')
      return
    }
    setBusy(true)
    try {
      const res =
        mode === 'login'
          ? await login(email.trim(), password)
          : await register(name.trim(), email.trim(), password)
      setToken(res.data.token)
      onAuthed()
      navigate('/app')
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Terjadi kesalahan.')
    } finally {
      setBusy(false)
    }
  }

  return (
    <div className="min-h-screen bg-paper grid place-items-center px-6 py-12">
      <div className="w-full max-w-md">
        <Link
          to="/"
          className="inline-flex items-center gap-2 font-mono text-xs uppercase tracking-[0.14em] text-muted hover:text-ink transition-colors"
        >
          <svg viewBox="0 0 16 16" className="w-3.5 h-3.5" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
            <path d="M10 3.5 5.5 8l4.5 4.5" />
          </svg>
          Kembali ke beranda
        </Link>

        <div className="mt-8">
          <div className="flex items-center gap-2.5">
            <span className="grid place-items-center w-9 h-9 rounded-md bg-pine text-paper">
              <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                <path d="M3 12h4l2-5 3 10 2.5-8 1.5 3H21" />
              </svg>
            </span>
            <span className="font-display font-bold tracking-tight text-ink">
              Coas<span className="text-pine">Connect</span>
            </span>
          </div>

          <h1 className="mt-8 font-display font-extrabold text-3xl tracking-[-0.015em] text-ink">
            {mode === 'login' ? 'Masuk' : 'Daftar sebagai pasien'}
          </h1>
          <p className="mt-2 text-muted leading-relaxed">
            {mode === 'login'
              ? 'Masuk untuk membuka dan memantau kasus perawatan.'
              : 'Registrasi membuka akun pasien. Akun dokter koas & spesialis dikelola penyelenggara.'}
          </p>

          <form
            onSubmit={onSubmit}
            className="mt-8 rounded-xl bg-white p-5 ring-1 ring-line shadow-[0_1px_3px_rgba(22,36,29,0.06)] space-y-4"
          >
            {mode === 'register' && (
              <div>
                <label htmlFor="auth-name" className="block text-sm font-medium text-ink mb-1.5">
                  Nama lengkap
                </label>
                <input
                  id="auth-name"
                  value={name}
                  onChange={(e) => setName(e.target.value)}
                  placeholder="cth: Marina Lestari"
                  className="w-full px-4 py-2.5 rounded-lg bg-paper ring-1 ring-line focus:ring-2 focus:ring-pine outline-none transition-shadow"
                />
              </div>
            )}
            <div>
              <label htmlFor="auth-email" className="block text-sm font-medium text-ink mb-1.5">
                Email
              </label>
              <input
                id="auth-email"
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                placeholder="cth: marina@coasconnect.id"
                className="w-full px-4 py-2.5 rounded-lg bg-paper ring-1 ring-line focus:ring-2 focus:ring-pine outline-none transition-shadow"
              />
            </div>
            <div>
              <label htmlFor="auth-password" className="block text-sm font-medium text-ink mb-1.5">
                Password
              </label>
              <input
                id="auth-password"
                type="password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                placeholder={mode === 'register' ? 'Minimal 8 karakter' : '••••••••'}
                className="w-full px-4 py-2.5 rounded-lg bg-paper ring-1 ring-line focus:ring-2 focus:ring-pine outline-none transition-shadow"
              />
            </div>

            {error && (
              <p className="text-sm text-clay bg-clay/5 ring-1 ring-clay/20 rounded-lg px-3 py-2">
                {error}
              </p>
            )}

            <button
              type="submit"
              disabled={busy}
              className="w-full px-4 py-3 rounded-lg bg-pine text-paper font-semibold hover:bg-ink disabled:opacity-60 transition-colors"
            >
              {busy ? 'Memproses…' : mode === 'login' ? 'Masuk' : 'Daftar'}
            </button>

            <button
              type="button"
              onClick={() => setMode(mode === 'login' ? 'register' : 'login')}
              className="w-full text-sm text-pine hover:text-ink transition-colors"
            >
              {mode === 'login' ? 'Belum punya akun? Daftar' : 'Sudah punya akun? Masuk'}
            </button>
          </form>

          {mode === 'login' && (
            <div className="mt-6 rounded-lg bg-mint px-4 py-3">
              <p className="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">
                Akun demo
              </p>
              <ul className="mt-2 space-y-1.5">
                {DEMO_AKUN.map(([peran, email, pass]) => (
                  <li key={email} className="flex flex-wrap items-baseline gap-x-2 text-sm">
                    <span className="font-medium text-ink">{peran}</span>
                    <span className="font-mono text-xs text-muted">{email}</span>
                    <span className="font-mono text-xs text-muted">· {pass}</span>
                  </li>
                ))}
              </ul>
            </div>
          )}
        </div>
      </div>
    </div>
  )
}
