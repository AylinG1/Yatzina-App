-- Tabla para registrar movimientos/acciones de alumnos
CREATE TABLE IF NOT EXISTS `movimientos` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `alumno_id` VARCHAR(128) DEFAULT NULL,
  `tipo` VARCHAR(100) NOT NULL,
  `detalle` TEXT,
  `pagina` VARCHAR(255),
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX (`alumno_id`),
  INDEX (`created_at`)
);

-- Ejemplo de uso:
-- INSERT INTO movimientos (alumno_id, tipo, detalle, pagina) VALUES ('123', 'avance_leccion', 'completó ejercicio 4', '/alumno/lecciones/Animales.html');
