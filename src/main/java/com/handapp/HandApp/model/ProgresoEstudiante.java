package com.handapp.HandApp.model;

import jakarta.persistence.*;
import java.time.LocalDateTime;

@Entity
@Table(name = "progreso_estudiante",
       uniqueConstraints = @UniqueConstraint(columnNames = {"estudiante_id", "leccion_id"}))
public class ProgresoEstudiante {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @ManyToOne(fetch = FetchType.LAZY, optional = false)
    @JoinColumn(name = "estudiante_id", nullable = false)
    private Usuario estudiante;

    @ManyToOne(fetch = FetchType.LAZY, optional = false)
    @JoinColumn(name = "leccion_id", nullable = false)
    private Leccion leccion;

    @Column(nullable = false)
    private boolean completado = false;

    private Integer puntaje; // 0-100, solo tiene sentido cuando completado = true

    private Integer preguntaActual; // cuántas preguntas ha respondido hasta ahora (progreso a medias)

    private LocalDateTime fechaCompletado; // se llena solo cuando completado pasa a true

    private LocalDateTime ultimaActividad; // se actualiza en cada guardado, completo o parcial

    public ProgresoEstudiante() {}

    public Long getId() { return id; }
    public void setId(Long id) { this.id = id; }

    public Usuario getEstudiante() { return estudiante; }
    public void setEstudiante(Usuario estudiante) { this.estudiante = estudiante; }

    public Leccion getLeccion() { return leccion; }
    public void setLeccion(Leccion leccion) { this.leccion = leccion; }

    public boolean isCompletado() { return completado; }
    public void setCompletado(boolean completado) { this.completado = completado; }

    public Integer getPuntaje() { return puntaje; }
    public void setPuntaje(Integer puntaje) { this.puntaje = puntaje; }

    public Integer getPreguntaActual() { return preguntaActual; }
    public void setPreguntaActual(Integer preguntaActual) { this.preguntaActual = preguntaActual; }

    public LocalDateTime getFechaCompletado() { return fechaCompletado; }
    public void setFechaCompletado(LocalDateTime fechaCompletado) { this.fechaCompletado = fechaCompletado; }

    public LocalDateTime getUltimaActividad() { return ultimaActividad; }
    public void setUltimaActividad(LocalDateTime ultimaActividad) { this.ultimaActividad = ultimaActividad; }
}