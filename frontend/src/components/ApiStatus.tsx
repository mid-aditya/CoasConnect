import { useEffect, useState } from 'react'
import { getHealth, type Health } from '../api/client'

export default function ApiStatus() {
  const [health, setHealth] = useState<Health | null>(null)
  const [error, setError] = useState<string | null>(null)
  const [loading, setLoading] = useState(true)

  const check = async () => {
    setLoading(true)
    setError(null)
    try {
      setHealth(await getHealth())
    } catch (e) {
      setError(e instanceof Error ? e.message : 'Terjadi kesalahan')
      setHealth(null)
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => {
    check()
    const timer = setInterval(check, 15_000)
    return () => clearInterval(timer)
  }, [])

  return (
    <section id="status" className="py-16 bg-ink-900">
      <div className="mx-auto max-w-6xl px-6">
        <div className="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
          <div>
            <p className="text-xs font-semibold uppercase tracking-widest text-coral-400">
              Koneksi backend
            </p>
            <h2 className="mt-2 font-display font-700 text-3xl text-sand-100">
              Status layanan CoasConnect
            </h2>
          </div>
          <button
            onClick={check}
            disabled={loading}
            className="self-start px-4 py-2 rounded-full text-sm font-medium ring-1 ring-white/20 text-sand-100 hover:bg-white/5 disabled:opacity-50 transition-colors"
          >
            {loading ? 'Memeriksa…' : 'Periksa ulang'}
          </button>
        </div>

        <div className="mt-8 rounded-2xl bg-ink-950/60 ring-1 ring-white/10 p-6">
          {loading && !health && !error ? (
            <div className="flex items-center gap-3 text-sand-100/60 text-sm">
              <span className="w-4 h-4 rounded-full border-2 border-aqua-500 border-t-transparent animate-spin" />
              Menghubungi API backend…
            </div>
          ) : error ? (
            <div className="flex flex-col sm:flex-row sm:items-center gap-4">
              <div className="grid place-items-center w-12 h-12 rounded-xl bg-coral-500/15 ring-1 ring-coral-500/40">
                <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="var(--color-coral-400)" strokeWidth="2" strokeLinecap="round">
                  <path d="M12 8v5M12 16.5v.5" />
                  <path d="M10.3 3.9 2.5 17.5A2 2 0 0 0 4.2 20.5h15.6a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z" />
                </svg>
              </div>
              <div>
                <p className="font-semibold text-sand-100">API tidak merespons</p>
                <p className="text-sm text-sand-100/50 mt-1">
                  {error}. Jalankan backend Go: <code className="text-aqua-400">cd backend && go run ./cmd/api</code>
                </p>
              </div>
            </div>
          ) : health ? (
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
              <div className="flex items-center gap-4">
                <span className="relative grid place-items-center w-12 h-12 rounded-xl bg-aqua-500/15 ring-1 ring-aqua-500/40">
                  <span className="w-3 h-3 rounded-full bg-aqua-400" />
                  <span className="absolute w-3 h-3 rounded-full bg-aqua-400 animate-ping opacity-40" />
                </span>
                <div>
                  <p className="font-semibold text-sand-100">Semua sistem normal</p>
                  <p className="text-sm text-sand-100/50 mt-0.5">
                    Layanan <code className="text-aqua-400">{health.service}</code> · status{' '}
                    <span className="text-aqua-400">{health.status}</span>
                  </p>
                </div>
              </div>
              <dl className="grid grid-cols-2 gap-x-10 gap-y-3 text-sm">
                <div>
                  <dt className="text-sand-100/40 text-xs">Uptime</dt>
                  <dd className="text-sand-100 font-medium">
                    {Math.floor(health.uptime_s / 60)} m {health.uptime_s % 60} d
                  </dd>
                </div>
                <div>
                  <dt className="text-sand-100/40 text-xs">Waktu server</dt>
                  <dd className="text-sand-100 font-medium">
                    {new Date(health.time).toLocaleTimeString('id-ID')}
                  </dd>
                </div>
              </dl>
            </div>
          ) : null}
        </div>
      </div>
    </section>
  )
}
