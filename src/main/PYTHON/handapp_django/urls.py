"""
Rutas URL de Django para HandApp + servidor de archivos estáticos originales
(styles.css, tema.css, CSS/*, JS/*, IMG/*, MODELS/*) con soporte de Range HTTP para video MP4.
"""
import mimetypes
import os
import re
from pathlib import Path
from django.urls import path, re_path
from django.http import FileResponse, Http404, HttpResponse, StreamingHttpResponse
from . import views
from .settings import STATIC_DIR


def _range_file_iterator(file_obj, offset: int, length: int, chunk_size: int = 65536):
    file_obj.seek(offset)
    remaining = length
    while remaining > 0:
        read_len = min(chunk_size, remaining)
        data = file_obj.read(read_len)
        if not data:
            break
        remaining -= len(data)
        yield data
    file_obj.close()


def serve_root_static(request, rel_path: str):
    target = (STATIC_DIR / rel_path).resolve()
    if not target.is_file():
        # Fallback automatico a subcarpetas organizadas (CSS/ e IMG/abecedario/)
        if rel_path.endswith('.css') and '/' not in rel_path:
            target = (STATIC_DIR / 'CSS' / rel_path).resolve()
        elif rel_path.startswith('IMG/') and rel_path.count('/') == 1:
            filename = rel_path.split('/', 1)[1]
            target = (STATIC_DIR / 'IMG' / 'abecedario' / filename).resolve()
    if not str(target).startswith(str(STATIC_DIR.resolve())) or not target.is_file():
        raise Http404("Archivo estático no encontrado")

    content_type, _ = mimetypes.guess_type(str(target))
    content_type = content_type or 'application/octet-stream'
    file_size = os.path.getsize(target)

    range_header = request.headers.get('Range', '').strip()
    if range_header:
        m = re.match(r'bytes=(\d+)-(\d*)', range_header)
        if m:
            start = int(m.group(1))
            end = int(m.group(2)) if m.group(2) else file_size - 1
            end = min(end, file_size - 1)
            if start <= end and start < file_size:
                length = end - start + 1
                f = open(target, 'rb')
                resp = StreamingHttpResponse(
                    _range_file_iterator(f, start, length),
                    status=206,
                    content_type=content_type,
                )
                resp['Content-Length'] = str(length)
                resp['Content-Range'] = f'bytes {start}-{end}/{file_size}'
                resp['Accept-Ranges'] = 'bytes'
                return resp

    resp = FileResponse(open(target, 'rb'), content_type=content_type)
    resp['Accept-Ranges'] = 'bytes'
    resp['Content-Length'] = str(file_size)
    return resp


urlpatterns = [
    path('', views.inicio, name='inicio'),
    path('abecedario', views.abecedario, name='abecedario'),
    path('biblioteca', views.biblioteca, name='biblioteca'),
    path('traductor', views.traductor, name='traductor'),
    path('video', views.video_page_or_stream, name='video'),
    path('letra', views.letra_api, name='letra'),
    path('login', views.login_view, name='login'),
    path('registro', views.registro_view, name='registro'),
    path('recuperar-cuenta', views.recuperar_cuenta_view, name='recuperar_cuenta'),
    path('auth/google', views.google_auth_view, name='google_auth'),
    path('auth/social', views.google_auth_view, name='social_auth'),
    path('auth/apple', views.google_auth_view, name='apple_auth'),
    path('logout', views.logout_view, name='logout'),
    path('estudiante/lecciones', views.estudiante_lecciones, name='estudiante_lecciones'),
    path('estudiante/lecciones/<int:leccion_id>', views.estudiante_leccion_detalle, name='estudiante_leccion_detalle'),
    path('estudiante/lecciones/<int:leccion_id>/completar', views.completar_leccion, name='completar_leccion'),

    # Panel Administrador (@handapp.com)
    path('admin/panel', views.admin_panel, name='admin_panel'),
    path('admin/usuarios/nuevo', views.admin_usuario_nuevo, name='admin_usuario_nuevo'),
    path('admin/usuarios/<int:usuario_id>/editar', views.admin_usuario_editar, name='admin_usuario_editar'),
    path('admin/usuarios/<int:usuario_id>/eliminar', views.admin_usuario_eliminar, name='admin_usuario_eliminar'),
    path('admin/reportes', views.admin_reportes, name='admin_reportes'),
    path('admin/reportes/exportar/<str:formato>', views.admin_exportar_reporte, name='admin_exportar_reporte'),

    # Panel Editor (@editorhand.com)
    path('editor/panel', views.editor_panel, name='editor_panel'),
    path('editor/lecciones/nueva', views.editor_leccion_nueva, name='editor_leccion_nueva'),
    path('editor/lecciones/<int:leccion_id>/editar', views.editor_leccion_editar, name='editor_leccion_editar'),
    path('editor/lecciones/<int:leccion_id>/eliminar', views.editor_leccion_eliminar, name='editor_leccion_eliminar'),
    path('editor/reportes', views.editor_reportes, name='editor_reportes'),

    # Archivos estáticos en las mismas rutas exactas que usa Spring Boot (/styles.css, /tema.css, /CSS/*, /JS/*, /IMG/*)
    re_path(r'^(?P<rel_path>(?:CSS|JS|IMG|MODELS)/.+)$', serve_root_static),
    re_path(r'^(?P<rel_path>[^/]+\.(?:css|js|png|jpg|jpeg|svg|ico|glb|gltf|mp4|webm))$', serve_root_static),
]
