package com.handapp.HandApp.service;

import com.handapp.HandApp.model.Leccion;
import com.handapp.HandApp.model.ProgresoEstudiante;
import com.handapp.HandApp.model.Usuario;
import com.handapp.HandApp.repository.LeccionRepository;
import com.handapp.HandApp.repository.ProgresoEstudianteRepository;
import com.handapp.HandApp.repository.UsuarioRepository;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import java.time.LocalDateTime;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

@Service
public class ProgresoEstudianteService {

    @Autowired
    private ProgresoEstudianteRepository progresoRepository;

    @Autowired
    private UsuarioRepository usuarioRepository;

    @Autowired
    private LeccionRepository leccionRepository;

    public ProgresoEstudiante registrarProgreso(Long estudianteId, Long leccionId, Integer puntaje) {
        Usuario estudiante = usuarioRepository.findById(estudianteId)
                .orElseThrow(() -> new IllegalArgumentException("Estudiante no encontrado"));

        Leccion leccion = leccionRepository.findById(leccionId)
                .orElseThrow(() -> new IllegalArgumentException("Lección no encontrada"));

        ProgresoEstudiante progreso = progresoRepository
                .findByEstudianteAndLeccion(estudiante, leccion)
                .orElseGet(ProgresoEstudiante::new);

        progreso.setEstudiante(estudiante);
        progreso.setLeccion(leccion);
        progreso.setCompletado(true);
        progreso.setPuntaje(puntaje);
        progreso.setFechaCompletado(LocalDateTime.now());
        progreso.setUltimaActividad(LocalDateTime.now());

        return progresoRepository.save(progreso);
    }

    public ProgresoEstudiante guardarProgresoParcial(Long estudianteId, Long leccionId, Integer preguntaActual) {
        Usuario estudiante = usuarioRepository.findById(estudianteId)
                .orElseThrow(() -> new IllegalArgumentException("Estudiante no encontrado"));

        Leccion leccion = leccionRepository.findById(leccionId)
                .orElseThrow(() -> new IllegalArgumentException("Lección no encontrada"));

        ProgresoEstudiante progreso = progresoRepository
                .findByEstudianteAndLeccion(estudiante, leccion)
                .orElseGet(ProgresoEstudiante::new);

        progreso.setEstudiante(estudiante);
        progreso.setLeccion(leccion);
        progreso.setPreguntaActual(preguntaActual);
        progreso.setUltimaActividad(LocalDateTime.now());

        return progresoRepository.save(progreso);
    }

    public List<ProgresoEstudiante> obtenerProgresoDeEstudiante(Long estudianteId) {
        Usuario estudiante = usuarioRepository.findById(estudianteId)
                .orElseThrow(() -> new IllegalArgumentException("Estudiante no encontrado"));
        return progresoRepository.findByEstudianteOrderByFechaCompletadoDesc(estudiante);
    }

    // Para el reporte del admin: progreso detallado con la lección ya cargada (evita LazyInitializationException)
    public List<ProgresoEstudiante> obtenerProgresoDetalladoDeEstudiante(Usuario estudiante) {
        return progresoRepository.findByEstudianteConLeccion(estudiante);
    }

    public List<Object[]> obtenerResumenAgregadoPorLeccion() {
        return progresoRepository.resumenAgregadoPorLeccion();
    }

    public List<Object[]> obtenerConteoCompletadasPorEstudiante() {
        return progresoRepository.conteoCompletadasPorEstudiante();
    }

    public Map<Long, Object[]> obtenerResumenPorLeccionComoMapa() {
        Map<Long, Object[]> mapa = new HashMap<>();
        for (Object[] fila : progresoRepository.resumenAgregadoPorLeccion()) {
            mapa.put((Long) fila[0], fila);
        }
        return mapa;
    }

    public Map<Long, ProgresoEstudiante> obtenerProgresoDeEstudianteComoMapa(Long estudianteId) {
        Map<Long, ProgresoEstudiante> mapa = new HashMap<>();
        for (ProgresoEstudiante p : obtenerProgresoDeEstudiante(estudianteId)) {
            mapa.put(p.getLeccion().getId(), p);
        }
        return mapa;
    }
}