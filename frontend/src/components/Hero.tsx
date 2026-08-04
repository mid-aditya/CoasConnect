import NetworkCanvas from './NetworkCanvas'

export default function Hero() {
  return (
    <section id="top" className="relative overflow-hidden bg-ink-950 text-sand-100">
      <div className="absolute inset-0 motif-grid opacity-60" />
      <div className="absolute -top-40 -right-40 w-[480px] h-[480px] rounded-full bg-aqua-500/10 blur-3xl" />
      <div className="absolute -bottom-32 -left-32 w-[420px] h-[420px] rounded-full bg-coral-500/10 blur-3xl" />

      <div className="relative mx-auto max-w-6xl px-6 pt-20 pb-10 grid lg:grid-cols-2 gap-12 items-center">
        <div>
          <p className="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium tracking-wide bg-aqua-500/10 ring-1 ring-aqua-500/30 text-aqua-400">
            <span className="w-1.5 h-1.5 rounded-full bg-aqua-400 animate-pulse" />
            Monitoring pasien · dokter koas
          </p>

          <h1 className="mt-6 font-display font-700 text-5xl sm:text-6xl tracking-tight leading-[1.05]">
            Satu alur,
            <br />
            dari janji temu{' '}
            <span className="text-aqua-400 relative">
              sampai pulih
              <svg viewBox="0 0 120 8" className="absolute -bottom-2 left-0 w-full" preserveAspectRatio="none">
                <path d="M2 6 C 30 1, 90 1, 118 5" fill="none" stroke="var(--color-coral-500)" strokeWidth="2.5" strokeLinecap="round" />
              </svg>
            </span>
          </h1>

          <p className="mt-6 text-lg leading-relaxed text-sand-100/70 max-w-md">
            CoasConnect menghubungkan pasien dengan dokter koas yang dibimbing
            dokter spesialis. Setiap kasus tercatat dari janji temu pertama,
            tiap sesi dimonitor, sampai pasien pulih atau dinyatakan selesai.
          </p>

          <div className="mt-8 flex flex-wrap gap-3">
            <a
              href="#kasus"
              className="px-6 py-3 rounded-full bg-aqua-500 text-ink-950 font-semibold hover:bg-aqua-400 hover:-translate-y-0.5 transition-all shadow-lg shadow-aqua-500/20"
            >
              Mulai konsultasi
            </a>
            <a
              href="#status"
              className="px-6 py-3 rounded-full ring-1 ring-white/20 text-sand-100 hover:bg-white/5 hover:-translate-y-0.5 transition-all"
            >
              Cek status API
            </a>
          </div>

          <dl className="mt-12 grid grid-cols-3 gap-6 max-w-sm">
            {[
              ['3', 'peran terhubung'],
              ['1', 'alur: janji → pulih'],
              ['100%', 'sesi terekam'],
            ].map(([value, label]) => (
              <div key={label}>
                <dt className="sr-only">{label}</dt>
                <dd className="font-display font-700 text-2xl text-aqua-400">{value}</dd>
                <dd className="text-xs text-sand-100/50 mt-1">{label}</dd>
              </div>
            ))}
          </dl>
        </div>

        <div className="relative">
          <div className="rounded-2xl bg-ink-900/70 ring-1 ring-white/10 p-6 shadow-2xl">
            <div className="flex items-center justify-between mb-4">
              <span className="text-xs font-medium text-sand-100/50 uppercase tracking-widest">
                Alur perawatan
              </span>
              <span className="inline-flex items-center gap-1.5 text-xs text-aqua-400">
                <span className="w-1.5 h-1.5 rounded-full bg-aqua-400 animate-pulse" />
                live
              </span>
            </div>
            <NetworkCanvas />
            <p className="mt-4 text-xs text-sand-100/45 leading-relaxed">
              Setiap titik adalah peran: pasien, dokter koas, dan pembimbing.
              Setiap garis adalah alur janji temu dan supervisi.
            </p>
          </div>
        </div>
      </div>
    </section>
  )
}
