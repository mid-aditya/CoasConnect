const PERAN = [
  {
    nama: 'Pasien',
    fokus: 'Web & mobile',
    body: 'Cari kampanye yang sesuai kondisi, daftar lewat WhatsApp, dan dapatkan bimbingan langsung dari dokter koas.',
  },
  {
    nama: 'Dokter Koas',
    fokus: 'Web · pasang kampanye',
    body: 'Buat profil (RS & bidang), pasang kampanye penjaringan, dan kelola status kampanye — pasien yang cocok datang sendiri.',
  },
  {
    nama: 'Dokter Pembimbing',
    fokus: 'Web · supervisi',
    body: 'Pantau kampanye & pasien yang dibimbing dokter koas di bawah supervisi Anda.',
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
          Tiga peran dalam satu alur penjaringan
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
