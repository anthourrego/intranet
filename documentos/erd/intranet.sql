-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 03-04-2020 a las 20:28:40
-- Versión del servidor: 10.4.11-MariaDB
-- Versión de PHP: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `intranet1`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `archivos_pi_referencias`
--

CREATE TABLE `archivos_pi_referencias` (
  `id` int(11) NOT NULL,
  `tipo` mediumtext NOT NULL DEFAULT '',
  `ruta` mediumtext NOT NULL DEFAULT '',
  `observaciones` mediumtext DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL,
  `activo` int(11) NOT NULL,
  `fk_referencia` int(11) NOT NULL,
  `fk_pi` int(11) DEFAULT NULL,
  `fk_categoria` int(11) NOT NULL,
  `fk_creador` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `aplica_pi` int(11) NOT NULL DEFAULT 0,
  `activo` int(11) NOT NULL DEFAULT 1,
  `publico` int(11) NOT NULL DEFAULT 1,
  `fk_categoria` int(11) NOT NULL DEFAULT 0,
  `fk_tipo_archivo` int(11) NOT NULL DEFAULT 0,
  `fk_creador` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logs_jobs`
--

CREATE TABLE `logs_jobs` (
  `id` int(11) NOT NULL,
  `nombre_tabla` varchar(100) NOT NULL,
  `id_registro` int(11) NOT NULL DEFAULT 0,
  `accion` varchar(100) NOT NULL,
  `fk_usuario` int(11) NOT NULL DEFAULT 0,
  `fecha_creacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marcas`
--

CREATE TABLE `marcas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1,
  `fk_creador` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pi`
--

CREATE TABLE `pi` (
  `id` int(11) NOT NULL,
  `pi` mediumtext NOT NULL,
  `unidades` varchar(50) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1,
  `fk_referencia` int(11) NOT NULL DEFAULT 0,
  `fk_creador` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `referencias`
--

CREATE TABLE `referencias` (
  `id` int(11) NOT NULL,
  `referencia` varchar(100) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `fk_marca` int(11) NOT NULL DEFAULT 0,
  `fk_creador` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `referencias_tecnologias`
--

CREATE TABLE `referencias_tecnologias` (
  `id` int(11) NOT NULL,
  `fk_referencia` int(11) NOT NULL,
  `fk_tecnologia` int(11) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1,
  `fk_creador` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tecnologias`
--

CREATE TABLE `tecnologias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1,
  `fk_tecnologia` int(11) NOT NULL DEFAULT 0,
  `fk_creador` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tecnologias`
--

INSERT INTO `tecnologias` (`id`, `nombre`, `fecha_creacion`, `activo`, `fk_tecnologia`, `fk_creador`) VALUES
(1, 'Línea Marrón', '2020-03-15 00:32:27', 1, 0, 0),
(2, 'Línea Blanca', '2020-03-15 00:32:49', 1, 0, 0),
(3, 'Televisores', '2020-03-15 00:32:56', 1, 1, 0),
(4, 'Accesorios', '2020-03-15 00:33:04', 1, 1, 0),
(5, 'Audio y Video', '2020-03-15 00:33:10', 1, 1, 0),
(6, 'Alta potencia', '2020-03-15 00:33:21', 1, 3, 0),
(7, 'Básicos', '2020-03-15 00:33:31', 1, 3, 0),
(8, 'Curvo', '2020-03-15 00:33:39', 1, 3, 0),
(9, 'Smart', '2020-03-15 00:33:39', 1, 3, 0),
(10, 'UHD (4K)', '2020-03-15 00:33:57', 1, 3, 0),
(11, 'Voice Assistant', '2020-03-15 00:34:05', 1, 4, 0),
(12, 'Smart', '2020-03-15 00:34:16', 1, 4, 0),
(13, 'MultiStream', '2020-03-15 00:34:23', 1, 4, 0),
(14, 'Barras de sonido', '2020-03-15 00:34:37', 1, 5, 0),
(15, 'Mini componentes', '2020-03-15 00:34:45', 1, 5, 0),
(16, 'Parlantes Multimedia', '2020-03-15 00:34:55', 1, 5, 0),
(17, 'Decodificadores', '2020-03-15 00:35:05', 1, 5, 0),
(18, 'DVD', '2020-03-15 00:35:10', 1, 5, 0),
(19, 'Aires Acondicionados', '2020-03-15 00:35:23', 1, 2, 0),
(20, 'Refrigeración', '2020-03-15 00:35:32', 1, 2, 0),
(21, 'Pequeños Electrodomésticos', '2020-03-15 00:35:38', 1, 2, 0),
(22, 'Lavadoras', '2020-03-15 00:35:43', 1, 2, 0),
(23, 'Split', '2020-03-15 00:35:51', 1, 19, 0),
(24, 'Inverter', '2020-03-15 00:36:10', 1, 19, 0),
(25, 'Portati', '2020-03-15 00:36:17', 1, 19, 0),
(26, 'Wifi', '2020-03-15 00:36:24', 1, 19, 0),
(27, 'Congeladores Horizontales', '2020-03-15 00:36:35', 1, 20, 0),
(28, 'Vitrinas Verticales', '2020-03-15 00:36:45', 1, 20, 0),
(29, 'Neveras Minibar', '2020-03-15 00:36:52', 1, 20, 0),
(30, 'Freidora de Aire', '2020-03-15 00:37:04', 1, 21, 0),
(31, 'Microondas', '2020-03-15 00:37:14', 1, 21, 0),
(32, 'Lavadoras', '2020-03-15 00:37:31', 1, 22, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tecnologia_no_compatible`
--

CREATE TABLE `tecnologia_no_compatible` (
  `id` int(11) NOT NULL,
  `fk_tecnologia` int(11) NOT NULL DEFAULT 0,
  `fk_tecnologia_compatible` int(11) NOT NULL DEFAULT 0,
  `fecha_creacion` datetime NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 1,
  `fk_creador` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_archivo`
--

CREATE TABLE `tipo_archivo` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `extensiones` varchar(100) NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 0,
  `fecha_creacion` datetime NOT NULL,
  `fk_creador` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `archivos_pi_referencias`
--
ALTER TABLE `archivos_pi_referencias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `logs_jobs`
--
ALTER TABLE `logs_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `marcas`
--
ALTER TABLE `marcas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pi`
--
ALTER TABLE `pi`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `referencias`
--
ALTER TABLE `referencias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `referencias_tecnologias`
--
ALTER TABLE `referencias_tecnologias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tecnologias`
--
ALTER TABLE `tecnologias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tecnologia_no_compatible`
--
ALTER TABLE `tecnologia_no_compatible`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tipo_archivo`
--
ALTER TABLE `tipo_archivo`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `archivos_pi_referencias`
--
ALTER TABLE `archivos_pi_referencias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `logs_jobs`
--
ALTER TABLE `logs_jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `marcas`
--
ALTER TABLE `marcas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `pi`
--
ALTER TABLE `pi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `referencias`
--
ALTER TABLE `referencias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `referencias_tecnologias`
--
ALTER TABLE `referencias_tecnologias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tecnologias`
--
ALTER TABLE `tecnologias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `tecnologia_no_compatible`
--
ALTER TABLE `tecnologia_no_compatible`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tipo_archivo`
--
ALTER TABLE `tipo_archivo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
