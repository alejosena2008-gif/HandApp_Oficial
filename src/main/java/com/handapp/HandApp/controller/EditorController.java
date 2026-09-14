package com.handapp.HandApp.controller;

import com.handapp.HandApp.dto.LeccionFormDTO;
import com.handapp.HandApp.dto.ResumenLeccionDTO;
import com.handapp.HandApp.model.Leccion;
import com.handapp.HandApp.model.Usuario;
import com.handapp.HandApp.repository.LeccionRepository;
import com.handapp.HandApp.repository.UsuarioRepository;
import com.handapp.HandApp.service.LeccionService;
import com.handapp.HandApp.service.ProgresoEstudianteService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;

import java.util.ArrayList;
import java.util.List;
import java.util.Map;

@Controller
@RequestMapping("/editor")
public class EditorController {

    @Autowired
    private LeccionRepository leccionRepository;

    @Autowired
    private LeccionService leccionService;

    @Autowired
    private UsuarioRepository usuarioRepository;

    @Autowired
    private ProgresoEstudianteService progresoEstudianteService;

    @GetMapping("/panel")
    public String panel(Model model) {
        List<Leccion> lecciones = leccionRepository.findAllByOrderByOrdenAsc();
        long publicadas = lecciones.stream().filter(Leccion::isPublicada).count();

        model.addAttribute("lecciones", lecciones);
        model.addAttribute("totalLecciones", lecciones.size());
        model.addAttribute("totalPublicadas", publicadas);
        model.addAttribute("totalBorrador", lecciones.size() - publicadas);

        agregarDatosEditorLogueado(model);
        return "editor/panel";
    }

    // ── Crear lección ──

    @GetMapping("/lecciones/nueva")
    public String mostrarFormularioNueva(Model model) {
        model.addAttribute("leccionForm", new LeccionFormDTO());
        model.addAttribute("esNueva", true);
        agregarDatosEditorLogueado(model);
        return "editor/leccion-form";
    }

    @PostMapping("/lecciones/nueva")
    public String crearLeccion(@ModelAttribute("leccionForm") LeccionFormDTO form,
                                RedirectAttributes redirectAttributes) {
        leccionService.crear(form, usuarioLogueado());
        redirectAttributes.addFlashAttribute("mensaje", "Lección creada correctamente.");
        return "redirect:/editor/panel";
    }

    // ── Editar lección ──

    @GetMapping("/lecciones/{id}/editar")
    public String mostrarFormularioEditar(@PathVariable Long id, Model model) {
        Leccion leccion = leccionRepository.findById(id)
                .orElseThrow(() -> new IllegalArgumentException("Lección no encontrada"));

        LeccionFormDTO form = new LeccionFormDTO();
        form.setId(leccion.getId());
        form.setTitulo(leccion.getTitulo());
        form.setDescripcion(leccion.getDescripcion());
        form.setContenidoUrl(leccion.getContenidoUrl());
        form.setOrden(leccion.getOrden());
        form.setPublicada(leccion.isPublicada());

        model.addAttribute("leccionForm", form);
        model.addAttribute("esNueva", false);
        agregarDatosEditorLogueado(model);
        return "editor/leccion-form";
    }

    @PostMapping("/lecciones/{id}/editar")
    public String actualizarLeccion(@PathVariable Long id,
                                     @ModelAttribute("leccionForm") LeccionFormDTO form,
                                     RedirectAttributes redirectAttributes) {
        leccionService.actualizar(id, form);
        redirectAttributes.addFlashAttribute("mensaje", "Lección actualizada correctamente.");
        return "redirect:/editor/panel";
    }

    // ── Eliminar lección ──

    @PostMapping("/lecciones/{id}/eliminar")
    public String eliminarLeccion(@PathVariable Long id, RedirectAttributes redirectAttributes) {
        leccionService.eliminar(id);
        redirectAttributes.addFlashAttribute("mensaje", "Lección eliminada correctamente.");
        return "redirect:/editor/panel";
    }

    // ── Reportes (agregado, sin datos personales) ──

    @GetMapping("/reportes")
    public String reportes(Model model) {
        List<Leccion> lecciones = leccionRepository.findAllByOrderByOrdenAsc();
        Map<Long, Object[]> resumen = progresoEstudianteService.obtenerResumenPorLeccionComoMapa();

        List<ResumenLeccionDTO> datos = new ArrayList<>();
        for (Leccion leccion : lecciones) {
            Object[] fila = resumen.get(leccion.getId());
            ResumenLeccionDTO dto = new ResumenLeccionDTO();
            dto.setLeccionId(leccion.getId());
            dto.setTitulo(leccion.getTitulo());

            if (fila != null) {
                long totalIntentos = (long) fila[1];
                long totalCompletados = fila[2] != null ? (long) fila[2] : 0;
                Double promedio = (Double) fila[3];

                dto.setTotalIntentos(totalIntentos);
                dto.setTotalCompletados(totalCompletados);
                dto.setPctCompletado(totalIntentos == 0 ? 0 : (int) Math.round((totalCompletados * 100.0) / totalIntentos));
                dto.setPromedioPuntaje(promedio != null ? (int) Math.round(promedio) : null);
            } else {
                dto.setTotalIntentos(0);
                dto.setTotalCompletados(0);
                dto.setPctCompletado(0);
                dto.setPromedioPuntaje(null);
            }
            datos.add(dto);
        }

        model.addAttribute("datos", datos);
        agregarDatosEditorLogueado(model);
        return "editor/reportes";
    }

    // ── Utilidad ──

    private Usuario usuarioLogueado() {
        Authentication auth = SecurityContextHolder.getContext().getAuthentication();
        return usuarioRepository.findByCorreo(auth.getName()).orElse(null);
    }

    private void agregarDatosEditorLogueado(Model model) {
        Usuario editor = usuarioLogueado();
        if (editor != null) {
            model.addAttribute("nombreEditor", editor.getNombre());
            model.addAttribute("inicialEditor", editor.getNombre().substring(0, 1).toUpperCase());
            model.addAttribute("correoEditor", editor.getCorreo());
        }
    }
}