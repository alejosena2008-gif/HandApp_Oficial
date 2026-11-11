package com.handapp.HandApp.dto;

public class ProgresoLeccionDTO {
    private String tituloLeccion;
    private boolean completado;
    private Integer puntaje;
    private Integer preguntaActual;

    public String getTituloLeccion() { return tituloLeccion; }
    public void setTituloLeccion(String tituloLeccion) { this.tituloLeccion = tituloLeccion; }

    public boolean isCompletado() { return completado; }
    public void setCompletado(boolean completado) { this.completado = completado; }

    public Integer getPuntaje() { return puntaje; }
    public void setPuntaje(Integer puntaje) { this.puntaje = puntaje; }

    public Integer getPreguntaActual() { return preguntaActual; }
    public void setPreguntaActual(Integer preguntaActual) { this.preguntaActual = preguntaActual; }
}