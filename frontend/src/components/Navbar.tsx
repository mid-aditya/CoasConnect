import { Link } from 'react-router-dom'

export default function Navbar({ authed }: { authed?: boolean }) {
  return (
    <header className="sticky top-0 z-40 bg-paper/90 backdrop-blur-md border-b border-line">
      <nav className="mx-auto max-w-6xl px-6 h-16 flex items-center justify-between">
        <Link to="/" className="flex items-center gap-2.5">
          <span className="grid place-items-center w-8 h-8 rounded-md bg-pine text-paper">
            <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
              <path d="M3 12h4l2-5 3 10 2.5-8 1.5 3H21" />
            </svg>
          </span>
          <span className="flex flex-col leading-tight">
            <span className="font-display font-bold tracking-tight text-ink">
              Coas<span className="text-pine">Connect</span>
            </span>
            <span className="hidden md:block font-mono text-[10px] tracking-[0.12em] text-muted">
              — Platform Penjaringan Pasien untuk Dokter Koas
            </span>
          </span>
        </Link>

        <div className="hidden md:flex items-center gap-8 text-sm font-medium text-muted">
          <a href="#cara-kerja" className="hover:text-ink transition-colors">
            Cara kerja
          </a>
          <a href="#untuk-siapa" className="hover:text-ink transition-colors">
            Untuk siapa
          </a>
          <a href="#status" className="hover:text-ink transition-colors">
            Status
          </a>
          <Link
            to={authed ? '/app' : '/login'}
            className="px-4 py-2 rounded-md bg-ink text-paper font-semibold hover:bg-pine transition-colors"
          >
            {authed ? 'Dashboard' : 'Masuk'}
          </Link>
        </div>
      </nav>
    </header>
  )
}
