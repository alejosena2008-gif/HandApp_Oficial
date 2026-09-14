package com.handapp.HandApp.dto;

public class ResumenLeccionDTO {

    private Long leccionId;
    private String titulo;
    private long totalIntentos;
    private long totalCompletados;
    private int pctCompletado;
    private Integer promedioPuntaje; // null si nadie ha completado esta lección aún

    public Long getLeccionId() { return leccionId; }
    public void setLeccionId(Long leccionId) { this.leccionId = leccionId; }

    public String getTitulo() { return titulo; }
    public void setTitulo(String titulo) { this.titulo = titulo; }

    public long getTotalIntentos() { return totalIntentos; }
    public void setTotalIntentos(long totalIntentos) { this.totalIntentos = totalIntentos; }

    public long getTotalCompletados() { return totalCompletados; }
    public void setTotalCompletados(long totalCompletados) { this.totalCompletados = totalCompletados; }

    public int getPctCompletado() { return pctCompletado; }
    public void setPctCompletado(int pctCompletado) { this.pctCompletado = pctCompletado; }

    public Integer getPromedioPuntaje() { return promedioPuntaje; }
    public void setPromedioPuntaje(Integer promedioPuntaje) { this.promedioPuntaje = promedioPuntaje; }
}