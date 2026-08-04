export default function Navbar() {
  return (
    <header className="sticky top-0 z-40 backdrop-blur-md bg-ink-950/85 border-b border-white/10">
      <nav className="mx-auto max-w-6xl px-6 h-16 flex items-center justify-between">
        <a href="#top" className="flex items-center gap-2.5 group">
          <span className="grid place-items-center w-8 h-8 rounded-lg bg-aqua-500/15 ring-1 ring-aqua-500/40 transition-transform duration-300 group-hover:rotate-12">
            <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="var(--color-aqua-400)" strokeWidth="2" strokeLinecap="round">
              <circle cx="6" cy="7" r="2.4" />
              <circle cx="17" cy="6" r="1.8" />
              <circle cx="15" cy="17" r="2.1" />
              <circle cx="6" cy="18" r="1.6" />
              <path d="M8 8 C 11 10, 13 9, 15 8 M7 9 C 8 12, 11 15, 13 16" />
            </svg>
          </span>
          <span className="font-display font-700 tracking-tight text-sand-100 text-lg">
            Coas<span className="text-aqua-400">Connect</span>
          </span>
        </a>

        <div className="hidden md:flex items-center gap-8 text-sm font-medium text-sand-100/70">
          <a href="#status" className="hover:text-aqua-400 transition-colors">Status API</a>
          <a href="#kasus" className="hover:text-aqua-400 transition-colors">Kasus</a>
          <a href="#fitur" className="hover:text-aqua-400 transition-colors">Cara kerja</a>
          <a
            href="#kasus"
            className="px-4 py-2 rounded-full bg-aqua-500 text-ink-950 font-semibold hover:bg-aqua-400 transition-colors"
          >
            Mulai
          </a>
        </div>
      </nav>
    </header>
  )
}
