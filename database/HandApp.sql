-- ============================================================================
-- Base de Datos Oficial de HandApp (MySQL / MariaDB - XAMPP)
-- Esquema compatible con Spring Boot (JPA) y Django (handapp_django)
-- ============================================================================

CREATE DATABASE IF NOT EXISTS `handapp`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE `handapp`;

-- ----------------------------------------------------------------------------
-- 1. Tabla de Usuarios
-- Reglas de dominio:
--   *@handapp.com    -> ADMINISTRADOR (acceso a /admin/panel)
--   *@editorhand.com -> EDITOR        (acceso a /editor/panel)
--   Otros dominios   -> ESTUDIANTE    (acceso a lecciones y traductor)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuarios` (
    `id` BIGINT NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(100) NOT NULL,
    `correo` VARCHAR(150) NOT NULL UNIQUE,
    `contrasena` VARCHAR(255) NOT NULL,
    `rol` ENUM('ADMINISTRADOR', 'EDITOR', 'ESTUDIANTE') NOT NULL DEFAULT 'ESTUDIANTE',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------------------------------------------------------
-- 2. Tabla de Lecciones LSC
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `lecciones` (
    `id` BIGINT NOT NULL AUTO_INCREMENT,
    `titulo` VARCHAR(150) NOT NULL,
    `descripcion` VARCHAR(500) NOT NULL,
    `contenido_url` VARCHAR(255) DEFAULT NULL,
    `orden` INT NOT NULL,
    `publicada` BIT(1) NOT NULL DEFAULT b'1',
    `vista` VARCHAR(100) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------------------------------------------------------
-- 3. Tabla de Progreso de Estudiantes
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `progreso_estudiante` (
    `id` BIGINT NOT NULL AUTO_INCREMENT,
    `estudiante_id` BIGINT NOT NULL,
    `leccion_id` BIGINT NOT NULL,
    `completado` BIT(1) NOT NULL DEFAULT b'0',
    `puntaje` INT DEFAULT NULL,
    `pregunta_actual` INT DEFAULT NULL,
    `fecha_completado` DATETIME(6) DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_estudiante_leccion` (`estudiante_id`, `leccion_id`),
    CONSTRAINT `fk_progreso_estudiante` FOREIGN KEY (`estudiante_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_progreso_leccion` FOREIGN KEY (`leccion_id`) REFERENCES `lecciones` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
