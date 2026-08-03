const FEATURES = [
  {
    title: 'Harga pasar real-time',
    body: 'Info harga ikan dari pelabuhan terdekat diperbarui otomatis, sehingga nelayan tahu kapan dan ke mana menjual.',
    icon: (
      <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round">
        <path d="M3 17l5-6 4 3 6-8 3 2" />
        <path d="M3 21h18" />
      </svg>
    ),
  },
  {
    title: 'Logistik bersama',
    body: 'Kapal dan armada pendingin saling terhubung, mengisi rute yang sama tanpa perjalanan kosong.',
    icon: (
      <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round">
        <path d="M3 7h11v8H3zM14 10h4l3 3v2h-7z" />
        <circle cx="7" cy="17.5" r="1.8" />
        <circle cx="17" cy="17.5" r="1.8" />
      </svg>
    ),
  },
  {
    title: 'Koperasi terhubung',
    body: 'Administrasi koperasi dari satu dasbor: iuran, pinjaman, dan pembagian hasil transparan.',
    icon: (
      <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round">
        <circle cx="9" cy="8" r="3.2" />
        <circle cx="17" cy="9" r="2.4" />
        <path d="M3.5 20c.8-3.2 2.9-5 5.5-5s4.7 1.8 5.5 5M13.5 15.2c2-.6 3.7-.4 4.8 1.3" />
      </svg>
    ),
  },
  {
    title: 'Aplikasi mobile',
    body: 'Akses jaringan dari mana saja lewat aplikasi mobile — bahkan saat berada di tengah laut.',
    icon: (
      <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round">
        <rect x="7" y="2.5" width="10" height="19" rx="2.5" />
        <path d="M11 18.5h2" />
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
            Jaringan yang bekerja untuk nelayan
          </h2>
          <p className="mt-3 text-ink-900/60 leading-relaxed">
            Dibangun dengan Go, React, dan React Native — cepat di laut, andal
            di darat.
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
