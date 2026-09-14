package com.handapp.HandApp.repository;

import com.handapp.HandApp.model.Leccion;
import org.springframework.data.jpa.repository.JpaRepository;

import java.util.List;

public interface LeccionRepository extends JpaRepository<Leccion, Long> {
    List<Leccion> findAllByOrderByOrdenAsc();
    List<Leccion> findAllByPublicadaTrueOrderByOrdenAsc();
}