"""
Vistas y persistencia en MySQL (`127.0.0.1:3306/handapp`) en Django para HandApp.
Reglas de dominio y paneles:
- Correos terminados en @handapp.com -> rol ADMINISTRADOR -> entran a /admin/panel
- Correos terminados en @editorhand.com -> rol EDITOR -> entran a /editor/panel
- Otros correos -> rol ESTUDIANTE -> entran a /
"""
import csv
import io
from datetime import datetime
from pathlib import Path
from django.http import JsonResponse, StreamingHttpResponse, HttpResponseRedirect, HttpResponse
from django.views.decorators.csrf import csrf_exempt
from .thymeleaf_bridge import render_thymeleaf
from .lsc_camera import generar_frames_mjpeg, letra_actual
from .mysql_db import MySQLConnection, MySQLIntegrityError, MySQLError
from .bcrypt_pure import check_password, hash_password

DOMINIO_ADMIN = '@handapp.com'
DOMINIO_EDITOR = '@editorhand.com'

MYSQL_CONFIG = {
    'host': '127.0.0.1',
    'port': 3306,
    'user': 'root',
    'password': '',
    'database': 'handapp',
}


def get_db() -> MySQLConnection:
    return MySQLConnection(**MYSQL_CONFIG)


def determinar_rol_por_correo(correo: str) -> str:
    c = (correo or '').strip().lower()
    if c.endswith(DOMINIO_ADMIN):
        return 'ADMINISTRADOR'
    if c.endswith(DOMINIO_EDITOR):
        return 'EDITOR'
    return 'ESTUDIANTE'


def validar_dominio_rol(correo: str, rol: str) -> str | None:
    """
    Valida que:
    - Solo @handapp.com pueda ser ADMINISTRADOR
    - Solo @editorhand.com pueda ser EDITOR
    - ESTUDIANTE no use los dominios exclusivos de Admin/Editor
    Retorna mensaje de error o None si es válido.
    """
    c = (correo or '').strip().lower()
    if rol == 'ADMINISTRADOR' and not c.endswith(DOMINIO_ADMIN):
        return f'Un ADMINISTRADOR debe tener un correo terminado en {DOMINIO_ADMIN}'
    if rol == 'EDITOR' and not c.endswith(DOMINIO_EDITOR):
        return f'Un EDITOR debe tener un correo terminado en {DOMINIO_EDITOR}'
    if rol == 'ESTUDIANTE' and (c.endswith(DOMINIO_ADMIN) or c.endswith(DOMINIO_EDITOR)):
        return f'Los dominios {DOMINIO_ADMIN} y {DOMINIO_EDITOR} son exclusivos para Administradores y Editores.'
    return None


def _url_destino_usuario(user: dict) -> str:
    correo = (user.get('correo') or '').strip().lower()
    rol = (user.get('rol') or '').strip().upper()
    if rol == 'ADMINISTRADOR' or correo.endswith(DOMINIO_ADMIN):
        return '/admin/panel'
    if rol == 'EDITOR' or correo.endswith(DOMINIO_EDITOR):
        return '/editor/panel'
    return '/'


def init_db():
    """
    Sincroniza la base de datos MySQL `handapp` y normaliza todos los registros de `usuarios`
    para que:
    - Todos los ADMINISTRADOR tengan correo @handapp.com y viceversa.
    - Todos los EDITOR tengan correo @editorhand.com y viceversa.
    """
    try:
        with get_db() as conn:
            conn.execute(
                "UPDATE lecciones SET vista = 'leccion-saludos' WHERE orden = 1 AND (vista IS NULL OR vista = '')"
            )

            # Normalizar correos y roles en MySQL según las reglas de @handapp.com y @editorhand.com
            usuarios = conn.execute("SELECT id, nombre, correo, rol FROM usuarios").fetchall()
            for u in usuarios:
                uid = u['id']
                correo = (u['correo'] or '').strip()
                correo_low = correo.lower()
                rol = u['rol']

                if correo_low.endswith(DOMINIO_ADMIN) and rol != 'ADMINISTRADOR':
                    conn.execute("UPDATE usuarios SET rol = 'ADMINISTRADOR' WHERE id = ?", (uid,))
                elif correo_low.endswith(DOMINIO_EDITOR) and rol != 'EDITOR':
                    conn.execute("UPDATE usuarios SET rol = 'EDITOR' WHERE id = ?", (uid,))
                elif rol == 'ADMINISTRADOR' and not correo_low.endswith(DOMINIO_ADMIN):
                    prefijo = correo.split('@')[0]
                    nuevo_correo = f"{prefijo}{DOMINIO_ADMIN}"
                    conn.execute("UPDATE usuarios SET correo = ? WHERE id = ?", (nuevo_correo, uid))
                elif rol == 'EDITOR' and not correo_low.endswith(DOMINIO_EDITOR):
                    prefijo = correo.split('@')[0]
                    nuevo_correo = f"{prefijo}{DOMINIO_EDITOR}"
                    conn.execute("UPDATE usuarios SET correo = ? WHERE id = ?", (nuevo_correo, uid))

    except Exception as exc:
        print("Error inicializando MySQL handapp:", exc)


init_db()


def _usuario_actual(request):
    uid = request.session.get('usuario_id')
    if not uid:
        return None
    with get_db() as conn:
        row = conn.execute("SELECT * FROM usuarios WHERE id = ?", (uid,)).fetchone()
        return dict(row) if row else None


def _es_admin(user: dict | None) -> bool:
    if not user:
        return False
    return user.get('rol') == 'ADMINISTRADOR' and str(user.get('correo', '')).lower().endswith(DOMINIO_ADMIN)


def _es_editor(user: dict | None) -> bool:
    if not user:
        return False
    return user.get('rol') == 'EDITOR' and str(user.get('correo', '')).lower().endswith(DOMINIO_EDITOR)


def _lecciones_publicadas(usuario_id=None):
    with get_db() as conn:
        rows = conn.execute(
            "SELECT * FROM lecciones WHERE publicada = 1 ORDER BY orden ASC"
        ).fetchall()
        progreso_map = {}
        if usuario_id:
            p_rows = conn.execute(
                "SELECT * FROM progreso_estudiante WHERE estudiante_id = ?",
                (usuario_id,),
            ).fetchall()
            for p in p_rows:
                progreso_map[p['leccion_id']] = dict(p)

        resultado = []
        for r in rows:
            d = dict(r)
            prog = progreso_map.get(d['id'])
            d['progreso_pct'] = 100 if (prog and prog.get('completado')) else 0
            resultado.append(d)
        return resultado


def _base_context(request):
    user = _usuario_actual(request)
    logueado = user is not None
    ctx = {
        'logueado': logueado,
        'lecciones': _lecciones_publicadas(user['id'] if user else None),
        'esAdmin': _es_admin(user),
        'esEditor': _es_editor(user),
    }
    if user:
        ctx['nombreUsuario'] = user['nombre']
        ctx['inicial'] = user['nombre'][:1].upper()
        ctx['usuario'] = user
    return ctx


def inicio(request):
    return render_thymeleaf(request, 'index.html', _base_context(request))


def abecedario(request):
    if not _usuario_actual(request):
        return HttpResponseRedirect('/login?requerido=1')
    return render_thymeleaf(request, 'abecedario.html', _base_context(request))


def biblioteca(request):
    if not _usuario_actual(request):
        return HttpResponseRedirect('/login?requerido=1')
    return render_thymeleaf(request, 'biblioteca_de_señas.html', _base_context(request))


def traductor(request):
    if not _usuario_actual(request):
        return HttpResponseRedirect('/login?requerido=1')
    return render_thymeleaf(request, 'traductor.html', _base_context(request))


def video_page_or_stream(request):
    accept = request.headers.get('Accept', '')
    sec_dest = request.headers.get('Sec-Fetch-Dest', '')
    if sec_dest == 'image' or ('image/' in accept and 'text/html' not in accept) or request.GET.get('stream') == '1':
        response = StreamingHttpResponse(
            generar_frames_mjpeg(),
            content_type='multipart/x-mixed-replace; boundary=frame',
        )
        response['Access-Control-Allow-Origin'] = '*'
        return response
    return render_thymeleaf(request, 'Video_introductorio.html', _base_context(request))


def letra_api(request):
    resp = JsonResponse(letra_actual)
    resp['Access-Control-Allow-Origin'] = '*'
    return resp


@csrf_exempt
def login_view(request):
    if request.method == 'POST':
        correo = (request.POST.get('correo') or '').strip()
        password = request.POST.get('password') or ''
        with get_db() as conn:
            row = conn.execute(
                "SELECT * FROM usuarios WHERE lower(correo) = lower(?)",
                (correo,),
            ).fetchone()

        if row and check_password(password, row['contrasena']):
            # Asegurar sincronización de rol con dominio al iniciar sesión
            rol_esperado = determinar_rol_por_correo(row['correo'])
            if row['rol'] != rol_esperado:
                with get_db() as conn:
                    conn.execute("UPDATE usuarios SET rol = ? WHERE id = ?", (rol_esperado, row['id']))
                row['rol'] = rol_esperado

            request.session['usuario_id'] = row['id']
            return HttpResponseRedirect(_url_destino_usuario(row))
        return HttpResponseRedirect('/login?error=1')

    return render_thymeleaf(request, 'login.html', _base_context(request))


@csrf_exempt
def registro_view(request):
    if request.method == 'POST':
        nombre = (request.POST.get('nombre') or '').strip()
        correo = (request.POST.get('correo') or '').strip()
        contrasena = request.POST.get('contrasena') or ''
        confirmar = request.POST.get('confirmarContrasena') or ''

        if not contrasena or contrasena != confirmar:
            ctx = _base_context(request)
            ctx['error'] = 'Las contraseñas no coinciden.'
            return render_thymeleaf(request, 'register.html', ctx)

        rol_asignado = determinar_rol_por_correo(correo)

        try:
            with get_db() as conn:
                existe = conn.execute(
                    "SELECT id FROM usuarios WHERE lower(correo) = lower(?)",
                    (correo,),
                ).fetchone()
                if existe:
                    ctx = _base_context(request)
                    ctx['error'] = 'Ese correo ya está registrado.'
                    return render_thymeleaf(request, 'register.html', ctx)

                hashed = hash_password(contrasena, cost=6)
                conn.execute(
                    "INSERT INTO usuarios (nombre, correo, contrasena, rol) VALUES (?, ?, ?, ?)",
                    (nombre, correo, hashed, rol_asignado),
                )
            return HttpResponseRedirect('/login?registrado=1')
        except MySQLIntegrityError:
            ctx = _base_context(request)
            ctx['error'] = 'Ese correo ya está registrado.'
            return render_thymeleaf(request, 'register.html', ctx)

    return render_thymeleaf(request, 'register.html', _base_context(request))


@csrf_exempt
def logout_view(request):
    request.session.flush()
    return HttpResponseRedirect('/login?logout=1')


@csrf_exempt
def recuperar_cuenta_view(request):
    if request.method == 'POST':
        correo = (request.POST.get('correo') or '').strip()
        nombre = (request.POST.get('nombre') or '').strip()
        nueva = request.POST.get('nuevaContrasena') or ''
        confirmar = request.POST.get('confirmarContrasena') or ''

        if not correo:
            ctx = _base_context(request)
            ctx['error'] = 'Primero verifica tu correo con Google.'
            return render_thymeleaf(request, 'recuperar-cuenta.html', ctx)

        if len(nueva) < 4 or nueva != confirmar:
            ctx = _base_context(request)
            ctx['error'] = 'Las contraseñas deben coincidir y tener al menos 4 caracteres.'
            return render_thymeleaf(request, 'recuperar-cuenta.html', ctx)

        hashed = hash_password(nueva, cost=6)
        rol_asignado = determinar_rol_por_correo(correo)

        with get_db() as conn:
            row = conn.execute(
                "SELECT * FROM usuarios WHERE lower(correo) = lower(?)",
                (correo,),
            ).fetchone()

            if row:
                conn.execute(
                    "UPDATE usuarios SET contrasena = ?, rol = ? WHERE id = ?",
                    (hashed, rol_asignado, row['id']),
                )
                user_obj = {'id': row['id'], 'correo': correo, 'rol': rol_asignado}
            else:
                nombre_final = nombre or correo.split('@')[0].replace('.', ' ').title()
                res = conn.execute(
                    "INSERT INTO usuarios (nombre, correo, contrasena, rol) VALUES (?, ?, ?, ?)",
                    (nombre_final, correo, hashed, rol_asignado),
                )
                user_obj = {'id': res.lastrowid, 'correo': correo, 'rol': rol_asignado}

        request.session['usuario_id'] = user_obj['id']
        return HttpResponseRedirect(_url_destino_usuario(user_obj))

    return render_thymeleaf(request, 'recuperar-cuenta.html', _base_context(request))


@csrf_exempt
def google_auth_view(request):
    if request.method == 'POST':
        correo = (request.POST.get('correo') or '').strip()
        nombre = (request.POST.get('nombre') or '').strip()
        provider = (request.POST.get('provider') or 'google').strip().lower()

        if not correo or '@' not in correo:
            return HttpResponseRedirect('/registro')

        rol_asignado = determinar_rol_por_correo(correo)

        with get_db() as conn:
            row = conn.execute(
                "SELECT * FROM usuarios WHERE lower(correo) = lower(?)",
                (correo,),
            ).fetchone()

            if row:
                if row['rol'] != rol_asignado:
                    conn.execute("UPDATE usuarios SET rol = ? WHERE id = ?", (rol_asignado, row['id']))
                user_obj = {'id': row['id'], 'correo': row['correo'], 'rol': rol_asignado}
            else:
                nombre_final = nombre or correo.split('@')[0].replace('.', ' ').title()
                oauth_pass = hash_password(f'{provider}_oauth_{correo}', cost=6)
                res = conn.execute(
                    "INSERT INTO usuarios (nombre, correo, contrasena, rol) VALUES (?, ?, ?, ?)",
                    (nombre_final, correo, oauth_pass, rol_asignado),
                )
                user_obj = {'id': res.lastrowid, 'correo': correo, 'rol': rol_asignado}

        request.session['usuario_id'] = user_obj['id']
        return HttpResponseRedirect(_url_destino_usuario(user_obj))

    return HttpResponseRedirect('/registro')


# ══════════════════════════════════════════════════════════════
# PANEL DE ADMINISTRADOR (Exclusivo para correos @handapp.com)
# ══════════════════════════════════════════════════════════════

def _admin_context(request, user: dict) -> dict:
    ctx = _base_context(request)
    ctx['nombreAdmin'] = user['nombre']
    ctx['inicialAdmin'] = user['nombre'][:1].upper()
    ctx['correoAdmin'] = user['correo']
    flash_msg = request.session.pop('flash_mensaje', None)
    flash_err = request.session.pop('flash_error', None)
    if flash_msg:
        ctx['mensaje'] = flash_msg
    if flash_err:
        ctx['error'] = flash_err
    return ctx


def admin_panel(request):
    user = _usuario_actual(request)
    if not _es_admin(user):
        return HttpResponseRedirect('/login?requerido=1')

    with get_db() as conn:
        usuarios = conn.execute("SELECT * FROM usuarios ORDER BY id ASC").fetchall()

    total = len(usuarios)
    estudiantes = sum(1 for u in usuarios if u['rol'] == 'ESTUDIANTE')
    editores = sum(1 for u in usuarios if u['rol'] == 'EDITOR')
    administradores = sum(1 for u in usuarios if u['rol'] == 'ADMINISTRADOR')

    ctx = _admin_context(request, user)
    ctx.update({
        'usuarios': usuarios,
        'totalUsuarios': total,
        'totalEstudiantes': estudiantes,
        'totalEditores': editores,
        'totalAdministradores': administradores,
        'pctEstudiantes': 0 if total == 0 else round((estudiantes * 100.0) / total),
        'pctEditores': 0 if total == 0 else round((editores * 100.0) / total),
        'pctAdministradores': 0 if total == 0 else round((administradores * 100.0) / total),
    })
    return render_thymeleaf(request, 'admin/panel.html', ctx)


@csrf_exempt
def admin_usuario_nuevo(request):
    user = _usuario_actual(request)
    if not _es_admin(user):
        return HttpResponseRedirect('/login?requerido=1')

    ctx = _admin_context(request, user)
    ctx['esNuevo'] = True

    if request.method == 'POST':
        nombre = (request.POST.get('nombre') or '').strip()
        correo = (request.POST.get('correo') or '').strip()
        contrasena = request.POST.get('contrasena') or ''
        rol = (request.POST.get('rol') or 'ESTUDIANTE').strip().upper()

        ctx['usuarioForm'] = {'nombre': nombre, 'correo': correo, 'rol': rol}

        err_dom = validar_dominio_rol(correo, rol)
        if err_dom:
            ctx['error'] = err_dom
            return render_thymeleaf(request, 'admin/usuario-form.html', ctx)

        with get_db() as conn:
            existe = conn.execute(
                "SELECT id FROM usuarios WHERE lower(correo) = lower(?)",
                (correo,),
            ).fetchone()
            if existe:
                ctx['error'] = 'Ese correo ya está registrado.'
                return render_thymeleaf(request, 'admin/usuario-form.html', ctx)

            hashed = hash_password(contrasena, cost=6)
            conn.execute(
                "INSERT INTO usuarios (nombre, correo, contrasena, rol) VALUES (?, ?, ?, ?)",
                (nombre, correo, hashed, rol),
            )

        request.session['flash_mensaje'] = 'Usuario creado correctamente en MySQL.'
        return HttpResponseRedirect('/admin/panel')

    ctx['usuarioForm'] = {'nombre': '', 'correo': '', 'rol': 'ESTUDIANTE'}
    return render_thymeleaf(request, 'admin/usuario-form.html', ctx)


@csrf_exempt
def admin_usuario_editar(request, usuario_id: int):
    user = _usuario_actual(request)
    if not _es_admin(user):
        return HttpResponseRedirect('/login?requerido=1')

    with get_db() as conn:
        objetivo = conn.execute("SELECT * FROM usuarios WHERE id = ?", (usuario_id,)).fetchone()
    if not objetivo:
        return HttpResponseRedirect('/admin/panel')

    ctx = _admin_context(request, user)
    ctx['esNuevo'] = False

    if request.method == 'POST':
        nombre = (request.POST.get('nombre') or '').strip()
        correo = (request.POST.get('correo') or '').strip()
        contrasena = request.POST.get('contrasena') or ''
        rol = (request.POST.get('rol') or objetivo['rol']).strip().upper()

        ctx['usuarioForm'] = {'id': usuario_id, 'nombre': nombre, 'correo': correo, 'rol': rol}

        err_dom = validar_dominio_rol(correo, rol)
        if err_dom:
            ctx['error'] = err_dom
            return render_thymeleaf(request, 'admin/usuario-form.html', ctx)

        with get_db() as conn:
            duplicado = conn.execute(
                "SELECT id FROM usuarios WHERE lower(correo) = lower(?) AND id != ?",
                (correo, usuario_id),
            ).fetchone()
            if duplicado:
                ctx['error'] = 'Ese correo ya está en uso por otro usuario.'
                return render_thymeleaf(request, 'admin/usuario-form.html', ctx)

            if contrasena.strip():
                hashed = hash_password(contrasena, cost=6)
                conn.execute(
                    "UPDATE usuarios SET nombre = ?, correo = ?, contrasena = ?, rol = ? WHERE id = ?",
                    (nombre, correo, hashed, rol, usuario_id),
                )
            else:
                conn.execute(
                    "UPDATE usuarios SET nombre = ?, correo = ?, rol = ? WHERE id = ?",
                    (nombre, correo, rol, usuario_id),
                )

        request.session['flash_mensaje'] = 'Usuario actualizado correctamente en MySQL.'
        return HttpResponseRedirect('/admin/panel')

    ctx['usuarioForm'] = dict(objetivo)
    return render_thymeleaf(request, 'admin/usuario-form.html', ctx)


@csrf_exempt
def admin_usuario_eliminar(request, usuario_id: int):
    user = _usuario_actual(request)
    if not _es_admin(user):
        return HttpResponseRedirect('/login?requerido=1')

    if request.method == 'POST':
        if usuario_id == user['id']:
            request.session['flash_error'] = 'No puedes eliminar tu propia cuenta.'
            return HttpResponseRedirect('/admin/panel')

        with get_db() as conn:
            conn.execute("DELETE FROM progreso_estudiante WHERE estudiante_id = ?", (usuario_id,))
            conn.execute("DELETE FROM usuarios WHERE id = ?", (usuario_id,))

        request.session['flash_mensaje'] = 'Usuario eliminado correctamente de MySQL.'
    return HttpResponseRedirect('/admin/panel')


def _construir_filas_reporte(busqueda: str | None, rol_filtro: str | None) -> list[dict]:
    with get_db() as conn:
        usuarios = conn.execute("SELECT * FROM usuarios ORDER BY id ASC").fetchall()
        progresos_all = conn.execute(
            """
            SELECT p.*, l.titulo AS titulo_leccion
            FROM progreso_estudiante p
            JOIN lecciones l ON l.id = p.leccion_id
            ORDER BY p.fecha_completado DESC
            """
        ).fetchall()

    prog_por_est = {}
    for p in progresos_all:
        prog_por_est.setdefault(p['estudiante_id'], []).append(p)

    b_low = (busqueda or '').strip().lower()
    r_up = (rol_filtro or '').strip().upper()

    filas = []
    for u in usuarios:
        if r_up and u['rol'] != r_up:
            continue
        if b_low and b_low not in u['nombre'].lower() and b_low not in u['correo'].lower():
            continue

        progs_list = []
        completadas = 0
        suma_pts = 0.0
        cnt_pts = 0

        if u['rol'] == 'ESTUDIANTE':
            for p in prog_por_est.get(u['id'], []):
                comp = bool(p.get('completado'))
                pts = p.get('puntaje')
                progs_list.append({
                    'tituloLeccion': p['titulo_leccion'],
                    'completado': comp,
                    'puntaje': pts,
                    'preguntaActual': p.get('pregunta_actual'),
                })
                if comp:
                    completadas += 1
                    if pts is not None:
                        suma_pts += float(pts)
                        cnt_pts += 1

        filas.append({
            'id': u['id'],
            'nombre': u['nombre'],
            'correo': u['correo'],
            'rol': u['rol'],
            'leccionesCompletadas': completadas,
            'promedioPuntaje': (suma_pts / cnt_pts) if cnt_pts > 0 else None,
            'progresos': progs_list,
        })
    return filas


def admin_reportes(request):
    user = _usuario_actual(request)
    if not _es_admin(user):
        return HttpResponseRedirect('/login?requerido=1')

    busqueda = request.GET.get('busqueda', '')
    rol = request.GET.get('rol', '')
    filas = _construir_filas_reporte(busqueda, rol)

    ctx = _admin_context(request, user)
    ctx.update({
        'filas': filas,
        'busqueda': busqueda,
        'rolSeleccionado': rol,
    })
    return render_thymeleaf(request, 'admin/reportes.html', ctx)


def admin_exportar_reporte(request, formato: str):
    user = _usuario_actual(request)
    if not _es_admin(user):
        return HttpResponseRedirect('/login?requerido=1')

    busqueda = request.GET.get('busqueda', '')
    rol = request.GET.get('rol', '')
    filas = _construir_filas_reporte(busqueda, rol)

    buf = io.StringIO()
    writer = csv.writer(buf)
    writer.writerow(['ID', 'Nombre', 'Correo', 'Rol', 'Lecciones Completadas', 'Promedio Puntaje'])
    for f in filas:
        prom = f"{f['promedioPuntaje']:.1f}" if f['promedioPuntaje'] is not None else 'N/A'
        writer.writerow([f['id'], f['nombre'], f['correo'], f['rol'], f['leccionesCompletadas'], prom])

    content = '\ufeff' + buf.getvalue()
    resp = HttpResponse(content.encode('utf-8'), content_type='text/csv; charset=utf-8')
    resp['Content-Disposition'] = f'attachment; filename="reporte-usuarios-{formato}.csv"'
    return resp


# ══════════════════════════════════════════════════════════════
# PANEL DE EDITOR (Exclusivo para correos @editorhand.com)
# ══════════════════════════════════════════════════════════════

def _editor_context(request, user: dict) -> dict:
    ctx = _base_context(request)
    ctx['nombreEditor'] = user['nombre']
    ctx['inicialEditor'] = user['nombre'][:1].upper()
    ctx['correoEditor'] = user['correo']
    flash_msg = request.session.pop('flash_mensaje', None)
    flash_err = request.session.pop('flash_error', None)
    if flash_msg:
        ctx['mensaje'] = flash_msg
    if flash_err:
        ctx['error'] = flash_err
    return ctx


def editor_panel(request):
    user = _usuario_actual(request)
    if not _es_editor(user):
        return HttpResponseRedirect('/login?requerido=1')

    with get_db() as conn:
        lecciones = conn.execute("SELECT * FROM lecciones ORDER BY orden ASC").fetchall()

    total = len(lecciones)
    publicadas = sum(1 for l in lecciones if l.get('publicada'))

    ctx = _editor_context(request, user)
    ctx.update({
        'lecciones': lecciones,
        'totalLecciones': total,
        'totalPublicadas': publicadas,
        'totalBorrador': total - publicadas,
    })
    return render_thymeleaf(request, 'editor/panel.html', ctx)


@csrf_exempt
def editor_leccion_nueva(request):
    user = _usuario_actual(request)
    if not _es_editor(user):
        return HttpResponseRedirect('/login?requerido=1')

    ctx = _editor_context(request, user)
    ctx['esNueva'] = True

    if request.method == 'POST':
        titulo = (request.POST.get('titulo') or '').strip()
        descripcion = (request.POST.get('descripcion') or '').strip()
        contenido_url = (request.POST.get('contenidoUrl') or '').strip()
        orden = int(request.POST.get('orden') or 1)
        vista = (request.POST.get('vista') or '').strip()
        publicada = 1 if request.POST.get('publicada') else 0

        with get_db() as conn:
            conn.execute(
                """
                INSERT INTO lecciones (titulo, descripcion, contenido_url, orden, publicada, vista, creado_por_id)
                VALUES (?, ?, ?, ?, ?, ?, ?)
                """,
                (titulo, descripcion, contenido_url, orden, publicada, vista or None, user['id']),
            )

        request.session['flash_mensaje'] = 'Lección creada correctamente en MySQL.'
        return HttpResponseRedirect('/editor/panel')

    ctx['leccionForm'] = {'titulo': '', 'descripcion': '', 'contenidoUrl': '', 'orden': 1, 'vista': '', 'publicada': True}
    return render_thymeleaf(request, 'editor/leccion-form.html', ctx)


@csrf_exempt
def editor_leccion_editar(request, leccion_id: int):
    user = _usuario_actual(request)
    if not _es_editor(user):
        return HttpResponseRedirect('/login?requerido=1')

    with get_db() as conn:
        lec = conn.execute("SELECT * FROM lecciones WHERE id = ?", (leccion_id,)).fetchone()
    if not lec:
        return HttpResponseRedirect('/editor/panel')

    ctx = _editor_context(request, user)
    ctx['esNueva'] = False

    if request.method == 'POST':
        titulo = (request.POST.get('titulo') or '').strip()
        descripcion = (request.POST.get('descripcion') or '').strip()
        contenido_url = (request.POST.get('contenidoUrl') or '').strip()
        orden = int(request.POST.get('orden') or lec['orden'])
        vista = (request.POST.get('vista') or '').strip()
        publicada = 1 if request.POST.get('publicada') else 0

        with get_db() as conn:
            conn.execute(
                """
                UPDATE lecciones
                SET titulo = ?, descripcion = ?, contenido_url = ?, orden = ?, publicada = ?, vista = ?
                WHERE id = ?
                """,
                (titulo, descripcion, contenido_url, orden, publicada, vista or None, leccion_id),
            )

        request.session['flash_mensaje'] = 'Lección actualizada correctamente en MySQL.'
        return HttpResponseRedirect('/editor/panel')

    ctx['leccionForm'] = {
        'id': lec['id'],
        'titulo': lec['titulo'],
        'descripcion': lec.get('descripcion') or '',
        'contenidoUrl': lec.get('contenido_url') or '',
        'orden': lec['orden'],
        'vista': lec.get('vista') or '',
        'publicada': bool(lec.get('publicada')),
    }
    return render_thymeleaf(request, 'editor/leccion-form.html', ctx)


@csrf_exempt
def editor_leccion_eliminar(request, leccion_id: int):
    user = _usuario_actual(request)
    if not _es_editor(user):
        return HttpResponseRedirect('/login?requerido=1')

    if request.method == 'POST':
        with get_db() as conn:
            conn.execute("DELETE FROM progreso_estudiante WHERE leccion_id = ?", (leccion_id,))
            conn.execute("DELETE FROM lecciones WHERE id = ?", (leccion_id,))
        request.session['flash_mensaje'] = 'Lección eliminada correctamente de MySQL.'
    return HttpResponseRedirect('/editor/panel')


def editor_reportes(request):
    user = _usuario_actual(request)
    if not _es_editor(user):
        return HttpResponseRedirect('/login?requerido=1')

    with get_db() as conn:
        lecciones = conn.execute("SELECT * FROM lecciones ORDER BY orden ASC").fetchall()
        progresos = conn.execute("SELECT * FROM progreso_estudiante").fetchall()

    datos = []
    for lec in lecciones:
        lid = lec['id']
        intentos_lec = [p for p in progresos if p['leccion_id'] == lid]
        completados_lec = [p for p in intentos_lec if p.get('completado')]
        puntajes = [p['puntaje'] for p in completados_lec if p.get('puntaje') is not None]

        total_int = len(intentos_lec)
        total_comp = len(completados_lec)
        pct = 0 if total_int == 0 else round((total_comp * 100.0) / total_int)
        prom = round(sum(puntajes) / len(puntajes)) if puntajes else None

        datos.append({
            'leccionId': lid,
            'titulo': lec['titulo'],
            'totalIntentos': total_int,
            'totalCompletados': total_comp,
            'pctCompletado': pct,
            'promedioPuntaje': prom,
        })

    ctx = _editor_context(request, user)
    ctx['datos'] = datos
    return render_thymeleaf(request, 'editor/reportes.html', ctx)


# ══════════════════════════════════════════════════════════════
# ESTUDIANTE — LECCIONES Y QUIZ
# ══════════════════════════════════════════════════════════════

def estudiante_lecciones(request):
    if not _usuario_actual(request):
        return HttpResponseRedirect('/login?requerido=1')
    return HttpResponseRedirect('/estudiante/lecciones/1')


def estudiante_leccion_detalle(request, leccion_id: int):
    if not _usuario_actual(request):
        return HttpResponseRedirect('/login?requerido=1')
    with get_db() as conn:
        row = conn.execute("SELECT * FROM lecciones WHERE id = ?", (leccion_id,)).fetchone()
    if not row:
        return HttpResponseRedirect('/')

    leccion = dict(row)
    ctx = _base_context(request)
    ctx['leccion'] = leccion

    vista = (leccion.get('vista') or '').strip()
    if not vista:
        return render_thymeleaf(request, 'estudiante/leccion-pendiente.html', ctx)
    return render_thymeleaf(request, f'estudiante/{vista}.html', ctx)


@csrf_exempt
def completar_leccion(request, leccion_id: int):
    if request.method == 'POST':
        user = _usuario_actual(request)
        if not user:
            return JsonResponse({'ok': False, 'error': 'No autenticado'}, status=401)
        estudiante_id = user['id']
        puntaje = int(request.POST.get('puntaje') or 100)
        ahora = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
        with get_db() as conn:
            existente = conn.execute(
                "SELECT id FROM progreso_estudiante WHERE estudiante_id = ? AND leccion_id = ?",
                (estudiante_id, leccion_id),
            ).fetchone()
            if existente:
                conn.execute(
                    """
                    UPDATE progreso_estudiante
                    SET completado = 1, puntaje = ?, pregunta_actual = 15,
                        fecha_completado = ?, ultima_actividad = ?
                    WHERE id = ?
                    """,
                    (puntaje, ahora, ahora, existente['id']),
                )
            else:
                conn.execute(
                    """
                    INSERT INTO progreso_estudiante
                        (completado, fecha_completado, puntaje, estudiante_id, leccion_id, pregunta_actual, ultima_actividad)
                    VALUES (1, ?, ?, ?, ?, 15, ?)
                    """,
                    (ahora, puntaje, estudiante_id, leccion_id, ahora),
                )
        return JsonResponse({'ok': True, 'puntaje': puntaje})
    return HttpResponse(status=405)
