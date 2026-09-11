package com.handapp.HandApp.controller;

import com.handapp.HandApp.model.Usuario;
import com.handapp.HandApp.repository.UsuarioRepository;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;

import java.util.List;

@Controller
public class AdminController {

    @Autowired
    private UsuarioRepository usuarioRepository;

    @GetMapping("/admin/panel")
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

        // Datos del admin actualmente logueado (para el sidebar)
        Authentication auth = SecurityContextHolder.getContext().getAuthentication();
        String correo = auth.getName();
        usuarioRepository.findByCorreo(correo).ifPresent(admin -> {
            model.addAttribute("nombreAdmin", admin.getNombre());
            model.addAttribute("inicialAdmin", admin.getNombre().substring(0, 1).toUpperCase());
            model.addAttribute("correoAdmin", admin.getCorreo());
        });

        return "admin/panel";
    }
}