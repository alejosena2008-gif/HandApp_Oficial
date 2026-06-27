/**
 * HandApp - Interacciones Únicas
 * JavaScript para animaciones y funcionalidades avanzadas
 */

// ============================================
// CURSOR PERSONALIZADO
// ============================================
class CustomCursor {
    constructor() {
        this.cursor = document.querySelector('.custom-cursor');
        this.follower = document.querySelector('.cursor-follower');
        
        if (!this.cursor || !this.follower) return;
        
        this.cursorPos = { x: 0, y: 0 };
        this.followerPos = { x: 0, y: 0 };
        
        this.init();
    }
    
    init() {
        // Detectar si es dispositivo táctil
        if ('ontouchstart' in window) {
            this.cursor.style.display = 'none';
            this.follower.style.display = 'none';
            return;
        }
        
        document.addEventListener('mousemove', (e) => {
            this.cursorPos.x = e.clientX;
            this.cursorPos.y = e.clientY;
        });
        
        // Efectos hover
        const hoverElements = document.querySelectorAll('a, button, input, .feature-card, .game-card, .dropdown-item');
        
        hoverElements.forEach(el => {
            el.addEventListener('mouseenter', () => {
                this.cursor.classList.add('hover');
                this.follower.classList.add('hover');
            });
            
            el.addEventListener('mouseleave', () => {
                this.cursor.classList.remove('hover');
                this.follower.classList.remove('hover');
            });
        });
        
        this.animate();
    }
    
    animate() {
        // Cursor principal - seguimiento instantáneo
        this.cursor.style.left = `${this.cursorPos.x}px`;
        this.cursor.style.top = `${this.cursorPos.y}px`;
        
        // Follower - seguimiento suave
        this.followerPos.x += (this.cursorPos.x - this.followerPos.x) * 0.15;
        this.followerPos.y += (this.cursorPos.y - this.followerPos.y) * 0.15;
        
        this.follower.style.left = `${this.followerPos.x}px`;
        this.follower.style.top = `${this.followerPos.y}px`;
        
        requestAnimationFrame(() => this.animate());
    }
}

// ============================================
// PARTÍCULAS DE FONDO
// ============================================
class ParticleSystem {
    constructor() {
        this.container = document.getElementById('particles');
        if (!this.container) return;
        
        this.particleCount = 30;
        this.init();
    }
    
    init() {
        for (let i = 0; i < this.particleCount; i++) {
            this.createParticle(i);
        }
    }
    
    createParticle(index) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        
        // Posición aleatoria
        particle.style.left = `${Math.random() * 100}%`;
        particle.style.top = `${Math.random() * 100 + 100}%`;
        
        // Tamaño aleatorio
        const size = Math.random() * 4 + 2;
        particle.style.width = `${size}px`;
        particle.style.height = `${size}px`;
        
        // Delay aleatorio para la animación
        particle.style.animationDelay = `${Math.random() * 15}s`;
        particle.style.animationDuration = `${15 + Math.random() * 10}s`;
        
        this.container.appendChild(particle);
    }
}

// ============================================
// NAVEGACIÓN CON SCROLL
// ============================================
class Navigation {
    constructor() {
        this.header = document.getElementById('header');
        this.mobileMenuToggle = document.getElementById('mobileMenuToggle');
        this.mobileMenu = document.getElementById('mobileMenu');
        this.searchToggle = document.getElementById('searchToggle');
        this.searchContainer = document.getElementById('searchContainer');
        this.searchClose = document.getElementById('searchClose');
        this.searchInput = document.getElementById('searchInput');
        
        this.init();
    }
    
    init() {
        // Scroll handler
        this.handleScroll();
        window.addEventListener('scroll', () => this.handleScroll(), { passive: true });
        
        // Mobile menu
        if (this.mobileMenuToggle && this.mobileMenu) {
            this.mobileMenuToggle.addEventListener('click', () => this.toggleMobileMenu());
            
            // Cerrar menú al hacer clic en enlaces
            this.mobileMenu.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', () => this.closeMobileMenu());
            });
        }
        
        // Search functionality
        if (this.searchToggle && this.searchContainer) {
            this.searchToggle.addEventListener('click', () => this.openSearch());
            this.searchClose?.addEventListener('click', () => this.closeSearch());
            
            // Cerrar con Escape
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.closeSearch();
                    this.closeMobileMenu();
                }
            });
        }
        
        // Accordions móviles
        this.initMobileAccordions();
        
        // Dropdown keyboard navigation
        this.initDropdownKeyboard();
    }
    
    handleScroll() {
        const scrollY = window.scrollY;
        
        if (scrollY > 50) {
            this.header?.classList.add('scrolled');
        } else {
            this.header?.classList.remove('scrolled');
        }
    }
    
    toggleMobileMenu() {
        this.mobileMenuToggle.classList.toggle('active');
        this.mobileMenu.classList.toggle('active');
        document.body.style.overflow = this.mobileMenu.classList.contains('active') ? 'hidden' : '';
    }
    
    closeMobileMenu() {
        this.mobileMenuToggle?.classList.remove('active');
        this.mobileMenu?.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    openSearch() {
        this.searchContainer.classList.add('active');
        this.searchInput?.focus();
    }
    
    closeSearch() {
        this.searchContainer?.classList.remove('active');
        if (this.searchInput) this.searchInput.value = '';
    }
    
    initMobileAccordions() {
        const accordions = document.querySelectorAll('.mobile-accordion');
        
        accordions.forEach(accordion => {
            const trigger = accordion.querySelector('.mobile-accordion-trigger');
            
            trigger?.addEventListener('click', () => {
                // Cerrar otros
                accordions.forEach(a => {
                    if (a !== accordion) a.classList.remove('active');
                });
                
                accordion.classList.toggle('active');
            });
        });
    }
    
    initDropdownKeyboard() {
        const dropdownTriggers = document.querySelectorAll('.dropdown-trigger');
        
        dropdownTriggers.forEach(trigger => {
            trigger.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    const parent = trigger.closest('.has-dropdown');
                    parent?.classList.toggle('dropdown-open');
                }
            });
        });
    }
}

// ============================================
// ANIMACIÓN DE MANO EN HERO
// ============================================
class HandAnimation {
    constructor() {
        this.hand = document.getElementById('animatedHand');
        this.letterDisplay = document.getElementById('currentLetter');
        
        if (!this.hand || !this.letterDisplay) return;
        
        this.letters = ['A', 'B', 'C', 'H', 'O', 'L', 'A'];
        this.currentIndex = 0;
        this.handPositions = {
            'A': { thumb: 100, index: 30, middle: 20, ring: 30, pinky: 50 },
            'B': { thumb: 100, index: 30, middle: 20, ring: 30, pinky: 50 },
            'C': { thumb: 80, index: 50, middle: 40, ring: 50, pinky: 60 },
            'H': { thumb: 100, index: 30, middle: 20, ring: 100, pinky: 100 },
            'O': { thumb: 60, index: 60, middle: 60, ring: 60, pinky: 60 },
            'L': { thumb: 35, index: 30, middle: 100, ring: 100, pinky: 100 }
        };
        
        this.init();
    }
    
    init() {
        this.animateLetter();
        setInterval(() => this.animateLetter(), 2000);
    }
    
    animateLetter() {
        const letter = this.letters[this.currentIndex];
        const positions = this.handPositions[letter] || this.handPositions['A'];
        
        // Actualizar display de letra con animación
        this.letterDisplay.style.transform = 'scale(0.8)';
        this.letterDisplay.style.opacity = '0';
        
        setTimeout(() => {
            this.letterDisplay.textContent = letter;
            this.letterDisplay.style.transform = 'scale(1)';
            this.letterDisplay.style.opacity = '1';
        }, 200);
        
        // Animar dedos
        const fingers = this.hand.querySelectorAll('.finger');
        fingers.forEach((finger, index) => {
            const fingerNames = ['thumb', 'index', 'middle', 'ring', 'pinky'];
            const rotation = (positions[fingerNames[index]] - 50) * 0.5;
            finger.style.transition = 'transform 0.5s ease-out';
            finger.style.transformOrigin = 'center bottom';
            finger.style.transform = `rotate(${rotation}deg)`;
        });
        
        this.currentIndex = (this.currentIndex + 1) % this.letters.length;
    }
}

// ============================================
// CONTADOR ANIMADO DE ESTADÍSTICAS
// ============================================
class StatsCounter {
    constructor() {
        this.stats = document.querySelectorAll('.stat-number[data-count]');
        this.animated = false;
        
        if (this.stats.length === 0) return;
        
        this.init();
    }
    
    init() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !this.animated) {
                    this.animated = true;
                    this.animateAll();
                }
            });
        }, { threshold: 0.5 });
        
        this.stats.forEach(stat => observer.observe(stat));
    }
    
    animateAll() {
        this.stats.forEach(stat => {
            const target = parseInt(stat.dataset.count, 10);
            this.animateCounter(stat, target);
        });
    }
    
    animateCounter(element, target) {
        const duration = 2000;
        const startTime = performance.now();
        
        const animate = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Easing function (ease-out)
            const easeOut = 1 - Math.pow(1 - progress, 3);
            const current = Math.floor(target * easeOut);
            
            element.textContent = current;
            
            if (progress < 1) {
                requestAnimationFrame(animate);
            } else {
                element.textContent = target;
            }
        };
        
        requestAnimationFrame(animate);
    }
}

// ============================================
// ANIMACIONES DE SCROLL (AOS alternativo)
// ============================================
class ScrollAnimations {
    constructor() {
        this.elements = document.querySelectorAll('[data-aos]');
        
        if (this.elements.length === 0) return;
        
        this.init();
    }
    
    init() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('aos-animate');
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });
        
        this.elements.forEach(el => observer.observe(el));
    }
}

// ============================================
// SMOOTH SCROLL PARA ENLACES INTERNOS
// ============================================
class SmoothScroll {
    constructor() {
        this.init();
    }
    
    init() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', (e) => {
                const href = anchor.getAttribute('href');
                if (href === '#') return;
                
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    
                    const headerHeight = document.querySelector('.header')?.offsetHeight || 0;
                    const targetPosition = target.getBoundingClientRect().top + window.scrollY - headerHeight - 20;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });
    }
}

// ============================================
// EFECTO PARALLAX PARA EL HERO
// ============================================
class ParallaxEffect {
    constructor() {
        this.floatingHands = document.querySelectorAll('.floating-hand');
        this.heroGradient = document.querySelector('.hero-gradient');
        
        if (this.floatingHands.length === 0) return;
        
        this.init();
    }
    
    init() {
        window.addEventListener('scroll', () => this.handleScroll(), { passive: true });
        document.addEventListener('mousemove', (e) => this.handleMouseMove(e), { passive: true });
    }
    
    handleScroll() {
        const scrollY = window.scrollY;
        
        this.floatingHands.forEach(hand => {
            const speed = parseFloat(hand.dataset.speed) || 1;
            hand.style.transform = `translateY(${scrollY * speed * 0.1}px)`;
        });
        
        if (this.heroGradient) {
            this.heroGradient.style.transform = `translateY(${scrollY * 0.3}px)`;
        }
    }
    
    handleMouseMove(e) {
        const { clientX, clientY } = e;
        const centerX = window.innerWidth / 2;
        const centerY = window.innerHeight / 2;
        
        const moveX = (clientX - centerX) / centerX;
        const moveY = (clientY - centerY) / centerY;
        
        this.floatingHands.forEach((hand, index) => {
            const intensity = (index + 1) * 10;
            hand.style.transform = `translate(${moveX * intensity}px, ${moveY * intensity}px)`;
        });
    }
}

// ============================================
// TIPEO ANIMADO (Typewriter Effect)
// ============================================
class TypeWriter {
    constructor(element, words, wait = 3000) {
        this.element = element;
        this.words = words;
        this.txt = '';
        this.wordIndex = 0;
        this.wait = parseInt(wait, 10);
        this.isDeleting = false;
        
        this.type();
    }
    
    type() {
        const current = this.wordIndex % this.words.length;
        const fullTxt = this.words[current];
        
        if (this.isDeleting) {
            this.txt = fullTxt.substring(0, this.txt.length - 1);
        } else {
            this.txt = fullTxt.substring(0, this.txt.length + 1);
        }
        
        this.element.innerHTML = `<span class="txt">${this.txt}</span>`;
        
        let typeSpeed = 100;
        
        if (this.isDeleting) {
            typeSpeed /= 2;
        }
        
        if (!this.isDeleting && this.txt === fullTxt) {
            typeSpeed = this.wait;
            this.isDeleting = true;
        } else if (this.isDeleting && this.txt === '') {
            this.isDeleting = false;
            this.wordIndex++;
            typeSpeed = 500;
        }
        
        setTimeout(() => this.type(), typeSpeed);
    }
}

// ============================================
// EFECTO RIPPLE EN BOTONES
// ============================================
class RippleEffect {
    constructor() {
        this.init();
    }
    
    init() {
        document.querySelectorAll('.btn').forEach(btn => {
            btn.addEventListener('click', (e) => this.createRipple(e, btn));
        });
    }
    
    createRipple(event, button) {
        const ripple = document.createElement('span');
        const rect = button.getBoundingClientRect();
        
        const size = Math.max(rect.width, rect.height);
        const x = event.clientX - rect.left - size / 2;
        const y = event.clientY - rect.top - size / 2;
        
        ripple.style.cssText = `
            position: absolute;
            width: ${size}px;
            height: ${size}px;
            left: ${x}px;
            top: ${y}px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: scale(0);
            animation: ripple-animation 0.6s ease-out;
            pointer-events: none;
        `;
        
        button.style.position = 'relative';
        button.style.overflow = 'hidden';
        button.appendChild(ripple);
        
        setTimeout(() => ripple.remove(), 600);
    }
}

// Agregar estilos de ripple
const rippleStyles = document.createElement('style');
rippleStyles.textContent = `
    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(rippleStyles);

// ============================================
// MAGNETIC BUTTONS (efecto magnético)
// ============================================
class MagneticButtons {
    constructor() {
        this.buttons = document.querySelectorAll('.btn-primary, .btn-large');
        
        if (this.buttons.length === 0 || 'ontouchstart' in window) return;
        
        this.init();
    }
    
    init() {
        this.buttons.forEach(btn => {
            btn.addEventListener('mousemove', (e) => this.handleMouseMove(e, btn));
            btn.addEventListener('mouseleave', (e) => this.handleMouseLeave(e, btn));
        });
    }
    
    handleMouseMove(e, btn) {
        const rect = btn.getBoundingClientRect();
        const x = e.clientX - rect.left - rect.width / 2;
        const y = e.clientY - rect.top - rect.height / 2;
        
        btn.style.transform = `translate(${x * 0.2}px, ${y * 0.2}px)`;
    }
    
    handleMouseLeave(e, btn) {
        btn.style.transform = 'translate(0, 0)';
    }
}

// ============================================
// TEXT REVEAL ON SCROLL
// ============================================
class TextReveal {
    constructor() {
        this.elements = document.querySelectorAll('.hero-title .title-line');
        
        if (this.elements.length === 0) return;
        
        this.init();
    }
    
    init() {
        // Ya se animan con CSS, pero podemos agregar más efectos
        this.elements.forEach((el, index) => {
            el.style.animationDelay = `${0.1 + index * 0.15}s`;
        });
    }
}

// ============================================
// SEARCH FUNCTIONALITY
// ============================================
class SearchFunctionality {
    constructor() {
        this.searchInput = document.getElementById('searchInput');

        if (!this.searchInput) return;

        this.init();
    }

    init() {
        // Al presionar Enter redirige a la biblioteca con ?buscar=
        this.searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                const query = this.searchInput.value.trim();
                if (query) {
                    window.location.href = `html/biblioteca_de_señas.html?buscar=${encodeURIComponent(query)}`;
                }
            }
        });
    }
}

// ============================================
// INICIALIZACIÓN
// ============================================
document.addEventListener('DOMContentLoaded', () => {
    // Inicializar todos los módulos
    new CustomCursor();
    new ParticleSystem();
    new Navigation();
    new HandAnimation();
    new StatsCounter();
    new ScrollAnimations();
    new SmoothScroll();
    new ParallaxEffect();
    new RippleEffect();
    new MagneticButtons();
    new TextReveal();
    new SearchFunctionality();
    
    // Preloader (opcional)
    document.body.classList.add('loaded');
    
    console.log('🤟 HandApp initialized successfully!');
});

// ============================================
// UTILIDADES
// ============================================

// Debounce function para optimizar eventos
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Throttle function para limitar ejecuciones
function throttle(func, limit) {
    let inThrottle;
    return function(...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// Detectar preferencia de movimiento reducido
const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

if (prefersReducedMotion) {
    console.log('Reduced motion preference detected. Some animations disabled.');
}

// Exportar utilidades para uso externo
window.HandApp = {
    debounce,
    throttle,
    prefersReducedMotion
};