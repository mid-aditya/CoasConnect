import { useState, type FormEvent } from 'react'
import { login, register, setToken } from '../api/client'

export default function Login({ onAuthed }: { onAuthed: () => void }) {
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
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Terjadi kesalahan.')
    } finally {
      setBusy(false)
    }
  }

  return (
    <section id="anggota" className="py-16 bg-sand-100">
      <div className="mx-auto max-w-md px-6">
        <p className="text-xs font-semibold uppercase tracking-widest text-coral-500 text-center">
          Monitoring pasien
        </p>
        <h2 className="mt-2 font-display font-700 text-3xl text-ink-900 text-center">
          {mode === 'login' ? 'Masuk' : 'Daftar sebagai pasien'}
        </h2>
        <p className="mt-3 text-center text-ink-900/60 leading-relaxed">
          {mode === 'login'
            ? 'Masuk untuk membuka dan memantau kasus perawatan.'
            : 'Registrasi membuka akun pasien. Akun dokter koas & spesialis dikelola penyelenggara.'}
        </p>

        <form
          onSubmit={onSubmit}
          className="mt-8 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-ink-900/5 space-y-4"
        >
          {mode === 'register' && (
            <div>
              <label htmlFor="auth-name" className="block text-sm font-medium text-ink-900 mb-1.5">
                Nama lengkap
              </label>
              <input
                id="auth-name"
                value={name}
                onChange={(e) => setName(e.target.value)}
                placeholder="cth: Marina Lestari"
                className="w-full px-4 py-2.5 rounded-xl bg-sand-100 ring-1 ring-ink-900/10 focus:ring-2 focus:ring-aqua-500 outline-none transition-shadow"
              />
            </div>
          )}
          <div>
            <label htmlFor="auth-email" className="block text-sm font-medium text-ink-900 mb-1.5">
              Email
            </label>
            <input
              id="auth-email"
              type="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              placeholder="cth: marina@coasconnect.id"
              className="w-full px-4 py-2.5 rounded-xl bg-sand-100 ring-1 ring-ink-900/10 focus:ring-2 focus:ring-aqua-500 outline-none transition-shadow"
            />
          </div>
          <div>
            <label htmlFor="auth-password" className="block text-sm font-medium text-ink-900 mb-1.5">
              Password
            </label>
            <input
              id="auth-password"
              type="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              placeholder={mode === 'register' ? 'Minimal 8 karakter' : '••••••••'}
              className="w-full px-4 py-2.5 rounded-xl bg-sand-100 ring-1 ring-ink-900/10 focus:ring-2 focus:ring-aqua-500 outline-none transition-shadow"
            />
          </div>

          {error && (
            <p className="text-sm text-coral-500 bg-coral-500/5 ring-1 ring-coral-500/20 rounded-lg px-3 py-2">
              {error}
            </p>
          )}

          <button
            type="submit"
            disabled={busy}
            className="w-full px-4 py-3 rounded-xl bg-ink-900 text-sand-100 font-semibold hover:bg-ink-800 hover:-translate-y-0.5 disabled:opacity-60 disabled:translate-y-0 transition-all"
          >
            {busy ? 'Memproses…' : mode === 'login' ? 'Masuk' : 'Daftar'}
          </button>

          <button
            type="button"
            onClick={() => setMode(mode === 'login' ? 'register' : 'login')}
            className="w-full text-sm text-ocean-700 hover:text-aqua-500 transition-colors"
          >
            {mode === 'login' ? 'Belum punya akun? Daftar' : 'Sudah punya akun? Masuk'}
          </button>
        </form>
      </div>
    </section>
  )
}
