-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-11-2025 a las 23:00:54
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
-- Base de datos: `clubs_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clubs`
--

CREATE TABLE `clubs` (
  `club_id` varchar(50) NOT NULL,
  `club_name` varchar(100) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `creator_name` varchar(100) NOT NULL,
  `club_type` enum('cultural','deportivo','civil','asesoria') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clubs`
--

INSERT INTO `clubs` (`club_id`, `club_name`, `password`, `creator_name`, `club_type`, `created_at`) VALUES
('ase001', 'Matemáticas 1', '$2y$10$hashMate1', 'Profe Rubén', 'asesoria', '2025-10-19 10:52:00'),
('ase002', 'Matemáticas 2', '$2y$10$hashMate2', 'Profe Rubén', 'asesoria', '2025-10-19 10:52:00'),
('ase003', 'Matemáticas 3', '$2y$10$hashMate3', 'Profe Rubén', 'asesoria', '2025-10-19 10:52:00'),
('ase004', 'Inglés', '$2y$10$hashIngles', 'Profe Mariana', 'asesoria', '2025-10-19 10:52:00'),
('civ001', 'Banda de guerra', '$2y$10$hashBanda', 'Profe Héctor', 'civil', '2025-10-19 10:52:00'),
('civ002', 'Escolta', '$2y$10$hashEscolta', 'Profe Brenda', 'civil', '2025-10-19 10:52:00'),
('cult001', 'Fotografía/Video', '$2y$10$hashFoto', 'Profe Laura', 'cultural', '2025-10-19 10:52:00'),
('cult002', 'Danza y baile', '$2y$10$hashDanza', 'Profe Miguel', 'cultural', '2025-10-19 10:52:00'),
('cult003', 'Música/Rondalla', '$2y$10$hashRondalla', 'Profe Ana', 'cultural', '2025-10-19 10:52:00'),
('cult004', 'Música grupo norteño', '$2y$10$hashNorteño', 'Profe Luis', 'cultural', '2025-10-19 10:52:00'),
('cult005', 'Arte manual', '$2y$10$hashArte', 'Profe Karla', 'cultural', '2025-10-19 10:52:00'),
('cult006', 'Oratoria y declamación', '$2y$10$hashOratoria', 'Profe Iván', 'cultural', '2025-10-19 10:52:00'),
('cult007', 'Pintura/Dibujo', '$2y$10$hashPintura', 'Profe Sonia', 'cultural', '2025-10-19 10:52:00'),
('cult008', 'Teatro', '$2y$10$hashTeatro', 'Profe Andrés', 'cultural', '2025-10-19 10:52:00'),
('cult009', 'Creación literaria', '$2y$10$hashLiteraria', 'Profe Diana', 'cultural', '2025-10-19 10:52:00'),
('dep001', 'Ajedrez', '$2y$10$hashAjedrez', 'Profe Jorge', 'deportivo', '2025-10-19 10:52:00'),
('dep002', 'Atletismo', '$2y$10$hashAtletismo', 'Profe Carla', 'deportivo', '2025-10-19 10:52:00'),
('dep003', 'Basquetbol', '$2y$10$hashBasket', 'Profe Raúl', 'deportivo', '2025-10-19 10:52:00'),
('dep004', 'Defensa personal', '$2y$10$hashDefensa', 'Profe Erika', 'deportivo', '2025-10-19 10:52:00'),
('dep005', 'Fútbol femenil', '$2y$10$hashFutFem', 'Profe Nancy', 'deportivo', '2025-10-19 10:52:00'),
('dep006', 'Fútbol varonil', '$2y$10$hashFutVar', 'Profe Mario', 'deportivo', '2025-10-19 10:52:00'),
('dep007', 'Voleibol femenil', '$2y$10$hashVoleiFem', 'Profe Silvia', 'deportivo', '2025-10-19 10:52:00'),
('dep008', 'Voleibol varonil', '$2y$10$hashVoleiVar', 'Profe Tomás', 'deportivo', '2025-10-19 10:52:00'),
('DEP_YUCTN', 'Club de roboticaa', NULL, 'asdfsfasfsf', 'deportivo', '2025-11-02 07:34:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `club_registrations`
--

CREATE TABLE `club_registrations` (
  `id` int(11) NOT NULL,
  `club_type` enum('cultural','deportivo','civil','asesoria') NOT NULL,
  `club_name` varchar(150) NOT NULL,
  `paterno` varchar(100) NOT NULL,
  `materno` varchar(100) NOT NULL,
  `nombres` varchar(150) NOT NULL,
  `semestre` varchar(50) DEFAULT NULL,
  `carrera` varchar(100) DEFAULT NULL,
  `correo` varchar(255) NOT NULL,
  `turno` varchar(50) DEFAULT NULL,
  `user_id` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `telefono` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `club_registrations`
--

INSERT INTO `club_registrations` (`id`, `club_type`, `club_name`, `paterno`, `materno`, `nombres`, `semestre`, `carrera`, `correo`, `turno`, `user_id`, `created_at`, `telefono`) VALUES
(64, 'cultural', 'Fotografía/Video', 'vazquez', 'tovar', 'jaret', '6to Semestre', 'PROGRAMACIÓN', 'ajarettovar@gmail.com', 'Matutino', '@al55770615', '2025-11-16 19:08:33', '2234567890'),
(65, 'civil', 'Banda de guerra', 'vazquez', 'tovar', 'jaret', '6to Semestre', 'PROGRAMACIÓN', 'ajarettovar@gmail.com', 'Matutino', '@al55770615', '2025-11-16 19:08:41', '2234567890'),
(66, 'civil', 'Escolta', 'vazquez', 'tovar', 'jaret', '6to Semestre', 'PROGRAMACIÓN', 'ajarettovar@gmail.com', 'Matutino', '@al55770615', '2025-11-16 19:20:02', '2234567890');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `correo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tutoring_registrations`
--

INSERT INTO `tutoring_registrations` (`id`, `materia`, `paterno`, `materno`, `nombres`, `carrera`, `turno`, `maestro`, `telefono`, `user_id`, `created_at`, `correo`) VALUES
(21, 'Matemáticas 1', 'vazquez', 'tovar', 'jaret', 'PROGRAMACION', 'matutino', 'Maestra Mónica León', '2234567890', '@al55770615', '2025-11-16 19:49:37', 'ajarettovar@gmail.com'),
(22, 'Matemáticas 2', 'vazquez', 'tovar', 'jaret', 'PROGRAMACIÓN', 'Matutino', 'Maestra Ana González', '2234567890', '@al55770615', '2025-11-16 20:04:57', 'ajarettovar@gmail.com'),
(23, 'Matemáticas 3', 'vazquez', 'tovar', 'jaret', 'PROGRAMACIÓN', 'Matutino', 'Maestra Ana González', '2234567890', '@al55770615', '2025-11-16 20:48:44', 'ajarettovar@gmail.com');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `nombres` varchar(255) DEFAULT NULL,
  `paterno` varchar(255) DEFAULT NULL,
  `materno` varchar(255) DEFAULT NULL,
  `telefono` varchar(10) DEFAULT NULL,
  `semestre` varchar(255) DEFAULT NULL,
  `grupo` varchar(10) DEFAULT NULL,
  `carrera` varchar(255) DEFAULT NULL,
  `turno` varchar(255) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `user_id`, `email`, `username`, `password_hash`, `role`, `created_at`, `nombres`, `paterno`, `materno`, `telefono`, `semestre`, `grupo`, `carrera`, `turno`, `profile_picture`) VALUES
(134, '@ma14927016', 'alberto.vazquez.alp@cbtis258.edu.mx', 'jaret vazquez', '$2y$10$EHPutC5gG.pk8NHEYZKl1.TH2ZbTmYeM3ZhabdF7lYqUNIOHpzyKW', 'teacher', '2025-11-16 19:07:17', 'jaret', 'vazquez', 'tovar', '1234567890', '', NULL, '', '', NULL),
(135, '@al55770615', 'ajarettovar@gmail.com', 'jaret vazquez', '$2y$10$Qwu0eOHOaEcUerVeHVfQIuib0zdx/HMKw3znthx.UqncmsTwpJRUO', 'student', '2025-11-16 19:08:07', 'jaret', 'vazquez', 'tovar', '2234567890', '6to Semestre', 'A', 'PROGRAMACIÓN', 'Matutino', 'assets/profile_pics/user_135_691a2396be2db3.82094161.png');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clubs`
--
ALTER TABLE `clubs`
  ADD PRIMARY KEY (`club_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `tutoring_registrations`
--
ALTER TABLE `tutoring_registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=136;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
