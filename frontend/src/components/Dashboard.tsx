import { useCallback, useEffect, useState, type FormEvent } from 'react'
import { Link } from 'react-router-dom'
import {
  createAppointment,
  createCase,
  getAppointments,
  getCases,
  getKoas,
  updateAppointment,
  updateCaseStatus,
  type Appointment,
  type CaseView,
  type Koas,
  type User,
} from '../api/client'

const STATUS_BADGE: Record<string, string> = {
  aktif: 'text-clay bg-clay/5 ring-clay/15',
  pulih: 'text-pine bg-pine/5 ring-pine/15',
  selesai: 'text-muted bg-ink/5 ring-ink/10',
}

const APPT_BADGE: Record<string, string> = {
  terjadwal: 'text-pine bg-pine/5 ring-pine/15',
  selesai: 'text-muted bg-ink/5 ring-ink/10',
  dibatalkan: 'text-clay bg-clay/5 ring-clay/15',
}

function Chip({ tone, label }: { tone: string; label: string }) {
  return (
    <span
      className={`inline-flex items-center gap-1.5 rounded-full px-2 py-0.5 font-mono text-[10px] uppercase tracking-[0.1em] ring-1 ${tone}`}
    >
      <span className="w-1 h-1 rounded-full bg-current" />
      {label}
    </span>
  )
}

const fmt = (iso: string) => new Date(iso).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' })
const toISO = (local: string) => new Date(local).toISOString()

const inputCls =
  'w-full px-3 py-2 rounded-lg bg-paper ring-1 ring-line focus:ring-2 focus:ring-pine outline-none transition-shadow'

export default function Dashboard({ user, onLogout }: { user: User; onLogout: () => void }) {
  const [cases, setCases] = useState<CaseView[]>([])
  const [koas, setKoas] = useState<Koas[]>([])
  const [selected, setSelected] = useState<CaseView | null>(null)
  const [appts, setAppts] = useState<Appointment[]>([])
  const [error, setError] = useState<string | null>(null)
  const [notice, setNotice] = useState<string | null>(null)

  const [koasId, setKoasId] = useState('')
  const [complaint, setComplaint] = useState('')
  const [when, setWhen] = useState('')
  const [apptWhen, setApptWhen] = useState('')
  const [notes, setNotes] = useState('')

  const load = useCallback(async () => {
    try {
      const res = await getCases()
      setCases(res.data)
    } catch (e) {
      setError(e instanceof Error ? e.message : 'Gagal memuat kasus')
    }
  }, [])

  useEffect(() => {
    load()
    if (user.role === 'pasien') {
      getKoas()
        .then((r) => setKoas(r.data))
        .catch(() => {})
    }
  }, [load, user.role])

  const open = async (c: CaseView) => {
    setSelected(c)
    setNotice(null)
    setError(null)
    try {
      const res = await getAppointments(c.id)
      setAppts(res.data)
    } catch (e) {
      setError(e instanceof Error ? e.message : 'Gagal memuat sesi monitoring')
    }
  }

  const onCreateCase = async (e: FormEvent) => {
    e.preventDefault()
    setError(null)
    setNotice(null)
    if (!koasId || !complaint.trim() || !when) {
      setError('Pilih dokter koas, tulis keluhan, dan tentukan jadwal.')
      return
    }
    try {
      const res = await createCase({ koas_id: Number(koasId), complaint: complaint.trim(), scheduled_at: toISO(when) })
      setComplaint('')
      setWhen('')
      setNotice('Kasus dibuka — janji temu pertama tercatat.')
      await load()
      await open(res.data)
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Gagal membuka kasus')
    }
  }

  const onCreateAppt = async (e: FormEvent) => {
    e.preventDefault()
    if (!selected || !apptWhen) return
    try {
      await createAppointment(selected.id, toISO(apptWhen))
      setApptWhen('')
      await open(selected)
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Gagal membuat janji temu')
    }
  }

  const onCompleteAppt = async (a: Appointment) => {
    if (!notes.trim()) {
      setError('Catatan sesi wajib diisi.')
      return
    }
    try {
      await updateAppointment(a.id, { status: 'selesai', notes: notes.trim() })
      setNotes('')
      if (selected) await open(selected)
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Gagal mencatat sesi')
    }
  }

  const onSetStatus = async (c: CaseView, status: string) => {
    try {
      await updateCaseStatus(c.id, status)
      setNotice(status === 'selesai' ? 'Kasus ditutup.' : 'Kasus ditandai pulih.')
      await load()
      if (selected?.id === c.id) await open(c)
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Gagal memperbarui status')
    }
  }

  const roleTitle =
    user.role === 'pasien'
      ? 'Kasus perawatan saya'
      : user.role === 'koas'
        ? 'Kasus yang saya tangani'
        : 'Kasus di bawah supervisi saya'

  return (
    <div className="min-h-screen bg-[#edf1ee]">
      <header className="sticky top-0 z-40 bg-ink text-paper border-b border-paper/10">
        <nav className="mx-auto max-w-6xl px-6 h-14 flex items-center justify-between">
          <div className="flex items-center gap-3">
            <Link to="/" className="flex items-center gap-2.5">
              <span className="grid place-items-center w-7 h-7 rounded-md bg-pine text-paper">
                <svg viewBox="0 0 24 24" className="w-4 h-4" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                  <path d="M3 12h4l2-5 3 10 2.5-8 1.5 3H21" />
                </svg>
              </span>
              <span className="font-display font-bold tracking-tight">
                Coas<span className="text-paper/60">Connect</span>
              </span>
            </Link>
            <span className="hidden sm:inline-flex items-center gap-1.5 font-mono text-[10px] uppercase tracking-[0.14em] text-paper/50">
              <span className="w-1 h-1 rounded-full bg-pine" />
              Dashboard
            </span>
          </div>

          <div className="flex items-center gap-3">
            <span className="hidden md:inline-flex items-center gap-2 font-mono text-[10px] uppercase tracking-[0.14em] text-paper/60">
              {user.role}
              <span className="text-paper/30">·</span>
              {user.name}
            </span>
            <button
              onClick={onLogout}
              className="px-3 py-1.5 rounded-md ring-1 ring-paper/20 text-xs font-medium text-paper/70 hover:text-paper hover:ring-paper/40 transition-colors"
            >
              Keluar
            </button>
          </div>
        </nav>
      </header>

      <main className="mx-auto max-w-6xl px-6 py-8">
        <div className="flex flex-wrap items-end justify-between gap-4">
          <div>
            <p className="font-mono text-[11px] uppercase tracking-[0.18em] text-pine">{user.role}</p>
            <h1 className="mt-1 font-display font-extrabold text-2xl tracking-[-0.015em] text-ink">{roleTitle}</h1>
          </div>
          <button
            onClick={load}
            className="px-3 py-2 rounded-lg ring-1 ring-line text-sm text-muted hover:text-ink hover:ring-ink transition-colors"
          >
            Muat ulang
          </button>
        </div>

        {error && (
          <p className="mt-5 text-sm text-clay bg-clay/5 ring-1 ring-clay/20 rounded-lg px-3 py-2">{error}</p>
        )}
        {notice && (
          <p className="mt-5 text-sm text-pine bg-pine/5 ring-1 ring-pine/20 rounded-lg px-3 py-2">{notice}</p>
        )}

        <div className="mt-6 grid lg:grid-cols-5 gap-6 items-start">
          {/* Kolom kiri: aksi */}
          <div className="lg:col-span-2 space-y-6">
            {user.role === 'pasien' && (
              <section className="rounded-lg bg-white ring-1 ring-line overflow-hidden">
                <h2 className="px-5 py-3 border-b border-line font-mono text-[10px] uppercase tracking-[0.14em] text-muted">
                  Buat janji temu baru
                </h2>
                <form onSubmit={onCreateCase} className="p-5 space-y-4">
                  <div>
                    <label htmlFor="koas" className="block text-sm font-medium text-ink mb-1.5">
                      Dokter koas
                    </label>
                    <select
                      id="koas"
                      value={koasId}
                      onChange={(e) => setKoasId(e.target.value)}
                      className={inputCls}
                    >
                      <option value="">Pilih dokter koas…</option>
                      {koas.map((k) => (
                        <option key={k.id} value={k.id}>
                          {k.name}
                        </option>
                      ))}
                    </select>
                  </div>
                  <div>
                    <label htmlFor="complaint" className="block text-sm font-medium text-ink mb-1.5">
                      Keluhan awal
                    </label>
                    <textarea
                      id="complaint"
                      value={complaint}
                      onChange={(e) => setComplaint(e.target.value)}
                      rows={3}
                      placeholder="cth: Demam tinggi sejak 2 hari, batuk…"
                      className={inputCls}
                    />
                  </div>
                  <div>
                    <label htmlFor="when" className="block text-sm font-medium text-ink mb-1.5">
                      Jadwal temu pertama
                    </label>
                    <input
                      id="when"
                      type="datetime-local"
                      value={when}
                      onChange={(e) => setWhen(e.target.value)}
                      className={inputCls}
                    />
                  </div>
                  <button
                    type="submit"
                    className="w-full px-4 py-2.5 rounded-lg bg-pine text-paper font-semibold hover:bg-ink transition-colors"
                  >
                    Buka kasus
                  </button>
                </form>
              </section>
            )}

            {selected && (
              <section className="rounded-lg bg-white ring-1 ring-line overflow-hidden">
                <div className="px-5 py-3 border-b border-line flex items-center justify-between">
                  <h2 className="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">Sesi monitoring</h2>
                  <Chip tone={STATUS_BADGE[selected.status]} label={selected.status} />
                </div>
                <div className="p-5 space-y-4">
                  <p className="text-sm text-muted leading-relaxed">
                    Kasus #{selected.id} — {selected.patient_name} · ditangani {selected.koas_name}
                    <br />
                    Pembimbing: {selected.supervisor_name}
                  </p>

                  <ul className="divide-y divide-line">
                    {appts.length === 0 && <li className="py-2 text-sm text-muted/60">Belum ada sesi.</li>}
                    {appts.map((a) => (
                      <li key={a.id} className="py-3 space-y-1">
                        <div className="flex items-center justify-between gap-3">
                          <span className="font-mono text-xs text-ink">{fmt(a.scheduled_at)}</span>
                          <Chip tone={APPT_BADGE[a.status]} label={a.status} />
                        </div>
                        {a.notes && <p className="text-sm text-muted">{a.notes}</p>}
                        {a.status === 'terjadwal' && user.role === 'koas' && (
                          <div className="flex gap-2 pt-1">
                            <input
                              value={notes}
                              onChange={(e) => setNotes(e.target.value)}
                              placeholder="Catatan hasil sesi…"
                              className="flex-1 px-3 py-1.5 text-sm rounded-lg bg-paper ring-1 ring-line focus:ring-2 focus:ring-pine outline-none"
                            />
                            <button
                              onClick={() => onCompleteAppt(a)}
                              className="px-3 py-1.5 text-sm rounded-lg bg-pine text-paper font-semibold hover:bg-ink transition-colors"
                            >
                              Catat
                            </button>
                          </div>
                        )}
                      </li>
                    ))}
                  </ul>

                  {user.role === 'pasien' && selected.status !== 'selesai' && (
                    <form onSubmit={onCreateAppt} className="flex gap-2">
                      <input
                        type="datetime-local"
                        value={apptWhen}
                        onChange={(e) => setApptWhen(e.target.value)}
                        className="flex-1 px-3 py-2 text-sm rounded-lg bg-paper ring-1 ring-line focus:ring-2 focus:ring-pine outline-none"
                      />
                      <button
                        type="submit"
                        className="px-4 py-2 text-sm rounded-lg bg-pine text-paper font-semibold hover:bg-ink transition-colors"
                      >
                        Janji lanjutan
                      </button>
                    </form>
                  )}

                  {(user.role === 'koas' || user.role === 'spesialis') && selected.status !== 'selesai' && (
                    <div className="flex gap-2">
                      {selected.status !== 'pulih' && (
                        <button
                          onClick={() => onSetStatus(selected, 'pulih')}
                          className="flex-1 px-4 py-2 text-sm rounded-lg bg-clay/10 ring-1 ring-clay/20 text-clay font-semibold hover:bg-clay/20 transition-colors"
                        >
                          Tandai pulih
                        </button>
                      )}
                      <button
                        onClick={() => onSetStatus(selected, 'selesai')}
                        className="flex-1 px-4 py-2 text-sm rounded-lg bg-ink text-paper font-semibold hover:bg-pine transition-colors"
                      >
                        Tutup kasus
                      </button>
                    </div>
                  )}
                </div>
              </section>
            )}
          </div>

          {/* Kolom kanan: daftar kasus */}
          <div className="lg:col-span-3">
            <section className="rounded-lg bg-white ring-1 ring-line overflow-hidden">
              <div className="px-5 py-3 border-b border-line flex items-center justify-between">
                <span className="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">
                  Daftar kasus
                  <span className="ml-2 px-1.5 py-0.5 rounded-full bg-pine/10 text-pine">{cases.length}</span>
                </span>
              </div>

              {cases.length === 0 ? (
                <div className="px-5 py-14 text-center">
                  <p className="font-display font-bold text-ink/70">Belum ada kasus</p>
                  <p className="text-sm text-muted mt-1">
                    {user.role === 'pasien'
                      ? 'Buka kasus pertama melalui form di samping.'
                      : 'Kasus yang terkait dengan Anda akan muncul di sini.'}
                  </p>
                </div>
              ) : (
                <ul className="divide-y divide-line">
                  {cases.map((c) => (
                    <li key={c.id} className="px-5 py-4 flex items-center gap-4 cursor-pointer hover:bg-mint/40 transition-colors" onClick={() => open(c)}>
                      <span className="grid place-items-center w-10 h-10 rounded-full bg-ink text-paper font-display font-bold text-sm shrink-0">
                        {c.patient_name.charAt(0).toUpperCase()}
                      </span>
                      <div className="min-w-0 flex-1">
                        <p className="font-medium text-ink truncate">{c.complaint}</p>
                        <p className="text-sm text-muted truncate">
                          {c.patient_name} · {c.koas_name} · {c.supervisor_name}
                        </p>
                      </div>
                      <Chip tone={STATUS_BADGE[c.status]} label={c.status} />
                    </li>
                  ))}
                </ul>
              )}
            </section>
          </div>
        </div>
      </main>
    </div>
  )
}
