import { Link } from 'react-router-dom'

export default function Footer() {
  return (
    <footer className="bg-ink text-paper">
      <div className="mx-auto max-w-6xl px-6 py-12">
        <div className="flex flex-col md:flex-row md:items-center justify-between gap-6">
          <div>
            <p className="font-display font-bold text-lg">
              Coas<span className="text-paper/70">Connect</span>
            </p>
            <p className="mt-1 text-sm text-paper/50">
              Monitoring pasien yang ditangani dokter koas, dibimbing dokter
              spesialis.
            </p>
          </div>
          <div className="flex flex-wrap gap-x-8 gap-y-3 text-sm text-paper/60">
            <a href="#cara-kerja" className="hover:text-paper transition-colors">
              Cara kerja
            </a>
            <a href="#untuk-siapa" className="hover:text-paper transition-colors">
              Untuk siapa
            </a>
            <a href="#status" className="hover:text-paper transition-colors">
              Status
            </a>
            <Link to="/login" className="hover:text-paper transition-colors">
              Masuk
            </Link>
          </div>
        </div>
        <div className="mt-10 pt-6 border-t border-paper/10 flex flex-col sm:flex-row justify-between gap-2 text-xs text-paper/35">
          <p>© {new Date().getFullYear()} CoasConnect.</p>
          <p>Go · React · React Native</p>
        </div>
      </div>
    </footer>
  )
}
