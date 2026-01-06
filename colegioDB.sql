-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 10-12-2025 a las 23:37:33
-- Versión del servidor: 8.4.7
-- Versión de PHP: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bdcolegio2`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumno`
--

DROP TABLE IF EXISTS `alumno`;
CREATE TABLE IF NOT EXISTS `alumno` (
  `id_alumno` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido_paterno` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido_materno` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `estado` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_seccion` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_apoderado` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_alumno`),
  KEY `Fk_Seccion` (`id_seccion`),
  KEY `Fk_Apoderado` (`id_apoderado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `alumno`
--

INSERT INTO `alumno` (`id_alumno`, `nombre`, `apellido_paterno`, `apellido_materno`, `fecha_nacimiento`, `estado`, `id_seccion`, `id_apoderado`) VALUES
('12345678', 'werwerw', 'werwerw', 'werwer', '2000-12-12', 'Postulante', '2A', '98765432'),
('19276401', 'Juan', 'Aguilar', 'Gutierrez', '2025-12-25', 'Postulante', '4B', '39572859'),
('22222222', 'abed', 'b', 'v', '2015-12-11', 'Postulante', '2A', '33333333'),
('27364918', 'Walter ', 'Arhuis', 'Chigne', '2009-02-12', 'Postulante', '5B', '19472698'),
('72389110', 'Julio', 'Vasquez', 'Aliaga', '2013-07-25', 'Postulante', '1A', '00198212'),
('74963678', 'Andre', 'Castañeda', 'Astudillo', '2000-03-12', 'Postulante', '6B', '19276390'),
('76487564', 'Pedro', 'Caballero', 'Caceres', '2025-12-31', 'Postulante', NULL, '75912576'),
('78462734', 'Leonel Andres', 'Messi', 'Cuccittini', '2025-12-20', 'Postulante', NULL, '74840398'),
('93847583', 'Luis', 'Sanchez', 'Alayo', '2025-12-30', 'Postulante', NULL, '58746302');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `apoderado`
--

DROP TABLE IF EXISTS `apoderado`;
CREATE TABLE IF NOT EXISTS `apoderado` (
  `id_apoderado` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido_paterno` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido_materno` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_apoderado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `apoderado`
--

INSERT INTO `apoderado` (`id_apoderado`, `nombre`, `apellido_paterno`, `apellido_materno`, `telefono`, `email`) VALUES
('00198212', 'Julio', 'Vasquez', 'Guitierrez', '923223332', 'vazgut@gmail.com'),
('19276390', 'Walter', 'Arhuis', 'Chigne', '980743968', 'walterasd@gmail.com'),
('19472698', 'Diego', 'Gomez', 'Rivera', '987351837', 'peter@gmail.com'),
('33333333', 'abc', 'w', 'v', '999999999', 'ab@gmail.com'),
('39572859', 'Pedro', 'Caceres', 'Gutierrez', '968758475', 'lkasdjlk@asdasd'),
('58746302', 'Fernando', 'Snahcez', 'Perez', '984756398', 'reww@gmalop'),
('74840398', 'Jose', 'Abelardo', 'Quiñones', '983647128', 'jose@gmail.com'),
('75912576', 'Cristiano Ronaldo', 'Dos Santos', 'Aveiro', '965726354', 'cristiano@gmail.com'),
('98765432', 'ewrwerwer', 'werwerwer', 'werwerwe', '987654321', 'qwerwqer@gmail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `concepto_pago`
--

DROP TABLE IF EXISTS `concepto_pago`;
CREATE TABLE IF NOT EXISTS `concepto_pago` (
  `id_concepto` int NOT NULL AUTO_INCREMENT,
  `id_solicitud` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_limite` date NOT NULL,
  `periodo_academico` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `medio_pago` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `codigo_pago` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_generacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_concepto`),
  UNIQUE KEY `codigo_pago` (`codigo_pago`),
  KEY `id_solicitud` (`id_solicitud`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `concepto_pago`
--

INSERT INTO `concepto_pago` (`id_concepto`, `id_solicitud`, `fecha_limite`, `periodo_academico`, `medio_pago`, `monto`, `descripcion`, `codigo_pago`, `fecha_generacion`) VALUES
(1, '6938f9050c', '3333-03-23', '2025-2', 'Depósito', 320.00, 'a', 'CP-CEC8EF94', '2025-12-10 14:43:05'),
(2, '6938f8575a', '1242-03-12', '2025-1', 'Transferencia', 123.00, 'a', 'CP-92B02F38', '2025-12-10 15:09:47'),
(3, '693994350d', '2025-12-27', '2025-2', 'Efectivo', 320.00, '.', 'CP-37169297', '2025-12-10 15:40:35'),
(4, '6939a2a09e', '2025-12-24', '2025-I', 'Banco', 350.00, 'Matrícula y Primera Mensualidad 2025', 'CP-K5FQOVI7', '2025-12-10 05:00:00'),
(5, '6939ae9cb8', '2025-12-12', '2025-II', 'Banco', 350.00, 'Matrícula', 'CP-EJYMKT60', '2025-12-10 05:00:00'),
(6, '6939bc987c', '2026-01-09', '2025-II', 'Efectivo', 350.00, 'Matrícula 2025', 'CP-N91SDLXE', '2025-12-10 05:00:00'),
(7, '6939d6e27d', '2025-12-11', '2025-II', 'Banco', 350.00, 'Matrícula 2025', 'CP-5ICVKDME', '2025-12-10 05:00:00'),
(8, '6939ff31ab', '2025-12-19', '2025-II', 'Banco', 350.00, 'Matrícula 2025', 'CP-DLEV25AY', '2025-12-10 05:00:00'),
(9, '6939fe7c5b', '2025-12-06', '2025-II', 'Banco', 350.00, 'Matrícula 2025', 'CP-YKUMH5EW', '2025-12-10 05:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grado`
--

DROP TABLE IF EXISTS `grado`;
CREATE TABLE IF NOT EXISTS `grado` (
  `id_grado` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_grado` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vacantes_disponibles` int NOT NULL,
  PRIMARY KEY (`id_grado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `grado`
--

INSERT INTO `grado` (`id_grado`, `nombre_grado`, `vacantes_disponibles`) VALUES
('1', '1° Grado', 59),
('2', '2° Grado', 58),
('3', '3° Grado', 60),
('4', '4° Grado', 59),
('5', '5° Grado', 59),
('6', '6° Grado', 59);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `matricula`
--

DROP TABLE IF EXISTS `matricula`;
CREATE TABLE IF NOT EXISTS `matricula` (
  `id_matricula` int NOT NULL AUTO_INCREMENT,
  `nro_matricula` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_matricula` date NOT NULL,
  `id_solicitud` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_alumno` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_matricula`),
  KEY `Fk_Solicitud` (`id_solicitud`),
  KEY `Fk_Alumno` (`id_alumno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seccion`
--

DROP TABLE IF EXISTS `seccion`;
CREATE TABLE IF NOT EXISTS `seccion` (
  `id_seccion` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_seccion` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `capacidad` int NOT NULL,
  `vacantes` int DEFAULT '0',
  `id_grado` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `5toA` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `6toA` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `4toA` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `3toA` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `2toA` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_seccion`),
  KEY `Fk_Grado` (`id_grado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `seccion`
--

INSERT INTO `seccion` (`id_seccion`, `nombre_seccion`, `capacidad`, `vacantes`, `id_grado`, `5toA`, `6toA`, `4toA`, `3toA`, `2toA`) VALUES
('1A', 'Sección A', 30, 29, '1', '', '', '', '', ''),
('1B', 'Sección B', 30, 30, '1', '', '', '', '', ''),
('2A', 'Sección A', 30, 28, '2', '', '', '', '', ''),
('2B', 'Sección B', 30, 30, '2', '', '', '', '', ''),
('3A', 'Sección A', 30, 30, '3', '', '', '', '', ''),
('3B', 'Sección B', 30, 30, '3', '', '', '', '', ''),
('4A', 'Sección A', 30, 30, '4', '', '', '', '', ''),
('4B', 'Sección B', 30, 29, '4', '', '', '', '', ''),
('5A', 'Sección A', 30, 30, '5', '', '', '', '', ''),
('5B', 'Sección B', 30, 29, '5', '', '', '', '', ''),
('6A', 'Sección A', 30, 30, '6', '', '', '', '', ''),
('6B', 'Sección B', 30, 29, '6', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitud`
--

DROP TABLE IF EXISTS `solicitud`;
CREATE TABLE IF NOT EXISTS `solicitud` (
  `id_solicitud` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_solicitud` date NOT NULL,
  `tipo_solicitud` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_apoderado` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_alumno` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `grado` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_solicitud`),
  KEY `Fk_Apoderado` (`id_apoderado`),
  KEY `Fk_Alumno` (`id_alumno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `solicitud`
--

INSERT INTO `solicitud` (`id_solicitud`, `fecha_solicitud`, `tipo_solicitud`, `estado`, `id_apoderado`, `id_alumno`, `grado`) VALUES
('6938f8575a', '2025-12-10', 'Matricula', 'Pendiente', '98765432', '12345678', '2'),
('6938f9050c', '2025-12-10', 'Matricula', 'Pendiente', '19276390', '74963678', '6'),
('693994350d', '2025-12-10', 'Matricula', 'Pendiente', '00198212', '72389110', '1'),
('6939a2a09e', '2025-12-10', 'Matricula', 'Pendiente', '33333333', '22222222', '2'),
('6939ae9cb8', '2025-12-10', 'Matricula', 'Pendiente', '19472698', '27364918', '5'),
('6939bc987c', '2025-12-10', 'Matricula', 'Pendiente', '39572859', '19276401', '4'),
('6939d6e27d', '2025-12-10', 'Matricula', 'Pendiente', '58746302', '93847583', '4'),
('6939fe7c5b', '2025-12-10', 'Matricula', 'Pendiente', '75912576', '76487564', '6'),
('6939ff31ab', '2025-12-10', 'Matricula', 'Pendiente', '74840398', '78462734', '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE IF NOT EXISTS `usuario` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `usuario` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contrasena` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_usuario`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `usuario`, `contrasena`, `rol`) VALUES
(6, 'renzoprincipe', '1234567', 'director'),
(7, 'walter', '1234', 'secretario');

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alumno`
--
ALTER TABLE `alumno`
  ADD CONSTRAINT `alumno_ibfk_1` FOREIGN KEY (`id_apoderado`) REFERENCES `apoderado` (`id_apoderado`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `alumno_ibfk_2` FOREIGN KEY (`id_seccion`) REFERENCES `seccion` (`id_seccion`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `concepto_pago`
--
ALTER TABLE `concepto_pago`
  ADD CONSTRAINT `concepto_pago_ibfk_1` FOREIGN KEY (`id_solicitud`) REFERENCES `solicitud` (`id_solicitud`) ON DELETE CASCADE;

--
-- Filtros para la tabla `matricula`
--
ALTER TABLE `matricula`
  ADD CONSTRAINT `matricula_ibfk_1` FOREIGN KEY (`id_alumno`) REFERENCES `alumno` (`id_alumno`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `matricula_ibfk_2` FOREIGN KEY (`id_solicitud`) REFERENCES `solicitud` (`id_solicitud`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `seccion`
--
ALTER TABLE `seccion`
  ADD CONSTRAINT `seccion_ibfk_1` FOREIGN KEY (`id_grado`) REFERENCES `grado` (`id_grado`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `solicitud`
--
ALTER TABLE `solicitud`
  ADD CONSTRAINT `solicitud_ibfk_1` FOREIGN KEY (`id_apoderado`) REFERENCES `apoderado` (`id_apoderado`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `solicitud_ibfk_2` FOREIGN KEY (`id_alumno`) REFERENCES `alumno` (`id_alumno`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
