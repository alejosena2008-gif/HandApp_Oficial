package com.handapp.HandApp.dto;

public class LeccionFormDTO {
    private Long id;
    private String titulo;
    private String descripcion;
    private String contenidoUrl;
    private Integer orden;
    private boolean publicada;
    private String vista;

    public Long getId() { return id; }
    public void setId(Long id) { this.id = id; }

    public String getTitulo() { return titulo; }
    public void setTitulo(String titulo) { this.titulo = titulo; }

    public String getDescripcion() { return descripcion; }
    public void setDescripcion(String descripcion) { this.descripcion = descripcion; }

    public String getContenidoUrl() { return contenidoUrl; }
    public void setContenidoUrl(String contenidoUrl) { this.contenidoUrl = contenidoUrl; }

    public Integer getOrden() { return orden; }
    public void setOrden(Integer orden) { this.orden = orden; }

    public boolean isPublicada() { return publicada; }
    public void setPublicada(boolean publicada) { this.publicada = publicada; }

    public String getVista() { return vista; }
    public void setVista(String vista) { this.vista = vista; }
}