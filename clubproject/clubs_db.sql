-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-11-2025 a las 15:31:49
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
  `club_type` enum('cultural','deportivo','civil') NOT NULL,
  `club_name` varchar(150) NOT NULL,
  `paterno` varchar(100) NOT NULL,
  `materno` varchar(100) NOT NULL,
  `nombres` varchar(150) NOT NULL,
  `semestre` varchar(50) DEFAULT NULL,
  `correo` varchar(255) NOT NULL,
  `turno` varchar(50) DEFAULT NULL,
  `user_id` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `telefono` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `club_registrations`
--

INSERT INTO `club_registrations` (`id`, `club_type`, `club_name`, `paterno`, `materno`, `nombres`, `semestre`, `correo`, `turno`, `user_id`, `created_at`, `telefono`) VALUES
(11, 'cultural', 'Fotografía', 'gonzalez', 'rojas', 'ana', '4to semestre', 'alberto@gmail.com', 'matutino', '@alp_2025_10', '2025-10-18 19:21:51', '8111111111'),
(26, 'civil', 'Escolta', 'torres', 'garcia', 'diego', '6to semestre', 'diego.torres@gmail.com', 'matutino', '@alp_2025_05', '2025-10-18 19:21:51', '1111111111'),
(27, 'civil', 'Banda de guerra', 'ramos', 'fernandez', 'fernando', '5to', 'fernando.ramos@cbtis258.edu.mx', 'vespertino', '@alp_2025_06', '2025-10-18 19:21:51', NULL),
(30, 'civil', 'Escolta', 'vera', 'molina', 'diana', '1ro', 'diana.vera@gmail.com', 'matutino', '@alp_2025_09', '2025-10-18 19:21:51', NULL),
(31, 'civil', 'Banda de guerra', 'molina', 'gomez', 'jose', '6to', 'jose.molina@cbtis258.edu.mx', 'vespertino', '@alp_2025_10', '2025-10-18 19:21:51', NULL),
(47, 'deportivo', 'Fútbol', 'castroaaa', 'fernandez', 'sebastian', '5to', 'sebastian.castro@gmail.com', 'vespertino', '@alp_2025_26', '2025-10-18 19:30:40', NULL),
(48, 'deportivo', 'Voleibol', 'fernandez', 'lopez', 'camila', '3ro', 'camila.fernandez@cbtis258.edu.mx', 'matutino', '@alp_2025_27', '2025-10-18 19:30:40', NULL),
(50, 'deportivo', 'Basquetbol', 'morales', 'ramirez', 'isabella', '1ro', 'isabella.morales@cbtis258.edu.mx', 'matutino', '@alp_2025_29', '2025-10-18 19:30:40', NULL),
(51, 'deportivo', 'Natación', 'ramirez', 'rojas', 'julian', '6to', 'julian.ramirez@gmail.com', 'vespertino', '@alp_2025_30', '2025-10-18 19:30:40', NULL),
(63, 'cultural', 'Danza y Baile', 'hernandez', 'aasdas', 'elsoua', '4to semestre', 'ana.gomez@cbtis258.edu.mx', 'matutino', '@al990729', '2025-11-02 03:50:26', '8123456789');

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

--
-- Volcado de datos para la tabla `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `token`, `expires_at`, `used`, `created_at`) VALUES
(1, '@tea_2025_11', '5d34843114e2abfb277aa53bfb576083', '2025-10-29 07:01:06', 0, '2025-10-29 05:01:06'),
(2, '@tea_2025_11', '485934', '2025-10-29 06:38:02', 1, '2025-10-29 05:28:02'),
(3, '@tea_2025_11', '938105', '2025-10-29 06:47:21', 1, '2025-10-29 05:37:21'),
(4, '@tea_2025_11', '417915', '2025-10-30 02:38:02', 1, '2025-10-30 01:28:02'),
(5, '@tea_2025_11', '574589', '2025-10-30 02:58:02', 1, '2025-10-30 01:48:02'),
(6, '@tea_2025_11', '785223', '2025-10-30 03:03:03', 1, '2025-10-30 01:53:03'),
(7, '@tea_2025_11', '962031', '2025-10-30 03:19:12', 1, '2025-10-30 02:09:12'),
(8, '@tea_2025_11', '152872', '2025-10-30 03:23:15', 0, '2025-10-30 02:13:15'),
(9, '@tea_2025_11', '388070', '2025-10-30 04:32:01', 0, '2025-10-30 03:22:01'),
(10, '@tea_2025_11', '185073', '2025-10-30 04:32:04', 0, '2025-10-30 03:22:04'),
(11, '@tea_2025_11', '440762', '2025-10-30 05:21:24', 0, '2025-10-30 04:11:24'),
(12, '@tea_2025_11', '531608', '2025-10-30 05:21:37', 1, '2025-10-30 04:11:37'),
(13, '@tea_2025_11', '468746', '2025-10-30 20:04:01', 1, '2025-10-30 18:54:01');

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
(1, 'Matemáticas', 'ramirez', 'lopez', 'jorge', 'Sistemas', 'matutino', 'Prof. Jorge Ramírez', '8111111111', '@tea_2025_01', '2025-10-18 19:21:51', NULL),
(2, 'Inglés', 'sanchez', 'rojas', 'maria', 'Administración', 'vespertino', 'Maestra María Sánchez', '8222222222', '@tea_2025_02', '2025-10-18 19:21:51', NULL),
(3, 'Física', 'ruiz', 'garcia', 'carlos', 'Electrónica', 'matutino', 'Prof. Carlos Ruiz', '8333333333', '@tea_2025_03', '2025-10-18 19:21:51', NULL),
(4, 'Química', 'martinez', 'lopez', 'laura', 'Química', 'vespertino', 'Maestra Laura Martínez', '8444444444', '@tea_2025_04', '2025-10-18 19:21:51', NULL),
(5, 'Programación', 'gomez', 'fernandez', 'eduardo', 'Informática', 'matutino', 'Prof. Eduardo Gómez', '8555555555', '@tea_2025_05', '2025-10-18 19:21:51', NULL),
(6, 'Contabilidad', 'fernandez', 'santos', 'patricia', 'Contaduría', 'vespertino', 'Maestra Patricia Fernández', '8666666666', '@tea_2025_06', '2025-10-18 19:21:51', NULL),
(7, 'Historia', 'castillo', 'vera', 'roberto', 'Humanidades', 'matutino', 'Prof. Roberto Castillo', '8777777777', '@tea_2025_07', '2025-10-18 19:21:51', NULL),
(8, 'Biología', 'leon', 'molina', 'monica', 'Biotecnología', 'vespertino', 'Maestra Mónica León', '8888888888', '@tea_2025_08', '2025-10-18 19:21:51', NULL),
(9, 'Geometría', 'morales', 'gomez', 'sergio', 'Construcción', 'matutino', 'Prof. Sergio Morales', '8999999999', '@tea_2025_09', '2025-10-18 19:21:51', NULL),
(10, 'Estadística', 'vargas', 'lopez', 'adriana', 'Administración', 'vespertino', 'Maestra Adriana Vargas', '8000000000', '@tea_2025_10', '2025-10-18 19:21:51', NULL),
(11, 'Matemáticas', 'gonzalez', 'rojas', 'ana', 'Sistemas', 'matutino', 'Maestra Ana González', '8111111111', '@tea_2025_21', '2025-10-18 19:30:40', NULL),
(12, 'Inglés', 'herrera', 'soto', 'luis', 'Administración', 'vespertino', 'Prof. Luis Herrera', '8222222222', '@tea_2025_22', '2025-10-18 19:30:40', NULL),
(13, 'Física', 'martinez', 'mendez', 'carla', 'Electrónica', 'matutino', 'Maestra Carla Martínez', '8333333333', '@tea_2025_23', '2025-10-18 19:30:40', NULL),
(14, 'Química', 'navarro', 'vargas', 'jorge', 'Química', 'vespertino', 'Prof. Jorge Navarro', '8444444444', '@tea_2025_24', '2025-10-18 19:30:40', NULL),
(15, 'Programación', 'salazar', 'ortega', 'monica', 'Informática', 'matutino', 'Maestra Mónica Salazar', '8555555555', '@tea_2025_25', '2025-10-18 19:30:40', NULL),
(16, 'Contabilidad', 'torres', 'castro', 'eduardo', 'Contaduría', 'vespertino', 'Prof. Eduardo Torres', '8666666666', '@tea_2025_26', '2025-10-18 19:30:40', NULL),
(17, 'Historia', 'vazquez', 'fernandez', 'patricia', 'Humanidades', 'matutino', 'Maestra Patricia Vázquez', '8777777777', '@tea_2025_27', '2025-10-18 19:30:40', NULL),
(18, 'Biología', 'molina', 'lopez', 'roberto', 'Biotecnología', 'vespertino', 'Prof. Roberto Molina', '8888888888', '@tea_2025_28', '2025-10-18 19:30:40', NULL),
(19, 'Geometría', 'cervantes', 'morales', 'laura', 'Construcción', 'matutino', 'Maestra Laura Cervantes', '8999999999', '@tea_2025_29', '2025-10-18 19:30:40', NULL),
(20, 'Estadística', 'rios', 'ramirez', 'fernando', 'Administración', 'vespertino', 'Prof. Fernando Ríos', '8000000000', '@tea_2025_30', '2025-10-18 19:30:40', NULL);

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
  `nombres` varchar(255) NOT NULL,
  `paterno` varchar(255) NOT NULL,
  `materno` varchar(255) NOT NULL,
  `telefono` varchar(10) NOT NULL,
  `semestre` varchar(255) NOT NULL,
  `turno` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `user_id`, `email`, `username`, `password_hash`, `role`, `created_at`, `nombres`, `paterno`, `materno`, `telefono`, `semestre`, `turno`) VALUES
(1, '@alp_2025', 'elsy.hernandez.alp@cbtis258.edu.mx', 'elsy ignacio', '$2y$10$wyi.3lfBbDCv1sPkFObRTOTbKkxH20OKRKULyg.H2bLgUvQCXI4Y.', 'student', '2025-10-17 14:33:10', '', '', '', '', '', ''),
(2, '@tea_2025', 'ilse.hernandez.alp@cbtis258.edu.mx', 'maestra maria', '$2y$10$jelPdf5hzi0PZ6zzNEhDsKe1YjtkchZxW7GUMWWAUpUTEnh5mNfG5ma', 'teacher', '2025-10-17 14:36:28', '', '', '', '', '', ''),
(3, '@tea_2025_11', 'alberto.vazquez.alp@cbtis258.edu.mx', 'jaretvzz_', '$2y$10$pctHIBlTsTLbficjXRccpe2JjXgyt54YHqgGzhmsTD7gpMKtlZzdu', 'teacher', '2025-10-18 19:14:48', 'Alberto Jaret', 'Vazquez', 'Tovar', '', '5to semestre', 'Matutino'),
(4, '@alp_2025_01', 'ana.gomez@cbtis258.edu.mx', 'Ana Gómez', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:21:51', '', '', '', '', '', ''),
(5, '@alp_2025_02', 'luis.martinez@gmail.com', 'Luis Martínez', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:21:51', '', '', '', '', '', ''),
(6, '@alp_2025_03', 'karla.lopez@cbtis258.edu.mx', 'Karla López', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:21:51', '', '', '', '', '', ''),
(7, '@alp_2025_04', 'sofia.mendez@cbtis258.edu.mx', 'Sofía Méndez', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:21:51', '', '', '', '', '', ''),
(8, '@alp_2025_05', 'diego.torres@gmail.com', 'Diego Torres', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:21:51', '', '', '', '', '', ''),
(9, '@alp_2025_06', 'fernando.ramos@cbtis258.edu.mx', 'Fernando Ramos', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:21:51', '', '', '', '', '', ''),
(10, '@alp_2025_07', 'valeria.nunez@gmail.com', 'Valeria Núñez', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:21:51', '', '', '', '', '', ''),
(11, '@alp_2025_08', 'ricardo.santos@cbtis258.edu.mx', 'Ricardo Santos', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:21:51', '', '', '', '', '', ''),
(12, '@alp_2025_09', 'diana.vera@gmail.com', 'Diana Vera', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:21:51', '', '', '', '', '', ''),
(13, '@alp_2025_10', 'jose.molina@cbtis258.edu.mx', 'José Molina', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:21:51', '', '', '', '', '', ''),
(14, '@tea_2025_01', 'jorge.ramirez@cbtis258.edu.mx', 'Prof. Jorge Ramírez', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:21:51', '', '', '', '', '', ''),
(15, '@tea_2025_02', 'maria.sanchez@gmail.com', 'Maestra María Sánchez', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:21:51', '', '', '', '', '', ''),
(16, '@tea_2025_03', 'carlos.ruiz@cbtis258.edu.mx', 'Prof. Carlos Ruiz', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:21:51', '', '', '', '', '', ''),
(17, '@tea_2025_04', 'laura.martinez@gmail.com', 'Maestra Laura Martínez', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:21:51', '', '', '', '', '', ''),
(18, '@tea_2025_05', 'eduardo.gomez@cbtis258.edu.mx', 'Prof. Eduardo Gómez', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:21:51', '', '', '', '', '', ''),
(19, '@tea_2025_06', 'patricia.fernandez@gmail.com', 'Maestra Patricia Fernández', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:21:51', '', '', '', '', '', ''),
(20, '@tea_2025_07', 'roberto.castillo@cbtis258.edu.mx', 'Prof. Roberto Castillo', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:21:51', '', '', '', '', '', ''),
(21, '@tea_2025_08', 'monica.leon@gmail.com', 'Maestra Mónica León', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:21:51', '', '', '', '', '', ''),
(22, '@tea_2025_09', 'sergio.morales@cbtis258.edu.mx', 'Prof. Sergio Morales', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:21:51', '', '', '', '', '', ''),
(23, '@tea_2025_10', 'adriana.vargas@gmail.com', 'Maestra Adriana Vargas', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:21:51', '', '', '', '', '', ''),
(24, '@alp_2025_11', 'marco.delgado@cbtis258.edu.mx', 'Marco Delgado', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:26:21', '', '', '', '', '', ''),
(25, '@alp_2025_12', 'paula.estrada@gmail.com', 'Paula Estrada', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:26:21', '', '', '', '', '', ''),
(26, '@alp_2025_13', 'alejandro.flores@cbtis258.edu.mx', 'Alejandro Flores', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:26:21', '', '', '', '', '', ''),
(27, '@alp_2025_14', 'camila.garcia@gmail.com', 'Camila García', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:26:21', '', '', '', '', '', ''),
(28, '@alp_2025_15', 'julio.herrera@cbtis258.edu.mx', 'Julio Herrera', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:26:21', '', '', '', '', '', ''),
(29, '@alp_2025_16', 'valentina.ibarra@gmail.com', 'Valentina Ibarra', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:26:21', '', '', '', '', '', ''),
(30, '@alp_2025_17', 'sebastian.jimenez@cbtis258.edu.mx', 'Sebastián Jiménez', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:26:21', '', '', '', '', '', ''),
(31, '@alp_2025_18', 'renata.luna@gmail.com', 'Renata Luna', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:26:21', '', '', '', '', '', ''),
(32, '@alp_2025_19', 'andres.moreno@cbtis258.edu.mx', 'Andrés Moreno', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:26:21', '', '', '', '', '', ''),
(33, '@alp_2025_20', 'isabela.navarro@gmail.com', 'Isabela Navarro', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:26:21', '', '', '', '', '', ''),
(74, '@alp_2025_21', 'emma.rojas@cbtis258.edu.mx', 'Emma Rojas', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:30:40', '', '', '', '', '', ''),
(75, '@alp_2025_22', 'daniel.soto@gmail.com', 'Daniel Soto', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:30:40', '', '', '', '', '', ''),
(76, '@alp_2025_23', 'lucia.mendez@cbtis258.edu.mx', 'Lucía Méndez', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:30:40', '', '', '', '', '', ''),
(77, '@alp_2025_24', 'mateo.vargas@gmail.com', 'Mateo Vargas', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:30:40', '', '', '', '', '', ''),
(78, '@alp_2025_25', 'valeria.ortega@cbtis258.edu.mx', 'Valeria Ortega', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:30:40', '', '', '', '', '', ''),
(79, '@alp_2025_26', 'sebastian.castro@gmail.com', 'Sebastián Castro', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:30:40', '', '', '', '', '', ''),
(80, '@alp_2025_27', 'camila.fernandez@cbtis258.edu.mx', 'Camila Fernández', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:30:40', '', '', '', '', '', ''),
(81, '@alp_2025_28', 'andres.lopez@gmail.com', 'Andrés López', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:30:40', '', '', '', '', '', ''),
(82, '@alp_2025_29', 'isabella.morales@cbtis258.edu.mx', 'Isabella Morales', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:30:40', '', '', '', '', '', ''),
(83, '@alp_2025_30', 'julian.ramirez@gmail.com', 'Julián Ramírez', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:30:40', '', '', '', '', '', ''),
(84, '@tea_2025_21', 'ana.gonzalez@cbtis258.edu.mx', 'Maestra Ana González', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:30:40', '', '', '', '', '', ''),
(85, '@tea_2025_22', 'luis.herrera@gmail.com', 'Prof. Luis Herrera', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:30:40', '', '', '', '', '', ''),
(86, '@tea_2025_23', 'carla.martinez@cbtis258.edu.mx', 'Maestra Carla Martínez', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:30:40', '', '', '', '', '', ''),
(87, '@tea_2025_24', 'jorge.navarro@gmail.com', 'Prof. Jorge Navarro', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:30:40', '', '', '', '', '', ''),
(88, '@tea_2025_25', 'monica.salazar@cbtis258.edu.mx', 'Maestra Mónica Salazar', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:30:40', '', '', '', '', '', ''),
(89, '@tea_2025_26', 'eduardo.torres@gmail.com', 'Prof. Eduardo Torres', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:30:40', '', '', '', '', '', ''),
(90, '@tea_2025_27', 'patricia.vazquez@cbtis258.edu.mx', 'Maestra Patricia Vázquez', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:30:40', '', '', '', '', '', ''),
(91, '@tea_2025_28', 'roberto.molina@gmail.com', 'Prof. Roberto Molina', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:30:40', '', '', '', '', '', ''),
(92, '@tea_2025_29', 'laura.cervantes@cbtis258.edu.mx', 'Maestra Laura Cervantes', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:30:40', '', '', '', '', '', ''),
(93, '@tea_2025_30', 'fernando.rios@gmail.com', 'Prof. Fernando Ríos', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:30:40', '', '', '', '', '', ''),
(94, '@alp_2025_021', 'ajarettovar@gmail.com', 'jaret', '$2y$10$w4LXiJ4HK/lOWKeeDTqACuAxyQkgO/nns0cbMKXnC7Enqv8sVX5dy', 'student', '2025-10-18 20:05:56', '', '', '', '', '', ''),
(95, '@alp_2025_022', 'alberto.alp@cbtis258.edu.mx', 'jaretvzz_1', '$2y$10$vuJK62K9abfL7Q9pml0PGu0u9A6rsQEsHWmnv.vz6jdQaYaHz0nAu', 'student', '2025-10-19 04:39:40', '', '', '', '', '', ''),
(96, '@tea_2025_0111', 'asd@gmail.com', 'asd', '$2y$10$groMeM9yM69tKJ5vRLw6H.qEOlZJ1i./es5ysqpOk7m4JgVnY6wt6', 'teacher', '2025-10-19 06:36:15', '', '', '', '', '', ''),
(97, '@tea_2025_01111', '123@gmail.com', 'asd', '$2y$10$H2K5ICXbISCjbEW0QBAk..ik/HWiPzr2bm6zLeCh/xT50hJJoWtwS', 'teacher', '2025-10-19 06:36:50', '', '', '', '', '', ''),
(98, '@alp_2025_777', 'vegetta777@gmail.com', 'vegetta777', '$2y$10$ZCmPDjhQ7PivyiXdIEzq8.PsaL6HE38UWC69ALaUMWtsH2hL00hdG', 'student', '2025-10-19 16:38:22', '', '', '', '', '', ''),
(99, '@tea_2025_willy', 'willy@gmail.com', 'willyrex', '$2y$10$F1.nd3H1I4YiwK36FXWVGet2pv/FXLLrte9L8RI4ZRi.5c8hrmwh6', 'teacher', '2025-10-19 16:53:30', '', '', '', '', '', ''),
(100, '@al831125', 'alberto.vazqasdfasdfuez.alp@cbtis258.edu.mx', 'jaretvz', '$2y$10$gbf9vsoDwjRpEZtOLcSuHe4R/DpqltAqdx6wXzWcu324qNf7F/zKW', 'student', '2025-10-28 06:30:10', '', '', '', '', '', ''),
(101, '@ma484721', 'alb@cbtis258.edu.mx', 'jaretvz', '$2y$10$IzioXLLWT9rqNzYrzAuQHeRvJ7dt6TaPlU9NTthx4Jt3q4CZN22G.', 'teacher', '2025-10-28 06:30:46', '', '', '', '', '', ''),
(102, '@al467195', 'alberto.vazquafasfez.alp@cbtis258.edu.mx', 'jaretvz', '$2y$10$YFgGJeQxrqyIQcOXQnZEdeo0yXfcrSjZjgBZsLBC9fnSsBA600msy', 'student', '2025-10-28 06:38:07', '', '', '', '', '', ''),
(103, '@al776512', '1@gmail.com', 'jaretvz', '$2y$10$Hs0cpayYOvi7jWis8NHN4O6NIFCs8tXKC/EtuCv4NBNBcphf52XGS', 'student', '2025-10-28 13:19:38', '', '', '', '', '', ''),
(104, '@al501360', 'ajarettovaaar@gmail.com', 'jaretvz', '$2y$10$xfTjBer5fjv3doHK1Nben.9sFUNDmKfU.FHTy/3nIu0h9lq2VVi3m', 'student', '2025-10-29 04:54:04', '', '', '', '', '', ''),
(105, '@al686934', 'alberto.vazaaaaaaaqauez.alp@cbtis258.edu.mx', 'jaretvz', '$2y$10$Sfe.zP8egJyfX1MTbhK8AedfqKhvHOQMqY2tUz01stIcnTh4KwEne', 'student', '2025-10-29 04:55:10', '', '', '', '', '', ''),
(106, '@ma974196', 'alberto.vazqauez.alp@cbtis258.edu.mx', 'asd', '$2y$10$Y7clEmGIX1sx143HFFiqRuSpWNM6ybgY0ChfDkPoPZfGU3PF4Zhau', 'teacher', '2025-10-29 04:55:44', '', '', '', '', '', ''),
(107, '@al068500', 'luis.maratinez@gmail.com', 'jaretvz', '$2y$10$6T75R1P1zcnti1kbWL0U8OPs19brxG6TxB/2cDsGOrdQWwQCY0/t2', 'student', '2025-10-29 05:00:19', '', '', '', '', '', ''),
(108, '@ma425666', 'vegetaata777@gmail.com', 'jaretvz', '$2y$10$bHBgEX6.5/n4VX.FsEO80eFBZvSQKMSM4KKOb6TCaJkmjV8cey4GO', 'teacher', '2025-10-30 01:29:36', '', '', '', '', '', ''),
(109, '@ma218451', 'alberto.vazqaaaaaauez.alp@cbtis258.edu.mx', 'jaretvz', '$2y$10$Z83k7NXzHAUdJzgtSkzy2.TTb7xd2XvnW9HWsLE4cU8JiytgPxha6', 'teacher', '2025-10-30 01:32:54', '', '', '', '', '', ''),
(110, '@ma154348', 'luis.martassssinez@gmail.com', 'jaretvz', '$2y$10$ZVHeDJUjwVLkJ2gu5o7F7uyNaJmqlty6IeGC72JnciEGSBdpM8S/S', 'teacher', '2025-10-30 01:41:53', '', '', '', '', '', ''),
(111, '@al748952', 'luis.maaaartinez@gmail.com', 'jaretvz', '$2y$10$gAd/YKdglrNwKbpPM/lr3.SbPV9mMcAr6van1dR2L2j5tKdMLYLt2', 'student', '2025-10-30 06:28:01', '', '', '', '', '', ''),
(112, '@al704678', 'luis.martinezaaa@gmail.com', 'jaretvz', '$2y$10$GsZ/7G8.hVH.BCo9csQi2.syHMZlvJ3vG2V0cwt5fyziclKAvicV6', 'student', '2025-10-30 06:47:57', '', '', '', '', '', ''),
(113, '@al005911', 'luis.madasdartinez@gmail.com', 'jaretvz', '$2y$10$BA3PIBzu2iXVphH0cYR7DeldSe.2ZUGqTEuzgzKHsfZpoGUOiFp0m', 'student', '2025-10-30 07:22:31', '', '', '', '', '', ''),
(114, '@al762601', 'ajarettoaasdfsdafvar@gmail.com', 'jaretvz', '$2y$10$Twwl6fBbAHMBAXq1TZ3YOun4cPtHo/O63TBq.vZ.RhfBQNX.Wtbs2', 'student', '2025-10-30 07:27:05', '', '', '', '', '', ''),
(115, '@al712243', 'luis.martinafasdsdfaez@gmail.com', 'jaretvz', '$2y$10$B8IOf9OoRVMjGWuhpjyw9uubGUQYq3eKunqC3RbM/0T8mHqX6QUm2', 'student', '2025-10-30 07:33:25', '', '', '', '', '', ''),
(116, '@al690910', 'albadferto.vazquafasdafez.alp@cbtis258.edu.mx', 'jaretvz', '$2y$10$bGSoVgGePayMDxhQMsMtKuUWux5J4yrTWsWnMTvPUKkD9y.NhANyu', 'student', '2025-10-30 07:34:27', '', '', '', '', '', ''),
(117, '@al549019', 'AFASluis.martinez@gmail.com', 'jaretvz', '$2y$10$JZAeUIj2iyPJqpwlJJVCaOIxg2G8dnWkoYPqZsmSsGEt/CPEaKN6G', 'student', '2025-10-30 07:39:28', '', '', '', '', '', ''),
(118, '@al673214', 'vegettDSDDa777@gmail.com', 'jaretvz', '$2y$10$ysf.yjMQf/tEO/LqrYuQ.ONgK./BqcwSZpAfIL0BQChMdDRkQ6azm', 'student', '2025-10-30 07:45:04', '', '', '', '', '', ''),
(119, '@al240901', 'luis.mFAFAFAFAartinez@gmail.com', 'jaretvz', '$2y$10$1zI7Z7EETErE7dHcPTx6/utkeOE78CRfu7UpRep.3ACLFariAe0b2', 'student', '2025-10-30 07:46:20', '', '', '', '', '', ''),
(120, '@al100025', 'vegettaAAAA777@gmail.com', 'jaretvz', '$2y$10$1PjuXPRz4qqFhJhmW3OA/.v52TmT0ltW/buPKoshW4x22MzwOkDbC', 'student', '2025-10-30 07:59:31', '', '', '', '', '', ''),
(121, '@al628631', 'vegetfdslsfldflta777@gmail.com', 'jaretvz', '$2y$10$XhsLjcOccjDudtR90GcIyOBzaU.7xC4SK5oXANNUi1yL.4LaFo8Zm', 'student', '2025-10-30 17:31:08', '', '', '', '', '', ''),
(122, '@tea_2025_11aaa', 'alberto.vazqsaasssuez.alp@cbtis258.edu.mx', 'jaretvz', '$2y$10$aro9/DTU9OIgA4TW8IZ1cOp9kKOzJA2NVSQafVhn4KSDqmLOPut12', 'teacher', '2025-10-30 17:33:36', '', '', '', '', '', ''),
(123, '@alp_2025ilse', 'janeth.hernandez.alp@cbtis258.edu.mx', 'ilse', '$2y$10$.7rlvkIWzfdspJ8XpVA2P.JpkhwH4gxt27uvUrlMYqMS6hbGctQjy', 'student', '2025-10-30 17:34:48', '', '', '', '', '', ''),
(124, '@ma092847', 'jose@gmail.com', 'jaretvz', '$2y$10$MCQzI5MbIGniHMjvJHvxAO5xFV2y5qnKteAKXD7aA/CS5rpTQjGO.', 'teacher', '2025-10-30 19:11:53', '', '', '', '', '', ''),
(125, '@ma884179', '1sasasa@gmail.com', 'jaretvz', '$2y$10$e8H43GoHhpmmToDVjMkkjeQw6Rnyjhh.83/1eXuNDhm/d/TqN8Lhm', 'teacher', '2025-10-30 19:45:35', '', '', '', '', '', ''),
(126, '@al925344', 'albesasaarto.vazquez.alp@cbtis258.edu.mx', 'jaretvz', '$2y$10$cVPzRssAJyo9cPNFD2PNAeposzUjvy45wkZ694UWsy7FOuHELBCSC', 'student', '2025-11-01 22:08:23', '', '', '', '', '', ''),
(127, '@al990729', '777@gmail.com', 'vegetta', '$2y$10$JVEUIP5c9T2isyFDweq/f.88Jy90tmiBF04jEhFcH7mb3muT0JloC', 'student', '2025-11-01 22:32:58', '', '', '', '', '', ''),
(128, '@tea_2025_11asdfsdafsaddasasdasd', 'vegefsdasdfasdftta777@gmail.com', 'fasdfasdfasdfasd@gmail.com', '$2y$10$ND5wYNFOAOS0GQDyYFCK.uJGFE.8OQgQhgzcEgV7BOV8yL/3DaMW6', 'teacher', '2025-11-01 22:40:42', '', '', '', '', '', ''),
(129, '@alp_2025ilsea', 'aasdfasdfsdfa@gmail.com', 'jaretvz', '$2y$10$cWsoDKXxAkKc0lqq0/qgvuHRP5jFdHOk3tfUgLEPeYE/8bE5SbnLy', 'student', '2025-11-01 22:41:57', '', '', '', '', '', ''),
(130, '@alp_2025_02aaaaaa', 'aaaaajaraaaaaaaaettovar@gmail.com', 'dfsfasdfsadfs', '$2y$10$kNd6is7tQ47ACeeOVCFYo.4iiwdPgmLNBQnQfopSmGGQ5iQwytBCC', 'student', '2025-11-01 22:42:42', '', '', '', '', '', ''),
(131, '@alp_2025_02a', 'aalberto.vazquez.alp@cbtis258.edu.mx', 'a', '$2y$10$Kcg/CcAHClFACIAGhCf6feUdUUSuKdvSF.UsFqS8jB2OX5mUVca1e', 'student', '2025-11-01 22:43:25', '', '', '', '', '', ''),
(132, '@al43259980', 'ajaretstovar@gmail.com', 'sebastian hernandez', '$2y$10$QpM.o.RIymkKWUE9c0nbBeD3FWonmMRKGNP89iZsrCi2T7LXvRyuu', 'student', '2025-11-02 05:07:42', 'sebastian', 'hernandez', 'fernandez', '8182345671', '6', 'matutino'),
(133, '@al68827371', 'ajaretto12121var@gmail.com', 'ana hernandez', '$2y$10$K3oAqIWi29X0aSEHidn6mes.9qSoVxjIwBgMUEIUc5WWJ8GE6bmma', 'student', '2025-11-02 13:20:29', 'ana', 'hernandez', 'rojas', '1111345111', '6', 'matutino');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `tutoring_registrations`
--
ALTER TABLE `tutoring_registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
