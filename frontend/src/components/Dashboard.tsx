import { useCallback, useEffect, useState, type FormEvent } from 'react'
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
  aktif: 'bg-aqua-500/10 text-aqua-500 ring-aqua-500/20',
  pulih: 'bg-coral-500/10 text-coral-500 ring-coral-500/20',
  selesai: 'bg-ink-900/10 text-ink-900/50 ring-ink-900/10',
}

const APPT_BADGE: Record<string, string> = {
  terjadwal: 'bg-aqua-500/10 text-aqua-500 ring-aqua-500/20',
  selesai: 'bg-ink-900/10 text-ink-900/60 ring-ink-900/10',
  dibatalkan: 'bg-coral-500/10 text-coral-500 ring-coral-500/20',
}

const fmt = (iso: string) => new Date(iso).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' })
const toISO = (local: string) => new Date(local).toISOString()

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
    <section id="kasus" className="py-16 bg-sand-100">
      <div className="mx-auto max-w-6xl px-6">
        <div className="flex flex-wrap items-end justify-between gap-4">
          <div>
            <p className="text-xs font-semibold uppercase tracking-widest text-coral-500">
              {user.role} · {user.name}
            </p>
            <h2 className="mt-2 font-display font-700 text-3xl text-ink-900">{roleTitle}</h2>
          </div>
          <button
            onClick={onLogout}
            className="px-4 py-2 rounded-full text-sm font-medium ring-1 ring-ink-900/10 text-ink-900/60 hover:text-coral-500 hover:ring-coral-500/30 transition-colors"
          >
            Keluar
          </button>
        </div>

        {error && (
          <p className="mt-6 text-sm text-coral-500 bg-coral-500/5 ring-1 ring-coral-500/20 rounded-lg px-3 py-2">
            {error}
          </p>
        )}
        {notice && (
          <p className="mt-6 text-sm text-aqua-500 bg-aqua-500/5 ring-1 ring-aqua-500/20 rounded-lg px-3 py-2">
            {notice}
          </p>
        )}

        <div className="mt-8 grid lg:grid-cols-5 gap-8">
          {/* Kolom kiri: aksi */}
          <div className="lg:col-span-2 space-y-6">
            {user.role === 'pasien' && (
              <form
                onSubmit={onCreateCase}
                className="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-ink-900/5 space-y-4"
              >
                <h3 className="font-display font-700 text-lg text-ink-900">Buat janji temu baru</h3>
                <div>
                  <label htmlFor="koas" className="block text-sm font-medium text-ink-900 mb-1.5">
                    Dokter koas
                  </label>
                  <select
                    id="koas"
                    value={koasId}
                    onChange={(e) => setKoasId(e.target.value)}
                    className="w-full px-4 py-2.5 rounded-xl bg-sand-100 ring-1 ring-ink-900/10 focus:ring-2 focus:ring-aqua-500 outline-none transition-shadow"
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
                  <label htmlFor="complaint" className="block text-sm font-medium text-ink-900 mb-1.5">
                    Keluhan awal
                  </label>
                  <textarea
                    id="complaint"
                    value={complaint}
                    onChange={(e) => setComplaint(e.target.value)}
                    rows={3}
                    placeholder="cth: Demam tinggi sejak 2 hari, batuk…"
                    className="w-full px-4 py-2.5 rounded-xl bg-sand-100 ring-1 ring-ink-900/10 focus:ring-2 focus:ring-aqua-500 outline-none transition-shadow"
                  />
                </div>
                <div>
                  <label htmlFor="when" className="block text-sm font-medium text-ink-900 mb-1.5">
                    Jadwal temu pertama
                  </label>
                  <input
                    id="when"
                    type="datetime-local"
                    value={when}
                    onChange={(e) => setWhen(e.target.value)}
                    className="w-full px-4 py-2.5 rounded-xl bg-sand-100 ring-1 ring-ink-900/10 focus:ring-2 focus:ring-aqua-500 outline-none transition-shadow"
                  />
                </div>
                <button
                  type="submit"
                  className="w-full px-4 py-3 rounded-xl bg-ink-900 text-sand-100 font-semibold hover:bg-ink-800 hover:-translate-y-0.5 transition-all"
                >
                  Buka kasus
                </button>
              </form>
            )}

            {selected && (
              <div className="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-ink-900/5 space-y-4">
                <div className="flex items-center justify-between">
                  <h3 className="font-display font-700 text-lg text-ink-900">Sesi monitoring</h3>
                  <span className={`px-2 py-0.5 rounded-full text-xs font-medium ring-1 ${STATUS_BADGE[selected.status]}`}>
                    {selected.status}
                  </span>
                </div>
                <p className="text-sm text-ink-900/60 leading-relaxed">
                  Kasus #{selected.id} — {selected.patient_name} · ditangani {selected.koas_name}
                  <br />
                  Pembimbing: {selected.supervisor_name}
                </p>

                <ul className="divide-y divide-ink-900/5">
                  {appts.length === 0 && <li className="py-2 text-sm text-ink-900/40">Belum ada sesi.</li>}
                  {appts.map((a) => (
                    <li key={a.id} className="py-3 space-y-1">
                      <div className="flex items-center justify-between gap-3">
                        <span className="text-sm font-medium text-ink-900">{fmt(a.scheduled_at)}</span>
                        <span className={`px-2 py-0.5 rounded-full text-xs font-medium ring-1 ${APPT_BADGE[a.status]}`}>
                          {a.status}
                        </span>
                      </div>
                      {a.notes && <p className="text-sm text-ink-900/55">{a.notes}</p>}
                      {a.status === 'terjadwal' && user.role === 'koas' && (
                        <div className="flex gap-2 pt-1">
                          <input
                            value={notes}
                            onChange={(e) => setNotes(e.target.value)}
                            placeholder="Catatan hasil sesi…"
                            className="flex-1 px-3 py-1.5 text-sm rounded-lg bg-sand-100 ring-1 ring-ink-900/10 focus:ring-2 focus:ring-aqua-500 outline-none"
                          />
                          <button
                            onClick={() => onCompleteAppt(a)}
                            className="px-3 py-1.5 text-sm rounded-lg bg-aqua-500 text-ink-950 font-semibold hover:bg-aqua-400"
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
                      className="flex-1 px-3 py-2 text-sm rounded-xl bg-sand-100 ring-1 ring-ink-900/10 focus:ring-2 focus:ring-aqua-500 outline-none"
                    />
                    <button
                      type="submit"
                      className="px-4 py-2 text-sm rounded-xl bg-ink-900 text-sand-100 font-semibold hover:bg-ink-800"
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
                        className="flex-1 px-4 py-2 text-sm rounded-xl bg-coral-500/10 ring-1 ring-coral-500/30 text-coral-500 font-semibold hover:bg-coral-500/20"
                      >
                        Tandai pulih
                      </button>
                    )}
                    <button
                      onClick={() => onSetStatus(selected, 'selesai')}
                      className="flex-1 px-4 py-2 text-sm rounded-xl bg-ink-900 text-sand-100 font-semibold hover:bg-ink-800"
                    >
                      Tutup kasus
                    </button>
                  </div>
                )}
              </div>
            )}
          </div>

          {/* Kolom kanan: daftar kasus */}
          <div className="lg:col-span-3">
            <div className="rounded-2xl bg-white shadow-sm ring-1 ring-ink-900/5 overflow-hidden">
              <div className="px-5 py-4 border-b border-ink-900/5 flex items-center justify-between">
                <span className="text-sm font-semibold text-ink-900">
                  {roleTitle}{' '}
                  <span className="ml-1 px-2 py-0.5 rounded-full bg-aqua-500/10 text-aqua-500 text-xs font-medium">
                    {cases.length}
                  </span>
                </span>
                <button onClick={load} className="text-xs text-ink-900/40 hover:text-aqua-500 transition-colors">
                  Muat ulang
                </button>
              </div>

              {cases.length === 0 ? (
                <div className="px-5 py-14 text-center">
                  <p className="font-display text-ink-900/70">Belum ada kasus</p>
                  <p className="text-sm text-ink-900/40 mt-1">
                    {user.role === 'pasien'
                      ? 'Buka kasus pertama melalui form di samping.'
                      : 'Kasus yang terkait dengan Anda akan muncul di sini.'}
                  </p>
                </div>
              ) : (
                <ul className="divide-y divide-ink-900/5">
                  {cases.map((c) => (
                    <li key={c.id} className="px-5 py-4 flex items-center gap-4 group cursor-pointer" onClick={() => open(c)}>
                      <span className="grid place-items-center w-10 h-10 rounded-full bg-ink-900 text-sand-100 font-display font-700 text-sm shrink-0">
                        {c.patient_name.charAt(0).toUpperCase()}
                      </span>
                      <div className="min-w-0 flex-1">
                        <p className="font-medium text-ink-900 truncate">{c.complaint}</p>
                        <p className="text-sm text-ink-900/45 truncate">
                          {c.patient_name} · {c.koas_name} · {c.supervisor_name}
                        </p>
                      </div>
                      <span className={`px-2 py-0.5 rounded-full text-xs font-medium ring-1 shrink-0 ${STATUS_BADGE[c.status]}`}>
                        {c.status}
                      </span>
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
