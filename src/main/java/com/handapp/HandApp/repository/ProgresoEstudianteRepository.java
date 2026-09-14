package com.handapp.HandApp.repository;

import com.handapp.HandApp.model.Leccion;
import com.handapp.HandApp.model.ProgresoEstudiante;
import com.handapp.HandApp.model.Usuario;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;

import java.util.List;
import java.util.Optional;

public interface ProgresoEstudianteRepository extends JpaRepository<ProgresoEstudiante, Long> {

    Optional<ProgresoEstudiante> findByEstudianteAndLeccion(Usuario estudiante, Leccion leccion);

    // Para el reporte del ADMIN: todo el progreso de un estudiante específico (con nombre/correo)
    List<ProgresoEstudiante> findByEstudianteOrderByFechaCompletadoDesc(Usuario estudiante);

    long countByLeccionAndCompletadoTrue(Leccion leccion);

    long countByLeccion(Leccion leccion);

    // Para el reporte del EDITOR: resumen agregado y anónimo por lección
    // Devuelve: [leccionId, totalIntentos, totalCompletados, promedioPuntaje]
    @Query("SELECT p.leccion.id, COUNT(p), " +
           "SUM(CASE WHEN p.completado = true THEN 1 ELSE 0 END), " +
           "AVG(CASE WHEN p.completado = true THEN p.puntaje ELSE NULL END) " +
           "FROM ProgresoEstudiante p GROUP BY p.leccion.id")
    List<Object[]> resumenAgregadoPorLeccion();

    // Trae el progreso de un estudiante con la lección ya cargada (para el reporte del admin)
    @Query("SELECT p FROM ProgresoEstudiante p JOIN FETCH p.leccion WHERE p.estudiante = :estudiante ORDER BY p.leccion.orden ASC")
    List<ProgresoEstudiante> findByEstudianteConLeccion(@Param("estudiante") Usuario estudiante);

    // Para el reporte del ADMIN: cuántas lecciones completó cada estudiante
    @Query("SELECT p.estudiante.id, COUNT(p) " +
           "FROM ProgresoEstudiante p WHERE p.completado = true GROUP BY p.estudiante.id")
    List<Object[]> conteoCompletadasPorEstudiante();
}