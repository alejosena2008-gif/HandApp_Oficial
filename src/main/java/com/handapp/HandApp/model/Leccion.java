package com.handapp.HandApp.model;

import jakarta.persistence.*;

@Entity
@Table(name = "lecciones")
public class Leccion {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(nullable = false)
    private String titulo;

    @Column(length = 1000)
    private String descripcion;

    // Enlace a un video o imagen que ilustra la seña/lección (opcional)
    private String contenidoUrl;

    @Column(nullable = false)
    private Integer orden;

    @Column(nullable = false)
    private boolean publicada = false;

    // Nombre del archivo HTML (sin ".html") dentro de templates/estudiante/
    // que contiene el quiz escrito a mano para esta lección. Ej: "leccion-saludos"
    private String vista;

    @ManyToOne
    @JoinColumn(name = "creado_por_id")
    private Usuario creadoPor;

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

    public Usuario getCreadoPor() { return creadoPor; }
    public void setCreadoPor(Usuario creadoPor) { this.creadoPor = creadoPor; }
}