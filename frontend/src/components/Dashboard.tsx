import { useCallback, useEffect, useState, type FormEvent } from 'react'
import { Link } from 'react-router-dom'
import {
  createCampaign,
  getCampaigns,
  getKoas,
  getMyCampaigns,
  updateCampaign,
  type CampaignView,
  type Koas,
  type User,
} from '../api/client'

function Chip({ tone, label }: { tone: string; label: string }) {
  const styles: Record<string, string> = {
    mint: 'bg-mint/10 text-mint ring-mint/30',
    clay: 'bg-clay/10 text-clay ring-clay/30',
    dim: 'bg-white/5 text-paper/50 ring-white/10',
  }
  return (
    <span
      className={`inline-flex items-center rounded-full px-2.5 py-0.5 font-mono text-[10px] uppercase tracking-[0.12em] ring-1 ${
        styles[tone] ?? styles.dim
      }`}
    >
      {label}
    </span>
  )
}

const fmt = (iso: string) =>
  new Date(iso).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })

const inputCls =
  'w-full px-3.5 py-2.5 text-sm rounded-lg bg-white/5 ring-1 ring-white/10 focus:ring-2 focus:ring-aqua-400 outline-none placeholder:text-paper/30 transition-shadow'

const labelCls = 'block text-sm font-medium text-paper/80 mb-1.5'

function WaLink({ number }: { number: string }) {
  return (
    <a
      href={`https://wa.me/${number}`}
      target="_blank"
      rel="noreferrer"
      className="inline-flex items-center justify-center gap-2 rounded-lg bg-pine text-paper font-semibold hover:bg-aqua-400 transition-colors w-full px-4 py-2.5 text-sm"
    >
      <svg viewBox="0 0 24 24" className="w-4 h-4 shrink-0" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" />
      </svg>
      Daftar lewat WhatsApp
    </a>
  )
}

export default function Dashboard({ user, onLogout }: { user: User; onLogout: () => void }) {
  const [campaigns, setCampaigns] = useState<CampaignView[]>([])
  const [selected, setSelected] = useState<CampaignView | null>(null)
  const [koas, setKoas] = useState<Koas[]>([])
  const [error, setError] = useState<string | null>(null)
  const [notice, setNotice] = useState<string | null>(null)
  const [busy, setBusy] = useState(false)
  const [form, setForm] = useState({ title: '', description: '', criteria: '', procedure: '', whatsapp: '' })

  const isPasien = user.role === 'pasien'
  const isKoas = user.role === 'koas'

  const load = useCallback(async () => {
    setError(null)
    try {
      if (isKoas) {
        const [c, k] = await Promise.all([getMyCampaigns(), getKoas()])
        setCampaigns(c.data)
        setKoas(k.data)
      } else if (user.role === 'spesialis') {
        const [c, k] = await Promise.all([getCampaigns(), getKoas()])
        setCampaigns(c.data)
        setKoas(k.data.filter((x) => x.supervisor_id === user.id))
      } else {
        const c = await getCampaigns()
        setCampaigns(c.data)
      }
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Gagal memuat data')
    }
  }, [isKoas, user.id, user.role])

  useEffect(() => {
    load()
  }, [load])

  const onCreate = async (e: FormEvent) => {
    e.preventDefault()
    setBusy(true)
    setError(null)
    setNotice(null)
    try {
      const res = await createCampaign({
        title: form.title.trim(),
        description: form.description.trim(),
        criteria: form.criteria.trim(),
        procedure: form.procedure.trim(),
        whatsapp: form.whatsapp.trim(),
      })
      setForm({ title: '', description: '', criteria: '', procedure: '', whatsapp: '' })
      setNotice(`Kampanye "${res.data.title}" terpasang dan aktif.`)
      setCampaigns((list) => [res.data, ...list])
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Gagal membuat kampanye')
    } finally {
      setBusy(false)
    }
  }

  const onToggle = async (c: CampaignView) => {
    setError(null)
    setNotice(null)
    try {
      const res = await updateCampaign(c.id, { status: c.status === 'aktif' ? 'tutup' : 'aktif' })
      setCampaigns((list) => list.map((x) => (x.id === c.id ? res.data : x)))
      setSelected((s) => (s?.id === c.id ? res.data : s))
      setNotice(`Kampanye "${res.data.title}" ${res.data.status === 'aktif' ? 'diaktifkan kembali' : 'ditutup'}.`)
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Gagal mengubah status kampanye')
    }
  }

  const set = (key: keyof typeof form) => (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) =>
    setForm((f) => ({ ...f, [key]: e.target.value }))

  const roleTitle =
    isPasien ? 'Cari kampanye'
      : isKoas ? 'Kampanye saya'
        : 'Kampanye koas di bawah supervisi'

  return (
    <div className="min-h-screen bg-ink-950 text-paper">
      <header className="sticky top-0 z-40 bg-ink-900 border-b border-white/10">
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
              <span className="w-1 h-1 rounded-full bg-aqua-400" />
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
              className="px-3 py-1.5 rounded-md ring-1 ring-white/20 text-xs font-medium text-paper/70 hover:text-paper hover:ring-paper/40 transition-colors"
            >
              Keluar
            </button>
          </div>
        </nav>
      </header>

      <main className="mx-auto max-w-6xl px-6 py-8">
        <div className="flex flex-wrap items-end justify-between gap-4">
          <div>
            <p className="font-mono text-[11px] uppercase tracking-[0.18em] text-aqua-400">{user.role}</p>
            <h1 className="mt-1 font-display font-extrabold text-2xl tracking-[-0.015em]">{roleTitle}</h1>
          </div>
          <button
            onClick={load}
            className="px-3 py-2 rounded-lg ring-1 ring-white/15 text-sm text-paper/60 hover:text-paper hover:ring-paper/40 transition-colors"
          >
            Muat ulang
          </button>
        </div>

        {error && (
          <p className="mt-5 text-sm text-clay bg-clay/10 ring-1 ring-clay/30 rounded-lg px-3 py-2">{error}</p>
        )}
        {notice && (
          <p className="mt-5 text-sm text-mint bg-mint/10 ring-1 ring-mint/30 rounded-lg px-3 py-2">{notice}</p>
        )}

        <div className="mt-6 grid lg:grid-cols-5 gap-6 items-start">
          {/* Kolom kiri: aksi / detail */}
          <div className="lg:col-span-2 space-y-6">
            {isKoas && (
              <>
                <section className="rounded-lg bg-ink-900 ring-1 ring-white/10 overflow-hidden">
                  <h2 className="px-5 py-3 border-b border-white/10 font-mono text-[10px] uppercase tracking-[0.14em] text-paper/50">
                    Profil koas
                  </h2>
                  <dl className="p-5 space-y-2.5 text-sm">
                    <div className="flex items-baseline justify-between gap-4">
                      <dt className="font-mono text-[10px] uppercase tracking-[0.14em] text-paper/40">RS</dt>
                      <dd className="font-medium text-paper/90 text-right">{user.hospital || '—'}</dd>
                    </div>
                    <div className="flex items-baseline justify-between gap-4">
                      <dt className="font-mono text-[10px] uppercase tracking-[0.14em] text-paper/40">Bidang</dt>
                      <dd className="font-medium text-paper/90 text-right">{user.specialty || '—'}</dd>
                    </div>
                  </dl>
                  <p className="px-5 pb-5 text-xs text-paper/40">
                    Pembimbing (dokter spesialis) akan dihubungkan lewat fitur profiling koas.
                  </p>
                </section>

                <section className="rounded-lg bg-ink-900 ring-1 ring-white/10 overflow-hidden">
                  <h2 className="px-5 py-3 border-b border-white/10 font-mono text-[10px] uppercase tracking-[0.14em] text-paper/50">
                    Pasang kampanye baru
                  </h2>
                  <form onSubmit={onCreate} className="p-5 space-y-4">
                    <div>
                      <label htmlFor="c-title" className={labelCls}>Judul</label>
                      <input id="c-title" value={form.title} onChange={set('title')} placeholder="cth: Program Pendampingan Hipertensi" className={inputCls} />
                    </div>
                    <div>
                      <label htmlFor="c-desc" className={labelCls}>Deskripsi</label>
                      <textarea id="c-desc" value={form.description} onChange={set('description')} rows={2} placeholder="Ringkasan singkat program…" className={inputCls} />
                    </div>
                    <div>
                      <label htmlFor="c-criteria" className={labelCls}>Kriteria pasien</label>
                      <textarea id="c-criteria" value={form.criteria} onChange={set('criteria')} rows={2} placeholder="cth: Pasien hipertensi usia 40–65 tahun…" className={inputCls} />
                    </div>
                    <div>
                      <label htmlFor="c-procedure" className={labelCls}>Prosedur pendaftaran</label>
                      <textarea id="c-procedure" value={form.procedure} onChange={set('procedure')} rows={3} placeholder={"1) Hubungi nomor WhatsApp\n2) Screening kriteria\n3) …"} className={inputCls} />
                    </div>
                    <div>
                      <label htmlFor="c-wa" className={labelCls}>Nomor WhatsApp (format: 628…)</label>
                      <input id="c-wa" value={form.whatsapp} onChange={set('whatsapp')} placeholder="cth: 6281234567890" className={inputCls} />
                    </div>
                    <button
                      type="submit"
                      disabled={busy}
                      className="w-full px-4 py-2.5 rounded-lg bg-pine text-paper font-semibold hover:bg-aqua-400 disabled:opacity-60 transition-colors"
                    >
                      {busy ? 'Memasang…' : 'Pasang kampanye'}
                    </button>
                  </form>
                </section>
              </>
            )}

            {isPasien &&
              (selected ? (
                <section className="rounded-lg bg-ink-900 ring-1 ring-white/10 overflow-hidden">
                  <div className="px-5 py-3 border-b border-white/10 flex items-center justify-between gap-3">
                    <h2 className="font-mono text-[10px] uppercase tracking-[0.14em] text-paper/50">Detail kampanye</h2>
                    <Chip tone={selected.status === 'aktif' ? 'mint' : 'clay'} label={selected.status} />
                  </div>
                  <div className="p-5 space-y-5">
                    <div>
                      <p className="font-display font-bold text-lg leading-snug">{selected.title}</p>
                      <p className="mt-1.5 text-sm text-paper/60 leading-relaxed">{selected.description}</p>
                      <div className="mt-3 flex flex-wrap gap-1.5">
                        {[selected.specialty, selected.hospital].filter(Boolean).map((t) => (
                          <span key={t} className="rounded-md bg-white/5 px-2 py-0.5 font-mono text-[10px] uppercase tracking-[0.1em] text-paper/60 ring-1 ring-white/10">
                            {t}
                          </span>
                        ))}
                      </div>
                    </div>

                    <div>
                      <p className="font-mono text-[10px] uppercase tracking-[0.14em] text-paper/40">Kriteria pasien</p>
                      <p className="mt-1.5 text-sm text-paper/80 leading-relaxed">{selected.criteria}</p>
                    </div>

                    <div>
                      <p className="font-mono text-[10px] uppercase tracking-[0.14em] text-paper/40">Prosedur</p>
                      <p className="mt-1.5 text-sm text-paper/80 whitespace-pre-line leading-relaxed">{selected.procedure}</p>
                    </div>

                    <dl className="text-sm space-y-2">
                      <div className="flex items-baseline justify-between gap-4">
                        <dt className="font-mono text-[10px] uppercase tracking-[0.14em] text-paper/40">Koas</dt>
                        <dd className="font-medium text-paper/90 text-right">{selected.koas_name}</dd>
                      </div>
                      <div className="flex items-baseline justify-between gap-4">
                        <dt className="font-mono text-[10px] uppercase tracking-[0.14em] text-paper/40">Pembimbing</dt>
                        <dd className="font-medium text-paper/90 text-right">{selected.supervisor_name || '—'}</dd>
                      </div>
                    </dl>

                    {isPasien && <WaLink number={selected.whatsapp} />}
                    <p className="text-xs text-paper/40">
                      Dibuat {fmt(selected.created_at)}. Pendaftaran & bimbingan berjalan di WhatsApp.
                    </p>
                  </div>
                </section>
              ) : (
                <section className="rounded-lg bg-ink-900 ring-1 ring-white/10 p-5">
                  <p className="text-sm text-paper/50">
                    Pilih kampanye di sebelah kanan untuk melihat kriteria, prosedur, dan tombol pendaftaran.
                  </p>
                </section>
              ))}

            {user.role === 'spesialis' && (
              <>
                <section className="rounded-lg bg-ink-900 ring-1 ring-white/10 overflow-hidden">
                  <h2 className="px-5 py-3 border-b border-white/10 font-mono text-[10px] uppercase tracking-[0.14em] text-paper/50">
                    Koas di bawah supervisi
                  </h2>
                  {koas.length === 0 ? (
                    <p className="p-5 text-sm text-paper/50">Belum ada koas yang terhubung ke Anda.</p>
                  ) : (
                    <ul className="divide-y divide-white/10">
                      {koas.map((k) => (
                        <li key={k.id} className="px-5 py-3.5 flex items-center gap-3">
                          <span className="grid place-items-center w-9 h-9 rounded-full bg-pine text-paper font-display font-bold text-sm shrink-0">
                            {k.name.charAt(0).toUpperCase()}
                          </span>
                          <div className="min-w-0">
                            <p className="font-medium text-sm truncate">{k.name}</p>
                            <p className="text-xs text-paper/50 truncate">
                              {k.hospital || '—'} · {k.specialty || '—'}
                            </p>
                          </div>
                        </li>
                      ))}
                    </ul>
                  )}
                </section>

                {selected && (
                  <section className="rounded-lg bg-ink-900 ring-1 ring-white/10 overflow-hidden">
                    <div className="px-5 py-3 border-b border-white/10 flex items-center justify-between gap-3">
                      <h2 className="font-mono text-[10px] uppercase tracking-[0.14em] text-paper/50">Detail kampanye</h2>
                      <Chip tone={selected.status === 'aktif' ? 'mint' : 'clay'} label={selected.status} />
                    </div>
                    <div className="p-5 space-y-5">
                      <div>
                        <p className="font-display font-bold text-lg leading-snug">{selected.title}</p>
                        <p className="mt-1.5 text-sm text-paper/60 leading-relaxed">{selected.description}</p>
                        <div className="mt-3 flex flex-wrap gap-1.5">
                          {[selected.specialty, selected.hospital].filter(Boolean).map((t) => (
                            <span key={t} className="rounded-md bg-white/5 px-2 py-0.5 font-mono text-[10px] uppercase tracking-[0.1em] text-paper/60 ring-1 ring-white/10">
                              {t}
                            </span>
                          ))}
                        </div>
                      </div>

                      <div>
                        <p className="font-mono text-[10px] uppercase tracking-[0.14em] text-paper/40">Kriteria pasien</p>
                        <p className="mt-1.5 text-sm text-paper/80 leading-relaxed">{selected.criteria}</p>
                      </div>

                      <div>
                        <p className="font-mono text-[10px] uppercase tracking-[0.14em] text-paper/40">Prosedur</p>
                        <p className="mt-1.5 text-sm text-paper/80 whitespace-pre-line leading-relaxed">{selected.procedure}</p>
                      </div>

                      <dl className="text-sm space-y-2">
                        <div className="flex items-baseline justify-between gap-4">
                          <dt className="font-mono text-[10px] uppercase tracking-[0.14em] text-paper/40">Koas</dt>
                          <dd className="font-medium text-paper/90 text-right">{selected.koas_name}</dd>
                        </div>
                        <div className="flex items-baseline justify-between gap-4">
                          <dt className="font-mono text-[10px] uppercase tracking-[0.14em] text-paper/40">Pembimbing</dt>
                          <dd className="font-medium text-paper/90 text-right">{selected.supervisor_name || '—'}</dd>
                        </div>
                      </dl>

                      <p className="text-xs text-paper/40">Dibuat {fmt(selected.created_at)}.</p>
                    </div>
                  </section>
                )}
              </>
            )}
          </div>

          {/* Kolom kanan: daftar kampanye */}
          <div className="lg:col-span-3">
            <section className="rounded-lg bg-ink-900 ring-1 ring-white/10 overflow-hidden">
              <div className="px-5 py-3 border-b border-white/10 flex items-center justify-between">
                <span className="font-mono text-[10px] uppercase tracking-[0.14em] text-paper/50">
                  {isKoas ? 'Daftar kampanye saya' : 'Daftar kampanye'}
                  <span className="ml-2 px-1.5 py-0.5 rounded-full bg-mint/10 text-mint">{campaigns.length}</span>
                </span>
              </div>

              {campaigns.length === 0 ? (
                <div className="px-5 py-14 text-center">
                  <p className="font-display font-bold text-paper/70">
                    {isKoas ? 'Belum ada kampanye' : isPasien ? 'Belum ada kampanye aktif' : 'Belum ada kampanye'}
                  </p>
                  <p className="text-sm text-paper/50 mt-1">
                    {isKoas
                      ? 'Pasang kampanye pertama melalui form di samping.'
                      : 'Kampanye yang sesuai akan muncul di sini.'}
                  </p>
                </div>
              ) : (
                <ul className="divide-y divide-white/10">
                  {campaigns.map((c) => (
                    <li
                      key={c.id}
                      className="px-5 py-4 flex items-start gap-4 cursor-pointer hover:bg-white/5 transition-colors"
                      onClick={() => setSelected(c)}
                    >
                      <span className="grid place-items-center w-10 h-10 rounded-full bg-pine text-paper font-display font-bold text-sm shrink-0">
                        {c.koas_name.charAt(0).toUpperCase()}
                      </span>
                      <div className="min-w-0 flex-1">
                        <div className="flex items-start justify-between gap-3">
                          <p className="font-medium truncate">{c.title}</p>
                          <Chip tone={c.status === 'aktif' ? 'mint' : 'clay'} label={c.status} />
                        </div>
                        <p className="text-sm text-paper/60 truncate mt-0.5">{c.description}</p>
                        <p className="text-xs text-paper/40 truncate mt-1 font-mono">
                          {c.koas_name} · {c.hospital || '—'} · {c.specialty || '—'} · {fmt(c.created_at)}
                        </p>
                        {isKoas && (
                          <div className="mt-2.5" onClick={(e) => e.stopPropagation()}>
                            <button
                              onClick={() => onToggle(c)}
                              className={`px-3 py-1.5 rounded-lg text-xs font-semibold ring-1 transition-colors ${
                                c.status === 'aktif'
                                  ? 'bg-clay/10 text-clay ring-clay/30 hover:bg-clay/20'
                                  : 'bg-mint/10 text-mint ring-mint/30 hover:bg-mint/20'
                              }`}
                            >
                              {c.status === 'aktif' ? 'Tutup kampanye' : 'Aktifkan kembali'}
                            </button>
                          </div>
                        )}
                      </div>
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
