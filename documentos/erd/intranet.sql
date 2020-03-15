-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         10.3.16-MariaDB - mariadb.org binary distribution
-- SO del servidor:              Win64
-- HeidiSQL Versión:             10.3.0.5771
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Volcando estructura de base de datos para intranet
CREATE DATABASE IF NOT EXISTS `intranet` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `intranet`;

-- Volcando estructura para tabla intranet.archivos_pi_referencias
CREATE TABLE IF NOT EXISTS `archivos_pi_referencias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` text NOT NULL DEFAULT '',
  `ruta` text NOT NULL DEFAULT '',
  `observaciones` text DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL,
  `activo` int(11) NOT NULL,
  `fk_pi` int(11) NOT NULL,
  `fk_categoria` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla intranet.categorias
CREATE TABLE IF NOT EXISTS `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1,
  `fk_categoria` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla intranet.marcas
CREATE TABLE IF NOT EXISTS `marcas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla intranet.pi
CREATE TABLE IF NOT EXISTS `pi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pi` text NOT NULL,
  `unidades` varchar(50) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1,
  `fk_referencia` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla intranet.referencias
CREATE TABLE IF NOT EXISTS `referencias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `referencia` varchar(100) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `fk_marca` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla intranet.referencias_tecnologias
CREATE TABLE IF NOT EXISTS `referencias_tecnologias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_referencia` int(11) NOT NULL,
  `fk_tipo` int(11) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla intranet.tecnologias
CREATE TABLE IF NOT EXISTS `tecnologias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1,
  `fk_categoria` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
