import { useEffect, useState } from 'react'
import { getCampaigns, type CampaignView } from '../api/client'

export default function CampaignsSection() {
  const [campaigns, setCampaigns] = useState<CampaignView[]>([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  useEffect(() => {
    getCampaigns()
      .then((res) => setCampaigns(res.data.filter((c) => c.status === 'aktif')))
      .catch((e) => setError(e instanceof Error ? e.message : 'Gagal memuat kampanye'))
      .finally(() => setLoading(false))
  }, [])

  return (
    <section id="campaigns" className="py-20 bg-paper">
      <div className="mx-auto max-w-6xl px-6">
        <div className="text-center mb-12">
          <p className="text-xs font-semibold uppercase tracking-widest text-coral-400">
            Kampanye Aktif
          </p>
          <h2 className="mt-3 font-display font-700 text-3xl sm:text-4xl text-ink-900">
            Temukan Dokter Koas yang Tepat
          </h2>
          <p className="mt-3 text-ink-600 max-w-xl mx-auto">
            Jelajahi kampanye dari dokter koas yang sedang mencari pasien.
            Lihat kriteria, prosedur, dan hubungi langsung via WhatsApp.
          </p>
        </div>

        {loading ? (
          <div className="flex justify-center py-12">
            <span className="w-6 h-6 rounded-full border-2 border-aqua-500 border-t-transparent animate-spin" />
          </div>
        ) : error ? (
          <div className="text-center py-12 text-ink-500">
            <p className="font-medium text-coral-500">Gagal memuat kampanye</p>
            <p className="text-sm mt-1">{error}</p>
          </div>
        ) : campaigns.length === 0 ? (
          <div className="text-center py-12 text-ink-400">
            Belum ada kampanye aktif saat ini.
          </div>
        ) : (
          <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {campaigns.map((c) => (
              <article
                key={c.id}
                className="bg-white rounded-2xl ring-1 ring-ink-100 shadow-sm hover:shadow-md transition-shadow p-6 flex flex-col"
              >
                <div className="flex flex-wrap gap-2 mb-3">
                  <span className="px-2.5 py-0.5 rounded-full text-xs font-medium bg-aqua-50 text-aqua-700 ring-1 ring-aqua-200">
                    {c.specialty}
                  </span>
                  <span className="px-2.5 py-0.5 rounded-full text-xs font-medium bg-mint-50 text-mint-700 ring-1 ring-mint-200">
                    {c.hospital}
                  </span>
                </div>

                <h3 className="font-display font-700 text-lg text-ink-900 leading-snug">
                  {c.title}
                </h3>
                <p className="mt-2 text-sm text-ink-600 line-clamp-3 flex-1">
                  {c.description}
                </p>

                {c.criteria && (
                  <p className="mt-3 text-xs text-ink-500">
                    <span className="font-semibold text-ink-700">Kriteria:</span>{' '}
                    {c.criteria.length > 100 ? c.criteria.slice(0, 100) + '…' : c.criteria}
                  </p>
                )}

                <div className="mt-4 pt-4 border-t border-ink-100 text-xs text-ink-500 space-y-1">
                  <p>
                    <span className="font-semibold text-ink-700">Koas:</span> {c.koas_name}
                  </p>
                  {c.supervisor_name && (
                    <p>
                      <span className="font-semibold text-ink-700">Pembimbing:</span>{' '}
                      {c.supervisor_name}
                    </p>
                  )}
                </div>

                {c.whatsapp && (
                  <a
                    href={`https://wa.me/${c.whatsapp.replace(/[^0-9]/g, '')}`}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="mt-4 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-[#25D366] text-white text-sm font-semibold hover:bg-[#1fb855] transition-colors"
                  >
                    <svg viewBox="0 0 24 24" className="w-4 h-4" fill="currentColor">
                      <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                    </svg>
                    Hubungi via WhatsApp
                  </a>
                )}
              </article>
            ))}
          </div>
        )}
      </div>
    </section>
  )
}
