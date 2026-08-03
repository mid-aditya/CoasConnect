export default function Footer() {
  return (
    <footer className="bg-ink-950 text-sand-100">
      <div className="mx-auto max-w-6xl px-6 py-12">
        <div className="flex flex-col md:flex-row md:items-center justify-between gap-6">
          <div>
            <p className="font-display font-700 text-lg">
              Coas<span className="text-aqua-400">Connect</span>
            </p>
            <p className="mt-1 text-sm text-sand-100/50">
              Menghubungkan komunitas pesisir dalam satu jaringan.
            </p>
          </div>
          <div className="flex flex-wrap gap-x-8 gap-y-3 text-sm text-sand-100/60">
            <a href="#status" className="hover:text-aqua-400 transition-colors">Status</a>
            <a href="#anggota" className="hover:text-aqua-400 transition-colors">Anggota</a>
            <a href="#fitur" className="hover:text-aqua-400 transition-colors">Fitur</a>
          </div>
        </div>
        <div className="mt-10 pt-6 border-t border-white/10 flex flex-col sm:flex-row justify-between gap-2 text-xs text-sand-100/35">
          <p>© {new Date().getFullYear()} CoasConnect. Dibuat untuk komunitas pesisir.</p>
          <p>Go · React · React Native</p>
        </div>
      </div>
    </footer>
  )
}
