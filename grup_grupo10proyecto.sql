-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 10-04-2026 a las 14:27:23
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `grup_grupo10proyecto`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `progreso`
--

CREATE TABLE `progreso` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `ejercicios_completados` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `progreso_ejercicios`
--

CREATE TABLE `progreso_ejercicios` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `ejercicio_n` int(11) DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'pendiente',
  `tipo` varchar(10) DEFAULT '6d',
  `num1` int(11) DEFAULT NULL,
  `num2` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `progreso_ejercicios`
--

INSERT INTO `progreso_ejercicios` (`id`, `usuario_id`, `ejercicio_n`, `estado`, `tipo`, `num1`, `num2`) VALUES
(1, 1, 1, 'resuelto', '6d', 879929, 672035),
(2, 2, 1, 'resuelto', '6d', NULL, NULL),
(3, 2, 3, 'resuelto', '6d', NULL, NULL),
(4, 1, 6, 'pendiente', '6d', 307580, 796470),
(5, 1, 2, 'resuelto', '6d', 750082, 805555),
(6, 1, 8, 'pendiente', '6d', 329499, 150794),
(7, 10, 2, 'resuelto', '6d', NULL, NULL),
(8, 1, 5, 'pendiente', '6d', 197695, 524457),
(9, 1, 3, 'pendiente', '6d', 596541, 826299),
(10, 1, 4, 'resuelto', '6d', 528838, 984144),
(11, 1, 7, 'pendiente', '6d', 175129, 613488),
(12, 1, 1, 'pendiente', '2d', 31, 13),
(13, 1, 2, 'pendiente', '2d', 47, 15),
(14, 1, 3, 'pendiente', '2d', 42, 63),
(15, 1, 4, 'pendiente', '2d', 20, 91),
(16, 1, 5, 'pendiente', '2d', 85, 59),
(17, 1, 6, 'pendiente', '2d', 37, 73),
(18, 1, 7, 'pendiente', '2d', 40, 96),
(19, 1, 8, 'pendiente', '2d', 22, 94),
(20, 1, 1, 'pendiente', '6d2', 851278, 833149),
(21, 1, 2, 'pendiente', '6d2', 404690, 868134),
(22, 1, 3, 'pendiente', '6d2', 855805, 733182),
(23, 1, 4, 'pendiente', '6d2', 634727, 176406),
(24, 1, 5, 'pendiente', '6d2', 430064, 281360),
(25, 1, 6, 'pendiente', '6d2', 236304, 767432),
(26, 1, 7, 'pendiente', '6d2', 545896, 165868),
(27, 1, 8, 'pendiente', '6d2', 633912, 543894),
(28, 1, 9, 'pendiente', '6d2', 730612, 656025),
(29, 1, 10, 'pendiente', '6d2', 267358, 408434),
(30, 1, 11, 'pendiente', '6d2', 731602, 851847),
(31, 1, 12, 'pendiente', '6d2', 446871, 280783),
(32, 1, 13, 'pendiente', '6d2', 965672, 614477),
(33, 1, 14, 'pendiente', '6d2', 302157, 110467),
(34, 1, 15, 'incorrecto', '6d2', 841424, 715113),
(35, 1, 16, 'pendiente', '6d2', 457967, 267333);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre_usuario` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `puntos_totales` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre_usuario`, `password`, `puntos_totales`) VALUES
(1, 'admin', 'admin', 4),
(2, 'grupo10proyecto', 'grupo10proyecto', 0),
(4, 'jose11', '123', 0),
(6, 'Hola', 'hola123', 0),
(7, 'user', 'user', 0),
(8, 'sas', '123', 0),
(10, 'user2', '12345678', 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `progreso`
--
ALTER TABLE `progreso`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `progreso_ejercicios`
--
ALTER TABLE `progreso_ejercicios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario_ejercicio_tipo` (`usuario_id`,`ejercicio_n`,`tipo`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre_usuario` (`nombre_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `progreso`
--
ALTER TABLE `progreso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `progreso_ejercicios`
--
ALTER TABLE `progreso_ejercicios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `progreso`
--
ALTER TABLE `progreso`
  ADD CONSTRAINT `progreso_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `progreso_ejercicios`
--
ALTER TABLE `progreso_ejercicios`
  ADD CONSTRAINT `progreso_ejercicios_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
