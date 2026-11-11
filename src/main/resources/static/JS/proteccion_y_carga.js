/**
 * HandApp — Protección de Apartados sin Sesión + Pantalla de Carga con Video Oficial
 *
 * 1. Bloquea Abecedario, Lecciones, Juegos, Traductor y Barra de Búsqueda cuando el usuario
 *    no ha iniciado sesión ni se ha registrado (window.HANDAPP_AUTHENTICATED !== true).
 * 2. Muestra el video oficial (/IMG/pantalla_carga.mp4) como pantalla de carga cada vez
 *    que el usuario entra a algún apartado de la aplicación.
 */
(function () {
    'use strict';

    const VIDEO_SRC = '/IMG/pantalla_carga.mp4';

    function isAuthenticated() {
        return window.HANDAPP_AUTHENTICATED === true;
    }

    function getCurrentApartadoName() {
        const path = window.location.pathname.toLowerCase();
        if (path.includes('/admin/panel')) return 'Panel de Administrador';
        if (path.includes('/editor/panel')) return 'Panel de Editor';
        if (path.includes('/abecedario')) return 'Abecedario Interactivo LSC';
        if (path.includes('/traductor')) return 'Traductor IA en Tiempo Real';
        if (path.includes('/estudiante/lecciones')) return 'Lecciones Interactivas';
        if (path.includes('/biblioteca')) return 'Biblioteca de Señas';
        if (path.includes('/video')) return 'Video Introductorio';
        return null;
    }

    function injectStyles() {
        if (document.getElementById('handapp-guard-loader-styles')) return;
        const style = document.createElement('style');
        style.id = 'handapp-guard-loader-styles';
        style.textContent = `
            /* ── Pantalla de Carga con Video ── */
            #handapp-video-loader {
                position: fixed;
                inset: 0;
                z-index: 999999;
                background: radial-gradient(circle at center, #151828 0%, #090b12 100%);
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 24px;
                opacity: 1;
                transition: opacity 0.45s cubic-bezier(.4,0,.2,1), transform 0.45s cubic-bezier(.4,0,.2,1);
            }
            #handapp-video-loader.is-hiding {
                opacity: 0;
                transform: scale(1.03);
                pointer-events: none;
            }
            .handapp-vloader__frame {
                position: relative;
                width: min(860px, 92vw);
                aspect-ratio: 16 / 9;
                border-radius: 24px;
                overflow: hidden;
                background: #000;
                border: 2px solid rgba(139, 132, 255, 0.4);
                box-shadow: 0 28px 80px rgba(0, 0, 0, 0.75), 0 0 45px rgba(108, 99, 255, 0.28);
            }
            .handapp-vloader__video {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }
            .handapp-vloader__bar-wrap {
                width: min(860px, 92vw);
                margin-top: 20px;
                display: flex;
                flex-direction: column;
                gap: 10px;
            }
            .handapp-vloader__info {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                color: #e8eaf6;
                font-family: 'Inter', sans-serif;
            }
            .handapp-vloader__title {
                display: flex;
                align-items: center;
                gap: 10px;
                font-size: 0.95rem;
                font-weight: 700;
                letter-spacing: -0.01em;
            }
            .handapp-vloader__dot {
                width: 10px;
                height: 10px;
                border-radius: 50%;
                background: #8b84ff;
                box-shadow: 0 0 12px #8b84ff;
                animation: handappPulseDot 1.1s infinite ease-in-out;
            }
            @keyframes handappPulseDot {
                0%, 100% { transform: scale(1); opacity: 1; }
                50% { transform: scale(1.35); opacity: 0.55; }
            }
            .handapp-vloader__skip {
                background: rgba(255, 255, 255, 0.1);
                color: #e8eaf6;
                border: 1px solid rgba(255, 255, 255, 0.2);
                padding: 6px 14px;
                border-radius: 999px;
                font-family: 'Inter', sans-serif;
                font-size: 0.8rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s;
            }
            .handapp-vloader__skip:hover {
                background: rgba(108, 99, 255, 0.35);
                border-color: #8b84ff;
            }
            .handapp-vloader__progress {
                width: 100%;
                height: 6px;
                background: rgba(255, 255, 255, 0.12);
                border-radius: 999px;
                overflow: hidden;
            }
            .handapp-vloader__progress-fill {
                height: 100%;
                width: 0%;
                background: linear-gradient(90deg, #6c63ff, #38bdf8, #fbbf24);
                border-radius: 999px;
                transition: width 0.15s linear;
            }

            /* ── Modal de Bloqueo cuando no hay Sesión ── */
            #handapp-auth-lock-modal {
                position: fixed;
                inset: 0;
                z-index: 999990;
                background: rgba(11, 13, 20, 0.78);
                backdrop-filter: blur(8px);
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.25s ease;
            }
            #handapp-auth-lock-modal.show {
                opacity: 1;
                pointer-events: auto;
            }
            .handapp-lock-card {
                width: 100%;
                max-width: 440px;
                background: #1a1d2b;
                border: 1px solid rgba(139, 132, 255, 0.35);
                border-radius: 22px;
                padding: 30px 28px;
                box-shadow: 0 28px 70px rgba(0, 0, 0, 0.65);
                text-align: center;
                color: #e8eaf6;
                font-family: 'Inter', sans-serif;
                transform: translateY(12px) scale(0.96);
                transition: transform 0.25s cubic-bezier(.2,.8,.2,1);
                position: relative;
            }
            #handapp-auth-lock-modal.show .handapp-lock-card {
                transform: translateY(0) scale(1);
            }
            .handapp-lock-close {
                position: absolute;
                top: 14px;
                right: 16px;
                background: rgba(255, 255, 255, 0.07);
                border: none;
                color: #94a3b8;
                width: 30px;
                height: 30px;
                border-radius: 50%;
                font-size: 1.1rem;
                cursor: pointer;
                display: grid;
                place-items: center;
            }
            .handapp-lock-close:hover {
                color: #fff;
                background: rgba(255, 255, 255, 0.15);
            }
            .handapp-lock-mascots {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 14px;
                margin-bottom: 16px;
            }
            .handapp-lock-mascots img {
                width: 68px;
                height: 68px;
                object-fit: contain;
                filter: drop-shadow(0 8px 16px rgba(0,0,0,0.4));
            }
            .handapp-lock-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                background: rgba(224, 82, 82, 0.15);
                border: 1px solid rgba(224, 82, 82, 0.4);
                color: #fca5a5;
                font-size: 0.76rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                padding: 5px 12px;
                border-radius: 999px;
                margin-bottom: 12px;
            }
            .handapp-lock-title {
                font-size: 1.3rem;
                font-weight: 800;
                margin-bottom: 8px;
                color: #fff;
            }
            .handapp-lock-desc {
                font-size: 0.9rem;
                color: #94a3b8;
                line-height: 1.5;
                margin-bottom: 24px;
            }
            .handapp-lock-desc strong {
                color: #c4b5fd;
            }
            .handapp-lock-actions {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }
            .handapp-lock-btn-primary {
                width: 100%;
                background: #6c63ff;
                color: #fff;
                text-decoration: none;
                padding: 13px 18px;
                border-radius: 12px;
                font-weight: 700;
                font-size: 0.95rem;
                transition: all 0.2s;
                display: inline-block;
            }
            .handapp-lock-btn-primary:hover {
                background: #8b84ff;
                transform: translateY(-1px);
            }
            .handapp-lock-btn-secondary {
                width: 100%;
                background: rgba(255, 255, 255, 0.06);
                color: #e8eaf6;
                border: 1px solid rgba(255, 255, 255, 0.15);
                text-decoration: none;
                padding: 12px 18px;
                border-radius: 12px;
                font-weight: 600;
                font-size: 0.9rem;
                transition: all 0.2s;
                display: inline-block;
            }
            .handapp-lock-btn-secondary:hover {
                border-color: #8b84ff;
                background: rgba(108, 99, 255, 0.12);
            }

            /* Indicador visual en barra de búsqueda bloqueada */
            .search-container[data-authenticated="false"] .search-toggle {
                opacity: 0.65;
                cursor: not-allowed;
            }
        `;
        document.head.appendChild(style);
    }

    /**
     * Muestra el video oficial como pantalla de carga.
     * @param {string} label - Nombre del apartado que se está cargando.
     * @param {Function} [onComplete] - Callback opcional al terminar el video.
     */
    function showVideoLoadingScreen(label, onComplete) {
        injectStyles();

        const existing = document.getElementById('handapp-video-loader');
        if (existing) existing.remove();

        const overlay = document.createElement('div');
        overlay.id = 'handapp-video-loader';
        overlay.innerHTML = `
            <div class="handapp-vloader__frame">
                <video class="handapp-vloader__video" src="${VIDEO_SRC}" autoplay muted playsinline></video>
            </div>
            <div class="handapp-vloader__bar-wrap">
                <div class="handapp-vloader__info">
                    <div class="handapp-vloader__title">
                        <span class="handapp-vloader__dot"></span>
                        <span>Cargando ${label || 'apartado'}...</span>
                    </div>
                    <button type="button" class="handapp-vloader__skip">Omitir ⏭</button>
                </div>
                <div class="handapp-vloader__progress">
                    <div class="handapp-vloader__progress-fill"></div>
                </div>
            </div>
        `;

        document.documentElement.appendChild(overlay);

        const video = overlay.querySelector('video');
        const fill = overlay.querySelector('.handapp-vloader__progress-fill');
        const skipBtn = overlay.querySelector('.handapp-vloader__skip');

        let finished = false;
        const finishLoader = () => {
            if (finished) return;
            finished = true;
            if (fill) fill.style.width = '100%';
            overlay.classList.add('is-hiding');
            setTimeout(() => {
                overlay.remove();
                if (typeof onComplete === 'function') onComplete();
            }, 420);
        };

        video.addEventListener('timeupdate', () => {
            if (video.duration && fill) {
                const pct = Math.min(100, (video.currentTime / video.duration) * 100);
                fill.style.width = pct.toFixed(1) + '%';
            }
        });

        video.addEventListener('ended', finishLoader);
        video.addEventListener('error', finishLoader);
        skipBtn.addEventListener('click', finishLoader);

        // Respaldo de seguridad por si el navegador bloquea el autoplay
        const playPromise = video.play();
        if (playPromise && typeof playPromise.catch === 'function') {
            playPromise.catch(() => {
                setTimeout(finishLoader, 1500);
            });
        }
        setTimeout(finishLoader, 9000);
    }

    /**
     * Muestra el modal indicando que se requiere Iniciar Sesión o Registrarse.
     */
    function showAuthRequiredModal(sectionName) {
        injectStyles();

        let modal = document.getElementById('handapp-auth-lock-modal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'handapp-auth-lock-modal';
            modal.innerHTML = `
                <div class="handapp-lock-card" role="dialog" aria-modal="true">
                    <button type="button" class="handapp-lock-close" aria-label="Cerrar">×</button>
                    <div class="handapp-lock-mascots">
                        <img src="/IMG/mascotas3d/mili_front.png" alt="Mili">
                        <img src="/IMG/mascotas3d/lulu_front.png" alt="Lulú">
                    </div>
                    <div class="handapp-lock-badge">🔒 Apartado Bloqueado</div>
                    <h3 class="handapp-lock-title">Inicia sesión para continuar</h3>
                    <p class="handapp-lock-desc">
                        Para acceder a <strong id="handapp-lock-section-name">este apartado</strong> debes iniciar sesión o registrarte previamente en HandApp.
                    </p>
                    <div class="handapp-lock-actions">
                        <a href="/login" class="handapp-lock-btn-primary">Iniciar Sesión</a>
                        <a href="/registro" class="handapp-lock-btn-secondary">Crear Cuenta Gratis</a>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);

            modal.querySelector('.handapp-lock-close').addEventListener('click', () => {
                modal.classList.remove('show');
            });
            modal.addEventListener('click', (e) => {
                if (e.target === modal) modal.classList.remove('show');
            });
        }

        const nameEl = modal.querySelector('#handapp-lock-section-name');
        if (nameEl) nameEl.textContent = sectionName || 'este apartado';
        modal.classList.add('show');
    }

    /**
     * Detecta si un elemento clicado pertenece a Abecedario, Lecciones, Juegos, Traductor o Búsqueda.
     */
    function detectTargetSection(target) {
        if (!target || !target.closest) return null;

        // 1. Barra de búsqueda
        if (target.closest('#searchContainer, #searchToggle, #searchInput, .search-input-wrapper')) {
            return { type: 'search', name: 'la Barra de Búsqueda' };
        }

        // 2. Botones de menú desplegable o acordeones móviles de Lecciones / Juegos
        const dropdownBtn = target.closest('.dropdown-trigger, .mobile-accordion-trigger');
        if (dropdownBtn) {
            const txt = (dropdownBtn.getAttribute('data-text') || dropdownBtn.textContent || '').trim().toLowerCase();
            if (txt.includes('leccion')) return { type: 'lecciones', name: 'el apartado de Lecciones' };
            if (txt.includes('juego')) return { type: 'juegos', name: 'el apartado de Juegos' };
        }

        // 3. Tarjetas de juegos (Memoria / Contrarreloj)
        const gameCard = target.closest('.game-card');
        if (gameCard) {
            const title = gameCard.querySelector('.game-title')?.textContent?.trim() || 'Juegos';
            return { type: 'juego-item', name: `el juego "${title}"` };
        }

        // 4. Enlaces <a> a cualquiera de los apartados
        const link = target.closest('a[href]');
        if (link) {
            const href = (link.getAttribute('href') || '').trim().toLowerCase();
            const text = (link.textContent || '').trim().toLowerCase();

            if (href.includes('/abecedario') || text === 'abecedario') {
                return { type: 'link', name: 'el Abecedario LSC', href: link.href };
            }
            if (href.includes('/estudiante/lecciones') || href === '#lecciones' || text.includes('leccion') || text === 'ver lecciones') {
                return { type: 'link', name: 'las Lecciones', href: link.href };
            }
            if (href === '#juegos' || text.includes('juego') || text === 'jugar ahora' || text === 'memoria' || text === 'contrarreloj') {
                return { type: 'juego-item', name: 'el apartado de Juegos', href: link.href };
            }
            if (href.includes('/traductor') || href === '#traductor' || text.includes('traductor')) {
                return { type: 'link', name: 'el Traductor IA', href: link.href };
            }
            if (href.includes('/biblioteca')) {
                return { type: 'link', name: 'la Biblioteca de Señas', href: link.href };
            }
        }

        // 5. Ítem bloqueado de lecciones
        if (target.closest('.dropdown-item.locked, .mobile-locked-item')) {
            return { type: 'lecciones', name: 'las Lecciones' };
        }

        return null;
    }

    function setupProtectionAndTransitions() {
        injectStyles();

        const authed = isAuthenticated();

        // Si estamos entrando a un apartado (y el usuario está autenticado), mostrar el video de carga
        const currentApartado = getCurrentApartadoName();
        if (currentApartado && authed) {
            showVideoLoadingScreen(currentApartado);
        }

        // Si NO está autenticado, deshabilitar físicamente el input de búsqueda
        if (!authed) {
            const searchInput = document.getElementById('searchInput');
            const searchContainer = document.getElementById('searchContainer');
            if (searchInput) {
                searchInput.disabled = true;
                searchInput.readOnly = true;
                searchInput.value = '';
                searchInput.placeholder = '🔒 Inicia sesión para buscar...';
            }
            if (searchContainer) {
                searchContainer.setAttribute('data-authenticated', 'false');
            }
        }

        // Interceptar clics en fase de captura
        document.addEventListener('click', (e) => {
            const section = detectTargetSection(e.target);
            if (!section) return;

            if (!isAuthenticated()) {
                // Bloquear completamente la acción si no ha iniciado sesión o registrado
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                showAuthRequiredModal(section.name);
                return;
            }

            // Si SÍ está autenticado y hace clic en Juegos o un apartado dentro de la misma página (#juegos, #lecciones, Memoria, Contrarreloj)
            if (section.type === 'juego-item' || (section.href && section.href.includes('#'))) {
                e.preventDefault();
                e.stopPropagation();
                showVideoLoadingScreen(section.name.replace(/^el |^la |^las /i, ''), () => {
                    if (section.type === 'juego-item') {
                        window.location.href = '/estudiante/lecciones/1';
                    }
                });
            }
        }, true);

        // Bloquear envío o tecleo en la barra de búsqueda sin sesión
        document.addEventListener('keydown', (e) => {
            if (e.target && e.target.id === 'searchInput') {
                if (!isAuthenticated()) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    showAuthRequiredModal('la Barra de Búsqueda');
                }
            }
        }, true);
    }

    window.HandAppVideoLoader = {
        show: showVideoLoadingScreen,
        showAuthModal: showAuthRequiredModal
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupProtectionAndTransitions);
    } else {
        setupProtectionAndTransitions();
    }
})();
