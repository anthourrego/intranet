-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-04-2020 a las 19:06:48
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
-- Base de datos: `intranet`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `archivos_pi_referencias`
--

CREATE TABLE `archivos_pi_referencias` (
  `id` int(11) NOT NULL,
  `tipo` mediumtext NOT NULL DEFAULT '',
  `tipo2` mediumtext NOT NULL DEFAULT '',
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
  `fk_creador` int(11) NOT NULL DEFAULT 0,
  `fk_permiso` varchar(100) NOT NULL
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
  `fk_creador` int(11) NOT NULL DEFAULT 0,
  `estado` int(11) NOT NULL DEFAULT 1
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
  `nivel` int(11) NOT NULL DEFAULT 1,
  `fk_creador` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tecnologias`
--

INSERT INTO `tecnologias` (`id`, `nombre`, `fecha_creacion`, `activo`, `fk_tecnologia`, `nivel`, `fk_creador`) VALUES
(1, 'Línea Marrón', '2020-03-15 00:32:27', 1, 0, 1, 98),
(2, 'Línea Blanca', '2020-03-15 00:32:49', 1, 0, 1, 98),
(3, 'Televisores', '2020-03-15 00:32:56', 1, 1, 2, 98),
(4, 'Accesorios', '2020-03-15 00:33:04', 1, 1, 2, 98),
(5, 'Audio y Video', '2020-03-15 00:33:10', 1, 1, 2, 98),
(6, 'Alta potencia', '2020-03-15 00:33:21', 1, 3, 3, 98),
(7, 'Básicos', '2020-03-15 00:33:31', 1, 3, 3, 98),
(8, 'Curvo', '2020-03-15 00:33:39', 1, 3, 3, 98),
(9, 'Smart', '2020-03-15 00:33:39', 1, 3, 3, 98),
(10, 'UHD (4K)', '2020-03-15 00:33:57', 1, 3, 3, 98),
(11, 'Voice Assistant', '2020-03-15 00:34:05', 1, 4, 3, 98),
(12, 'Smart', '2020-03-15 00:34:16', 1, 4, 3, 98),
(13, 'MultiStream', '2020-03-15 00:34:23', 1, 4, 3, 98),
(14, 'Barras de sonido', '2020-03-15 00:34:37', 1, 5, 3, 98),
(15, 'Mini componentes', '2020-03-15 00:34:45', 1, 5, 3, 98),
(16, 'Parlantes Multimedia', '2020-03-15 00:34:55', 1, 5, 3, 98),
(17, 'Decodificadores', '2020-03-15 00:35:05', 1, 5, 3, 98),
(18, 'DVD', '2020-03-15 00:35:10', 1, 5, 3, 98),
(19, 'Aires Acondicionados', '2020-03-15 00:35:23', 1, 2, 2, 98),
(20, 'Refrigeración', '2020-03-15 00:35:32', 1, 2, 2, 98),
(21, 'Pequeños Electrodomésticos', '2020-03-15 00:35:38', 1, 2, 2, 98),
(22, 'Lavadoras', '2020-03-15 00:35:43', 1, 2, 2, 98),
(23, 'Split', '2020-03-15 00:35:51', 1, 19, 3, 98),
(24, 'Inverter', '2020-03-15 00:36:10', 1, 19, 3, 98),
(25, 'Portatil', '2020-03-15 00:36:17', 1, 19, 3, 98),
(26, 'Wifi', '2020-03-15 00:36:24', 1, 19, 3, 98),
(27, 'Congeladores Horizontales', '2020-03-15 00:36:35', 1, 20, 3, 98),
(28, 'Vitrinas Verticales', '2020-03-15 00:36:45', 1, 20, 3, 98),
(29, 'Neveras Minibar', '2020-03-15 00:36:52', 1, 20, 3, 98),
(30, 'Freidora de Aire', '2020-03-15 00:37:04', 1, 21, 3, 98),
(31, 'Microondas', '2020-03-15 00:37:14', 1, 21, 3, 98),
(32, 'Lavadoras', '2020-03-15 00:37:31', 1, 22, 3, 98);

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

--
-- Volcado de datos para la tabla `tecnologia_no_compatible`
--

INSERT INTO `tecnologia_no_compatible` (`id`, `fk_tecnologia`, `fk_tecnologia_compatible`, `fecha_creacion`, `estado`, `fk_creador`) VALUES
(5, 6, 7, '2020-04-09 04:11:11', 1, 98),
(6, 6, 9, '2020-04-09 04:11:37', 0, 98),
(7, 6, 8, '2020-04-09 04:12:49', 1, 98),
(8, 6, 10, '2020-04-09 04:13:02', 0, 98),
(9, 7, 8, '2020-04-09 04:15:24', 1, 98),
(10, 7, 9, '2020-04-09 04:15:24', 1, 98),
(11, 7, 10, '2020-04-09 04:15:24', 1, 98),
(12, 1, 2, '2020-04-09 10:13:20', 1, 98),
(13, 3, 4, '2020-04-09 10:14:55', 1, 98),
(14, 3, 5, '2020-04-09 10:14:55', 1, 98),
(15, 7, 36, '2020-04-09 13:29:24', 1, 98),
(16, 10, 36, '2020-04-09 13:29:50', 1, 98),
(17, 6, 36, '2020-04-09 13:35:50', 0, 98),
(18, 37, 38, '2020-04-09 14:15:29', 1, 98),
(19, 42, 37, '2020-04-09 14:15:34', 1, 98),
(20, 42, 38, '2020-04-09 14:15:34', 1, 98),
(21, 45, 8, '2020-04-09 14:57:52', 1, 98),
(22, 46, 45, '2020-04-09 14:58:08', 1, 98),
(23, 47, 8, '2020-04-09 14:58:46', 1, 98),
(24, 47, 10, '2020-04-09 14:58:46', 1, 98),
(25, 47, 45, '2020-04-09 14:58:46', 1, 98),
(26, 47, 46, '2020-04-09 14:58:46', 1, 98),
(27, 48, 47, '2020-04-09 14:59:02', 1, 98),
(28, 49, 1, '2020-04-09 17:04:07', 1, 98),
(29, 49, 2, '2020-04-09 17:04:07', 1, 98),
(30, 49, 49, '2020-04-09 17:04:07', 1, 98),
(31, 49, 19, '2020-04-09 17:04:48', 1, 98),
(32, 49, 20, '2020-04-09 17:04:48', 1, 98),
(33, 49, 21, '2020-04-09 17:04:48', 1, 98),
(34, 49, 22, '2020-04-09 17:04:48', 1, 98),
(35, 6, 1, '2020-04-09 17:24:58', 1, 98),
(36, 6, 2, '2020-04-09 17:24:58', 1, 98),
(37, 7, 1, '2020-04-09 17:25:52', 1, 98),
(38, 7, 2, '2020-04-09 17:25:52', 1, 98),
(39, 50, 6, '2020-04-12 19:00:39', 1, 98),
(40, 50, 7, '2020-04-12 19:00:39', 1, 98),
(41, 50, 8, '2020-04-12 19:00:39', 1, 98),
(42, 50, 10, '2020-04-12 19:00:39', 1, 98),
(43, 33, 1, '2020-04-13 12:27:41', 1, 98),
(44, 33, 2, '2020-04-13 12:27:41', 1, 98),
(45, 33, 33, '2020-04-13 12:27:41', 1, 98);

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
-- Volcado de datos para la tabla `tipo_archivo`
--

INSERT INTO `tipo_archivo` (`id`, `nombre`, `extensiones`, `estado`, `fecha_creacion`, `fk_creador`) VALUES
(1, 'imagen', 'jpg', 1, '2020-04-07 16:29:00', 98),
(2, 'imagen', 'jpeg', 1, '2020-04-07 16:29:00', 98),
(3, 'imagen', 'png', 1, '2020-04-07 16:29:00', 98),
(4, 'documento', 'docx', 1, '2020-04-07 16:29:00', 98),
(5, 'documento', 'xlsx', 1, '2020-04-07 16:29:00', 98),
(6, 'documento', 'pptx', 1, '2020-04-07 16:29:00', 98),
(8, 'documento', 'xls', 1, '2020-04-07 16:29:00', 98),
(9, 'documento', 'ppt', 1, '2020-04-07 16:29:00', 98),
(10, 'documento', 'doc', 1, '2020-04-07 16:29:00', 98),
(11, 'documento', 'pdf', 1, '2020-04-07 16:29:00', 98),
(12, 'archivo', 'rar', 1, '2020-04-07 16:29:00', 98),
(13, 'archivo', 'zip', 1, '2020-04-07 16:29:00', 98);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_archivo_categoria`
--

CREATE TABLE `tipo_archivo_categoria` (
  `id` int(11) NOT NULL,
  `fk_categoria` int(11) NOT NULL DEFAULT 0,
  `fk_tarchivo` int(11) NOT NULL DEFAULT 0,
  `estado` int(11) NOT NULL DEFAULT 0,
  `fecha_creacion` datetime NOT NULL,
  `fk_creador` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `tipo_archivo_categoria`
--

INSERT INTO `tipo_archivo_categoria` (`id`, `fk_categoria`, `fk_tarchivo`, `estado`, `fecha_creacion`, `fk_creador`) VALUES
(20, 1, 11, 1, '2020-04-12 18:34:16', 98),
(21, 2, 1, 1, '2020-04-12 18:36:02', 98),
(22, 2, 2, 1, '2020-04-12 18:36:06', 98),
(23, 2, 3, 1, '2020-04-12 18:36:11', 98),
(24, 3, 1, 1, '2020-04-12 18:36:30', 98),
(25, 3, 2, 1, '2020-04-12 18:36:36', 98),
(26, 3, 3, 1, '2020-04-12 18:36:42', 98),
(27, 4, 5, 1, '2020-04-12 18:37:28', 98),
(28, 4, 8, 1, '2020-04-12 18:37:55', 98),
(29, 1, 1, 1, '0000-00-00 00:00:00', 98),
(30, 4, 4, 1, '2020-04-13 12:14:21', 98),
(31, 4, 10, 1, '2020-04-13 12:14:26', 98);

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
-- Indices de la tabla `tipo_archivo_categoria`
--
ALTER TABLE `tipo_archivo_categoria`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de la tabla `tipo_archivo`
--
ALTER TABLE `tipo_archivo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `tipo_archivo_categoria`
--
ALTER TABLE `tipo_archivo_categoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
