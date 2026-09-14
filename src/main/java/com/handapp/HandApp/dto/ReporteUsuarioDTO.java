package com.handapp.HandApp.dto;

import com.handapp.HandApp.model.Usuario;
import java.util.List;

public class ReporteUsuarioDTO {
    private Long id;
    private String nombre;
    private String correo;
    private Usuario.Rol rol;
    private int leccionesCompletadas;
    private Double promedioPuntaje;
    private List<ProgresoLeccionDTO> progresos;

    public Long getId() { return id; }
    public void setId(Long id) { this.id = id; }

    public String getNombre() { return nombre; }
    public void setNombre(String nombre) { this.nombre = nombre; }

    public String getCorreo() { return correo; }
    public void setCorreo(String correo) { this.correo = correo; }

    public Usuario.Rol getRol() { return rol; }
    public void setRol(Usuario.Rol rol) { this.rol = rol; }

    public int getLeccionesCompletadas() { return leccionesCompletadas; }
    public void setLeccionesCompletadas(int leccionesCompletadas) { this.leccionesCompletadas = leccionesCompletadas; }

    public Double getPromedioPuntaje() { return promedioPuntaje; }
    public void setPromedioPuntaje(Double promedioPuntaje) { this.promedioPuntaje = promedioPuntaje; }

    public List<ProgresoLeccionDTO> getProgresos() { return progresos; }
    public void setProgresos(List<ProgresoLeccionDTO> progresos) { this.progresos = progresos; }
}