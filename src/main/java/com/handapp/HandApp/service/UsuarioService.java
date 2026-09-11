package com.handapp.HandApp.service;

import com.handapp.HandApp.dto.RegistroDTO;
import org.springframework.security.crypto.password.PasswordEncoder;
import com.handapp.HandApp.model.Usuario;
import com.handapp.HandApp.repository.UsuarioRepository;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.security.core.authority.SimpleGrantedAuthority;
import org.springframework.security.core.userdetails.User;
import org.springframework.security.core.userdetails.UserDetails;
import org.springframework.security.core.userdetails.UserDetailsService;
import org.springframework.security.core.userdetails.UsernameNotFoundException;
import org.springframework.stereotype.Service;

@Service
public class UsuarioService implements UserDetailsService {

    @Autowired
    private UsuarioRepository usuarioRepository;

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
    
    @Autowired
    private PasswordEncoder passwordEncoder;

    public void registrar(RegistroDTO dto) {
        Usuario usuario = new Usuario();
        usuario.setNombre(dto.getNombre());
        usuario.setCorreo(dto.getCorreo());
        usuario.setContrasena(passwordEncoder.encode(dto.getContrasena())); // nunca guardes la contraseña en texto plano
        usuario.setRol(Usuario.Rol.ESTUDIANTE); // todo registro nuevo entra como estudiante
        usuarioRepository.save(usuario);
}
}
