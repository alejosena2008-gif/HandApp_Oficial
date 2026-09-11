<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>HandApp · Panel del Moderador</title>
<meta name="description" content="Panel de moderación de HandApp: gestiona usuarios, revisa reportes y mantén una comunidad segura para aprender lengua de señas." />
<meta name="theme-color" content="#080b14" />
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet" />
<style>
/* ============================================
   HandApp · Panel del Moderador
   Tema: Aurora Glass — vidrio esmerilado sobre un
   fondo de luces en movimiento. (Misma identidad
   visual que los paneles de estudiante e instructor)
   ============================================ */
:root {
    /* -- superficies -- */
    --bg-deep: #07060f;
    --bg-deep-2: #0d1126;
    --surface: rgba(255, 255, 255, 0.05);
    --surface-2: rgba(255, 255, 255, 0.085);
    --surface-hover: rgba(255, 255, 255, 0.12);
    --border-glass: rgba(255, 255, 255, 0.12);
    --border-glass-strong: rgba(255, 255, 255, 0.24);

    /* -- texto -- */
    --text-primary: #f4f5fb;
    --text-secondary: #b1b6d6;
    --text-muted: #6e759a;
    --text-inverse: #0a0c16;

    /* -- acentos -- */
    --violet: #8b7cff;
    --violet-deep: #5c4cdb;
    --cyan: #34e8d4;
    --coral: #ff7096;
    --amber: #ffb648;
    --teal: #14b8a6;

    --grad-violet: linear-gradient(135deg, var(--violet), var(--cyan));
    --grad-warm: linear-gradient(135deg, var(--amber), var(--coral));
    --grad-cool: linear-gradient(135deg, var(--teal), var(--cyan));
    --grad-rose: linear-gradient(135deg, var(--coral), var(--violet));

    --shadow-glass: 0 8px 32px rgba(0, 0, 0, 0.45), inset 0 1px 0 rgba(255, 255, 255, 0.07);
    --shadow-glow-violet: 0 0 36px rgba(139, 124, 255, 0.35);
    --shadow-glow-cyan: 0 0 36px rgba(52, 232, 212, 0.3);

    --font-display: 'Sora', 'Inter', sans-serif;
    --font-body: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    --font-mono: 'JetBrains Mono', 'Courier New', monospace;

    --font-size-xs: 0.75rem;
    --font-size-sm: 0.875rem;
    --font-size-base: 1rem;
    --font-size-lg: 1.125rem;
    --font-size-xl: 1.25rem;
    --font-size-2xl: 1.5rem;
    --font-size-3xl: 1.875rem;
    --font-size-4xl: 2.5rem;

    --spacing-1: 0.25rem;
    --spacing-2: 0.5rem;
    --spacing-3: 0.75rem;
    --spacing-4: 1rem;
    --spacing-5: 1.25rem;
    --spacing-6: 1.5rem;
    --spacing-8: 2rem;
    --spacing-10: 2.5rem;
    --spacing-12: 3rem;
    --spacing-16: 4rem;

    --radius-sm: 0.3rem;
    --radius-md: 0.6rem;
    --radius-lg: 0.9rem;
    --radius-xl: 1.2rem;
    --radius-2xl: 1.6rem;
    --radius-full: 9999px;

    --transition-fast: 150ms ease;
    --transition-base: 220ms cubic-bezier(.2,.8,.2,1);
    --transition-slow: 360ms cubic-bezier(.16,1,.3,1);

    --sidebar-width: 272px;
}

*, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

html { scroll-behavior: smooth; font-size: 16px; background-color: var(--bg-deep); }

body {
    font-family: var(--font-body);
    font-size: var(--font-size-base);
    line-height: 1.6;
    color: var(--text-primary);
    background-color: var(--bg-deep);
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    min-height: 100vh;
    position: relative;
}

h1, h2, h3 { font-family: var(--font-display); letter-spacing: -0.01em; }

::selection { background: var(--violet); color: var(--text-inverse); }

:focus-visible { outline: 2px solid var(--cyan); outline-offset: 3px; border-radius: var(--radius-sm); }

::-webkit-scrollbar { width: 10px; height: 10px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.14); border-radius: var(--radius-full); border: 2px solid transparent; background-clip: padding-box; }
::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.24); background-clip: padding-box; }

a { color: inherit; }

/* ============================================
   AURORA — fondo vivo
   ============================================ */
.aurora {
    position: fixed;
    inset: 0;
    z-index: -2;
    overflow: hidden;
    background: radial-gradient(ellipse at 20% 0%, #131736 0%, var(--bg-deep) 55%);
}
.aurora-blob {
    position: absolute;
    border-radius: 50%;
    filter: blur(90px);
    opacity: 0.55;
    mix-blend-mode: screen;
    will-change: transform;
}
.blob-1 { width: 46vw; height: 46vw; max-width: 620px; max-height: 620px; top: -12%; left: -8%; background: radial-gradient(circle, var(--violet), transparent 70%); animation: drift-a 26s ease-in-out infinite alternate; }
.blob-2 { width: 38vw; height: 38vw; max-width: 520px; max-height: 520px; top: 10%; right: -10%; background: radial-gradient(circle, var(--cyan), transparent 70%); animation: drift-b 32s ease-in-out infinite alternate; }
.blob-3 { width: 34vw; height: 34vw; max-width: 460px; max-height: 460px; bottom: -10%; left: 18%; background: radial-gradient(circle, var(--coral), transparent 70%); animation: drift-c 22s ease-in-out infinite alternate; }
.blob-4 { width: 28vw; height: 28vw; max-width: 380px; max-height: 380px; bottom: 6%; right: 12%; background: radial-gradient(circle, var(--amber), transparent 70%); opacity: 0.35; animation: drift-d 28s ease-in-out infinite alternate; }

@keyframes drift-a { 0% { transform: translate(0,0) scale(1); } 100% { transform: translate(6%, 8%) scale(1.12); } }
@keyframes drift-b { 0% { transform: translate(0,0) scale(1); } 100% { transform: translate(-8%, 6%) scale(0.92); } }
@keyframes drift-c { 0% { transform: translate(0,0) scale(1); } 100% { transform: translate(5%, -8%) scale(1.08); } }
@keyframes drift-d { 0% { transform: translate(0,0) scale(1); } 100% { transform: translate(-6%, -6%) scale(1.15); } }

/* ============================================
   VIDRIO — utilidades reutilizables
   ============================================ */
.glass {
    background: var(--surface);
    backdrop-filter: blur(22px) saturate(160%);
    -webkit-backdrop-filter: blur(22px) saturate(160%);
    border: 1px solid var(--border-glass);
    box-shadow: var(--shadow-glass);
}

.spot { position: relative; overflow: hidden; }
.spot::before {
    content: "";
    position: absolute;
    inset: 0;
    background: radial-gradient(280px circle at var(--mx, 50%) var(--my, 50%), rgba(139, 124, 255, 0.22), transparent 65%);
    opacity: 0;
    transition: opacity var(--transition-base);
    pointer-events: none;
}
.spot:hover::before, .spot:focus-within::before { opacity: 1; }

[data-tilt] { transition: transform var(--transition-base); transform-style: preserve-3d; will-change: transform; }

/* ============================================
   LAYOUT
   ============================================ */
.app-shell { display: flex; min-height: 100vh; }

/* ---------- Sidebar ---------- */
.sidebar {
    position: fixed;
    top: var(--spacing-3);
    left: var(--spacing-3);
    bottom: var(--spacing-3);
    width: calc(var(--sidebar-width) - var(--spacing-3));
    border-radius: var(--radius-2xl);
    display: flex;
    flex-direction: column;
    padding: var(--spacing-6) var(--spacing-5);
    z-index: 200;
    transform: translateX(-120%);
    transition: transform var(--transition-slow);
    overflow-y: auto;
}
.sidebar.active { transform: translateX(0); }
@media (min-width: 1024px) { .sidebar { transform: translateX(0); } }

.logo { display: flex; align-items: center; gap: var(--spacing-3); text-decoration: none; color: var(--text-primary); margin-bottom: var(--spacing-8); }
.logo-mark {
    width: 42px; height: 42px;
    display: flex; align-items: center; justify-content: center;
    background: var(--grad-violet);
    border-radius: var(--radius-lg);
    color: var(--text-inverse);
    box-shadow: var(--shadow-glow-violet);
}
.logo-mark svg { width: 22px; height: 22px; }
.logo-text { font-family: var(--font-display); font-size: var(--font-size-xl); font-weight: 800; letter-spacing: -0.02em; display: block; }
.logo-accent { color: var(--cyan); }
.logo-role { display: block; font-size: 0.68rem; font-weight: 600; color: var(--text-muted); letter-spacing: 0.04em; margin-top: 2px; text-transform: uppercase; }

.sidebar-nav { display: flex; flex-direction: column; gap: var(--spacing-1); position: relative; }

.nav-pill {
    position: absolute;
    left: 0; top: 0;
    width: 100%;
    border-radius: var(--radius-lg);
    background: linear-gradient(120deg, rgba(139,124,255,0.22), rgba(52,232,212,0.14));
    border: 1px solid rgba(139, 124, 255, 0.35);
    box-shadow: 0 0 24px rgba(139, 124, 255, 0.18);
    transition: top var(--transition-slow), height var(--transition-slow);
    pointer-events: none;
    z-index: 0;
}

.sidebar-label {
    font-size: var(--font-size-xs); font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em;
    color: var(--text-muted); padding: 0 var(--spacing-3);
    margin: var(--spacing-6) 0 var(--spacing-2);
    position: relative; z-index: 1;
}

.nav-link {
    position: relative; z-index: 1;
    display: flex; align-items: center; gap: var(--spacing-3);
    padding: var(--spacing-3); font-size: var(--font-size-sm); font-weight: 500;
    color: var(--text-secondary); text-decoration: none;
    border-radius: var(--radius-lg); border: none; background: transparent;
    cursor: pointer; width: 100%; text-align: left;
    transition: color var(--transition-fast);
}
.nav-link svg { width: 19px; height: 19px; flex-shrink: 0; }
.nav-link:hover { color: var(--text-primary); }
.nav-link.active { color: var(--text-primary); font-weight: 600; }

.nav-badge {
    margin-left: auto; font-size: 0.7rem; font-weight: 700; font-family: var(--font-mono);
    padding: 2px 8px; border-radius: var(--radius-full);
    background: var(--grad-cool); color: var(--text-inverse);
}
.nav-badge.alert { background: var(--grad-warm); }

.sidebar-footer { margin-top: auto; padding-top: var(--spacing-6); }

.streak-card {
    display: flex; align-items: center; gap: var(--spacing-3);
    padding: var(--spacing-4); border-radius: var(--radius-xl);
    background: linear-gradient(135deg, rgba(52,232,212,0.14), rgba(139,124,255,0.1));
    border: 1px solid rgba(52, 232, 212, 0.25);
}
.streak-flame {
    width: 42px; height: 42px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    background: var(--grad-cool); border-radius: var(--radius-lg);
    box-shadow: 0 0 22px rgba(52, 232, 212, 0.4);
}
.streak-flame svg { width: 22px; height: 22px; color: #fff; }
.streak-num { font-family: var(--font-mono); font-size: var(--font-size-lg); font-weight: 700; line-height: 1; }
.streak-text { font-size: var(--font-size-xs); color: var(--text-muted); margin-top: 2px; }

/* ---------- Main ---------- */
.main { flex: 1; margin-left: 0; min-width: 0; }
@media (min-width: 1024px) { .main { margin-left: var(--sidebar-width); } }

.topbar {
    position: sticky; top: 0; z-index: 150;
    display: flex; align-items: center; gap: var(--spacing-4);
    padding: var(--spacing-4) var(--spacing-6);
    background: rgba(10, 12, 22, 0.55);
    backdrop-filter: blur(18px) saturate(140%);
    -webkit-backdrop-filter: blur(18px) saturate(140%);
    border-bottom: 1px solid var(--border-glass);
}

.menu-toggle {
    display: flex; align-items: center; justify-content: center;
    width: 40px; height: 40px; border: none; background: transparent;
    border-radius: var(--radius-lg); color: var(--text-primary); cursor: pointer;
    transition: background var(--transition-fast);
}
.menu-toggle:hover { background: var(--surface-hover); }
.menu-toggle svg { width: 22px; height: 22px; }
@media (min-width: 1024px) { .menu-toggle { display: none; } }

.search-box {
    display: flex; align-items: center; gap: var(--spacing-2);
    flex: 1; max-width: 420px; padding: var(--spacing-2) var(--spacing-4);
    background: var(--surface); border: 1px solid var(--border-glass);
    border-radius: var(--radius-full); color: var(--text-muted);
    transition: border-color var(--transition-base), box-shadow var(--transition-base), background var(--transition-base);
}
.search-box:focus-within { border-color: rgba(139, 124, 255, 0.55); box-shadow: 0 0 0 4px rgba(139, 124, 255, 0.12); background: var(--surface-2); }
.search-box svg { width: 18px; height: 18px; flex-shrink: 0; }
.search-box input { flex: 1; border: none; background: transparent; outline: none; font-size: var(--font-size-sm); color: var(--text-primary); font-family: inherit; }
.search-box input::placeholder { color: var(--text-muted); }

.topbar-actions { display: flex; align-items: center; gap: var(--spacing-2); margin-left: auto; }

.icon-btn {
    position: relative; width: 40px; height: 40px;
    display: flex; align-items: center; justify-content: center;
    border: none; background: transparent; border-radius: var(--radius-lg);
    color: var(--text-secondary); cursor: pointer;
    transition: color var(--transition-fast), background var(--transition-fast);
}
.icon-btn:hover { color: var(--text-primary); background: var(--surface-hover); }
.icon-btn svg { width: 20px; height: 20px; }
.icon-btn .dot { position: absolute; top: 9px; right: 10px; width: 8px; height: 8px; background: var(--coral); border-radius: 50%; }
.icon-btn .dot::after {
    content: ""; position: absolute; inset: -4px; border-radius: 50%;
    border: 2px solid var(--coral); opacity: 0.7; animation: ping 2.2s ease-out infinite;
}
@keyframes ping { 0% { transform: scale(0.6); opacity: 0.7; } 100% { transform: scale(2); opacity: 0; } }

.notif-wrap { position: relative; }
.notif-panel {
    position: absolute; top: calc(100% + 12px); right: 0; width: 300px;
    padding: var(--spacing-4); border-radius: var(--radius-xl);
    opacity: 0; visibility: hidden; transform: translateY(-8px) scale(0.98);
    transition: opacity var(--transition-base), transform var(--transition-base), visibility var(--transition-base);
    z-index: 400;
}
.notif-panel.open { opacity: 1; visibility: visible; transform: translateY(0) scale(1); }
.notif-title { font-size: var(--font-size-sm); font-weight: 700; margin-bottom: var(--spacing-2); }
.notif-item { display: flex; gap: 10px; padding: var(--spacing-3) 0; border-top: 1px solid var(--border-glass); }
.notif-item:first-of-type { border-top: none; padding-top: 0; }
.notif-dot { width: 8px; height: 8px; border-radius: 50%; margin-top: 6px; flex-shrink: 0; }
.notif-dot.violet { background: var(--violet); box-shadow: 0 0 8px var(--violet); }
.notif-dot.cyan { background: var(--cyan); box-shadow: 0 0 8px var(--cyan); }
.notif-dot.amber { background: var(--amber); box-shadow: 0 0 8px var(--amber); }
.notif-text { font-size: var(--font-size-xs); color: var(--text-secondary); line-height: 1.5; }
.notif-text strong { color: var(--text-primary); }
.notif-time { font-size: 0.7rem; color: var(--text-muted); }

.avatar {
    display: flex; align-items: center; gap: var(--spacing-3);
    padding: var(--spacing-1); padding-right: var(--spacing-3);
    border-radius: var(--radius-full); cursor: pointer;
    transition: border-color var(--transition-fast), background var(--transition-fast);
}
.avatar:hover { background: var(--surface-hover); }
.avatar-img {
    width: 36px; height: 36px; border-radius: 50%;
    background: var(--grad-violet); display: flex; align-items: center; justify-content: center;
    color: var(--text-inverse); font-weight: 700; font-size: var(--font-size-sm); font-family: var(--font-display);
}
.avatar-name { font-size: var(--font-size-sm); font-weight: 600; line-height: 1.2; }
.avatar-role { font-size: 0.7rem; color: var(--text-muted); }
@media (max-width: 640px) { .avatar-text { display: none; } }

.content { padding: var(--spacing-8) var(--spacing-6); max-width: 1320px; margin: 0 auto; }
@media (min-width: 768px) { .content { padding: var(--spacing-10) var(--spacing-8); } }

/* ---------- Welcome ---------- */
.welcome { display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: var(--spacing-4); margin-bottom: var(--spacing-8); }
.welcome-title { font-size: var(--font-size-3xl); font-weight: 800; line-height: 1.1; text-wrap: balance; }
@media (min-width: 768px) { .welcome-title { font-size: var(--font-size-4xl); } }
#greetWord { background: var(--grad-violet); -webkit-background-clip: text; background-clip: text; color: transparent; }
.welcome-subtitle { color: var(--text-secondary); margin-top: var(--spacing-2); }
.welcome-subtitle strong { color: var(--text-primary); }
.welcome-actions { display: flex; gap: var(--spacing-3); flex-wrap: wrap; }

.btn {
    display: inline-flex; align-items: center; justify-content: center; gap: var(--spacing-2);
    padding: var(--spacing-3) var(--spacing-5); font-size: var(--font-size-sm); font-weight: 600;
    font-family: inherit; text-decoration: none; border-radius: var(--radius-lg); border: none;
    cursor: pointer; transition: all var(--transition-base); white-space: nowrap;
}
.btn svg { width: 18px; height: 18px; transition: transform var(--transition-fast); }
.btn-accent { background: var(--grad-violet); color: var(--text-inverse); box-shadow: 0 8px 24px rgba(139, 124, 255, 0.3); }
.btn-accent:hover { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(139, 124, 255, 0.45); }
.btn-outline { background: var(--surface); color: var(--text-primary); border: 1px solid var(--border-glass); }
.btn-outline:hover { background: var(--surface-hover); border-color: var(--border-glass-strong); }
.btn-danger { background: var(--grad-rose); color: var(--text-inverse); box-shadow: 0 8px 24px rgba(255, 112, 150, 0.28); }
.btn-danger:hover { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(255, 112, 150, 0.4); }
.btn-sm { padding: var(--spacing-2) var(--spacing-4); font-size: var(--font-size-xs); }

/* ---------- Stats grid ---------- */
.stats-grid { display: grid; grid-template-columns: 1fr; gap: var(--spacing-4); margin-bottom: var(--spacing-8); }
@media (min-width: 640px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
@media (min-width: 1024px) { .stats-grid { grid-template-columns: repeat(4, 1fr); } }

.stat-card { border-radius: var(--radius-2xl); padding: var(--spacing-6); transition: transform var(--transition-base), box-shadow var(--transition-base); }
.stat-card:hover { box-shadow: var(--shadow-glass), var(--shadow-glow-violet); }

.ring-icon { position: relative; width: 72px; height: 72px; margin-bottom: var(--spacing-4); }
.ring-svg { position: absolute; inset: 0; width: 72px; height: 72px; transform: rotate(-90deg); }
.ring-track { fill: none; stroke: rgba(255, 255, 255, 0.08); stroke-width: 4; }
.ring-fill { fill: none; stroke-width: 4; stroke-linecap: round; }

.stat-icon {
    position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
    width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;
    border-radius: var(--radius-lg); color: var(--text-inverse);
}
.stat-icon svg { width: 20px; height: 20px; }
.stat-icon.indigo { background: var(--grad-violet); }
.stat-icon.teal { background: var(--grad-cool); }
.stat-icon.amber { background: var(--grad-warm); }
.stat-icon.rose { background: var(--grad-rose); }

.stat-value { font-family: var(--font-mono); font-size: var(--font-size-3xl); font-weight: 700; line-height: 1; }
.stat-name { font-size: var(--font-size-sm); color: var(--text-secondary); margin-top: var(--spacing-2); }
.stat-trend { font-size: var(--font-size-xs); font-weight: 600; color: var(--cyan); margin-top: var(--spacing-2); display: inline-flex; align-items: center; gap: 4px; }
.stat-trend.warn { color: var(--amber); }
.stat-trend.down { color: var(--coral); }
.stat-trend svg { width: 14px; height: 14px; }

/* ---------- Columns ---------- */
.grid-2 { display: grid; grid-template-columns: 1fr; gap: var(--spacing-6); }
@media (min-width: 1024px) { .grid-2 { grid-template-columns: 1.7fr 1fr; } }

.panel-card { border-radius: var(--radius-2xl); padding: var(--spacing-6); }

.panel-head { display: flex; align-items: center; justify-content: space-between; gap: var(--spacing-4); margin-bottom: var(--spacing-6); flex-wrap: wrap; }
.panel-title { font-size: var(--font-size-xl); font-weight: 700; }
.panel-link { font-size: var(--font-size-sm); font-weight: 600; color: var(--cyan); text-decoration: none; display: inline-flex; align-items: center; gap: var(--spacing-1); transition: gap var(--transition-fast); background:none; border:none; cursor:pointer; font-family:inherit; }
.panel-link:hover { gap: var(--spacing-2); }
.panel-link svg { width: 16px; height: 16px; }

/* ---------- Filtros / toolbar de tabla ---------- */
.table-toolbar { display: flex; flex-wrap: wrap; align-items: center; gap: var(--spacing-3); margin-bottom: var(--spacing-5); }
.table-search {
    display: flex; align-items: center; gap: var(--spacing-2);
    flex: 1; min-width: 200px; padding: var(--spacing-2) var(--spacing-4);
    background: var(--surface); border: 1px solid var(--border-glass);
    border-radius: var(--radius-full); color: var(--text-muted);
    transition: border-color var(--transition-base), box-shadow var(--transition-base);
}
.table-search:focus-within { border-color: rgba(139, 124, 255, 0.55); box-shadow: 0 0 0 4px rgba(139, 124, 255, 0.12); }
.table-search svg { width: 17px; height: 17px; flex-shrink: 0; }
.table-search input { flex: 1; border: none; background: transparent; outline: none; font-size: var(--font-size-sm); color: var(--text-primary); font-family: inherit; }
.table-search input::placeholder { color: var(--text-muted); }

.filter-chips { display: flex; flex-wrap: wrap; gap: var(--spacing-2); }
.chip {
    padding: var(--spacing-2) var(--spacing-4); font-size: var(--font-size-xs); font-weight: 600;
    border-radius: var(--radius-full); border: 1px solid var(--border-glass);
    background: var(--surface); color: var(--text-secondary); cursor: pointer;
    font-family: inherit; transition: all var(--transition-fast);
}
.chip:hover { color: var(--text-primary); border-color: var(--border-glass-strong); }
.chip.active { background: var(--grad-violet); color: var(--text-inverse); border-color: transparent; box-shadow: 0 0 18px rgba(139, 124, 255, 0.3); }

/* ---------- Tabla de usuarios ---------- */
.table-wrap { overflow-x: auto; border-radius: var(--radius-xl); border: 1px solid var(--border-glass); }
.user-table { width: 100%; border-collapse: collapse; min-width: 720px; }
.user-table thead th {
    text-align: left; font-size: var(--font-size-xs); font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;
    color: var(--text-muted); padding: var(--spacing-4); background: rgba(255, 255, 255, 0.03);
    border-bottom: 1px solid var(--border-glass); white-space: nowrap;
}
.user-table tbody tr { transition: background var(--transition-fast); border-bottom: 1px solid var(--border-glass); }
.user-table tbody tr:last-child { border-bottom: none; }
.user-table tbody tr:hover { background: var(--surface-2); }
.user-table td { padding: var(--spacing-4); font-size: var(--font-size-sm); vertical-align: middle; }

.user-cell { display: flex; align-items: center; gap: var(--spacing-3); min-width: 200px; }
.user-pic {
    width: 40px; height: 40px; flex-shrink: 0; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: var(--text-inverse); font-weight: 700; font-size: var(--font-size-sm); font-family: var(--font-display);
}
.pic-violet { background: var(--grad-violet); }
.pic-teal { background: var(--grad-cool); }
.pic-amber { background: var(--grad-warm); }
.pic-rose { background: var(--grad-rose); }
.user-name { font-weight: 600; color: var(--text-primary); }
.user-mail { font-size: var(--font-size-xs); color: var(--text-muted); }

.badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 10px; font-size: var(--font-size-xs); font-weight: 600;
    border-radius: var(--radius-full); white-space: nowrap;
}
.badge .badge-dot { width: 7px; height: 7px; border-radius: 50%; }
.role-admin { background: rgba(139, 124, 255, 0.16); color: var(--violet); }
.role-instructor { background: rgba(52, 232, 212, 0.14); color: var(--cyan); }
.role-student { background: rgba(255, 255, 255, 0.07); color: var(--text-secondary); }
.role-mod { background: rgba(255, 182, 72, 0.16); color: var(--amber); }

.status-active { color: var(--cyan); }
.status-active .badge-dot { background: var(--cyan); box-shadow: 0 0 8px var(--cyan); }
.status-suspended { color: var(--coral); }
.status-suspended .badge-dot { background: var(--coral); box-shadow: 0 0 8px var(--coral); }
.status-pending { color: var(--amber); }
.status-pending .badge-dot { background: var(--amber); box-shadow: 0 0 8px var(--amber); }

.cell-mono { font-family: var(--font-mono); font-size: var(--font-size-xs); color: var(--text-secondary); white-space: nowrap; }

/* ---------- Acciones de fila ---------- */
.row-actions { position: relative; display: flex; justify-content: flex-end; }
.row-toggle {
    width: 34px; height: 34px; border: none; background: transparent; cursor: pointer;
    border-radius: var(--radius-md); color: var(--text-secondary);
    display: flex; align-items: center; justify-content: center;
    transition: background var(--transition-fast), color var(--transition-fast);
}
.row-toggle:hover { background: var(--surface-hover); color: var(--text-primary); }
.row-toggle svg { width: 18px; height: 18px; }
.row-menu {
    position: absolute; top: calc(100% + 6px); right: 0; width: 196px; z-index: 50;
    padding: var(--spacing-2); border-radius: var(--radius-lg);
    opacity: 0; visibility: hidden; transform: translateY(-6px) scale(0.97);
    transition: opacity var(--transition-base), transform var(--transition-base), visibility var(--transition-base);
}
.row-menu.open { opacity: 1; visibility: visible; transform: translateY(0) scale(1); }
.row-menu button {
    display: flex; align-items: center; gap: var(--spacing-3); width: 100%; text-align: left;
    padding: var(--spacing-2) var(--spacing-3); font-size: var(--font-size-sm); font-family: inherit;
    color: var(--text-secondary); background: transparent; border: none; cursor: pointer;
    border-radius: var(--radius-md); transition: background var(--transition-fast), color var(--transition-fast);
}
.row-menu button:hover { background: var(--surface-hover); color: var(--text-primary); }
.row-menu button.danger:hover { color: var(--coral); }
.row-menu button svg { width: 16px; height: 16px; flex-shrink: 0; }
.row-menu .menu-sep { height: 1px; background: var(--border-glass); margin: var(--spacing-1) 0; }

.no-results { text-align: center; padding: var(--spacing-8) var(--spacing-4); color: var(--text-muted); font-size: var(--font-size-sm); display: none; }
.no-results.show { display: block; }

.table-footer { display: flex; align-items: center; justify-content: space-between; gap: var(--spacing-4); margin-top: var(--spacing-5); flex-wrap: wrap; }
.table-count { font-size: var(--font-size-xs); color: var(--text-muted); font-family: var(--font-mono); }

/* ---------- Cola de reportes ---------- */
.report-list { display: flex; flex-direction: column; gap: var(--spacing-3); }
.report-item {
    position: relative; overflow: hidden;
    display: flex; align-items: flex-start; gap: var(--spacing-4);
    padding: var(--spacing-4); border: 1px solid var(--border-glass); border-radius: var(--radius-xl);
    background: rgba(255, 255, 255, 0.02);
    transition: background var(--transition-base), border-color var(--transition-base);
}
.report-item::before { content: ""; position: absolute; left: 0; top: 0; bottom: 0; width: 3px; background: var(--grad-warm); }
.report-item.high::before { background: var(--grad-rose); }
.report-item.low::before { background: var(--grad-cool); }
.report-item:hover { background: var(--surface-2); border-color: var(--border-glass-strong); }
.report-icon {
    width: 42px; height: 42px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    border-radius: var(--radius-lg); background: rgba(255, 182, 72, 0.16); color: var(--amber);
}
.report-item.high .report-icon { background: rgba(255, 112, 150, 0.16); color: var(--coral); }
.report-item.low .report-icon { background: rgba(52, 232, 212, 0.14); color: var(--cyan); }
.report-icon svg { width: 21px; height: 21px; }
.report-body { flex: 1; min-width: 0; }
.report-reason { font-weight: 600; font-size: var(--font-size-sm); }
.report-meta { font-size: var(--font-size-xs); color: var(--text-muted); margin-top: 2px; }
.report-meta strong { color: var(--text-secondary); }
.report-tag { font-size: 0.66rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--amber); }
.report-item.high .report-tag { color: var(--coral); }
.report-item.low .report-tag { color: var(--cyan); }
.report-actions { display: flex; gap: var(--spacing-2); margin-top: var(--spacing-3); flex-wrap: wrap; }
.mini-btn {
    padding: 5px 12px; font-size: var(--font-size-xs); font-weight: 600; font-family: inherit;
    border-radius: var(--radius-full); border: 1px solid var(--border-glass);
    background: var(--surface); color: var(--text-secondary); cursor: pointer;
    transition: all var(--transition-fast);
}
.mini-btn:hover { color: var(--text-primary); border-color: var(--border-glass-strong); }
.mini-btn.solve { background: rgba(52, 232, 212, 0.14); color: var(--cyan); border-color: transparent; }
.mini-btn.ban { background: rgba(255, 112, 150, 0.14); color: var(--coral); border-color: transparent; }

/* ---------- Herramientas rápidas ---------- */
.tools-grid { display: grid; grid-template-columns: 1fr; gap: var(--spacing-4); margin-top: var(--spacing-6); }
@media (min-width: 640px) { .tools-grid { grid-template-columns: repeat(3, 1fr); } }
.tool-card {
    display: flex; align-items: center; gap: var(--spacing-4);
    padding: var(--spacing-5); border-radius: var(--radius-2xl);
    text-decoration: none; color: var(--text-primary);
    transition: box-shadow var(--transition-base);
    border: none; cursor: pointer; font-family: inherit; text-align: left; width: 100%;
}
.tool-card:hover { box-shadow: var(--shadow-glass), var(--shadow-glow-cyan); }
.tool-icon {
    width: 52px; height: 52px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    background: var(--grad-violet); border-radius: var(--radius-lg); color: var(--text-inverse);
}
.tool-icon.teal { background: var(--grad-cool); }
.tool-icon.rose { background: var(--grad-rose); }
.tool-icon svg { width: 26px; height: 26px; }
.tool-title { font-size: var(--font-size-base); font-weight: 700; }
.tool-desc { font-size: var(--font-size-xs); color: var(--text-secondary); }

.section-block { margin-top: var(--spacing-10); }

/* ---------- Modal ---------- */
.modal-backdrop {
    position: fixed; inset: 0; z-index: 500;
    display: flex; align-items: center; justify-content: center; padding: var(--spacing-4);
    background: rgba(4, 5, 12, 0.65); backdrop-filter: blur(6px);
    opacity: 0; visibility: hidden; transition: opacity var(--transition-base), visibility var(--transition-base);
}
.modal-backdrop.open { opacity: 1; visibility: visible; }
.modal {
    width: 100%; max-width: 460px; border-radius: var(--radius-2xl); padding: var(--spacing-8) var(--spacing-6) var(--spacing-6);
    transform: translateY(16px) scale(0.97); transition: transform var(--transition-slow);
}
.modal-backdrop.open .modal { transform: translateY(0) scale(1); }
.modal-icon {
    width: 56px; height: 56px; margin: 0 auto var(--spacing-4);
    display: flex; align-items: center; justify-content: center;
    border-radius: var(--radius-xl); background: var(--grad-rose); color: var(--text-inverse);
    box-shadow: 0 0 26px rgba(255, 112, 150, 0.4);
}
.modal-icon.violet { background: var(--grad-violet); box-shadow: var(--shadow-glow-violet); }
.modal-icon.teal { background: var(--grad-cool); box-shadow: var(--shadow-glow-cyan); }
.modal-icon svg { width: 26px; height: 26px; }
.modal-title { font-size: var(--font-size-xl); font-weight: 800; text-align: center; }
.modal-text { font-size: var(--font-size-sm); color: var(--text-secondary); text-align: center; margin-top: var(--spacing-2); line-height: 1.55; }
.modal-text strong { color: var(--text-primary); }
.modal-field { margin-top: var(--spacing-5); }
.modal-field label { display: block; font-size: var(--font-size-xs); font-weight: 600; color: var(--text-muted); margin-bottom: var(--spacing-2); text-transform: uppercase; letter-spacing: 0.05em; }
.modal-field select, .modal-field textarea {
    width: 100%; padding: var(--spacing-3) var(--spacing-4); font-family: inherit; font-size: var(--font-size-sm);
    color: var(--text-primary); background: var(--surface); border: 1px solid var(--border-glass);
    border-radius: var(--radius-lg); outline: none; transition: border-color var(--transition-fast);
}
.modal-field select option { background: var(--bg-deep-2); color: var(--text-primary); }
.modal-field select:focus, .modal-field textarea:focus { border-color: rgba(139, 124, 255, 0.55); }
.modal-field textarea { resize: vertical; min-height: 80px; }
.modal-actions { display: flex; gap: var(--spacing-3); margin-top: var(--spacing-6); }
.modal-actions .btn { flex: 1; }

/* ---------- Overlay menú móvil ---------- */
.overlay {
    position: fixed; inset: 0; background: rgba(4, 5, 12, 0.6); backdrop-filter: blur(4px);
    z-index: 190; opacity: 0; visibility: hidden; transition: opacity var(--transition-base), visibility var(--transition-base);
}
.overlay.active { opacity: 1; visibility: visible; }
@media (min-width: 1024px) { .overlay { display: none; } }

/* ---------- Toasts ---------- */
.toast-container { position: fixed; bottom: var(--spacing-6); right: var(--spacing-6); z-index: 600; display: flex; flex-direction: column; gap: var(--spacing-2); max-width: calc(100vw - 2 * var(--spacing-6)); }
.toast {
    display: flex; align-items: center; gap: var(--spacing-3);
    padding: var(--spacing-3) var(--spacing-5); border-radius: var(--radius-full);
    font-size: var(--font-size-sm); font-weight: 600;
    opacity: 0; transform: translateX(24px); transition: opacity var(--transition-slow), transform var(--transition-slow);
}
.toast.show { opacity: 1; transform: translateX(0); }
.toast.hide { opacity: 0; transform: translateX(24px); }
.toast-ic { font-size: 1.05rem; }

/* ---------- Animación de aparición ---------- */
[data-reveal] { opacity: 0; transform: translateY(24px); transition: opacity 0.7s ease, transform 0.7s ease; }
[data-reveal].revealed { opacity: 1; transform: translateY(0); }

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after { animation-duration: 0.01ms !important; animation-iteration-count: 1 !important; transition-duration: 0.01ms !important; scroll-behavior: auto !important; }
    [data-reveal] { opacity: 1; transform: none; }
}
</style>
</head>
<body>

<!-- Fondo animado -->
<div class="aurora" aria-hidden="true">
    <span class="aurora-blob blob-1"></span>
    <span class="aurora-blob blob-2"></span>
    <span class="aurora-blob blob-3"></span>
    <span class="aurora-blob blob-4"></span>
</div>

<!-- Degradados reutilizables para anillos SVG -->
<svg width="0" height="0" style="position:absolute" aria-hidden="true">
    <defs>
        <linearGradient id="grad-indigo" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#8b7cff" /><stop offset="100%" stop-color="#34e8d4" />
        </linearGradient>
        <linearGradient id="grad-teal" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#14b8a6" /><stop offset="100%" stop-color="#34e8d4" />
        </linearGradient>
        <linearGradient id="grad-amber" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#ffb648" /><stop offset="100%" stop-color="#ff7096" />
        </linearGradient>
        <linearGradient id="grad-rose" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#ff7096" /><stop offset="100%" stop-color="#8b7cff" />
        </linearGradient>
    </defs>
</svg>

<div class="overlay" id="overlay" aria-hidden="true"></div>

<div class="app-shell">
    <!-- ============ SIDEBAR ============ -->
    <aside class="sidebar glass" id="sidebar" aria-label="Navegación principal">
        <a href="#" class="logo">
            <span class="logo-mark" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 11V6a2 2 0 0 0-2-2v0a2 2 0 0 0-2 2v0"/><path d="M14 10V4a2 2 0 0 0-2-2v0a2 2 0 0 0-2 2v2"/><path d="M10 10.5V6a2 2 0 0 0-2-2v0a2 2 0 0 0-2 2v8"/><path d="M18 8a2 2 0 1 1 4 0v6a8 8 0 0 1-8 8h-2c-2.8 0-4.5-.86-5.99-2.34l-3.6-3.6a2 2 0 0 1 2.83-2.82L7 15"/></svg>
            </span>
            <span>
                <span class="logo-text">Hand<span class="logo-accent">App</span></span>
                <span class="logo-role">Panel del moderador</span>
            </span>
        </a>

        <nav class="sidebar-nav" id="sidebarNav">
            <span class="nav-pill" id="navPill" aria-hidden="true"></span>
            <button class="nav-link active">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>
                Panel
            </button>
            <button class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Usuarios
                <span class="nav-badge">2.4k</span>
            </button>
            <button class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                Reportes
                <span class="nav-badge alert">7</span>
            </button>
            <button class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                Contenido
                <span class="nav-badge alert">3</span>
            </button>
            <button class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                Mensajes
                <span class="nav-badge">5</span>
            </button>
            <button class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z"/><path d="M12 14a7 7 0 0 0-7 7h14a7 7 0 0 0-7-7Z"/></svg>
                Mi perfil
            </button>

            <p class="sidebar-label">Supervisión</p>
            <button class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                Estadísticas
            </button>
            <button class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Registro de acciones
            </button>
            <button class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                Ajustes
            </button>

            <p class="sidebar-label">Acceso</p>
            <a href="/panel-estudiante.html" class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                Panel de estudiante
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="streak-card">
                <span class="streak-flame" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
                </span>
                <div>
                    <div class="streak-num">98<span style="font-size:var(--font-size-sm);color:var(--text-muted)">%</span></div>
                    <div class="streak-text">Comunidad sana</div>
                </div>
            </div>
        </div>
    </aside>

    <!-- ============ MAIN ============ -->
    <div class="main">
        <!-- Topbar -->
        <header class="topbar">
            <button class="menu-toggle" id="menuToggle" aria-label="Abrir menú">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>

            <div class="search-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input id="globalSearch" type="search" placeholder="Buscar usuarios, reportes…" aria-label="Buscar" />
            </div>

            <div class="topbar-actions">
                <div class="notif-wrap">
                    <button class="icon-btn" id="notifBtn" aria-label="Notificaciones" aria-expanded="false">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                        <span class="dot" aria-hidden="true"></span>
                    </button>
                    <div class="notif-panel glass" id="notifPanel" role="region" aria-label="Notificaciones recientes">
                        <p class="notif-title">Notificaciones</p>
                        <div class="notif-item">
                            <span class="notif-dot coral" style="background:var(--coral)"></span>
                            <div><p class="notif-text"><strong>Nuevo reporte</strong> sobre un comentario en el foro de saludos.</p><span class="notif-time">hace 4 min</span></div>
                        </div>
                        <div class="notif-item">
                            <span class="notif-dot amber"></span>
                            <div><p class="notif-text"><strong>3 cuentas</strong> esperan verificación de instructor.</p><span class="notif-time">hace 25 min</span></div>
                        </div>
                        <div class="notif-item">
                            <span class="notif-dot cyan"></span>
                            <div><p class="notif-text">Se levantó la suspensión de <strong>Daniel Ruiz</strong>.</p><span class="notif-time">hace 1 h</span></div>
                        </div>
                    </div>
                </div>

                <button class="avatar" aria-label="Tu cuenta">
                    <span class="avatar-img" aria-hidden="true">CM</span>
                    <span class="avatar-text">
                        <span class="avatar-name">Carmen M.</span>
                        <span class="avatar-role">Moderadora</span>
                    </span>
                </button>
            </div>
        </header>

        <div class="content">
            <!-- Bienvenida -->
            <section class="welcome" data-reveal>
                <div>
                    <h1 class="welcome-title"><span id="greetWord">Buen día</span>, Carmen</h1>
                    <p class="welcome-subtitle">Tienes <strong>7 reportes</strong> pendientes y <strong>3 cuentas</strong> por verificar hoy.</p>
                </div>
                <div class="welcome-actions">
                    <button class="btn btn-outline" id="exportBtn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Exportar
                    </button>
                    <button class="btn btn-accent" id="inviteBtn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                        Invitar usuario
                    </button>
                </div>
            </section>

            <!-- Estadísticas -->
            <section class="stats-grid" aria-label="Resumen de la comunidad">
                <article class="stat-card glass spot" data-reveal data-tilt>
                    <div class="ring-icon">
                        <svg class="ring-svg" viewBox="0 0 72 72"><circle class="ring-track" cx="36" cy="36" r="32"/><circle class="ring-fill" cx="36" cy="36" r="32" stroke="url(#grad-indigo)" data-ring="88"/></svg>
                        <span class="stat-icon indigo"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
                    </div>
                    <div class="stat-value" data-count="2438">0</div>
                    <p class="stat-name">Usuarios totales</p>
                    <span class="stat-trend"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>+128 este mes</span>
                </article>

                <article class="stat-card glass spot" data-reveal data-tilt>
                    <div class="ring-icon">
                        <svg class="ring-svg" viewBox="0 0 72 72"><circle class="ring-track" cx="36" cy="36" r="32"/><circle class="ring-fill" cx="36" cy="36" r="32" stroke="url(#grad-teal)" data-ring="72"/></svg>
                        <span class="stat-icon teal"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg></span>
                    </div>
                    <div class="stat-value" data-count="1762">0</div>
                    <p class="stat-name">Activos esta semana</p>
                    <span class="stat-trend"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>72% del total</span>
                </article>

                <article class="stat-card glass spot" data-reveal data-tilt>
                    <div class="ring-icon">
                        <svg class="ring-svg" viewBox="0 0 72 72"><circle class="ring-track" cx="36" cy="36" r="32"/><circle class="ring-fill" cx="36" cy="36" r="32" stroke="url(#grad-amber)" data-ring="35"/></svg>
                        <span class="stat-icon amber"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></span>
                    </div>
                    <div class="stat-value" data-count="7">0</div>
                    <p class="stat-name">Reportes pendientes</p>
                    <span class="stat-trend warn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>2 urgentes</span>
                </article>

                <article class="stat-card glass spot" data-reveal data-tilt>
                    <div class="ring-icon">
                        <svg class="ring-svg" viewBox="0 0 72 72"><circle class="ring-track" cx="36" cy="36" r="32"/><circle class="ring-fill" cx="36" cy="36" r="32" stroke="url(#grad-rose)" data-ring="14"/></svg>
                        <span class="stat-icon rose"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg></span>
                    </div>
                    <div class="stat-value" data-count="12">0</div>
                    <p class="stat-name">Cuentas suspendidas</p>
                    <span class="stat-trend down"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/><polyline points="17 18 23 18 23 12"/></svg>-3 vs. semana previa</span>
                </article>
            </section>

            <!-- Dos columnas: tabla de usuarios + reportes -->
            <div class="grid-2">
                <!-- Gestión de usuarios -->
                <section class="panel-card glass" data-reveal aria-label="Gestión de usuarios">
                    <div class="panel-head">
                        <h2 class="panel-title">Gestión de usuarios</h2>
                        <button class="panel-link">Ver todos
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </button>
                    </div>

                    <div class="table-toolbar">
                        <div class="table-search">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            <input id="userSearch" type="search" placeholder="Buscar por nombre o correo…" aria-label="Buscar usuarios" />
                        </div>
                        <div class="filter-chips" id="filterChips" role="group" aria-label="Filtrar por estado">
                            <button class="chip active" data-filter="all">Todos</button>
                            <button class="chip" data-filter="active">Activos</button>
                            <button class="chip" data-filter="suspended">Suspendidos</button>
                            <button class="chip" data-filter="pending">Pendientes</button>
                        </div>
                    </div>

                    <div class="table-wrap">
                        <table class="user-table">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
                                    <th>Última actividad</th>
                                    <th><span class="sr-only" style="position:absolute;width:1px;height:1px;overflow:hidden">Acciones</span></th>
                                </tr>
                            </thead>
                            <tbody id="userTbody"><!-- filas generadas por JS --></tbody>
                        </table>
                        <div class="no-results" id="userNoResults">No se encontraron usuarios con esos criterios.</div>
                    </div>

                    <div class="table-footer">
                        <span class="table-count" id="userCount">—</span>
                        <div style="display:flex;gap:var(--spacing-2)">
                            <button class="btn btn-outline btn-sm">Anterior</button>
                            <button class="btn btn-outline btn-sm">Siguiente</button>
                        </div>
                    </div>
                </section>

                <!-- Cola de reportes -->
                <section class="panel-card glass" data-reveal aria-label="Cola de reportes">
                    <div class="panel-head">
                        <h2 class="panel-title">Reportes</h2>
                        <button class="panel-link">Historial
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </button>
                    </div>

                    <div class="report-list" id="reportList">
                        <article class="report-item high">
                            <span class="report-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></span>
                            <div class="report-body">
                                <span class="report-tag">Prioridad alta</span>
                                <p class="report-reason">Lenguaje ofensivo en el foro</p>
                                <p class="report-meta">Reportado contra <strong>usuario_anon_42</strong> · hace 4 min</p>
                                <div class="report-actions">
                                    <button class="mini-btn solve" data-action="resolve">Resolver</button>
                                    <button class="mini-btn ban" data-action="ban">Suspender</button>
                                    <button class="mini-btn" data-action="dismiss">Descartar</button>
                                </div>
                            </div>
                        </article>
                        <article class="report-item high">
                            <span class="report-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></span>
                            <div class="report-body">
                                <span class="report-tag">Prioridad alta</span>
                                <p class="report-reason">Spam de enlaces en lecciones</p>
                                <p class="report-meta">Reportado contra <strong>Mariana Ruiz</strong> · hace 22 min</p>
                                <div class="report-actions">
                                    <button class="mini-btn solve" data-action="resolve">Resolver</button>
                                    <button class="mini-btn ban" data-action="ban">Suspender</button>
                                    <button class="mini-btn" data-action="dismiss">Descartar</button>
                                </div>
                            </div>
                        </article>
                        <article class="report-item">
                            <span class="report-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></span>
                            <div class="report-body">
                                <span class="report-tag">Prioridad media</span>
                                <p class="report-reason">Video de seña incorrecto</p>
                                <p class="report-meta">Reportado en <strong>Lección: Familia</strong> · hace 1 h</p>
                                <div class="report-actions">
                                    <button class="mini-btn solve" data-action="resolve">Resolver</button>
                                    <button class="mini-btn" data-action="dismiss">Descartar</button>
                                </div>
                            </div>
                        </article>
                        <article class="report-item low">
                            <span class="report-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z"/><path d="M12 14a7 7 0 0 0-7 7h14a7 7 0 0 0-7-7Z"/></svg></span>
                            <div class="report-body">
                                <span class="report-tag">Prioridad baja</span>
                                <p class="report-reason">Foto de perfil inapropiada</p>
                                <p class="report-meta">Reportado contra <strong>kevin_99</strong> · hace 3 h</p>
                                <div class="report-actions">
                                    <button class="mini-btn solve" data-action="resolve">Resolver</button>
                                    <button class="mini-btn" data-action="dismiss">Descartar</button>
                                </div>
                            </div>
                        </article>
                    </div>
                </section>
            </div>

            <!-- Herramientas rápidas -->
            <section class="section-block" data-reveal aria-label="Herramientas de moderación">
                <div class="panel-head">
                    <h2 class="panel-title">Herramientas rápidas</h2>
                </div>
                <div class="tools-grid" id="toolsGrid">
                    <button class="tool-card glass spot" data-tilt>
                        <span class="tool-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg></span>
                        <div><div class="tool-title">Invitar usuario</div><div class="tool-desc">Crear cuenta o enviar invitación</div></div>
                    </button>
                    <button class="tool-card glass spot" data-tilt>
                        <span class="tool-icon teal" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></span>
                        <div><div class="tool-title">Registro de acciones</div><div class="tool-desc">Auditoría de moderación</div></div>
                    </button>
                    <button class="tool-card glass spot" data-tilt>
                        <span class="tool-icon rose" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></span>
                        <div><div class="tool-title">Contenido reportado</div><div class="tool-desc">Revisar publicaciones marcadas</div></div>
                    </button>
                </div>
            </section>
        </div>
    </div>
</div>

<!-- ============ MODAL DE ACCIONES ============ -->
<div class="modal-backdrop" id="modalBackdrop" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <div class="modal glass">
        <div class="modal-icon" id="modalIcon" aria-hidden="true"></div>
        <h3 class="modal-title" id="modalTitle">Confirmar acción</h3>
        <p class="modal-text" id="modalText">¿Deseas continuar?</p>
        <div id="modalExtra"></div>
        <div class="modal-actions">
            <button class="btn btn-outline" id="modalCancel">Cancelar</button>
            <button class="btn btn-danger" id="modalConfirm">Confirmar</button>
        </div>
    </div>
</div>

<!-- Toasts -->
<div class="toast-container" id="toastContainer" aria-live="polite" aria-atomic="true"></div>

<script>
(function () {
    "use strict";
    var prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    /* ====================================================
       DATOS DE EJEMPLO DE USUARIOS
       ==================================================== */
    var USERS = [
        { id: 1, name: "Sofía Londoño", email: "sofia.l@correo.com", role: "student", roleLabel: "Estudiante", roleClass: "role-student", status: "active", last: "Hace 5 min", pic: "pic-violet", initials: "SL" },
        { id: 2, name: "Diego Martínez", email: "diego.m@correo.com", role: "instructor", roleLabel: "Instructor", roleClass: "role-instructor", status: "active", last: "Hace 1 h", pic: "pic-teal", initials: "DM" },
        { id: 3, name: "Mariana Ruiz", email: "mariana.r@correo.com", role: "student", roleLabel: "Estudiante", roleClass: "role-student", status: "suspended", last: "Hace 2 días", pic: "pic-rose", initials: "MR" },
        { id: 4, name: "Kevin Estrada", email: "kevin_99@correo.com", role: "student", roleLabel: "Estudiante", roleClass: "role-student", status: "pending", last: "Nunca", pic: "pic-amber", initials: "KE" },
        { id: 5, name: "Valentina Cruz", email: "vale.cruz@correo.com", role: "moderator", roleLabel: "Moderadora", roleClass: "role-mod", status: "active", last: "Hace 12 min", pic: "pic-violet", initials: "VC" },
        { id: 6, name: "Andrés Quintero", email: "andresq@correo.com", role: "instructor", roleLabel: "Instructor", roleClass: "role-instructor", status: "active", last: "Hace 3 h", pic: "pic-teal", initials: "AQ" },
        { id: 7, name: "usuario_anon_42", email: "anon42@correo.com", role: "student", roleLabel: "Estudiante", roleClass: "role-student", status: "suspended", last: "Hace 6 h", pic: "pic-rose", initials: "U4" },
        { id: 8, name: "Daniela Pinto", email: "dani.pinto@correo.com", role: "student", roleLabel: "Estudiante", roleClass: "role-student", status: "active", last: "Hace 30 min", pic: "pic-amber", initials: "DP" },
        { id: 9, name: "Camilo Vargas", email: "camilo.v@correo.com", role: "student", roleLabel: "Estudiante", roleClass: "role-student", status: "pending", last: "Nunca", pic: "pic-violet", initials: "CV" }
    ];

    var STATUS_LABEL = { active: "Activo", suspended: "Suspendido", pending: "Pendiente" };

    var currentFilter = "all";
    var currentQuery = "";
    var tbody = document.getElementById("userTbody");
    var userNoResults = document.getElementById("userNoResults");
    var userCount = document.getElementById("userCount");

    function svgDots() {
        return '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="12" cy="5" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="12" cy="19" r="2"/></svg>';
    }

    function buildRow(u) {
        var suspendLabel = u.status === "suspended" ? "Reactivar cuenta" : "Suspender cuenta";
        var suspendAction = u.status === "suspended" ? "activate" : "suspend";
        var verifyItem = u.status === "pending"
            ? '<button data-act="verify"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>Verificar cuenta</button>'
            : '';
        return '' +
        '<tr data-id="' + u.id + '" data-status="' + u.status + '" data-name="' + u.name.toLowerCase() + '" data-email="' + u.email.toLowerCase() + '">' +
            '<td><div class="user-cell">' +
                '<span class="user-pic ' + u.pic + '" aria-hidden="true">' + u.initials + '</span>' +
                '<div><div class="user-name">' + u.name + '</div><div class="user-mail">' + u.email + '</div></div>' +
            '</div></td>' +
            '<td><span class="badge ' + u.roleClass + '">' + u.roleLabel + '</span></td>' +
            '<td><span class="badge status-' + u.status + '"><span class="badge-dot"></span>' + STATUS_LABEL[u.status] + '</span></td>' +
            '<td class="cell-mono">' + u.last + '</td>' +
            '<td>' +
                '<div class="row-actions">' +
                    '<button class="row-toggle" aria-label="Acciones para ' + u.name + '" aria-haspopup="true" aria-expanded="false">' + svgDots() + '</button>' +
                    '<div class="row-menu glass">' +
                        '<button data-act="view"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>Ver perfil</button>' +
                        verifyItem +
                        '<button data-act="role"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 11l-3 3-1.5-1.5"/></svg>Cambiar rol</button>' +
                        '<button data-act="' + suspendAction + '"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>' + suspendLabel + '</button>' +
                        '<div class="menu-sep"></div>' +
                        '<button class="danger" data-act="delete"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>Eliminar usuario</button>' +
                    '</div>' +
                '</div>' +
            '</td>' +
        '</tr>';
    }

    function renderTable() {
        var html = "";
        var visible = 0;
        USERS.forEach(function (u) {
            var matchFilter = currentFilter === "all" || u.status === currentFilter;
            var matchQuery = !currentQuery || u.name.toLowerCase().indexOf(currentQuery) !== -1 || u.email.toLowerCase().indexOf(currentQuery) !== -1;
            if (matchFilter && matchQuery) { html += buildRow(u); visible++; }
        });
        tbody.innerHTML = html;
        userNoResults.classList.toggle("show", visible === 0);
        userCount.textContent = "Mostrando " + visible + " de " + USERS.length + " usuarios";
        wireRowMenus();
    }

    /* ====================================================
       MENÚS DE ACCIÓN POR FILA
       ==================================================== */
    function closeAllRowMenus() {
        document.querySelectorAll(".row-menu.open").forEach(function (m) {
            m.classList.remove("open");
            var t = m.previousElementSibling;
            if (t) t.setAttribute("aria-expanded", "false");
        });
    }

    function wireRowMenus() {
        document.querySelectorAll(".row-toggle").forEach(function (toggle) {
            toggle.addEventListener("click", function (e) {
                e.stopPropagation();
                var menu = toggle.nextElementSibling;
                var isOpen = menu.classList.contains("open");
                closeAllRowMenus();
                if (!isOpen) { menu.classList.add("open"); toggle.setAttribute("aria-expanded", "true"); }
            });
        });
        document.querySelectorAll(".row-menu button").forEach(function (btn) {
            btn.addEventListener("click", function (e) {
                e.stopPropagation();
                var row = btn.closest("tr");
                var id = parseInt(row.getAttribute("data-id"), 10);
                var user = USERS.find(function (u) { return u.id === id; });
                closeAllRowMenus();
                handleAction(btn.getAttribute("data-act"), user);
            });
        });
    }

    document.addEventListener("click", closeAllRowMenus);

    /* ====================================================
       ACCIONES DE USUARIO (con modal de confirmación)
       ==================================================== */
    var modalBackdrop = document.getElementById("modalBackdrop");
    var modalIcon = document.getElementById("modalIcon");
    var modalTitle = document.getElementById("modalTitle");
    var modalText = document.getElementById("modalText");
    var modalExtra = document.getElementById("modalExtra");
    var modalConfirm = document.getElementById("modalConfirm");
    var modalCancel = document.getElementById("modalCancel");
    var pendingConfirm = null;

    var ICONS = {
        warn: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>',
        trash: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>',
        role: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>',
        check: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>'
    };

    function openModal(opts) {
        modalIcon.className = "modal-icon " + (opts.iconClass || "");
        modalIcon.innerHTML = opts.icon || ICONS.warn;
        modalTitle.textContent = opts.title;
        modalText.innerHTML = opts.text;
        modalExtra.innerHTML = opts.extra || "";
        modalConfirm.textContent = opts.confirmLabel || "Confirmar";
        modalConfirm.className = "btn " + (opts.confirmClass || "btn-danger");
        pendingConfirm = opts.onConfirm;
        modalBackdrop.classList.add("open");
    }
    function closeModal() { modalBackdrop.classList.remove("open"); pendingConfirm = null; }

    modalConfirm.addEventListener("click", function () {
        if (pendingConfirm) pendingConfirm();
        closeModal();
    });
    modalCancel.addEventListener("click", closeModal);
    modalBackdrop.addEventListener("click", function (e) { if (e.target === modalBackdrop) closeModal(); });
    document.addEventListener("keydown", function (e) { if (e.key === "Escape" && modalBackdrop.classList.contains("open")) closeModal(); });

    function handleAction(act, user) {
        if (act === "view") {
            showToast("Abriendo el perfil de " + user.name + "…", "check");
            return;
        }
        if (act === "role") {
            openModal({
                iconClass: "violet", icon: ICONS.role,
                title: "Cambiar rol",
                text: "Selecciona el nuevo rol para <strong>" + user.name + "</strong>.",
                extra: '<div class="modal-field"><label for="roleSelect">Rol</label>' +
                    '<select id="roleSelect">' +
                        '<option value="student"' + (user.role === "student" ? " selected" : "") + '>Estudiante</option>' +
                        '<option value="instructor"' + (user.role === "instructor" ? " selected" : "") + '>Instructor</option>' +
                        '<option value="moderator"' + (user.role === "moderator" ? " selected" : "") + '>Moderador</option>' +
                    '</select></div>',
                confirmLabel: "Guardar rol", confirmClass: "btn-accent",
                onConfirm: function () {
                    var sel = document.getElementById("roleSelect");
                    var map = {
                        student: { label: "Estudiante", cls: "role-student", role: "student" },
                        instructor: { label: "Instructor", cls: "role-instructor", role: "instructor" },
                        moderator: { label: "Moderador", cls: "role-mod", role: "moderator" }
                    };
                    var m = map[sel.value];
                    user.role = m.role; user.roleLabel = m.label; user.roleClass = m.cls;
                    renderTable();
                    showToast(user.name + " ahora es " + m.label + ".", "check");
                }
            });
            return;
        }
        if (act === "suspend") {
            openModal({
                iconClass: "", icon: ICONS.warn,
                title: "Suspender cuenta",
                text: "<strong>" + user.name + "</strong> no podrá iniciar sesión ni publicar hasta que reactives su cuenta.",
                extra: '<div class="modal-field"><label for="banReason">Motivo (opcional)</label><textarea id="banReason" placeholder="Ej: incumplimiento de las normas de la comunidad"></textarea></div>',
                confirmLabel: "Suspender", confirmClass: "btn-danger",
                onConfirm: function () {
                    user.status = "suspended";
                    renderTable();
                    showToast(user.name + " fue suspendido.", "check");
                }
            });
            return;
        }
        if (act === "activate") {
            openModal({
                iconClass: "teal", icon: ICONS.check,
                title: "Reactivar cuenta",
                text: "Se restaurará el acceso completo de <strong>" + user.name + "</strong>.",
                confirmLabel: "Reactivar", confirmClass: "btn-accent",
                onConfirm: function () {
                    user.status = "active"; user.last = "Hace un momento";
                    renderTable();
                    showToast(user.name + " fue reactivado.", "check");
                }
            });
            return;
        }
        if (act === "verify") {
            openModal({
                iconClass: "teal", icon: ICONS.check,
                title: "Verificar cuenta",
                text: "Confirma la cuenta de <strong>" + user.name + "</strong> para darle acceso a la plataforma.",
                confirmLabel: "Verificar", confirmClass: "btn-accent",
                onConfirm: function () {
                    user.status = "active"; user.last = "Hace un momento";
                    renderTable();
                    showToast(user.name + " fue verificado.", "check");
                }
            });
            return;
        }
        if (act === "delete") {
            openModal({
                iconClass: "", icon: ICONS.trash,
                title: "Eliminar usuario",
                text: "Esta acción es permanente. Se borrará la cuenta y el progreso de <strong>" + user.name + "</strong>.",
                confirmLabel: "Eliminar", confirmClass: "btn-danger",
                onConfirm: function () {
                    var idx = USERS.findIndex(function (u) { return u.id === user.id; });
                    if (idx !== -1) USERS.splice(idx, 1);
                    renderTable();
                    showToast(user.name + " fue eliminado.", "check");
                }
            });
            return;
        }
    }

    /* ====================================================
       FILTROS Y BÚSQUEDA DE LA TABLA
       ==================================================== */
    document.getElementById("filterChips").addEventListener("click", function (e) {
        var chip = e.target.closest(".chip");
        if (!chip) return;
        this.querySelectorAll(".chip").forEach(function (c) { c.classList.remove("active"); });
        chip.classList.add("active");
        currentFilter = chip.getAttribute("data-filter");
        renderTable();
    });

    var userSearch = document.getElementById("userSearch");
    userSearch.addEventListener("input", function () {
        currentQuery = this.value.trim().toLowerCase();
        renderTable();
    });

    /* ---- Búsqueda global del topbar (reusa la tabla) ---- */
    document.getElementById("globalSearch").addEventListener("input", function () {
        currentQuery = this.value.trim().toLowerCase();
        userSearch.value = this.value;
        renderTable();
    });

    /* ====================================================
       REPORTES
       ==================================================== */
    document.getElementById("reportList").addEventListener("click", function (e) {
        var btn = e.target.closest(".mini-btn");
        if (!btn) return;
        var item = btn.closest(".report-item");
        var reason = item.querySelector(".report-reason").textContent;
        var act = btn.getAttribute("data-action");
        if (act === "ban") {
            showToast("Usuario suspendido por: " + reason, "check");
        } else if (act === "resolve") {
            showToast("Reporte resuelto: " + reason, "star");
        } else {
            showToast("Reporte descartado.", "check");
        }
        item.style.transition = "opacity .3s ease, transform .3s ease, max-height .4s ease";
        item.style.opacity = "0";
        item.style.transform = "translateX(16px)";
        setTimeout(function () { item.remove(); }, 320);
    });

    /* ====================================================
       NAV PILL (indicador deslizante)
       ==================================================== */
    var navLinks = document.querySelectorAll(".sidebar-nav .nav-link");
    var navPill = document.getElementById("navPill");
    function movePill(link) {
        if (!link) { navPill.style.opacity = "0"; return; }
        navPill.style.opacity = "1";
        navPill.style.top = link.offsetTop + "px";
        navPill.style.height = link.offsetHeight + "px";
    }
    navLinks.forEach(function (link) {
        link.addEventListener("click", function () {
            navLinks.forEach(function (l) { l.classList.remove("active"); });
            link.classList.add("active");
            movePill(link);
            if (window.innerWidth < 1024) closeSidebar();
        });
    });
    var initialActive = document.querySelector(".sidebar-nav .nav-link.active");
    window.addEventListener("load", function () { movePill(initialActive); });
    window.addEventListener("resize", function () { movePill(document.querySelector(".sidebar-nav .nav-link.active")); });

    /* ====================================================
       SIDEBAR MÓVIL
       ==================================================== */
    var sidebar = document.getElementById("sidebar");
    var overlay = document.getElementById("overlay");
    var menuToggle = document.getElementById("menuToggle");
    function openSidebar() { sidebar.classList.add("active"); overlay.classList.add("active"); }
    function closeSidebar() { sidebar.classList.remove("active"); overlay.classList.remove("active"); }
    menuToggle.addEventListener("click", openSidebar);
    overlay.addEventListener("click", closeSidebar);

    /* ====================================================
       NOTIFICACIONES
       ==================================================== */
    var notifBtn = document.getElementById("notifBtn");
    var notifPanel = document.getElementById("notifPanel");
    notifBtn.addEventListener("click", function (e) {
        e.stopPropagation();
        var open = notifPanel.classList.toggle("open");
        notifBtn.setAttribute("aria-expanded", open ? "true" : "false");
    });
    document.addEventListener("click", function (e) {
        if (!notifPanel.contains(e.target) && e.target !== notifBtn && !notifBtn.contains(e.target)) {
            notifPanel.classList.remove("open");
            notifBtn.setAttribute("aria-expanded", "false");
        }
    });
    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") {
            closeSidebar();
            notifPanel.classList.remove("open");
            notifBtn.setAttribute("aria-expanded", "false");
        }
    });

    /* ====================================================
       BOTONES DE CABECERA Y HERRAMIENTAS
       ==================================================== */
    document.getElementById("inviteBtn").addEventListener("click", function () {
        openModal({
            iconClass: "violet", icon: ICONS.role,
            title: "Invitar usuario",
            text: "Envía una invitación por correo para unirse a HandApp.",
            extra: '<div class="modal-field"><label for="inviteEmail">Correo</label><textarea id="inviteEmail" placeholder="nombre@correo.com" style="min-height:auto;height:46px"></textarea></div>',
            confirmLabel: "Enviar invitación", confirmClass: "btn-accent",
            onConfirm: function () { showToast("Invitación enviada.", "check"); }
        });
    });
    document.getElementById("exportBtn").addEventListener("click", function () {
        showToast("Exportando lista de usuarios…", "check");
    });
    document.querySelectorAll("#toolsGrid .tool-card").forEach(function (card) {
        card.addEventListener("click", function () {
            showToast(card.querySelector(".tool-title").textContent + "…", "check");
        });
    });

    /* ====================================================
       SPOTLIGHT + TILT
       ==================================================== */
    function wireSpot() {
        document.querySelectorAll(".spot").forEach(function (el) {
            if (el.dataset.spotWired) return;
            el.dataset.spotWired = "1";
            el.addEventListener("mousemove", function (e) {
                var r = el.getBoundingClientRect();
                el.style.setProperty("--mx", ((e.clientX - r.left) / r.width * 100) + "%");
                el.style.setProperty("--my", ((e.clientY - r.top) / r.height * 100) + "%");
            });
        });
    }
    wireSpot();

    if (!prefersReducedMotion) {
        document.querySelectorAll("[data-tilt]").forEach(function (el) {
            var max = 6;
            el.addEventListener("mousemove", function (e) {
                var r = el.getBoundingClientRect();
                var px = (e.clientX - r.left) / r.width - 0.5;
                var py = (e.clientY - r.top) / r.height - 0.5;
                el.style.transform = "perspective(700px) rotateY(" + (px * max) + "deg) rotateX(" + (-py * max) + "deg) translateY(-3px)";
            });
            el.addEventListener("mouseleave", function () { el.style.transform = ""; });
        });
    }

    /* ====================================================
       REVEAL
       ==================================================== */
    var revealEls = document.querySelectorAll("[data-reveal]");
    if ("IntersectionObserver" in window) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) { entry.target.classList.add("revealed"); io.unobserve(entry.target); }
            });
        }, { threshold: 0.1 });
        revealEls.forEach(function (el) { io.observe(el); });
    } else {
        revealEls.forEach(function (el) { el.classList.add("revealed"); });
    }

    /* ====================================================
       CONTADORES
       ==================================================== */
    function animateCount(el) {
        var target = parseInt(el.getAttribute("data-count"), 10);
        var duration = 1200, start = null;
        function step(ts) {
            if (!start) start = ts;
            var progress = Math.min((ts - start) / duration, 1);
            var eased = 1 - Math.pow(1 - progress, 3);
            el.textContent = Math.floor(eased * target).toLocaleString("es");
            if (progress < 1) requestAnimationFrame(step);
            else el.textContent = target.toLocaleString("es");
        }
        requestAnimationFrame(step);
    }
    var counters = document.querySelectorAll("[data-count]");
    if ("IntersectionObserver" in window) {
        var countObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) { animateCount(entry.target); countObserver.unobserve(entry.target); }
            });
        }, { threshold: 0.5 });
        counters.forEach(function (el) { countObserver.observe(el); });
    } else {
        counters.forEach(animateCount);
    }

    /* ====================================================
       ANILLOS SVG
       ==================================================== */
    function animateRing(circle) {
        var progress = parseFloat(circle.getAttribute("data-ring")) || 0;
        var length = circle.getTotalLength();
        circle.style.strokeDasharray = length;
        circle.style.strokeDashoffset = length;
        requestAnimationFrame(function () {
            circle.style.transition = "stroke-dashoffset 1.4s cubic-bezier(.16,1,.3,1)";
            circle.style.strokeDashoffset = length * (1 - progress / 100);
        });
    }
    var rings = document.querySelectorAll(".ring-fill");
    if ("IntersectionObserver" in window) {
        var ringObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) { animateRing(entry.target); ringObserver.unobserve(entry.target); }
            });
        }, { threshold: 0.5 });
        rings.forEach(function (el) { ringObserver.observe(el); });
    } else {
        rings.forEach(animateRing);
    }

    /* ====================================================
       TOASTS
       ==================================================== */
    var toastContainer = document.getElementById("toastContainer");
    function showToast(message, icon) {
        var el = document.createElement("div");
        el.className = "toast glass";
        var ic = document.createElement("span");
        ic.className = "toast-ic";
        ic.textContent = icon === "star" ? "🎉" : "✅";
        var txt = document.createElement("span");
        txt.textContent = message;
        el.appendChild(ic); el.appendChild(txt);
        toastContainer.appendChild(el);
        requestAnimationFrame(function () { el.classList.add("show"); });
        setTimeout(function () {
            el.classList.remove("show"); el.classList.add("hide");
            setTimeout(function () { el.remove(); }, 400);
        }, 3200);
    }

    /* ---- Saludo según la hora ---- */
    var h = new Date().getHours();
    document.getElementById("greetWord").textContent = h < 12 ? "Buen día" : h < 19 ? "Buenas tardes" : "Buenas noches";

    /* ---- Render inicial ---- */
    renderTable();
})();
</script>
</body>
</html>
