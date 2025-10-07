-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-10-2025 a las 19:31:39
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
-- Base de datos: `voxfx`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `programas`
--

CREATE TABLE `programas` (
  `id_programa` int(11) NOT NULL,
  `nombre_programa` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `horario` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `programas`
--

INSERT INTO `programas` (`id_programa`, `nombre_programa`, `descripcion`, `horario`) VALUES
(1, 'Pixelados', 'Programa principal de FM Pixel dedicado a la actualidad de los videojuegos, lanzamientos y cultura gamer.', 'Lunes a Viernes 18:00 - 20:00'),
(2, 'RetroWave', 'Espacio dedicado a los clásicos de los 80s, 90s y 2000, con análisis y música retro.', 'Sábados 16:00 - 18:00'),
(3, 'Modo Historia', 'Programa narrativo sobre sagas y desarrolladores que marcaron la historia de los videojuegos.', 'Domingos 19:00 - 21:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `programas_sonidos`
--

CREATE TABLE `programas_sonidos` (
  `id_sonidos_programa` int(11) NOT NULL,
  `id_programa` int(11) NOT NULL,
  `id_sonido` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `programas_sonidos`
--

INSERT INTO `programas_sonidos` (`id_sonidos_programa`, `id_programa`, `id_sonido`) VALUES
(1, 1, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sonidos`
--

CREATE TABLE `sonidos` (
  `id_sonido` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  `tipo` enum('institucional','personal','programa') NOT NULL,
  `propietario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sonidos`
--

INSERT INTO `sonidos` (`id_sonido`, `nombre`, `url`, `tipo`, `propietario`) VALUES
(1, 'zarpado', 'uploads/Goofy Cartoon Sounds - Neave.mp3', 'programa', 2),
(2, 'aaaaaa', 'uploads/Goofy Cartoon Sounds - Neave.mp3', 'institucional', 2),
(3, 'asda', 'uploads/Goofy Cartoon Sounds - Neave.mp3', 'programa', 2),
(4, '1up', 'uploads/onepu.mp3', 'personal', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` varchar(20) NOT NULL DEFAULT 'usuario'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `username`, `email`, `contrasena`, `rol`) VALUES
(2, 'Pedro Coppola', 'pedroicoppola@gmail.com', '$2y$10$81CoIGvZvExuBdfqb5tfSOueWumAGaLu5zbUojDNwUpEsKqZzRA9y', 'operador'),
(3, 'jefe_juan', 'juan@fmpixel.com', '$2y$10$4dQWQxM.JPZK4b1pyYwl7u6hV7jJKFhKZbPqM3wZ3BKoAqwrH6Y.2', 'jefe'),
(4, 'operador_pedro', 'pedro@fmpixel.com', '$2y$10$OzZxvXksTnPBYkF36BzXUO4cdVZ7Kcsn0LwOyFmuEGPrlVjpiCrCe', 'operador'),
(5, 'operador_luca', 'luca@fmpixel.com', '$2y$10$GkS2D3ZPZKmM.93vXkiTROX7vOOVtXyPyQFpmZf6lqZ8yIzQzVAKq', 'operador'),
(6, 'operador_alan', 'alan@fmpixel.com', '$2y$10$LM0bW7x4UP1sL5Q8h8/95u.5DPrmE/Es8F8xXlVj8EGBq5E5h8ToS', 'operador'),
(7, 'productor_martin', 'martin@fmpixel.com', '$2y$10$1Fq0aSxeN6BZQdc3EjHGVuVxqO5ov6DmB7fD2A7aAVMZbZ1nXnM7a', 'productor'),
(8, 'productora_lara', 'lara@fmpixel.com', '$2y$10$y1dBzA0b0q8DqFiX1eT5OeJZ5I76XlD4YcfG1Xb8oP1TjZ7rE/5Eq', 'productor');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_programas`
--

CREATE TABLE `usuarios_programas` (
  `id_asignacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_programa` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios_programas`
--

INSERT INTO `usuarios_programas` (`id_asignacion`, `id_usuario`, `id_programa`) VALUES
(1, 2, 1),
(2, 5, 1),
(3, 3, 2),
(4, 6, 2),
(5, 4, 3),
(6, 5, 3);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `programas`
--
ALTER TABLE `programas`
  ADD PRIMARY KEY (`id_programa`);

--
-- Indices de la tabla `programas_sonidos`
--
ALTER TABLE `programas_sonidos`
  ADD PRIMARY KEY (`id_sonidos_programa`),
  ADD KEY `id_programa` (`id_programa`),
  ADD KEY `id_sonido` (`id_sonido`);

--
-- Indices de la tabla `sonidos`
--
ALTER TABLE `sonidos`
  ADD PRIMARY KEY (`id_sonido`),
  ADD KEY `propietario` (`propietario`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `usuarios_programas`
--
ALTER TABLE `usuarios_programas`
  ADD PRIMARY KEY (`id_asignacion`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_programa` (`id_programa`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `programas`
--
ALTER TABLE `programas`
  MODIFY `id_programa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `programas_sonidos`
--
ALTER TABLE `programas_sonidos`
  MODIFY `id_sonidos_programa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `sonidos`
--
ALTER TABLE `sonidos`
  MODIFY `id_sonido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `usuarios_programas`
--
ALTER TABLE `usuarios_programas`
  MODIFY `id_asignacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `programas_sonidos`
--
ALTER TABLE `programas_sonidos`
  ADD CONSTRAINT `programas_sonidos_ibfk_1` FOREIGN KEY (`id_programa`) REFERENCES `programas` (`id_programa`),
  ADD CONSTRAINT `programas_sonidos_ibfk_2` FOREIGN KEY (`id_sonido`) REFERENCES `sonidos` (`id_sonido`);

--
-- Filtros para la tabla `sonidos`
--
ALTER TABLE `sonidos`
  ADD CONSTRAINT `sonidos_ibfk_1` FOREIGN KEY (`propietario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `usuarios_programas`
--
ALTER TABLE `usuarios_programas`
  ADD CONSTRAINT `usuarios_programas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `usuarios_programas_ibfk_2` FOREIGN KEY (`id_programa`) REFERENCES `programas` (`id_programa`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
