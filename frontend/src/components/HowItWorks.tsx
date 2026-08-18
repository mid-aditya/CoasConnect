const LANGKAH = [
  {
    no: '01',
    title: 'Pasien buka kasus',
    body: 'Pasien memilih dokter koas dan mencatat keluhan awal. Janji temu pertama langsung terjadwal, kasus pun terbuka.',
  },
  {
    no: '02',
    title: 'Koas menangani',
    body: 'Dokter koas melakukan anamnesis dan penanganan. Setiap sesi dicatat ke dalam berkas kasus yang sama.',
  },
  {
    no: '03',
    title: 'Pembimbing menilai',
    body: 'Dokter spesialis meninjau catatan sesi dan memberikan supervisi atas penanganan yang dilakukan koas.',
  },
  {
    no: '04',
    title: 'Sampai pulih',
    body: 'Status kasus berjalan dari aktif ke pulih, lalu ditutup saat pasien dinyatakan selesai dirawat.',
  },
] as const

export default function HowItWorks() {
  return (
    <section id="cara-kerja" className="py-20 bg-white">
      <div className="mx-auto max-w-6xl px-6">
        <div className="grid lg:grid-cols-[0.85fr_1.15fr] gap-12">
          <div>
            <p className="font-mono text-[11px] uppercase tracking-[0.18em] text-pine">
              Cara kerja
            </p>
            <h2 className="mt-4 font-display font-extrabold text-3xl tracking-[-0.015em] text-ink sm:text-4xl">
              Satu alur yang tidak terputus, dari kasus dibuka sampai ditutup
            </h2>
            <p className="mt-4 text-muted leading-relaxed">
              Tidak ada lagi riwayat yang tersebar di chat atau catatan
              terpisah. Semua pihak melihat perkembangan yang sama di berkas
              yang sama.
            </p>
          </div>

          <ol className="divide-y divide-line">
            {LANGKAH.map((l) => (
              <li key={l.no} className="grid sm:grid-cols-[3.5rem_1fr] gap-2 sm:gap-6 py-6 first:pt-0 last:pb-0">
                <span className="font-mono text-sm text-pine" aria-hidden="true">
                  {l.no}
                </span>
                <div>
                  <h3 className="font-display font-bold text-lg text-ink">{l.title}</h3>
                  <p className="mt-1.5 text-sm leading-relaxed text-muted">{l.body}</p>
                </div>
              </li>
            ))}
          </ol>
        </div>
      </div>
    </section>
  )
}
