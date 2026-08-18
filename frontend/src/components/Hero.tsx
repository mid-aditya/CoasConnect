import { Link } from 'react-router-dom'

// Kartu kampanye: artefak inti produk — koas memasang kriteria & prosedur,
// pasien mendaftar via WhatsApp lalu dibimbing langsung.

const LANGKAH = [
  { no: '01', label: 'Hubungi via WhatsApp' },
  { no: '02', label: 'Skrining & konfirmasi kriteria' },
  { no: '03', label: 'Pendampingan & evaluasi' },
] as const

function CampaignCard() {
  return (
    <figure className="rounded-xl bg-white ring-1 ring-line shadow-[0_1px_3px_rgba(22,36,29,0.06)]">
      <div className="flex items-center justify-between gap-3 border-b border-line px-5 py-4">
        <span className="font-mono text-xs text-ink">CP-2026-0142</span>
        <span className="inline-flex items-center gap-1.5 font-mono text-[10px] uppercase tracking-[0.14em] text-pine">
          <span className="w-1.5 h-1.5 rounded-full bg-pine" />
          Aktif
        </span>
      </div>

      <div className="px-5 pt-4">
        <p className="font-display font-bold text-lg tracking-[-0.01em] text-ink">
          Program Pendampingan Hipertensi
        </p>
        <div className="mt-2 flex flex-wrap gap-1.5">
          {['Penyakit Dalam', 'RSUD Dr. Soetomo'].map((t) => (
            <span key={t} className="rounded-md bg-mint px-2 py-0.5 font-mono text-[10px] uppercase tracking-[0.1em] text-pine">
              {t}
            </span>
          ))}
        </div>
      </div>

      <dl className="px-5 py-4 space-y-2.5">
        <div className="flex items-baseline justify-between gap-4">
          <dt className="font-mono text-[10px] uppercase tracking-[0.14em] text-muted shrink-0">Kriteria</dt>
          <dd className="text-sm text-ink/80 text-right">
            Pasien hipertensi usia 40–65 th, siap kontrol rutin
          </dd>
        </div>
        <div className="flex items-baseline justify-between gap-4">
          <dt className="font-mono text-[10px] uppercase tracking-[0.14em] text-muted shrink-0">Koas</dt>
          <dd className="text-sm font-medium text-ink text-right">Andi Pratama</dd>
        </div>
        <div className="flex items-baseline justify-between gap-4">
          <dt className="font-mono text-[10px] uppercase tracking-[0.14em] text-muted shrink-0">Pembimbing</dt>
          <dd className="text-sm font-medium text-ink text-right">Dr. Sari Wulandari, Sp.PD</dd>
        </div>
      </dl>

      <div className="border-t border-line px-5 py-4">
        <p className="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">Prosedur</p>
        <ol className="mt-3 space-y-2">
          {LANGKAH.map((s) => (
            <li key={s.no} className="flex items-center gap-3 text-sm">
              <span className="font-mono text-[10px] text-pine">{s.no}</span>
              <span className="text-ink/80">{s.label}</span>
            </li>
          ))}
        </ol>
      </div>

      <div className="border-t border-line px-5 py-4">
        <span className="inline-flex items-center gap-2 rounded-lg bg-pine px-4 py-2 text-sm font-semibold text-paper">
          <svg viewBox="0 0 24 24" className="w-4 h-4" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" />
          </svg>
          Daftar lewat WhatsApp
        </span>
      </div>

      <figcaption className="border-t border-line px-5 py-3 font-mono text-[10px] text-muted">
        Contoh kartu kampanye · data fiktif
      </figcaption>
    </figure>
  )
}

export default function Hero() {
  return (
    <section id="top" className="relative overflow-hidden bg-paper">
      <div className="relative mx-auto max-w-6xl px-6 pt-16 pb-24 lg:pt-24 grid lg:grid-cols-[1.05fr_0.95fr] gap-14 lg:gap-16 items-center">
        <div>
          <p className="font-mono text-[11px] uppercase tracking-[0.18em] text-pine">
            Platform penjaringan pasien untuk dokter koas
          </p>

          <h1 className="mt-5 font-display font-extrabold text-[2.6rem] leading-[1.06] tracking-[-0.02em] text-ink sm:text-6xl">
            Koas memasang kampanye, pasien yang cocok datang sendiri.
          </h1>

          <p className="mt-6 text-lg leading-relaxed text-muted max-w-xl">
            CoasConnect mempertemukan dokter koas dengan pasien sesuai
            kriteria. Koas memasang kampanye berisi kriteria & prosedur,
            pasien menemukannya di web atau mobile, lalu dibimbing langsung
            — komunikasi berjalan lewat WhatsApp.
          </p>

          <div className="mt-8 flex flex-wrap gap-3">
            <Link
              to="/login"
              className="px-5 py-3 rounded-lg bg-pine text-paper text-sm font-semibold hover:bg-ink transition-colors"
            >
              Mulai kampanye
            </Link>
            <a
              href="#cara-kerja"
              className="px-5 py-3 rounded-lg ring-1 ring-line text-ink text-sm font-semibold hover:ring-ink transition-colors"
            >
              Lihat cara kerja
            </a>
          </div>
        </div>

        <div className="relative lg:pl-2">
          <CampaignCard />
        </div>
      </div>
    </section>
  )
}
