// Signature CoasConnect: sebuah "peta jaringan" — titik-titik komunitas
// pesisir yang terhubung oleh garis-garis energi yang mengalir.

const NODES = [
  { x: 60, y: 210, r: 7, c: 'var(--color-aqua-500)' },
  { x: 190, y: 130, r: 5, c: 'var(--color-aqua-400)' },
  { x: 330, y: 190, r: 8, c: 'var(--color-coral-500)' },
  { x: 470, y: 110, r: 5, c: 'var(--color-aqua-400)' },
  { x: 600, y: 170, r: 6, c: 'var(--color-coral-400)' },
  { x: 700, y: 90, r: 4, c: 'var(--color-aqua-400)' },
  { x: 130, y: 300, r: 5, c: 'var(--color-coral-400)' },
  { x: 330, y: 320, r: 6, c: 'var(--color-aqua-500)' },
  { x: 540, y: 300, r: 5, c: 'var(--color-coral-400)' },
  { x: 650, y: 250, r: 7, c: 'var(--color-aqua-500)' },
] as const

const LINKS: Array<[number, number]> = [
  [0, 1], [1, 2], [2, 3], [3, 4], [4, 5],
  [2, 6], [2, 7], [4, 8], [7, 8], [8, 9], [4, 9],
]

export default function NetworkCanvas() {
  return (
    <svg
      viewBox="0 0 760 400"
      className="w-full h-auto"
      role="img"
      aria-label="Jaringan komunitas pesisir CoasConnect"
    >
      <defs>
        <linearGradient id="linkGrad" x1="0" y1="0" x2="1" y2="0">
          <stop offset="0%" stopColor="var(--color-aqua-500)" stopOpacity="0.15" />
          <stop offset="100%" stopColor="var(--color-coral-500)" stopOpacity="0.25" />
        </linearGradient>
      </defs>

      {/* Garis koneksi */}
      {LINKS.map(([a, b], i) => (
        <line
          key={i}
          x1={NODES[a].x}
          y1={NODES[a].y}
          x2={NODES[b].x}
          y2={NODES[b].y}
          stroke="url(#linkGrad)"
          strokeWidth={1.6}
          strokeDasharray="5 9"
          className="animate-dash-flow"
          style={{ animationDelay: `${i * 0.35}s` }}
        />
      ))}

      {/* Node komunitas */}
      {NODES.map((n, i) => (
        <g key={i}>
          <circle
            cx={n.x}
            cy={n.y}
            r={n.r * 2.4}
            fill={n.c}
            opacity={0.14}
            className="animate-pulse-node"
            style={{ animationDelay: `${i * 0.22}s` }}
          />
          <circle cx={n.x} cy={n.y} r={n.r} fill={n.c} />
        </g>
      ))}

      {/* Gelombang dasar */}
      <path
        d="M0 370 C 120 350, 220 385, 340 368 S 580 352, 760 372"
        fill="none"
        stroke="var(--color-ocean-700)"
        strokeWidth={2}
        opacity={0.5}
      />
      <path
        d="M0 382 C 140 366, 260 396, 400 380 S 640 366, 760 384"
        fill="none"
        stroke="var(--color-aqua-500)"
        strokeWidth={1.4}
        opacity={0.35}
      />
    </svg>
  )
}
