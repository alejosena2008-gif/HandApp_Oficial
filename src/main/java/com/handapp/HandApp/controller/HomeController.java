package com.handapp.HandApp.controller;

import com.handapp.HandApp.model.Leccion;
import com.handapp.HandApp.repository.LeccionRepository;
import com.handapp.HandApp.repository.UsuarioRepository;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;

import java.util.List;

@Controller
public class HomeController {

    @Autowired
    private UsuarioRepository usuarioRepository;

    @Autowired
    private LeccionRepository leccionRepository;

    @GetMapping("/")
    public String inicio(Model model) {
        Authentication auth = SecurityContextHolder.getContext().getAuthentication();
        boolean logueado = auth != null && auth.isAuthenticated()
                && !"anonymousUser".equals(auth.getPrincipal());

        model.addAttribute("logueado", logueado);

        if (logueado) {
            String correo = auth.getName();
            usuarioRepository.findByCorreo(correo).ifPresent(usuario -> {
                model.addAttribute("nombreUsuario", usuario.getNombre());
                model.addAttribute("inicial", usuario.getNombre().substring(0, 1).toUpperCase());
            });
        }

        List<Leccion> lecciones = leccionRepository.findAllByPublicadaTrueOrderByOrdenAsc();
        model.addAttribute("lecciones", lecciones);

        return "index";
    }
}