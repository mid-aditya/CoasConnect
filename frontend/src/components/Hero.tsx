import { Link } from 'react-router-dom'

// Kartu kasus: artefak inti produk — berkas perawatan yang mencatat
// pasien, koas, pembimbing, dan riwayat sesi sampai pulih/selesai.

const SESI = [
  { tgl: '12 JUN · 09:30', catatan: 'Anamnesis & keluhan awal' },
  { tgl: '26 JUN · 14:00', catatan: 'Evaluasi respons terapi' },
  { tgl: '10 JUL · 11:15', catatan: 'Supervisi pembimbing' },
] as const

function CaseCard() {
  return (
    <figure className="rounded-xl bg-white ring-1 ring-line shadow-[0_1px_3px_rgba(22,36,29,0.06)]">
      <div className="flex items-center justify-between border-b border-line px-5 py-4">
        <span className="font-mono text-xs text-ink">K-2026-0417</span>
        <span className="inline-flex items-center gap-1.5 font-mono text-[10px] uppercase tracking-[0.14em] text-clay">
          <span className="w-1.5 h-1.5 rounded-full bg-clay" />
          Aktif
        </span>
      </div>

      <dl className="px-5 py-4 space-y-2.5">
        {[
          ['Pasien', 'Tn. Budi · 42 th'],
          ['Koas', 'Andi Pratama'],
          ['Pembimbing', 'Dr. Sari Wulandari, Sp.PD'],
        ].map(([k, v]) => (
          <div key={k} className="flex items-baseline justify-between gap-4">
            <dt className="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">{k}</dt>
            <dd className="text-sm font-medium text-ink text-right">{v}</dd>
          </div>
        ))}
      </dl>

      <div className="border-t border-line px-5 py-4">
        <p className="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">
          Riwayat sesi
        </p>
        <ul className="mt-3 space-y-2.5">
          {SESI.map((s) => (
            <li key={s.tgl} className="flex items-center gap-3 text-sm">
              <svg viewBox="0 0 16 16" className="w-4 h-4 shrink-0 text-pine" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                <circle cx="8" cy="8" r="6.5" />
                <path d="M5.2 8.4l2 2 3.6-4" />
              </svg>
              <span className="font-mono text-xs text-muted">{s.tgl}</span>
              <span className="text-ink/80">{s.catatan}</span>
            </li>
          ))}
        </ul>
      </div>

      <div className="border-t border-line px-5 py-4">
        <div className="flex items-center gap-3">
          {[
            ['Aktif', 'bg-ink text-paper'],
            ['Pulih', ''],
            ['Selesai', ''],
          ].map(([label, active], i) => (
            <div key={label} className="flex items-center gap-3">
              <span
                className={`inline-flex items-center gap-1.5 rounded-md px-2.5 py-1 font-mono text-[10px] uppercase tracking-[0.12em] ${
                  active || i === 0 ? active + ' ring-1 ring-line' : 'text-muted ring-1 ring-line'
                }`}
              >
                {label}
              </span>
              {i < 2 && <span className="w-4 h-px bg-line" aria-hidden="true" />}
            </div>
          ))}
        </div>
      </div>

      <figcaption className="border-t border-line px-5 py-3 font-mono text-[10px] text-muted">
        Contoh berkas kasus · data fiktif
      </figcaption>
    </figure>
  )
}

// Garis ECG: metafora monitoring yang literal — direkam, bukan dekorasi.
// pathLength=1 menormalkan panjang path sehingga dasharray 1 = seluruh garis.
function EcgLine() {
  return (
    <svg
      viewBox="0 0 1200 56"
      preserveAspectRatio="none"
      className="absolute inset-x-0 bottom-0 w-full h-14 text-pine/25"
      aria-hidden="true"
    >
      <path
        className="ecg-line animate-ecg-draw motion-reduce:animate-none"
        pathLength={1}
        d="M0 28 H240 l10 -14 8 30 10 -34 8 26 10 -16 6 8 H600 l10 -14 8 30 10 -34 8 26 10 -16 6 8 H1200"
        fill="none"
        stroke="currentColor"
        strokeWidth="1.5"
        strokeLinecap="round"
        strokeLinejoin="round"
      />
    </svg>
  )
}

export default function Hero() {
  return (
    <section id="top" className="relative overflow-hidden bg-paper">
      <div className="relative mx-auto max-w-6xl px-6 pt-16 pb-24 lg:pt-24 grid lg:grid-cols-[1.05fr_0.95fr] gap-14 lg:gap-16 items-center">
        <div>
          <p className="font-mono text-[11px] uppercase tracking-[0.18em] text-pine">
            Platform monitoring dokter koas
          </p>

          <h1 className="mt-5 font-display font-extrabold text-[2.6rem] leading-[1.06] tracking-[-0.02em] text-ink sm:text-6xl">
            Perawatan pasien tercatat, dari janji temu sampai pulih.
          </h1>

          <p className="mt-6 text-lg leading-relaxed text-muted max-w-xl">
            CoasConnect menyatukan pasien, dokter koas, dan dokter pembimbing
            dalam satu berkas perawatan. Kasus dibuka di janji temu pertama,
            setiap sesi terekam, sampai pasien dinyatakan pulih atau selesai.
          </p>

          <div className="mt-8 flex flex-wrap gap-3">
            <Link
              to="/login"
              className="px-5 py-3 rounded-lg bg-pine text-paper text-sm font-semibold hover:bg-ink transition-colors"
            >
              Buka kasus pertama
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
          <CaseCard />
        </div>
      </div>

      <EcgLine />
    </section>
  )
}
