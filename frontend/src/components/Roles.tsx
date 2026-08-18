const PERAN = [
  {
    nama: 'Pasien',
    fokus: 'Aplikasi mobile',
    body: 'Buka kasus, pilih dokter koas, dan pantau jadwal temu dari HP. Semua perkembangan perawatan ada di satu tempat.',
  },
  {
    nama: 'Dokter Koas',
    fokus: 'Web · catatan sesi',
    body: 'Tangani kasus dengan panduan pembimbing. Catat tiap sesi, dan riwayat kasus tetap utuh sampai pasien selesai.',
  },
  {
    nama: 'Dokter Pembimbing',
    fokus: 'Web · validasi',
    body: 'Awasi penanganan koas, tinjau catatan sesi, lalu nyatakan pasien pulih atau selesai dengan validasi.',
  },
] as const

export default function Roles() {
  return (
    <section id="untuk-siapa" className="py-20 bg-mint">
      <div className="mx-auto max-w-6xl px-6">
        <p className="font-mono text-[11px] uppercase tracking-[0.18em] text-pine">
          Untuk siapa
        </p>
        <h2 className="mt-4 max-w-2xl font-display font-extrabold text-3xl tracking-[-0.015em] text-ink sm:text-4xl">
          Tiga peran, satu berkas perawatan yang sama
        </h2>

        <div className="mt-12 grid gap-10 sm:grid-cols-3 sm:gap-8">
          {PERAN.map((p) => (
            <article key={p.nama} className="border-t-2 border-pine pt-5">
              <h3 className="font-display font-bold text-xl text-ink">{p.nama}</h3>
              <p className="mt-1 font-mono text-[10px] uppercase tracking-[0.14em] text-muted">
                {p.fokus}
              </p>
              <p className="mt-4 text-sm leading-relaxed text-muted">{p.body}</p>
            </article>
          ))}
        </div>
      </div>
    </section>
  )
}
