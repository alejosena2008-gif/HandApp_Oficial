/**
 * HandApp 3D Mascots Engine — Modelos Oficiales 3D de Mili (Foca) y Lulú (Patito)
 *
 * - Usa los 6 renders 3D oficiales (izquierda, frente, derecha) con cambio dinámico de pose 3D
 *   según la posición del cursor o el arrastre con el mouse.
 * - Coloca a Mili y Lulú GRANDES y en LADOS OPUESTOS DE LA PANTALLA (Mili a la izquierda,
 *   Lulú a la derecha).
 * - Aparecen cuando el usuario está logueado Y en todos los FORMULARIOS (login, registro,
 *   usuario-form, leccion-form), donde cada mascota dice frases contextuales sobre el formulario
 *   y reacciona en vivo al campo que el usuario está completando.
 */
(function () {
    'use strict';

    const SPRITES = {
        mili: {
            left:  '/IMG/mascotas3d/mili_left.png',
            front: '/IMG/mascotas3d/mili_front.png',
            right: '/IMG/mascotas3d/mili_right.png'
        },
        lulu: {
            left:  '/IMG/mascotas3d/lulu_left.png',
            front: '/IMG/mascotas3d/lulu_front.png',
            right: '/IMG/mascotas3d/lulu_right.png'
        }
    };

    const FRASES_GENERAL_MILI = [
        '¡Hola! Soy Mili 🦭 ¿Practicamos una seña nueva hoy?',
        '¡Recuerda mantener tu mano bien iluminada en el traductor!',
        '¡Cada seña que aprendes acerca a más personas! 💜',
        '¡Qué orgullo verte avanzar paso a paso en HandApp!'
    ];

    const FRASES_GENERAL_LULU = [
        '¡Cuac! Soy Lulú 🐥 ¡Vamos a romper tu récord en las lecciones!',
        '¡Pasa el mouse o arrástrame para verme girar en 3D!',
        '¡Explora las 27 letras del abecedario interactivo! ✨',
        '¡La práctica constante te hará experto en LSC!'
    ];

    // Diálogos específicos por formulario y por campo para Mili y Lulú
    const FORM_DIALOGUES = {
        login: {
            initial: {
                mili: '¡Hola de nuevo! 🦭 Ingresa tu correo y contraseña en el formulario para retomar tus clases de LSC.',
                lulu: '¡Te estábamos esperando! 🐥 Si aún no tienes cuenta, dale clic abajo en "Regístrate gratis".'
            },
            error: {
                mili: '¡Uy! Parece que el correo o la contraseña no coinciden. ¡Inténtalo de nuevo con calma!',
                lulu: 'Tip: toca el ícono del ojito 👁️ en la contraseña para revisar que esté bien escrita.'
            },
            registrado: {
                mili: '¡Felicidades por crear tu cuenta! 🎉 Escribe tu correo y contraseña para entrar por primera vez.',
                lulu: '¡Qué emoción! Ya tenemos todo listo para empezar tu primera lección de señas.'
            },
            logout: {
                mili: 'Cerraste tu sesión de forma segura. ¡Vuelve a ingresar cuando quieras seguir practicando!',
                lulu: '¡Aquí cuidaremos tu progreso hasta que regreses! 🐥✨'
            },
            recuperado: {
                mili: '¡Cuenta recuperada con Google! 🎉 Ya puedes ingresar con tu nueva contraseña.',
                lulu: '¡Qué alivio tenerte de vuelta! 🐥 Tus lecciones siguen intactas.'
            },
            requerido: {
                mili: '¡Para entrar a Abecedario, Lecciones, Juegos, Traductor o usar el Buscador primero debes iniciar sesión! 🦭🔒',
                lulu: '¡Si todavía no tienes cuenta, dale clic abajo en "Regístrate gratis" o entra con Google! 🐥✨'
            },
            fields: {
                correo: {
                    mili: 'Escribe aquí el correo electrónico con el que te registraste en HandApp.',
                    lulu: 'Si olvidaste tu clave, usa el botón "Iniciar sesión con Google (Recuperar cuenta)" de abajo.'
                },
                password: {
                    mili: 'Ingresa tu contraseña secreta para desbloquear tus lecciones y tu progreso.',
                    lulu: 'Puedes presionar el botón del ojito 👁️ a la derecha o recuperarla con Google.'
                }
            }
        },
        recuperar: {
            initial: {
                mili: '¡Tranquilo! 🦭 Con tu cuenta de Google recuperaremos tu acceso a HandApp en segundos.',
                lulu: '¡Haz clic en "Continuar con Google" para verificar tu identidad de forma segura! 🐥🔐'
            },
            steps: {
                selector: {
                    mili: 'Selecciona tu cuenta de Google en la lista o elige "Usar otra cuenta de Google".',
                    lulu: 'Google verificará que eres el dueño del correo sin que tengas que recordar tu clave anterior.'
                },
                otra: {
                    mili: 'Escribe tu correo de Gmail y tu nombre para vincular y recuperar tu cuenta.',
                    lulu: 'Luego presiona "Verificar con Google" para pasar al cambio de contraseña. ✨'
                },
                verificado: {
                    mili: '¡Identidad verificada con Google! ✅ Ya puedes entrar directo o crear tu nueva contraseña abajo.',
                    lulu: '¡Genial! Si quieres cambiar tu contraseña, escríbela en los dos campos y guarda los cambios. 🎉'
                }
            },
            fields: {
                otraCuentaCorreo: {
                    mili: 'Ingresa el correo de tu cuenta de Google (ej. usuario@gmail.com).',
                    lulu: 'Con este correo buscaremos tu progreso en HandApp.'
                },
                otraCuentaNombre: {
                    mili: 'Escribe tu nombre para que podamos saludarte en la plataforma.',
                    lulu: '¡Así sabremos cómo llamarte cuando entres a practicar señas! 🐥'
                },
                nuevaContrasena: {
                    mili: 'Escribe una nueva contraseña que sea fácil de recordar para ti pero segura.',
                    lulu: 'Mínimo 4 caracteres; la barra de colores te mostrará qué tan fuerte es. 💪'
                },
                confirmarContrasena: {
                    mili: 'Repite tu nueva contraseña para confirmar que no haya errores de escritura.',
                    lulu: '¡Al presionar "Guardar nueva contraseña e ingresar" entrarás directo a HandApp! 🚀'
                }
            }
        },
        register: {
            initial: {
                mili: '¡Bienvenido a HandApp! 🦭 Puedes registrarte en un clic con Google o Apple, o llenar el formulario.',
                lulu: '¡Qué alegría tener un nuevo estudiante! 🐥 Elige Google, Apple o escribe tus datos paso a paso.'
            },
            social: {
                google: {
                    mili: '¡Elegiste Google! 🌐 Selecciona tu cuenta de Gmail o escribe tu correo para crear tu perfil al instante.',
                    lulu: '¡Con Google no necesitas memorizar otra contraseña y entras directo a tus lecciones! 🐥✨'
                },
                apple: {
                    mili: '¡Elegiste Apple ID! 🍎 Selecciona tu cuenta de iCloud o vincúlala para registrarte de forma rápida y segura.',
                    lulu: '¡Súper rápido! Con tu Apple ID guardaremos todo tu progreso en Lengua de Señas. 🎉'
                }
            },
            fields: {
                socialCustomNombre: {
                    mili: 'Escribe tu nombre completo para vincularlo a tu cuenta de Google o Apple.',
                    lulu: '¡Así te saludaremos apenas entres a HandApp! 🐥'
                },
                socialCustomCorreo: {
                    mili: 'Ingresa tu correo de Google (@gmail.com) o Apple (@icloud.com).',
                    lulu: 'Puedes darle a "Continuar" para entrar directo o "Usar en formulario".'
                },
                nombre: {
                    mili: 'Escribe tu nombre completo; así te saludaremos en tu perfil y en los reportes.',
                    lulu: '¡Me encanta conocer nuevos amigos que quieren aprender Lengua de Señas! 🐥'
                },
                correo: {
                    mili: 'Ingresa un correo electrónico válido; será tu usuario para iniciar sesión.',
                    lulu: 'Con tu correo guardaremos tus estrellas y el avance de cada lección.'
                },
                contrasena: {
                    mili: 'Crea una contraseña segura de al menos 6 caracteres para proteger tu cuenta.',
                    lulu: '¡Consejo! Combina letras mayúsculas y números para que la barra se ponga verde. 💪'
                },
                confirmarContrasena: {
                    mili: 'Repite exactamente la misma contraseña para confirmar que la escribiste bien.',
                    lulu: '¡Si ambas contraseñas coinciden, ya casi estamos listos para empezar!'
                },
                terminos: {
                    mili: '¡Excelente! Al aceptar los términos y condiciones ya puedes presionar "Crear mi cuenta".',
                    lulu: '¡Todo listo para despegar! 🚀 Dale clic al botón para unirte a HandApp.'
                }
            }
        },
        adminUsuario: {
            initial: {
                mili: 'En este formulario puedes registrar o actualizar los datos y el rol de un usuario.',
                lulu: 'Recuerda la regla del formulario: @handapp.com para Admin y @editorhand.com para Editor.'
            },
            fields: {
                nombre: {
                    mili: 'Ingresa el nombre y apellido del usuario que deseas gestionar.',
                    lulu: 'Este nombre se mostrará en el panel de control y en los reportes exportables.'
                },
                correo: {
                    mili: 'Verifica el dominio del correo según el rol que le vayas a asignar.',
                    lulu: 'ADMINISTRADOR requiere @handapp.com y EDITOR requiere @editorhand.com.'
                },
                contrasena: {
                    mili: 'Asigna una contraseña inicial segura (o déjala en blanco si editas y no deseas cambiarla).',
                    lulu: '¡La contraseña se almacena cifrada para proteger la seguridad del usuario!'
                },
                rol: {
                    mili: 'Selecciona el rol: Estudiante, Editor de lecciones o Administrador del sistema.',
                    lulu: 'Cada rol desbloquea permisos específicos dentro de la plataforma HandApp.'
                }
            }
        },
        editorLeccion: {
            initial: {
                mili: '¡Diseñemos una lección increíble! Define su título, descripción y orden de aparición.',
                lulu: 'Cada lección que publicas aquí aparece enseguida para todos los estudiantes.'
            },
            fields: {
                titulo: {
                    mili: 'Escribe un título claro y motivador, por ejemplo: "Saludos Básicos" o "La Familia".',
                    lulu: '¡Un buen título despierta la curiosidad de todos los estudiantes! ✨'
                },
                descripcion: {
                    mili: 'Resume qué señas o temas aprenderán los estudiantes dentro de esta lección.',
                    lulu: 'Una descripción breve les ayuda a saber qué practicarán hoy.'
                },
                contenidoUrl: {
                    mili: 'Opcionalmente puedes pegar el enlace a un video o recurso visual de apoyo.',
                    lulu: '¡Los recursos visuales hacen mucho más fácil memorizar cada seña!'
                },
                orden: {
                    mili: 'El número de orden define en qué posición se mostrará en el menú de lecciones.',
                    lulu: 'Por ejemplo: 1 para la primera lección, 2 para la siguiente, y así sucesivamente.'
                },
                vista: {
                    mili: 'Escribe el nombre del archivo HTML del quiz (ej. leccion-saludos) sin el ".html".',
                    lulu: 'Si lo dejas vacío, nosotras saldremos con casco de construcción avisando que está en preparación. 👷‍♀️'
                },
                publicada: {
                    mili: 'Marca esta casilla cuando la lección esté lista para que los estudiantes la vean.',
                    lulu: 'Si la desmarcas, quedará guardada como borrador privado en tu panel.'
                }
            }
        }
    };

    const loadedImages = { mili: {}, lulu: {} };
    const instances = [];
    const pointer = { x: window.innerWidth * 0.5, y: window.innerHeight * 0.5 };
    let dockControllers = { mili: null, lulu: null };

    function preloadSprites() {
        ['mili', 'lulu'].forEach((who) => {
            ['left', 'front', 'right'].forEach((pose) => {
                const img = new Image();
                img.src = SPRITES[who][pose];
                loadedImages[who][pose] = img;
            });
        });
    }

    window.addEventListener('mousemove', (e) => {
        pointer.x = e.clientX;
        pointer.y = e.clientY;
    }, { passive: true });

    function detectFormType() {
        if (document.getElementById('recoveryForm') || window.location.pathname.includes('/recuperar-cuenta')) return 'recuperar';
        if (document.getElementById('loginForm')) return 'login';
        if (document.getElementById('registerForm')) return 'register';
        const path = window.location.pathname;
        if (path.includes('/admin/usuarios')) return 'adminUsuario';
        if (path.includes('/editor/lecciones')) return 'editorLeccion';
        if (document.querySelector('.form-card form')) {
            return document.querySelector('select#rol') ? 'adminUsuario' : 'editorLeccion';
        }
        return null;
    }

    function isUserAuthenticated() {
        if (typeof window.HANDAPP_AUTHENTICATED === 'boolean') {
            return window.HANDAPP_AUTHENTICATED;
        }
        if (document.querySelector('[data-authenticated="true"]')) return true;
        if (document.getElementById('userMenuToggle')) return true;
        if (document.getElementById('lesson-card') || document.querySelector('.mascots-construccion')) return true;
        return false;
    }

    function injectStyles() {
        if (document.getElementById('handapp-mascot3d-styles')) return;
        const style = document.createElement('style');
        style.id = 'handapp-mascot3d-styles';
        style.textContent = `
            .seal-wrap, .duck-wrap, .mascot-wrap {
                position: relative !important;
                display: inline-flex !important;
                align-items: center;
                justify-content: center;
                cursor: grab;
                user-select: none;
                -webkit-user-select: none;
                perspective: 700px;
            }
            .seal-wrap:active, .duck-wrap:active, .mascot-wrap:active {
                cursor: grabbing;
            }
            .mascot-3d-canvas {
                width: 100% !important;
                height: 100% !important;
                display: block;
                pointer-events: auto;
                filter: drop-shadow(0 12px 22px rgba(15, 23, 42, 0.28));
                transition: filter 0.25s ease;
                will-change: transform;
            }
            .seal-wrap:hover .mascot-3d-canvas,
            .duck-wrap:hover .mascot-3d-canvas,
            .mascot-wrap:hover .mascot-3d-canvas,
            .handapp-side-dock__stage:hover .mascot-3d-canvas {
                filter: drop-shadow(0 16px 28px rgba(99, 102, 241, 0.38));
            }
            .mascot-svg-hidden {
                position: absolute !important;
                width: 0 !important;
                height: 0 !important;
                opacity: 0 !important;
                pointer-events: none !important;
                overflow: hidden !important;
            }

            /* ── Mascotas 3D Grandes en Lados Opuestos de la Pantalla ── */
            .handapp-side-dock {
                position: fixed;
                z-index: 9990;
                display: flex;
                flex-direction: column;
                align-items: center;
                pointer-events: none;
                transition: transform 0.35s cubic-bezier(.2,.8,.2,1), opacity 0.3s ease;
            }
            .handapp-side-dock--left {
                left: 22px;
                bottom: 18px;
            }
            .handapp-side-dock--right {
                right: 22px;
                bottom: 76px;
            }
            /* En páginas de formulario, centrarlas verticalmente a los lados del formulario */
            .handapp-side-dock.is-form-mode.handapp-side-dock--left {
                left: 3vw;
                bottom: 50%;
                transform: translateY(52%);
            }
            .handapp-side-dock.is-form-mode.handapp-side-dock--right {
                right: 3vw;
                bottom: 50%;
                transform: translateY(52%);
            }
            .handapp-side-dock.is-minimized .handapp-side-dock__stage,
            .handapp-side-dock.is-minimized .handapp-side-dock__bubble {
                display: none !important;
            }
            .handapp-side-dock__stage {
                width: 215px;
                height: 230px;
                position: relative;
                pointer-events: auto;
                cursor: pointer;
                perspective: 800px;
            }
            .handapp-side-dock__bar {
                display: flex;
                align-items: center;
                gap: 6px;
                margin-top: -6px;
                pointer-events: auto;
                background: rgba(255, 255, 255, 0.92);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(99, 102, 241, 0.28);
                padding: 4px 12px;
                border-radius: 999px;
                box-shadow: 0 6px 18px rgba(15, 23, 42, 0.14);
            }
            html[data-theme="dark"] .handapp-side-dock__bar {
                background: rgba(30, 23, 41, 0.92);
                border-color: rgba(167, 139, 250, 0.35);
            }
            .handapp-side-dock__name {
                font-family: 'Inter', sans-serif;
                font-size: 0.76rem;
                font-weight: 800;
                letter-spacing: 0.06em;
                text-transform: uppercase;
                color: #6366f1;
            }
            html[data-theme="dark"] .handapp-side-dock__name {
                color: #c4b5fd;
            }
            .handapp-side-dock--right .handapp-side-dock__name {
                color: #d97706;
            }
            html[data-theme="dark"] .handapp-side-dock--right .handapp-side-dock__name {
                color: #fbbf24;
            }
            .handapp-side-dock__toggle {
                border: none;
                background: rgba(99, 102, 241, 0.14);
                color: inherit;
                width: 20px;
                height: 20px;
                border-radius: 50%;
                font-size: 12px;
                font-weight: 800;
                line-height: 1;
                cursor: pointer;
                display: grid;
                place-items: center;
                transition: background 0.2s;
            }
            .handapp-side-dock__toggle:hover {
                background: rgba(99, 102, 241, 0.28);
            }
            .handapp-side-dock__bubble {
                position: absolute;
                bottom: 236px;
                width: 245px;
                padding: 12px 15px;
                border-radius: 18px;
                background: rgba(255, 255, 255, 0.97);
                border: 1.5px solid rgba(99, 102, 241, 0.38);
                box-shadow: 0 14px 32px rgba(15, 23, 42, 0.22);
                color: #1e293b;
                font-family: 'Inter', sans-serif;
                font-size: 0.82rem;
                font-weight: 600;
                line-height: 1.42;
                pointer-events: auto;
                opacity: 0;
                transform: translateY(8px) scale(0.95);
                transition: opacity 0.25s ease, transform 0.25s ease, border-color 0.25s ease;
                z-index: 10;
            }
            .handapp-side-dock--right .handapp-side-dock__bubble {
                border-color: rgba(245, 158, 11, 0.45);
            }
            html[data-theme="dark"] .handapp-side-dock__bubble {
                background: rgba(28, 22, 43, 0.97);
                color: #f4efff;
                border-color: rgba(167, 139, 250, 0.45);
            }
            html[data-theme="dark"] .handapp-side-dock--right .handapp-side-dock__bubble {
                border-color: rgba(251, 191, 36, 0.45);
            }
            .handapp-side-dock__speaker {
                display: block;
                font-size: 0.7rem;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 0.06em;
                margin-bottom: 4px;
                color: #6366f1;
            }
            html[data-theme="dark"] .handapp-side-dock__speaker {
                color: #a78bfa;
            }
            .handapp-side-dock--right .handapp-side-dock__speaker {
                color: #d97706;
            }
            html[data-theme="dark"] .handapp-side-dock--right .handapp-side-dock__speaker {
                color: #fbbf24;
            }
            .handapp-side-dock--left .handapp-side-dock__bubble {
                left: 4px;
                border-bottom-left-radius: 4px;
            }
            .handapp-side-dock--right .handapp-side-dock__bubble {
                right: 4px;
                border-bottom-right-radius: 4px;
            }
            .handapp-side-dock__bubble.show {
                opacity: 1;
                transform: translateY(0) scale(1);
            }

            @media (max-width: 1100px) {
                .handapp-side-dock.is-form-mode.handapp-side-dock--left { left: 10px; }
                .handapp-side-dock.is-form-mode.handapp-side-dock--right { right: 10px; }
                .handapp-side-dock__stage {
                    width: 155px;
                    height: 168px;
                }
                .handapp-side-dock__bubble {
                    bottom: 175px;
                    width: 195px;
                    font-size: 0.75rem;
                }
            }
            @media (max-width: 760px) {
                .handapp-side-dock.is-form-mode.handapp-side-dock--left {
                    bottom: 14px;
                    transform: none;
                }
                .handapp-side-dock.is-form-mode.handapp-side-dock--right {
                    bottom: 70px;
                    transform: none;
                }
                .handapp-side-dock__stage {
                    width: 105px;
                    height: 115px;
                }
                .handapp-side-dock__bubble {
                    bottom: 120px;
                    width: 160px;
                    font-size: 0.7rem;
                    padding: 8px 10px;
                }
            }
        `;
        document.head.appendChild(style);
    }

    function drawStar(ctx, cx, cy, radius, rot, color) {
        ctx.save();
        ctx.translate(cx, cy);
        ctx.rotate(rot);
        ctx.beginPath();
        for (let i = 0; i < 10; i++) {
            const r = i % 2 === 0 ? radius : radius * 0.44;
            const a = (i * Math.PI) / 5 - Math.PI / 2;
            const x = Math.cos(a) * r;
            const y = Math.sin(a) * r;
            if (i === 0) ctx.moveTo(x, y);
            else ctx.lineTo(x, y);
        }
        ctx.closePath();
        ctx.fillStyle = color || '#fbbf24';
        ctx.shadowColor = color || '#fbbf24';
        ctx.shadowBlur = 8;
        ctx.fill();
        ctx.restore();
    }

    function drawHardHatOverlay(ctx, w, h) {
        ctx.save();
        ctx.translate(w * 0.5, h * 0.14);
        const grad = ctx.createRadialGradient(-w * 0.06, -h * 0.04, 2, 0, 0, w * 0.22);
        grad.addColorStop(0, '#fef08a');
        grad.addColorStop(0.6, '#fbbf24');
        grad.addColorStop(1, '#d97706');
        ctx.fillStyle = grad;
        ctx.beginPath();
        ctx.arc(0, 0, w * 0.17, Math.PI, 0);
        ctx.closePath();
        ctx.fill();
        ctx.fillStyle = '#f59e0b';
        ctx.beginPath();
        ctx.ellipse(0, 0, w * 0.21, h * 0.032, 0, 0, Math.PI * 2);
        ctx.fill();
        ctx.restore();
    }

    function selectPoseImage(inst) {
        const rect = inst.canvas.getBoundingClientRect();
        const centerX = rect.left + rect.width * 0.5;
        const dx = pointer.x - centerX;
        const normDx = dx / Math.max(window.innerWidth * 0.35, 180) + inst.userYaw;

        const set = loadedImages[inst.type];
        if (normDx < -0.22) {
            return set.left;
        } else if (normDx > 0.22) {
            return set.right;
        }
        return set.front;
    }

    function renderInstance(inst, now) {
        const { ctx, canvas, width: w, height: h, phaseOffset } = inst;
        const t = now * 0.001 + phaseOffset;
        const elapsed = (now - inst.reactionStart) * 0.001;

        ctx.clearRect(0, 0, w, h);

        const rect = canvas.getBoundingClientRect();
        const cx = rect.left + rect.width * 0.5;
        const cy = rect.top + rect.height * 0.5;
        const relX = Math.max(-1, Math.min(1, (pointer.x - cx) / Math.max(window.innerWidth * 0.5, 200))) + inst.userYaw;
        const relY = Math.max(-1, Math.min(1, (pointer.y - cy) / Math.max(window.innerHeight * 0.5, 200))) + inst.userPitch;

        let floatY = Math.sin(t * 2.4) * (h * 0.025);
        let scaleX = 1 + Math.sin(t * 2.4) * 0.012;
        let scaleY = 1 - Math.sin(t * 2.4) * 0.012;
        let rotZ = Math.sin(t * 1.6) * 0.025 + relX * 0.04;

        const effectiveMood = inst.mood === 'default' ? (inst.defaultHappy ? 'happy' : 'neutral') : inst.mood;

        if (inst.mood === 'happy' && elapsed < 0.9) {
            floatY -= Math.abs(Math.sin(elapsed * Math.PI * 3)) * (h * 0.12);
            scaleX = 1 + Math.sin(elapsed * Math.PI * 6) * 0.05;
            scaleY = 1 - Math.sin(elapsed * Math.PI * 6) * 0.05;
        } else if (inst.mood === 'sad' && elapsed < 0.8) {
            rotZ += Math.sin(elapsed * Math.PI * 8) * 0.14;
            floatY += h * 0.03;
        } else if (inst.mood === 'celebrate' && elapsed < 1.1) {
            floatY -= Math.abs(Math.sin(elapsed * Math.PI * 4)) * (h * 0.15);
            rotZ += Math.sin(elapsed * Math.PI * 4) * 0.16;
        }

        const tiltY = Math.max(-18, Math.min(18, relX * 15)) + inst.spinDeg;
        const tiltX = Math.max(-14, Math.min(14, -relY * 11));
        canvas.style.transform = `rotateX(${tiltX.toFixed(1)}deg) rotateY(${tiltY.toFixed(1)}deg)`;

        // Sombra en la base
        ctx.save();
        const shadowGrad = ctx.createRadialGradient(w * 0.5, h * 0.93, 2, w * 0.5, h * 0.93, w * 0.34);
        shadowGrad.addColorStop(0, 'rgba(15, 23, 42, 0.26)');
        shadowGrad.addColorStop(1, 'rgba(15, 23, 42, 0)');
        ctx.fillStyle = shadowGrad;
        ctx.beginPath();
        ctx.ellipse(w * 0.5, h * 0.93, w * 0.32, h * 0.055, 0, 0, Math.PI * 2);
        ctx.fill();
        ctx.restore();

        // Modelo 3D oficial en la pose correspondiente
        const poseImg = selectPoseImage(inst);
        if (poseImg && poseImg.complete && poseImg.naturalWidth > 0) {
            ctx.save();
            ctx.translate(w * 0.5, h * 0.52 + floatY);
            ctx.rotate(rotZ);
            ctx.scale(scaleX, scaleY);

            const drawSize = Math.min(w, h) * 0.92;
            ctx.drawImage(poseImg, -drawSize * 0.5, -drawSize * 0.5, drawSize, drawSize);
            ctx.restore();
        }

        if (inst.options.construction) {
            drawHardHatOverlay(ctx, w, h);
        }

        if (effectiveMood === 'sad') {
            ctx.save();
            ctx.fillStyle = '#60a5fa';
            const tearY = (h * 0.45) + ((t * 45) % (h * 0.18));
            ctx.beginPath();
            ctx.ellipse(w * 0.34, tearY, w * 0.025, h * 0.042, 0, 0, Math.PI * 2);
            ctx.ellipse(w * 0.66, tearY, w * 0.025, h * 0.042, 0, 0, Math.PI * 2);
            ctx.fill();
            ctx.restore();
        }

        if (effectiveMood === 'celebrate' || (inst.mood === 'happy' && elapsed < 1.0)) {
            const starCount = 4;
            for (let s = 0; s < starCount; s++) {
                const angle = t * 2.2 + (s * Math.PI * 2) / starCount;
                const sx = w * 0.5 + Math.cos(angle) * (w * 0.44);
                const sy = h * 0.16 + Math.sin(angle) * (h * 0.08) + floatY;
                const sr = Math.max(4, w * 0.048);
                drawStar(ctx, sx, sy, sr, t * 2.5 + s, s % 2 === 0 ? '#fbbf24' : '#38bdf8');
            }
        }
    }

    function createMascotInstance(container, type, options = {}) {
        if (!container || container.dataset.mascot3dReady === 'true') return null;
        container.dataset.mascot3dReady = 'true';

        container.querySelectorAll('svg, img').forEach((el) => {
            el.classList.add('mascot-svg-hidden');
        });

        const width = Math.max(container.clientWidth || 120, 56);
        const height = Math.max(container.clientHeight || 120, 56);
        const dpr = Math.min(window.devicePixelRatio || 1, 2);

        const canvas = document.createElement('canvas');
        canvas.className = 'mascot-3d-canvas';
        canvas.width = width * dpr;
        canvas.height = height * dpr;
        canvas.title = (type === 'mili' ? 'Mili 3D' : 'Lulú 3D') + ' — Mueve el cursor, arrastra o haz clic';

        const ctx = canvas.getContext('2d');
        ctx.scale(dpr, dpr);
        container.appendChild(canvas);

        const state = {
            type,
            container,
            canvas,
            ctx,
            width,
            height,
            dpr,
            options,
            mood: 'default',
            defaultHappy: options.defaultHappy !== false,
            phaseOffset: type === 'lulu' ? 1.6 : 0,
            reactionStart: 0,
            spinDeg: 0,
            userYaw: options.initialYaw || 0,
            userPitch: 0
        };

        let dragging = false;
        let moved = false;
        let lastX = 0, lastY = 0;

        canvas.addEventListener('pointerdown', (e) => {
            dragging = true;
            moved = false;
            lastX = e.clientX;
            lastY = e.clientY;
            canvas.setPointerCapture(e.pointerId);
        });

        canvas.addEventListener('pointermove', (e) => {
            if (!dragging) return;
            const dx = e.clientX - lastX;
            const dy = e.clientY - lastY;
            if (Math.abs(dx) > 2 || Math.abs(dy) > 2) moved = true;
            state.userYaw = Math.max(-1.2, Math.min(1.2, state.userYaw + dx * 0.012));
            state.userPitch = Math.max(-0.6, Math.min(0.6, state.userPitch + dy * 0.01));
            lastX = e.clientX;
            lastY = e.clientY;
        });

        canvas.addEventListener('pointerup', () => {
            dragging = false;
            if (!moved) {
                state.spinDeg = 360;
                state.mood = 'celebrate';
                state.reactionStart = performance.now();
                if (typeof options.onClick === 'function') {
                    options.onClick(state);
                }
                setTimeout(() => {
                    state.mood = 'default';
                }, 1000);
            }
        });

        const existingSvg = container.querySelector('svg');
        if (existingSvg) {
            const syncFromSvg = () => {
                let detected = 'neutral';
                const happyEye = existingSvg.querySelector('[id$="-eyes-happy"]');
                const sadEye = existingSvg.querySelector('[id$="-eyes-sad"]');
                if (existingSvg.classList.contains('celebrate')) {
                    detected = 'celebrate';
                } else if (existingSvg.classList.contains('shake') || (sadEye && sadEye.style.display !== 'none')) {
                    detected = 'sad';
                } else if (existingSvg.classList.contains('bounce') || (happyEye && happyEye.style.display !== 'none')) {
                    detected = 'happy';
                }
                if (detected !== state.mood) {
                    state.mood = detected;
                    state.reactionStart = performance.now();
                }
            };
            const observer = new MutationObserver(syncFromSvg);
            observer.observe(existingSvg, { attributes: true, subtree: true, attributeFilter: ['class', 'style'] });
        }

        if (window.ResizeObserver) {
            const ro = new ResizeObserver(() => {
                const nw = container.clientWidth;
                const nh = container.clientHeight;
                if (nw > 0 && nh > 0 && (nw !== state.width || nh !== state.height)) {
                    state.width = nw;
                    state.height = nh;
                    canvas.width = nw * state.dpr;
                    canvas.height = nh * state.dpr;
                    ctx.setTransform(state.dpr, 0, 0, state.dpr, 0, 0);
                }
            });
            ro.observe(container);
        }

        instances.push(state);
        return state;
    }

    /**
     * Crea a Mili (Izquierda) y Lulú (Derecha) con sus burbujas de diálogo.
     * En páginas de formulario las burbujas permanecen visibles y cada mascota dice algo
     * específico relacionado con el formulario y con el campo activo.
     */
    function createOppositeScreenCompanions(formType) {
        if (document.getElementById('handapp-dock-mili')) return;

        const isFormMode = Boolean(formType);
        const formDlg = formType ? FORM_DIALOGUES[formType] : null;

        // Determinar mensaje inicial en formularios (incluyendo ?error, ?registrado, ?logout en login)
        let initialMili = FRASES_GENERAL_MILI[0];
        let initialLulu = FRASES_GENERAL_LULU[0];

        if (formDlg) {
            const params = new URLSearchParams(window.location.search);
            if (formType === 'login' && (params.has('error') || document.querySelector('.alert.err.show'))) {
                initialMili = formDlg.error.mili;
                initialLulu = formDlg.error.lulu;
            } else if (formType === 'login' && params.has('registrado')) {
                initialMili = formDlg.registrado.mili;
                initialLulu = formDlg.registrado.lulu;
            } else if (formType === 'login' && params.has('recuperado')) {
                initialMili = formDlg.recuperado.mili;
                initialLulu = formDlg.recuperado.lulu;
            } else if (formType === 'login' && params.has('requerido')) {
                initialMili = formDlg.requerido.mili;
                initialLulu = formDlg.requerido.lulu;
            } else if (formType === 'login' && params.has('logout')) {
                initialMili = formDlg.logout.mili;
                initialLulu = formDlg.logout.lulu;
            } else {
                initialMili = formDlg.initial.mili;
                initialLulu = formDlg.initial.lulu;
            }
        }

        const configs = [
            {
                id: 'handapp-dock-mili',
                type: 'mili',
                name: 'Mili',
                speakerLabel: 'Mili te aconseja:',
                sideClass: 'handapp-side-dock--left',
                phrases: FRASES_GENERAL_MILI,
                initialText: initialMili,
                initialYaw: 0.25
            },
            {
                id: 'handapp-dock-lulu',
                type: 'lulu',
                name: 'Lulú',
                speakerLabel: 'Lulú te recuerda:',
                sideClass: 'handapp-side-dock--right',
                phrases: FRASES_GENERAL_LULU,
                initialText: initialLulu,
                initialYaw: -0.25
            }
        ];

        configs.forEach((cfg, idx) => {
            const dock = document.createElement('aside');
            dock.id = cfg.id;
            dock.className = `handapp-side-dock ${cfg.sideClass}` + (isFormMode ? ' is-form-mode' : '');
            dock.setAttribute('aria-label', `${cfg.name} compañero 3D`);

            const bubble = document.createElement('div');
            bubble.className = 'handapp-side-dock__bubble';

            const speakerEl = document.createElement('strong');
            speakerEl.className = 'handapp-side-dock__speaker';
            speakerEl.textContent = cfg.speakerLabel;

            const textEl = document.createElement('span');
            textEl.textContent = cfg.initialText;

            bubble.appendChild(speakerEl);
            bubble.appendChild(textEl);

            const stage = document.createElement('div');
            stage.className = 'handapp-side-dock__stage';

            const bar = document.createElement('div');
            bar.className = 'handapp-side-dock__bar';

            const nameSpan = document.createElement('span');
            nameSpan.className = 'handapp-side-dock__name';
            nameSpan.textContent = cfg.name;

            const minBtn = document.createElement('button');
            minBtn.type = 'button';
            minBtn.className = 'handapp-side-dock__toggle';
            minBtn.title = 'Minimizar / Mostrar mascota 3D';
            minBtn.textContent = '–';

            minBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                const minimized = dock.classList.toggle('is-minimized');
                minBtn.textContent = minimized ? '+' : '–';
            });

            bar.appendChild(nameSpan);
            bar.appendChild(minBtn);
            dock.appendChild(bubble);
            dock.appendChild(stage);
            dock.appendChild(bar);
            document.body.appendChild(dock);

            let phraseIdx = 0;
            let bubbleTimer = null;

            const setSay = (msg, persistent = isFormMode) => {
                textEl.textContent = msg;
                bubble.classList.add('show');
                if (bubbleTimer) clearTimeout(bubbleTimer);
                if (!persistent) {
                    bubbleTimer = setTimeout(() => bubble.classList.remove('show'), 4800);
                }
            };

            const inst = createMascotInstance(stage, cfg.type, {
                defaultHappy: true,
                initialYaw: cfg.initialYaw,
                onClick: () => {
                    if (isFormMode && formDlg) {
                        setSay(cfg.type === 'mili' ? formDlg.initial.mili : formDlg.initial.lulu, true);
                    } else {
                        phraseIdx = (phraseIdx + 1) % cfg.phrases.length;
                        setSay(cfg.phrases[phraseIdx], false);
                    }
                }
            });

            dockControllers[cfg.type] = { setSay, inst };

            // Mostrar burbuja al cargar
            if (isFormMode) {
                setSay(cfg.initialText, true);
            } else {
                setTimeout(() => setSay(cfg.initialText, false), 800 + idx * 650);
            }
        });

        // En formularios: escuchar focus/change en los campos para que Mili y Lulú hablen sobre cada campo
        if (isFormMode && formDlg && formDlg.fields) {
            const inputs = document.querySelectorAll('input, select, textarea');
            inputs.forEach((inp) => {
                const fieldKey = inp.name || inp.id;
                const handleFieldFocus = () => {
                    const pair = formDlg.fields[fieldKey] || formDlg.fields[inp.id];
                    if (!pair) return;
                    if (dockControllers.mili) {
                        dockControllers.mili.setSay(pair.mili, true);
                        dockControllers.mili.inst.mood = 'happy';
                        dockControllers.mili.inst.reactionStart = performance.now();
                    }
                    if (dockControllers.lulu) {
                        dockControllers.lulu.setSay(pair.lulu, true);
                        dockControllers.lulu.inst.mood = 'happy';
                        dockControllers.lulu.inst.reactionStart = performance.now();
                    }
                };
                inp.addEventListener('focus', handleFieldFocus);
                if (inp.type === 'checkbox') {
                    inp.addEventListener('change', handleFieldFocus);
                }
            });
        }
    }

    function animate(now) {
        requestAnimationFrame(animate);
        for (let i = 0; i < instances.length; i++) {
            const inst = instances[i];
            inst.userYaw *= 0.97;
            inst.userPitch *= 0.95;
            inst.spinDeg *= 0.86;
            if (Math.abs(inst.spinDeg) < 0.4) inst.spinDeg = 0;
            renderInstance(inst, now);
        }
    }

    function mountAllMascots() {
        injectStyles();

        const formType = detectFormType();
        const loggedIn = isUserAuthenticated();

        // Mostrar mascotas si el usuario está logueado O si está en un formulario
        if (!loggedIn && !formType) {
            document.querySelectorAll('.mascot-duo, .mascot-note, .handapp-side-dock').forEach((el) => {
                el.style.display = 'none';
            });
            return;
        }

        const isQuizPage = !!document.getElementById('lesson-card');

        // 1. Contenedores en quiz (.mascot-bar) o banners (.mascot-duo)
        document.querySelectorAll('.seal-wrap').forEach((el) => {
            createMascotInstance(el, 'mili', { defaultHappy: !isQuizPage, initialYaw: 0.18 });
        });
        document.querySelectorAll('.duck-wrap').forEach((el) => {
            createMascotInstance(el, 'lulu', { defaultHappy: !isQuizPage, initialYaw: -0.18 });
        });

        // 2. Modo construcción en leccion-pendiente.html
        const constructionWraps = document.querySelectorAll('.mascots-construccion .mascot-wrap');
        if (constructionWraps.length >= 2) {
            createMascotInstance(constructionWraps[0], 'mili', { construction: true, defaultHappy: true });
            createMascotInstance(constructionWraps[1], 'lulu', { construction: true, defaultHappy: true });
        }

        // 3. Compañeros grandes en lados opuestos de la pantalla (con diálogos de formulario cuando aplica)
        createOppositeScreenCompanions(formType);
    }

    function init() {
        preloadSprites();
        mountAllMascots();
        requestAnimationFrame(animate);
    }

    window.HandAppMascots3D = {
        setMood(mood) {
            instances.forEach((inst) => {
                inst.mood = mood;
                inst.reactionStart = performance.now();
            });
        },
        onGoogleStep(stepName, email) {
            const recDlg = FORM_DIALOGUES.recuperar && FORM_DIALOGUES.recuperar.steps
                ? FORM_DIALOGUES.recuperar.steps[stepName]
                : null;
            if (!recDlg) return;
            const miliMsg = email && stepName === 'verificado'
                ? `¡Identidad verificada como ${email}! ✅ Entra directo con Google o define tu nueva contraseña.`
                : recDlg.mili;
            if (dockControllers.mili) {
                dockControllers.mili.setSay(miliMsg, true);
                dockControllers.mili.inst.mood = stepName === 'verificado' ? 'celebrate' : 'happy';
                dockControllers.mili.inst.reactionStart = performance.now();
            }
            if (dockControllers.lulu) {
                dockControllers.lulu.setSay(recDlg.lulu, true);
                dockControllers.lulu.inst.mood = stepName === 'verificado' ? 'celebrate' : 'happy';
                dockControllers.lulu.inst.reactionStart = performance.now();
            }
        },
        onSocialRegister(provider) {
            const socDlg = FORM_DIALOGUES.register && FORM_DIALOGUES.register.social
                ? FORM_DIALOGUES.register.social[provider]
                : null;
            if (!socDlg) return;
            if (dockControllers.mili) {
                dockControllers.mili.setSay(socDlg.mili, true);
                dockControllers.mili.inst.mood = 'celebrate';
                dockControllers.mili.inst.reactionStart = performance.now();
            }
            if (dockControllers.lulu) {
                dockControllers.lulu.setSay(socDlg.lulu, true);
                dockControllers.lulu.inst.mood = 'celebrate';
                dockControllers.lulu.inst.reactionStart = performance.now();
            }
        },
        refresh: mountAllMascots
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
