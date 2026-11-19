# 📋 Planificación Ágil (Scrum): Sprints, Historias de Usuario e Issues — HandApp

Este documento contiene la planificación completa de **HandApp** lista para copiar y pegar en **GitHub Issues**, **Jira**, **Trello** o **Azure DevOps**, organizada por **Épicas**, **4 Sprints**, **Historias de Usuario (HU)** con criterios de aceptación Gherkin (`Dado / Cuando / Entonces`) y **Tareas Técnicas (Issues)**.

---

## 🏷️ Etiquetas (Labels) Recomendadas para GitHub / Jira

* `epic:autenticacion` — Seguridad, Login, Registro, Roles y OAuth (Google/Apple).
* `epic:aprendizaje-lsc` — Abecedario, Biblioteca de Señas, Lecciones y Quizes.
* `epic:ia-traductor` — Cámara en vivo, MediaPipe, Django y modelo `modelo_lsc.pkl`.
* `epic:paneles-gestion` — Panel Administrador (`@handapp.com`), Panel Editor (`@editorhand.com`) y Reportes.
* `epic:ux-mascotas` — Mascotas 3D (Mili y Lulú), modo claro/oscuro y video de pantalla de carga.
* `priority:alta` / `priority:media` / `priority:baja`
* `backend:spring-django` / `frontend:thymeleaf-js` / `database:mysql` / `ia:mediapipe`

---

## 🗓️ SPRINT 1: Arquitectura Base, Base de Datos MySQL y Autenticación por Roles
**Objetivo del Sprint:** Establecer la base de datos `handapp` en MySQL, el sistema de autenticación encriptado con BCrypt, el registro/recuperación con Google y Apple, y la asignación automática de roles por dominio (`@handapp.com` y `@editorhand.com`).

### HU-01: Registro de usuarios con asignación de rol por dominio y acceso social (Google / Apple)
* **Como:** Nuevo usuario de HandApp
* **Quiero:** Registrarme con mi correo y contraseña o mediante mi cuenta de Google / Apple
* **Para:** Crear mi perfil en MySQL y obtener automáticamente el rol correspondiente a mi correo (`ESTUDIANTE`, `EDITOR` o `ADMINISTRADOR`).
* **Puntos de Historia:** `8 SP` | **Prioridad:** `Alta` | **Etiquetas:** `epic:autenticacion`, `database:mysql`
* **Criterios de Aceptación:**
  1. **Dado** que un usuario ingresa al formulario `/registro`, **cuando** se registra con un correo terminado en `@handapp.com`, **entonces** el sistema le asigna el rol `ADMINISTRADOR`, encripta su contraseña con BCrypt y lo guarda en la tabla `usuarios` de MySQL.
  2. **Dado** que un usuario se registra con un correo terminado en `@editorhand.com`, **cuando** envía el formulario, **entonces** el sistema le asigna automáticamente el rol `EDITOR`.
  3. **Dado** que un usuario se registra con cualquier otro dominio (ej. `@gmail.com`, `@icloud.com`), **cuando** completa el registro o usa los botones de **Google / Apple**, **entonces** se registra con el rol `ESTUDIANTE`.
* **Issues / Tareas Técnicas del Sprint:**
  * `[ISSUE-101]` Crear esquema MySQL `handapp` con las tablas `usuarios`, `lecciones` y `progreso_estudiante` (`database/HandApp.sql`).
  * `[ISSUE-102]` Implementar encriptación BCrypt compatible entre Spring Security (`UsuarioService.java`) y Django (`bcrypt_pure.py`).
  * `[ISSUE-103]` Desarrollar validación automática de dominios `@handapp.com` (Admin) y `@editorhand.com` (Editor) en backend y frontend (`register.html`).
  * `[ISSUE-104]` Implementar selector de registro rápido con cuentas de **Google** y **Apple** (`/auth/google` y `/auth/apple`).

---

### HU-02: Inicio de sesión y redirección inteligente según rol
* **Como:** Usuario registrado (`Estudiante`, `Editor` o `Administrador`)
* **Quiero:** Iniciar sesión con mis credenciales en `/login`
* **Para:** Ser redirigido automáticamente a mi entorno de trabajo correspondiente (`/`, `/editor/panel` o `/admin/panel`).
* **Puntos de Historia:** `5 SP` | **Prioridad:** `Alta` | **Etiquetas:** `epic:autenticacion`, `backend:spring-django`
* **Criterios de Aceptación:**
  1. **Dado** un usuario con rol `ADMINISTRADOR` (`@handapp.com`), **cuando** inicia sesión con credenciales válidas, **entonces** es redirigido directamente a `/admin/panel`.
  2. **Dado** un usuario con rol `EDITOR` (`@editorhand.com`), **cuando** inicia sesión, **entonces** es redirigido directamente a `/editor/panel`.
  3. **Dado** un usuario con rol `ESTUDIANTE`, **cuando** inicia sesión, **entonces** ingresa a la pantalla principal `/` con todos los apartados desbloqueados.
* **Issues / Tareas Técnicas:**
  * `[ISSUE-105]` Configurar autenticación y manejo de sesiones en `AuthController.java`, `SecurityConfig.java` y `handapp_django/views.py`.
  * `[ISSUE-106]` Implementar redirección automática por rol tras login exitoso.

---

### HU-03: Recuperación de cuenta mediante verificación con Google
* **Como:** Usuario que olvidó su contraseña
* **Quiero:** Verificar mi identidad iniciando sesión con mi cuenta de Google en `/recuperar-cuenta`
* **Para:** Restablecer mi contraseña en MySQL sin perder mi progreso.
* **Puntos de Historia:** `5 SP` | **Prioridad:** `Media` | **Etiquetas:** `epic:autenticacion`, `frontend:thymeleaf-js`
* **Criterios de Aceptación:**
  1. **Dado** que el usuario entra a `/recuperar-cuenta`, **cuando** selecciona o verifica su correo mediante el botón de Google y define una nueva contraseña (mínimo 8 caracteres), **entonces** su contraseña se actualiza en MySQL con hash BCrypt y es redirigido a `/login?recuperado=true`.
* **Issues / Tareas Técnicas:**
  * `[ISSUE-107]` Diseñar vista `recuperar-cuenta.html` en dos pasos (verificación con Google + nueva contraseña).
  * `[ISSUE-108]` Implementar endpoint POST `/recuperar-cuenta` con actualización de hash en MySQL.

---

### HU-04: Bloqueo de apartados y barra de búsqueda para visitantes no autenticados
* **Como:** Product Owner / Administrador de Seguridad
* **Quiero:** Bloquear el acceso a Abecedario, Biblioteca, Lecciones, Juegos, Traductor y la Barra de Búsqueda si el usuario no ha iniciado sesión
* **Para:** Garantizar que solo usuarios registrados y autenticados consuman los módulos interactivos.
* **Puntos de Historia:** `5 SP` | **Prioridad:** `Alta` | **Etiquetas:** `epic:autenticacion`, `frontend:thymeleaf-js`
* **Criterios de Aceptación:**
  1. **Dado** un visitante sin sesión activa (`window.HANDAPP_AUTHENTICATED === false`), **cuando** intenta abrir Abecedario, Lecciones, Juegos, Traductor o hacer clic en la barra de búsqueda, **entonces** se muestra un modal solicitando iniciar sesión o registrarse.
  2. **Dado** un visitante no autenticado que escribe directamente la URL `/abecedario`, `/traductor` o `/biblioteca`, **cuando** el servidor procesa la petición, **entonces** lo redirige a `/login?requerido=1`.
* **Issues / Tareas Técnicas:**
  * `[ISSUE-109]` Crear guardia de rutas en cliente (`proteccion_y_carga.js`) y bloqueo de `#searchInput` / `#searchToggle`.
  * `[ISSUE-110]` Añadir validación de sesión en servidor para rutas protegidas en Django y Spring Boot.

---

## 🗓️ SPRINT 2: Módulos de Aprendizaje LSC (Abecedario, Biblioteca y Lecciones Interactivas)
**Objetivo del Sprint:** Construir la experiencia educativa de Lengua de Señas Colombiana (LSC) con el abecedario visual de 27 letras, biblioteca filtrable por categorías y quizes interactivos con guardado de puntaje en MySQL.

### HU-05: Exploración del Abecedario Interactivo LSC (27 letras)
* **Como:** Estudiante autenticado
* **Quiero:** Visualizar las 27 letras del abecedario en LSC con tarjetas animadas y vista ampliada en modal
* **Para:** Aprender la configuración manual de cada letra de la A a la Z (incluyendo la Ñ).
* **Puntos de Historia:** `5 SP` | **Prioridad:** `Alta` | **Etiquetas:** `epic:aprendizaje-lsc`, `frontend:thymeleaf-js`
* **Criterios de Aceptación:**
  1. **Dado** un estudiante en `/abecedario`, **cuando** hace clic en el botón "Ver letra" de cualquier tarjeta (`#01 A` hasta `#27 Z`), **entonces** se abre un modal con la fotografía de la seña desde `/IMG/abecedario/`.
* **Issues / Tareas Técnicas:**
  * `[ISSUE-201]` Maquetar cuadrícula de 27 letras y modal interactivo en `abecedario.html` y `CSS/abecedario.css`.
  * `[ISSUE-202]` Organizar banco de imágenes de señas en `static/IMG/abecedario/`.

---

### HU-06: Biblioteca de Señas con buscador en tiempo real y filtros por categoría
* **Como:** Estudiante autenticado
* **Quiero:** Buscar señas por palabra clave o filtrar por categoría (`Saludos`, `Emociones`, `Números`, `Familia`, `Colores`)
* **Para:** Encontrar rápidamente el video demostrativo de la seña que necesito practicar.
* **Puntos de Historia:** `5 SP` | **Prioridad:** `Media` | **Etiquetas:** `epic:aprendizaje-lsc`, `frontend:thymeleaf-js`
* **Criterios de Aceptación:**
  1. **Dado** que el usuario usa la barra de búsqueda del encabezado o entra a `/biblioteca`, **cuando** escribe un término (ej. `"hola"`, `"gracias"`) o selecciona un filtro de categoría, **entonces** la vista filtra dinámicamente las tarjetas de video y muestra el contador de resultados.
* **Issues / Tareas Técnicas:**
  * `[ISSUE-203]` Desarrollar vista `biblioteca_de_señas.html` y estilos `CSS/biblioteca_de_señas.css`.
  * `[ISSUE-204]` Conectar la barra de búsqueda global (`app.js`) con redirección a `/biblioteca?buscar=<termino>`.

---

### HU-07: Lecciones interactivas tipo Quiz con guardado de progreso y puntaje en MySQL
* **Como:** Estudiante autenticado
* **Quiero:** Realizar la lección interactiva de 15 preguntas en video (`leccion-saludos.html`) con retroalimentación inmediata y datos curiosos
* **Para:** Evaluar mi aprendizaje en LSC y guardar mi porcentaje de progreso y puntaje final en la base de datos.
* **Puntos de Historia:** `8 SP` | **Prioridad:** `Alta` | **Etiquetas:** `epic:aprendizaje-lsc`, `database:mysql`
* **Criterios de Aceptación:**
  1. **Dado** un estudiante en `/estudiante/lecciones/1`, **cuando** selecciona una opción de respuesta, **entonces** el sistema indica si es correcta o incorrecta, anima a Mili y Lulú y actualiza la barra de progreso.
  2. **Dado** que el estudiante finaliza las 15 preguntas, **cuando** se muestra el trofeo y el puntaje obtenido, **entonces** se envía una petición POST a `/estudiante/lecciones/{id}/completar` que registra `completado = 1`, `puntaje` y `fecha_completado` en la tabla `progreso_estudiante` de MySQL.
* **Issues / Tareas Técnicas:**
  * `[ISSUE-205]` Construir motor de quiz de 15 preguntas, pantallas de transición con datos curiosos y pantalla final en `estudiante/leccion-saludos.html`.
  * `[ISSUE-206]` Implementar persistencia de progreso parcial y final en `EstudianteController.java`, `ProgresoEstudianteService.java` y `handapp_django/views.py`.

---

## 🗓️ SPRINT 3: Integración de Django, Inteligencia Artificial LSC, Mascotas 3D y Video de Carga
**Objetivo del Sprint:** Integrar Django con MediaPipe y scikit-learn para el traductor de señas por cámara en tiempo real, incorporar los modelos 3D interactivos de las mascotas Mili y Lulú con diálogos contextuales en formularios, y añadir la pantalla de carga en video.

### HU-08: Traductor de Lengua de Señas en tiempo real con Cámara Web, MediaPipe y Django
* **Como:** Estudiante autenticado
* **Quiero:** Activar mi cámara en `/traductor` para que la Inteligencia Artificial reconozca mis señas y forme palabras con voz (Text-to-Speech)
* **Para:** Practicar la Lengua de Señas Colombiana en vivo y traducir mis gestos a texto y audio.
* **Puntos de Historia:** `13 SP` | **Prioridad:** `Alta` | **Etiquetas:** `epic:ia-traductor`, `ia:mediapipe`, `backend:spring-django`
* **Criterios de Aceptación:**
  1. **Dado** que el servidor Django está activo, **cuando** el usuario entra a `/traductor` y activa la cámara, **entonces** el endpoint `/video` transmite el stream MJPEG con los 21 puntos de la mano detectados por `hand_landmarker.task`.
  2. **Dado** que el usuario mantiene una seña frente a la cámara durante 1.5 segundos (barra de confirmación), **cuando** el endpoint `/letra` devuelve una predicción de `modelo_lsc.pkl` con confianza válida, **entonces** la letra se agrega automáticamente al constructor de palabras y puede escucharse con el sintetizador de voz.
* **Issues / Tareas Técnicas:**
  * `[ISSUE-301]` Integrar paquete Django (`src/main/PYTHON/handapp_django/`) y conector automático `PythonProcessManager.java`.
  * `[ISSUE-302]` Implementar detector `lsc_camera.py` con MediaPipe HandLandmarker y clasificador RandomForest (`ia_lsc/modelos/modelo_lsc.pkl`).
  * `[ISSUE-303]` Conectar interfaz `traductor.html` con streaming `/video`, polling `/letra`, barra de retención de 1.5s, constructor de palabras y Web Speech API.

---

### HU-09: Mascotas 3D interactivas (Mili y Lulú) con seguimiento de cursor y diálogos en formularios
* **Como:** Usuario de HandApp
* **Quiero:** Ver a las mascotas 3D **Mili** (foca azul, a la izquierda) y **Lulú** (pato amarillo, a la derecha) acompañándome cuando inicio sesión y guiándome con frases contextuales en todos los formularios
* **Para:** Tener una experiencia visual amigable, interactiva y guiada paso a paso.
* **Puntos de Historia:** `8 SP` | **Prioridad:** `Media` | **Etiquetas:** `epic:ux-mascotas`, `frontend:thymeleaf-js`
* **Criterios de Aceptación:**
  1. **Dado** un usuario en cualquier formulario (`login.html`, `register.html`, `recuperar-cuenta.html`, `admin/usuario-form.html`, `editor/leccion-form.html`) o con sesión iniciada, **cuando** mueve el cursor o hace clic en un campo (`correo`, `contraseña`, `nombre`, `rol`, etc.), **entonces** Mili y Lulú rotan en 3D con efecto parallax, cambian su pose (`front`, `left`, `right`) y muestran burbujas de diálogo con consejos relacionados con ese campo.
* **Issues / Tareas Técnicas:**
  * `[ISSUE-304]` Integrar los 6 renders 3D oficiales en `static/IMG/mascotas3d/` (`mili_front/left/right.png` y `lulu_front/left/right.png`).
  * `[ISSUE-305]` Desarrollar motor 3D de inclinación parallax, partículas y diálogos reactivos por formulario e input en `static/JS/mascotas3d.js`.

---

### HU-10: Pantalla de carga en video al navegar entre apartados y modo Claro/Oscuro
* **Como:** Usuario autenticado
* **Quiero:** Ver una pantalla de transición con el video animado oficial de HandApp al entrar a cualquier apartado y poder alternar entre tema claro y oscuro
* **Para:** Disfrutar de transiciones fluidas y adaptar la interfaz a mi preferencia visual.
* **Puntos de Historia:** `5 SP` | **Prioridad:** `Media` | **Etiquetas:** `epic:ux-mascotas`, `frontend:thymeleaf-js`
* **Criterios de Aceptación:**
  1. **Dado** un usuario autenticado, **cuando** hace clic para entrar a Abecedario, Biblioteca, Lecciones, Traductor o Paneles, **entonces** se reproduce el overlay de carga con `IMG/pantalla_carga.mp4` antes de cambiar de vista.
  2. **Dado** cualquier página de HandApp, **cuando** el usuario pulsa el botón flotante de tema, **entonces** la interfaz alterna entre modo claro y oscuro persistiendo la elección en `localStorage`.
* **Issues / Tareas Técnicas:**
  * `[ISSUE-306]` Implementar reproductor de transición con `pantalla_carga.mp4` en `proteccion_y_carga.js` y soporte HTTP `Range (206)` en `handapp_django/urls.py`.
  * `[ISSUE-307]` Desarrollar sistema de temas claro/oscuro en `CSS/tema.css` y `JS/tema.js`.

---

## 🗓️ SPRINT 4: Paneles de Administración (`@handapp.com`), Edición (`@editorhand.com`) y Reportes
**Objetivo del Sprint:** Proveer herramientas completas de gestión CRUD sobre MySQL y reportes estadísticos para los roles de Administrador y Editor, además de la limpieza y organización general del repositorio.

### HU-11: Panel de Administrador (`@handapp.com`) — Gestión CRUD de usuarios y exportación de reportes
* **Como:** Administrador (`@handapp.com`)
* **Quiero:** Gestionar usuarios (crear, editar, filtrar por rol y eliminar) en `/admin/panel` y consultar/exportar reportes de progreso en `/admin/reportes`
* **Para:** Administrar la comunidad de HandApp y auditar el avance académico de los estudiantes.
* **Puntos de Historia:** `8 SP` | **Prioridad:** `Alta` | **Etiquetas:** `epic:paneles-gestion`, `database:mysql`
* **Criterios de Aceptación:**
  1. **Dado** un administrador en `/admin/panel`, **cuando** crea o edita un usuario en `/admin/usuarios/nuevo` o `/editar`, **entonces** el sistema valida que el dominio del correo coincida con el rol seleccionado (`@handapp.com` para Admin, `@editorhand.com` para Editor) y guarda los cambios en MySQL.
  2. **Dado** un administrador en `/admin/reportes`, **cuando** filtra por nombre/correo o rol y pulsa exportar, **entonces** descarga el reporte con las lecciones completadas y promedio de puntaje de cada estudiante.
* **Issues / Tareas Técnicas:**
  * `[ISSUE-401]` Desarrollar vistas `admin/panel.html`, `admin/usuario-form.html` y `admin/reportes.html`.
  * `[ISSUE-402]` Implementar CRUD de usuarios y generador de reportes exportables en `AdminController.java`, `ReporteExportService.java` y `handapp_django/views.py`.

---

### HU-12: Panel de Editor (`@editorhand.com`) — Gestión CRUD de lecciones y métricas de desempeño
* **Como:** Editor de contenido (`@editorhand.com`)
* **Quiero:** Crear, editar, publicar/archivar y eliminar lecciones en `/editor/panel` y ver estadísticas de completado por lección en `/editor/reportes`
* **Para:** Mantener actualizado el catálogo de lecciones LSC y medir qué lecciones tienen mejor desempeño.
* **Puntos de Historia:** `8 SP` | **Prioridad:** `Alta` | **Etiquetas:** `epic:paneles-gestion`, `database:mysql`
* **Criterios de Aceptación:**
  1. **Dado** un editor en `/editor/panel`, **cuando** crea o modifica una lección indicando título, descripción, orden, plantilla `vista` y estado (`Publicada` o `Borrador`), **entonces** la tabla `lecciones` en MySQL se actualiza inmediatamente y las lecciones publicadas aparecen en el menú principal.
  2. **Dado** un editor en `/editor/reportes`, **cuando** abre la vista de analíticas, **entonces** visualiza gráficos circulares (donuts) con el porcentaje de completado, intentos totales y puntaje promedio de cada lección.
* **Issues / Tareas Técnicas:**
  * `[ISSUE-403]` Desarrollar vistas `editor/panel.html`, `editor/leccion-form.html` y `editor/reportes.html`.
  * `[ISSUE-404]` Implementar CRUD de lecciones y cálculo de métricas en `EditorController.java`, `LeccionService.java` y `handapp_django/views.py`.
  * `[ISSUE-405]` Estandarizar y limpiar la estructura de carpetas del proyecto (`database/`, `docs/`, `ia_lsc/`, `static/CSS/`, `static/IMG/abecedario/`, `templates/`).

---

## 📊 Resumen del Product Backlog por Sprints

| Sprint | Historia de Usuario | Rol | Prioridad | Estimación | Estado |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Sprint 1** | **HU-01**: Registro con validación de dominio y Google/Apple | Usuario | Alta | 8 SP | ✅ Completado |
| **Sprint 1** | **HU-02**: Inicio de sesión y redirección por rol | Usuario | Alta | 5 SP | ✅ Completado |
| **Sprint 1** | **HU-03**: Recuperación de cuenta con Google | Usuario | Media | 5 SP | ✅ Completado |
| **Sprint 1** | **HU-04**: Bloqueo de apartados y buscador sin sesión | Sistema | Alta | 5 SP | ✅ Completado |
| **Sprint 2** | **HU-05**: Abecedario interactivo LSC (27 letras) | Estudiante | Alta | 5 SP | ✅ Completado |
| **Sprint 2** | **HU-06**: Biblioteca de señas con buscador y filtros | Estudiante | Media | 5 SP | ✅ Completado |
| **Sprint 2** | **HU-07**: Lecciones tipo Quiz con puntaje en MySQL | Estudiante | Alta | 8 SP | ✅ Completado |
| **Sprint 3** | **HU-08**: Traductor LSC con cámara IA (Django + MediaPipe) | Estudiante | Alta | 13 SP | ✅ Completado |
| **Sprint 3** | **HU-09**: Mascotas 3D (Mili y Lulú) interactivas en formularios | Usuario | Media | 8 SP | ✅ Completado |
| **Sprint 3** | **HU-10**: Video de pantalla de carga y modo Claro/Oscuro | Usuario | Media | 5 SP | ✅ Completado |
| **Sprint 4** | **HU-11**: Panel Administrador (`@handapp.com`) y reportes | Administrador | Alta | 8 SP | ✅ Completado |
| **Sprint 4** | **HU-12**: Panel Editor (`@editorhand.com`) y analíticas | Editor | Alta | 8 SP | ✅ Completado |
| **TOTAL** | **12 Historias de Usuario / 24 Issues Técnicos** | — | — | **83 SP** | **100%** |


---

## 🗂️ Fichas Detalladas de Historias de Usuario (Formato Oficial SENA - ADSO 228118)

### 📄 HU-01: Registro de usuario con asignación de rol por dominio y Google/Apple
| Campo | Descripción |
| :--- | :--- |
| **ID Historia** | **HU-01** |
| **Nombre** | Registro de usuario con asignación automática de rol por dominio y acceso con Google/Apple |
| **Sprint** | Sprint 1 |
| **Prioridad** | Alta |
| **Puntos de Historia** | 8 SP |
| **Como (Rol)** | Nuevo usuario de la plataforma HandApp |
| **Quiero (Acción)** | Registrarme ingresando mi nombre, correo y contraseña o usando los botones de **Google / Apple** en `/registro` |
| **Para (Beneficio)** | Crear mi cuenta en la base de datos MySQL y recibir automáticamente los permisos de mi rol (`ADMINISTRADOR`, `EDITOR` o `ESTUDIANTE`). |
| **Precondiciones** | El servidor MySQL (`handapp`) debe estar activo y el correo electrónico no debe estar registrado previamente. |
| **Postcondiciones** | Se crea el registro en la tabla `usuarios` de MySQL con la contraseña encriptada en BCrypt (`$2a$10$`). |

### 📄 HU-02: Inicio de sesión con BCrypt y redirección inteligente por rol
| Campo | Descripción |
| :--- | :--- |
| **ID Historia** | **HU-02** |
| **Nombre** | Inicio de sesión seguro y redirección automática según el rol del usuario |
| **Sprint** | Sprint 1 |
| **Prioridad** | Alta |
| **Puntos de Historia** | 5 SP |
| **Como (Rol)** | Usuario registrado (`Estudiante`, `Editor` o `Administrador`) |
| **Quiero (Acción)** | Iniciar sesión con mi correo y contraseña en `/login` |
| **Para (Beneficio)** | Acceder de forma directa a mi entorno de trabajo según mi rol en la plataforma (`/admin/panel`, `/editor/panel` o `/`). |
| **Precondiciones** | El usuario debe existir previamente en la tabla `usuarios` de MySQL. |
| **Postcondiciones** | Se inicia una sesión autenticada y el usuario es dirigido a su panel o vista principal. |

### 📄 HU-03: Recuperación de cuenta mediante verificación con Google
| Campo | Descripción |
| :--- | :--- |
| **ID Historia** | **HU-03** |
| **Nombre** | Recuperación de cuenta y restablecimiento de contraseña con Google |
| **Sprint** | Sprint 1 |
| **Prioridad** | Media |
| **Puntos de Historia** | 5 SP |
| **Como (Rol)** | Usuario registrado que olvidó su contraseña |
| **Quiero (Acción)** | Verificar mi identidad iniciando sesión con mi cuenta de Google en `/recuperar-cuenta` |
| **Para (Beneficio)** | Definir una nueva contraseña en MySQL y recuperar el acceso a mi cuenta sin perder mi progreso. |
| **Precondiciones** | El correo seleccionado con Google debe existir en la tabla `usuarios` de MySQL. |
| **Postcondiciones** | El campo `contrasena` del usuario se actualiza en MySQL con un nuevo hash BCrypt. |

### 📄 HU-04: Bloqueo de apartados y barra de búsqueda sin sesión activa
| Campo | Descripción |
| :--- | :--- |
| **ID Historia** | **HU-04** |
| **Nombre** | Restricción de acceso a apartados y barra de búsqueda para visitantes no autenticados |
| **Sprint** | Sprint 1 |
| **Prioridad** | Alta |
| **Puntos de Historia** | 5 SP |
| **Como (Rol)** | Administrador de seguridad del sistema |
| **Quiero (Acción)** | Que los apartados Abecedario, Biblioteca, Lecciones, Juegos, Traductor y la Barra de Búsqueda no funcionen sin haber iniciado sesión previamente |
| **Para (Beneficio)** | Garantizar la trazabilidad del progreso en MySQL y proteger los módulos interactivos. |
| **Precondiciones** | El visitante accede a `/` sin una sesión activa (`window.HANDAPP_AUTHENTICATED === false`). |
| **Postcondiciones** | Cualquier intento de uso muestra el modal de bloqueo o redirige a `/login?requerido=1`. |

### 📄 HU-05: Exploración del Abecedario Interactivo LSC (27 letras)
| Campo | Descripción |
| :--- | :--- |
| **ID Historia** | **HU-05** |
| **Nombre** | Consulta interactiva de las 27 letras del abecedario en Lengua de Señas Colombiana |
| **Sprint** | Sprint 2 |
| **Prioridad** | Alta |
| **Puntos de Historia** | 5 SP |
| **Como (Rol)** | Estudiante autenticado |
| **Quiero (Acción)** | Visualizar las 27 letras del abecedario LSC (A–Z incluyendo la Ñ) en tarjetas interactivas y ampliarlas en un modal |
| **Para (Beneficio)** | Aprender con claridad la posición de los dedos y la mano correspondiente a cada letra. |
| **Precondiciones** | El usuario debe haber iniciado sesión en HandApp. |
| **Postcondiciones** | El estudiante visualiza las imágenes de cada seña desde `/IMG/abecedario/`. |

### 📄 HU-06: Biblioteca de Señas con buscador y filtros por categoría
| Campo | Descripción |
| :--- | :--- |
| **ID Historia** | **HU-06** |
| **Nombre** | Búsqueda y filtrado por categorías en la Biblioteca de Señas en video |
| **Sprint** | Sprint 2 |
| **Prioridad** | Media |
| **Puntos de Historia** | 5 SP |
| **Como (Rol)** | Estudiante autenticado |
| **Quiero (Acción)** | Buscar señas por palabra clave o filtrar por categoría (`Todos`, `Saludos`, `Emociones`, `Números`, `Familia`, `Colores`) en `/biblioteca` |
| **Para (Beneficio)** | Reproducir videos demostrativos de vocabulario cotidiano en LSC. |
| **Precondiciones** | El estudiante debe tener sesión iniciada. |
| **Postcondiciones** | La lista de tarjetas en video se actualiza dinámicamente en pantalla sin recargar la página. |

### 📄 HU-07: Lecciones interactivas tipo Quiz con guardado de puntaje en MySQL
| Campo | Descripción |
| :--- | :--- |
| **ID Historia** | **HU-07** |
| **Nombre** | Resolución de lecciones interactivas tipo Quiz de 15 preguntas con registro de puntaje |
| **Sprint** | Sprint 2 |
| **Prioridad** | Alta |
| **Puntos de Historia** | 8 SP |
| **Como (Rol)** | Estudiante autenticado |
| **Quiero (Acción)** | Realizar la lección interactiva en video (`leccion-saludos.html`) respondiendo 15 preguntas de selección múltiple |
| **Para (Beneficio)** | Practicar la identificación de señas en LSC y guardar mi progreso y puntaje final en MySQL. |
| **Precondiciones** | El estudiante debe estar autenticado y la lección debe estar `Publicada` en la tabla `lecciones`. |
| **Postcondiciones** | Se inserta o actualiza el registro en la tabla `progreso_estudiante` de MySQL con `completado = 1`, `puntaje` y `fecha_completado`. |

### 📄 HU-08: Traductor LSC en tiempo real con Cámara, Django y MediaPipe IA
| Campo | Descripción |
| :--- | :--- |
| **ID Historia** | **HU-08** |
| **Nombre** | Traducción de Lengua de Señas Colombiana en tiempo real mediante visión artificial |
| **Sprint** | Sprint 3 |
| **Prioridad** | Alta |
| **Puntos de Historia** | 13 SP |
| **Como (Rol)** | Estudiante autenticado |
| **Quiero (Acción)** | Activar mi cámara web en `/traductor` para que el motor de IA en Django reconozca mis señas, forme palabras y las pronuncie en voz alta |
| **Para (Beneficio)** | Practicar mi expresión en LSC con detección en vivo y traducir señas a texto y voz. |
| **Precondiciones** | El servidor Django (`manage.py`), los modelos `hand_landmarker.task` y `modelo_lsc.pkl`, y la cámara web deben estar disponibles. |
| **Postcondiciones** | Las señas detectadas se convierten en letras, palabras e historial hablado mediante Web Speech API. |

### 📄 HU-09: Mascotas 3D interactivas (Mili y Lulú) con frases en formularios
| Campo | Descripción |
| :--- | :--- |
| **ID Historia** | **HU-09** |
| **Nombre** | Asistentes virtuales 3D (Mili y Lulú) con seguimiento de cursor y diálogos contextuales en formularios |
| **Sprint** | Sprint 3 |
| **Prioridad** | Media |
| **Puntos de Historia** | 8 SP |
| **Como (Rol)** | Usuario de HandApp |
| **Quiero (Acción)** | Ver a **Mili** (foca azul, a la izquierda) y **Lulú** (pato amarillo, a la derecha) en 3D cuando inicio sesión y en todos los formularios diciendo frases sobre el formulario |
| **Para (Beneficio)** | Recibir acompañamiento visual y consejos útiles al diligenciar cada campo. |
| **Precondiciones** | Los 6 renders 3D oficiales deben estar cargados en `/IMG/mascotas3d/`. |
| **Postcondiciones** | Las mascotas reaccionan en tiempo real al movimiento del mouse y al foco de los inputs. |

### 📄 HU-10: Pantalla de carga en video al cambiar de apartado y Modo Claro/Oscuro
| Campo | Descripción |
| :--- | :--- |
| **ID Historia** | **HU-10** |
| **Nombre** | Video animado de pantalla de carga entre apartados y selector de tema Claro/Oscuro |
| **Sprint** | Sprint 3 |
| **Prioridad** | Media |
| **Puntos de Historia** | 5 SP |
| **Como (Rol)** | Usuario autenticado |
| **Quiero (Acción)** | Visualizar el video animado de carga al entrar a cualquier apartado y poder cambiar entre tema claro y oscuro |
| **Para (Beneficio)** | Tener una navegación fluida, atractiva y adaptada a mi comodidad visual. |
| **Precondiciones** | El usuario debe estar autenticado y el archivo `/IMG/pantalla_carga.mp4` disponible. |
| **Postcondiciones** | Se reproduce el video de carga antes de redirigir al apartado seleccionado y el tema visual se guarda en `localStorage`. |

### 📄 HU-11: Panel Administrador (`@handapp.com`): CRUD de usuarios y reportes multicriterio
| Campo | Descripción |
| :--- | :--- |
| **ID Historia** | **HU-11** |
| **Nombre** | Gestión CRUD de usuarios, filtros multicriterio configurables y exportación de reportes estadísticos |
| **Sprint** | Sprint 4 |
| **Prioridad** | Alta |
| **Puntos de Historia** | 8 SP |
| **Como (Rol)** | Administrador (`@handapp.com`) |
| **Quiero (Acción)** | Administrar cuentas de usuario en `/admin/panel` y generar reportes con filtros multicriterio y consultas estadísticas en `/admin/reportes` |
| **Para (Beneficio)** | Controlar los roles de acceso y exportar informes en PDF, Excel o Word para la toma de decisiones. |
| **Precondiciones** | El usuario debe haber iniciado sesión con una cuenta `@handapp.com` (rol `ADMINISTRADOR`). |
| **Postcondiciones** | Los cambios de usuarios persisten en MySQL y los reportes se descargan con los filtros aplicados. |

### 📄 HU-12: Panel Editor (`@editorhand.com`): CRUD de lecciones y estadísticas de desempeño
| Campo | Descripción |
| :--- | :--- |
| **ID Historia** | **HU-12** |
| **Nombre** | Gestión CRUD de lecciones LSC y consulta de gráficos estadísticos por lección |
| **Sprint** | Sprint 4 |
| **Prioridad** | Alta |
| **Puntos de Historia** | 8 SP |
| **Como (Rol)** | Editor de contenido (`@editorhand.com`) |
| **Quiero (Acción)** | Crear, editar, publicar o eliminar lecciones en `/editor/panel` y consultar gráficos de rendimiento en `/editor/reportes` |
| **Para (Beneficio)** | Administrar el plan de estudios de LSC y analizar qué porcentaje de estudiantes completa cada lección y con qué puntaje promedio. |
| **Precondiciones** | El usuario debe haber iniciado sesión con una cuenta `@editorhand.com` (rol `EDITOR`). |
| **Postcondiciones** | Las lecciones publicadas quedan disponibles en MySQL para todos los estudiantes. |
