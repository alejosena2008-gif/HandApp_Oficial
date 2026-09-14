package com.handapp.HandApp.controller;

import com.handapp.HandApp.dto.ProgresoLeccionDTO;
import com.handapp.HandApp.dto.ReporteUsuarioDTO;
import com.handapp.HandApp.dto.UsuarioFormDTO;
import com.handapp.HandApp.model.ProgresoEstudiante;
import com.handapp.HandApp.model.Usuario;
import com.handapp.HandApp.repository.UsuarioRepository;
import com.handapp.HandApp.service.ProgresoEstudianteService;
import com.handapp.HandApp.service.ReporteExportService;
import com.handapp.HandApp.service.UsuarioService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpHeaders;
import org.springframework.http.MediaType;
import org.springframework.http.ResponseEntity;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;

import java.io.IOException;
import java.util.ArrayList;
import java.util.List;

@Controller
@RequestMapping("/admin")
public class AdminController {

    @Autowired
    private UsuarioRepository usuarioRepository;

    @Autowired
    private UsuarioService usuarioService;

    @Autowired
    private ProgresoEstudianteService progresoEstudianteService;

    @Autowired
    private ReporteExportService reporteExportService;

    @GetMapping("/panel")
    public String panel(Model model) {
        List<Usuario> usuarios = usuarioRepository.findAll();
        long total = usuarios.size();

        long estudiantes = usuarios.stream().filter(u -> u.getRol() == Usuario.Rol.ESTUDIANTE).count();
        long editores = usuarios.stream().filter(u -> u.getRol() == Usuario.Rol.EDITOR).count();
        long administradores = usuarios.stream().filter(u -> u.getRol() == Usuario.Rol.ADMINISTRADOR).count();

        model.addAttribute("usuarios", usuarios);
        model.addAttribute("totalUsuarios", total);
        model.addAttribute("totalEstudiantes", estudiantes);
        model.addAttribute("totalEditores", editores);
        model.addAttribute("totalAdministradores", administradores);

        model.addAttribute("pctEstudiantes", total == 0 ? 0 : Math.round((estudiantes * 100.0) / total));
        model.addAttribute("pctEditores", total == 0 ? 0 : Math.round((editores * 100.0) / total));
        model.addAttribute("pctAdministradores", total == 0 ? 0 : Math.round((administradores * 100.0) / total));

        agregarDatosAdminLogueado(model);

        return "admin/panel";
    }

    // ── Crear usuario ──

    @GetMapping("/usuarios/nuevo")
    public String mostrarFormularioNuevo(Model model) {
        model.addAttribute("usuarioForm", new UsuarioFormDTO());
        model.addAttribute("esNuevo", true);
        model.addAttribute("roles", Usuario.Rol.values());
        agregarDatosAdminLogueado(model);
        return "admin/usuario-form";
    }

    @PostMapping("/usuarios/nuevo")
    public String crearUsuario(@ModelAttribute("usuarioForm") UsuarioFormDTO form,
                                RedirectAttributes redirectAttributes,
                                Model model) {
        try {
            usuarioService.crearDesdeAdmin(form);
            redirectAttributes.addFlashAttribute("mensaje", "Usuario creado correctamente.");
            return "redirect:/admin/panel";
        } catch (IllegalStateException e) {
            model.addAttribute("error", e.getMessage());
            model.addAttribute("esNuevo", true);
            model.addAttribute("roles", Usuario.Rol.values());
            agregarDatosAdminLogueado(model);
            return "admin/usuario-form";
        }
    }

    // ── Editar usuario ──

    @GetMapping("/usuarios/{id}/editar")
    public String mostrarFormularioEditar(@PathVariable Long id, Model model) {
        Usuario usuario = usuarioRepository.findById(id)
                .orElseThrow(() -> new IllegalArgumentException("Usuario no encontrado"));

        UsuarioFormDTO form = new UsuarioFormDTO();
        form.setId(usuario.getId());
        form.setNombre(usuario.getNombre());
        form.setCorreo(usuario.getCorreo());
        form.setRol(usuario.getRol());

        model.addAttribute("usuarioForm", form);
        model.addAttribute("esNuevo", false);
        model.addAttribute("roles", Usuario.Rol.values());
        agregarDatosAdminLogueado(model);
        return "admin/usuario-form";
    }

    @PostMapping("/usuarios/{id}/editar")
    public String actualizarUsuario(@PathVariable Long id,
                                     @ModelAttribute("usuarioForm") UsuarioFormDTO form,
                                     RedirectAttributes redirectAttributes,
                                     Model model) {
        try {
            usuarioService.actualizar(id, form);
            redirectAttributes.addFlashAttribute("mensaje", "Usuario actualizado correctamente.");
            return "redirect:/admin/panel";
        } catch (IllegalStateException | IllegalArgumentException e) {
            model.addAttribute("error", e.getMessage());
            model.addAttribute("esNuevo", false);
            model.addAttribute("roles", Usuario.Rol.values());
            agregarDatosAdminLogueado(model);
            return "admin/usuario-form";
        }
    }

    // ── Eliminar usuario ──

    @PostMapping("/usuarios/{id}/eliminar")
    public String eliminarUsuario(@PathVariable Long id, RedirectAttributes redirectAttributes) {
        Authentication auth = SecurityContextHolder.getContext().getAuthentication();
        String correoActual = auth.getName();

        Usuario objetivo = usuarioRepository.findById(id).orElse(null);

        if (objetivo != null && objetivo.getCorreo().equals(correoActual)) {
            redirectAttributes.addFlashAttribute("error", "No puedes eliminar tu propia cuenta.");
            return "redirect:/admin/panel";
        }

        usuarioService.eliminar(id);
        redirectAttributes.addFlashAttribute("mensaje", "Usuario eliminado correctamente.");
        return "redirect:/admin/panel";
    }

    // ── Reportes ──

    @GetMapping("/reportes")
    public String reportes(@RequestParam(required = false) String busqueda,
                            @RequestParam(required = false) Usuario.Rol rol,
                            Model model) {
        List<ReporteUsuarioDTO> filas = construirFilasReporte(busqueda, rol);

        model.addAttribute("filas", filas);
        model.addAttribute("busqueda", busqueda);
        model.addAttribute("rolSeleccionado", rol);
        model.addAttribute("roles", Usuario.Rol.values());
        agregarDatosAdminLogueado(model);
        return "admin/reportes";
    }

    @GetMapping("/reportes/exportar/excel")
    public ResponseEntity<byte[]> exportarExcel(@RequestParam(required = false) String busqueda,
                                                 @RequestParam(required = false) Usuario.Rol rol) throws IOException {
        List<ReporteUsuarioDTO> filas = construirFilasReporte(busqueda, rol);
        byte[] archivo = reporteExportService.generarExcel(filas);

        return ResponseEntity.ok()
                .header(HttpHeaders.CONTENT_DISPOSITION, "attachment; filename=reporte-usuarios.xlsx")
                .contentType(MediaType.parseMediaType("application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"))
                .body(archivo);
    }

    @GetMapping("/reportes/exportar/word")
    public ResponseEntity<byte[]> exportarWord(@RequestParam(required = false) String busqueda,
                                                @RequestParam(required = false) Usuario.Rol rol) throws IOException {
        List<ReporteUsuarioDTO> filas = construirFilasReporte(busqueda, rol);
        byte[] archivo = reporteExportService.generarWord(filas);

        return ResponseEntity.ok()
                .header(HttpHeaders.CONTENT_DISPOSITION, "attachment; filename=reporte-usuarios.docx")
                .contentType(MediaType.parseMediaType("application/vnd.openxmlformats-officedocument.wordprocessingml.document"))
                .body(archivo);
    }

    @GetMapping("/reportes/exportar/pdf")
    public ResponseEntity<byte[]> exportarPdf(@RequestParam(required = false) String busqueda,
                                               @RequestParam(required = false) Usuario.Rol rol) throws Exception {
        List<ReporteUsuarioDTO> filas = construirFilasReporte(busqueda, rol);
        byte[] archivo = reporteExportService.generarPdf(filas);

        return ResponseEntity.ok()
                .header(HttpHeaders.CONTENT_DISPOSITION, "attachment; filename=reporte-usuarios.pdf")
                .contentType(MediaType.APPLICATION_PDF)
                .body(archivo);
    }

    private List<ReporteUsuarioDTO> construirFilasReporte(String busqueda, Usuario.Rol rol) {
        List<Usuario> usuarios = usuarioRepository.findAll();

        String busquedaLower = busqueda != null ? busqueda.trim().toLowerCase() : null;
        List<Usuario> usuariosFiltrados = usuarios.stream()
                .filter(u -> rol == null || u.getRol() == rol)
                .filter(u -> busquedaLower == null || busquedaLower.isBlank()
                        || u.getNombre().toLowerCase().contains(busquedaLower)
                        || u.getCorreo().toLowerCase().contains(busquedaLower))
                .toList();

        List<ReporteUsuarioDTO> filas = new ArrayList<>();
        for (Usuario usuario : usuariosFiltrados) {
            ReporteUsuarioDTO fila = new ReporteUsuarioDTO();
            fila.setId(usuario.getId());
            fila.setNombre(usuario.getNombre());
            fila.setCorreo(usuario.getCorreo());
            fila.setRol(usuario.getRol());

            List<ProgresoLeccionDTO> progresos = new ArrayList<>();
            if (usuario.getRol() == Usuario.Rol.ESTUDIANTE) {
                List<ProgresoEstudiante> registros =
                        progresoEstudianteService.obtenerProgresoDetalladoDeEstudiante(usuario);

                int completadas = 0;
                double sumaPuntajes = 0;
                int cantidadPuntajes = 0;

                for (ProgresoEstudiante p : registros) {
                    ProgresoLeccionDTO pl = new ProgresoLeccionDTO();
                    pl.setTituloLeccion(p.getLeccion().getTitulo());
                    pl.setCompletado(p.isCompletado());
                    pl.setPuntaje(p.getPuntaje());
                    pl.setPreguntaActual(p.getPreguntaActual());
                    progresos.add(pl);

                    if (p.isCompletado()) {
                        completadas++;
                        if (p.getPuntaje() != null) {
                            sumaPuntajes += p.getPuntaje();
                            cantidadPuntajes++;
                        }
                    }
                }

                fila.setLeccionesCompletadas(completadas);
                fila.setPromedioPuntaje(cantidadPuntajes == 0 ? null : sumaPuntajes / cantidadPuntajes);
            }
            fila.setProgresos(progresos);
            filas.add(fila);
        }
        return filas;
    }

    // ── Utilidad ──

    private void agregarDatosAdminLogueado(Model model) {
        Authentication auth = SecurityContextHolder.getContext().getAuthentication();
        String correo = auth.getName();
        usuarioRepository.findByCorreo(correo).ifPresent(admin -> {
            model.addAttribute("nombreAdmin", admin.getNombre());
            model.addAttribute("inicialAdmin", admin.getNombre().substring(0, 1).toUpperCase());
            model.addAttribute("correoAdmin", admin.getCorreo());
        });
    }
}