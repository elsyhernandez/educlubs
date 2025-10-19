-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-10-2025 a las 19:23:12
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
  `password` varchar(255) NOT NULL,
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
('club1', 'Club de robotica', '$2y$10$mDFw5Ut6HkkQc6VaSscK8ecjBJNEeHK3T14.LtKh8rI.QS1Nqtkeu', 'Alberto Jaret Vazquez Tovar', 'deportivo', '2025-10-18 15:20:23'),
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
('dep008', 'Voleibol varonil', '$2y$10$hashVoleiVar', 'Profe Tomás', 'deportivo', '2025-10-19 10:52:00');

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
(1, 'cultural', 'Fotografía/Video', 'hernandez', 'ignacio', 'elso', '5to', 'elsy.hernandez.alp@cbtis258.edu.mx', 'matutino', '@alp_2025', '2025-10-17 14:34:05'),
(2, 'cultural', 'Teatro', 'gomez', 'lopez', 'ana', '4to', 'ana.gomez@cbtis258.edu.mx', 'matutino', '@alp_2025_01', '2025-10-18 19:21:51'),
(3, 'cultural', 'Música', 'martinez', 'rojas', 'luis', '5to', 'luis.martinez@gmail.com', 'vespertino', '@alp_2025_02', '2025-10-18 19:21:51'),
(4, 'cultural', 'Danza', 'lopez', 'garcia', 'karla', '6to', 'karla.lopez@cbtis258.edu.mx', 'matutino', '@alp_2025_03', '2025-10-18 19:21:51'),
(5, 'cultural', 'Fotografía', 'mendez', 'lopez', 'sofia', '4to', 'sofia.mendez@cbtis258.edu.mx', 'vespertino', '@alp_2025_04', '2025-10-18 19:21:51'),
(6, 'cultural', 'Pintura', 'torres', 'garcia', 'diego', '6to', 'diego.torres@gmail.com', 'matutino', '@alp_2025_05', '2025-10-18 19:21:51'),
(7, 'cultural', 'Coro', 'ramos', 'fernandez', 'fernando', '5to', 'fernando.ramos@cbtis258.edu.mx', 'vespertino', '@alp_2025_06', '2025-10-18 19:21:51'),
(8, 'cultural', 'Teatro', 'nunez', 'santos', 'valeria', '3ro', 'valeria.nunez@gmail.com', 'matutino', '@alp_2025_07', '2025-10-18 19:21:51'),
(9, 'cultural', 'Música', 'santos', 'vera', 'ricardo', '2do', 'ricardo.santos@cbtis258.edu.mx', 'vespertino', '@alp_2025_08', '2025-10-18 19:21:51'),
(10, 'cultural', 'Danza', 'vera', 'molina', 'diana', '1ro', 'diana.vera@gmail.com', 'matutino', '@alp_2025_09', '2025-10-18 19:21:51'),
(11, 'cultural', 'Fotografía', 'molina', 'gomez', 'jose', '6to', 'jose.molina@cbtis258.edu.mx', 'vespertino', '@alp_2025_10', '2025-10-18 19:21:51'),
(12, 'deportivo', 'Fútbol', 'gomez', 'lopez', 'ana', '4to', 'ana.gomez@cbtis258.edu.mx', 'matutino', '@alp_2025_01', '2025-10-18 19:21:51'),
(13, 'deportivo', 'Voleibol', 'martinez', 'rojas', 'luis', '5to', 'luis.martinez@gmail.com', 'vespertino', '@alp_2025_02', '2025-10-18 19:21:51'),
(14, 'deportivo', 'Atletismo', 'lopez', 'garcia', 'karla', '6to', 'karla.lopez@cbtis258.edu.mx', 'matutino', '@alp_2025_03', '2025-10-18 19:21:51'),
(15, 'deportivo', 'Basquetbol', 'mendez', 'lopez', 'sofia', '4to', 'sofia.mendez@cbtis258.edu.mx', 'vespertino', '@alp_2025_04', '2025-10-18 19:21:51'),
(16, 'deportivo', 'Natación', 'torres', 'garcia', 'diego', '6to', 'diego.torres@gmail.com', 'matutino', '@alp_2025_05', '2025-10-18 19:21:51'),
(17, 'deportivo', 'Fútbol', 'ramos', 'fernandez', 'fernando', '5to', 'fernando.ramos@cbtis258.edu.mx', 'vespertino', '@alp_2025_06', '2025-10-18 19:21:51'),
(18, 'deportivo', 'Voleibol', 'nunez', 'santos', 'valeria', '3ro', 'valeria.nunez@gmail.com', 'matutino', '@alp_2025_07', '2025-10-18 19:21:51'),
(19, 'deportivo', 'Atletismo', 'santos', 'vera', 'ricardo', '2do', 'ricardo.santos@cbtis258.edu.mx', 'vespertino', '@alp_2025_08', '2025-10-18 19:21:51'),
(20, 'deportivo', 'Basquetbol', 'vera', 'molina', 'diana', '1ro', 'diana.vera@gmail.com', 'matutino', '@alp_2025_09', '2025-10-18 19:21:51'),
(21, 'deportivo', 'Natación', 'molina', 'gomez', 'jose', '6to', 'jose.molina@cbtis258.edu.mx', 'vespertino', '@alp_2025_10', '2025-10-18 19:21:51'),
(22, 'civil', 'Escolta', 'gomez', 'lopez', 'ana', '4to', 'ana.gomez@cbtis258.edu.mx', 'matutino', '@alp_2025_01', '2025-10-18 19:21:51'),
(23, 'civil', 'Banda de guerra', 'martinez', 'rojas', 'luis', '5to', 'luis.martinez@gmail.com', 'vespertino', '@alp_2025_02', '2025-10-18 19:21:51'),
(24, 'civil', 'Escolta', 'lopez', 'garcia', 'karla', '6to', 'karla.lopez@cbtis258.edu.mx', 'matutino', '@alp_2025_03', '2025-10-18 19:21:51'),
(25, 'civil', 'Banda de guerra', 'mendez', 'lopez', 'sofia', '4to', 'sofia.mendez@cbtis258.edu.mx', 'vespertino', '@alp_2025_04', '2025-10-18 19:21:51'),
(26, 'civil', 'Escolta', 'torres', 'garcia', 'diego', '6to', 'diego.torres@gmail.com', 'matutino', '@alp_2025_05', '2025-10-18 19:21:51'),
(27, 'civil', 'Banda de guerra', 'ramos', 'fernandez', 'fernando', '5to', 'fernando.ramos@cbtis258.edu.mx', 'vespertino', '@alp_2025_06', '2025-10-18 19:21:51'),
(28, 'civil', 'Escolta', 'nunez', 'santos', 'valeria', '3ro', 'valeria.nunez@gmail.com', 'matutino', '@alp_2025_07', '2025-10-18 19:21:51'),
(29, 'civil', 'Banda de guerra', 'santos', 'vera', 'ricardo', '2do', 'ricardo.santos@cbtis258.edu.mx', 'vespertino', '@alp_2025_08', '2025-10-18 19:21:51'),
(30, 'civil', 'Escolta', 'vera', 'molina', 'diana', '1ro', 'diana.vera@gmail.com', 'matutino', '@alp_2025_09', '2025-10-18 19:21:51'),
(31, 'civil', 'Banda de guerra', 'molina', 'gomez', 'jose', '6to', 'jose.molina@cbtis258.edu.mx', 'vespertino', '@alp_2025_10', '2025-10-18 19:21:51'),
(32, 'cultural', 'Teatro', 'rojas', 'soto', 'emma', '4to', 'emma.rojas@cbtis258.edu.mx', 'matutino', '@alp_2025_21', '2025-10-18 19:30:40'),
(33, 'cultural', 'Música', 'soto', 'mendez', 'daniel', '5to', 'daniel.soto@gmail.com', 'vespertino', '@alp_2025_22', '2025-10-18 19:30:40'),
(34, 'cultural', 'Danza', 'mendez', 'vargas', 'lucia', '6to', 'lucia.mendez@cbtis258.edu.mx', 'matutino', '@alp_2025_23', '2025-10-18 19:30:40'),
(35, 'cultural', 'Fotografía', 'vargas', 'ortega', 'mateo', '4to', 'mateo.vargas@gmail.com', 'vespertino', '@alp_2025_24', '2025-10-18 19:30:40'),
(36, 'cultural', 'Pintura', 'ortega', 'castro', 'valeria', '6to', 'valeria.ortega@cbtis258.edu.mx', 'matutino', '@alp_2025_25', '2025-10-18 19:30:40'),
(37, 'cultural', 'Coro', 'castr', 'fernandez', 'sebastian', '5to', 'sebastian.castro@gmail.com', 'vespertino', '@alp_2025_26', '2025-10-18 19:30:40'),
(38, 'cultural', 'Teatro', 'fernandez', 'lopez', 'camila', '3ro', 'camila.fernandez@cbtis258.edu.mx', 'matutino', '@alp_2025_27', '2025-10-18 19:30:40'),
(39, 'cultural', 'Música', 'lopez', 'morales', 'andres', '2do', 'andres.lopez@gmail.com', 'vespertino', '@alp_2025_28', '2025-10-18 19:30:40'),
(40, 'cultural', 'Danza', 'morales', 'ramirez', 'isabella', '1ro', 'isabella.morales@cbtis258.edu.mx', 'matutino', '@alp_2025_29', '2025-10-18 19:30:40'),
(41, 'cultural', 'Fotografía', 'ramirez', 'rojas', 'julian', '6to', 'julian.ramirez@gmail.com', 'vespertino', '@alp_2025_30', '2025-10-18 19:30:40'),
(42, 'deportivo', 'Fútbol', 'rojas', 'soto', 'emma', '4to', 'emma.rojas@cbtis258.edu.mx', 'matutino', '@alp_2025_21', '2025-10-18 19:30:40'),
(43, 'deportivo', 'Voleibol', 'soto', 'mendez', 'daniel', '5to', 'daniel.soto@gmail.com', 'vespertino', '@alp_2025_22', '2025-10-18 19:30:40'),
(44, 'deportivo', 'Atletismo', 'mendez', 'vargas', 'lucia', '6to', 'lucia.mendez@cbtis258.edu.mx', 'matutino', '@alp_2025_23', '2025-10-18 19:30:40'),
(45, 'deportivo', 'Basquetbol', 'vargas', 'ortega', 'mateo', '4to', 'mateo.vargas@gmail.com', 'vespertino', '@alp_2025_24', '2025-10-18 19:30:40'),
(46, 'deportivo', 'Natación', 'ortega', 'castro', 'valeria', '6to', 'valeria.ortega@cbtis258.edu.mx', 'matutino', '@alp_2025_25', '2025-10-18 19:30:40'),
(47, 'deportivo', 'Fútbol', 'castro', 'fernandez', 'sebastian', '5to', 'sebastian.castro@gmail.com', 'vespertino', '@alp_2025_26', '2025-10-18 19:30:40'),
(48, 'deportivo', 'Voleibol', 'fernandez', 'lopez', 'camila', '3ro', 'camila.fernandez@cbtis258.edu.mx', 'matutino', '@alp_2025_27', '2025-10-18 19:30:40'),
(49, 'deportivo', 'Atletismo', 'lopez', 'morales', 'andres', '2do', 'andres.lopez@gmail.com', 'vespertino', '@alp_2025_28', '2025-10-18 19:30:40'),
(50, 'deportivo', 'Basquetbol', 'morales', 'ramirez', 'isabella', '1ro', 'isabella.morales@cbtis258.edu.mx', 'matutino', '@alp_2025_29', '2025-10-18 19:30:40'),
(51, 'deportivo', 'Natación', 'ramirez', 'rojas', 'julian', '6to', 'julian.ramirez@gmail.com', 'vespertino', '@alp_2025_30', '2025-10-18 19:30:40'),
(52, 'civil', 'Escolta', 'rojas', 'soto', 'emma', '4to', 'emma.rojas@cbtis258.edu.mx', 'matutino', '@alp_2025_21', '2025-10-18 19:30:40'),
(53, 'civil', 'Banda de guerra', 'soto', 'mendez', 'daniel', '5to', 'daniel.soto@gmail.com', 'vespertino', '@alp_2025_22', '2025-10-18 19:30:40'),
(54, 'civil', 'Escolta', 'mendez', 'vargas', 'lucia', '6to', 'lucia.mendez@cbtis258.edu.mx', 'matutino', '@alp_2025_23', '2025-10-18 19:30:40'),
(55, 'civil', 'Banda de guerra', 'vargas', 'ortega', 'mateo', '4to', 'mateo.vargas@gmail.com', 'vespertino', '@alp_2025_24', '2025-10-18 19:30:40'),
(56, 'civil', 'Escolta', 'ortega', 'castro', 'valeria', '6to', 'valeria.ortega@cbtis258.edu.mx', 'matutino', '@alp_2025_25', '2025-10-18 19:30:40'),
(57, 'civil', 'Banda de guerra', 'castro', 'fernandez', 'sebastian', '5to', 'sebastian.castro@gmail.com', 'vespertino', '@alp_2025_26', '2025-10-18 19:30:40'),
(58, 'civil', 'Escolta', 'fernandez', 'lopez', 'camila', '3ro', 'camila.fernandez@cbtis258.edu.mx', 'matutino', '@alp_2025_27', '2025-10-18 19:30:40'),
(59, 'civil', 'Banda de guerra', 'lopez', 'morales', 'andres', '2do', 'andres.lopez@gmail.com', 'vespertino', '@alp_2025_28', '2025-10-18 19:30:40'),
(60, 'civil', 'Escolta', 'morales', 'ramirez', 'isabella', '1ro', 'isabella.morales@cbtis258.edu.mx', 'matutino', '@alp_2025_29', '2025-10-18 19:30:40'),
(61, 'civil', 'Banda de guerra', 'ramirez', 'rojas', 'julian', '6to', 'julian.ramirez@gmail.com', 'vespertino', '@alp_2025_30', '2025-10-18 19:30:40');

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

--
-- Volcado de datos para la tabla `tutoring_registrations`
--

INSERT INTO `tutoring_registrations` (`id`, `materia`, `paterno`, `materno`, `nombres`, `carrera`, `turno`, `maestro`, `telefono`, `user_id`, `created_at`) VALUES
(1, 'Matemáticas', 'ramirez', 'lopez', 'jorge', 'Sistemas', 'matutino', 'Prof. Jorge Ramírez', '8111111111', '@tea_2025_01', '2025-10-18 19:21:51'),
(2, 'Inglés', 'sanchez', 'rojas', 'maria', 'Administración', 'vespertino', 'Maestra María Sánchez', '8222222222', '@tea_2025_02', '2025-10-18 19:21:51'),
(3, 'Física', 'ruiz', 'garcia', 'carlos', 'Electrónica', 'matutino', 'Prof. Carlos Ruiz', '8333333333', '@tea_2025_03', '2025-10-18 19:21:51'),
(4, 'Química', 'martinez', 'lopez', 'laura', 'Química', 'vespertino', 'Maestra Laura Martínez', '8444444444', '@tea_2025_04', '2025-10-18 19:21:51'),
(5, 'Programación', 'gomez', 'fernandez', 'eduardo', 'Informática', 'matutino', 'Prof. Eduardo Gómez', '8555555555', '@tea_2025_05', '2025-10-18 19:21:51'),
(6, 'Contabilidad', 'fernandez', 'santos', 'patricia', 'Contaduría', 'vespertino', 'Maestra Patricia Fernández', '8666666666', '@tea_2025_06', '2025-10-18 19:21:51'),
(7, 'Historia', 'castillo', 'vera', 'roberto', 'Humanidades', 'matutino', 'Prof. Roberto Castillo', '8777777777', '@tea_2025_07', '2025-10-18 19:21:51'),
(8, 'Biología', 'leon', 'molina', 'monica', 'Biotecnología', 'vespertino', 'Maestra Mónica León', '8888888888', '@tea_2025_08', '2025-10-18 19:21:51'),
(9, 'Geometría', 'morales', 'gomez', 'sergio', 'Construcción', 'matutino', 'Prof. Sergio Morales', '8999999999', '@tea_2025_09', '2025-10-18 19:21:51'),
(10, 'Estadística', 'vargas', 'lopez', 'adriana', 'Administración', 'vespertino', 'Maestra Adriana Vargas', '8000000000', '@tea_2025_10', '2025-10-18 19:21:51'),
(11, 'Matemáticas', 'gonzalez', 'rojas', 'ana', 'Sistemas', 'matutino', 'Maestra Ana González', '8111111111', '@tea_2025_21', '2025-10-18 19:30:40'),
(12, 'Inglés', 'herrera', 'soto', 'luis', 'Administración', 'vespertino', 'Prof. Luis Herrera', '8222222222', '@tea_2025_22', '2025-10-18 19:30:40'),
(13, 'Física', 'martinez', 'mendez', 'carla', 'Electrónica', 'matutino', 'Maestra Carla Martínez', '8333333333', '@tea_2025_23', '2025-10-18 19:30:40'),
(14, 'Química', 'navarro', 'vargas', 'jorge', 'Química', 'vespertino', 'Prof. Jorge Navarro', '8444444444', '@tea_2025_24', '2025-10-18 19:30:40'),
(15, 'Programación', 'salazar', 'ortega', 'monica', 'Informática', 'matutino', 'Maestra Mónica Salazar', '8555555555', '@tea_2025_25', '2025-10-18 19:30:40'),
(16, 'Contabilidad', 'torres', 'castro', 'eduardo', 'Contaduría', 'vespertino', 'Prof. Eduardo Torres', '8666666666', '@tea_2025_26', '2025-10-18 19:30:40'),
(17, 'Historia', 'vazquez', 'fernandez', 'patricia', 'Humanidades', 'matutino', 'Maestra Patricia Vázquez', '8777777777', '@tea_2025_27', '2025-10-18 19:30:40'),
(18, 'Biología', 'molina', 'lopez', 'roberto', 'Biotecnología', 'vespertino', 'Prof. Roberto Molina', '8888888888', '@tea_2025_28', '2025-10-18 19:30:40'),
(19, 'Geometría', 'cervantes', 'morales', 'laura', 'Construcción', 'matutino', 'Maestra Laura Cervantes', '8999999999', '@tea_2025_29', '2025-10-18 19:30:40'),
(20, 'Estadística', 'rios', 'ramirez', 'fernando', 'Administración', 'vespertino', 'Prof. Fernando Ríos', '8000000000', '@tea_2025_30', '2025-10-18 19:30:40');

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
(2, '@tea_2025', 'ilse.hernandez.alp@cbtis258.edu.mx', 'maestra maria', '$2y$10$IezmucrghF/Jv2RGGKuZGOlqngTbCv6cedVC7j395lWoMqZMmZs7O', 'teacher', '2025-10-17 14:36:28'),
(3, '@tea_2025_11', 'alberto.vazquez.alp@cbtis258.edu.mx', 'jaretvzz_', '$2y$10$yX3aOtxTw8wDxq2cNUZaBukKDui7bO/knZWjtryIdy3noYIjdgGse', 'teacher', '2025-10-18 19:14:48'),
(4, '@alp_2025_01', 'ana.gomez@cbtis258.edu.mx', 'Ana Gómez', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:21:51'),
(5, '@alp_2025_02', 'luis.martinez@gmail.com', 'Luis Martínez', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:21:51'),
(6, '@alp_2025_03', 'karla.lopez@cbtis258.edu.mx', 'Karla López', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:21:51'),
(7, '@alp_2025_04', 'sofia.mendez@cbtis258.edu.mx', 'Sofía Méndez', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:21:51'),
(8, '@alp_2025_05', 'diego.torres@gmail.com', 'Diego Torres', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:21:51'),
(9, '@alp_2025_06', 'fernando.ramos@cbtis258.edu.mx', 'Fernando Ramos', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:21:51'),
(10, '@alp_2025_07', 'valeria.nunez@gmail.com', 'Valeria Núñez', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:21:51'),
(11, '@alp_2025_08', 'ricardo.santos@cbtis258.edu.mx', 'Ricardo Santos', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:21:51'),
(12, '@alp_2025_09', 'diana.vera@gmail.com', 'Diana Vera', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:21:51'),
(13, '@alp_2025_10', 'jose.molina@cbtis258.edu.mx', 'José Molina', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:21:51'),
(14, '@tea_2025_01', 'jorge.ramirez@cbtis258.edu.mx', 'Prof. Jorge Ramírez', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:21:51'),
(15, '@tea_2025_02', 'maria.sanchez@gmail.com', 'Maestra María Sánchez', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:21:51'),
(16, '@tea_2025_03', 'carlos.ruiz@cbtis258.edu.mx', 'Prof. Carlos Ruiz', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:21:51'),
(17, '@tea_2025_04', 'laura.martinez@gmail.com', 'Maestra Laura Martínez', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:21:51'),
(18, '@tea_2025_05', 'eduardo.gomez@cbtis258.edu.mx', 'Prof. Eduardo Gómez', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:21:51'),
(19, '@tea_2025_06', 'patricia.fernandez@gmail.com', 'Maestra Patricia Fernández', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:21:51'),
(20, '@tea_2025_07', 'roberto.castillo@cbtis258.edu.mx', 'Prof. Roberto Castillo', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:21:51'),
(21, '@tea_2025_08', 'monica.leon@gmail.com', 'Maestra Mónica León', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:21:51'),
(22, '@tea_2025_09', 'sergio.morales@cbtis258.edu.mx', 'Prof. Sergio Morales', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:21:51'),
(23, '@tea_2025_10', 'adriana.vargas@gmail.com', 'Maestra Adriana Vargas', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:21:51'),
(24, '@alp_2025_11', 'marco.delgado@cbtis258.edu.mx', 'Marco Delgado', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:26:21'),
(25, '@alp_2025_12', 'paula.estrada@gmail.com', 'Paula Estrada', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:26:21'),
(26, '@alp_2025_13', 'alejandro.flores@cbtis258.edu.mx', 'Alejandro Flores', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:26:21'),
(27, '@alp_2025_14', 'camila.garcia@gmail.com', 'Camila García', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:26:21'),
(28, '@alp_2025_15', 'julio.herrera@cbtis258.edu.mx', 'Julio Herrera', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:26:21'),
(29, '@alp_2025_16', 'valentina.ibarra@gmail.com', 'Valentina Ibarra', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:26:21'),
(30, '@alp_2025_17', 'sebastian.jimenez@cbtis258.edu.mx', 'Sebastián Jiménez', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:26:21'),
(31, '@alp_2025_18', 'renata.luna@gmail.com', 'Renata Luna', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:26:21'),
(32, '@alp_2025_19', 'andres.moreno@cbtis258.edu.mx', 'Andrés Moreno', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:26:21'),
(33, '@alp_2025_20', 'isabela.navarro@gmail.com', 'Isabela Navarro', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:26:21'),
(74, '@alp_2025_21', 'emma.rojas@cbtis258.edu.mx', 'Emma Rojas', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:30:40'),
(75, '@alp_2025_22', 'daniel.soto@gmail.com', 'Daniel Soto', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:30:40'),
(76, '@alp_2025_23', 'lucia.mendez@cbtis258.edu.mx', 'Lucía Méndez', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:30:40'),
(77, '@alp_2025_24', 'mateo.vargas@gmail.com', 'Mateo Vargas', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:30:40'),
(78, '@alp_2025_25', 'valeria.ortega@cbtis258.edu.mx', 'Valeria Ortega', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:30:40'),
(79, '@alp_2025_26', 'sebastian.castro@gmail.com', 'Sebastián Castro', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:30:40'),
(80, '@alp_2025_27', 'camila.fernandez@cbtis258.edu.mx', 'Camila Fernández', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:30:40'),
(81, '@alp_2025_28', 'andres.lopez@gmail.com', 'Andrés López', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:30:40'),
(82, '@alp_2025_29', 'isabella.morales@cbtis258.edu.mx', 'Isabella Morales', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:30:40'),
(83, '@alp_2025_30', 'julian.ramirez@gmail.com', 'Julián Ramírez', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'student', '2025-10-18 19:30:40'),
(84, '@tea_2025_21', 'ana.gonzalez@cbtis258.edu.mx', 'Maestra Ana González', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:30:40'),
(85, '@tea_2025_22', 'luis.herrera@gmail.com', 'Prof. Luis Herrera', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:30:40'),
(86, '@tea_2025_23', 'carla.martinez@cbtis258.edu.mx', 'Maestra Carla Martínez', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:30:40'),
(87, '@tea_2025_24', 'jorge.navarro@gmail.com', 'Prof. Jorge Navarro', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:30:40'),
(88, '@tea_2025_25', 'monica.salazar@cbtis258.edu.mx', 'Maestra Mónica Salazar', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:30:40'),
(89, '@tea_2025_26', 'eduardo.torres@gmail.com', 'Prof. Eduardo Torres', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:30:40'),
(90, '@tea_2025_27', 'patricia.vazquez@cbtis258.edu.mx', 'Maestra Patricia Vázquez', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:30:40'),
(91, '@tea_2025_28', 'roberto.molina@gmail.com', 'Prof. Roberto Molina', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:30:40'),
(92, '@tea_2025_29', 'laura.cervantes@cbtis258.edu.mx', 'Maestra Laura Cervantes', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:30:40'),
(93, '@tea_2025_30', 'fernando.rios@gmail.com', 'Prof. Fernando Ríos', '$2y$10$Yz1xv9ZQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzUe9zQeJzK1xG8x7YzU', 'teacher', '2025-10-18 19:30:40'),
(94, '@alp_2025_021', 'ajarettovar@gmail.com', 'jaret', '$2y$10$w4LXiJ4HK/lOWKeeDTqACuAxyQkgO/nns0cbMKXnC7Enqv8sVX5dy', 'student', '2025-10-18 20:05:56'),
(95, '@alp_2025_022', 'alberto.alp@cbtis258.edu.mx', 'jaretvzz_1', '$2y$10$vuJK62K9abfL7Q9pml0PGu0u9A6rsQEsHWmnv.vz6jdQaYaHz0nAu', 'student', '2025-10-19 04:39:40'),
(96, '@tea_2025_0111', 'asd@gmail.com', 'asd', '$2y$10$groMeM9yM69tKJ5vRLw6H.qEOlZJ1i./es5ysqpOk7m4JgVnY6wt6', 'teacher', '2025-10-19 06:36:15'),
(97, '@tea_2025_01111', '123@gmail.com', 'asd', '$2y$10$H2K5ICXbISCjbEW0QBAk..ik/HWiPzr2bm6zLeCh/xT50hJJoWtwS', 'teacher', '2025-10-19 06:36:50'),
(98, '@alp_2025_777', 'vegetta777@gmail.com', 'vegetta777', '$2y$10$ZCmPDjhQ7PivyiXdIEzq8.PsaL6HE38UWC69ALaUMWtsH2hL00hdG', 'student', '2025-10-19 16:38:22'),
(99, '@tea_2025_willy', 'willy@gmail.com', 'willyrex', '$2y$10$F1.nd3H1I4YiwK36FXWVGet2pv/FXLLrte9L8RI4ZRi.5c8hrmwh6', 'teacher', '2025-10-19 16:53:30');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tutoring_registrations`
--
ALTER TABLE `tutoring_registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
