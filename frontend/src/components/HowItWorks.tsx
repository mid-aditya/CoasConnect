const LANGKAH = [
  {
    no: '01',
    title: 'Koas memasang kampanye',
    body: 'Dokter koas membuat akun, melengkapi profil (RS & bidang), lalu memasang kampanye berisi kriteria pasien dan prosedur pendaftaran.',
  },
  {
    no: '02',
    title: 'Pasien menemukan kampanye',
    body: 'Pasien mencari kampanye yang sesuai kondisinya lewat web atau mobile — cukup yang sedang aktif dan cocok dengan kriteria.',
  },
  {
    no: '03',
    title: 'Pasien mendaftar via WhatsApp',
    body: 'Pasien mengikuti prosedur di kartu kampanye dan menghubungi nomor WhatsApp koas. Pendaftaran & jadwal diurus di sana.',
  },
  {
    no: '04',
    title: 'Dibimbing langsung oleh koas',
    body: 'Koas membimbing pasien dengan supervisi dokter spesialis. Interaksi berjalan di WhatsApp — platform ini fokus mencari & mendistribusikan pasien.',
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
              Dari kampanye terpasang sampai pasien terbimbing
            </h2>
            <p className="mt-4 text-muted leading-relaxed">
              Tanpa birokrasi pendaftaran yang panjang. Koas menentukan
              kriteria, pasien yang cocok mengikuti prosedur, dan bimbingan
              berjalan langsung lewat WhatsApp.
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
