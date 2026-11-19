# 🤟 HandApp — Plataforma Interactiva de Aprendizaje y Traducción de Lengua de Señas Colombiana (LSC)

**HandApp** es una plataforma web educativa e inclusiva desarrollada para el aprendizaje, práctica y traducción en tiempo real de la **Lengua de Señas Colombiana (LSC)**, integrando **Spring Boot (Java 17)**, **Django (Python 3.12)**, **MySQL** e **Inteligencia Artificial por Visión Artificial (MediaPipe + scikit-learn)** junto con los asistentes virtuales 3D **Mili** y **Lulú**.

> 🎓 **Proyecto Formativo SENA** — *Tecnólogo en Análisis y Desarrollo de Software (ADSO - Código 228118) — Regional Distrito Capital (Centro de Servicios Financieros)*.

---

## 📚 Documentación Oficial del Proyecto (Carpeta `docs/`)

* 📋 **[Planificación Ágil Scrum: Sprints, Historias de Usuario (Formato SENA) e Issues (`docs/Sprints_Historias_Issues_HandApp.md`)](docs/Sprints_Historias_Issues_HandApp.md)**
* 🏛️ **[Documento de Arquitectura de Software (`docs/Documento Arquitectura HandApp.pdf`)](docs/Documento%20Arquitectura%20HandApp.pdf)**
* 🗄️ **[Script de Base de Datos MySQL (`database/HandApp.sql`)](database/HandApp.sql)**

---

## ✨ Características Principales

1. **Autenticación Segura, Acceso Social y Roles por Dominio (MySQL + BCrypt)**
   * Encriptación de contraseñas con **BCrypt (`$2a$10$`)** en la tabla `usuarios` de MySQL.
   * Registro e inicio de sesión con **Google** y **Apple**, además de **Recuperación de Cuenta con Google** (`/recuperar-cuenta`).
   * **Asignación automática de rol por dominio:**
     * Correos terminados en `@handapp.com` ➔ **`ADMINISTRADOR`** (acceso directo a `/admin/panel`).
     * Correos terminados en `@editorhand.com` ➔ **`EDITOR`** (acceso directo a `/editor/panel`).
     * Otros dominios (`@gmail.com`, `@icloud.com`, etc.) ➔ **`ESTUDIANTE`** (acceso a módulos de aprendizaje).
   * Bloqueo de seguridad en **Abecedario, Biblioteca, Lecciones, Juegos, Traductor y Barra de Búsqueda** para visitantes sin sesión activa.

2. **Módulos Educativos LSC**
   * **Abecedario Interactivo (`/abecedario`)**: Las 27 letras del abecedario LSC (`A–Z` incluyendo la `Ñ`) con tarjetas animadas y vista modal ampliada.
   * **Biblioteca de Señas (`/biblioteca`)**: Catálogo en video con buscador en tiempo real y filtros por categoría (`Saludos`, `Emociones`, `Números`, `Familia`, `Colores`).
   * **Lecciones Tipo Quiz (`/estudiante/lecciones/1`)**: Evaluaciones interactivas de 15 preguntas en video con retroalimentación inmediata, datos curiosos y guardado de puntaje y progreso en la tabla `progreso_estudiante` de MySQL.

3. **Traductor LSC con Inteligencia Artificial en Tiempo Real (`/traductor`)**
   * Motor en **Django + MediaPipe (`hand_landmarker.task`) + RandomForest (`modelo_lsc.pkl`)**.
   * Detección en vivo de los 21 puntos articulares de la mano por cámara web (`/video` y `/letra`).
   * Confirmación automática al mantener la seña durante 1.5 segundos, constructor de palabras/frases y pronunciación por voz (**Text-to-Speech**).

4. **Asistentes Virtuales 3D (Mili y Lulú) y Experiencia Visual**
   * **Mili** (foca azul, izquierda) y **Lulú** (pato amarillo, derecha) renderizados en 3D con seguimiento de cursor parallax y frases contextuales en todos los formularios.
   * **Pantalla de Carga en Video (`pantalla_carga.mp4`)** al navegar entre apartados y selector de **Modo Claro / Oscuro**.

5. **Paneles de Gestión y Reportes Estadísticos Multicriterio**
   * **Panel Administrador (`/admin/panel` y `/admin/reportes`)**: CRUD de usuarios, métricas de distribución por rol (`% del total`), **filtros multicriterio configurables** (texto + rol), cálculo de promedio de puntaje y exportación de reportes en **PDF, Excel y Word**.
   * **Panel Editor (`/editor/panel` y `/editor/reportes`)**: CRUD de lecciones LSC (`Publicada` / `Borrador`) y gráficos circulares (**Donut Charts**) con porcentaje de completado, puntaje promedio (`X/100`) e intentos por lección.

---

## 🗂️ Estructura del Proyecto

```text
HandApp/
├── database/
│   └── HandApp.sql                            # Esquema MySQL oficial (usuarios, lecciones, progreso_estudiante)
├── docs/
│   ├── Documento Arquitectura HandApp.pdf     # Documento técnico de arquitectura
│   └── Sprints_Historias_Issues_HandApp.md    # Sprints, 12 Historias de Usuario SENA y 24 Issues
├── iniciar_django.bat                         # Arranque rápido del servidor Django + IA
├── pom.xml                                    # Configuración Maven (Spring Boot)
└── src/main/
    ├── PYTHON/                                # Backend Django + Visión Artificial MediaPipe
    │   ├── manage.py
    │   ├── handapp_django/                    # settings, urls, views, mysql_db, bcrypt_pure, lsc_camera
    │   └── ia_lsc/                            # Modelos entrenados (modelo_lsc.pkl) y scripts de entrenamiento
    ├── java/com/handapp/HandApp/              # Backend Spring Boot (config, controller, dto, model, repository, service)
    └── resources/
        ├── static/                            # CSS/, JS/, IMG/ (abecedario/, mascotas3d/, pantalla_carga.mp4)
        └── templates/                         # Vistas HTML (index, login, register, admin/, editor/, estudiante/)
```

---

## 📅 Resumen de Sprints e Historias de Usuario

> 📄 Ver fichas completas con criterios de aceptación en **[`docs/Sprints_Historias_Issues_HandApp.md`](docs/Sprints_Historias_Issues_HandApp.md)**.

| Sprint | ID | Historia de Usuario | Rol | Prioridad | Puntos | Estado |
| :---: | :---: | :--- | :--- | :---: | :---: | :---: |
| **Sprint 1** | **HU-01** | Registro de usuario con rol por dominio y Google/Apple | Usuario nuevo | Alta | 8 SP | ✅ Completado |
| **Sprint 1** | **HU-02** | Inicio de sesión con BCrypt y redirección por rol | Usuario registrado | Alta | 5 SP | ✅ Completado |
| **Sprint 1** | **HU-03** | Recuperación de cuenta mediante verificación con Google | Usuario registrado | Media | 5 SP | ✅ Completado |
| **Sprint 1** | **HU-04** | Bloqueo de apartados y barra de búsqueda sin sesión | Sistema | Alta | 5 SP | ✅ Completado |
| **Sprint 2** | **HU-05** | Abecedario Interactivo LSC (27 letras con modal) | Estudiante | Alta | 5 SP | ✅ Completado |
| **Sprint 2** | **HU-06** | Biblioteca de Señas con buscador y filtros por categoría | Estudiante | Media | 5 SP | ✅ Completado |
| **Sprint 2** | **HU-07** | Lecciones tipo Quiz de 15 preguntas con puntaje en MySQL | Estudiante | Alta | 8 SP | ✅ Completado |
| **Sprint 3** | **HU-08** | Traductor LSC en tiempo real con Cámara, Django y MediaPipe | Estudiante | Alta | 13 SP | ✅ Completado |
| **Sprint 3** | **HU-09** | Mascotas 3D (Mili y Lulú) con frases guía en formularios | Usuario | Media | 8 SP | ✅ Completado |
| **Sprint 3** | **HU-10** | Video de pantalla de carga entre apartados y Modo Claro/Oscuro | Usuario | Media | 5 SP | ✅ Completado |
| **Sprint 4** | **HU-11** | Panel Administrador (`@handapp.com`): CRUD y reportes multicriterio | Administrador | Alta | 8 SP | ✅ Completado |
| **Sprint 4** | **HU-12** | Panel Editor (`@editorhand.com`): CRUD de lecciones y estadísticas | Editor | Alta | 8 SP | ✅ Completado |
