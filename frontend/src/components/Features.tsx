const FEATURES = [
  {
    title: 'Janji temu pertama',
    body: 'Pasien memilih dokter koas dan mencatat keluhan awal — kasus langsung terbuka beserta jadwal temu pertama.',
    icon: (
      <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round">
        <rect x="3" y="5" width="18" height="16" rx="2" />
        <path d="M8 3v4M16 3v4M3 10h18" />
      </svg>
    ),
  },
  {
    title: 'Penanganan dokter koas',
    body: 'Dokter koas menangani kasus dengan pendampingan dokter spesialis sebagai pembimbing supervisi.',
    icon: (
      <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round">
        <path d="M12 21s-7-4.6-9.3-9A5.4 5.4 0 0 1 12 6.6 5.4 5.4 0 0 1 21.3 12C19 16.4 12 21 12 21Z" />
      </svg>
    ),
  },
  {
    title: 'Sesi monitoring tercatat',
    body: 'Setiap sesi dicatat: jadwal, hasil, dan catatan perkembangan pasien — riwayat utuh dalam satu kasus.',
    icon: (
      <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round">
        <path d="M3 17l5-6 4 3 6-8 3 2" />
        <path d="M3 21h18" />
      </svg>
    ),
  },
  {
    title: 'Status sampai pulih',
    body: 'Kasus berjalan dari aktif, ditandai pulih, hingga selesai — jelas kapan pasien dinyatakan selesai dirawat.',
    icon: (
      <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round">
        <circle cx="12" cy="12" r="9" />
        <path d="M8.5 12.5l2.5 2.5 5-5" />
      </svg>
    ),
  },
]

export default function Features() {
  return (
    <section id="fitur" className="py-16 bg-mist-100">
      <div className="mx-auto max-w-6xl px-6">
        <div className="max-w-xl">
          <p className="text-xs font-semibold uppercase tracking-widest text-coral-500">
            Mengapa CoasConnect
          </p>
          <h2 className="mt-2 font-display font-700 text-3xl text-ink-900">
            Perawatan yang terpantau dari awal sampai selesai
          </h2>
          <p className="mt-3 text-ink-900/60 leading-relaxed">
            Dibangun dengan Go, React, dan React Native — cepat diakses dari
            klinik maupun dari rumah.
          </p>
        </div>

        <div className="mt-10 grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
          {FEATURES.map((f) => (
            <article
              key={f.title}
              className="group rounded-2xl bg-white p-6 ring-1 ring-ink-900/5 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all"
            >
              <span className="grid place-items-center w-12 h-12 rounded-xl bg-ink-900 text-aqua-400 group-hover:bg-aqua-500 group-hover:text-ink-950 transition-colors">
                {f.icon}
              </span>
              <h3 className="mt-5 font-display font-700 text-lg text-ink-900">{f.title}</h3>
              <p className="mt-2 text-sm leading-relaxed text-ink-900/55">{f.body}</p>
            </article>
          ))}
        </div>
      </div>
    </section>
  )
}
