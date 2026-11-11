package com.handapp.HandApp.controller;

import com.handapp.HandApp.model.Leccion;
import com.handapp.HandApp.model.ProgresoEstudiante;
import com.handapp.HandApp.model.Usuario;
import com.handapp.HandApp.repository.LeccionRepository;
import com.handapp.HandApp.repository.UsuarioRepository;
import com.handapp.HandApp.service.ProgresoEstudianteService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;

import java.util.List;
import java.util.Map;

@Controller
@RequestMapping("/estudiante")
public class EstudianteController {

    @Autowired
    private LeccionRepository leccionRepository;

    @Autowired
    private UsuarioRepository usuarioRepository;

    @Autowired
    private ProgresoEstudianteService progresoService;

    @GetMapping("/lecciones")
    public String listar(Model model) {
        Usuario estudiante = usuarioLogueado();
        List<Leccion> lecciones = leccionRepository.findAllByPublicadaTrueOrderByOrdenAsc();
        Map<Long, ProgresoEstudiante> progresoPorLeccion =
                progresoService.obtenerProgresoDeEstudianteComoMapa(estudiante.getId());

        model.addAttribute("lecciones", lecciones);
        model.addAttribute("progresoPorLeccion", progresoPorLeccion);
        model.addAttribute("nombreEstudiante", estudiante.getNombre());
        model.addAttribute("inicialEstudiante", estudiante.getNombre().substring(0, 1).toUpperCase());
        return "estudiante/lecciones";
    }

    @GetMapping("/lecciones/{id}")
    public String detalle(@PathVariable Long id, Model model) {
        Leccion leccion = leccionRepository.findById(id)
                .orElseThrow(() -> new IllegalArgumentException("Lección no encontrada"));
        Usuario estudiante = usuarioLogueado();

        Map<Long, ProgresoEstudiante> progresoMap =
                progresoService.obtenerProgresoDeEstudianteComoMapa(estudiante.getId());
        ProgresoEstudiante progreso = progresoMap.get(id); // puede ser null si nunca la ha intentado

        model.addAttribute("leccion", leccion);
        model.addAttribute("progreso", progreso);

        // Cada lección tiene su propio archivo HTML con el quiz escrito a mano.
        // Si todavía no se le asignó ese archivo (campo "vista" vacío), mostramos un aviso
        // en vez de dejar que Thymeleaf tire un error 500 buscando una plantilla que no existe.
        if (leccion.getVista() == null || leccion.getVista().isBlank()) {
            return "estudiante/leccion-pendiente";
        }
        return "estudiante/" + leccion.getVista();
    }

    // Se llama cada vez que el estudiante avanza una pregunta dentro del quiz (frontend)
    @PostMapping("/lecciones/{id}/progreso-parcial")
    public String guardarParcial(@PathVariable Long id,
                                  @RequestParam Integer preguntaActual,
                                  RedirectAttributes redirectAttributes) {
        Usuario estudiante = usuarioLogueado();
        progresoService.guardarProgresoParcial(estudiante.getId(), id, preguntaActual);
        redirectAttributes.addFlashAttribute("mensaje", "Progreso guardado (pregunta " + preguntaActual + ").");
        return "redirect:/estudiante/lecciones/" + id;
    }

    // Se llama cuando el estudiante termina las 15 preguntas del quiz
    @PostMapping("/lecciones/{id}/completar")
    public String completar(@PathVariable Long id,
                            @RequestParam Integer puntaje,
                            RedirectAttributes redirectAttributes) {
        Usuario estudiante = usuarioLogueado();
        progresoService.registrarProgreso(estudiante.getId(), id, puntaje);
        redirectAttributes.addFlashAttribute("mensaje", "¡Lección completada! Puntaje: " + puntaje);
        return "redirect:/estudiante/lecciones";
    }

    private Usuario usuarioLogueado() {
        Authentication auth = SecurityContextHolder.getContext().getAuthentication();
        return usuarioRepository.findByCorreo(auth.getName())
                .orElseThrow(() -> new IllegalStateException("Usuario no encontrado"));
    }
}