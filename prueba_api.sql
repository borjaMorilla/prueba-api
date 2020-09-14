-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 14-09-2020 a las 02:37:10
-- Versión del servidor: 10.4.13-MariaDB
-- Versión de PHP: 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `prueba_api`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `player`
--

DROP TABLE IF EXISTS `player`;
CREATE TABLE IF NOT EXISTS `player` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `team_id` int(11) DEFAULT NULL,
  `price` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_98197A65296CD8AE` (`team_id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `player`
--

INSERT INTO `player` (`id`, `name`, `last_name`, `team_id`, `price`) VALUES
(1, 'SERGIO', 'Ramos', 12, NULL),
(28, 'Toni', 'Kroos', 12, NULL),
(29, 'Luca', 'Modric', 12, NULL),
(30, 'Darwin', 'Machis', 13, 3000000),
(34, 'Antonio', 'Puertas', 13, 1000000);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `player_position`
--

DROP TABLE IF EXISTS `player_position`;
CREATE TABLE IF NOT EXISTS `player_position` (
  `player_id` int(11) NOT NULL,
  `position_id` int(11) NOT NULL,
  PRIMARY KEY (`player_id`,`position_id`),
  KEY `IDX_40FBA51599E6F5DF` (`player_id`),
  KEY `IDX_40FBA515DD842E46` (`position_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `player_position`
--

INSERT INTO `player_position` (`player_id`, `position_id`) VALUES
(1, 2),
(28, 3),
(28, 4),
(29, 3),
(29, 4),
(30, 3),
(30, 4),
(34, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `position`
--

DROP TABLE IF EXISTS `position`;
CREATE TABLE IF NOT EXISTS `position` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_name` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `position`
--

INSERT INTO `position` (`id`, `name`, `short_name`) VALUES
(1, 'goalkeeper', 'GK'),
(2, 'defender', 'DF'),
(3, 'midfielder', 'MD'),
(4, 'forward', 'FW');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `team`
--

DROP TABLE IF EXISTS `team`;
CREATE TABLE IF NOT EXISTS `team` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_name` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `team`
--

INSERT INTO `team` (`id`, `name`, `short_name`) VALUES
(12, 'Real Madrid', 'RM'),
(13, 'Granada CF', 'GR');

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `player`
--
ALTER TABLE `player`
  ADD CONSTRAINT `FK_98197A65296CD8AE` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`);

--
-- Filtros para la tabla `player_position`
--
ALTER TABLE `player_position`
  ADD CONSTRAINT `FK_40FBA51599E6F5DF` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_40FBA515DD842E46` FOREIGN KEY (`position_id`) REFERENCES `position` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
