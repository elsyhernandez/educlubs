-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-10-2025 a las 19:54:03
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `clubs_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `club_registrations`
--

CREATE TABLE `club_registrations` (
  `id` int(11) NOT NULL,
  `club_type` enum('cultural','deportivo','civil') NOT NULL,
  `club_name` varchar(150) NOT NULL,
  `paterno` varchar(100) NOT NULL,
  `materno` varchar(100) NOT NULL,
  `nombres` varchar(150) NOT NULL,
  `semestre` varchar(50) DEFAULT NULL,
  `correo` varchar(255) NOT NULL,
  `turno` varchar(50) DEFAULT NULL,
  `user_id` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `club_registrations`
--

INSERT INTO `club_registrations` (`id`, `club_type`, `club_name`, `paterno`, `materno`, `nombres`, `semestre`, `correo`, `turno`, `user_id`, `created_at`) VALUES
(1, 'cultural', 'Fotografía/Video', 'hernandez', 'ignacio', 'elsy', '5to', 'elsy.hernandez.alp@cbtis258.edu.mx', 'matutino', '@alp_2025', '2025-10-17 14:34:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tutoring_registrations`
--

CREATE TABLE `tutoring_registrations` (
  `id` int(11) NOT NULL,
  `materia` varchar(150) NOT NULL,
  `paterno` varchar(100) NOT NULL,
  `materno` varchar(100) NOT NULL,
  `nombres` varchar(150) NOT NULL,
  `carrera` varchar(150) DEFAULT NULL,
  `turno` varchar(50) DEFAULT NULL,
  `maestro` varchar(150) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `user_id` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('student','teacher') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `user_id`, `email`, `username`, `password_hash`, `role`, `created_at`) VALUES
(1, '@alp_2025', 'elsy.hernandez.alp@cbtis258.edu.mx', 'elsy ignacio', '$2y$10$wyi.3lfBbDCv1sPkFObRTOTbKkxH20OKRKULyg.H2bLgUvQCXI4Y.', 'student', '2025-10-17 14:33:10'),
(2, '@tea_2025', 'ilse.hernandez.alp@cbtis258.edu.mx', 'maestra maria', '$2y$10$IezmucrghF/Jv2RGGKuZGOlqngTbCv6cedVC7j395lWoMqZMmZs7O', 'teacher', '2025-10-17 14:36:28');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `club_registrations`
--
ALTER TABLE `club_registrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tutoring_registrations`
--
ALTER TABLE `tutoring_registrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `club_registrations`
--
ALTER TABLE `club_registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tutoring_registrations`
--
ALTER TABLE `tutoring_registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
