-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: bd-yatzina.mysql.database.azure.com:3306
-- Tiempo de generación: 02-12-2025 a las 15:40:34
-- Versión del servidor: 8.0.42-azure
-- Versión de PHP: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `yatzinaapp`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumnos_maestros`
--

CREATE TABLE `alumnos_maestros` (
  `id` int NOT NULL,
  `id_maestro` int NOT NULL,
  `id_alumno` int NOT NULL,
  `fecha_asignacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `amigos`
--

CREATE TABLE `amigos` (
  `id` int NOT NULL,
  `id_usuario` int NOT NULL,
  `id_amigo` int NOT NULL,
  `estado` enum('pendiente','aceptado','rechazado') DEFAULT 'pendiente',
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentarios_maestro`
--

CREATE TABLE `comentarios_maestro` (
  `id` int NOT NULL,
  `id_maestro` int NOT NULL,
  `id_alumno` int NOT NULL,
  `comentario` text NOT NULL,
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insignias`
--

CREATE TABLE `insignias` (
  `id` int NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `icono` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `palabras_diccionario`
--

CREATE TABLE `palabras_diccionario` (
  `id` int NOT NULL,
  `palabra_hnahnu` varchar(255) NOT NULL,
  `traduccion_espanol` varchar(255) NOT NULL,
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `palabras_personalizadas`
--

CREATE TABLE `palabras_personalizadas` (
  `id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `palabra_hnahnu` varchar(255) DEFAULT NULL,
  `traduccion_espanol` varchar(255) DEFAULT NULL,
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `progreso_lecciones`
--

CREATE TABLE `progreso_lecciones` (
  `id` int NOT NULL,
  `id_alumno` int NOT NULL,
  `leccion` varchar(50) NOT NULL,
  `progreso` decimal(5,2) DEFAULT '0.00',
  `completada` tinyint(1) DEFAULT '0',
  `puntos` int DEFAULT '0',
  `porcentaje` int DEFAULT '0',
  `fecha_completada` datetime DEFAULT NULL,
  `tiempo_dedicado` int DEFAULT '0',
  `intentos` int DEFAULT '0',
  `fecha_actualizacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `progreso_lecciones`
--

INSERT INTO `progreso_lecciones` (`id`, `id_alumno`, `leccion`, `progreso`, `completada`, `puntos`, `porcentaje`, `fecha_completada`, `tiempo_dedicado`, `intentos`, `fecha_actualizacion`) VALUES
(1, 4, 'tortillas', '100.00', 1, 0, 0, '2025-12-02 08:05:35', 0, 1, '2025-12-02 09:46:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `nombre_usuario` varchar(50) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `contrasena` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `avatar` varchar(300) DEFAULT NULL,
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `rol` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre_usuario`, `correo`, `contrasena`, `avatar`, `fecha_registro`, `rol`) VALUES
(1, 'adminyatzina', 'adminyatzina@gmail.com', '$2y$10$5HnAvKm9fWFuATQXrC6uGOw9ec9TEBKQSfZ9.DOn8hhqz.Tas74ES', NULL, '2025-11-30 14:53:45', 'alumno'),
(2, 'usuarioyatzina1', 'usuarioyatzina@gmail.com', '$2y$10$tx9DJcjbluKN90PWOGdujOILepINTbaqLfHJ2D48IxXp1WBXDJxiq', NULL, '2025-11-30 19:58:35', 'alumno'),
(3, 'adminIsaac', 'ayl@gmail.com', '$2y$10$NHv8WqfPHyWhc2cnwAzReOguZ7RTT/HI1gM6/mUsFTT.vq8z9klSS', NULL, '2025-12-02 01:59:10', 'maestro'),
(4, 'alumnoyatzina', 'alumnoyatzina@gmail.com', '$2y$10$aLJ1ImfFFE09/zHhP8VrKOpPh9cUP6YCG8o0XtLYeaiJlU8fcVXPm', NULL, '2025-12-02 07:35:58', 'alumno');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuariosnuevos`
--

CREATE TABLE `usuariosnuevos` (
  `id` int NOT NULL,
  `usuario` varchar(255) DEFAULT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `correo` varchar(255) DEFAULT NULL,
  `contrasena` varchar(255) DEFAULT NULL,
  `rol` varchar(50) DEFAULT NULL,
  `tipoRol` varchar(50) DEFAULT NULL,
  `grado` varchar(50) DEFAULT NULL,
  `idMaestro` int DEFAULT NULL,
  `nombreCompleto` varchar(255) DEFAULT NULL,
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_insignias`
--

CREATE TABLE `usuarios_insignias` (
  `id` int NOT NULL,
  `id_usuario` int NOT NULL,
  `id_insignia` int NOT NULL,
  `fecha_obtenida` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_perfiles`
--

CREATE TABLE `usuarios_perfiles` (
  `id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `nombre_completo` varchar(255) DEFAULT NULL,
  `grado` varchar(50) DEFAULT NULL,
  `tipo_rol` enum('Alumno','Maestro') DEFAULT NULL,
  `id_maestro` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alumnos_maestros`
--
ALTER TABLE `alumnos_maestros`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_relacion` (`id_maestro`,`id_alumno`),
  ADD KEY `id_alumno` (`id_alumno`);

--
-- Indices de la tabla `amigos`
--
ALTER TABLE `amigos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_amigo` (`id_amigo`);

--
-- Indices de la tabla `comentarios_maestro`
--
ALTER TABLE `comentarios_maestro`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_maestro` (`id_maestro`),
  ADD KEY `id_alumno` (`id_alumno`);

--
-- Indices de la tabla `insignias`
--
ALTER TABLE `insignias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `palabras_diccionario`
--
ALTER TABLE `palabras_diccionario`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `palabras_personalizadas`
--
ALTER TABLE `palabras_personalizadas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `progreso_lecciones`
--
ALTER TABLE `progreso_lecciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_alumno_leccion` (`id_alumno`,`leccion`),
  ADD UNIQUE KEY `uk_progreso` (`id_alumno`,`leccion`),
  ADD KEY `idx_leccion` (`leccion`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre_usuario` (`nombre_usuario`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indices de la tabla `usuariosnuevos`
--
ALTER TABLE `usuariosnuevos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios_insignias`
--
ALTER TABLE `usuarios_insignias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_insignia` (`id_insignia`);

--
-- Indices de la tabla `usuarios_perfiles`
--
ALTER TABLE `usuarios_perfiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `id_maestro` (`id_maestro`),
  ADD KEY `idx_tipo_rol` (`tipo_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alumnos_maestros`
--
ALTER TABLE `alumnos_maestros`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `amigos`
--
ALTER TABLE `amigos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `comentarios_maestro`
--
ALTER TABLE `comentarios_maestro`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `insignias`
--
ALTER TABLE `insignias`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `palabras_diccionario`
--
ALTER TABLE `palabras_diccionario`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `palabras_personalizadas`
--
ALTER TABLE `palabras_personalizadas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `progreso_lecciones`
--
ALTER TABLE `progreso_lecciones`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuariosnuevos`
--
ALTER TABLE `usuariosnuevos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios_insignias`
--
ALTER TABLE `usuarios_insignias`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios_perfiles`
--
ALTER TABLE `usuarios_perfiles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alumnos_maestros`
--
ALTER TABLE `alumnos_maestros`
  ADD CONSTRAINT `alumnos_maestros_ibfk_1` FOREIGN KEY (`id_maestro`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `alumnos_maestros_ibfk_2` FOREIGN KEY (`id_alumno`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `amigos`
--
ALTER TABLE `amigos`
  ADD CONSTRAINT `amigos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `amigos_ibfk_2` FOREIGN KEY (`id_amigo`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `comentarios_maestro`
--
ALTER TABLE `comentarios_maestro`
  ADD CONSTRAINT `comentarios_maestro_ibfk_1` FOREIGN KEY (`id_maestro`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `comentarios_maestro_ibfk_2` FOREIGN KEY (`id_alumno`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `palabras_personalizadas`
--
ALTER TABLE `palabras_personalizadas`
  ADD CONSTRAINT `palabras_personalizadas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `progreso_lecciones`
--
ALTER TABLE `progreso_lecciones`
  ADD CONSTRAINT `progreso_lecciones_ibfk_1` FOREIGN KEY (`id_alumno`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios_insignias`
--
ALTER TABLE `usuarios_insignias`
  ADD CONSTRAINT `usuarios_insignias_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `usuarios_insignias_ibfk_2` FOREIGN KEY (`id_insignia`) REFERENCES `insignias` (`id`);

--
-- Filtros para la tabla `usuarios_perfiles`
--
ALTER TABLE `usuarios_perfiles`
  ADD CONSTRAINT `usuarios_perfiles_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `usuarios_perfiles_ibfk_2` FOREIGN KEY (`id_maestro`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
