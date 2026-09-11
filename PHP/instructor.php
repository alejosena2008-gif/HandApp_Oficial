<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>HandApp · Panel del Instructor</title>
<meta name="description" content="Panel de instructor de HandApp: gestiona tus cursos de lengua de señas, da seguimiento a tus estudiantes y prepara tus clases en vivo." />
<meta name="theme-color" content="#080b14" />
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet" />
<style>
/* ============================================
   HandApp · Panel del Instructor
   Tema: Aurora Glass — vidrio esmerilado sobre un
   fondo de luces en movimiento, inspirado en el
   flujo y la expresividad de la lengua de señas.
   (Misma identidad visual que el panel del estudiante)
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
    background: linear-gradient(135deg, rgba(255,182,72,0.14), rgba(255,112,150,0.1));
    border: 1px solid rgba(255, 182, 72, 0.25);
}
.streak-card.next-class { background: linear-gradient(135deg, rgba(52,232,212,0.14), rgba(139,124,255,0.1)); border: 1px solid rgba(52, 232, 212, 0.25); }
.streak-flame {
    width: 42px; height: 42px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    background: var(--grad-warm); border-radius: var(--radius-lg);
    box-shadow: 0 0 22px rgba(255, 150, 80, 0.45);
}
.streak-flame.cool { background: var(--grad-cool); box-shadow: 0 0 22px rgba(52, 232, 212, 0.4); }
.streak-flame svg { width: 22px; height: 22px; color: #fff; transform-origin: 50% 90%; animation: flicker 2.4s ease-in-out infinite; }
.streak-flame.cool svg { animation: none; }
@keyframes flicker {
    0%, 100% { transform: scale(1) rotate(0deg); }
    25% { transform: scale(1.05, 0.96) rotate(-3deg); }
    50% { transform: scale(0.97, 1.04) rotate(2deg); }
    75% { transform: scale(1.03, 0.98) rotate(-1deg); }
}
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
 
.btn {
    display: inline-flex; align-items: center; justify-content: center; gap: var(--spacing-2);
    padding: var(--spacing-3) var(--spacing-5); font-size: var(--font-size-sm); font-weight: 600;
    font-family: inherit; text-decoration: none; border-radius: var(--radius-lg); border: none;
    cursor: pointer; transition: all var(--transition-base); white-space: nowrap;
}
.btn svg { width: 18px; height: 18px; transition: transform var(--transition-fast); }
.btn-accent { background: var(--grad-violet); color: var(--text-inverse); box-shadow: 0 8px 24px rgba(139, 124, 255, 0.3); }
.btn-accent:hover { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(139, 124, 255, 0.45); }
.btn-accent:hover svg { transform: translateX(3px); }
.btn-outline { background: var(--surface); color: var(--text-primary); border: 1px solid var(--border-glass); }
.btn-outline:hover { background: var(--surface-hover); border-color: var(--border-glass-strong); }
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
.stat-trend svg { width: 14px; height: 14px; }
 
/* ---------- Columns ---------- */
.grid-2 { display: grid; grid-template-columns: 1fr; gap: var(--spacing-6); }
@media (min-width: 1024px) { .grid-2 { grid-template-columns: 1.6fr 1fr; } }
 
.panel-card { border-radius: var(--radius-2xl); padding: var(--spacing-6); }
 
.panel-head { display: flex; align-items: center; justify-content: space-between; gap: var(--spacing-4); margin-bottom: var(--spacing-6); }
.panel-title { font-size: var(--font-size-xl); font-weight: 700; }
.panel-link { font-size: var(--font-size-sm); font-weight: 600; color: var(--cyan); text-decoration: none; display: inline-flex; align-items: center; gap: var(--spacing-1); transition: gap var(--transition-fast); background:none; border:none; cursor:pointer; font-family:inherit; }
.panel-link:hover { gap: var(--spacing-2); }
.panel-link svg { width: 16px; height: 16px; }
 
/* Próxima clase - destacado */
.continue-card {
    display: flex; flex-direction: column; gap: var(--spacing-5);
    padding: var(--spacing-6); border-radius: var(--radius-2xl); margin-bottom: var(--spacing-6);
    background: linear-gradient(135deg, rgba(139,124,255,0.16), rgba(52,232,212,0.07));
}
@media (min-width: 640px) { .continue-card { flex-direction: row; align-items: center; } }
 
.continue-illustration {
    width: 90px; height: 90px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    background: var(--grad-violet); border-radius: var(--radius-2xl);
    box-shadow: var(--shadow-glow-violet); position: relative; z-index: 1;
}
.continue-illustration svg { width: 44px; height: 44px; color: #fff; }
 
.continue-body { flex: 1; position: relative; z-index: 1; }
.continue-eyebrow { font-size: var(--font-size-xs); font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: var(--cyan); display: inline-flex; align-items: center; gap: 6px; }
.continue-eyebrow .live-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--coral); box-shadow: 0 0 8px var(--coral); animation: ping 1.8s ease-out infinite; }
.continue-title { font-size: var(--font-size-2xl); font-weight: 800; margin: var(--spacing-2) 0; }
.continue-meta { font-size: var(--font-size-sm); color: var(--text-secondary); margin-bottom: var(--spacing-4); }
 
.progress-track { height: 9px; background: rgba(255, 255, 255, 0.1); border-radius: var(--radius-full); overflow: hidden; position: relative; }
.progress-fill { display: block; height: 100%; background: var(--grad-violet); border-radius: var(--radius-full); transition: width 1.2s cubic-bezier(.16,1,.3,1); position: relative; overflow: hidden; }
.progress-fill::after {
    content: ""; position: absolute; inset: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.55), transparent);
    width: 40%; animation: shimmer 2.6s ease-in-out infinite;
}
@keyframes shimmer { 0% { transform: translateX(-120%); } 100% { transform: translateX(280%); } }
.continue-progress-label { display: flex; justify-content: space-between; font-size: var(--font-size-xs); color: var(--text-muted); margin-top: var(--spacing-2); font-family: var(--font-mono); }
.continue-actions { display: flex; flex-wrap: wrap; gap: var(--spacing-3); margin-top: var(--spacing-5); position: relative; z-index: 1; }
 
/* Lista de estudiantes / lecciones */
.lesson-list { display: flex; flex-direction: column; gap: var(--spacing-3); }
.lesson-item {
    position: relative; overflow: hidden;
    display: flex; align-items: center; gap: var(--spacing-4);
    padding: var(--spacing-4); border: 1px solid var(--border-glass); border-radius: var(--radius-xl);
    background: rgba(255, 255, 255, 0.02);
    transition: background var(--transition-base), border-color var(--transition-base), transform var(--transition-base);
    cursor: pointer;
}
.lesson-item::before { content: ""; position: absolute; left: 0; top: 0; bottom: 0; width: 3px; background: var(--border-glass); transition: background var(--transition-base); }
.lesson-item:hover { background: var(--surface-2); border-color: var(--border-glass-strong); transform: translateX(4px); }
.lesson-item.locked { opacity: 0.55; cursor: not-allowed; }
.lesson-item.locked:hover { transform: none; background: rgba(255, 255, 255, 0.02); }
 
.lesson-thumb {
    width: 48px; height: 48px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    border-radius: var(--radius-lg); background: var(--surface); color: var(--text-secondary);
    font-family: var(--font-display); font-weight: 700; font-size: var(--font-size-sm);
}
.lesson-thumb svg { width: 23px; height: 23px; }
.lesson-thumb.done { background: rgba(20, 184, 166, 0.16); color: var(--cyan); }
.lesson-thumb.current { background: rgba(139, 124, 255, 0.18); color: var(--violet); animation: pulseGlow 2.4s ease-in-out infinite; }
.lesson-thumb.alert { background: rgba(255, 112, 150, 0.16); color: var(--coral); }
.lesson-thumb.warn { background: rgba(255, 182, 72, 0.16); color: var(--amber); }
.lesson-item.done::before { background: var(--grad-cool); }
.lesson-item.alert::before { background: var(--grad-rose); }
.lesson-item.warn::before { background: var(--grad-warm); }
.lesson-item:has(.lesson-thumb.current)::before { background: var(--grad-violet); }
@keyframes pulseGlow {
    0%, 100% { box-shadow: 0 0 0 0 rgba(139, 124, 255, 0.45); }
    50% { box-shadow: 0 0 0 7px rgba(139, 124, 255, 0); }
}
 
.lesson-info { flex: 1; min-width: 0; }
.lesson-num { font-size: 0.7rem; font-weight: 700; color: var(--violet); font-family: var(--font-mono); letter-spacing: 0.05em; }
.lesson-num.ok { color: var(--cyan); }
.lesson-num.warn { color: var(--amber); }
.lesson-num.alert { color: var(--coral); }
.lesson-num.muted { color: var(--text-muted); }
.lesson-name { font-size: var(--font-size-base); font-weight: 600; }
.lesson-desc { font-size: var(--font-size-xs); color: var(--text-muted); }
 
.lesson-side { display: flex; flex-direction: column; align-items: flex-end; gap: var(--spacing-2); min-width: 76px; }
.lesson-mini-track { width: 64px; height: 5px; background: rgba(255,255,255,0.08); border-radius: var(--radius-full); overflow: hidden; }
.lesson-mini-fill { display: block; height: 100%; background: var(--grad-violet); border-radius: var(--radius-full); }
.lesson-pct { font-size: var(--font-size-xs); font-weight: 600; color: var(--text-secondary); font-family: var(--font-mono); }
.lock-ic { width: 18px; height: 18px; color: var(--text-muted); }
 
.no-results { text-align: center; padding: var(--spacing-8) var(--spacing-4); color: var(--text-muted); font-size: var(--font-size-sm); display: none; }
.no-results.show { display: block; }
 
/* Tareas / objetivos del día */
.goal-list { display: flex; flex-direction: column; gap: var(--spacing-4); }
.goal-item { display: flex; align-items: center; gap: var(--spacing-3); }
.goal-check {
    width: 24px; height: 24px; flex-shrink: 0; border-radius: 50%;
    border: 2px solid var(--border-glass-strong);
    display: flex; align-items: center; justify-content: center; cursor: pointer;
    transition: all var(--transition-base); position: relative;
}
.goal-check svg { width: 14px; height: 14px; color: var(--text-inverse); opacity: 0; transition: opacity var(--transition-fast); }
.goal-item.completed .goal-check { background: var(--grad-violet); border-color: transparent; box-shadow: 0 0 14px rgba(139, 124, 255, 0.5); }
.goal-item.completed .goal-check svg { opacity: 1; }
.goal-item.completed .goal-text { color: var(--text-muted); text-decoration: line-through; }
.goal-text { font-size: var(--font-size-sm); font-weight: 500; transition: color var(--transition-fast); }
.goal-xp { margin-left: auto; font-size: 0.7rem; font-family: var(--font-mono); color: var(--text-muted); flex-shrink: 0; }
 
.daily-progress { margin-top: var(--spacing-6); padding-top: var(--spacing-6); border-top: 1px solid var(--border-glass); }
.daily-row { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: var(--spacing-3); }
.daily-row span:first-child { font-size: var(--font-size-sm); color: var(--text-secondary); }
.daily-num { font-size: var(--font-size-sm); font-weight: 700; color: var(--cyan); font-family: var(--font-mono); }
.daily-track { height: 9px; background: rgba(255,255,255,0.08); border-radius: var(--radius-full); overflow: hidden; }
.daily-fill { display: block; height: 100%; background: var(--grad-cool); border-radius: var(--radius-full); transition: width 0.8s cubic-bezier(.16,1,.3,1); }
 
/* Reconocimientos */
.achv-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: var(--spacing-3); }
@media (min-width: 480px) { .achv-grid { grid-template-columns: repeat(3, 1fr); } }
@media (min-width: 1024px) { .achv-grid { grid-template-columns: repeat(2, 1fr); } }
.achv {
    position: relative; overflow: hidden;
    display: flex; flex-direction: column; align-items: center; text-align: center; gap: var(--spacing-2);
    padding: var(--spacing-4); border: 1px solid var(--border-glass); border-radius: var(--radius-xl);
    background: rgba(255, 255, 255, 0.02);
    transition: transform var(--transition-base), border-color var(--transition-base);
}
.achv:not(.locked):hover { transform: translateY(-3px); border-color: var(--border-glass-strong); }
.achv:not(.locked)::after {
    content: ""; position: absolute; inset: 0;
    background: linear-gradient(115deg, transparent 30%, rgba(255,255,255,0.28) 50%, transparent 70%);
    background-size: 220% 100%; background-position: 200% 0;
    transition: background-position 0.9s ease; pointer-events: none;
}
.achv:not(.locked):hover::after { background-position: -60% 0; }
.achv-medal { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: var(--text-inverse); }
.achv-medal svg { width: 23px; height: 23px; }
.medal-amber { background: var(--grad-warm); box-shadow: 0 0 18px rgba(255, 182, 72, 0.4); }
.medal-violet { background: var(--grad-violet); box-shadow: 0 0 18px rgba(139, 124, 255, 0.4); }
.medal-teal { background: var(--grad-cool); box-shadow: 0 0 18px rgba(52, 232, 212, 0.4); }
.achv.locked { opacity: 0.45; }
.achv.locked .achv-medal { background: rgba(255,255,255,0.08); color: var(--text-muted); box-shadow: none; }
.achv-name { font-size: var(--font-size-xs); font-weight: 600; }
 
/* Herramientas rápidas */
.games-grid { display: grid; grid-template-columns: 1fr; gap: var(--spacing-4); margin-top: var(--spacing-6); }
@media (min-width: 640px) { .games-grid { grid-template-columns: repeat(3, 1fr); } }
 
.game-card {
    display: flex; align-items: center; gap: var(--spacing-4);
    padding: var(--spacing-5); border-radius: var(--radius-2xl);
    text-decoration: none; color: var(--text-primary);
    transition: box-shadow var(--transition-base);
    border: none; cursor: pointer; font-family: inherit; text-align: left; width: 100%;
}
.game-card:hover { box-shadow: var(--shadow-glass), var(--shadow-glow-cyan); }
.game-icon {
    width: 52px; height: 52px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    background: var(--grad-violet); border-radius: var(--radius-lg); color: var(--text-inverse);
}
.game-icon svg { width: 26px; height: 26px; }
.game-title { font-size: var(--font-size-base); font-weight: 700; }
.game-desc { font-size: var(--font-size-xs); color: var(--text-secondary); }
 
.section-block { margin-top: var(--spacing-10); }
 
/* Overlay para menú móvil */
.overlay {
    position: fixed; inset: 0; background: rgba(4, 5, 12, 0.6); backdrop-filter: blur(4px);
    z-index: 190; opacity: 0; visibility: hidden; transition: opacity var(--transition-base), visibility var(--transition-base);
}
.overlay.active { opacity: 1; visibility: visible; }
@media (min-width: 1024px) { .overlay { display: none; } }
 
/* Toasts */
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
 
/* Confeti */
.confetti-canvas { position: fixed; inset: 0; z-index: 599; pointer-events: none; }
 
/* Texto flotante al completar tareas */
.xp-float {
    position: fixed; z-index: 601; font-family: var(--font-mono); font-weight: 700; font-size: 0.8rem;
    color: var(--cyan); text-shadow: 0 0 10px rgba(52, 232, 212, 0.6);
    pointer-events: none; opacity: 0; transform: translate(-50%, 0);
}
.xp-float.rise { opacity: 1; transform: translate(-50%, -28px); transition: transform 0.5s ease, opacity 0.3s ease; }
.xp-float.fade { opacity: 0; transform: translate(-50%, -58px); transition: transform 0.6s ease, opacity 0.6s ease; }
 
/* Animación de aparición */
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
 
<!-- Definiciones de degradados reutilizables para los anillos SVG -->
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
                <span class="logo-role">Panel del instructor</span>
            </span>
        </a>
 
        <nav class="sidebar-nav" id="sidebarNav">
            <span class="nav-pill" id="navPill" aria-hidden="true"></span>
            <button class="nav-link active">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>
                Panel
            </button>
            <button class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                Mis cursos
                <span class="nav-badge">6</span>
            </button>
            <button class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Estudiantes
            </button>
            <button class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/></svg>
                Evaluaciones
                <span class="nav-badge alert">8</span>
            </button>
            <button class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                Mensajes
                <span class="nav-badge">2</span>
            </button>
            <button class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z"/><path d="M12 14a7 7 0 0 0-7 7h14a7 7 0 0 0-7-7Z"/></svg>
                Mi perfil
            </button>
 
            <p class="sidebar-label">Comunidad</p>
            <button class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                Foro
            </button>
            <button class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                Reportes
            </button>
            <button class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                Ajustes
            </button>
        </nav>
 
        <div class="sidebar-footer">
            <div class="streak-card next-class">
                <span class="streak-flame cool" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 7l-7 5 7 5V7z"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
                </span>
                <div>
                    <div class="streak-num">Hoy · 4:00 PM</div>
                    <div class="streak-text">Tu próxima clase en vivo</div>
                </div>
            </div>
        </div>
    </aside>
 
    <!-- ============ MAIN ============ -->
    <div class="main">
        <header class="topbar">
            <button class="menu-toggle" id="menuToggle" aria-label="Abrir menú">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
 
            <div class="search-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="search" id="searchInput" placeholder="Buscar estudiantes o herramientas..." aria-label="Buscar" />
            </div>
 
            <div class="topbar-actions">
                <div class="notif-wrap">
                    <button class="icon-btn" id="notifBtn" aria-label="Notificaciones" aria-haspopup="true" aria-expanded="false">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                        <span class="dot"></span>
                    </button>
                    <div class="notif-panel glass" id="notifPanel" role="menu">
                        <p class="notif-title">Notificaciones</p>
                        <div class="notif-item">
                            <span class="notif-dot violet" aria-hidden="true"></span>
                            <div><p class="notif-text"><strong>Nueva entrega.</strong> Camila envió su evaluación del Módulo 4.</p><span class="notif-time">Hace 20 minutos</span></div>
                        </div>
                        <div class="notif-item">
                            <span class="notif-dot cyan" aria-hidden="true"></span>
                            <div><p class="notif-text"><strong>Sebastián</strong> te escribió con una duda sobre la Lección 6.</p><span class="notif-time">Hace 1 hora</span></div>
                        </div>
                        <div class="notif-item">
                            <span class="notif-dot amber" aria-hidden="true"></span>
                            <div><p class="notif-text">Tu <strong>clase en vivo</strong> comienza en 1 hora.</p><span class="notif-time">Hoy</span></div>
                        </div>
                    </div>
                </div>
                <button class="avatar" aria-label="Perfil de Andrés Martínez">
                    <span class="avatar-img">AM</span>
                    <span class="avatar-text">
                        <span class="avatar-name">Andrés Martínez</span><br>
                        <span class="avatar-role">Instructor · LSC</span>
                    </span>
                </button>
            </div>
        </header>
 
        <main class="content">
            <!-- Welcome -->
            <section class="welcome" data-reveal>
                <div>
                    <h1 class="welcome-title"><span id="greetWord">Hola</span>, Andrés <span aria-hidden="true">👋</span></h1>
                    <p class="welcome-subtitle">Tienes <strong>3 clases</strong> programadas esta semana · <span id="todayDate"></span></p>
                </div>
                <button class="btn btn-accent">
                    Crear nueva lección
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </button>
            </section>
 
            <!-- Stats -->
            <section class="stats-grid" aria-label="Resumen de tu actividad como instructor">
                <div class="stat-card glass spot" data-reveal data-tilt>
                    <div class="ring-icon">
                        <svg class="ring-svg" viewBox="0 0 72 72" aria-hidden="true">
                            <circle class="ring-track" cx="36" cy="36" r="30"></circle>
                            <circle class="ring-fill" cx="36" cy="36" r="30" stroke="url(#grad-indigo)" data-ring="82"></circle>
                        </svg>
                        <span class="stat-icon indigo" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
                    </div>
                    <div class="stat-value" data-count="142">0</div>
                    <div class="stat-name">Estudiantes activos</div>
                    <span class="stat-trend"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>+8 esta semana</span>
                </div>
                <div class="stat-card glass spot" data-reveal data-tilt>
                    <div class="ring-icon">
                        <svg class="ring-svg" viewBox="0 0 72 72" aria-hidden="true">
                            <circle class="ring-track" cx="36" cy="36" r="30"></circle>
                            <circle class="ring-fill" cx="36" cy="36" r="30" stroke="url(#grad-teal)" data-ring="60"></circle>
                        </svg>
                        <span class="stat-icon teal" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg></span>
                    </div>
                    <div class="stat-value" data-count="6">0</div>
                    <div class="stat-name">Cursos publicados</div>
                    <span class="stat-trend"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>2 en borrador</span>
                </div>
                <div class="stat-card glass spot" data-reveal data-tilt>
                    <div class="ring-icon">
                        <svg class="ring-svg" viewBox="0 0 72 72" aria-hidden="true">
                            <circle class="ring-track" cx="36" cy="36" r="30"></circle>
                            <circle class="ring-fill" cx="36" cy="36" r="30" stroke="url(#grad-amber)" data-ring="35"></circle>
                        </svg>
                        <span class="stat-icon amber" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/></svg></span>
                    </div>
                    <div class="stat-value" data-count="8">0</div>
                    <div class="stat-name">Evaluaciones pendientes</div>
                    <span class="stat-trend warn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>3 vencen hoy</span>
                </div>
                <div class="stat-card glass spot" data-reveal data-tilt>
                    <div class="ring-icon">
                        <svg class="ring-svg" viewBox="0 0 72 72" aria-hidden="true">
                            <circle class="ring-track" cx="36" cy="36" r="30"></circle>
                            <circle class="ring-fill" cx="36" cy="36" r="30" stroke="url(#grad-rose)" data-ring="78"></circle>
                        </svg>
                        <span class="stat-icon rose" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg></span>
                    </div>
                    <div class="stat-value" data-count="78" data-suffix="%">0</div>
                    <div class="stat-name">Tasa de finalización</div>
                    <span class="stat-trend"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>+5% este mes</span>
                </div>
            </section>
 
            <!-- Two columns -->
            <div class="grid-2">
                <!-- Left -->
                <div>
                    <!-- Próxima clase en vivo -->
                    <article class="continue-card glass spot" data-reveal>
                        <div class="continue-illustration" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 7l-7 5 7 5V7z"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
                        </div>
                        <div class="continue-body">
                            <p class="continue-eyebrow"><span class="live-dot" aria-hidden="true"></span>En vivo · Hoy 4:00 PM</p>
                            <h2 class="continue-title">Módulo 3: Conversaciones cotidianas</h2>
                            <p class="continue-meta">18 estudiantes inscritos · Sala virtual B</p>
                            <div class="progress-track"><span class="progress-fill" style="width:85%"></span></div>
                            <div class="continue-progress-label"><span>Preparación de la clase</span><span>85%</span></div>
                            <div class="continue-actions">
                                <button class="btn btn-accent">
                                    Iniciar clase
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                </button>
                                <button class="btn btn-outline">Ver lista de asistentes</button>
                            </div>
                        </div>
                    </article>
 
                    <!-- Estudiantes -->
                    <section class="panel-card glass" data-reveal>
                        <div class="panel-head">
                            <h2 class="panel-title">Estudiantes que necesitan atención</h2>
                            <a href="#" class="panel-link">Ver todos <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></a>
                        </div>
                        <div class="lesson-list" id="studentList">
                            <div class="lesson-item done">
                                <span class="lesson-thumb done" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                                <div class="lesson-info">
                                    <span class="lesson-num ok">MEJORANDO</span>
                                    <div class="lesson-name">Mateo Pérez</div>
                                    <div class="lesson-desc">Contactado ayer · Retomó su ritmo</div>
                                </div>
                                <div class="lesson-side">
                                    <div class="lesson-mini-track"><span class="lesson-mini-fill" style="width:70%"></span></div>
                                    <span class="lesson-pct">70%</span>
                                </div>
                            </div>
                            <div class="lesson-item alert">
                                <span class="lesson-thumb alert" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></span>
                                <div class="lesson-info">
                                    <span class="lesson-num alert">URGENTE</span>
                                    <div class="lesson-name">Sebastián Ortiz</div>
                                    <div class="lesson-desc">Sin avance hace 6 días · Módulo 2</div>
                                </div>
                                <div class="lesson-side">
                                    <div class="lesson-mini-track"><span class="lesson-mini-fill" style="width:24%"></span></div>
                                    <span class="lesson-pct">24%</span>
                                </div>
                            </div>
                            <div class="lesson-item warn">
                                <span class="lesson-thumb warn" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/></svg></span>
                                <div class="lesson-info">
                                    <span class="lesson-num warn">ATENCIÓN</span>
                                    <div class="lesson-name">Sofía Londoño</div>
                                    <div class="lesson-desc">Bajó su racha · Módulo 3</div>
                                </div>
                                <div class="lesson-side">
                                    <div class="lesson-mini-track"><span class="lesson-mini-fill" style="width:41%"></span></div>
                                    <span class="lesson-pct">41%</span>
                                </div>
                            </div>
                            <div class="lesson-item locked">
                                <span class="lesson-thumb" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></span>
                                <div class="lesson-info">
                                    <span class="lesson-num muted">NUEVA</span>
                                    <div class="lesson-name">Laura Gómez</div>
                                    <div class="lesson-desc">Aún no inicia ninguna lección</div>
                                </div>
                                <div class="lesson-side">
                                    <span class="lock-ic" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></span>
                                </div>
                            </div>
                        </div>
                        <p class="no-results" id="studentNoResults">No encontramos estudiantes que coincidan con tu búsqueda.</p>
                    </section>
                </div>
 
                <!-- Right -->
                <div>
                    <!-- Tareas de hoy -->
                    <section class="panel-card glass" data-reveal style="margin-bottom: var(--spacing-6);">
                        <div class="panel-head"><h2 class="panel-title">Tareas de hoy</h2></div>
                        <div class="goal-list" id="goalList">
                            <div class="goal-item completed">
                                <span class="goal-check" role="checkbox" aria-checked="true" tabindex="0"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                                <span class="goal-text">Calificar evaluaciones del Módulo 4</span>
                                <span class="goal-xp">5 entregas</span>
                            </div>
                            <div class="goal-item completed">
                                <span class="goal-check" role="checkbox" aria-checked="true" tabindex="0"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                                <span class="goal-text">Responder mensajes de estudiantes</span>
                                <span class="goal-xp">3 mensajes</span>
                            </div>
                            <div class="goal-item">
                                <span class="goal-check" role="checkbox" aria-checked="false" tabindex="0"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                                <span class="goal-text">Preparar materiales de la clase en vivo</span>
                                <span class="goal-xp">30 min</span>
                            </div>
                            <div class="goal-item">
                                <span class="goal-check" role="checkbox" aria-checked="false" tabindex="0"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                                <span class="goal-text">Publicar el anuncio semanal del curso</span>
                                <span class="goal-xp">Pendiente</span>
                            </div>
                        </div>
                        <div class="daily-progress">
                            <div class="daily-row">
                                <span>Progreso del día</span>
                                <span class="daily-num" id="dailyNum">2 / 4 completadas</span>
                            </div>
                            <div class="daily-track"><span class="daily-fill" id="dailyFill" style="width:50%"></span></div>
                        </div>
                    </section>
 
                    <!-- Reconocimientos -->
                    <section class="panel-card glass" data-reveal>
                        <div class="panel-head">
                            <h2 class="panel-title">Reconocimientos</h2>
                            <a href="#" class="panel-link">Ver todos <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></a>
                        </div>
                        <div class="achv-grid">
                            <div class="achv">
                                <span class="achv-medal medal-amber" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></span>
                                <span class="achv-name">Mentor 5 estrellas</span>
                            </div>
                            <div class="achv">
                                <span class="achv-medal medal-violet" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></span>
                                <span class="achv-name">100 estudiantes graduados</span>
                            </div>
                            <div class="achv">
                                <span class="achv-medal medal-teal" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg></span>
                                <span class="achv-name">Clase mejor calificada</span>
                            </div>
                            <div class="achv locked">
                                <span class="achv-medal" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
                                <span class="achv-name">Embajador del programa</span>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
 
            <!-- Herramientas rápidas -->
            <section class="section-block" data-reveal>
                <div class="panel-head">
                    <h2 class="panel-title">Herramientas rápidas</h2>
                    <a href="#" class="panel-link">Ver todas <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></a>
                </div>
                <div class="games-grid" id="toolsGrid">
                    <button type="button" class="game-card glass spot" data-tilt>
                        <span class="game-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg></span>
                        <div>
                            <div class="game-title">Crear nueva lección</div>
                            <div class="game-desc">Diseña contenido y añade nuevas señas</div>
                        </div>
                    </button>
                    <button type="button" class="game-card glass spot" data-tilt>
                        <span class="game-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 7l-7 5 7 5V7z"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg></span>
                        <div>
                            <div class="game-title">Programar clase en vivo</div>
                            <div class="game-desc">Agenda una sesión y notifica a tus estudiantes</div>
                        </div>
                    </button>
                    <button type="button" class="game-card glass spot" data-tilt>
                        <span class="game-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/></svg></span>
                        <div>
                            <div class="game-title">Crear evaluación</div>
                            <div class="game-desc">Arma un quiz o examen para tus estudiantes</div>
                        </div>
                    </button>
                </div>
                <p class="no-results" id="toolsNoResults">No encontramos herramientas que coincidan con tu búsqueda.</p>
            </section>
        </main>
    </div>
</div>
 
<div class="toast-container" id="toastContainer" aria-live="polite"></div>
<canvas class="confetti-canvas" id="confettiCanvas" aria-hidden="true"></canvas>
 
<script>
(function () {
    "use strict";
 
    var prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
 
    /* ---- Saludo y fecha dinámicos ---- */
    var greetEl = document.getElementById("greetWord");
    var dateEl = document.getElementById("todayDate");
    var hour = new Date().getHours();
    var greeting = "Buenos días";
    if (hour >= 12 && hour < 19) greeting = "Buenas tardes";
    else if (hour >= 19 || hour < 5) greeting = "Buenas noches";
    if (greetEl) greetEl.textContent = greeting;
    if (dateEl) {
        try {
            var fmt = new Intl.DateTimeFormat("es-CO", { weekday: "long", day: "numeric", month: "long" });
            var d = fmt.format(new Date());
            dateEl.textContent = d.charAt(0).toUpperCase() + d.slice(1);
        } catch (e) { /* noop */ }
    }
 
    /* ---- Menú móvil ---- */
    var sidebar = document.getElementById("sidebar");
    var overlay = document.getElementById("overlay");
    var menuToggle = document.getElementById("menuToggle");
 
    function openSidebar() { sidebar.classList.add("active"); overlay.classList.add("active"); }
    function closeSidebar() { sidebar.classList.remove("active"); overlay.classList.remove("active"); }
    menuToggle.addEventListener("click", openSidebar);
    overlay.addEventListener("click", closeSidebar);
 
    /* ---- Navegación activa + píldora deslizante ---- */
    var sidebarNav = document.getElementById("sidebarNav");
    var navPill = document.getElementById("navPill");
    var navLinks = document.querySelectorAll(".sidebar-nav .nav-link");
 
    function movePill(link) {
        if (!link || !navPill || !sidebarNav) return;
        var linkRect = link.getBoundingClientRect();
        var navRect = sidebarNav.getBoundingClientRect();
        navPill.style.top = (linkRect.top - navRect.top) + "px";
        navPill.style.height = linkRect.height + "px";
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
    window.addEventListener("resize", function () {
        var current = document.querySelector(".sidebar-nav .nav-link.active");
        movePill(current);
    });
 
    /* ---- Notificaciones ---- */
    var notifBtn = document.getElementById("notifBtn");
    var notifPanel = document.getElementById("notifPanel");
    notifBtn.addEventListener("click", function (e) {
        e.stopPropagation();
        var open = notifPanel.classList.toggle("open");
        notifBtn.setAttribute("aria-expanded", open ? "true" : "false");
    });
    document.addEventListener("click", function (e) {
        if (!notifPanel.contains(e.target) && e.target !== notifBtn) {
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
 
    /* ---- Búsqueda en vivo ---- */
    var searchInput = document.getElementById("searchInput");
    var studentItems = document.querySelectorAll("#studentList .lesson-item");
    var toolCards = document.querySelectorAll("#toolsGrid .game-card");
    var studentNoResults = document.getElementById("studentNoResults");
    var toolsNoResults = document.getElementById("toolsNoResults");
 
    searchInput.addEventListener("input", function () {
        var q = this.value.trim().toLowerCase();
        var studentMatches = 0;
        studentItems.forEach(function (item) {
            var name = item.querySelector(".lesson-name").textContent.toLowerCase();
            var match = name.indexOf(q) !== -1;
            item.style.display = match ? "" : "none";
            if (match) studentMatches++;
        });
        studentNoResults.classList.toggle("show", q.length > 0 && studentMatches === 0);
 
        var toolMatches = 0;
        toolCards.forEach(function (card) {
            var name = card.querySelector(".game-title").textContent.toLowerCase();
            var match = name.indexOf(q) !== -1;
            card.style.display = match ? "" : "none";
            if (match) toolMatches++;
        });
        toolsNoResults.classList.toggle("show", q.length > 0 && toolMatches === 0);
    });
 
    /* ---- Efecto de foco (spotlight) en tarjetas de vidrio ---- */
    document.querySelectorAll(".spot").forEach(function (el) {
        el.addEventListener("mousemove", function (e) {
            var r = el.getBoundingClientRect();
            el.style.setProperty("--mx", ((e.clientX - r.left) / r.width * 100) + "%");
            el.style.setProperty("--my", ((e.clientY - r.top) / r.height * 100) + "%");
        });
    });
 
    /* ---- Inclinación 3D sutil ---- */
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
 
    /* ---- Animación de aparición (reveal) ---- */
    var revealEls = document.querySelectorAll("[data-reveal]");
    if ("IntersectionObserver" in window) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add("revealed");
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        revealEls.forEach(function (el) { io.observe(el); });
    } else {
        revealEls.forEach(function (el) { el.classList.add("revealed"); });
    }
 
    /* ---- Contadores animados ---- */
    function animateCount(el) {
        var target = parseInt(el.getAttribute("data-count"), 10);
        var suffix = el.getAttribute("data-suffix") || "";
        var duration = 1200;
        var start = null;
        function step(ts) {
            if (!start) start = ts;
            var progress = Math.min((ts - start) / duration, 1);
            var eased = 1 - Math.pow(1 - progress, 3);
            el.textContent = Math.floor(eased * target).toLocaleString("es") + suffix;
            if (progress < 1) requestAnimationFrame(step);
            else el.textContent = target.toLocaleString("es") + suffix;
        }
        requestAnimationFrame(step);
    }
 
    var counters = document.querySelectorAll("[data-count]");
    if ("IntersectionObserver" in window) {
        var countObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    animateCount(entry.target);
                    countObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        counters.forEach(function (el) { countObserver.observe(el); });
    } else {
        counters.forEach(animateCount);
    }
 
    /* ---- Anillos de progreso SVG ---- */
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
                if (entry.isIntersecting) {
                    animateRing(entry.target);
                    ringObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        rings.forEach(function (el) { ringObserver.observe(el); });
    } else {
        rings.forEach(animateRing);
    }
 
    /* ---- Confeti ---- */
    var confettiCanvas = document.getElementById("confettiCanvas");
    var ctx = confettiCanvas.getContext("2d");
    function resizeCanvas() { confettiCanvas.width = window.innerWidth; confettiCanvas.height = window.innerHeight; }
    resizeCanvas();
    window.addEventListener("resize", resizeCanvas);
 
    function burstConfetti() {
        if (prefersReducedMotion) return;
        var colors = ["#8b7cff", "#34e8d4", "#ff7096", "#ffb648"];
        var particles = [];
        var originX = confettiCanvas.width / 2;
        var originY = confettiCanvas.height * 0.25;
        for (var i = 0; i < 90; i++) {
            particles.push({
                x: originX, y: originY,
                vx: (Math.random() - 0.5) * 11,
                vy: Math.random() * -11 - 4,
                size: Math.random() * 6 + 4,
                color: colors[Math.floor(Math.random() * colors.length)],
                rotation: Math.random() * 360,
                rotSpeed: (Math.random() - 0.5) * 12,
                life: 0
            });
        }
        var frame = 0;
        function loop() {
            frame++;
            ctx.clearRect(0, 0, confettiCanvas.width, confettiCanvas.height);
            var alive = false;
            particles.forEach(function (p) {
                p.vy += 0.35;
                p.x += p.vx; p.y += p.vy;
                p.rotation += p.rotSpeed;
                p.life++;
                if (p.life < 140) {
                    alive = true;
                    ctx.save();
                    ctx.translate(p.x, p.y);
                    ctx.rotate(p.rotation * Math.PI / 180);
                    ctx.fillStyle = p.color;
                    ctx.globalAlpha = Math.max(0, 1 - p.life / 140);
                    ctx.fillRect(-p.size / 2, -p.size / 2, p.size, p.size * 0.6);
                    ctx.restore();
                }
            });
            if (alive && frame < 170) requestAnimationFrame(loop);
            else ctx.clearRect(0, 0, confettiCanvas.width, confettiCanvas.height);
        }
        loop();
    }
 
    /* ---- Notificaciones tipo toast ---- */
    var toastContainer = document.getElementById("toastContainer");
    function showToast(message, icon) {
        var el = document.createElement("div");
        el.className = "toast glass";
        var ic = document.createElement("span");
        ic.className = "toast-ic";
        ic.textContent = icon === "star" ? "🎉" : "✅";
        var txt = document.createElement("span");
        txt.textContent = message;
        el.appendChild(ic);
        el.appendChild(txt);
        toastContainer.appendChild(el);
        requestAnimationFrame(function () { el.classList.add("show"); });
        setTimeout(function () {
            el.classList.remove("show");
            el.classList.add("hide");
            setTimeout(function () { el.remove(); }, 400);
        }, 3200);
    }
 
    /* ---- Texto flotante al completar una tarea ---- */
    function spawnFloatingDone(el) {
        var r = el.getBoundingClientRect();
        var span = document.createElement("span");
        span.className = "xp-float";
        span.textContent = "✓ Hecho";
        span.style.left = (r.left + r.width / 2) + "px";
        span.style.top = r.top + "px";
        document.body.appendChild(span);
        requestAnimationFrame(function () { span.classList.add("rise"); });
        setTimeout(function () { span.classList.add("fade"); }, 650);
        setTimeout(function () { span.remove(); }, 1300);
    }
 
    /* ---- Tareas de hoy: marcar/desmarcar con progreso dinámico ---- */
    var dailyFill = document.getElementById("dailyFill");
    var dailyNum = document.getElementById("dailyNum");
    var goals = document.querySelectorAll(".goal-item");
    var TOTAL_TASKS = goals.length;
 
    function updateDailyProgress() {
        var completed = document.querySelectorAll(".goal-item.completed").length;
        var pct = TOTAL_TASKS ? Math.min(100, (completed / TOTAL_TASKS) * 100) : 0;
        dailyFill.style.width = pct + "%";
        dailyNum.textContent = completed + " / " + TOTAL_TASKS + " completadas";
        return completed;
    }
 
    goals.forEach(function (goal) {
        var check = goal.querySelector(".goal-check");
        function toggle() {
            var wasCompleted = goal.classList.contains("completed");
            var done = goal.classList.toggle("completed");
            check.setAttribute("aria-checked", done ? "true" : "false");
            if (done && !wasCompleted) {
                spawnFloatingDone(check);
                showToast("¡Tarea completada!", "check");
            }
            var completed = updateDailyProgress();
            if (completed >= TOTAL_TASKS) {
                burstConfetti();
                showToast("¡Completaste todas tus tareas de hoy!", "star");
            }
        }
        check.addEventListener("click", toggle);
        check.addEventListener("keydown", function (e) {
            if (e.key === "Enter" || e.key === " ") { e.preventDefault(); toggle(); }
        });
    });
 
    /* ---- Estudiantes: marcar como contactado ---- */
    var studentNames = {
        "Mateo Pérez": true,
        "Sebastián Ortiz": "Sebastián",
        "Sofía Londoño": "Sofía",
        "Laura Gómez": "Laura"
    };
    document.querySelectorAll("#studentList .lesson-item").forEach(function (item) {
        if (item.classList.contains("locked")) return;
        item.addEventListener("click", function () {
            var name = item.querySelector(".lesson-name").textContent;
            if (item.classList.contains("done")) {
                showToast(name + " ya está al día.", "check");
                return;
            }
            showToast("Recordatorio enviado a " + name + ".", "check");
        });
        item.setAttribute("tabindex", "0");
        item.setAttribute("role", "button");
        item.addEventListener("keydown", function (e) {
            if (e.key === "Enter" || e.key === " ") { e.preventDefault(); item.click(); }
        });
    });
 
    /* ---- Herramientas rápidas: feedback al hacer clic ---- */
    document.querySelectorAll("#toolsGrid .game-card").forEach(function (card) {
        card.addEventListener("click", function () {
            var title = card.querySelector(".game-title").textContent;
            showToast(title + "…", "check");
        });
    });
 
    updateDailyProgress();
})();
</script>
</body>
</html>