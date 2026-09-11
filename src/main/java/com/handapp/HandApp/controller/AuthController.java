package com.handapp.HandApp.controller;

import com.handapp.HandApp.dto.RegistroDTO;
import com.handapp.HandApp.service.UsuarioService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.ModelAttribute;
import org.springframework.web.bind.annotation.PostMapping;

@Controller
public class AuthController {

    @Autowired
    private UsuarioService usuarioService;

    @GetMapping("/login")
    public String login() {
        return "login"; // el propio HTML lee ?error, ?logout y ?registrado con Thymeleaf
    }

    @GetMapping("/registro")
    public String mostrarRegistro(@ModelAttribute("registroDTO") RegistroDTO registroDTO) {
        return "register";
    }

    @PostMapping("/registro")
    public String registrar(@ModelAttribute("registroDTO") RegistroDTO registroDTO, Model model) {

        if (registroDTO.getContrasena() == null
                || !registroDTO.getContrasena().equals(registroDTO.getConfirmarContrasena())) {
            model.addAttribute("error", "Las contraseñas no coinciden.");
            return "register";
        }

        try {
            usuarioService.registrar(registroDTO);
        } catch (Exception e) {
            // Ajusta el tipo de excepción si tu UsuarioService lanza una específica (ver nota abajo)
            model.addAttribute("error", "Ese correo ya está registrado.");
            return "register";
        }

        return "redirect:/login?registrado";
    }
}