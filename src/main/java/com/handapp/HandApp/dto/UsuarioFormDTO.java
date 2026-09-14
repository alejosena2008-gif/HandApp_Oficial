package com.handapp.HandApp.dto;

import com.handapp.HandApp.model.Usuario;

public class UsuarioFormDTO {
    private Long id;
    private String nombre;
    private String correo;
    private String contrasena; // opcional al editar: si viene vacío, no se cambia
    private Usuario.Rol rol;

    public Long getId() { return id; }
    public void setId(Long id) { this.id = id; }

    public String getNombre() { return nombre; }
    public void setNombre(String nombre) { this.nombre = nombre; }

    public String getCorreo() { return correo; }
    public void setCorreo(String correo) { this.correo = correo; }

    public String getContrasena() { return contrasena; }
    public void setContrasena(String contrasena) { this.contrasena = contrasena; }

    public Usuario.Rol getRol() { return rol; }
    public void setRol(Usuario.Rol rol) { this.rol = rol; }
}