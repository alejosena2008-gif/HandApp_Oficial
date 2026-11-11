"""
Puente de plantillas Thymeleaf -> Django para HandApp.
Renderiza todas las plantillas originales en src/main/resources/templates/
(incluyendo index, login, register, recuperar-cuenta, admin/*, editor/* y estudiante/*)
sin alterar clases CSS ni estilos, manteniendo 100% compatibilidad con Spring Boot.
"""
import html
import re
from pathlib import Path
from django.http import HttpResponse
from .settings import TEMPLATES_DIR


def _load_fragment_tema() -> str:
    tema_path = TEMPLATES_DIR / 'fragments' / 'tema.html'
    if not tema_path.exists():
        return ''
    raw = tema_path.read_text(encoding='utf-8')
    m = re.search(r'<th:block[^>]*th:fragment="assets"[^>]*>(.*?)</th:block>', raw, re.DOTALL)
    content = m.group(1) if m else raw
    content = re.sub(
        r'th:(?:href|src)="\@{([^"]+)}"',
        lambda m2: f'href="{m2.group(1)}"' if 'href' in m2.group(0) else f'src="{m2.group(1)}"',
        content,
    )
    return content


def _load_fragment_mascotas(title: str, text: str) -> str:
    masc_path = TEMPLATES_DIR / 'fragments' / 'mascotas.html'
    if not masc_path.exists():
        return ''
    raw = masc_path.read_text(encoding='utf-8')
    m2 = re.search(r'(<div[^>]*th:fragment="duo.*?)</body>', raw, re.DOTALL)
    frag = m2.group(1).strip() if m2 else ''
    frag = re.sub(r'\s*th:fragment="[^"]*"', '', frag)
    frag = re.sub(
        r'<strong\s+th:text="\$\{title\}">.*?</strong>',
        f'<strong>{html.escape(title)}</strong>',
        frag,
        flags=re.DOTALL,
    )
    frag = re.sub(
        r'<span\s+th:text="\$\{text\}">.*?</span>',
        f'<span>{html.escape(text)}</span>',
        frag,
        flags=re.DOTALL,
    )
    return frag


def _remove_element_when_false(doc: str, attr_pattern: str) -> str:
    """Elimina bloques <tag ... attr_pattern ...>...</tag> cuando su condición th:if es falsa."""
    pattern = re.compile(
        r'<([a-zA-Z0-9:_-]+)\b[^>]*\b' + attr_pattern + r'[^>]*>',
        re.DOTALL,
    )
    while True:
        match = pattern.search(doc)
        if not match:
            break
        tag_name = match.group(1)
        start_idx = match.start()
        open_end = match.end()

        open_tag_re = re.compile(r'<' + re.escape(tag_name) + r'\b[^>]*>', re.DOTALL)
        close_tag_re = re.compile(r'</' + re.escape(tag_name) + r'\s*>', re.DOTALL)

        depth = 1
        pos = open_end
        while depth > 0 and pos < len(doc):
            next_open = open_tag_re.search(doc, pos)
            next_close = close_tag_re.search(doc, pos)
            if not next_close:
                pos = len(doc)
                break
            if next_open and next_open.start() < next_close.start():
                depth += 1
                pos = next_open.end()
            else:
                depth -= 1
                pos = next_close.end()
        doc = doc[:start_idx] + doc[pos:]
    return doc


def render_thymeleaf(request, template_rel_path: str, context: dict | None = None) -> HttpResponse:
    ctx = dict(context or {})
    if not template_rel_path.endswith('.html'):
        template_rel_path += '.html'

    tpl_path: Path = TEMPLATES_DIR / template_rel_path
    if not tpl_path.exists() and template_rel_path.startswith('HTML/'):
        tpl_path = TEMPLATES_DIR / template_rel_path.split('HTML/', 1)[1]
    doc = tpl_path.read_text(encoding='utf-8')

    # 1. Reemplazar fragmento de tema (<th:block th:replace="~{fragments/tema :: assets}"></th:block>)
    logueado = bool(ctx.get('logueado', False))
    logueado_flag = 'true' if logueado else 'false'
    tema_assets = f'<script>window.HANDAPP_AUTHENTICATED = {logueado_flag};</script>\n' + _load_fragment_tema()
    doc = re.sub(
        r'<th:block[^>]*th:replace="~\{fragments/tema\s*::\s*assets\}"[^>]*>\s*</th:block>',
        tema_assets,
        doc,
        flags=re.DOTALL,
    )

    # 2. Reemplazar fragmentos de mascotas
    def _repl_mascot(m):
        return _load_fragment_mascotas(m.group(1), m.group(2))

    doc = re.sub(
        r'<div[^>]*th:replace="~\{fragments/mascotas\s*::\s*duo\(\'([^\']*)\'\s*,\s*\'([^\']*)\'\)\}"[^>]*>\s*</div>',
        _repl_mascot,
        doc,
        flags=re.DOTALL,
    )

    # 3. Condicionales th:if comunes
    lecciones = ctx.get('lecciones', [])
    usuarios = ctx.get('usuarios', [])
    filas = ctx.get('filas', [])
    error_msg = ctx.get('error', '')
    mensaje_msg = ctx.get('mensaje', '')
    es_admin = bool(ctx.get('esAdmin', False))
    es_editor = bool(ctx.get('esEditor', False))

    if logueado:
        doc = _remove_element_when_false(doc, r'th:if="\$\{!logueado\}"')
    else:
        doc = _remove_element_when_false(doc, r'th:if="\$\{logueado\}"')

    if not es_admin:
        doc = _remove_element_when_false(doc, r'th:if="\$\{esAdmin\}"')
    if not es_editor:
        doc = _remove_element_when_false(doc, r'th:if="\$\{esEditor\}"')

    if len(lecciones) > 0:
        doc = _remove_element_when_false(doc, r'th:if="\$\{#lists\.isEmpty\(lecciones\)\}"')
    if len(usuarios) > 0:
        doc = _remove_element_when_false(doc, r'th:if="\$\{#lists\.isEmpty\(usuarios\)\}"')
    if len(filas) > 0:
        doc = _remove_element_when_false(doc, r'th:if="\$\{#lists\.isEmpty\(filas\)\}"')

    if not error_msg:
        doc = _remove_element_when_false(doc, r'th:if="\$\{error\}"')
    else:
        doc = re.sub(
            r'(<div[^>]*th:if="\$\{error\}"[^>]*>)(.*?)(</div>)',
            lambda m: f'{m.group(1)}{html.escape(str(error_msg))}{m.group(3)}',
            doc,
            flags=re.DOTALL,
        )

    if not mensaje_msg:
        doc = _remove_element_when_false(doc, r'th:if="\$\{mensaje\}"')
    else:
        doc = re.sub(
            r'(<div[^>]*th:if="\$\{mensaje\}"[^>]*>)(.*?)(</div>)',
            lambda m: f'{m.group(1)}{html.escape(str(mensaje_msg))}{m.group(3)}',
            doc,
            flags=re.DOTALL,
        )

    # Parámetros GET (?error, ?registrado, ?recuperado, ?logout, ?requerido)
    for param_name in ('error', 'registrado', 'recuperado', 'logout', 'requerido'):
        if param_name not in request.GET:
            doc = _remove_element_when_false(doc, rf'th:if="\$\{{param\.{param_name}\}}"')

    # 4. Plantillas específicas de Admin y Editor
    if template_rel_path == 'admin/panel.html':
        tr_re = re.compile(r'<tr\s+th:each="usuario\s*:\s*\$\{usuarios\}"[^>]*>.*?</tr>', re.DOTALL)
        if tr_re.search(doc):
            rows_html = []
            for u in usuarios:
                uid = u['id']
                unombre = html.escape(str(u['nombre']))
                ucorreo = html.escape(str(u['correo']))
                urol = str(u['rol'])
                urol_low = urol.lower()
                ini = html.escape(str(u['nombre'])[:1].upper())
                rows_html.append(f"""
                <tr data-user-row>
                    <td>
                        <div class="user-cell">
                            <div class="mini-avatar rol-{urol_low}">{ini}</div>
                            <div>
                                <div class="cell-name">{unombre}</div>
                                <div class="cell-email">{ucorreo}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="role-tag {urol_low}">{urol}</span>
                    </td>
                    <td>
                        <div class="action-btns">
                            <a href="/admin/usuarios/{uid}/editar" class="action-btn" title="Editar">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>
                            <form action="/admin/usuarios/{uid}/eliminar" method="post" onsubmit="return confirm('¿Eliminar a {unombre}? Esta acción no se puede deshacer.');" style="display:contents">
                                <button type="submit" class="action-btn" title="Eliminar">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3,6 5,6 21,6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                """)
            doc = tr_re.sub('\n'.join(rows_html), doc)

        doc = doc.replace(
            'th:style="\'width:\' + ${pctEstudiantes} + \'%; background:var(--info)\'"',
            f'style="width:{ctx.get("pctEstudiantes", 0)}%; background:var(--info)"',
        )
        doc = doc.replace(
            'th:style="\'width:\' + ${pctEditores} + \'%; background:var(--success)\'"',
            f'style="width:{ctx.get("pctEditores", 0)}%; background:var(--success)"',
        )
        doc = doc.replace(
            'th:style="\'width:\' + ${pctAdministradores} + \'%; background:var(--accent2)\'"',
            f'style="width:{ctx.get("pctAdministradores", 0)}%; background:var(--accent2)"',
        )
        for pct_var in ('pctEstudiantes', 'pctEditores', 'pctAdministradores'):
            val = ctx.get(pct_var, 0)
            doc = re.sub(
                rf'(<[^>]*\bth:text="\$\{{{pct_var}\}}\s*\+\s*\'% del total\'"[^>]*>).*?(</[a-zA-Z0-9]+>)',
                lambda m, v=val: f'{m.group(1)}{v}% del total{m.group(2)}',
                doc,
            )

    elif template_rel_path == 'admin/usuario-form.html':
        es_nuevo = bool(ctx.get('esNuevo', True))
        uform = ctx.get('usuarioForm') or {}
        uid = uform.get('id', '')
        unombre = html.escape(str(uform.get('nombre', '') or ''))
        ucorreo = html.escape(str(uform.get('correo', '') or ''))
        urol = str(uform.get('rol', 'ESTUDIANTE') or 'ESTUDIANTE')

        titulo_txt = 'Crear nuevo usuario' if es_nuevo else 'Editar usuario'
        btn_txt = 'Crear usuario' if es_nuevo else 'Guardar cambios'
        action_url = '/admin/usuarios/nuevo' if es_nuevo else f'/admin/usuarios/{uid}/editar'

        doc = re.sub(r'<title[^>]*>.*?</title>', f'<title>{titulo_txt} - HandApp</title>', doc, flags=re.DOTALL)
        doc = re.sub(r'<h1[^>]*>.*?</h1>', f'<h1>{titulo_txt}</h1>', doc, flags=re.DOTALL)
        doc = re.sub(r'<form[^>]*>', f'<form action="{action_url}" method="post">', doc, count=1)
        doc = re.sub(
            r'<input\s+type="text"\s+id="nombre"[^>]*>',
            f'<input type="text" id="nombre" name="nombre" value="{unombre}" required>',
            doc,
        )
        doc = re.sub(
            r'<input\s+type="email"\s+id="correo"[^>]*>',
            f'<input type="email" id="correo" name="correo" value="{ucorreo}" required>',
            doc,
        )
        req_attr = 'required' if es_nuevo else ''
        doc = re.sub(
            r'<input\s+type="password"\s+id="contrasena"[^>]*>',
            f'<input type="password" id="contrasena" name="contrasena" {req_attr} placeholder="Dejar en blanco para no cambiarla">',
            doc,
            flags=re.DOTALL,
        )
        if es_nuevo:
            doc = _remove_element_when_false(doc, r'th:unless="\$\{esNuevo\}"')

        roles_opts = []
        for r in ('ESTUDIANTE', 'EDITOR', 'ADMINISTRADOR'):
            sel = 'selected' if r == urol else ''
            roles_opts.append(f'<option value="{r}" {sel}>{r}</option>')
        doc = re.sub(
            r'<select\s+id="rol"[^>]*>.*?</select>',
            f'<select id="rol" name="rol" required>{"".join(roles_opts)}</select>',
            doc,
            flags=re.DOTALL,
        )
        doc = re.sub(
            r'<button\s+type="submit"\s+class="btn btn-primary"[^>]*>.*?</button>',
            f'<button type="submit" class="btn btn-primary">{btn_txt}</button>',
            doc,
            flags=re.DOTALL,
        )

    elif template_rel_path == 'admin/reportes.html':
        busqueda = html.escape(str(ctx.get('busqueda') or ''))
        rol_sel = str(ctx.get('rolSeleccionado') or '')
        doc = doc.replace('th:value="${busqueda}"', f'value="{busqueda}"')
        roles_opts = ['<option value="">Todos</option>']
        for r in ('ESTUDIANTE', 'EDITOR', 'ADMINISTRADOR'):
            sel = 'selected' if r == rol_sel else ''
            roles_opts.append(f'<option value="{r}" {sel}>{r}</option>')
        doc = re.sub(
            r'<select\s+id="rol"\s+name="rol">.*?</select>',
            f'<select id="rol" name="rol">{"".join(roles_opts)}</select>',
            doc,
            flags=re.DOTALL,
        )
        doc = re.sub(
            r'th:href="\@\{/admin/reportes/exportar/(pdf|excel|word)\([^)]*\)\}"',
            lambda m: f'href="/admin/reportes/exportar/{m.group(1)}?busqueda={busqueda}&rol={rol_sel}"',
            doc,
        )
        tr_re = re.compile(r'<tr\s+th:each="fila\s*:\s*\$\{filas\}"[^>]*>.*?</tr>', re.DOTALL)
        if tr_re.search(doc):
            f_html = []
            for f in filas:
                fnombre = html.escape(str(f['nombre']))
                fcorreo = html.escape(str(f['correo']))
                frol = str(f['rol'])
                progs = f.get('progresos') or []
                if frol == 'ESTUDIANTE' and progs:
                    prom = f.get('promedioPuntaje')
                    prom_html = f'<div class="resumen-label">Promedio: {prom:.1f} pts</div>' if prom is not None else '<div class="resumen-label">Sin puntajes todavía</div>'
                    resumen_col = f'<div><div class="resumen-num">{f.get("leccionesCompletadas", 0)} completadas</div>{prom_html}</div>'
                    p_items = []
                    for p in progs:
                        ptit = html.escape(str(p['tituloLeccion']))
                        if p.get('completado'):
                            est = f'<span class="estado-completado">{p.get("puntaje", 100)} pts</span>'
                        elif p.get('preguntaActual') is not None:
                            est = f'<span class="estado-progreso">Pregunta {int(p["preguntaActual"]) + 1}</span>'
                        else:
                            est = '<span class="estado-sin">Sin empezar</span>'
                        p_items.append(f'<div class="progreso-item"><span class="titulo">{ptit}</span>{est}</div>')
                    prog_col = f'<div class="progreso-lista">{"".join(p_items)}</div>'
                elif frol == 'ESTUDIANTE':
                    resumen_col = '<div class="sin-datos">Aún no ha empezado ninguna lección</div>'
                    prog_col = '<span class="sin-datos">—</span>'
                else:
                    resumen_col = f'<div class="sin-datos">No aplica (rol {frol})</div>'
                    prog_col = '<span class="sin-datos">—</span>'

                f_html.append(f"""
                <tr>
                    <td>
                        <div class="usuario-nombre">{fnombre}</div>
                        <div class="usuario-correo">{fcorreo}</div>
                    </td>
                    <td><span class="rol-pill">{frol}</span></td>
                    <td>{resumen_col}</td>
                    <td>{prog_col}</td>
                </tr>
                """)
            doc = tr_re.sub('\n'.join(f_html), doc)

    elif template_rel_path == 'editor/panel.html':
        tr_re = re.compile(r'<tr\s+th:each="leccion\s*:\s*\$\{lecciones\}"[^>]*>.*?</tr>', re.DOTALL)
        if tr_re.search(doc):
            l_html = []
            for lec in lecciones:
                lid = lec['id']
                lorden = lec['orden']
                ltitulo = html.escape(str(lec['titulo']))
                ldesc = html.escape(str(lec.get('descripcion') or ''))
                lpub = bool(lec.get('publicada'))
                lvista = (lec.get('vista') or '').strip()
                chip_cls = 'publicada' if lpub else 'borrador'
                chip_txt = 'Publicada' if lpub else 'Borrador'
                if lvista:
                    vista_html = f'<span style="font-size:.78rem; color:var(--accent);">{html.escape(lvista)}</span>'
                else:
                    vista_html = '<span class="chip borrador" style="font-size:.7rem;"><span class="chip-dot"></span> Sin asignar</span>'
                l_html.append(f"""
                <tr>
                    <td>{lorden}</td>
                    <td>
                        <div class="cell-title">{ltitulo}</div>
                        <div class="cell-desc">{ldesc}</div>
                    </td>
                    <td>
                        <span class="chip {chip_cls}">
                            <span class="chip-dot"></span>
                            <span>{chip_txt}</span>
                        </span>
                    </td>
                    <td>{vista_html}</td>
                    <td>
                        <div class="action-btns">
                            <a href="/editor/lecciones/{lid}/editar" class="action-btn" title="Editar">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>
                            <form action="/editor/lecciones/{lid}/eliminar" method="post" onsubmit="return confirm('¿Eliminar esta lección?');" style="display:contents">
                                <button type="submit" class="action-btn" title="Eliminar">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3,6 5,6 21,6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                """)
            doc = tr_re.sub('\n'.join(l_html), doc)

    elif template_rel_path == 'editor/leccion-form.html':
        es_nueva = bool(ctx.get('esNueva', True))
        lform = ctx.get('leccionForm') or {}
        lid = lform.get('id', '')
        ltit = html.escape(str(lform.get('titulo', '') or ''))
        ldesc = html.escape(str(lform.get('descripcion', '') or ''))
        lurl = html.escape(str(lform.get('contenidoUrl', '') or ''))
        lorden = lform.get('orden', 1) or 1
        lvista = html.escape(str(lform.get('vista', '') or ''))
        lpub = bool(lform.get('publicada', True))

        titulo_txt = 'Nueva lección' if es_nueva else 'Editar lección'
        sub_txt = 'Crea contenido nuevo para los estudiantes.' if es_nueva else 'Modifica esta lección.'
        btn_txt = 'Crear lección' if es_nueva else 'Guardar cambios'
        action_url = '/editor/lecciones/nueva' if es_nueva else f'/editor/lecciones/{lid}/editar'

        doc = re.sub(r'<title[^>]*>.*?</title>', f'<title>HandApp — {titulo_txt}</title>', doc, flags=re.DOTALL)
        doc = re.sub(r'<div class="form-title"[^>]*>.*?</div>', f'<div class="form-title">{titulo_txt}</div>', doc)
        doc = re.sub(r'<div class="form-sub"[^>]*>.*?</div>', f'<div class="form-sub">{sub_txt}</div>', doc)
        doc = re.sub(r'<form[^>]*>', f'<form action="{action_url}" method="post">', doc, count=1)
        doc = re.sub(r'<input type="hidden"[^>]*_csrf[^>]*/>', '', doc)
        doc = re.sub(
            r'<input type="text"\s+th:field="\*\s*\{titulo\}"[^>]*>',
            f'<input type="text" name="titulo" value="{ltit}" placeholder="Ej. Saludos Básicos" required>',
            doc,
        )
        doc = re.sub(
            r'<textarea\s+th:field="\*\s*\{descripcion\}"[^>]*>.*?</textarea>',
            f'<textarea name="descripcion" placeholder="Breve descripción de la lección" required>{ldesc}</textarea>',
            doc,
            flags=re.DOTALL,
        )
        doc = re.sub(
            r'<input type="text"\s+th:field="\*\s*\{contenidoUrl\}"[^>]*>',
            f'<input type="text" name="contenidoUrl" value="{lurl}" placeholder="https://...">',
            doc,
        )
        doc = re.sub(
            r'<input type="number"\s+th:field="\*\s*\{orden\}"[^>]*>',
            f'<input type="number" name="orden" value="{lorden}" placeholder="1" min="1" required>',
            doc,
        )
        doc = re.sub(
            r'<input type="text"\s+th:field="\*\s*\{vista\}"[^>]*>',
            f'<input type="text" name="vista" value="{lvista}" placeholder="ej. leccion-saludos">',
            doc,
        )
        chk = 'checked' if lpub else ''
        doc = re.sub(
            r'<input type="checkbox"\s+id="publicada"[^>]*>',
            f'<input type="checkbox" id="publicada" name="publicada" value="true" {chk}>',
            doc,
        )
        doc = re.sub(
            r'<button type="submit"\s+class="btn btn-primary"[^>]*>.*?</button>',
            f'<button type="submit" class="btn btn-primary">{btn_txt}</button>',
            doc,
            flags=re.DOTALL,
        )

    elif template_rel_path == 'editor/reportes.html':
        datos = ctx.get('datos', [])
        card_re = re.compile(r'<div class="card"\s+th:each="item\s*:\s*\$\{datos\}"[^>]*>.*?</div>\s*</div>\s*</div>', re.DOTALL)
        grid_re = re.compile(r'(<div class="grid">)(.*?)(</div>\s*</body>)', re.DOTALL)
        if grid_re.search(doc):
            c_html = []
            for item in datos:
                itit = html.escape(str(item['titulo']))
                ipct = int(item.get('pctCompletado', 0))
                iprom = item.get('promedioPuntaje')
                icomp = int(item.get('totalCompletados', 0))
                iintentos = int(item.get('totalIntentos', 0))
                if iprom is not None:
                    score_html = f"""
                    <div class="score-head"><span>Puntaje promedio</span><span>{iprom}/100</span></div>
                    <div class="score-track"><div class="score-fill" style="width:{iprom}%"></div></div>
                    """
                else:
                    score_html = """
                    <div class="score-head"><span>Puntaje promedio</span></div>
                    <div class="score-empty">Sin datos todavía</div>
                    """
                c_html.append(f"""
                <div class="card">
                    <div class="card-title">{itit}</div>
                    <div class="donut" style="background: conic-gradient(var(--accent) {ipct}%, var(--track) 0)">
                        <div class="donut-hole">
                            <span class="donut-pct">{ipct}%</span>
                            <span class="donut-label">Completado</span>
                        </div>
                    </div>
                    <div class="score-wrap">{score_html}</div>
                    <div class="card-footer">
                        <strong>{icomp}</strong> completados de <strong>{iintentos}</strong> intentos
                    </div>
                </div>
                """)
            doc = grid_re.sub(lambda m: f'{m.group(1)}\n{"".join(c_html)}\n{m.group(3)}', doc)

    # 5. Bucles de lecciones en index.html (menú desktop y móvil)
    desktop_loop_re = re.compile(
        r'<a\s+[^>]*th:each="leccion\s*:\s*\$\{lecciones\}"[^>]*class="dropdown-item"[^>]*>.*?</a>',
        re.DOTALL,
    )
    if desktop_loop_re.search(doc):
        items_html = []
        for lec in lecciones:
            orden_str = f"{int(lec['orden']):02d}"
            titulo_str = html.escape(str(lec['titulo']))
            pct = int(lec.get('progreso_pct', 0))
            items_html.append(
                f'<a href="/estudiante/lecciones/{lec["id"]}" class="dropdown-item">'
                f'<span class="lesson-number">{orden_str}</span>'
                f'<span class="lesson-title">{titulo_str}</span>'
                f'<span class="lesson-progress"><span class="progress-bar" style="--progress: {pct}%"></span></span>'
                f'</a>'
            )
        doc = desktop_loop_re.sub('\n'.join(items_html), doc)

    mobile_loop_re = re.compile(
        r'<a\s+[^>]*th:each="leccion\s*:\s*\$\{lecciones\}"[^>]*>Lección</a>',
        re.DOTALL,
    )
    if mobile_loop_re.search(doc):
        m_items = [
            f'<a href="/estudiante/lecciones/{lec["id"]}">Lección {lec["orden"]}: {html.escape(str(lec["titulo"]))}</a>'
            for lec in lecciones
        ]
        doc = mobile_loop_re.sub('\n'.join(m_items), doc)

    # 6. Variables simples th:text="${var}"
    simple_vars = (
        'inicial', 'nombreUsuario', 'inicialAdmin', 'nombreAdmin', 'correoAdmin',
        'inicialEditor', 'nombreEditor', 'correoEditor', 'totalUsuarios',
        'totalEstudiantes', 'totalEditores', 'totalAdministradores',
        'totalLecciones', 'totalPublicadas', 'totalBorrador',
    )
    for sv in simple_vars:
        if sv in ctx:
            val_esc = html.escape(str(ctx[sv]))
            doc = re.sub(
                rf'(<[^>]*\bth:text="\$\{{{sv}\}}"[^>]*>).*?(</[a-zA-Z0-9]+>)',
                lambda m, v=val_esc: f'{m.group(1)}{v}{m.group(2)}',
                doc,
            )

    nombre_usuario = html.escape(str(ctx.get('nombreUsuario', 'Estudiante')))
    leccion_obj = ctx.get('leccion') or {'id': 1, 'titulo': 'Saludos en LSC', 'orden': 1}
    leccion_titulo = html.escape(str(leccion_obj.get('titulo', 'Lección')))

    doc = re.sub(
        r'(<[^>]*\bth:text="\'Hola, \'\s*\+\s*\$\{nombreUsuario\}"[^>]*>).*?(</[a-zA-Z0-9]+>)',
        lambda m: f'{m.group(1)}Hola, {nombre_usuario}{m.group(2)}',
        doc,
    )
    doc = re.sub(
        r'(<title[^>]*\bth:text="\$\{leccion\.titulo\}\s*\+\s*\' - HandApp\'"[^>]*>).*?(</title>)',
        lambda m: f'{m.group(1)}{leccion_titulo} - HandApp{m.group(2)}',
        doc,
    )
    doc = re.sub(
        r'(<[^>]*\bth:text="\$\{leccion\.titulo\}"[^>]*>).*?(</[a-zA-Z0-9]+>)',
        lambda m: f'{m.group(1)}{leccion_titulo}{m.group(2)}',
        doc,
    )

    # Meta CSRF en leccion-saludos.html
    doc = re.sub(r'th:content="\$\{_csrf\.token\}"', 'content="django-csrf-token"', doc)
    doc = re.sub(r'th:content="\$\{_csrf\.headerName\}"', 'content="X-CSRFToken"', doc)

    # th:field="*{nombre}" en register.html
    doc = re.sub(
        r'th:field="\*\s*\{([^}]+)\}"',
        lambda m: f'name="{m.group(1)}"',
        doc,
    )

    # th:href="@{...}", th:src="@{...}", th:action="@{...}"
    for attr in ('href', 'src', 'action'):
        def _replace_th_url(match, a=attr):
            url = match.group(1)
            return f'{a}="{url}"'
        doc = re.sub(rf'\bth:{attr}="\@\{{([^"}}]+)\}}"', _replace_th_url, doc)

    # Atributos de búsqueda y estado en index.html
    doc = doc.replace('th:attr="data-authenticated=${logueado}"', f'data-authenticated="{str(logueado).lower()}"')
    doc = doc.replace('th:attr="aria-disabled=${!logueado}"', f'aria-disabled="{str(not logueado).lower()}"')
    if logueado:
        doc = doc.replace('th:disabled="${!logueado}"', '')
    else:
        doc = doc.replace('th:disabled="${!logueado}"', 'disabled="disabled"')

    return HttpResponse(doc, content_type='text/html; charset=utf-8')
