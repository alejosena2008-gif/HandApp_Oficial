package com.handapp.HandApp.service;

import com.handapp.HandApp.dto.RegistroDTO;
import com.handapp.HandApp.dto.UsuarioFormDTO;
import org.springframework.security.crypto.password.PasswordEncoder;
import com.handapp.HandApp.model.Usuario;
import com.handapp.HandApp.repository.ProgresoEstudianteRepository;
import com.handapp.HandApp.repository.UsuarioRepository;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.security.core.authority.SimpleGrantedAuthority;
import org.springframework.security.core.userdetails.User;
import org.springframework.security.core.userdetails.UserDetails;
import org.springframework.security.core.userdetails.UserDetailsService;
import org.springframework.security.core.userdetails.UsernameNotFoundException;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

@Service
public class UsuarioService implements UserDetailsService {

    private static final String DOMINIO_ADMIN  = "@handapp.com";
    private static final String DOMINIO_EDITOR = "@editorhand.com";

    @Autowired
    private UsuarioRepository usuarioRepository;

    @Autowired
    private ProgresoEstudianteRepository progresoEstudianteRepository;

    @Autowired
    private PasswordEncoder passwordEncoder;

    @Override
    public UserDetails loadUserByUsername(String correo) throws UsernameNotFoundException {
        Usuario usuario = usuarioRepository.findByCorreo(correo)
                .orElseThrow(() -> new UsernameNotFoundException("Usuario no encontrado: " + correo));

        return User.builder()
                .username(usuario.getCorreo())
                .password(usuario.getContrasena())
                .authorities(new SimpleGrantedAuthority("ROLE_" + usuario.getRol().name()))
                .build();
    }

    public void registrar(RegistroDTO dto) {
        Usuario usuario = new Usuario();
        usuario.setNombre(dto.getNombre());
        usuario.setCorreo(dto.getCorreo());
        usuario.setContrasena(passwordEncoder.encode(dto.getContrasena()));
        usuario.setRol(Usuario.Rol.ESTUDIANTE); // todo registro público entra como estudiante
        usuarioRepository.save(usuario);
    }

    // ── Gestión desde el panel de administrador ──

    public void crearDesdeAdmin(UsuarioFormDTO dto) {
        if (usuarioRepository.findByCorreo(dto.getCorreo()).isPresent()) {
            throw new IllegalStateException("Ese correo ya está registrado.");
        }
        validarDominioPorRol(dto.getCorreo(), dto.getRol());

        Usuario usuario = new Usuario();
        usuario.setNombre(dto.getNombre());
        usuario.setCorreo(dto.getCorreo());
        usuario.setContrasena(passwordEncoder.encode(dto.getContrasena()));
        usuario.setRol(dto.getRol());
        usuarioRepository.save(usuario);
    }

    public void actualizar(Long id, UsuarioFormDTO dto) {
        Usuario usuario = usuarioRepository.findById(id)
                .orElseThrow(() -> new IllegalArgumentException("Usuario no encontrado"));

        if (usuarioRepository.existsByCorreoAndIdNot(dto.getCorreo(), id)) {
            throw new IllegalStateException("Ese correo ya está en uso por otro usuario.");
        }
        validarDominioPorRol(dto.getCorreo(), dto.getRol());

        usuario.setNombre(dto.getNombre());
        usuario.setCorreo(dto.getCorreo());
        usuario.setRol(dto.getRol());

        if (dto.getContrasena() != null && !dto.getContrasena().isBlank()) {
            usuario.setContrasena(passwordEncoder.encode(dto.getContrasena()));
        }

        usuarioRepository.save(usuario);
    }

    @Transactional
    public void eliminar(Long id) {
        Usuario usuario = usuarioRepository.findById(id)
                .orElseThrow(() -> new IllegalArgumentException("Usuario no encontrado"));

        // Si el usuario es estudiante, primero borramos su progreso relacionado
        // (evita el error de foreign key al eliminar el usuario)
        if (usuario.getRol() == Usuario.Rol.ESTUDIANTE) {
            progresoEstudianteRepository.deleteAll(
                    progresoEstudianteRepository.findByEstudianteOrderByFechaCompletadoDesc(usuario)
            );
        }

        usuarioRepository.deleteById(id);
    }

    // ── Validación de dominio según rol ──

    private void validarDominioPorRol(String correo, Usuario.Rol rol) {
        if (correo == null || rol == null) return;
        String correoLower = correo.toLowerCase();

        if (rol == Usuario.Rol.ADMINISTRADOR && !correoLower.endsWith(DOMINIO_ADMIN)) {
            throw new IllegalStateException(
                    "Un administrador debe tener un correo terminado en " + DOMINIO_ADMIN);
        }

        if (rol == Usuario.Rol.EDITOR && !correoLower.endsWith(DOMINIO_EDITOR)) {
            throw new IllegalStateException(
                    "Un editor debe tener un correo terminado en " + DOMINIO_EDITOR);
        }
    }
}