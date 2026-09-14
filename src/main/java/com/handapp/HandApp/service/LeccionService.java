package com.handapp.HandApp.service;

import com.handapp.HandApp.dto.LeccionFormDTO;
import com.handapp.HandApp.model.Leccion;
import com.handapp.HandApp.model.Usuario;
import com.handapp.HandApp.repository.LeccionRepository;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

@Service
public class LeccionService {

    @Autowired
    private LeccionRepository leccionRepository;

    public void crear(LeccionFormDTO dto, Usuario autor) {
        Leccion leccion = new Leccion();
        leccion.setTitulo(dto.getTitulo());
        leccion.setDescripcion(dto.getDescripcion());
        leccion.setContenidoUrl(dto.getContenidoUrl());
        leccion.setOrden(dto.getOrden());
        leccion.setPublicada(dto.isPublicada());
        leccion.setVista(dto.getVista());
        leccion.setCreadoPor(autor);
        leccionRepository.save(leccion);
    }

    public void actualizar(Long id, LeccionFormDTO dto) {
        Leccion leccion = leccionRepository.findById(id)
                .orElseThrow(() -> new IllegalArgumentException("Lección no encontrada"));

        leccion.setTitulo(dto.getTitulo());
        leccion.setDescripcion(dto.getDescripcion());
        leccion.setContenidoUrl(dto.getContenidoUrl());
        leccion.setOrden(dto.getOrden());
        leccion.setPublicada(dto.isPublicada());
        leccion.setVista(dto.getVista());

        leccionRepository.save(leccion);
    }

    public void eliminar(Long id) {
        leccionRepository.deleteById(id);
    }
}