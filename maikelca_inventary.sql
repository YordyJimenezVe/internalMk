-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-05-2026 a las 15:17:24
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
-- Base de datos: `maikelca_inventary`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `accesorio_engines`
--

CREATE TABLE `accesorio_engines` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `maintenances_id` bigint(20) UNSIGNED NOT NULL,
  `valve_cover` varchar(255) DEFAULT NULL,
  `chain_cover` varchar(255) DEFAULT NULL,
  `carter` varchar(255) DEFAULT NULL,
  `pescador` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `billings`
--

CREATE TABLE `billings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `partida_id` bigint(20) UNSIGNED DEFAULT NULL,
  `big` varchar(255) DEFAULT NULL,
  `iva` varchar(255) DEFAULT NULL,
  `bs` varchar(255) DEFAULT NULL,
  `value_divisa` varchar(255) DEFAULT NULL,
  `divisa` varchar(255) DEFAULT NULL,
  `precio_total` varchar(255) DEFAULT NULL,
  `total` decimal(12,2) DEFAULT NULL,
  `igtf` varchar(255) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `numero_factura` varchar(255) DEFAULT NULL,
  `numero_control` varchar(255) DEFAULT NULL,
  `numero_nota_credito` varchar(255) DEFAULT NULL,
  `numero_factura_afect` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_name` varchar(255) DEFAULT NULL,
  `client_cedula` varchar(255) DEFAULT NULL,
  `client_phone` varchar(255) DEFAULT NULL,
  `client_address` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `billing_requests`
--

CREATE TABLE `billing_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `partida_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `client_name` varchar(255) DEFAULT NULL,
  `client_cedula` varchar(255) DEFAULT NULL,
  `client_phone` varchar(255) DEFAULT NULL,
  `client_address` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `billing_requests`
--

INSERT INTO `billing_requests` (`id`, `partida_id`, `user_id`, `quantity`, `price`, `client_name`, `client_cedula`, `client_phone`, `client_address`, `status`, `created_at`, `updated_at`) VALUES
(1, 660, 1, 1, 800.00, 'YORDY', '26136890', '04126776250', 'calle 11', 'processed', '2026-02-17 16:54:52', '2026-02-17 16:56:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bitacoras`
--

CREATE TABLE `bitacoras` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `users_id` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `bitacoras`
--

INSERT INTO `bitacoras` (`id`, `users_id`, `action`, `description`, `created_at`, `updated_at`) VALUES
(49, 1, 'ELIMINACIÓN DE INVENTARIO: ARRANQUE', 'EL USUARIO YORDY JIMENEZ ELIMINÓ EL REGISTRO DE INVENTARIO #662.', '2026-02-18 20:48:26', '2026-02-18 20:48:26'),
(50, 1, 'ACTUALIZACIÓN DE USUARIO: YORDY JIMENEZ', 'EL USUARIO YORDY JIMENEZ ACTUALIZÓ EL REGISTRO DE USUARIO #1. SE CAMBIÓ \'REMEMBER_TOKEN\' DE \'NGKKBVXIGARJDQIYKXTHPKICLEQDNM82E6KTTHIYD2WPP3QXJGOOFZ2QW6WV\' A \'ULLMRT9CMMUQUIYGD8CDVSRAELJJVKRYQAPSDOSYSFSQD9CX9IFN4IS10FBD\'', '2026-02-19 16:00:53', '2026-02-19 16:00:53'),
(51, 1, 'ACTUALIZACIÓN DE USUARIO: YORDY JIMENEZ', 'EL USUARIO YORDY JIMENEZ ACTUALIZÓ EL REGISTRO DE USUARIO #1. SE CAMBIÓ \'REMEMBER_TOKEN\' DE \'ULLMRT9CMMUQUIYGD8CDVSRAELJJVKRYQAPSDOSYSFSQD9CX9IFN4IS10FBD\' A \'MSKSGYTNV6H07VXJ5ZZ5UHZ1AGBG2BLMRH0CCQABXYWBE9IL6EFD4QAP5KLX\'', '2026-02-19 23:28:12', '2026-02-19 23:28:12'),
(52, 1, 'ACTUALIZACIÓN DE INVENTARIO: MOTOR 7/8 JEEP CHEROKEE  3.7L KJ', 'EL USUARIO YORDY JIMENEZ ACTUALIZÓ EL REGISTRO DE INVENTARIO #660. SE CAMBIÓ \'SERIAL\' DE \'\' A \'4CD-85659F42-124\'', '2026-03-14 15:32:05', '2026-03-14 15:32:05'),
(53, 1, 'ACTUALIZACIÓN DE INVENTARIO: MOTOR 7/8 JEEP CHEROKEE  3.7L KJ', 'EL USUARIO YORDY JIMENEZ ACTUALIZÓ EL REGISTRO DE INVENTARIO #660. SE CAMBIÓ \'SERIAL\' DE \'4CD-85659F42-124\' A \'\'', '2026-03-14 15:33:11', '2026-03-14 15:33:11'),
(54, 1, 'ACTUALIZACIÓN DE USUARIO: YORDY JIMENEZ', 'EL USUARIO YORDY JIMENEZ ACTUALIZÓ EL REGISTRO DE USUARIO #1. SE CAMBIÓ \'REMEMBER_TOKEN\' DE \'MSKSGYTNV6H07VXJ5ZZ5UHZ1AGBG2BLMRH0CCQABXYWBE9IL6EFD4QAP5KLX\' A \'JPHVHLBDWDVEWAXU7DKWOPL6WLJZSUDPZZOGBAU2QLNLUDOGVEO3FQDFMGCP\'', '2026-03-14 15:36:52', '2026-03-14 15:36:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `containers`
--

CREATE TABLE `containers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cod` varchar(255) DEFAULT NULL,
  `expediente` varchar(255) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `motores` int(11) DEFAULT NULL,
  `cajas` int(11) DEFAULT NULL,
  `camaras` int(11) DEFAULT NULL,
  `accesorios` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `containers`
--

INSERT INTO `containers` (`id`, `cod`, `expediente`, `fecha`, `hora`, `motores`, `cajas`, `camaras`, `accesorios`, `created_at`, `updated_at`) VALUES
(3, 'TCNU5391958', '150166', '2015-07-01', '08:00:00', 95, 65, 0, 0, '2024-04-17 15:06:30', '2024-04-17 15:18:01'),
(4, 'ZIMU2527941', '150182', '2015-07-01', '08:00:00', 55, 0, 0, 0, '2024-04-17 15:08:42', '2024-04-17 15:08:42'),
(5, 'ZCSU8882203', '150243', '2015-10-01', '08:00:00', 45, 34, 0, 0, '2024-04-17 15:12:14', '2024-04-17 15:51:16'),
(6, 'CMAU5619733', '160293', '2016-08-01', '08:00:00', 44, 0, 0, 0, '2024-04-17 15:13:30', '2024-04-17 15:13:30'),
(7, 'TGHU0081376', '160329', '2016-07-01', '08:00:00', 40, 0, 0, 0, '2024-04-17 15:14:53', '2024-04-17 15:14:53'),
(8, 'CMAU5779032', '170233', '2017-10-01', '08:00:00', 61, 30, 0, 0, '2024-04-17 15:16:04', '2024-04-17 15:16:04'),
(9, 'TEMU4090712', '210262', '2021-10-01', '08:00:00', 66, 35, 0, 0, '2024-04-17 16:15:57', '2024-04-17 16:15:57'),
(10, 'MRKU7253489', '220101', '2022-04-01', '08:00:00', 58, 37, 0, 0, '2024-04-17 16:17:28', '2024-04-17 16:17:28'),
(11, 'XHCU5375284', '220282', '2022-09-01', '08:00:00', 49, 68, 0, 0, '2024-04-17 16:18:41', '2024-04-17 16:18:41'),
(12, 'FCIU2872471', '220329', '2022-11-01', '08:00:00', 87, 20, 0, 0, '2024-04-17 16:19:45', '2024-04-17 16:19:45'),
(13, 'CXDU1751820', '230058', '2023-03-01', '08:00:00', 84, 47, 0, 0, '2024-04-17 16:20:54', '2024-04-17 16:20:54'),
(14, 'SEKU6213120', '230145', '2023-07-01', '08:00:00', 90, 100, 0, 0, '2024-04-17 16:21:50', '2024-04-17 16:21:50'),
(15, 'BEAU5978274', '230319', '2023-12-01', '08:00:00', 46, 49, 0, 0, '2024-04-17 16:22:56', '2024-04-17 16:22:56'),
(16, 'TRHU7771420', '240001', '2024-01-01', '08:00:00', 76, 69, 0, 0, '2024-04-17 22:48:57', '2024-04-17 22:48:57'),
(18, 'EXISTENTE-2015-2017', '060325', '2025-03-06', '14:50:00', 340, 99, 0, 0, '2025-03-06 20:52:33', '2025-03-06 20:52:33'),
(19, '200922', '200922', '2025-03-19', '08:00:00', 10, 10, 10, 0, '2025-03-19 14:54:54', '2025-03-19 17:03:48'),
(20, 'CRSU9209960', '240222', '2024-01-07', '20:00:00', 88, 58, 101, 0, '2025-03-19 17:03:38', '2025-03-19 17:03:38'),
(21, 'TRHU6544206', '250090', '2025-03-22', '08:00:00', 81, 18, 76, 320, '2025-03-29 20:46:01', '2025-03-29 21:10:26'),
(22, 'MRKU7908457', '250089', '2025-03-26', '08:34:00', 45, 45, 10, 10, '2025-04-01 13:32:18', '2025-04-01 13:32:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `employees`
--

CREATE TABLE `employees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cedula` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `apellido` varchar(255) NOT NULL,
  `tlf` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `employees`
--

INSERT INTO `employees` (`id`, `cedula`, `nombre`, `apellido`, `tlf`, `created_at`, `updated_at`) VALUES
(1, 26136890, 'YORDY', 'JIMENEZ', '04126776250', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `exchange_rates`
--

CREATE TABLE `exchange_rates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rate` decimal(15,4) NOT NULL,
  `source` varchar(255) NOT NULL DEFAULT 'BCV',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `exchange_rates`
--

INSERT INTO `exchange_rates` (`id`, `rate`, `source`, `created_at`, `updated_at`) VALUES
(1, 396.3674, 'BCV', '2026-02-16 18:12:09', '2026-02-16 18:12:09');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventarios`
--

CREATE TABLE `inventarios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `origen` varchar(255) NOT NULL DEFAULT 'IMPORTADO',
  `item` varchar(50) NOT NULL,
  `tipo` varchar(255) NOT NULL,
  `marca` varchar(255) DEFAULT NULL,
  `modelo` varchar(255) DEFAULT NULL,
  `serial` varchar(255) DEFAULT NULL,
  `año` varchar(255) DEFAULT NULL,
  `codInv` varchar(255) DEFAULT NULL,
  `expediente` varchar(255) DEFAULT NULL,
  `condicion` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `price` varchar(255) NOT NULL,
  `costo` decimal(10,2) DEFAULT NULL,
  `price_sale` varchar(45) DEFAULT NULL,
  `container_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `categorie` varchar(255) DEFAULT NULL,
  `cantidad` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `inventarios`
--

INSERT INTO `inventarios` (`id`, `origen`, `item`, `tipo`, `marca`, `modelo`, `serial`, `año`, `codInv`, `expediente`, `condicion`, `status`, `price`, `costo`, `price_sale`, `container_id`, `created_at`, `updated_at`, `categorie`, `cantidad`) VALUES
(2, 'IMPORTADO', '3/4 Chevrolet Vitara J18', 'MOTOR 3/4', 'Chevrolet', 'Vitara J18', NULL, '2002', 'D0128', '060325', 'APLICA', 'DISPONIBLE', '700', NULL, '700', 18, '2025-03-06 23:15:49', '2026-02-18 20:47:52', NULL, NULL),
(3, 'IMPORTADO', 'Hyundai Getz G4ED', 'MOTOR 3/4', 'Hyundai', 'Getz G4ED', NULL, '2005', 'D0115', '060325', 'APLICA', 'DISPONIBLE', '500', NULL, '500', 18, '2025-03-06 23:17:44', '2026-02-18 20:47:52', NULL, NULL),
(4, 'IMPORTADO', 'Ford Festiva / Turpial', 'MOTOR COMPLETO', 'Ford', 'Festiva/ turpial', NULL, '2007', 'D0071', '060325', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 18, '2025-03-06 23:20:38', '2026-02-18 20:47:52', NULL, NULL),
(8, 'IMPORTADO', 'Motor Ford Festiva, Turpial 1.3L Nuevo', 'MOTOR COMPLETO', 'Ford', 'Festiva, Turpial', NULL, '2000-2007', 'D0348', '060325', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 18, '2025-03-10 21:24:50', '2026-02-18 20:47:52', NULL, NULL),
(9, 'IMPORTADO', 'Ford Festiva, Turpial', 'MOTOR COMPLETO', 'Ford', 'Festiva, Turpial', NULL, '2000-2007', '420', '060325', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 18, '2025-03-10 21:31:07', '2026-02-18 20:47:52', NULL, NULL),
(10, 'IMPORTADO', 'Motor 7/8 Toyota 4A 1.6L', 'MOTOR 3/4', 'Toyota', 'Avila, Sapito', NULL, '1998', 'D0034', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-10 21:44:49', '2026-02-18 20:47:52', NULL, NULL),
(11, 'IMPORTADO', 'Motor 3/4 Chevrolet Optra 2.0L tapa Negra', 'MOTOR 3/4', 'Chevrolet', 'Optra tapa negra Limité', NULL, '2005', '412', '060325', 'APLICA', 'DISPONIBLE', '800', NULL, '800', 18, '2025-03-10 21:46:49', '2026-02-18 20:47:52', NULL, NULL),
(12, 'IMPORTADO', 'Toyota 3VZ', 'MOTOR 3/4', 'Toyota', '3VZ', NULL, '2000', '16', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-10 21:48:11', '2026-02-18 20:47:52', NULL, NULL),
(13, 'IMPORTADO', 'Motor 3/4 Ford 4.6L 3V BA', 'MOTOR 3/4', 'Ford', 'Explorer 3V', NULL, '2006', '488', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-10 21:49:43', '2026-02-18 20:47:52', NULL, NULL),
(14, 'IMPORTADO', 'Motor 3/4 Toyota 2ZR Corolla 2016', 'MOTOR 3/4', 'Toyota', '2ZR', NULL, '2016', 'D0004', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-10 21:51:09', '2026-02-18 20:47:52', NULL, NULL),
(15, 'IMPORTADO', 'Motor 3/4 Chevrolet Cruz 1.8L', 'MOTOR 3/4', 'Chevrolet', 'Cruz 1.8L', NULL, '2010', '567', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-11 00:32:43', '2026-02-18 20:47:52', NULL, NULL),
(16, 'IMPORTADO', 'Motor 7/8 Nissan QR35', 'MOTOR 7/8', 'Nissan', 'QR35', NULL, '2000', 'D0412', '060325', 'APLICA', 'DISPONIBLE', '1.200', NULL, '1.200', 18, '2025-03-11 00:34:21', '2026-02-18 20:47:52', NULL, NULL),
(17, 'IMPORTADO', 'Motor 3/4 Hyundai Santa Fe 2.7L', 'MOTOR 3/4', 'Hyundai', 'Santa Fe 2.7L', NULL, '2006', 'D0019', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-11 00:35:59', '2026-02-18 20:47:52', NULL, NULL),
(18, 'IMPORTADO', 'Motor 7/8 Ford 4.2L 6V Fortaleza', 'MOTOR 7/8', 'Ford', 'Fortaleza 4.2L', NULL, '2005', '439', '240001', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 16, '2025-03-11 00:57:37', '2026-02-18 20:47:52', NULL, NULL),
(19, 'IMPORTADO', 'Motor 7/8 Honda K20A3', 'MOTOR 7/8', 'Honda', 'K20A3', NULL, '2000', 'D0394', '060325', 'APLICA', 'DISPONIBLE', '1.200', NULL, '1.200', 18, '2025-03-11 01:03:03', '2026-02-18 20:47:52', NULL, NULL),
(20, 'IMPORTADO', 'Motor completo Ford 3.0L Carburado', 'MOTOR COMPLETO', 'Ford', '3.0L para adaptar Nuevo', NULL, '1990', 'D0393', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-11 01:04:13', '2026-02-18 20:47:52', NULL, NULL),
(21, 'IMPORTADO', 'Motor 7/8 Ford Coyote 5.0L', 'MOTOR 7/8', 'Ford', 'Coyote 5.0L', NULL, '2016', '467', '240001', 'APLICA', 'DISPONIBLE', '3.500', NULL, '3.500', 16, '2025-03-11 01:05:42', '2026-02-18 20:47:52', NULL, NULL),
(22, 'IMPORTADO', 'Ford Mazda 2.3L', 'MOTOR 7/8', 'Ford', 'Mazda 2.3L', NULL, '2007', 'D0086', '210262', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 9, '2025-03-11 01:07:55', '2026-02-18 20:47:52', NULL, NULL),
(23, 'IMPORTADO', 'Motor 7/8 Ford Fusion 3.0L', 'MOTOR 7/8', 'Ford', 'Fisio 3.0L', NULL, '2007', 'D0331', '060325', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 18, '2025-03-11 01:10:58', '2026-02-18 20:47:52', NULL, NULL),
(24, 'IMPORTADO', 'Motor 7/8 Chevrolet Optra Limite', 'MOTOR 7/8', 'Chevrolet', 'Optra 1.8L limite', NULL, '2006', '465', '240001', 'APLICA', 'DISPONIBLE', '1.200', NULL, '1.200', 16, '2025-03-11 01:22:29', '2026-02-18 20:47:52', NULL, NULL),
(25, 'IMPORTADO', 'Motor 7/8 Hyundai G4ED', 'MOTOR 7/8', 'Hyundai', 'Accel, Río, Getz G4ED', NULL, '2008', 'D0708', '230145', 'APLICA', 'DISPONIBLE', '1.200', NULL, '1.200', 14, '2025-03-11 01:26:54', '2026-02-18 20:47:52', NULL, NULL),
(26, 'IMPORTADO', 'Motor 3/4 Mazda 6 2.3L', 'MOTOR 3/4', 'Ford', 'Mazda 6 2.3L', NULL, '2006', 'D0343', '210262', 'APLICA', 'DISPONIBLE', '800', NULL, '800', 9, '2025-03-11 01:30:57', '2026-02-18 20:47:52', NULL, NULL),
(27, 'IMPORTADO', 'Motor 7/8 Kia Espectra', 'MOTOR 7/8', 'Kia', 'Espectra', NULL, '2002', 'D0383', '220282', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 11, '2025-03-11 01:33:36', '2026-02-18 20:47:52', NULL, NULL),
(28, 'IMPORTADO', 'Motor 7/8 Chevrolet 5.3L taquetes Inteligente', 'MOTOR 7/8', 'Chevrolet', 'Silverado, Tahoe, Avalancha', NULL, '2007', '441', '060325', 'APLICA', 'DISPONIBLE', '1.800', NULL, '1.800', 18, '2025-03-11 01:34:58', '2026-02-18 20:47:52', NULL, NULL),
(29, 'IMPORTADO', 'Motor 7/8 Toyota 2UZ 4.7L VVTI', 'MOTOR 7/8', 'Toyota', 'Tundra 4.7L', NULL, '2007', '523', '240001', 'APLICA', 'DISPONIBLE', '3.500', NULL, '3.500', 16, '2025-03-11 01:36:51', '2026-02-18 20:47:52', NULL, NULL),
(30, 'IMPORTADO', 'Motor 7/8 Jeep Grand Cherokee 4.7 8B EGR', 'MOTOR 7/8', 'Jeep', 'Grand Cherokee', NULL, '2007', 'D0362', '220329', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 12, '2025-03-11 01:40:10', '2026-02-18 20:47:52', NULL, NULL),
(31, 'IMPORTADO', 'Motor 7/8 Jeep Cherokee 3.7L KJ', 'MOTOR 7/8', 'Jeep', 'Liberty 3.7L KJ', NULL, '2005', 'D0476', '220282', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 11, '2025-03-11 02:18:47', '2026-02-18 20:47:52', NULL, NULL),
(32, 'IMPORTADO', 'Motor 7/8 Toyota 4A 1.6L', 'MOTOR 7/8', 'Toyota', '4A 1.6L', NULL, '1998', 'D0050', '060325', 'APLICA', 'DISPONIBLE', '1.100', NULL, '1.100', 18, '2025-03-11 02:20:17', '2026-02-18 20:47:52', NULL, NULL),
(33, 'IMPORTADO', 'Motor 7/8 Toyota 4A 1.6L', 'MOTOR 7/8', 'Toyota', '4A 1.6L', NULL, '1998', '415', '060325', 'APLICA', 'DISPONIBLE', '1.100', NULL, '1.100', 18, '2025-03-11 02:21:31', '2026-02-18 20:47:52', NULL, NULL),
(34, 'IMPORTADO', 'Motor 7/8 Kia Caren G4KC', 'MOTOR 7/8', 'Hyundai', 'G4KC', NULL, '2007', '418', '060325', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 18, '2025-03-11 02:26:58', '2026-02-18 20:47:52', NULL, NULL),
(35, 'IMPORTADO', 'Motor 7/8 Chevrolet Traibleizer 4.2L', 'MOTOR 7/8', 'Chevrolet', 'Traibleizer 4.2L tapa Plástica', NULL, '2005', '325', '240001', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 16, '2025-03-11 02:29:39', '2026-02-18 20:47:52', NULL, NULL),
(36, 'IMPORTADO', 'Motor 7/8 Toyota 2GR- Camry', 'MOTOR 7/8', 'Toyota', '2GR- Camry', NULL, '2008', '487', '060325', 'APLICA', 'DISPONIBLE', '2.100', NULL, '2.100', 18, '2025-03-11 02:31:23', '2026-02-18 20:47:52', NULL, NULL),
(37, 'IMPORTADO', 'Motor 7/8 Hyundai Sonata G4NG', 'MOTOR 7/8', 'Hyundai', 'G4NG', NULL, '2010', '435', '060325', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 18, '2025-03-11 02:33:53', '2026-02-18 20:47:52', NULL, NULL),
(38, 'IMPORTADO', 'Motor 7/8 Ford Escape 3.0L', 'MOTOR 7/8', 'Ford', 'Escape 3.0L tapa Plástico', NULL, '2002', '446', '060325', 'APLICA', 'DISPONIBLE', '1.200', NULL, '1.200', 18, '2025-03-11 02:34:50', '2026-02-18 20:47:52', NULL, NULL),
(39, 'IMPORTADO', 'Motor 7/8 Chevrolet 5.3L 2010', 'MOTOR 7/8', 'Chevrolet', '5.3L 2010 taquete inteligente', NULL, '2010', '427', '060325', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 18, '2025-03-11 02:35:59', '2026-02-18 20:47:52', NULL, NULL),
(40, 'IMPORTADO', 'Motor 7/8 Chevrolet 5.3L 2010', 'MOTOR 7/8', 'Chevrolet', '5.3L Taquete Inteligente', NULL, '2010', '422', '060325', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 18, '2025-03-11 02:36:58', '2026-02-18 20:47:52', NULL, NULL),
(41, 'IMPORTADO', 'Motor 7/8 Jeep Grand Cherokee 5.7L 4G', 'MOTOR 7/8', 'Jeep', 'Grand Cherokee', NULL, '2012', '157', '240001', 'APLICA', 'DISPONIBLE', '2.800', NULL, '2.800', 16, '2025-03-11 02:38:19', '2026-02-18 20:47:52', NULL, NULL),
(42, 'IMPORTADO', 'Motor 7/8 Jeep 3.7L Cherokee Liberty', 'MOTOR 7/8', 'Jeep', 'Liberty', NULL, '2005', '443', '220282', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 11, '2025-03-11 02:39:32', '2026-02-18 20:47:52', NULL, NULL),
(43, 'IMPORTADO', 'Motor 7/8 Jeep Cherokee 3.7L KK', 'MOTOR 7/8', 'Jeep', 'Cherokee  3.7L KK', NULL, '2008', '321', '240001', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 16, '2025-03-11 02:41:30', '2026-02-18 20:47:52', NULL, NULL),
(44, 'IMPORTADO', 'Motor 7/8 Jeep Dodge Ram 5.7L', 'MOTOR 7/8', 'Jeep', 'Dodge Ram', NULL, '2008', '546', '230319', 'APLICA', 'DISPONIBLE', '1.800', NULL, '1.800', 15, '2025-03-11 02:43:21', '2026-02-18 20:47:52', NULL, NULL),
(45, 'IMPORTADO', 'Motor 7/8 Chevrolet Colorado Tapa Plástica', 'MOTOR 7/8', 'Chevrolet', 'Colorado 3.7L tapa Plástico', NULL, '2005', '474', '240001', 'APLICA', 'DISPONIBLE', '1.800', NULL, '1.800', 16, '2025-03-11 02:45:17', '2026-02-18 20:47:52', NULL, NULL),
(46, 'IMPORTADO', 'Motor 7/8 Hyundai G4KJ Sorento, Santa Fe', 'MOTOR 7/8', 'Hyundai', 'Sorento Santa fe', NULL, '2010-2015', '442', '240001', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 16, '2025-03-11 02:50:26', '2026-02-18 20:47:52', NULL, NULL),
(47, 'IMPORTADO', 'Motor 7/8 Jeep Rubicon 3.6L', 'MOTOR 7/8', 'Jeep', 'Rubicon', NULL, '2015-2018', '440', '230145', 'APLICA', 'DISPONIBLE', '4.000', NULL, '4.000', 14, '2025-03-11 02:52:18', '2026-02-18 20:47:52', NULL, NULL),
(48, 'IMPORTADO', 'Motor Hyundai Santa Fe 2.4L G4JS', 'MOTOR 7/8', 'Hyundai', 'G4JS', NULL, '2002-2006', '426', '230145', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 14, '2025-03-11 02:55:23', '2026-02-18 20:47:52', NULL, NULL),
(49, 'IMPORTADO', 'Motor 7/8 Jeep Grand Cherokee 5.7L 4G', 'MOTOR 7/8', 'Jeep', 'Grand Cherokee 5.7L 4G', NULL, '2012', '444', '240001', 'APLICA', 'DISPONIBLE', '2.800', NULL, '2.800', 16, '2025-03-11 02:59:08', '2026-02-18 20:47:52', NULL, NULL),
(50, 'IMPORTADO', 'Motor 7/8 Jeep Grand Cherokee 5.7L 4G', 'MOTOR 7/8', 'Jeep', 'Grand Cherokee 5.7L 4G', NULL, '2012', '421', '240001', 'APLICA', 'DISPONIBLE', '2.800', NULL, '2.800', 16, '2025-03-11 03:00:33', '2026-02-18 20:47:52', NULL, NULL),
(51, 'IMPORTADO', 'Motor 7/8 Jeep 4.7L 16 B', 'MOTOR 7/8', 'Jeep', 'Grand Cherokee 4.6L 16 B', NULL, '2009', '568', '240001', 'APLICA', 'DISPONIBLE', '2.200', NULL, '2.200', 16, '2025-03-11 03:05:58', '2026-02-18 20:47:52', NULL, NULL),
(52, 'IMPORTADO', 'Motor 7/8 Volkswagen Beta', 'MOTOR 7/8', 'Volkswagen', 'Beta', NULL, '2008', '485', '240001', 'APLICA', 'DISPONIBLE', '2.500', NULL, '2.500', 16, '2025-03-11 03:08:39', '2026-02-18 20:47:52', NULL, NULL),
(53, 'IMPORTADO', 'Motor 7/8 Honda R18 Emocio', 'MOTOR 7/8', 'Honda', 'R18', NULL, '2005', '160', '060325', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 18, '2025-03-11 03:10:38', '2026-02-18 20:47:52', NULL, NULL),
(54, 'IMPORTADO', 'Motor 7/8 Chevrolet Cruce 1.8L', 'MOTOR COMPLETO', 'Chevrolet', 'Cruce 1.8L', NULL, '2010', '503', '240001', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 16, '2025-03-11 14:21:20', '2026-02-18 20:47:52', NULL, NULL),
(55, 'IMPORTADO', 'Motor Nissan QR25', 'MOTOR COMPLETO', 'Nissan', 'QR25', NULL, '2015', '37', '210262', 'APLICA', 'DISPONIBLE', '1.800', NULL, '1.800', 9, '2025-03-11 14:23:44', '2026-02-18 20:47:52', NULL, NULL),
(56, 'IMPORTADO', 'Motor Hyundai Accel 1.6L', 'MOTOR COMPLETO', 'Hyundai', 'Accel, Getz, Río 1.6L', NULL, '2008', '498', '240001', 'APLICA', 'DISPONIBLE', '1.300', NULL, '1.300', 16, '2025-03-11 14:24:48', '2026-02-18 20:47:52', NULL, NULL),
(57, 'IMPORTADO', 'Motor 7/8 Jeep Caliber 2.0L', 'MOTOR 7/8', 'Jeep', 'Caliber 2.0L', NULL, '2006', '10', '220282', 'APLICA', 'DISPONIBLE', '1.300', NULL, '1.300', 11, '2025-03-11 14:25:52', '2026-02-18 20:47:52', NULL, NULL),
(58, 'IMPORTADO', 'Motor Hyundai G4BA 2.7L', 'MOTOR COMPLETO', 'Hyundai', 'Santa Fe 2.7L VVTI', NULL, '2010', '520', '230145', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 14, '2025-03-11 14:27:07', '2026-02-18 20:47:52', NULL, NULL),
(59, 'IMPORTADO', 'Motor Jeep Dodge 2.0L tapa Aluminio', 'MOTOR COMPLETO', 'Jeep', 'Dodge Tapa de aluminio', NULL, '2010', '518', '230145', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 14, '2025-03-11 14:28:28', '2026-02-18 20:47:52', NULL, NULL),
(60, 'IMPORTADO', 'Motor Hyundai 2.4L G4KE', 'MOTOR COMPLETO', 'Hyundai', 'G4KE', NULL, '2008', '502', '240001', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 16, '2025-03-11 14:29:36', '2026-02-18 20:47:52', NULL, NULL),
(61, 'IMPORTADO', 'Motor Ford Escape 3.0L tapa Platica', 'MOTOR COMPLETO', 'Ford', 'Escape 3.0L tapa de aluminio', NULL, '2004', '499', '230319', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 15, '2025-03-11 14:30:46', '2026-02-18 20:47:52', NULL, NULL),
(62, 'IMPORTADO', 'Motor 7/8 Mitsubishi 6G74', 'MOTOR COMPLETO', 'Mitsubishi', '6G74', NULL, '2008', '6', '220101', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 10, '2025-03-11 14:39:11', '2026-02-18 20:47:52', NULL, NULL),
(63, 'IMPORTADO', 'Motor Mazda 2.3L', 'MOTOR COMPLETO', 'Ford', 'Mazda 2.3L', NULL, '2008', '81', '230145', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 14, '2025-03-11 14:40:47', '2026-02-18 20:47:52', NULL, NULL),
(64, 'IMPORTADO', 'Motor Kia G4KH Turbo', 'MOTOR COMPLETO', 'Kia', 'G4KH', NULL, '2010', '515', '230145', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 14, '2025-03-11 14:42:06', '2026-02-18 20:47:52', NULL, NULL),
(65, 'IMPORTADO', 'Motor Chevrolet Impala 5.3L', 'MOTOR COMPLETO', 'Chevrolet', 'Impala 5.3L', NULL, '2010', '501', '230319', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 15, '2025-03-11 14:46:17', '2026-02-18 20:47:52', NULL, NULL),
(66, 'IMPORTADO', 'Motor Hyundai Elantra G4NB', 'MOTOR COMPLETO', 'Hyundai', 'Elantra G4NB', NULL, '2015', '511', '230319', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 15, '2025-03-11 14:47:31', '2026-02-18 20:47:52', NULL, NULL),
(67, 'IMPORTADO', 'Motor Mitsubishi Montero 3.5L', 'MOTOR COMPLETO', 'Mitsubishi', 'Montero G474 3.5L', NULL, '2008', '77', '210262', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 9, '2025-03-11 14:49:15', '2026-02-18 20:47:52', NULL, NULL),
(68, 'IMPORTADO', 'Motor Kia Caren 6DKD', 'MOTOR COMPLETO', 'Kia', 'Caren 6DKD', NULL, '2008', '82', '240001', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 16, '2025-03-11 14:50:36', '2026-02-18 20:47:52', NULL, NULL),
(69, 'IMPORTADO', 'Motor Hyundai G4FD', 'MOTOR COMPLETO', 'Hyundai', 'G4FD', NULL, '2010', '513', '230145', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 14, '2025-03-11 15:47:04', '2026-02-18 20:47:52', NULL, NULL),
(70, 'IMPORTADO', 'Motor Malibú 2.5L BT8', 'MOTOR COMPLETO', 'Chevrolet', 'Malibú', NULL, '2015', '274', '230058', 'APLICA', 'DISPONIBLE', '2.500', NULL, '2.500', 13, '2025-03-11 15:50:35', '2026-02-18 20:47:52', NULL, NULL),
(71, 'IMPORTADO', 'Motor Dodge Caliber 2.4L', 'MOTOR COMPLETO', 'Dodge', 'Caliber 2.4L', NULL, '2008', '508', '230145', 'APLICA', 'DISPONIBLE', '1.300', NULL, '1.300', 14, '2025-03-11 15:51:46', '2026-02-18 20:47:52', NULL, NULL),
(72, 'IMPORTADO', 'Motor Jeep 3.7L Cherokee Liberty', 'MOTOR COMPLETO', 'Jeep', 'Cherokee  3.7L KJ', NULL, '2005', '320', '230145', 'APLICA', 'DISPONIBLE', '1.300', NULL, '1.300', 14, '2025-03-11 15:54:10', '2026-02-18 20:47:52', NULL, NULL),
(73, 'IMPORTADO', 'Motor Hyundai G4KD 2.4L', 'MOTOR COMPLETO', 'Hyundai', 'G4KD 2.4L', NULL, '2008', '517', '230145', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 14, '2025-03-11 15:59:59', '2026-02-18 20:47:52', NULL, NULL),
(74, 'IMPORTADO', 'Motor Ford Fortaleza 4.6L 2V', 'MOTOR COMPLETO', 'Ford', 'Fortaleza 4.6L 2V', NULL, '2002', '8', '210262', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 9, '2025-03-11 16:01:10', '2026-02-18 20:47:52', NULL, NULL),
(75, 'IMPORTADO', 'Motor Ford Tritón 5.4L 2V', 'MOTOR COMPLETO', 'Ford', 'Tritón 5.4L 2v', NULL, '2005', '357', '240001', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 16, '2025-03-11 16:02:42', '2026-02-18 20:47:52', NULL, NULL),
(76, 'IMPORTADO', 'Motor Hyundai Accel 1.6L', 'MOTOR COMPLETO', 'Hyundai', 'Accel 1.6L', NULL, '2008', '510', '230319', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 15, '2025-03-11 16:04:31', '2026-02-18 20:47:52', NULL, NULL),
(77, 'IMPORTADO', 'Motor Jeep Grand Cherokee 4.7L 8B EGR', 'MOTOR COMPLETO', 'Jeep', 'Grand Cherokee 4.7L 8B EGR', NULL, '2008', '13', '220282', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 11, '2025-03-11 16:09:06', '2026-02-18 20:47:52', NULL, NULL),
(78, 'IMPORTADO', 'Motor Hyundai Elantra G4NB', 'MOTOR COMPLETO', 'Hyundai', 'Elantra G4NB', NULL, '2015', '491', '230319', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 15, '2025-03-11 16:24:02', '2026-02-18 20:47:52', NULL, NULL),
(79, 'IMPORTADO', 'Motor Hyundai Sonata 3.3L', 'MOTOR COMPLETO', 'Hyundai', 'Sonata 3.3L', NULL, '2008', '281', '240001', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 16, '2025-03-11 16:31:08', '2026-02-18 20:47:52', NULL, NULL),
(80, 'IMPORTADO', 'Motor Ford Escapé 3.0L Tapa Plástica', 'MOTOR COMPLETO', 'Ford', 'Escape 3.0L Tapa Plástico', NULL, '2002', '519', '230145', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 14, '2025-03-11 16:33:43', '2026-02-18 20:47:52', NULL, NULL),
(81, 'IMPORTADO', 'Motor Hyundai G4KE', 'MOTOR COMPLETO', 'Hyundai', 'G4KE', NULL, '2015', '512', '230145', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 14, '2025-03-11 16:34:33', '2026-02-18 20:47:52', NULL, NULL),
(82, 'IMPORTADO', 'Motor Chevrolet trailer Tapa Plástico', 'MOTOR COMPLETO', 'Chevrolet', 'Traibleizer 4.2L  Tapa Plástica', NULL, '2005', '497', '230145', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 14, '2025-03-11 16:37:52', '2026-02-18 20:47:52', NULL, NULL),
(83, 'IMPORTADO', 'Motor Hyundai Accel 1.6L', 'MOTOR COMPLETO', 'Hyundai', 'Accel, Getz, Rio 1.6L', NULL, '2008', '504', '230319', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 15, '2025-03-11 16:42:26', '2026-02-18 20:47:52', NULL, NULL),
(84, 'IMPORTADO', 'Motor Nissan 12 valvulad', 'MOTOR COMPLETO', 'Hyundai', 'KA24 MV', NULL, '1995', '529', '060325', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 18, '2025-03-11 16:43:29', '2026-02-18 20:47:52', NULL, NULL),
(85, 'IMPORTADO', 'Motor Ford Tritón 5.4L 2V', 'MOTOR COMPLETO', 'Ford', 'Tritón 5.4L 2v', NULL, '2000', '45', '230058', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 13, '2025-03-11 23:10:39', '2026-02-18 20:47:52', NULL, NULL),
(86, 'IMPORTADO', 'Motor Ford Tritón 5.4L 2V', 'MOTOR COMPLETO', 'Ford', 'Tritón 5.4L 2v', NULL, '2005', '317', '230058', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 13, '2025-03-11 23:12:23', '2026-02-18 20:47:52', NULL, NULL),
(87, 'IMPORTADO', 'Motor Jeep 4.7L 16Bujias', 'MOTOR COMPLETO', 'Jeep', '4.7L 16Bujias.', NULL, '2009', '296', '240001', 'APLICA', 'DISPONIBLE', '2.500', NULL, '2.500', 16, '2025-03-12 14:22:20', '2026-02-18 20:47:52', NULL, NULL),
(88, 'IMPORTADO', 'Motor Ford 4.6L 2V', 'MOTOR COMPLETO', 'Ford', 'Grand Marquiz', NULL, '2000', '493', '230319', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 15, '2025-03-12 14:26:43', '2026-02-18 20:47:52', NULL, NULL),
(89, 'IMPORTADO', 'Motor Toyota 3VZ 3.4L', 'MOTOR COMPLETO', 'Toyota', '3VZ', NULL, '1992', '287', '230319', 'APLICA', 'DISPONIBLE', '1.800', NULL, '1.800', 15, '2025-03-12 14:29:05', '2026-02-18 20:47:52', NULL, NULL),
(90, 'IMPORTADO', 'Motor Toyota 5VZ 3.4L', 'MOTOR COMPLETO', 'Toyota', '5VZ Prado, Runner', NULL, '1995', '305', '240001', 'APLICA', 'DISPONIBLE', '1.800', NULL, '1.800', 16, '2025-03-12 14:30:06', '2026-02-18 20:47:52', NULL, NULL),
(91, 'IMPORTADO', 'Motor Chevrolet Traibleizer 4.2L TP', 'MOTOR COMPLETO', 'Chevrolet', 'Traibleizer 4.2L Tapa Plástico', NULL, '2005', '359', '230058', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 13, '2025-03-12 14:31:10', '2026-02-18 20:47:52', NULL, NULL),
(92, 'IMPORTADO', 'Motor Chevrolet trailer Tapa Plástico', 'MOTOR COMPLETO', 'Chevrolet', 'Traibleizer 4.2L Tapa Plástico', NULL, '2005', '360', '230058', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 13, '2025-03-12 14:32:06', '2026-02-18 20:47:52', NULL, NULL),
(93, 'IMPORTADO', 'Motor Jeep 3.7L KK', 'MOTOR COMPLETO', 'Jeep', 'Cherokee  3.7L KK', NULL, '2008', '356', '230319', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 15, '2025-03-12 14:33:13', '2026-02-18 20:47:52', NULL, NULL),
(94, 'IMPORTADO', 'Motor Jeep 3.7L KK', 'MOTOR COMPLETO', 'Jeep', 'Cherokee  3.7L KK', NULL, '2008', '303', '230319', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 15, '2025-03-12 14:35:51', '2026-02-18 20:47:52', NULL, NULL),
(95, 'IMPORTADO', 'Motor Chevrolet 262 Vortec', 'MOTOR COMPLETO', 'Chevrolet', '262 Tipo Vortec', NULL, '1998', '298', '240001', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 16, '2025-03-12 14:37:05', '2026-02-18 20:47:52', NULL, NULL),
(96, 'IMPORTADO', 'Motor Jeep 4.7L 8B EGR', 'MOTOR COMPLETO', 'Jeep', 'Grand Cherokee 4.7L 8B EGR', NULL, '2006', '295', '230145', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 14, '2025-03-12 14:39:17', '2026-02-18 20:47:52', NULL, NULL),
(97, 'IMPORTADO', 'Motor Chevrolet Vitara H25', 'MOTOR COMPLETO', 'Chevrolet', 'Grand Vitara XL5', NULL, '2005', '340', '230145', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 14, '2025-03-12 14:40:20', '2026-02-18 20:47:52', NULL, NULL),
(98, 'IMPORTADO', 'Motor Chevrolet Vitara H25', 'MOTOR COMPLETO', 'Chevrolet', 'Grand Vitara XL5', NULL, '2005', '530', '240001', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 16, '2025-03-12 14:41:23', '2026-02-18 20:47:52', NULL, NULL),
(99, 'IMPORTADO', 'Motor Chevrolet Vitara 4 cilindros', 'MOTOR COMPLETO', 'Chevrolet', 'Vitara 4 cilindros', NULL, '1992', '339', '060325', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 18, '2025-03-12 14:43:11', '2026-02-18 20:47:52', NULL, NULL),
(100, 'IMPORTADO', 'Motor Chevrolet 262 Vortec', 'MOTOR COMPLETO', 'Chevrolet', '262 Vortec', NULL, '1998', '311', '240001', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 16, '2025-03-12 14:45:04', '2026-02-18 20:47:52', NULL, NULL),
(101, 'IMPORTADO', 'Motor Jeep 4.7L 8 B EGR', 'MOTOR COMPLETO', 'Jeep', 'Grand Cherokee 4.7L 8B', NULL, '1600', '1600', '240001', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 16, '2025-03-12 14:46:47', '2026-02-18 20:47:52', NULL, NULL),
(102, 'IMPORTADO', 'Motor Jeep Grand Cherokee 4.7L 8B EGR', 'MOTOR COMPLETO', 'Jeep', 'Grand Cherokee', NULL, '2007', '594', '240001', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 16, '2025-03-12 14:48:17', '2026-02-18 20:47:52', NULL, NULL),
(103, 'IMPORTADO', 'Motor Chevrolet 262 Vortec', 'MOTOR COMPLETO', 'Chevrolet', 'Vortec', NULL, '1400', '297', '220282', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 11, '2025-03-12 14:49:45', '2026-02-18 20:47:52', NULL, NULL),
(104, 'IMPORTADO', 'Motor Chevrolet Rey Camión 6.0L', 'MOTOR COMPLETO', 'Chevrolet', 'Rey Camion 6.0L', NULL, '2010', '653', '240001', 'APLICA', 'DISPONIBLE', '2.500', NULL, '2.500', 16, '2025-03-12 14:58:48', '2026-02-18 20:47:52', NULL, NULL),
(105, 'IMPORTADO', 'Motor Chevrolet 4.8L Nuevo', 'MOTOR COMPLETO', 'Chevrolet', 'Silverado 4.8L', NULL, '2008', '456', '230058', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 13, '2025-03-12 14:59:57', '2026-02-18 20:47:52', NULL, NULL),
(106, 'IMPORTADO', 'Motor Toyota 1GR-II Generación', 'MOTOR 7/8', 'Toyota', '1GR-II', NULL, '2015', '527', '230058', 'APLICA', 'DISPONIBLE', '5.000', NULL, '5.000', 13, '2025-03-12 15:00:55', '2026-02-18 20:47:52', NULL, NULL),
(107, 'IMPORTADO', 'Motor Chevrolet 5.3L 2003', 'MOTOR COMPLETO', 'Chevrolet', 'Silverado 5.3L', NULL, '2003', '468', '230145', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 14, '2025-03-12 15:02:37', '2026-02-18 20:47:52', NULL, NULL),
(108, 'IMPORTADO', 'Motor 7/8 Chevrolet 5.3L 2010', 'MOTOR 7/8', 'Chevrolet', 'Silverado 5.3L', NULL, '2010', '668', '240001', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 16, '2025-03-12 15:04:24', '2026-02-18 20:47:52', NULL, NULL),
(109, 'IMPORTADO', 'Motor 7/8 Chevrolet 5.3L 2015', 'MOTOR 7/8', 'Chevrolet', 'Silverado 5.3L', NULL, '2015', '290', '230145', 'APLICA', 'DISPONIBLE', '3.000', NULL, '3.000', 14, '2025-03-12 15:06:01', '2026-02-18 20:47:52', NULL, NULL),
(110, 'IMPORTADO', 'Motor Ford 4.6L 2V', 'MOTOR 7/8', 'Ford', 'Fortaleza 4.6L 2V', NULL, '2005', '492', '230145', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 14, '2025-03-12 15:06:59', '2026-02-18 20:47:52', NULL, NULL),
(111, 'IMPORTADO', 'Motor 7/8 Hyundai Tucson 2.0L', 'MOTOR 7/8', 'Hyundai', 'Tucson 2.0L', NULL, '2006', '479', '230145', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 14, '2025-03-12 15:26:25', '2026-02-18 20:47:52', NULL, NULL),
(112, 'IMPORTADO', '112', 'MOTOR 7/8', 'Chevrolet', 'Traibleizer 4.2L tapa Aluminio', NULL, '2007', '476', '240001', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 16, '2025-03-12 15:27:42', '2026-02-18 20:47:52', NULL, NULL),
(113, 'IMPORTADO', 'Motor 7/8 Chevrolet Traibleizer 4.2L TA', 'MOTOR 7/8', 'Chevrolet', 'Traibleizer 4.2L tapa Aluminio', NULL, '2007', '473', '230145', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 14, '2025-03-12 15:28:52', '2026-02-18 20:47:52', NULL, NULL),
(114, 'IMPORTADO', 'Motor Chevrolet Traibleizer 4.2L TP', 'MOTOR COMPLETO', 'Chevrolet', 'Traibleizer 4.2L tapa Plástica', NULL, '2005', '495', '230058', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 13, '2025-03-12 15:30:23', '2026-02-18 20:47:52', NULL, NULL),
(115, 'IMPORTADO', 'Motor Chevrolet 5.3 Traibleizer', 'MOTOR COMPLETO', 'Chevrolet', 'Traibleizer 5.3L', NULL, '2008', '458', '230319', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 15, '2025-03-12 15:34:00', '2026-02-18 20:47:52', NULL, NULL),
(116, 'IMPORTADO', 'Motor Chevrolet 5.3L Impala', 'MOTOR COMPLETO', 'Chevrolet', 'IMPALA 5.3L', NULL, '2008', '489', '230319', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 15, '2025-03-12 15:35:20', '2026-02-18 20:47:52', NULL, NULL),
(117, 'IMPORTADO', 'Motor 7/8 Toyota 1ZZ', 'MOTOR 7/8', 'Toyota', 'Corolla 1ZZ', NULL, '2010', '250', '230058', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 13, '2025-03-12 16:10:24', '2026-02-18 20:47:52', NULL, NULL),
(118, 'IMPORTADO', 'Motor 7/8 Toyota 1ZZ', 'MOTOR COMPLETO', 'Toyota', '1ZZ', NULL, '2010', '35', '210262', 'APLICA', 'DISPONIBLE', '1.700', NULL, '1.700', 9, '2025-03-12 16:11:56', '2026-02-18 20:47:52', NULL, NULL),
(119, 'IMPORTADO', 'Motor 7/8 Toyota 1ZZ', 'MOTOR 7/8', 'Toyota', '1ZZ', NULL, '2010', '425', '230145', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 14, '2025-03-12 16:14:52', '2026-02-18 20:47:52', NULL, NULL),
(120, 'IMPORTADO', 'Motor 7/8 Dodge Caliber 2.0L', 'MOTOR 7/8', 'Dodge', 'Caliber 2.0L', NULL, '2005', '538', '230145', 'APLICA', 'DISPONIBLE', '1.300', NULL, '1.300', 14, '2025-03-12 16:23:06', '2026-02-18 20:47:52', NULL, NULL),
(121, 'IMPORTADO', 'Motor 7/8 Chevrolet Cruz 1.8L', 'MOTOR 7/8', 'Chevrolet', 'Cruze', NULL, '2010', '480', '230145', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 14, '2025-03-12 16:23:52', '2026-02-18 20:47:52', NULL, NULL),
(122, 'IMPORTADO', 'Motor Honda Civic Híbrido', 'MOTOR 7/8', 'Honda', 'Civic', NULL, '2000', '406', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-12 16:25:03', '2026-02-18 20:47:52', NULL, NULL),
(123, 'IMPORTADO', 'Motor 7/8 Toyota 2GR-Tacoma', 'MOTOR 7/8', 'Toyota', '2GR-TACOMA', NULL, '2015', '483', '230145', 'APLICA', 'DISPONIBLE', '3.500', NULL, '3.500', 14, '2025-03-12 16:26:29', '2026-02-18 20:47:52', NULL, NULL),
(124, 'IMPORTADO', 'Motor Jeep 6.1 Azul', 'MOTOR 7/8', 'Jeep', '6.1L', NULL, '2000', '534', '060325', 'APLICA', 'DISPONIBLE', '3.000', NULL, '3.000', 18, '2025-03-12 16:28:21', '2026-02-18 20:47:52', NULL, NULL),
(125, 'IMPORTADO', 'Motor Chevrolet 5.7L Ls3', 'MOTOR COMPLETO', 'Chevrolet', '5.7L LS3', NULL, '2007', '454', '060325', 'APLICA', 'DISPONIBLE', '2.500', NULL, '2.500', 18, '2025-03-12 16:29:16', '2026-02-18 20:47:52', NULL, NULL),
(126, 'IMPORTADO', 'Motor Chevrolet 454 anaranjado', 'MOTOR COMPLETO', 'Chevrolet', '454', NULL, '2008', '532', '060325', 'APLICA', 'DISPONIBLE', '4.000', NULL, '4.000', 18, '2025-03-12 16:31:49', '2026-02-18 20:47:52', NULL, NULL),
(127, 'IMPORTADO', 'Hyundai Sorento G6DA 3.8L', 'MOTOR 7/8', 'Hyundai', 'Sonata 3.8L', NULL, '2008', 'D0630', '230145', 'APLICA', 'DISPONIBLE', '2.300', NULL, '2.300', 14, '2025-03-14 00:15:50', '2026-02-18 20:47:52', NULL, NULL),
(128, 'IMPORTADO', 'Motor 7/8 Chevrolet 5.3L BA', 'MOTOR 7/8', 'Chevrolet', 'Silverado  BA', NULL, '2008', '645', '240001', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 16, '2025-03-14 00:17:34', '2026-02-18 20:47:52', NULL, NULL),
(129, 'IMPORTADO', 'Motor 7/8 Chevrolet 5.3L Taquete Mecánico', 'MOTOR 7/8', 'Chevrolet', '5.3L', NULL, '2005', '1S/C', '240001', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 16, '2025-03-14 00:19:53', '2026-02-18 20:47:52', NULL, NULL),
(130, 'IMPORTADO', 'Motor 3/4 Ford Tritón 5.4L 2V', 'MOTOR 3/4', 'Ford', '5.4L 2V', NULL, '2010', '2S/C', '230145', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 14, '2025-03-14 00:24:03', '2026-02-18 20:47:52', NULL, NULL),
(131, 'IMPORTADO', 'Motor 7/8 Chevrolet 5.3L 2008', 'MOTOR 7/8', 'Chevrolet', 'Silverado 5.3L', NULL, '2008', '3S/C', '230319', 'APLICA', 'DISPONIBLE', '1.900', NULL, '1.900', 15, '2025-03-14 00:31:31', '2026-02-18 20:47:52', NULL, NULL),
(132, 'IMPORTADO', 'Motor 7/8 Toyota 2AZ', 'MOTOR 7/8', 'Toyota', '2AZ', NULL, '2008', '544', '230319', 'APLICA', 'DISPONIBLE', '1.700', NULL, '1.700', 15, '2025-03-14 00:32:53', '2026-02-18 20:47:52', NULL, NULL),
(133, 'IMPORTADO', 'Motor Chevrolet 6.2L LS', 'MOTOR COMPLETO', 'Chevrolet', 'Ls 6.2L', NULL, '2010', '4S/C', '230319', 'APLICA', 'DISPONIBLE', '4.000', NULL, '4.000', 15, '2025-03-14 00:36:43', '2026-02-18 20:47:52', NULL, NULL),
(134, 'IMPORTADO', 'Motor 7/8 Toyota 4A 1.6L', 'MOTOR 7/8', 'Toyota 4A 1.6L', 'Avila, Sapito', NULL, '1995', '5S/C', '060325', 'APLICA', 'DISPONIBLE', '1.100', NULL, '1.100', 18, '2025-03-14 00:43:24', '2026-02-18 20:47:52', NULL, NULL),
(135, 'IMPORTADO', 'Motor 3\\4 Toyota 2ZR', 'MOTOR 3/4', 'TOYOTA', '2ZR VVTI', NULL, '2005', '6S/C', '230145', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 14, '2025-03-14 00:46:55', '2026-02-18 20:47:52', NULL, NULL),
(136, 'IMPORTADO', 'Motor 3\\4 Ford Ecosport 2.0l', 'MOTOR 3/4', 'Ford', 'Ecosport 2.0L', NULL, '2015', '7S/C', '230145', 'APLICA', 'DISPONIBLE', '1.300', NULL, '1.300', 14, '2025-03-14 00:48:09', '2026-02-18 20:47:52', NULL, NULL),
(137, 'IMPORTADO', 'Motor 7/8 Jeep Cherokee 3.7L Kj', 'MOTOR 7/8', 'Jeep', '3.7L', NULL, '2005', '8S/C', '230145', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 14, '2025-03-14 01:04:03', '2026-02-18 20:47:52', NULL, NULL),
(138, 'IMPORTADO', 'Motor 7/8 Jeep Grand Cherokee 4.7L 8B EGR', 'MOTOR 7/8', 'Jeep', 'Grand Cherokee 4.7L', NULL, '2008', '9 S/C', '240001', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 16, '2025-03-14 01:06:21', '2026-02-18 20:47:52', NULL, NULL),
(139, 'IMPORTADO', 'Motor 7/8 Jeep Cherokee 3.7L KJ', 'MOTOR 7/8', 'Jeep', 'Cherokee  3.7L KJ', NULL, '2005', '10S/C', '230145', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 14, '2025-03-14 01:10:09', '2026-02-18 20:47:52', NULL, NULL),
(140, 'IMPORTADO', 'Motor 7/8 Chevrolet Colorado TA', 'MOTOR 7/8', 'Chevrolet', 'Colorado 3.7', NULL, '2005', '11S/C', '230145', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 14, '2025-03-14 01:14:53', '2026-02-18 20:47:52', NULL, NULL),
(141, 'IMPORTADO', 'Motor 7/8 Dodge Ram 5.7L', 'MOTOR 7/8', 'Jeep', 'Dodge Ram 5.7L', NULL, '2006', '12S/C', '230058', 'APLICA', 'DISPONIBLE', '1.800', NULL, '1.800', 13, '2025-03-14 01:26:56', '2026-02-18 20:47:52', NULL, NULL),
(142, 'IMPORTADO', 'Motor 7/8 Toyota 1NZ Yaris', 'MOTOR 7/8', 'Toyota', '1NZ Yaris', NULL, '2005', '13S/C', '230145', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 14, '2025-03-14 14:31:49', '2026-02-18 20:47:52', NULL, NULL),
(143, 'IMPORTADO', 'Motor Toyota 2AZ Cammry- previa', 'MOTOR COMPLETO', 'Toyota', 'Camrry- Previa', NULL, '2008', '14S/C', '230319', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 15, '2025-03-14 14:34:16', '2026-02-18 20:47:52', NULL, NULL),
(144, 'IMPORTADO', 'Motor 7/8 Hyundai Tucson 2.0L', 'MOTOR 7/8', 'Hyundai', 'Tucson', NULL, '2008', '15S/C', '230145', 'APLICA', 'DISPONIBLE', '1.300', NULL, '1.300', 14, '2025-03-14 14:37:30', '2026-02-18 20:47:52', NULL, NULL),
(145, 'IMPORTADO', 'Motor 7/8 Toyota 5VZ 3.4L', 'MOTOR 7/8', 'Toyota', '5VZ 3.4L', NULL, '2005', '16S/C', '060325', 'APLICA', 'DISPONIBLE', '1.800', NULL, '1.800', 18, '2025-03-14 14:46:08', '2026-02-18 20:47:52', NULL, NULL),
(146, 'IMPORTADO', 'Motor 3/4 Ford 5.4L 2V', 'MOTOR 7/8', 'Ford', 'Triton', NULL, '2006', '17S/C', '240001', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 16, '2025-03-14 14:47:32', '2026-02-18 20:47:52', NULL, NULL),
(147, 'IMPORTADO', 'Motor Jeep 3.7L KK Hidrido', 'MOTOR COMPLETO', 'Jeep', 'Cherokee  3.7L KK Hidrido', NULL, '2008', '18S/C', '230145', 'APLICA', 'DISPONIBLE', '1.800', NULL, '1.800', 14, '2025-03-14 14:55:40', '2026-02-18 20:47:52', NULL, NULL),
(148, 'IMPORTADO', 'Motor 7/8 Ford 5.4L 3V', 'MOTOR 7/8', 'Ford', 'FX4 5.4L 3V', NULL, '2008', '19S/C', '230145', 'APLICA', 'DISPONIBLE', '1.800', NULL, '1.800', 14, '2025-03-14 15:02:04', '2026-02-18 20:47:52', NULL, NULL),
(149, 'IMPORTADO', 'Motor 7/8 Ford Tritón 5.4L 2V', 'MOTOR 7/8', 'Ford', '5.4L 2V', NULL, '2010', '20S/C', '230145', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 14, '2025-03-14 15:03:48', '2026-02-18 20:47:52', NULL, NULL),
(150, 'IMPORTADO', 'Motor 7/8 Toyota 2ZR Corolla', 'MOTOR 7/8', 'Toyota 2ZR', 'Corolla', NULL, '2016', '21S/C', '230058', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 13, '2025-03-14 15:09:38', '2026-02-18 20:47:52', NULL, NULL),
(151, 'IMPORTADO', 'Motor 7/8 Chevrolet Vitara XL5', 'MOTOR 7/8', 'Chevrolet', 'Grand Vitara XL5', NULL, '2005', '22S/C', '230058', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 13, '2025-03-14 15:10:56', '2026-02-18 20:47:52', NULL, NULL),
(152, 'IMPORTADO', 'Motor 7/8 Mitsubishi 6G75 3.8L Mivec', 'MOTOR 7/8', 'Mitsubishi', '6G75 3.8L', NULL, '2002', '23S/C', '240001', 'NO APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 16, '2025-03-14 15:14:52', '2026-02-18 20:47:52', NULL, NULL),
(153, 'IMPORTADO', 'Motor Chevrolet 350 Tapa Rallada', 'MOTOR COMPLETO', 'Chevrolet', '350 Tapa Rallada', NULL, '1995', '24S/C', '060325', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 18, '2025-03-14 15:20:56', '2026-02-18 20:47:52', NULL, NULL),
(154, 'IMPORTADO', 'Motor Chevrolet Van Exprés 6.0L', 'MOTOR COMPLETO', 'Chevrolet', 'Van Exprés 6.0L', NULL, '2006', '25S/C', '220329', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 12, '2025-03-14 15:21:53', '2026-02-18 20:47:52', NULL, NULL),
(155, 'IMPORTADO', 'Motor Chevrolet 5.3L Taquete Mecánico', 'MOTOR COMPLETO', 'Chevrolet', 'Silverado 5.3L', NULL, '2001', '26S/C', '230058', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 13, '2025-03-14 15:24:00', '2026-02-18 20:47:52', NULL, NULL),
(156, 'IMPORTADO', 'Motor Toyota 1UR', 'MOTOR COMPLETO', 'Toyota', '1UR', NULL, '2015', '490', '240001', 'APLICA', 'DISPONIBLE', '3.000', NULL, '3.000', 16, '2025-03-14 16:14:10', '2026-02-18 20:47:52', NULL, NULL),
(157, 'IMPORTADO', 'Motor Jeep 318 Modelo Viejo', 'MOTOR COMPLETO', 'Jeep', '318 modelo viejo', NULL, '1900', '308', '220282', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 11, '2025-03-14 16:15:23', '2026-02-18 20:47:52', NULL, NULL),
(158, 'IMPORTADO', 'Motor Chevrolet Rey Camión 6.0L', 'MOTOR COMPLETO', 'Chevrolet', 'Rey Camión 6.0L', NULL, '2010', '570', '240001', 'APLICA', 'DISPONIBLE', '2.500', NULL, '2.500', 16, '2025-03-14 16:36:51', '2026-02-18 20:47:52', NULL, NULL),
(159, 'IMPORTADO', 'Motor 3/4 Toyota Celica 2ZZ', 'MOTOR 3/4', 'Toyota', 'Celica 2ZZ', NULL, '2000', '27S/C', '230058', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 13, '2025-03-14 17:00:00', '2026-02-18 20:47:52', NULL, NULL),
(160, 'IMPORTADO', 'Motor 3/4 Toyota Celica 2ZZ', 'MOTOR 3/4', 'Toyota', 'Celica 2ZZ', NULL, '2000', '28S/C', '230058', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 13, '2025-03-14 17:00:42', '2026-02-18 20:47:52', NULL, NULL),
(161, 'IMPORTADO', 'Motor Ford Explorer 3.5L TA/ Mazda CX9', 'MOTOR COMPLETO', 'Ford', 'Explorer 3.5L/ Mazda CX9', NULL, '2010', '12', '210262', 'NO APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 9, '2025-03-18 00:38:25', '2026-02-18 20:47:52', NULL, NULL),
(162, 'IMPORTADO', 'Motor Ford 3.5L Explorer TA/Mazda Cx9', 'MOTOR COMPLETO', 'Ford', 'Ford 3.5L/Mazda Cx9', NULL, '2010', '509', '210262', 'NO APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 9, '2025-03-18 00:41:36', '2026-02-18 20:47:52', NULL, NULL),
(163, 'IMPORTADO', 'Motor 7/8 Ford Explorer 3.5L/MazdaCX9', 'MOTOR 7/8', 'Ford', 'Explorer 3.5L TA/Mazda CX9', NULL, '2010', '31', '060325', 'NO APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-18 00:42:54', '2026-02-18 20:47:52', NULL, NULL),
(164, 'IMPORTADO', 'Motor 7/8 Ford 3.5L TA/Mazda CX9', 'MOTOR 7/8', 'Ford', 'Ford Explorer 3.5L TA/Mazda CX9', NULL, '2010', '5', '060325', 'NO APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-18 00:44:14', '2026-02-18 20:47:52', NULL, NULL),
(165, 'IMPORTADO', 'Motor 7/8 Jeep Dodge Ram 5.7L', 'MOTOR 7/8', 'Jeep', 'Dodge Ram 5.7L', NULL, '2008', '166', '220329', 'APLICA', 'DISPONIBLE', '1.800', NULL, '1.800', 12, '2025-03-18 00:45:35', '2026-02-18 20:47:52', NULL, NULL),
(166, 'IMPORTADO', 'Motor 7/8 Toyota 2GR-VVTI Tacoma', 'MOTOR 7/8', 'Toyota', 'Tacoma 2GR-VVTI', NULL, '2015', '216', '230319', 'APLICA', 'DISPONIBLE', '4.000', NULL, '4.000', 15, '2025-03-18 00:47:26', '2026-02-18 20:47:52', NULL, NULL),
(167, 'IMPORTADO', 'Motor 7/8 Ford Explorer 3.5L TA/MazdaCX9', 'MOTOR COMPLETO', 'Ford', 'Ford Explorer 3.5L TA/Mazda CX9', NULL, '2010', '29S/C', '210262', 'NO APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 9, '2025-03-18 00:51:17', '2026-02-18 20:47:52', NULL, NULL),
(168, 'IMPORTADO', 'Motor Dodge 360 Magnum', 'MOTOR COMPLETO', 'Dodge', 'Magnum', NULL, '1998', '56', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-18 00:52:53', '2026-02-18 20:47:52', NULL, NULL),
(169, 'IMPORTADO', 'Motor 7/8 Ford Fortaleza 4.6L 2V', 'MOTOR 7/8', 'Ford', 'Fortaleza 4.6L 2V', NULL, '2006', '30S/C', '230319', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 15, '2025-03-18 00:54:29', '2026-02-18 20:47:52', NULL, NULL),
(170, 'IMPORTADO', 'Motor 7/8 Chevrolet Traibleizer TA', 'MOTOR 7/8', 'Chevrolet', 'Traibleizer 4.2L tapa Aluminio', NULL, '2008', '324', '240001', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 16, '2025-03-18 01:02:53', '2026-02-18 20:47:52', NULL, NULL),
(171, 'IMPORTADO', 'Motor 7/8 Dodge Caliber 2.0L', 'MOTOR 7/8', 'Dosge', 'Caliber 2.4L', NULL, '2008', '352', '230319', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 15, '2025-03-18 01:03:51', '2026-02-18 20:47:52', NULL, NULL),
(172, 'IMPORTADO', 'Motor 7/8 Chevrolet 5.3L TM 2002', 'MOTOR 7/8', 'Chevrolet', '5.3L Taquete Mecanico', NULL, '2002', '31S\\C', '240001', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 16, '2025-03-18 01:07:44', '2026-02-18 20:47:52', NULL, NULL),
(173, 'IMPORTADO', 'Motor 7/8 Chevrolet 5.3L TM 2002', 'MOTOR 7/8', 'Chevrolet', '5.3L Taquete Mecánico', NULL, '2002', '32S/C', '240001', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 16, '2025-03-18 01:09:24', '2026-02-18 20:47:52', NULL, NULL),
(174, 'IMPORTADO', 'Motor 7/8 Chevrolet 5.3L 2002', 'MOTOR 7/8', 'Chevrolet', 'Silverado 5.3L', NULL, '2002', '33S/C', '240001', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 16, '2025-03-18 01:10:45', '2026-02-18 20:47:52', NULL, NULL),
(175, 'IMPORTADO', 'Moro 7/8 Honda Civic F23A1', 'MOTOR 7/8', 'Honda', 'Civic F23A1', NULL, '2000', '50', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-18 01:16:19', '2026-02-18 20:47:52', NULL, NULL),
(176, 'IMPORTADO', 'Motor 7/8 Honda D17A1', 'MOTOR 7/8', 'Honda', 'Civic D17A1', NULL, '2000', '628', '240001', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 16, '2025-03-18 01:17:35', '2026-02-18 20:47:52', NULL, NULL),
(177, 'IMPORTADO', 'Motor 7/8 Honda Civic D16Y7', 'MOTOR 7/8', 'Honda', 'Civic D16Y7', NULL, '2000', '626', '240001', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 16, '2025-03-18 01:18:43', '2026-02-18 20:47:52', NULL, NULL),
(178, 'IMPORTADO', 'Motor 7/8 Toyota 3UR 5.7L', 'MOTOR 7/8', 'Toyota', 'Tundra 5.7L', NULL, '2015', 'D0844', '240001', 'APLICA', 'DISPONIBLE', '4.000', NULL, '4.000', 16, '2025-03-18 01:19:42', '2026-02-18 20:47:52', NULL, NULL),
(179, 'IMPORTADO', 'Motor Nissan KA24 FRONTIER', 'MOTOR COMPLETO', 'Nissan', 'KA24 FRONTIER', NULL, '2010', '550', '240001', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 16, '2025-03-19 01:10:37', '2026-02-18 20:47:52', NULL, NULL),
(180, 'IMPORTADO', 'Motor 7/8 Ford 200', 'MOTOR 7/8', 'Ford', '200', NULL, '1990', '272', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-19 14:42:21', '2026-02-18 20:47:52', NULL, NULL),
(181, 'IMPORTADO', 'Motor 7/8 Chevrolet 350', 'MOTOR 7/8', 'Chevrolet', '350 Tapa Rallada', NULL, '1995', '271', '060325', 'APLICA', 'DISPONIBLE', '1.200', NULL, '1.200', 18, '2025-03-19 14:44:02', '2026-02-18 20:47:52', NULL, NULL),
(182, 'IMPORTADO', 'Motor 7/8 Hyundai Santa Fe/Sorento G4JS', 'MOTOR 7/8', 'Hyundai', 'Santa Fe/Sorento 2.4L', NULL, '2002-2006', '270', '200922', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 19, '2025-03-19 15:01:32', '2026-02-18 20:47:52', NULL, NULL),
(183, 'IMPORTADO', 'Motor 7/8 Jeep Cherokee 3.7L KK', 'MOTOR 7/8', 'Jeep', 'Cherokee  3.7L KK', NULL, '2008', '275', '230145', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 14, '2025-03-19 15:03:07', '2026-02-18 20:47:52', NULL, NULL),
(184, 'IMPORTADO', 'Motor Chevrolet Cruce', 'MOTOR COMPLETO', 'Jeep', 'Chevrolet Cruce', NULL, '2010', '292', '240001', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 16, '2025-03-19 15:04:17', '2026-02-18 20:47:52', NULL, NULL),
(185, 'IMPORTADO', 'Motor Chevrolet Malibú MN', 'MOTOR COMPLETO', 'Chevrolet', 'Malibú MN', NULL, '2015', '273', '200922', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 19, '2025-03-19 15:06:32', '2026-02-18 20:47:52', NULL, NULL),
(186, 'IMPORTADO', 'Motor Hyundai 2.4L', 'MOTOR COMPLETO', 'Hyundai 2.4L', '2.4L', NULL, '2010', '283', '240001', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 16, '2025-03-19 15:07:35', '2026-02-18 20:47:52', NULL, NULL),
(187, 'IMPORTADO', 'Motor Chevrolet 5.3L LS5', 'MOTOR COMPLETO', 'Chevrolet', '+5.3L LS5 Electrico', NULL, '2015', '289', '220282', 'APLICA', 'DISPONIBLE', '4.000', NULL, '4.000', 11, '2025-03-19 15:09:46', '2026-02-18 20:47:52', NULL, NULL),
(188, 'IMPORTADO', 'Motor Mitsubishi 4F63', 'MOTOR COMPLETO', 'Mitsubishi', '4F63', NULL, '1995', '276', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-19 15:10:45', '2026-02-18 20:47:52', NULL, NULL),
(189, 'IMPORTADO', 'Motor 7/8 3 cilindros', 'MOTOR 7/8', '3 cilindros', 'No se conoce', NULL, '1990', '263', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-19 15:18:04', '2026-02-18 20:47:52', NULL, NULL),
(190, 'IMPORTADO', 'Motor 7/8 Toyota 2UZ 4.7L', 'MOTOR 7/8', 'Toyota', '4.7L 2UZ', NULL, '2008', '262', '220101', 'APLICA', 'DISPONIBLE', '3.000', NULL, '3.000', 10, '2025-03-19 15:19:57', '2026-02-18 20:47:52', NULL, NULL),
(191, 'IMPORTADO', 'Motor 7/8 Chevrolet steen', 'MOTOR 7/8', 'Chevrolet', 'Steen', NULL, '2000', '261', '230145', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 14, '2025-03-19 15:22:50', '2026-02-18 20:47:52', NULL, NULL),
(192, 'IMPORTADO', 'Motor 7/8 Ford Explorer 3.5L', 'MOTOR 7/8', 'FORD', 'Explorer 3.5L', NULL, '2015', '264', '200922', 'APLICA', 'DISPONIBLE', '3.000', NULL, '3.000', 19, '2025-03-19 15:24:06', '2026-02-18 20:47:52', NULL, NULL),
(195, 'IMPORTADO', 'Motor 7/8 Chevrolet 5.3L LS5', 'MOTOR 7/8', 'Chevrolet', 'Ls5', NULL, '2015', '265', '220282', 'APLICA', 'DISPONIBLE', '3.000', NULL, '3.000', 11, '2025-03-19 15:27:10', '2026-02-18 20:47:52', NULL, NULL),
(196, 'IMPORTADO', 'Motor Hyundai Santa Fe 2.7L/2.5L', 'MOTOR COMPLETO', 'Hyundai', 'Santa Fe 2.7L', NULL, '2005', '288', '240001', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 16, '2025-03-19 15:28:37', '2026-02-18 20:47:52', NULL, NULL),
(197, 'IMPORTADO', 'Motor Hyundai Santa Fe 2.7L/2.5L', 'MOTOR COMPLETO', 'Hyundai', 'Santa Fe 2.7L', NULL, '2005', '269', '220282', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 11, '2025-03-19 15:29:55', '2026-02-18 20:47:52', NULL, NULL),
(198, 'IMPORTADO', 'Motor 7/8 Chevrolet 5.3L LS5', 'MOTOR 7/8', 'Chevrolet', '5.3L LS5', NULL, '2015', '268', '220282', 'APLICA', 'DISPONIBLE', '3.000', NULL, '3.000', 11, '2025-03-19 15:32:08', '2026-02-18 20:47:52', NULL, NULL),
(199, 'IMPORTADO', 'Motor 3/4 Chevrolet 5.3L BA', 'MOTOR 3/4', 'Chevrolet', '5.3L BA', NULL, '2005', 'D0047', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-19 15:33:37', '2026-02-18 20:47:52', NULL, NULL),
(200, 'IMPORTADO', 'Motor 7/8 Jeep Grand Cherokee 5.7L 4G', 'MOTOR 7/8', 'Jeep', 'Grand Cherokee 5.7L 4G', NULL, '2012', '108', '220282', 'APLICA', 'DISPONIBLE', '2.800', NULL, '2.800', 11, '2025-03-19 15:35:29', '2026-02-18 20:47:52', NULL, NULL),
(201, 'IMPORTADO', 'Motor 7/8 Hyundai Sonata 3.3L', 'MOTOR 7/8', 'Hyundai', 'Sonata 3.3L', NULL, '2008', '257', '200922', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 19, '2025-03-19 15:36:39', '2026-02-18 20:47:52', NULL, NULL),
(202, 'IMPORTADO', 'Motor 7/8 Hyundai Sorento 3.8L', 'MOTOR 7/8', 'Hyundai', 'Sorento 3.8L', NULL, '2008', '256', '220282', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 11, '2025-03-19 15:37:39', '2026-02-18 20:47:52', NULL, NULL),
(203, 'IMPORTADO', 'Motor 7/8 Ford 200', 'MOTOR 7/8', 'Ford', '200', NULL, '1990', '259', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-19 15:38:21', '2026-02-18 20:47:52', NULL, NULL),
(204, 'IMPORTADO', 'Motor 7/8 Jeep 318 MV', 'MOTOR 7/8', 'Jeep', '318', NULL, '1990', '258', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-19 15:39:06', '2026-02-18 20:47:52', NULL, NULL),
(208, 'IMPORTADO', 'Motor Chevrolet 262 Tipo Vortec', 'MOTOR COMPLETO', 'Chevrolet', '262 Vortec', NULL, '1995', '355', '060325', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 18, '2025-03-19 15:41:55', '2026-02-18 20:47:52', NULL, NULL),
(209, 'IMPORTADO', 'Motor 7/8 Chevrolet 366', 'MOTOR 7/8', 'Chevrolet', '366', NULL, '1990', '267', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-19 15:42:44', '2026-02-18 20:47:52', NULL, NULL),
(210, 'IMPORTADO', 'Motor 7/8 Ford Explorer 3.5L', 'MOTOR 7/8', 'Ford', 'Explorer 3.5L', NULL, '2015', '255', '230145', 'APLICA', 'DISPONIBLE', '3.000', NULL, '3.000', 14, '2025-03-19 15:43:44', '2026-02-18 20:47:52', NULL, NULL),
(211, 'IMPORTADO', 'Motor 7/8 Toyota 2GR-Camry', 'MOTOR 7/8', 'Toyota', '2GR-Camry', NULL, '2005', '260', '210262', 'APLICA', 'DISPONIBLE', '2.300', NULL, '2.300', 9, '2025-03-19 15:45:19', '2026-02-18 20:47:52', NULL, NULL),
(212, 'IMPORTADO', 'Motor 7/8 Nissan Xtrail QR25', 'MOTOR 7/8', 'Nissan', 'Xtrail QR25', NULL, '2008', '253', '060325', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 18, '2025-03-19 15:47:08', '2026-02-18 20:47:52', NULL, NULL),
(213, 'IMPORTADO', 'Motor 7/8 Chevrolet 262 Vortec', 'MOTOR 7/8', 'Chevrolet', '262 Vortec', NULL, '1996', '252', '060325', 'APLICA', 'DISPONIBLE', '1.200', NULL, '1.200', 18, '2025-03-19 15:48:22', '2026-02-18 20:47:52', NULL, NULL),
(214, 'IMPORTADO', 'Motor Chevrolet 250', 'MOTOR COMPLETO', 'Chevrolet', '250', NULL, '1990', '307', '060325', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 18, '2025-03-20 14:38:57', '2026-02-18 20:47:52', NULL, NULL),
(215, 'IMPORTADO', 'Motor 7/8 Hyundai Sonata 3.3L', 'MOTOR 7/8', 'Hyundai', 'Sonata 3.3L', NULL, '2005', '245', '220329', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 12, '2025-03-20 22:12:35', '2026-02-18 20:47:52', NULL, NULL),
(216, 'IMPORTADO', 'Motor 7/8 Toyota 1NZ 1.5L', 'MOTOR 7/8', 'Toyota', '1NZ Yaris', NULL, '2008', '244', '230058', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 13, '2025-03-20 22:13:42', '2026-02-18 20:47:52', NULL, NULL),
(217, 'IMPORTADO', 'Motor 7/8 3 cilindros', 'MOTOR 7/8', 'Sin marca', '3 cilindros', NULL, '1990', '243', '060325', 'APLICA', 'DISPONIBLE', '500', NULL, '500', 18, '2025-03-20 22:14:43', '2026-02-18 20:47:52', NULL, NULL),
(218, 'IMPORTADO', 'Motor 7/8 3 cilindros', 'MOTOR 7/8', 'Sin marca', '3 cilindros', NULL, '1990', '242', '060325', 'APLICA', 'DISPONIBLE', '500', NULL, '500', 18, '2025-03-20 22:15:17', '2026-02-18 20:47:52', NULL, NULL),
(219, 'IMPORTADO', 'Motor 7/8 Honda Civic K24A1', 'MOTOR 7/8', 'Honda', 'Civic K24A1', NULL, '2005', '246', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-20 22:16:56', '2026-02-18 20:47:52', NULL, NULL),
(220, 'IMPORTADO', 'Motor 7/8 Chevrolet 350', 'MOTOR 7/8', 'Chevrolet', '350', NULL, '1990', '11', '060325', 'APLICA', 'DISPONIBLE', '1.200', NULL, '1.200', 18, '2025-03-20 22:17:34', '2026-02-18 20:47:52', NULL, NULL),
(221, 'IMPORTADO', 'Motor Ford Sin modelo', 'MOTOR COMPLETO', 'Ford', 'Ford', NULL, '1990', '248', '060325', 'APLICA', 'DISPONIBLE', '1.200', NULL, '1.200', 18, '2025-03-20 22:18:27', '2026-02-18 20:47:52', NULL, NULL),
(222, 'IMPORTADO', 'Motor 7/8 Chevrolet 262 vortec', 'MOTOR 7/8', 'Chevrolet', '262 Vortec', NULL, '1995', '249', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-20 22:19:58', '2026-02-18 20:47:52', NULL, NULL);
INSERT INTO `inventarios` (`id`, `origen`, `item`, `tipo`, `marca`, `modelo`, `serial`, `año`, `codInv`, `expediente`, `condicion`, `status`, `price`, `costo`, `price_sale`, `container_id`, `created_at`, `updated_at`, `categorie`, `cantidad`) VALUES
(223, 'IMPORTADO', 'Motor Ford 3.0L 6 cilindros', 'MOTOR COMPLETO', 'Ford', '3.0L 6 cilindros', NULL, '1990', '46', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-20 22:21:06', '2026-02-18 20:47:52', NULL, NULL),
(224, 'IMPORTADO', 'Motor 7/8 Chevrolet 5.3L Taquete Mecánico', 'MOTOR 7/8', 'Chevrolet', '5.3L Taquete Mecanico', NULL, '2005', '251', '060325', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 18, '2025-03-20 22:22:08', '2026-02-18 20:47:52', NULL, NULL),
(225, 'IMPORTADO', 'Motor Chevrolet 262 TB1', 'MOTOR COMPLETO', 'Chevrolet', '262 TB1', NULL, '1990', '279', '060325', 'APLICA', 'DISPONIBLE', '1.200', NULL, '1.200', 18, '2025-03-20 22:23:13', '2026-02-18 20:47:52', NULL, NULL),
(226, 'IMPORTADO', 'Motor Chevrolet 262 tipo TB1', 'MOTOR COMPLETO', 'Chevrolet', '262 Tipo TB1', NULL, '1990', '310', '060325', 'APLICA', 'DISPONIBLE', '1.200', NULL, '1.200', 18, '2025-03-20 22:24:01', '2026-02-18 20:47:52', NULL, NULL),
(227, 'IMPORTADO', 'Motor Toyota 7M-GE', 'MOTOR COMPLETO', 'Toyota', '7M-GE', NULL, '1990', '306', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-20 22:26:01', '2026-02-18 20:47:52', NULL, NULL),
(228, 'IMPORTADO', 'Motor 7/8 Mazda 3', 'MOTOR 7/8', 'Mazda', '3', NULL, '2000', '234', '200922', 'APLICA', 'DISPONIBLE', '800', NULL, '800', 19, '2025-03-20 23:02:40', '2026-02-18 20:47:52', NULL, NULL),
(229, 'IMPORTADO', 'Motor 7/8 Mazda 3', 'MOTOR 7/8', 'Mazda', '3', NULL, '2000', '233', '060325', 'APLICA', 'DISPONIBLE', '800', NULL, '800', 18, '2025-03-20 23:04:11', '2026-02-18 20:47:52', NULL, NULL),
(232, 'IMPORTADO', 'Motor 7/8 Mazda 3', 'MOTOR 7/8', 'Mazda', '3', NULL, '2000', '232', '200922', 'APLICA', 'DISPONIBLE', '800', NULL, '800', 19, '2025-03-20 23:07:16', '2026-02-18 20:47:52', NULL, NULL),
(233, 'IMPORTADO', 'Mitsubishi 6G75 3.5L', 'MOTOR COMPLETO', 'Mitsubishi', '6G75 3.5L', NULL, '2000', '109', '060325', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 18, '2025-03-20 23:09:25', '2026-02-18 20:47:52', NULL, NULL),
(234, 'IMPORTADO', 'Motor 7/8 Toyota 1ZZ', 'MOTOR 7/8', 'Toyota', '1ZZ Nueva Sensación', NULL, '2008', '236', '230145', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 14, '2025-03-20 23:10:21', '2026-02-18 20:47:52', NULL, NULL),
(235, 'IMPORTADO', 'Motor 7/8 Toyota 2GR-Camry', 'MOTOR 7/8', 'Toyota', '2GR-CAMRY', NULL, '2008', '237', '220329', 'APLICA', 'DISPONIBLE', '2.500', NULL, '2.500', 12, '2025-03-20 23:11:35', '2026-02-18 20:47:52', NULL, NULL),
(236, 'IMPORTADO', 'Motor Dodge Neón 2.4L', 'MOTOR COMPLETO', 'Dodge', 'Neon', NULL, '1990', '239', '230145', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 14, '2025-03-20 23:13:56', '2026-02-18 20:47:52', NULL, NULL),
(237, 'IMPORTADO', 'Motor Dodge Neón 2.4L', 'MOTOR COMPLETO', 'Dodge', 'Neon 2.4L', NULL, '2000', 'D0699', '230145', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 14, '2025-03-20 23:15:58', '2026-02-18 20:47:52', NULL, NULL),
(238, 'IMPORTADO', 'Motor Jeep Cherokee 4.0L', 'MOTOR COMPLETO', 'Jeep', 'Cherokee 4.0L', NULL, '1990', '313', '230319', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 15, '2025-03-20 23:19:02', '2026-02-18 20:47:52', NULL, NULL),
(239, 'IMPORTADO', 'Motor Jeep 258 WAGONIER', 'MOTOR COMPLETO', 'Jeep', '258', NULL, '1978', '312', '230319', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 15, '2025-03-20 23:20:28', '2026-02-18 20:47:52', NULL, NULL),
(240, 'IMPORTADO', 'Motor Jeep Cherokee 4.0L', 'MOTOR COMPLETO', 'Jeep', 'Cherokee 4.0L', NULL, '2000', '302', '230319', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 15, '2025-03-20 23:21:48', '2026-02-18 20:47:52', NULL, NULL),
(241, 'IMPORTADO', 'Motor Jeep Cherokee 4.0L MV', 'MOTOR COMPLETO', 'Jeepp', '4.0L MV', NULL, '2000', '314', '240001', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 16, '2025-03-20 23:22:37', '2026-02-18 20:47:52', NULL, NULL),
(242, 'IMPORTADO', 'Motor Toyota 4M', 'MOTOR COMPLETO', 'Toyota', '4M', NULL, '1970', 'D0306', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-20 23:24:53', '2026-02-18 20:47:52', NULL, NULL),
(243, 'IMPORTADO', 'Motor 7/8 Motor 4 cilindros', 'MOTOR 7/8', 'Sin marca', '4 cilindros', NULL, '2000', '224', '060325', 'APLICA', 'DISPONIBLE', '500', NULL, '500', 18, '2025-03-20 23:27:31', '2026-02-18 20:47:52', NULL, NULL),
(244, 'IMPORTADO', 'Motor 3/4 Nissan VQ35', 'MOTOR 3/4', 'Nissan', 'VQ35', NULL, '2000', '223', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-20 23:28:21', '2026-02-18 20:47:52', NULL, NULL),
(245, 'IMPORTADO', 'Motor 7/8 Dodge Cruise', 'MOTOR 7/8', 'Dodge', 'Cruiser', NULL, '2000', '222', '060325', 'APLICA', 'DISPONIBLE', '800', NULL, '800', 18, '2025-03-20 23:30:50', '2026-02-18 20:47:52', NULL, NULL),
(246, 'IMPORTADO', 'Motor 7/8 Volkswagen', 'MOTOR 7/8', 'Volkswagen', '4 cilindros', NULL, '2000', '225', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-20 23:33:24', '2026-02-18 20:47:52', NULL, NULL),
(247, 'IMPORTADO', 'Motor 7/8 Daewon Nubira', 'MOTOR 7/8', 'Daewoo', 'Nubira', NULL, '2000', '226', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-20 23:34:19', '2026-02-18 20:47:52', NULL, NULL),
(248, 'IMPORTADO', 'Motor 7/8 Chevrolet 262 TB1', 'MOTOR 7/8', 'Chevrolet', '262 TB1', NULL, '1992', '227', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-20 23:35:12', '2026-02-18 20:47:52', NULL, NULL),
(249, 'IMPORTADO', 'Motor 7/8 Chevrolet 5.3L ÑS5', 'MOTOR 7/8', 'Chevrolet', '5.3L LS5', NULL, '2015', '228', '220329', 'APLICA', 'DISPONIBLE', '4.000', NULL, '4.000', 12, '2025-03-20 23:36:10', '2026-02-18 20:47:52', NULL, NULL),
(250, 'IMPORTADO', 'Motor 7/8 Hyundai G4NB Elantra', 'MOTOR 7/8', 'Hyundai', 'G4NB', NULL, '2015', '231', '230058', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 13, '2025-03-20 23:36:58', '2026-02-18 20:47:52', NULL, NULL),
(251, 'IMPORTADO', 'Motor 7/8 Ford Fusion 3.0L', 'MOTOR 7/8', 'Ford', 'Fusión 3.0L', NULL, '2008', '229', '060325', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 18, '2025-03-20 23:38:16', '2026-02-18 20:47:52', NULL, NULL),
(252, 'IMPORTADO', 'Motor 7/8 Toyota 1MZ', 'MOTOR 7/8', 'Toyota', '1MZ', NULL, '2005', '230', '230058', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 13, '2025-03-20 23:39:17', '2026-02-18 20:47:52', NULL, NULL),
(253, 'IMPORTADO', 'Motor 7/8 Toyota 2GR- Camry', 'MOTOR 7/8', 'Toyota', '2GR-Camry', NULL, '2008', '218', '060325', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 18, '2025-03-20 23:40:27', '2026-02-18 20:47:52', NULL, NULL),
(254, 'IMPORTADO', 'Motor 7/8 Toyota 2ZR VVTI', 'MOTOR 7/8', 'Toyota', '2ZR VVTI', NULL, '2015', '219', '220101', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 10, '2025-03-20 23:41:42', '2026-02-18 20:47:52', NULL, NULL),
(259, 'IMPORTADO', 'Motor 7/8 Toyota 1MZ', 'MOTOR 7/8', 'Toyota', '1MZ', NULL, '2000', '220', '230058', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 13, '2025-03-20 23:50:25', '2026-02-18 20:47:52', NULL, NULL),
(260, 'IMPORTADO', 'Chevrolet Z24', 'MOTOR 7/8', 'Chevrolet', 'Z24', NULL, '1990', '221', '060325', 'APLICA', 'DISPONIBLE', '800', NULL, '800', 18, '2025-03-20 23:51:38', '2026-02-18 20:47:52', NULL, NULL),
(261, 'IMPORTADO', 'Motor Chevrolet 2.5L', 'MOTOR 7/8', 'Chevrolet', '2.5L', NULL, '1990', '211', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 00:27:32', '2026-02-18 20:47:52', NULL, NULL),
(262, 'IMPORTADO', 'Motor 7/8 Mitsubishi 4G63', 'MOTOR 7/8', 'Mitsubishi', '4G63', NULL, '2000', '210', '230058', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 13, '2025-03-21 00:28:48', '2026-02-18 20:47:52', NULL, NULL),
(263, 'IMPORTADO', 'Motor 7/8 Toyota 1MZ', 'MOTOR 7/8', 'Toyota', '1MZ', NULL, '2000', '212', '230058', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 13, '2025-03-21 00:29:43', '2026-02-18 20:47:52', NULL, NULL),
(264, 'IMPORTADO', 'Chevrolet Z24', 'MOTOR 7/8', 'Chevrolet', 'Z24', NULL, '2000', '213', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 00:30:36', '2026-02-18 20:47:52', NULL, NULL),
(265, 'IMPORTADO', 'Motor 7/8 Chevrolet 5.3L 2010', 'MOTOR 7/8', 'Chevrolet', '5.3L LS4', NULL, '2010', '214', '220329', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 12, '2025-03-21 00:31:52', '2026-02-18 20:47:52', NULL, NULL),
(266, 'IMPORTADO', 'Motor Ford 3.0L 6 cilindros', 'MOTOR COMPLETO', 'Ford', '3.0L 6 cilindros', NULL, '2000', '215', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 00:32:59', '2026-02-18 20:47:52', NULL, NULL),
(267, 'IMPORTADO', 'Motor 7/8 Honda D16Z6 civic', 'MOTOR 7/8', 'Honda', 'D16Z6 Civic', NULL, '2000', '206', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 00:34:17', '2026-02-18 20:47:52', NULL, NULL),
(268, 'IMPORTADO', 'Motor 7/8 Honda Civic D16Z6', 'MOTOR 7/8', 'Honda', 'Civic', NULL, '2000', '204', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 00:35:03', '2026-02-18 20:47:52', NULL, NULL),
(269, 'IMPORTADO', 'Motor Ford 3.0L 6 cilindros', 'MOTOR COMPLETO', 'Ford', '3.0L 6 cilindros', NULL, '1990', '203', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 00:35:47', '2026-02-18 20:47:52', NULL, NULL),
(270, 'IMPORTADO', 'Motor 7/8 Honda F23A1', 'MOTOR 7/8', 'Honda', 'F23A1', NULL, '2000', '196', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 00:36:48', '2026-02-18 20:47:52', NULL, NULL),
(272, 'IMPORTADO', 'Motor 7/8 Jeep Cherokee 3.7L KJ', 'MOTOR 7/8', 'Jeep', 'Cherokee  3.7L KJ', NULL, '2006', '195', '230145', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 14, '2025-03-21 00:38:16', '2026-02-18 20:47:52', NULL, NULL),
(273, 'IMPORTADO', 'Motor 7/8 Honda J30 A1', 'MOTOR 7/8', 'Honda', 'J30A1', NULL, '2000', '194', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 00:39:43', '2026-02-18 20:47:52', NULL, NULL),
(274, 'IMPORTADO', 'Motor 7/8 Ford Focus Zetec', 'MOTOR 7/8', 'Ford', 'Focus Zetec', NULL, '2000', '197', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 00:40:28', '2026-02-18 20:47:52', NULL, NULL),
(275, 'IMPORTADO', 'Motor 7/8 Ford Leiser', 'MOTOR 7/8', 'Ford', 'Leiser', NULL, '2000', '198', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 00:41:25', '2026-02-18 20:47:52', NULL, NULL),
(276, 'IMPORTADO', 'Motor 7/8 Ford Leiser', 'MOTOR 7/8', 'Ford', 'Leiser', NULL, '2000', '199', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 00:42:04', '2026-02-18 20:47:52', NULL, NULL),
(277, 'IMPORTADO', 'Motor Hyundai G4KC', 'MOTOR COMPLETO', 'Hyundai', 'G4KC 2.4L', NULL, '2010', '202', '220282', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 11, '2025-03-21 00:43:13', '2026-02-18 20:47:52', NULL, NULL),
(278, 'IMPORTADO', 'Motor Ford 3.0L 6 cilindros', 'MOTOR COMPLETO', 'Ford', '3.0L', NULL, '2000', '193', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 00:47:40', '2026-02-18 20:47:52', NULL, NULL),
(279, 'IMPORTADO', 'Motor 7/8 Jeep Grand Cherokee 4.7L 8 B', 'MOTOR 7/8', 'Jeep', 'Grand Cherokee 4.7L 8 B', NULL, '2008', '192', '220329', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 12, '2025-03-21 00:48:38', '2026-02-18 20:47:52', NULL, NULL),
(280, 'IMPORTADO', 'Motor 7/8 Ford Focus Zetec', 'MOTOR 7/8', 'Ford', 'Focus Zetec', NULL, '2000', '191', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 00:49:42', '2026-02-18 20:47:52', NULL, NULL),
(281, 'IMPORTADO', 'Motor 7/8 Honda J30A1', 'MOTOR 3/4', 'Honda', 'J30A1', NULL, '1990', '189', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 00:50:47', '2026-02-18 20:47:52', NULL, NULL),
(282, 'IMPORTADO', 'Motor 7/9 Ford Focus Zetec', 'MOTOR 7/8', 'Ford', 'Focus Zetec', NULL, '2000', '190', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 00:51:28', '2026-02-18 20:47:52', NULL, NULL),
(283, 'IMPORTADO', 'Motor 7/8 Honda B16A2', 'MOTOR 7/8', 'Honda', 'B16A2', NULL, '1990', '188', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 00:52:32', '2026-02-18 20:47:52', NULL, NULL),
(284, 'IMPORTADO', 'Motor 7/8 Chevrolet 5.3L LS1', 'MOTOR 7/8', 'Chevrolet', '5.3L LS1', NULL, '2005', 'D0191', '060325', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 18, '2025-03-21 00:53:31', '2026-02-18 20:47:52', NULL, NULL),
(285, 'IMPORTADO', 'Motor Ford Focus Zetec', 'MOTOR COMPLETO', 'Ford', 'Focus Zetec', NULL, '2000', '183', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 00:54:25', '2026-02-18 20:47:52', NULL, NULL),
(286, 'IMPORTADO', 'Motor Kia Kia', 'MOTOR COMPLETO', 'Kia', 'Kia', NULL, '2000', '187', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 00:55:42', '2026-02-18 20:47:52', NULL, NULL),
(287, 'IMPORTADO', 'Motor 7/8 Hyundai Elantra 2.0L', 'MOTOR 7/8', 'Hyundai', 'Elantra 2.0L', NULL, '2000', '186', '060325', 'APLICA', 'DISPONIBLE', '1.300', NULL, '1.300', 18, '2025-03-21 00:56:36', '2026-02-18 20:47:52', NULL, NULL),
(288, 'IMPORTADO', 'Motor Hyundai Caren', 'MOTOR COMPLETO', 'Hyundai', 'Caren', NULL, '2010', '185', '240222', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 20, '2025-03-21 00:57:42', '2026-02-18 20:47:52', NULL, NULL),
(289, 'IMPORTADO', 'Motor 7/8 Chevrolet Z24', 'MOTOR 7/8', 'Chevrolet', 'Z24', NULL, '1980', '184', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 00:59:41', '2026-02-18 20:47:52', NULL, NULL),
(290, 'IMPORTADO', 'Motor 7/8 Toyota 4A', 'MOTOR 7/8', 'Toyota', '4A', NULL, '1995', '241', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 01:01:13', '2026-02-18 20:47:52', NULL, NULL),
(291, 'IMPORTADO', 'Motor Nissan Almera', 'MOTOR COMPLETO', 'Nissan', 'Almera', NULL, '1990', '182', '200922', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 19, '2025-03-21 01:02:11', '2026-02-18 20:47:52', NULL, NULL),
(292, 'IMPORTADO', 'Motor 7/8 Nissan Almera', 'MOTOR 7/8', 'Nissan', 'Almera', NULL, '1990', '181', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 01:03:00', '2026-02-18 20:47:52', NULL, NULL),
(293, 'IMPORTADO', 'Motor Nissan Almera', 'MOTOR COMPLETO', 'Nissan', 'Almera', NULL, '1990', '180', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 01:03:38', '2026-02-18 20:47:52', NULL, NULL),
(294, 'IMPORTADO', 'Motor Nissan Almera', 'MOTOR COMPLETO', 'Nissan', 'Almera', NULL, '1990', '179', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 01:28:02', '2026-02-18 20:47:52', NULL, NULL),
(295, 'IMPORTADO', 'Motor Nissan Almena', 'MOTOR COMPLETO', 'Hyundai', 'Almera', NULL, '1990', '178', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 01:28:48', '2026-02-18 20:47:52', NULL, NULL),
(296, 'IMPORTADO', 'Motor Nissan Almera', 'MOTOR COMPLETO', 'Nissan', 'Almera', NULL, '1990', '177', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 01:29:39', '2026-02-18 20:47:52', NULL, NULL),
(297, 'IMPORTADO', 'Motor 7/8 Kia Sportag', 'MOTOR 7/8', 'Kia', 'Sportg', NULL, '1990', '176', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 01:31:14', '2026-02-18 20:47:52', NULL, NULL),
(298, 'IMPORTADO', 'Motor 7/8 Kia Sportag', 'MOTOR 7/8', 'Kia', 'Sportg', NULL, '1990', '175', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 01:31:58', '2026-02-18 20:47:52', NULL, NULL),
(299, 'IMPORTADO', 'Motor 7/8 Chevrolet 5.3L BA 2005', 'MOTOR 7/8', 'Chevrolet', '5.3L LS1', NULL, '2005', '174', '060325', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 18, '2025-03-21 01:36:38', '2026-02-18 20:47:52', NULL, NULL),
(300, 'IMPORTADO', 'Motor 3/4 Honda Civic', 'MOTOR 3/4', 'Honda', 'Civic', NULL, '2000', '173', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 01:37:55', '2026-02-18 20:47:52', NULL, NULL),
(301, 'IMPORTADO', 'Motor 7/8 Chevrolet 5.3L LS1', 'MOTOR 7/8', 'Chevrolet', '5.3L', NULL, '2005', '172', '060325', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 18, '2025-03-21 01:39:11', '2026-02-18 20:47:52', NULL, NULL),
(302, 'IMPORTADO', 'Motor 7/8 Mitsubishi Montero TD', 'MOTOR 7/8', 'Mitsubishi', 'Montero TD', NULL, '2000', '24', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 01:40:58', '2026-02-18 20:47:52', NULL, NULL),
(303, 'IMPORTADO', 'Motor 7/8 Volkswagen 4 cilindro', 'MOTOR 7/8', 'Volkswagen', 'S/N', NULL, '2000', '21', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 01:44:01', '2026-02-18 20:47:52', NULL, NULL),
(304, 'IMPORTADO', 'Motor 7/8 Chevrolet Cavalier Z24', 'MOTOR 7/8', 'Chevrolet', 'Z24', NULL, '1990', '19', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 01:45:55', '2026-02-18 20:47:52', NULL, NULL),
(305, 'IMPORTADO', 'Motor 7/8 Chevrolet Centry 3.1 Lumina', 'MOTOR 7/8', 'Chevrolet', 'Lumina 3.1L', NULL, '1990', '18', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 01:46:56', '2026-02-18 20:47:52', NULL, NULL),
(306, 'IMPORTADO', 'Motor 7/8 Chevrolet Cavalier', 'MOTOR 7/8', 'Chevrolet', 'Tapa lisa Cavalier', NULL, '1990', '20', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 01:47:42', '2026-02-18 20:47:52', NULL, NULL),
(307, 'IMPORTADO', 'Motor Nissan 6 cilindros', 'MOTOR COMPLETO', 'Nissan', '6 cilindros', NULL, '1990', '22', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 01:48:23', '2026-02-18 20:47:52', NULL, NULL),
(308, 'IMPORTADO', 'Motor Volkswagen Beta', 'MOTOR COMPLETO', 'Volkswagen', 'Beta', NULL, '1990', '33', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 01:49:03', '2026-02-18 20:47:52', NULL, NULL),
(309, 'IMPORTADO', 'Motor Mitsubishi Montero TD', 'MOTOR COMPLETO', 'Mitsubishi', '6G75 Montero', NULL, '2000', '23', '220101', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 10, '2025-03-21 01:50:08', '2026-02-18 20:47:52', NULL, NULL),
(310, 'IMPORTADO', 'Motor Toyota 3VZ', 'MOTOR COMPLETO', 'Toyota', '3VZ', NULL, '1992', 'D0028', '060325', 'APLICA', 'DISPONIBLE', '1.800', NULL, '1.800', 18, '2025-03-21 01:51:21', '2026-02-18 20:47:52', NULL, NULL),
(311, 'IMPORTADO', 'Motor Mercedes Benz', 'MOTOR COMPLETO', 'Mercedes', 'Benz', NULL, '1992', '28', '060325', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 18, '2025-03-21 01:51:58', '2026-02-18 20:47:52', NULL, NULL),
(312, 'IMPORTADO', 'Motor 7/8 Challengers 5.7L', 'MOTOR 7/8', 'Jeep', 'Challengers', NULL, '2000', '26', '060325', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 18, '2025-03-21 01:53:17', '2026-02-18 20:47:52', NULL, NULL),
(313, 'IMPORTADO', 'Motor 7/8 Chevrolet 5.3L Ls2', 'MOTOR 7/8', 'Chevrolet', '5.3L Ls2', NULL, '2008', '156', '060325', 'APLICA', 'DISPONIBLE', '1.800', NULL, '1.800', 18, '2025-03-21 01:54:34', '2026-02-18 20:47:52', NULL, NULL),
(314, 'IMPORTADO', 'Motor 7/8 Chevrolet 262 TB1', 'MOTOR 7/8', 'Chevrolet', '262 TB1', NULL, '1992', '154', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 01:55:23', '2026-02-18 20:47:52', NULL, NULL),
(315, 'IMPORTADO', 'Motor 7/8 Mitsubishi 4G61 Lancer', 'MOTOR 7/8', 'Mitsubishi', 'Lancer 4G61', NULL, '2000', '153', '230058', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 13, '2025-03-21 01:56:27', '2026-02-18 20:47:52', NULL, NULL),
(316, 'IMPORTADO', 'Motor 7/8 Toyota 2GR -Camry', 'MOTOR 7/8', 'Toyota', '2GR-Camry', NULL, '2008', '364', '230058', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 13, '2025-03-21 01:57:30', '2026-02-18 20:47:52', NULL, NULL),
(317, 'IMPORTADO', 'Motor 7/8 Toyota 2AZ 2.4L', 'MOTOR 7/8', 'Toyota', '2AZ 2.4L', NULL, '2006', '152', '060325', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 18, '2025-03-21 01:58:21', '2026-02-18 20:47:52', NULL, NULL),
(320, 'IMPORTADO', 'Motor 7/8 Hyundai Sonata 3.3L', 'MOTOR 7/8', 'Hyundai', 'Sonata 3.3', NULL, '2008', '145', '200922', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 19, '2025-03-21 02:02:02', '2026-02-18 20:47:52', NULL, NULL),
(321, 'IMPORTADO', 'Motor 7/8 Toyota 1ZZ', 'MOTOR 7/8', 'Toyota', '1ZZ', NULL, '2010', '158', '230319', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 15, '2025-03-21 02:03:17', '2026-02-18 20:47:52', NULL, NULL),
(322, 'IMPORTADO', 'Motor 7/8 Ford Explorer 3.5L', 'MOTOR 7/8', 'Ford', '3.5L', NULL, '2012', '162', '220329', 'APLICA', 'DISPONIBLE', '3.000', NULL, '3.000', 12, '2025-03-21 02:04:13', '2026-02-18 20:47:52', NULL, NULL),
(323, 'IMPORTADO', 'Motor 7/8 Chevrolet 350MV', 'MOTOR 7/8', 'Chevrolet', '350 MV', NULL, '1970', '161', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 02:05:58', '2026-02-18 20:47:52', NULL, NULL),
(324, 'IMPORTADO', 'Motor 7/8 Toyota 5.7L 3UR', 'MOTOR 7/8', 'Toyota', 'Tundra 5.7L', NULL, '2015', '159', '200922', 'APLICA', 'DISPONIBLE', '4.000', NULL, '4.000', 19, '2025-03-21 02:07:53', '2026-02-18 20:47:52', NULL, NULL),
(326, 'IMPORTADO', 'Motor 7/8 Toyota 2RZ', 'MOTOR 7/8', 'Toyota', '2RZ', NULL, '1992', '144', '230058', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 13, '2025-03-21 02:11:36', '2026-02-18 20:47:52', NULL, NULL),
(327, 'IMPORTADO', 'Motor 7/8 Hyundai Sorento/Sonata 6GDB', 'MOTOR 7/8', 'Hyundai', 'Sonata 3.3L', NULL, '2008', '143', '200922', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 19, '2025-03-21 02:13:02', '2026-02-18 20:47:52', NULL, NULL),
(328, 'IMPORTADO', 'Motor 7/8 Ford 3.5L Explorer', 'MOTOR 7/8', 'Ford', 'Explorer 3.5L', NULL, '2012', '147', '220329', 'APLICA', 'DISPONIBLE', '3.000', NULL, '3.000', 12, '2025-03-21 02:14:03', '2026-02-18 20:47:52', NULL, NULL),
(329, 'IMPORTADO', 'Motor Chevrolet Centry 3.1L', 'MOTOR COMPLETO', 'Chevrolet', 'Century', NULL, '1990', '146', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 02:15:00', '2026-02-18 20:47:52', NULL, NULL),
(330, 'IMPORTADO', 'Motor 7/8 Toyota Tundra 3UT', 'MOTOR 7/8', 'Toyota', '3UR', NULL, '2015', '362', '200922', 'APLICA', 'DISPONIBLE', '4.000', NULL, '4.000', 19, '2025-03-21 02:15:58', '2026-02-18 20:47:52', NULL, NULL),
(331, 'IMPORTADO', 'Motor 7/8 Toyota 2AZ', 'MOTOR 7/8', 'Toyota', '2AZ', NULL, '2008', '142', '210262', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 9, '2025-03-21 02:17:15', '2026-02-18 20:47:52', NULL, NULL),
(332, 'IMPORTADO', 'Motor 7/8 Mitsubishi 4G63', 'MOTOR 7/8', 'Mitsubishi', '4G63', NULL, '1990', '52', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 02:18:24', '2026-02-18 20:47:52', NULL, NULL),
(333, 'IMPORTADO', 'Motor 7/8 Caribe Suzuki 4XCL', 'MOTOR 7/8', 'Suzuki', 'Caribe Suzuki 4XCL', NULL, '1990', '135', '060325', 'APLICA', 'DISPONIBLE', '800', NULL, '800', 18, '2025-03-21 02:19:41', '2026-02-18 20:47:52', NULL, NULL),
(334, 'IMPORTADO', 'Motor 7/8 Vitara 1.6LV', 'MOTOR 7/8', 'Chevrolet', 'Vitara 1.6L MV', NULL, '1990', '134', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 02:20:39', '2026-02-18 20:47:52', NULL, NULL),
(335, 'IMPORTADO', 'Motor 7/8 Hyundai Accel 1.6L', 'MOTOR 7/8', 'Hyundai', 'Accel 1.6L', NULL, '2008', '133', '240001', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 16, '2025-03-21 02:22:13', '2026-02-18 20:47:52', NULL, NULL),
(336, 'IMPORTADO', 'Motor 7/8 Dodge Caliber 2.0L', 'MOTOR 7/8', 'Dodge', 'Caliber', NULL, '2008', 'D0141', '060325', 'APLICA', 'DISPONIBLE', '1.200', NULL, '1.200', 18, '2025-03-21 02:25:15', '2026-02-18 20:47:52', NULL, NULL),
(337, 'IMPORTADO', 'Motor 7/8 Mitsubishi 4G63', 'MOTOR 7/8', 'Mitsubishi', '4G63', NULL, '2000', '131', '060325', 'APLICA', 'DISPONIBLE', '1.200', NULL, '1.200', 18, '2025-03-21 02:26:09', '2026-02-18 20:47:52', NULL, NULL),
(338, 'IMPORTADO', 'Motor 7/8 Mitsubishi 4G64', 'MOTOR 7/8', 'Mitsubishi', '4G64', NULL, '1970', '376', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 02:27:26', '2026-02-18 20:47:52', NULL, NULL),
(339, 'IMPORTADO', 'Motor 7/9 Ford Explorer 4 Cadena', 'MOTOR 7/8', 'Ford', 'Explorer 4 cadena', NULL, '2005', '137', '060325', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 18, '2025-03-21 02:28:56', '2026-02-18 20:47:52', NULL, NULL),
(340, 'IMPORTADO', 'Motor 7/8 Jeep Grand Cherokee 4.7L EGR 8 B', 'MOTOR 7/8', 'Jeep', 'Grand Cherokee 4.7L 9 B', NULL, '2007', '330', '220282', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 11, '2025-03-21 02:30:19', '2026-02-18 20:47:52', NULL, NULL),
(341, 'IMPORTADO', 'Motor 7/8 Ford 302', 'MOTOR 7/8', 'Ford', '302', NULL, '1970', '140', '060325', 'APLICA', 'DISPONIBLE', '800', NULL, '800', 18, '2025-03-21 02:31:03', '2026-02-18 20:47:52', NULL, NULL),
(342, 'IMPORTADO', 'Motor 7/8 Ford Explorer 4.0L 4 cadenas', 'MOTOR 7/8', 'Ford', 'Explorer 4.0L', NULL, '2006', '139', '060325', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 18, '2025-03-21 02:33:22', '2026-02-18 20:47:52', NULL, NULL),
(343, 'IMPORTADO', 'Motor 7/8 Chevrolet Rey Camión 2010', 'MOTOR 7/8', 'Chevrolet', 'Rey camion 6.0L', NULL, '2010', '141', '060325', 'NO APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 02:34:11', '2026-02-18 20:47:52', NULL, NULL),
(344, 'IMPORTADO', 'Motor 7/8 Volkswagen', 'MOTOR 7/8', 'Volkswagen', 'Sin modelo', NULL, '2000', 'D0762', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 14:12:19', '2026-02-18 20:47:52', NULL, NULL),
(345, 'IMPORTADO', 'Motor 7/8 Toyota 5E', 'MOTOR 7/8', 'Toyota', '5E', NULL, '2000', '372', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 14:13:26', '2026-02-18 20:47:52', NULL, NULL),
(346, 'IMPORTADO', 'Motor Ford Fusión 3.0L', 'MOTOR COMPLETO', 'Ford', 'Fisio 3.0L', NULL, '2010', '373', '060325', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 18, '2025-03-21 14:14:55', '2026-02-18 20:47:52', NULL, NULL),
(347, 'IMPORTADO', 'Motor 7/8 Kia Espectra', 'MOTOR 7/8', 'Kia', 'Espectra', NULL, '2000', '375', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 14:17:06', '2026-02-18 20:47:52', NULL, NULL),
(348, 'IMPORTADO', 'Motor 3/4 Jeep Cherokee 3.7L Kk', 'MOTOR 3/4', 'Jeep', 'Cherokee 3.7L KK', NULL, '2006', 'D0166', '200922', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 19, '2025-03-21 14:19:30', '2026-02-18 20:47:52', NULL, NULL),
(349, 'IMPORTADO', 'Motor 7/8 Toyota 2.0L Camrry', 'MOTOR 7/8', 'Toyota', 'Camry 2.0L', NULL, '2000', '377', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 14:20:47', '2026-02-18 20:47:52', NULL, NULL),
(350, 'IMPORTADO', 'Motor 7/8 Toyota 2.0L Camry', 'MOTOR 7/8', 'Toyota', 'Camry 2.0L', NULL, '2000', '378', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 14:23:14', '2026-02-18 20:47:52', NULL, NULL),
(351, 'IMPORTADO', 'Motor 7/8 Toyota 2.0L Camry', 'MOTOR 7/8', 'Toyota', 'Camry 2.0L', NULL, '2000', '379', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 14:24:23', '2026-02-18 20:47:52', NULL, NULL),
(353, 'IMPORTADO', 'Motor 7/8 Chevrolet Impala 3.8L', 'MOTOR 7/8', 'Chevrolet', 'Impala 3.8L', NULL, '2000', '309', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 14:25:26', '2026-02-18 20:47:52', NULL, NULL),
(354, 'IMPORTADO', 'Motor 7/8 Cavalier 2.2L', 'MOTOR 7/8', 'Chevrolet', 'Cavalier 2.2L', NULL, '2000', '381', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 14:26:35', '2026-02-18 20:47:52', NULL, NULL),
(355, 'IMPORTADO', 'Motor 7/8 Chevrolet Cavalier 2.2L tapa rallada', 'MOTOR 7/8', 'Chevrolet', 'Cavalier 2.2L', NULL, '2000', '382', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 14:29:54', '2026-02-18 20:47:52', NULL, NULL),
(356, 'IMPORTADO', 'Motor 7/8 Nissan Almera', 'MOTOR 7/8', 'Nissan', 'Almera', NULL, '1990', '383', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 14:31:01', '2026-02-18 20:47:52', NULL, NULL),
(357, 'IMPORTADO', 'Motor 7/8 Nissan VQ35', 'MOTOR 7/8', 'Nissan', 'VQ35', NULL, '2000', '384', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 14:32:09', '2026-02-18 20:47:52', NULL, NULL),
(358, 'IMPORTADO', 'Motor 7/8 Toyota 2AZ', 'MOTOR 7/8', 'Toyota', '2AZ', NULL, '2008', '386', '060325', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 18, '2025-03-21 14:32:53', '2026-02-18 20:47:52', NULL, NULL),
(359, 'IMPORTADO', 'Motor 3/4 Dodge 360', 'MOTOR 7/8', 'Dodge', '360', NULL, '1990', '385', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 14:33:36', '2026-02-18 20:47:52', NULL, NULL),
(360, 'IMPORTADO', 'Motor 7/8 Dodge 3.8L Levaron', 'MOTOR 7/8', 'Dodge', 'Levaron 3.8L', NULL, '2000', '389', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 14:34:48', '2026-02-18 20:47:52', NULL, NULL),
(361, 'IMPORTADO', 'Ford Mustang', 'MOTOR 7/8', 'Ford', 'Mustang 3.8L', NULL, '2000', '387', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 14:35:32', '2026-02-18 20:47:52', NULL, NULL),
(362, 'IMPORTADO', 'Motor 7/8 Ford 300', 'MOTOR 7/8', 'Ford', '300', NULL, '1970', '388', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 14:36:21', '2026-02-18 20:47:52', NULL, NULL),
(363, 'IMPORTADO', 'Motor 7/8 Toyota 2UZ', 'MOTOR 7/8', 'Toyota', '2UZ', NULL, '2000', '390', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 14:39:29', '2026-02-18 20:47:52', NULL, NULL),
(364, 'IMPORTADO', 'Motor 7/8 Toyota  1NZ-YARIS', 'MOTOR 7/8', 'Toyota', 'Yaris 1NZ', NULL, '2010', '59', '060325', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 18, '2025-03-21 17:41:06', '2026-02-18 20:47:52', NULL, NULL),
(365, 'IMPORTADO', 'Motor 7/8 Toyota 1NZ Yaris', 'MOTOR 7/8', 'Toyota', '1NZ Yaris', NULL, '2010', '60', '060325', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 18, '2025-03-21 17:42:00', '2026-02-18 20:47:52', NULL, NULL),
(366, 'IMPORTADO', 'Motor 7/8 Toyota 1NZ Yaris', 'MOTOR 7/8', 'Totota', '1NX Yaris', NULL, '2010', '61', '060325', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 18, '2025-03-21 17:42:58', '2026-02-18 20:47:52', NULL, NULL),
(367, 'IMPORTADO', 'Motor 7/8 Toyota 1.6L 4A', 'MOTOR 7/8', 'Toyota', '4A 1.6L', NULL, '1992', '62', '060325', 'APLICA', 'DISPONIBLE', '1.200', NULL, '1.200', 18, '2025-03-21 17:44:02', '2026-02-18 20:47:52', NULL, NULL),
(368, 'IMPORTADO', 'Motor 7/8 Toyota 2AZ', 'MOTOR 7/8', 'Toyota', '2AZ Previa', NULL, '2010', '63', '210262', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 9, '2025-03-21 17:46:09', '2026-02-18 20:47:52', NULL, NULL),
(369, 'IMPORTADO', 'Motor 7/8 Mitsubishi Montero TD', 'MOTOR 7/8', 'Mitsubishi', 'Montero TD', NULL, '2006', '64', '210262', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 9, '2025-03-21 17:47:28', '2026-02-18 20:47:52', NULL, NULL),
(370, 'IMPORTADO', 'Motor 7/8 Toyota 2AZ', 'MOTOR 7/8', 'Toyota', '2AZ Previa', NULL, '2008', '65', '210262', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 9, '2025-03-21 17:48:47', '2026-02-18 20:47:52', NULL, NULL),
(371, 'IMPORTADO', 'Motor 7/8 Jeep 4.7L 8B EGR', 'MOTOR 7/8', 'Jeep', '4.7L 8 B EGR', NULL, '2008', '167', '220282', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 11, '2025-03-21 17:50:18', '2026-02-18 20:47:52', NULL, NULL),
(372, 'IMPORTADO', 'Motor 7/8 Toyota 1ZZ Nueva Sensación', 'MOTOR 7/8', 'Toyota', '1ZZ Nueva Sensación', NULL, '2010', '165', '220282', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 11, '2025-03-21 17:51:08', '2026-02-18 20:47:52', NULL, NULL),
(373, 'IMPORTADO', 'Motor 3/4 Chevrolet Van Exprés', 'MOTOR 3/4', 'Chevrolet', '4.3L van Exprés', NULL, '2005', '68', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 17:52:18', '2026-02-18 20:47:52', NULL, NULL),
(374, 'IMPORTADO', 'Motor Toyota 2ZR VVTI', 'MOTOR COMPLETO', 'Toyota', '2AZ Previa', NULL, '2008', 'D0504', '230058', 'APLICA', 'DISPONIBLE', '2.500', NULL, '2.500', 13, '2025-03-21 17:53:14', '2026-02-18 20:47:52', NULL, NULL),
(375, 'IMPORTADO', 'Motor 7/8 Toyota 4A', 'MOTOR 3/4', 'Toyota', '4A 1.6L', NULL, '1992', '1', '060325', 'APLICA', 'DISPONIBLE', '1.200', NULL, '1.200', 18, '2025-03-21 17:54:28', '2026-02-18 20:47:52', NULL, NULL),
(376, 'IMPORTADO', 'Motor 7/8 Ford Fiesta Titanium', 'MOTOR 7/8', 'Ford', 'Fiesta Titanium', NULL, '2015', '164', '060325', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 18, '2025-03-21 17:55:38', '2026-02-18 20:47:52', NULL, NULL),
(377, 'IMPORTADO', 'Motor 7/8 Toyota 1MZ', 'MOTOR 7/8', 'Toyota', '1MZ', NULL, '1990', '44', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 17:56:26', '2026-02-18 20:47:52', NULL, NULL),
(378, 'IMPORTADO', 'Motor 7/8 Hyundai G4NB', 'MOTOR 7/8', 'Hyundai', 'G4NB', NULL, '2015', '71', '230058', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 13, '2025-03-21 17:57:25', '2026-02-18 20:47:52', NULL, NULL),
(379, 'IMPORTADO', 'Motor 7/8 Jeep 258', 'MOTOR 7/8', 'Jeep', '258', NULL, '1970', '9', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 17:58:45', '2026-02-18 20:47:52', NULL, NULL),
(380, 'IMPORTADO', 'Motor 7/8 Ford 4.6L  2VBA', 'MOTOR 7/8', 'Ford', 'Explorer 4.6L 2V BA', NULL, '2005', '72', '240001', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 16, '2025-03-21 17:59:53', '2026-02-18 20:47:52', NULL, NULL),
(381, 'IMPORTADO', 'Motor 7/8 3 cilindros', 'MOTOR 7/8', 'Sin marca', '3 cilindros', NULL, '1990', '74', '060325', 'APLICA', 'DISPONIBLE', '500', NULL, '500', 18, '2025-03-21 20:25:34', '2026-02-18 20:47:52', NULL, NULL),
(382, 'IMPORTADO', 'Motor 7/8 Chevrolet Vitara J18', 'MOTOR 7/8', 'Chevrolet', 'J18', NULL, '2000', '75', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 20:26:16', '2026-02-18 20:47:52', NULL, NULL),
(383, 'IMPORTADO', 'Motor 7/8 Toyota 1NZ Yaris', 'MOTOR 7/8', 'Toyota', 'Yaris 1NZ', NULL, '2008', '76', '060325', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 18, '2025-03-21 20:27:05', '2026-02-18 20:47:52', NULL, NULL),
(384, 'IMPORTADO', 'Motor 7/8 Mitsubishi 4 Cilindros', 'MOTOR 7/8', 'Mitsubishi', '4G93', NULL, '2000', '30', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 20:27:51', '2026-02-18 20:47:52', NULL, NULL),
(385, 'IMPORTADO', 'Motor Jeep Cherokee 3.7L KJ', 'MOTOR COMPLETO', 'Jeep', 'Cherokee 3.7L KJ', NULL, '2005', '78', '240001', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 16, '2025-03-21 20:29:03', '2026-02-18 20:47:52', NULL, NULL),
(386, 'IMPORTADO', 'Motor 3/4 Nissan 4 cilindros', 'MOTOR 3/4', 'Nissn', '4 cilindro', NULL, '1990', '79', '060325', 'APLICA', 'DISPONIBLE', '800', NULL, '800', 18, '2025-03-21 20:31:47', '2026-02-18 20:47:52', NULL, NULL),
(387, 'IMPORTADO', 'Motor Toyota 2GR-Camry', 'MOTOR COMPLETO', 'Toyota', '2GR-CAMRY', NULL, '2008', '38', '060325', 'APLICA', 'DISPONIBLE', '2.500', NULL, '2.500', 18, '2025-03-21 20:32:47', '2026-02-18 20:47:52', NULL, NULL),
(388, 'IMPORTADO', 'Motor 7/8 Ford Escape TP', 'MOTOR 7/8', 'Chevrolet', 'Escape 3.0L TP', NULL, '2002', '4', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 20:34:05', '2026-02-18 20:47:52', NULL, NULL),
(389, 'IMPORTADO', 'Motor 7/8 Ford Escape 3.0L TP', 'MOTOR 7/8', 'Ford', 'Escape 3.0L', NULL, '2002', '3', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 20:35:09', '2026-02-18 20:47:52', NULL, NULL),
(390, 'IMPORTADO', 'Motor 7/8 Nissan VQ35', 'MOTOR 7/8', 'Nissa', 'VQ35', NULL, '2005', '282', '200922', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 19, '2025-03-21 21:05:17', '2026-02-18 20:47:52', NULL, NULL),
(391, 'IMPORTADO', 'Motor 7/8 Kia', 'MOTOR 7/8', 'Kia', 'Sin modelo', NULL, '1990', '14', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 21:06:31', '2026-02-18 20:47:52', NULL, NULL),
(392, 'IMPORTADO', 'Motor 3/4 Jeep Magnum', 'MOTOR 3/4', 'Jeep', 'Mangum', NULL, '1990', '53', '060325', 'APLICA', 'DISPONIBLE', '700', NULL, '700', 18, '2025-03-21 21:07:16', '2026-02-18 20:47:52', NULL, NULL),
(393, 'IMPORTADO', 'Motor 7/8 Kia Espectra', 'MOTOR 7/8', 'Kia', 'Espectra', NULL, '2000', '58', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 21:07:59', '2026-02-18 20:47:52', NULL, NULL),
(394, 'IMPORTADO', 'Motor 7/8 Nissan Armada Bk56', 'MOTOR 7/8', 'Nissan', 'Armada', NULL, '2000', '86', '060325', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 18, '2025-03-21 21:08:46', '2026-02-18 20:47:52', NULL, NULL),
(395, 'IMPORTADO', 'Motor 3/4 Toyota 2UZ VVTi', 'MOTOR 3/4', 'Toyota', '2UZ VVTi', NULL, '2000', '327', '240001', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 16, '2025-03-21 21:09:31', '2026-02-18 20:47:52', NULL, NULL),
(396, 'IMPORTADO', 'Motor 7/8 Kia G4CS', 'MOTOR 7/8', 'Kia', 'G4CS', NULL, '200', '87', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 21:10:56', '2026-02-18 20:47:52', NULL, NULL),
(398, 'IMPORTADO', 'Motor 7/8 Toyota 2.0L Camry', 'MOTOR 7/8', 'Toyota', '2.0L Camry', NULL, '1990', '88', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 21:12:08', '2026-02-18 20:47:52', NULL, NULL),
(399, 'IMPORTADO', 'Motor 7/8 Caribe', 'MOTOR 7/8', 'Caribe', '4XE1', NULL, '1990', '89', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 22:28:06', '2026-02-18 20:47:52', NULL, NULL),
(400, 'IMPORTADO', 'Motor 7/8 Ford Explorer 4.0L cadena', 'MOTOR 7/8', 'Ford', 'Explorer 4 Cadena', NULL, '1998', '90', '060325', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 18, '2025-03-21 22:29:01', '2026-02-18 20:47:52', NULL, NULL),
(401, 'IMPORTADO', 'Motor 7/8 Mitsubishi Montero TD', 'MOTOR 7/8', 'Mitsubishi', 'Montero TD', NULL, '1990', '91', '060325', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 18, '2025-03-21 22:29:48', '2026-02-18 20:47:52', NULL, NULL),
(402, 'IMPORTADO', 'Motor 7/8 Toyota 2ZR 1 VVTi', 'MOTOR 7/8', 'Toyota', '2ZR', NULL, '2010', '92', '060325', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 18, '2025-03-21 22:30:44', '2026-02-18 20:47:52', NULL, NULL),
(403, 'IMPORTADO', 'Motor 7/8 Ford 4 cilindros', 'MOTOR 7/8', 'Ford', '4 Cilindro VCT', NULL, '1990', '32', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 22:31:43', '2026-02-18 20:47:52', NULL, NULL),
(404, 'IMPORTADO', 'Motor 7/8 Toyota 1ZZ Nueva Sensación', 'MOTOR 7/8', 'Toyota', '1ZZ', NULL, '2010', '674', '240222', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 20, '2025-03-21 22:33:37', '2026-02-18 20:47:52', NULL, NULL),
(405, 'IMPORTADO', 'Motor 7/8 Toyota Camry 2GR', 'MOTOR 7/8', 'Toyota', '2GR- Camry', NULL, '2008', '94', '210262', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 9, '2025-03-21 22:34:38', '2026-02-18 20:47:52', NULL, NULL),
(406, 'IMPORTADO', 'Motor 7/8 Chevrolet 350 Vortec', 'MOTOR 7/8', 'Chevrolet', '350 vortec', NULL, '1990', '95', '060325', 'APLICA', 'DISPONIBLE', '1.200', NULL, '1.200', 18, '2025-03-21 22:35:28', '2026-02-18 20:47:52', NULL, NULL),
(407, 'IMPORTADO', 'Motor Chevrolet 262 TB1', 'MOTOR COMPLETO', 'Chevrolet', '262 tipo TB1', NULL, '1992', '96', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 22:38:14', '2026-02-18 20:47:52', NULL, NULL),
(408, 'IMPORTADO', 'Motor 7/8 Honda K24Z6', 'MOTOR 7/8', 'Honda', 'K24Z6', NULL, '2000', '97', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 22:39:37', '2026-02-18 20:47:52', NULL, NULL),
(412, 'IMPORTADO', 'Motor 3/4 Chevrolet 5.3L TM 2008', 'MOTOR 3/4', 'Chevrolet', '5.3L 2008', NULL, '2008', '15', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 22:45:01', '2026-02-18 20:47:52', NULL, NULL),
(413, 'IMPORTADO', 'Motor 7/8 Toyota Corolla 4A 1.6L', 'MOTOR 7/8', 'Toyota', 'Corolla 4A', NULL, '1992', '36', '060325', 'APLICA', 'DISPONIBLE', '1.200', NULL, '1.200', 18, '2025-03-21 22:45:56', '2026-02-18 20:47:52', NULL, NULL),
(414, 'IMPORTADO', 'Motor 7/8 Toyota Camry 2.0L', 'MOTOR 7/8', 'Toyota', 'Camry 2.0L', NULL, '1992', '99', '060325', 'APLICA', 'DISPONIBLE', '1.200', NULL, '1.200', 18, '2025-03-21 22:46:59', '2026-02-18 20:47:52', NULL, NULL),
(415, 'IMPORTADO', 'Jeep 4.7L 8 B', 'MOTOR 3/4', 'Jeep', 'CHEROKEE 4.7L 8B', NULL, '2008', '35S/C', '240222', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 20, '2025-03-21 23:30:30', '2026-02-18 20:47:52', NULL, NULL),
(418, 'IMPORTADO', 'Toyota 2AZ Previa', 'MOTOR 7/8', 'Toyota', '2AZ', NULL, '2008', '34S/C', '060325', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 18, '2025-03-21 23:35:00', '2026-02-18 20:47:52', NULL, NULL),
(419, 'IMPORTADO', 'Motor 7/8 Volkswagen', 'MOTOR 7/8', 'Volkswagen', 'S/N', NULL, '2000', '102', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 23:47:00', '2026-02-18 20:47:52', NULL, NULL),
(420, 'IMPORTADO', 'Motor 7/8 Dodge Neon', 'MOTOR 7/8', 'Dodge', 'Neón 2.0L', NULL, '1990', '103', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 23:47:53', '2026-02-18 20:47:52', NULL, NULL),
(421, 'IMPORTADO', 'Motor 7/8 Toyota 2RZ', 'MOTOR 7/8', 'Toyota', '2RZ', NULL, '1992', '104', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 23:48:33', '2026-02-18 20:47:52', NULL, NULL),
(422, 'IMPORTADO', 'Motor 7/8 Dodge Cruise', 'MOTOR 7/8', 'Dodge', 'Cruiser', NULL, '1990', '168', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 23:49:50', '2026-02-18 20:47:52', NULL, NULL),
(423, 'IMPORTADO', 'Motor Chevrolet Century 2.8L', 'MOTOR COMPLETO', 'Chevrolet', 'Century 2.8L', NULL, '1990', '106', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 23:50:39', '2026-02-18 20:47:52', NULL, NULL),
(424, 'IMPORTADO', 'Motor 7/8 Ford 4 Cilindro', 'MOTOR 7/8', 'Ford', '4 Cilindro', NULL, '1990', '209', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 23:52:50', '2026-02-18 20:47:52', NULL, NULL),
(425, 'IMPORTADO', 'Motor 7/8 Jeep 5.7L Challengers', 'MOTOR 7/8', 'Jeep', 'Challengers', NULL, '2000', '42', '060325', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 18, '2025-03-21 23:53:39', '2026-02-18 20:47:52', NULL, NULL),
(426, 'IMPORTADO', 'Motor Honda 6 Cilindros', 'MOTOR COMPLETO', 'Honda', '6 cilindros', NULL, '2000', '34', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 23:54:23', '2026-02-18 20:47:52', NULL, NULL),
(427, 'IMPORTADO', 'Motor Chevrolet Lumina 3.1L', 'MOTOR COMPLETO', 'Chevrolet', 'Lumina 3.1L', NULL, '1990', '40', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 23:55:25', '2026-02-18 20:47:52', NULL, NULL),
(428, 'IMPORTADO', 'Motor 7/8 Nissan Armada BK56', 'MOTOR 7/8', 'Nissan', 'Armada BK56', NULL, '2000', '101', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 23:56:16', '2026-02-18 20:47:52', NULL, NULL),
(429, 'IMPORTADO', 'Motor 7/8 Ford Focus Zetec', 'MOTOR 7/8', 'Ford', 'Focus Zetec', NULL, '2000', '51', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-21 23:57:23', '2026-02-18 20:47:52', NULL, NULL),
(430, 'IMPORTADO', 'Motor 7/8 Toyota 1NZ Yaris', 'MOTOR 7/8', 'Toyota', '1NZ Yaris', NULL, '2009', '114', '240001', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 16, '2025-03-21 23:59:31', '2026-02-18 20:47:52', NULL, NULL),
(431, 'IMPORTADO', 'Motor 7/8 Chevrolet 366', 'MOTOR 7/8', 'Chevrolet', '366', NULL, '1990', '57', '060325', 'APLICA', 'DISPONIBLE', '800', NULL, '800', 18, '2025-03-22 00:00:17', '2026-02-18 20:47:52', NULL, NULL),
(433, 'IMPORTADO', 'Motor 7/8 Jeep 5.7 L 4G', 'MOTOR 7/8', 'Jeep', '5.7L 4G', NULL, '2012', '36S/C', '060325', 'APLICA', 'DISPONIBLE', '2.800', NULL, '2.800', 18, '2025-03-22 00:01:51', '2026-02-18 20:47:52', NULL, NULL),
(434, 'IMPORTADO', 'Motor Hyundai Accel 1.6L', 'MOTOR COMPLETO', 'Hyundai', 'Accel, Río, Getz G4ED', NULL, '2008', '116', '220282', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 11, '2025-03-22 00:02:56', '2026-02-18 20:47:52', NULL, NULL),
(435, 'IMPORTADO', 'Motor 7/8 Dodge Neon', 'MOTOR 7/8', 'Dodge', 'Neon', NULL, '1990', '117', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-22 00:04:41', '2026-02-18 20:47:52', NULL, NULL),
(436, 'IMPORTADO', 'Motor 7/8 Toyota 2AZ', 'MOTOR 7/8', 'Toyota', '2AZ', NULL, '2008', '41', '060325', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 18, '2025-03-22 00:05:19', '2026-02-18 20:47:52', NULL, NULL),
(437, 'IMPORTADO', 'Motor 7/8 Toyota 2RZ', 'MOTOR 7/8', 'Toyota', '2RZ', NULL, '1992', '354', '240222', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 20, '2025-03-22 00:06:09', '2026-02-18 20:47:52', NULL, NULL),
(438, 'IMPORTADO', 'Motor 7/8 Honda F22', 'MOTOR 7/8', 'Honda', 'F22', NULL, '1990', '118', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-22 00:06:52', '2026-02-18 20:47:52', NULL, NULL),
(439, 'IMPORTADO', 'Motor 7/8 Mitsubishi 6G73', 'MOTOR 7/8', 'Mitsubishi', '6G73', NULL, '1990', '119', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-22 00:08:07', '2026-02-18 20:47:52', NULL, NULL),
(440, 'IMPORTADO', 'Motor Chevrolet Cavalier 2.2L', 'MOTOR COMPLETO', 'Chevrolet', 'Cavalier 2.2L', NULL, '1990', '380', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-22 00:09:19', '2026-02-18 20:47:52', NULL, NULL),
(441, 'IMPORTADO', 'Motor 3/4 Toyota 22R', 'MOTOR 3/4', 'Toyota', '22R', NULL, '1992', '130', '240222', 'APLICA', 'DISPONIBLE', '800', NULL, '800', 20, '2025-03-22 00:10:42', '2026-02-18 20:47:52', NULL, NULL),
(442, 'IMPORTADO', 'Motor 7/8 Ford Explorer 4 cadena', 'MOTOR 7/8', 'Ford', 'Explorer 4.0L', NULL, '2000', '120', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-22 00:11:35', '2026-02-18 20:47:52', NULL, NULL),
(443, 'IMPORTADO', 'Motor Fiat', 'MOTOR COMPLETO', 'Fiat', 'Sin modelo', NULL, '1990', '121', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-22 00:12:14', '2026-02-18 20:47:52', NULL, NULL),
(444, 'IMPORTADO', 'Motor 7/8 Chevrolet 262 TB1', 'MOTOR 7/8', 'Chevrolet', '262 TB1', NULL, '1992', '122', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-22 00:13:14', '2026-02-18 20:47:52', NULL, NULL),
(445, 'IMPORTADO', 'Motor 7/8 Ford 200', 'MOTOR 7/8', 'Ford', '200', NULL, '1990', '123', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-22 00:13:53', '2026-02-18 20:47:52', NULL, NULL),
(446, 'IMPORTADO', 'Motor 7/8 Diesel Mitsubishi 2.2L', 'MOTOR COMPLETO', 'Mitsubishi', '2.2L', NULL, '2000', '124', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-22 00:15:19', '2026-02-18 20:47:52', NULL, NULL),
(447, 'IMPORTADO', 'Motor Ford 3.8L', 'MOTOR 7/8', 'Ford', '3.8L', NULL, '1990', '125', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-22 00:16:49', '2026-02-18 20:47:52', NULL, NULL),
(448, 'IMPORTADO', 'Motor 7/8 Chevrolet 262 TB1', 'MOTOR 7/8', 'Chevrolet', '262 TB1', NULL, '1992', '126', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-22 00:17:32', '2026-02-18 20:47:52', NULL, NULL),
(451, 'IMPORTADO', 'Motor 7/8 Toyota 2ZR', 'MOTOR 7/8', 'Toyota', '2ZR', NULL, '2016', '37S/C', '060325', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 18, '2025-03-22 00:19:29', '2026-02-18 20:47:52', NULL, NULL),
(452, 'IMPORTADO', 'Motor 7/8 Chevrolet 262 TB1', 'MOTOR 7/8', 'Chevrolet', '262 TB1', NULL, '2000', '127', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-22 00:20:25', '2026-02-18 20:47:52', NULL, NULL),
(454, 'IMPORTADO', 'Motor 7/8 Chévete con caja', 'MOTOR 7/8', 'Chevrolet', 'Chévete', NULL, '2000', '38S/C', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-22 00:22:24', '2026-02-18 20:47:52', NULL, NULL),
(456, 'IMPORTADO', 'Motor 7/8 Chevrolet 5.3 L TM 2005', 'MOTOR 7/8', 'Chevrolet', '5.3L  TM', NULL, '2005', '39S/C', '240222', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 20, '2025-03-22 02:28:16', '2026-02-18 20:47:52', NULL, NULL),
(457, 'IMPORTADO', 'Motor 7/8 Chevrolet 5.3L 2005', 'MOTOR 7/8', 'Chevrolet', '5.3L', NULL, '2005', '40S/C', '240222', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 20, '2025-03-22 02:30:43', '2026-02-18 20:47:52', NULL, NULL),
(459, 'IMPORTADO', 'Motor 3/4 Ford Tritón 5.4L 2V', 'MOTOR 3/4', 'Ford', '5.4L 2v', NULL, '2005', '41S/C', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-22 02:46:46', '2026-02-18 20:47:52', NULL, NULL),
(460, 'IMPORTADO', 'Motor 7/8 Toyota 2GR-CAMRY', 'MOTOR 7/8', 'Toyota', '2GR-Camry', NULL, '2008', '217', '060325', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 18, '2025-03-22 02:58:23', '2026-02-18 20:47:52', NULL, NULL),
(461, 'IMPORTADO', 'Motor 7/8 Honda Civic D16', 'MOTOR 7/8', 'Honda', 'Civic D16Z6', NULL, '2000', '205', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-22 03:07:35', '2026-02-18 20:47:52', NULL, NULL),
(463, 'IMPORTADO', 'Motor 7/8 Chevrolet 5.3L 2002', 'MOTOR 7/8', 'Chevrolet', '5.3L TM', NULL, '2005', '42S/C', '060325', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 18, '2025-03-22 03:09:31', '2026-02-18 20:47:52', NULL, NULL),
(464, 'IMPORTADO', 'Toyota 2AZ', 'MOTOR 3/4', 'Toyota', '2AZ', NULL, '2008', '43s/c', '240222', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 20, '2025-03-22 03:17:06', '2026-02-18 20:47:52', NULL, NULL),
(466, 'IMPORTADO', 'Motor 7/8 Chevrolet 262 Vortec', 'MOTOR 7/8', 'Chevrolet', '262 vortec', NULL, '1995', '44S/C', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-22 03:19:15', '2026-02-18 20:47:52', NULL, NULL),
(468, 'IMPORTADO', 'Motor 7/8 Mitsubishi 16V', 'MOTOR 7/8', 'Mitsubishi', '16 valvulas', NULL, '1990', '45S/C', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-22 03:21:53', '2026-02-18 20:47:52', NULL, NULL),
(469, 'IMPORTADO', 'Motor Chevrolet Captiva 3.6', 'MOTOR 7/8', 'Chevrolet', 'Captiva 3.6', NULL, '2008', 'DJ-008M', '230145', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 14, '2025-03-22 03:27:52', '2026-02-18 20:47:52', NULL, NULL),
(470, 'IMPORTADO', 'Motor Jeep Rubicon 3.6L', 'MOTOR COMPLETO', 'Jeep', 'Rubicon 3.6L', NULL, '2008', '368', '230145', 'APLICA', 'DISPONIBLE', '4.000', NULL, '4.000', 14, '2025-03-22 03:29:03', '2026-02-18 20:47:52', NULL, NULL);
INSERT INTO `inventarios` (`id`, `origen`, `item`, `tipo`, `marca`, `modelo`, `serial`, `año`, `codInv`, `expediente`, `condicion`, `status`, `price`, `costo`, `price_sale`, `container_id`, `created_at`, `updated_at`, `categorie`, `cantidad`) VALUES
(471, 'IMPORTADO', 'Motor Toyota 5VZ', 'MOTOR COMPLETO', 'Toyota', '5VZ', NULL, '2000', '367', '230145', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 14, '2025-03-22 03:30:08', '2026-02-18 20:47:52', NULL, NULL),
(472, 'IMPORTADO', 'Motor Chevrolet 3.8L', 'MOTOR COMPLETO', 'Chevrolet', '3.8L IMPALA', NULL, '1990', '361', '060325', 'APLICA', 'DISPONIBLE', '800', NULL, '800', 18, '2025-03-22 03:31:05', '2026-02-18 20:47:52', NULL, NULL),
(473, 'IMPORTADO', 'Motor 5/8 Kia 3.5L', 'MOTOR 5/8', 'Kia', '3.5L', NULL, '2009', '170', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-22 03:32:00', '2026-02-18 20:47:52', NULL, NULL),
(474, 'IMPORTADO', 'Motor 7/8 Chevrolet 6.0l', 'MOTOR 7/8', 'Chevrolet', '6.0L Rey Camion', NULL, '2010', '80', '240222', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 20, '2025-03-22 03:33:40', '2026-02-18 20:47:52', NULL, NULL),
(475, 'IMPORTADO', 'Motor Jeep 4.7L 8 Bujias', 'MOTOR COMPLETO', 'Jeep', '4.7L 8 B', NULL, '2007', '315', '240222', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 20, '2025-03-22 03:34:34', '2026-02-18 20:47:52', NULL, NULL),
(476, 'IMPORTADO', 'Motor 7/8 Toyota 2UZ', 'MOTOR COMPLETO', 'Toyota', '2UZ', NULL, '2000', '342', '060325', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 18, '2025-03-22 03:35:36', '2026-02-18 20:47:52', NULL, NULL),
(477, 'IMPORTADO', 'Motor 7/8 Toyota 2A', 'MOTOR 7/8', 'Toyota', '2AZ Previa', NULL, '2008', '46S/C', '240222', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 20, '2025-03-22 04:17:50', '2026-02-18 20:47:52', NULL, NULL),
(478, 'IMPORTADO', 'Motor 7/8 Chevrolet 8.1L', 'MOTOR 7/8', 'Chevrolet', '8.1L', NULL, '1990', '55', '060325', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 18, '2025-03-22 04:21:25', '2026-02-18 20:47:52', NULL, NULL),
(479, 'IMPORTADO', 'Motor 7/8 Toyota 1ZZ nueva sensación', 'MOTOR 7/8', 'TOYOTA', '1ZZ', NULL, '2008', '671', '240222', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.550', 20, '2025-03-28 00:11:02', '2026-02-18 20:47:52', NULL, NULL),
(480, 'IMPORTADO', 'Toyota IZZ nueva sensación', 'MOTOR 7/8', 'TOYOTA', 'IZZ', NULL, '2008', '670', '240222', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.550', 20, '2025-03-28 00:14:16', '2026-02-18 20:47:52', NULL, NULL),
(481, 'IMPORTADO', 'Motor 7/8 Nissan MR20', 'MOTOR 7/8', 'Nissan', 'MR20', NULL, '2007', '673', '240222', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.500', 20, '2025-03-28 00:20:22', '2026-02-18 20:47:52', NULL, NULL),
(482, 'IMPORTADO', 'Motor 7/8 Nissan MR20', 'MOTOR 7/8', 'NISSAN', 'MR20', NULL, '2002', '672', '240222', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.500', 20, '2025-03-28 00:21:25', '2026-02-18 20:47:52', NULL, NULL),
(483, 'IMPORTADO', 'Motor 7/8 chevrolet Orlando', 'MOTOR 7/8', 'Chevrolet', 'Orlando', NULL, '2010', '348', '240222', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.500', 20, '2025-03-28 00:23:01', '2026-02-18 20:47:52', NULL, NULL),
(484, 'IMPORTADO', 'MOTOR 7/8 CHEVROLET ORLANDO 2.4', 'MOTOR 7/8', 'Chevrolet', 'Orlando 2.4', NULL, '2010', '47 S/C', '240222', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 20, '2025-03-28 00:34:55', '2026-02-18 20:47:52', NULL, NULL),
(485, 'IMPORTADO', 'MOTOR 7/8 TOYOTA 1NZ', 'MOTOR 7/8', 'TOYOTA', '1NZ', NULL, '2008', '85', '060325', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 18, '2025-03-28 00:36:56', '2026-02-18 20:47:52', NULL, NULL),
(486, 'IMPORTADO', 'MOTOR 7/8 TOYOTA INZ YARIS', 'MOTOR 7/8', 'TOYOTA', 'INZ', NULL, '2008', '630', '240222', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 20, '2025-03-28 00:38:19', '2026-02-18 20:47:52', NULL, NULL),
(487, 'IMPORTADO', 'MOTOR 7/8 TOYOTA 1NZ YARIS', 'MOTOR 7/8', 'TOYOTA', 'INZ YARIS', NULL, '2008', '347', '240222', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 20, '2025-03-28 00:39:18', '2026-02-18 20:47:52', NULL, NULL),
(488, 'IMPORTADO', 'MOTOR 7/8 HONDA D17A1', 'MOTOR 7/8', 'HONDA', 'CIVIC D17A1', NULL, '2005', '629', '240222', 'APLICA', 'DISPONIBLE', '1.200', NULL, '1.200', 20, '2025-03-28 00:40:19', '2026-02-18 20:47:52', NULL, NULL),
(489, 'IMPORTADO', 'MOTOR 7/8 HONDA D16Y7', 'MOTOR 7/8', 'HONDA', 'CIVIC D16Y7', NULL, '2002', '627', '240222', 'APLICA', 'DISPONIBLE', '1.200', NULL, '1.200', 20, '2025-03-28 00:41:38', '2026-02-18 20:47:52', NULL, NULL),
(490, 'IMPORTADO', 'MOTOR 7/8 DODGE RAM 5.7', 'MOTOR 7/8', 'DODGE', 'RAM 5.7', NULL, '2008', '49', '240222', 'APLICA', 'DISPONIBLE', '1.900', NULL, '1.900', 20, '2025-03-28 00:42:46', '2026-02-18 20:47:52', NULL, NULL),
(491, 'IMPORTADO', 'MOTOR 7/8 JEEP 5.7 4G', 'MOTOR 7/8', 'JEEP', 'GRAND CHEROKEE 5.7 4G', NULL, '2012', 'D0786', '240001', 'APLICA', 'DISPONIBLE', '2.800', NULL, '2.800', 16, '2025-03-28 00:44:14', '2026-02-18 20:47:52', NULL, NULL),
(492, 'IMPORTADO', 'MOTOR 7/8 JEEP 5.7 4G', 'MOTOR 7/8', 'JEEP', 'GRAND CHEROKEE 5.7 4G', NULL, '2012', '343', '240001', 'APLICA', 'DISPONIBLE', '2.800', NULL, '2.800', 16, '2025-03-28 00:45:20', '2026-02-18 20:47:52', NULL, NULL),
(493, 'IMPORTADO', 'MOTOR 7/8 TOYOTA 2ZR', 'MOTOR 7/8', 'TOYOTA', 'COROLLA 2ZR', NULL, '2016', '39', '220101', 'APLICA', 'DISPONIBLE', '2.500', NULL, '2.500', 10, '2025-03-28 00:46:24', '2026-02-18 20:47:52', NULL, NULL),
(494, 'IMPORTADO', 'MOTOR 7/8 TOYOTA 2ZR', 'MOTOR 7/8', 'TOYOTA', 'COROLLA 2ZR', NULL, '2016', 'D0828', '240001', 'APLICA', 'DISPONIBLE', '2.500', NULL, '2.500', 16, '2025-03-28 00:47:26', '2026-02-18 20:47:52', NULL, NULL),
(495, 'IMPORTADO', 'MOTOR 7/8 TOYOTA 2AR', 'MOTOR 7/8', 'TOYOTA', 'COROLLA 2AR', NULL, '2018', '345', '240222', 'APLICA', 'DISPONIBLE', '3.000', NULL, '3.000', 20, '2025-03-28 00:48:18', '2026-02-18 20:47:52', NULL, NULL),
(496, 'IMPORTADO', 'MOTOR 7/8 CHEVROLET 5.3', 'MOTOR 7/8', 'CHEVROLET', 'SILVERADO LS 5.3', NULL, '2010', '643', '240222', 'APLICA', 'DISPONIBLE', '2.200', NULL, '2.200', 20, '2025-03-28 00:50:48', '2026-02-18 20:47:52', NULL, NULL),
(497, 'IMPORTADO', 'MOTOR 7/8 CHEVROLET 5.3 LS', 'MOTOR 7/8', 'CHEVROLET', 'SILVERADO 5.3 LS', NULL, '2010', '616', '240222', 'APLICA', 'DISPONIBLE', '2.200', NULL, '2.200', 20, '2025-03-28 00:55:29', '2026-02-18 20:47:52', NULL, NULL),
(498, 'IMPORTADO', 'CHEVROLET SILVERADO 5.3 LS', 'MOTOR 7/8', 'CHEVROLET', 'SILVERADO 5.3 LS', NULL, '2008', '648', '240222', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 20, '2025-03-28 00:56:29', '2026-02-18 20:47:52', NULL, NULL),
(499, 'IMPORTADO', 'MOTOR 7/8 CHEVROLET 5.3 LS', 'MOTOR 7/8', 'CHEVROLET', 'SILVERADO 5.3 LS', NULL, '2008', '617', '240222', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 20, '2025-03-28 00:58:10', '2026-02-18 20:47:52', NULL, NULL),
(500, 'IMPORTADO', 'CHEVROLET SILVERADO 5.3 LS', 'MOTOR 7/8', 'CHEVROLET', 'SILVERADO 5.3 LS', NULL, '2010', '646', '240222', 'APLICA', 'DISPONIBLE', '2.200', NULL, '2.200', 20, '2025-03-28 00:59:28', '2026-02-18 20:47:52', NULL, NULL),
(501, 'IMPORTADO', 'MOTOR 7/8 FORD EXPLORER 3.5', 'MOTOR 7/8', 'FORD', 'EXPLORER 3.5', NULL, '2012', '580', '240222', 'APLICA', 'DISPONIBLE', '2.800', NULL, '2.800', 20, '2025-03-28 01:03:11', '2026-02-18 20:47:52', NULL, NULL),
(502, 'IMPORTADO', 'MOTOR 7/8 TOYOTA 2ZR', 'MOTOR 7/8', 'TOYOTA', '2ZR', NULL, '2005', '70', '220101', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 10, '2025-03-28 01:06:20', '2026-02-18 20:47:52', NULL, NULL),
(503, 'IMPORTADO', 'MOTOR 7/8 TOYOTA 2ZR', 'MOTOR 7/8', 'TOYOTA', '2ZR', NULL, '2005', '69', '220101', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 10, '2025-03-28 01:07:49', '2026-02-18 20:47:52', NULL, NULL),
(504, 'IMPORTADO', 'MOTOR 7/8 JEEP 5.7 4G', 'MOTOR 7/8', 'JEEP', 'GRAND CHEROKEE 5.7 4G', NULL, '2012', '600', '240222', 'APLICA', 'DISPONIBLE', '2.800', NULL, '2.800', 20, '2025-03-28 01:12:07', '2026-02-18 20:47:52', NULL, NULL),
(505, 'IMPORTADO', 'MOTOR 7/8 JEEP 5.7 4G', 'MOTOR 7/8', 'JEEP', 'GRAND CHEROKEE 5.7 4G', NULL, '2012', '601', '240222', 'APLICA', 'DISPONIBLE', '2.800', NULL, '2.800', 20, '2025-03-28 01:12:55', '2026-02-18 20:47:52', NULL, NULL),
(506, 'IMPORTADO', 'MOTOR 7/8 JEEP 5.7 4G', 'MOTOR 7/8', 'JEEP', 'GRAND CHEROKEE 5.7 4G', NULL, '2012', '602', '240222', 'APLICA', 'DISPONIBLE', '2.800', NULL, '2.800', 20, '2025-03-28 01:13:34', '2026-02-18 20:47:52', NULL, NULL),
(507, 'IMPORTADO', 'MOTOR 7/8 JEEP 5.7 4G', 'MOTOR 7/8', 'JEEP', 'GRAND CHEROKEE 5.7 4G', NULL, '2012', '693', '240222', 'APLICA', 'DISPONIBLE', '2.800', NULL, '2.800', 20, '2025-03-28 01:14:04', '2026-02-18 20:47:52', NULL, NULL),
(508, 'IMPORTADO', 'MOTOR 7/8 JEEP 5.7 4G', 'MOTOR 7/8', 'JEEP', 'GRAND CHEROKEE 5.7 4G', NULL, '2012', '334', '240001', 'APLICA', 'DISPONIBLE', '2.800', NULL, '2.800', 16, '2025-03-28 01:14:58', '2026-02-18 20:47:52', NULL, NULL),
(509, 'IMPORTADO', 'MOTOR 7/8 JEEP 5.7 4G', 'MOTOR 7/8', 'JEEP', 'GRAND CHEROKEE 5.7 4G', NULL, '2012', '344', '240001', 'APLICA', 'DISPONIBLE', '2.800', NULL, '2.800', 16, '2025-03-28 01:16:04', '2026-02-18 20:47:52', NULL, NULL),
(510, 'IMPORTADO', 'MOTOR HONDA K24 D17A2', 'MOTOR 7/8', 'HONDA', 'K24 D17A2', NULL, '2005', '631', '240222', 'APLICA', 'DISPONIBLE', '1.200', NULL, '1.200', 20, '2025-03-28 01:25:34', '2026-02-18 20:47:52', NULL, NULL),
(511, 'IMPORTADO', 'MOTOR 7/8 CHEVROLET ORLANDO 2.4', 'MOTOR 7/8', 'CHEVROLET', 'ORLANDO 2.4', NULL, '2010', 'D0835', '240001', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 16, '2025-03-28 01:27:03', '2026-02-18 20:47:52', NULL, NULL),
(512, 'IMPORTADO', 'MOTOR FORD EXPLORER 3.5', 'MOTOR 7/8', 'FORD', 'EXPLORER 3.5', NULL, '2012', '581', '240222', 'APLICA', 'DISPONIBLE', '2.800', NULL, '2.800', 20, '2025-03-28 01:29:24', '2026-02-18 20:47:52', NULL, NULL),
(513, 'IMPORTADO', 'MOTOR 7/8 TOYOTA 2UZ', 'MOTOR 7/8', 'TOYOTA', 'TUNDRA 2UZ', NULL, '2008', '650', '240222', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 20, '2025-03-28 01:32:02', '2026-02-18 20:47:52', NULL, NULL),
(514, 'IMPORTADO', 'MOTOR 7/8 CHEVROLET 5.3 LS', 'MOTOR 7/8', 'CHEVROLET', '5.3 LS', NULL, '2010', '644', '240222', 'APLICA', 'DISPONIBLE', '2.200', NULL, '2.200', 20, '2025-03-28 01:32:59', '2026-02-18 20:47:52', NULL, NULL),
(515, 'IMPORTADO', 'MOTOR 7/8 CHEVROLET 5.3 LS', 'MOTOR 7/8', 'CHEVROLET', 'SILVERADO 5.3 LS', NULL, '2008', '647', '240222', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 20, '2025-03-28 01:33:53', '2026-02-18 20:47:52', NULL, NULL),
(516, 'IMPORTADO', 'MOTOR 7/8 TOYOTA 2UZ', 'MOTOR 7/8', 'TOYOTA', 'TUNDRA 2UZ', NULL, '2008', '661', '240222', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 20, '2025-03-28 01:34:47', '2026-02-18 20:47:52', NULL, NULL),
(517, 'IMPORTADO', 'MOTOR 7/8 CHEVROLET VITARA', 'MOTOR 3/4', 'CHEVROLET', 'VITARA', NULL, '2006', '48/SC', '240222', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 20, '2025-03-28 01:37:48', '2026-02-18 20:47:52', NULL, NULL),
(518, 'IMPORTADO', 'MOTOR 7/8 TOYOTA 2UZ', 'MOTOR 7/8', 'TOYOTA', 'TUNDRA 2UZ', NULL, '2008', '662', '240222', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 20, '2025-03-28 01:38:41', '2026-02-18 20:47:52', NULL, NULL),
(519, 'IMPORTADO', 'MOTOR 7/8 HONDA K24 D17A2', 'MOTOR 7/8', 'HONDA', 'K24 D17A2', NULL, '2006', '632', '240222', 'APLICA', 'DISPONIBLE', '1.200', NULL, '1.200', 20, '2025-03-28 01:39:51', '2026-02-18 20:47:52', NULL, NULL),
(520, 'IMPORTADO', 'MOTOR 7/8 FORD COYOTE 5.0', 'MOTOR 7/8', 'FORD', 'COYOTE 5.0', NULL, '2015', '322', '240001', 'APLICA', 'DISPONIBLE', '3.000', NULL, '3.000', 16, '2025-03-28 01:41:39', '2026-02-18 20:47:52', NULL, NULL),
(521, 'IMPORTADO', 'MOTOR 7/8 FORD COYOTE 5.0', 'MOTOR 7/8', 'FORD', 'COYOTE 5.0', NULL, '2015', '318', '240222', 'APLICA', 'DISPONIBLE', '3.000', NULL, '3.000', 20, '2025-03-28 01:42:39', '2026-02-18 20:47:52', NULL, NULL),
(522, 'IMPORTADO', 'MOTOR 7/8 FORD COYOTE 5.0', 'MOTOR 7/8', 'FORD', 'COYOTE 5.0', NULL, '2015', '285', '240222', 'APLICA', 'DISPONIBLE', '3.000', NULL, '3.000', 20, '2025-03-28 01:44:03', '2026-02-18 20:47:52', NULL, NULL),
(523, 'IMPORTADO', 'CHEVROLET SILVERADO 5.3', 'MOTOR 7/8', 'CHEVROLET', 'SILVERADO 5.3', NULL, '2008', '49 S/C', '240222', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 20, '2025-03-28 01:48:30', '2026-02-18 20:47:52', NULL, NULL),
(525, 'IMPORTADO', 'Motor 7/8 Nissan Pathafinder KA24', 'MOTOR 7/8', 'Nissan', 'Pathfinder', NULL, '2010', '489-1', '250090', 'APLICA', 'DISPONIBLE', '2.300', NULL, '2.300', 21, '2025-03-29 20:57:07', '2026-02-18 20:47:52', NULL, NULL),
(526, 'IMPORTADO', 'Motor 7/8 Chevrolet Vitara J20', 'MOTOR 7/8', 'Chevrolet', 'Vitara J20', NULL, '2008', '490-2', '250090', 'APLICA', 'DISPONIBLE', '2.200', NULL, '2.200', 21, '2025-03-29 20:58:18', '2026-02-18 20:47:52', NULL, NULL),
(527, 'IMPORTADO', 'Motor 7/8 Hyundai Tucson 2.0L', 'MOTOR 7/8', 'Hyundai', 'Tucson 2.0L', NULL, '2009', '491-3', '250090', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 21, '2025-03-29 20:59:18', '2026-02-18 20:47:52', NULL, NULL),
(528, 'IMPORTADO', 'Motor 7/8 Hyundai Tucson 2.0L', 'MOTOR 7/8', 'Hyundai', 'Tucson 2.0L', NULL, '2008', '492-4', '250090', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 21, '2025-03-29 20:59:56', '2026-02-18 20:47:52', NULL, NULL),
(529, 'IMPORTADO', 'Motor 7/8 Hyundai Santa Fe VVTi', 'MOTOR 7/8', 'Hyundai', 'Santa Fe VVTI', NULL, '2009', '493-5', '250090', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 21, '2025-03-29 21:01:17', '2026-02-18 20:47:52', NULL, NULL),
(530, 'IMPORTADO', 'Motor Toyota 3RZ', 'MOTOR COMPLETO', 'Toyota', '3RZ Hallix', NULL, '2009', '494-6', '250090', 'APLICA', 'DISPONIBLE', '2.500', NULL, '2.500', 21, '2025-03-29 21:03:13', '2026-02-18 20:47:52', NULL, NULL),
(531, 'IMPORTADO', 'Motor 7/8 Toyota 1GR-FE', 'MOTOR 7/8', 'Toyota', '1GR-FE', NULL, '2008', '495-7', '250090', 'APLICA', 'DISPONIBLE', '4.600', NULL, '4.600', 21, '2025-03-29 21:06:43', '2026-02-18 20:47:52', NULL, NULL),
(532, 'IMPORTADO', 'Motor 7/8 Toyota 1GR-FE', 'MOTOR 7/8', 'Toyota', '1GR-FE', NULL, '2008', '496-8', '250090', 'APLICA', 'DISPONIBLE', '4.600', NULL, '4.600', 21, '2025-03-29 21:08:14', '2026-02-18 20:47:52', NULL, NULL),
(533, 'IMPORTADO', 'Motor 7/8 Nissan QR25', 'MOTOR 7/8', 'Nissan', 'QR25', NULL, '2008', '497-9', '250090', 'APLICA', 'DISPONIBLE', '1.800', NULL, '1.800', 21, '2025-03-29 21:09:52', '2026-02-18 20:47:52', NULL, NULL),
(534, 'IMPORTADO', 'Motor 7/8 Ford Tritón 5.4L 2V', 'MOTOR 7/8', 'Ford', 'Tritón 5.4L 2V', NULL, '2008', '498-10', '250090', 'APLICA', 'DISPONIBLE', '1.800', NULL, '1.800', 21, '2025-03-29 21:12:26', '2026-02-18 20:47:52', NULL, NULL),
(535, 'IMPORTADO', 'Motor 7/8 Ford Tritón 5.4L 2V', 'MOTOR 7/8', 'Ford', 'Tritón 5.4L 2V', NULL, '2008', '499-11', '250090', 'APLICA', 'DISPONIBLE', '1.800', NULL, '1.800', 21, '2025-03-29 21:13:34', '2026-02-18 20:47:52', NULL, NULL),
(536, 'IMPORTADO', 'Motor 7/8 Ford Tritón 5.4L 2V', 'MOTOR 7/8', 'Ford', 'Tritón 5.4L 2v', NULL, '2008', '500-12', '250090', 'APLICA', 'DISPONIBLE', '1.800', NULL, '1.800', 21, '2025-03-29 21:14:28', '2026-02-18 20:47:52', NULL, NULL),
(537, 'IMPORTADO', 'Motor 7/8 Ford Tritón 5.4L 2V', 'MOTOR 7/8', 'Ford', 'Tritón 5.4L 2v', NULL, '2008', '501-13', '250090', 'APLICA', 'DISPONIBLE', '1.800', NULL, '1.800', 21, '2025-03-29 21:15:20', '2026-02-18 20:47:52', NULL, NULL),
(538, 'IMPORTADO', 'Motor 7/8 Jeep Grand Cherokee 5.7L 4G', 'MOTOR 7/8', 'Jeep', 'Grand Cherokee 5.7L 4G', NULL, '2012', '502-14', '250090', 'APLICA', 'DISPONIBLE', '2.800', NULL, '2.800', 21, '2025-03-29 21:16:50', '2026-02-18 20:47:52', NULL, NULL),
(539, 'IMPORTADO', 'Motor 7/8 Jeep Grand Cherokee 5.7L 4G', 'MOTOR 7/8', 'Jeep', 'Grand Cherokee 5.7L 4G', NULL, '2012', '503-15', '250090', 'APLICA', 'DISPONIBLE', '2.800', NULL, '2.800', 21, '2025-03-29 21:17:37', '2026-02-18 20:47:52', NULL, NULL),
(540, 'IMPORTADO', 'Motor 7/8 Chevrolet 5.3L 2010', 'MOTOR 7/8', 'Chevrolet', '5.3L', NULL, '2010', '504-16', '250090', 'APLICA', 'DISPONIBLE', '2.300', NULL, '2.300', 21, '2025-03-29 21:21:58', '2026-02-18 20:47:52', NULL, NULL),
(541, 'IMPORTADO', 'Motor 7/8 Chevrolet 5.3L 2008', 'MOTOR 7/8', 'Chevrolet', '5.3L', NULL, '2008', '505-17', '250090', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 21, '2025-03-29 21:22:52', '2026-02-18 20:47:52', NULL, NULL),
(542, 'IMPORTADO', 'Motor 7/8 Jeep 4.7L 8B EGR', 'MOTOR 7/8', 'Chevrolet', '5.3L 2008', NULL, '2008', '506-17', '250090', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 21, '2025-03-29 21:24:05', '2026-02-18 20:47:52', NULL, NULL),
(543, 'IMPORTADO', 'Motor 7/8 Jeep 4.7L 8B EGR', 'MOTOR 7/8', 'Jeep', 'Grand Cherokee 4.7L 8B', NULL, '2008', '507-19', '250090', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 21, '2025-03-29 23:26:22', '2026-02-18 20:47:52', NULL, NULL),
(544, 'IMPORTADO', 'Motor 7/8 Jeep 4.7L 8B EGR', 'MOTOR 7/8', 'Jeep', 'Grand Cherokee 4.7L 8B EGR', NULL, '2008', '508-20', '250090', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 21, '2025-03-29 23:27:51', '2026-02-18 20:47:52', NULL, NULL),
(545, 'IMPORTADO', 'Motor 7/8 Jeep 4.7L 8B EGR', 'MOTOR 7/8', 'Jeep', 'Jeep Grand Cherokee 4.7L 8B EGR', NULL, '2008', '509-21', '250090', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 21, '2025-03-29 23:29:40', '2026-02-18 20:47:52', NULL, NULL),
(546, 'IMPORTADO', 'Motor 7/8 Jeep 4.7L 8B EGR', 'MOTOR 7/8', 'Jeep', 'Jeep Grand Cherokee 4.7L 8B EGR', NULL, '2008', '510-22', '250090', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 21, '2025-03-29 23:30:38', '2026-02-18 20:47:52', NULL, NULL),
(547, 'IMPORTADO', 'Motor 7/8 Ford FX4 5.4L 3V', 'MOTOR 7/8', 'Ford', 'FX4 5.4L 3V', NULL, '2007', '511-23', '250090', 'APLICA', 'DISPONIBLE', '1.900', NULL, '1.900', 21, '2025-03-30 00:15:08', '2026-02-18 20:47:52', NULL, NULL),
(548, 'IMPORTADO', 'Motor 7/8 Ford FX4 5.4L 3V', 'MOTOR 7/8', 'Ford', 'Fx4 5.4L 3V', NULL, '2008', '512-24', '250090', 'APLICA', 'DISPONIBLE', '1.900', NULL, '1.900', 21, '2025-03-30 00:16:56', '2026-02-18 20:47:52', NULL, NULL),
(549, 'IMPORTADO', 'Motor 7/8 Chevrolet Orlando 2.4L', 'MOTOR 7/8', 'Chevrolet', 'Orlando 2.4L', NULL, '2010', '513-25', '250090', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 21, '2025-03-30 00:18:28', '2026-02-18 20:47:52', NULL, NULL),
(550, 'IMPORTADO', 'Motor 7/8 Toyota 2TR', 'MOTOR 7/8', 'Toyota', '2TR', NULL, '2015', '514-26', '250090', 'APLICA', 'DISPONIBLE', '4.000', NULL, '4.000', 21, '2025-03-30 00:19:41', '2026-02-18 20:47:52', NULL, NULL),
(551, 'IMPORTADO', 'Motor 7/8 Toyota 2TR VVTi dual', 'MOTOR 7/8', 'Toyota', '2TR VVTI Dual', NULL, '2008', '515-27', '250090', 'APLICA', 'DISPONIBLE', '4.000', NULL, '4.000', 21, '2025-03-30 00:21:24', '2026-02-18 20:47:52', NULL, NULL),
(552, 'IMPORTADO', 'Motor 7/8 Chevrolet Rey Camión 6.0L', 'MOTOR 7/8', 'Chevrolet', 'Rey Camión 6.0L', NULL, '2010', '516-28', '250090', 'APLICA', 'DISPONIBLE', '2.500', NULL, '2.500', 21, '2025-03-30 00:23:15', '2026-02-18 20:47:52', NULL, NULL),
(553, 'IMPORTADO', 'Motor 7/8 Chevrolet Rey Camión 2010', 'MOTOR 7/8', 'Chevrolet', 'Rey Camión 6.0L', NULL, '2010', '517-29', '250090', 'APLICA', 'DISPONIBLE', '2.500', NULL, '2.500', 21, '2025-03-30 00:24:23', '2026-02-18 20:47:52', NULL, NULL),
(554, 'IMPORTADO', 'Motor 7/8 Ford Explorer 3.5L', 'MOTOR 7/8', 'Ford', 'Ford Explorer 3.5L', NULL, '2015', '518-30', '250090', 'APLICA', 'DISPONIBLE', '3.000', NULL, '3.000', 21, '2025-03-30 00:25:43', '2026-02-18 20:47:52', NULL, NULL),
(555, 'IMPORTADO', 'Motor 7/8 Ford Explorer 3.5L', 'MOTOR 7/8', 'Ford', 'Explorer 3.5L', NULL, '2015', '519-31', '250090', 'APLICA', 'DISPONIBLE', '3.000', NULL, '3.000', 21, '2025-03-30 00:26:44', '2026-02-18 20:47:52', NULL, NULL),
(556, 'IMPORTADO', 'Motor 7/8 Ford Explorer 3.5L', 'MOTOR 7/8', 'Ford', 'Explorer 3.5L', NULL, '2015', '520-32', '250090', 'APLICA', 'DISPONIBLE', '3.000', NULL, '3.000', 21, '2025-03-30 00:27:50', '2026-02-18 20:47:52', NULL, NULL),
(557, 'IMPORTADO', 'Motor 7/8 Ford Explorer 3.5L', 'MOTOR 7/8', 'Ford', 'Ford Explorer 3.5L', NULL, '2015', '521-33', '250090', 'APLICA', 'DISPONIBLE', '3.000', NULL, '3.000', 21, '2025-03-30 00:28:47', '2026-02-18 20:47:52', NULL, NULL),
(558, 'IMPORTADO', 'Motor 7/8 Ford EcoSport 2.0L', 'MOTOR 7/8', 'Ford', 'EcoSport 2.0L', NULL, '2008', '522-34', '250090', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 21, '2025-03-30 00:30:49', '2026-02-18 20:47:52', NULL, NULL),
(559, 'IMPORTADO', 'Motor 7/8 Ford EcoSport 2.0L', 'MOTOR 7/8', 'Ford', 'EcoSport 2.0L', NULL, '2008', '523-35', '250090', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 21, '2025-03-30 00:32:10', '2026-02-18 20:47:52', NULL, NULL),
(560, 'IMPORTADO', 'Motor 7/8 Ford EcoSport 2.0L', 'MOTOR 7/8', 'Ford', 'EcoSport 2.0L', NULL, '2008', '524-36', '250090', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 21, '2025-03-30 00:34:59', '2026-02-18 20:47:52', NULL, NULL),
(561, 'IMPORTADO', 'Motor 7/8 Ford EcoSport 2.0L', 'MOTOR 7/8', 'Ford', 'EcoSport 2.0L', NULL, '2008', '525-37', '250090', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 21, '2025-03-30 00:35:54', '2026-02-18 20:47:52', NULL, NULL),
(562, 'IMPORTADO', 'Motor 7/8 Ford EcoSport 2.0L', 'MOTOR 7/8', 'Ford', 'EcoSport 2.0L', NULL, '2008', '526-38', '250090', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 21, '2025-03-30 00:36:36', '2026-02-18 20:47:52', NULL, NULL),
(563, 'IMPORTADO', 'Motor 7/8 Ford Fortaleza 4.6L 2V', 'MOTOR 7/8', 'Ford', 'Fortaleza 4.6L 2V', NULL, '2008', '527-39', '250090', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 21, '2025-03-30 00:38:09', '2026-02-18 20:47:52', NULL, NULL),
(564, 'IMPORTADO', 'Motor 7/8 Ford Fortaleza 4.6L 2V', 'MOTOR 7/8', 'Ford', 'Fortaleza 4.6L 2V', NULL, '2008', '528-40', '250090', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 21, '2025-03-30 00:40:40', '2026-02-18 20:47:52', NULL, NULL),
(565, 'IMPORTADO', 'Motor 7/8 Ford Fortaleza 4.6L 2v', 'MOTOR 7/8', 'Ford', 'Fortaleza 4.6L 2V', NULL, '2008', '529-41', '250090', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 21, '2025-03-30 00:45:07', '2026-02-18 20:47:52', NULL, NULL),
(566, 'IMPORTADO', 'Motor 7/8 Ford Fortaleza 4.6L 2v', 'MOTOR 7/8', 'Ford', 'Fortaleza 4.6L 2V', NULL, '2008', '530-42', '250090', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 21, '2025-03-30 00:47:00', '2026-02-18 20:47:52', NULL, NULL),
(567, 'IMPORTADO', 'Motor 7/8 Ford Ranger 2.3L', 'MOTOR 7/8', 'Ford', 'Ranger 2.3L', NULL, '2010', '531-43', '250090', 'APLICA', 'DISPONIBLE', '1.800', NULL, '1.800', 21, '2025-04-01 00:37:52', '2026-02-18 20:47:52', NULL, NULL),
(568, 'IMPORTADO', 'Motor 7/8 Ford Ranger 2.3L', 'MOTOR 7/8', 'Ford', 'Ranger 2.3L', NULL, '2008', '532-44', '250090', 'APLICA', 'DISPONIBLE', '1.800', NULL, '1.800', 21, '2025-04-01 00:40:44', '2026-02-18 20:47:52', NULL, NULL),
(569, 'IMPORTADO', 'Motor 7/8 Nissan MR18', 'MOTOR 7/8', 'Nissan', 'MR18', NULL, '2008', '533-45', '250090', 'APLICA', 'DISPONIBLE', '1.800', NULL, '1.800', 21, '2025-04-01 00:41:44', '2026-02-18 20:47:52', NULL, NULL),
(570, 'IMPORTADO', 'Motor 7/8 Toyota 1ZZ Nueva Sensación', 'MOTOR 7/8', 'Toyota', '1ZZ Nueva Sensación', NULL, '2008', '535-46', '250090', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 21, '2025-04-01 00:45:25', '2026-02-18 20:47:52', NULL, NULL),
(571, 'IMPORTADO', 'Motor 7/8 Toyota 3RZ', 'MOTOR 7/8', 'Toyota', '3RZ', NULL, '2008', '535-47', '250090', 'APLICA', 'DISPONIBLE', '2.300', NULL, '2.300', 21, '2025-04-01 00:50:38', '2026-02-18 20:47:52', NULL, NULL),
(572, 'IMPORTADO', 'Motor 7/8 Nissan KA24', 'MOTOR 7/8', 'Nissan', 'KA24', NULL, '2007', '536-48', '250090', 'APLICA', 'DISPONIBLE', '2.300', NULL, '2.300', 21, '2025-04-01 00:53:16', '2026-02-18 20:47:52', NULL, NULL),
(573, 'IMPORTADO', 'Motor 7/8 Ford Escape 3.0L TA', 'MOTOR 7/8', 'Ford', 'Escape', NULL, '2008', '537-49', '250090', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 21, '2025-04-01 00:54:57', '2026-02-18 20:47:52', NULL, NULL),
(575, 'IMPORTADO', 'Motor 7/8 Ford Escape 3.0L', 'MOTOR 7/8', 'Ford', 'Escape TA', NULL, '2008', '538-50', '250090', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 21, '2025-04-01 00:57:58', '2026-02-18 20:47:52', NULL, NULL),
(576, 'IMPORTADO', 'Motor 7/8 Ford Fusion 3.0L', 'MOTOR 7/8', 'Ford', 'Fusión 3.0L', NULL, '2008', '539-51', '250090', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 21, '2025-04-01 01:07:15', '2026-02-18 20:47:52', NULL, NULL),
(577, 'IMPORTADO', 'Motor 7/8 Ford Fusion 3.0L', 'MOTOR 7/8', 'Ford', 'Fisio 3.0L', NULL, '2008', '540-52', '250090', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 21, '2025-04-01 01:09:33', '2026-02-18 20:47:52', NULL, NULL),
(578, 'IMPORTADO', 'Motor 7/8 Toyota 2UZ', 'MOTOR 7/8', 'Toyota', '2UZ 4.7L Tundra', NULL, '2008', '541-53', '250090', 'APLICA', 'DISPONIBLE', '3.000', NULL, '3.000', 21, '2025-04-01 01:11:19', '2026-02-18 20:47:52', NULL, NULL),
(579, 'IMPORTADO', 'Motor 7/8 Toyota 2UZ 4.7L Tundra', 'MOTOR 7/8', 'Toyota', '2UZ 4.7L Tundra', NULL, '2008', '542-54', '250090', 'APLICA', 'DISPONIBLE', '3.000', NULL, '3.000', 21, '2025-04-01 01:12:59', '2026-02-18 20:47:52', NULL, NULL),
(580, 'IMPORTADO', 'Motor 7/8 Toyota 2UZ 4.7L Tundra', 'MOTOR 7/8', 'Toyota', 'Tundra 4.7L', NULL, '2008', '543-55', '250090', 'APLICA', 'DISPONIBLE', '3.000', NULL, '3.000', 21, '2025-04-01 01:13:52', '2026-02-18 20:47:52', NULL, NULL),
(581, 'IMPORTADO', 'Motor 7/8 Ford 4.6L 3V', 'MOTOR 7/8', 'Ford', 'Explorer 3V', NULL, '2008', '544-56', '250090', 'APLICA', 'DISPONIBLE', '2.300', NULL, '2.300', 21, '2025-04-01 01:15:26', '2026-02-18 20:47:52', NULL, NULL),
(582, 'IMPORTADO', 'Motor 7/8 Ford 4.6L 3V', 'MOTOR 7/8', 'Ford', 'Explorer 3V', NULL, '2008', '545-57', '250090', 'APLICA', 'DISPONIBLE', '2.300', NULL, '2.300', 21, '2025-04-01 01:16:41', '2026-02-18 20:47:52', NULL, NULL),
(583, 'IMPORTADO', 'Motor 7/8 Ford 4.6L 3V', 'MOTOR 7/8', 'Ford', 'Explorer 3V', NULL, '2008', '546-58', '250090', 'APLICA', 'DISPONIBLE', '2.300', NULL, '2.300', 21, '2025-04-01 01:17:40', '2026-02-18 20:47:52', NULL, NULL),
(584, 'IMPORTADO', 'Motor 7/8 Ford 4.6L 3V', 'MOTOR 7/8', 'Ford', 'Explorer 3V', NULL, '2008', '547-59', '250090', 'APLICA', 'DISPONIBLE', '2.300', NULL, '2.300', 21, '2025-04-01 01:18:37', '2026-02-18 20:47:52', NULL, NULL),
(585, 'IMPORTADO', 'Motor 7/8 Jeep Grand Cherokee 4.7L 16B', 'MOTOR 7/8', 'Jeep', 'Grand Cherokee 4.7L 16B', NULL, '2009', '548-60', '250090', 'APLICA', 'DISPONIBLE', '2.300', NULL, '2.300', 21, '2025-04-01 01:21:10', '2026-02-18 20:47:52', NULL, NULL),
(586, 'IMPORTADO', 'Motor 7/8 Jeep Grand Cherokee 4.7L 16B', 'MOTOR 7/8', 'Jeep', 'Grand Cherokee 4.6L 16 B', NULL, '2009', '549-61', '250090', 'APLICA', 'DISPONIBLE', '2.300', NULL, '2.300', 21, '2025-04-01 01:23:12', '2026-02-18 20:47:52', NULL, NULL),
(587, 'IMPORTADO', 'Motor 7/8 Jeep Grand Cherokee 4.7L 16B', 'MOTOR 7/8', 'Jeep', 'Grand Cherokee 4.7L 16B', NULL, '2010', '550-62', '250090', 'APLICA', 'DISPONIBLE', '2.300', NULL, '2.300', 21, '2025-04-01 01:24:02', '2026-02-18 20:47:52', NULL, NULL),
(588, 'IMPORTADO', 'Motor 7/8 Grand Cherokee 4.7 L 8B', 'MOTOR 7/8', 'Jeep', 'Grand Cherokee 4.7L 8B', NULL, '2010', '551-63', '250090', 'APLICA', 'DISPONIBLE', '2.300', NULL, '2.300', 21, '2025-04-01 01:25:21', '2026-02-18 20:47:52', NULL, NULL),
(589, 'IMPORTADO', 'Motor 7/8 Jeep Grand Cherokee 4.7L 16B', 'MOTOR 7/8', 'Jeep', 'Grand Cherokee 4.7L 16B', NULL, '2010', '552-64', '250090', 'APLICA', 'DISPONIBLE', '2.300', NULL, '2.300', 21, '2025-04-01 01:26:12', '2026-02-18 20:47:52', NULL, NULL),
(590, 'IMPORTADO', 'Motor 7/8 Jeep Grand Cherokee 4.7L 16B', 'MOTOR 7/8', 'Jeep', 'Grand Cherokee 4.7L 16B', NULL, '2010', '553-65', '250090', 'APLICA', 'DISPONIBLE', '2.300', NULL, '2.300', 21, '2025-04-01 01:27:04', '2026-02-18 20:47:52', NULL, NULL),
(591, 'IMPORTADO', 'Motor 7/8 Ford 300', 'MOTOR 7/8', 'Ford', '300', NULL, '1980', '554-66', '250090', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 21, '2025-04-01 01:28:04', '2026-02-18 20:47:52', NULL, NULL),
(592, 'IMPORTADO', 'Motor 7/8 Ford 300', 'MOTOR 7/8', 'Ford', '300', NULL, '1980', '555-67', '250090', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 21, '2025-04-01 01:28:51', '2026-02-18 20:47:52', NULL, NULL),
(593, 'IMPORTADO', 'Motor 7/8 Chevrolet Orlando 2.4L', 'MOTOR 7/8', 'Chevrolet', 'Orlando 2.4L', NULL, '1980', '556-68', '250090', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 21, '2025-04-01 01:29:50', '2026-02-18 20:47:52', NULL, NULL),
(594, 'IMPORTADO', 'Motor 7/8 Nissan K24', 'MOTOR 7/8', 'Nissan', 'K24', NULL, '2007', '587-69', '250090', 'APLICA', 'DISPONIBLE', '1.800', NULL, '1.800', 21, '2025-04-01 01:31:37', '2026-02-18 20:47:52', NULL, NULL),
(595, 'IMPORTADO', 'Motor 7/8 Ford Explorer 3.5L', 'MOTOR 7/8', 'Ford', 'Explorer 3.5L', NULL, '2012', '558-70', '250090', 'APLICA', 'DISPONIBLE', '2.800', NULL, '2.800', 21, '2025-04-01 01:32:21', '2026-02-18 20:47:52', NULL, NULL),
(596, 'IMPORTADO', 'Motor 7/8 Chevrolet Rey Camion 6.0L', 'MOTOR 7/8', 'Chevrolet', 'Rey Camion 6.0L', NULL, '2010', '559-71', '250090', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 21, '2025-04-01 01:33:15', '2026-02-18 20:47:52', NULL, NULL),
(597, 'IMPORTADO', 'Motor 7/8 Chevrolet Rey Camión 6.0L', 'MOTOR 7/8', 'Chevrolet', 'Rey Camión 6.0L', NULL, '2010', '560-72', '250090', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 21, '2025-04-01 01:34:13', '2026-02-18 20:47:52', NULL, NULL),
(598, 'IMPORTADO', 'Motor 7/8 Chevrolet Rey Camión 6.0L', 'MOTOR 7/8', 'Chevrolet', 'Rey Camión 6.0L', NULL, '2010', '561-73', '250090', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 21, '2025-04-01 01:35:25', '2026-02-18 20:47:52', NULL, NULL),
(599, 'IMPORTADO', 'Motor 7/8 Chevrolet Rey Camión 6.0L', 'MOTOR 7/8', 'Chevrolet', 'Rey Camión 6.0L', NULL, '2010', '562-74', '250090', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 21, '2025-04-01 01:36:30', '2026-02-18 20:47:52', NULL, NULL),
(600, 'IMPORTADO', 'Motor 7/8 Ford Super Duty 6.2L', 'MOTOR 7/8', 'Ford', 'Super Duty 6.2L', NULL, '2010', '563-75', '250090', 'APLICA', 'DISPONIBLE', '4.000', NULL, '4.000', 21, '2025-04-01 01:37:42', '2026-02-18 20:47:52', NULL, NULL),
(601, 'IMPORTADO', 'Motor 7/8 Ford Super Duty 6.2L', 'MOTOR 7/8', 'Ford', 'Super Duty 6.2L', NULL, '2010', '564-76', '250090', 'APLICA', 'DISPONIBLE', '4.000', NULL, '4.000', 21, '2025-04-01 01:38:36', '2026-02-18 20:47:52', NULL, NULL),
(602, 'IMPORTADO', 'Motor 7/8 Ford Super Duty 6.2L', 'MOTOR 7/8', 'Motor 7/8 Ford Super Duty 6.2L', 'Super Duty 6.2L', NULL, '2010', '565-77', '250090', 'APLICA', 'DISPONIBLE', '4.000', NULL, '4.000', 21, '2025-04-01 01:40:08', '2026-02-18 20:47:52', NULL, NULL),
(603, 'IMPORTADO', 'Motor 7/8 Ford Super Duty 6.2L', 'MOTOR 7/8', 'Ford', 'Super Duty 6.2L', NULL, '2010', '566-78', '250090', 'APLICA', 'DISPONIBLE', '4.000', NULL, '4.000', 21, '2025-04-01 01:40:57', '2026-02-18 20:47:52', NULL, NULL),
(604, 'IMPORTADO', 'Motor 7/8 Ford 4.6L 3V', 'MOTOR 7/8', 'Ford', '4.6L 3V Explorer', NULL, '2010', '567-79', '250090', 'APLICA', 'DISPONIBLE', '2.300', NULL, '2.300', 21, '2025-04-01 01:42:03', '2026-02-18 20:47:52', NULL, NULL),
(605, 'IMPORTADO', 'Motor Ford Ranger 2.3L', 'MOTOR 7/8', 'Ford', 'Explorer 4.6L 3V', NULL, '2010', '568-80', '250090', 'APLICA', 'DISPONIBLE', '2.300', NULL, '2.300', 21, '2025-04-01 01:43:42', '2026-02-18 20:47:52', NULL, NULL),
(606, 'IMPORTADO', 'Motor 7/8 Ford 4.6L 3V', 'MOTOR 7/8', 'Ford', 'Explorer 3V', NULL, '2009', '569-81', '250090', 'APLICA', 'DISPONIBLE', '2.300', NULL, '2.300', 21, '2025-04-01 01:44:41', '2026-02-18 20:47:52', NULL, NULL),
(607, 'IMPORTADO', 'Motor Toyota 2RZ', 'MOTOR COMPLETO', 'Toyota', '2RZ', NULL, '1995', '570-82', '250089', 'APLICA', 'DISPONIBLE', '2.200', NULL, '2.200', 22, '2025-04-01 13:33:26', '2026-02-18 20:47:52', NULL, NULL),
(608, 'IMPORTADO', 'Motor Ford Mazda PY', 'MOTOR COMPLETO', 'Ford', 'Mazda', NULL, '2014', '571-83', '250089', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 22, '2025-04-01 13:34:42', '2026-02-18 20:47:52', NULL, NULL),
(609, 'IMPORTADO', 'Motor Toyota 2AZ Previa-Camry', 'MOTOR COMPLETO', 'Toyota', '2AZ', NULL, '2008', '572-84', '250089', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 22, '2025-04-01 13:35:54', '2026-02-18 20:47:52', NULL, NULL),
(610, 'IMPORTADO', 'Motor Toyota 5VZ Prado', 'MOTOR COMPLETO', 'Toyota', '5VZ Prado', NULL, '2002', '573-85', '250089', 'APLICA', 'DISPONIBLE', '1.900', NULL, '1.900', 22, '2025-04-01 13:36:53', '2026-02-18 20:47:52', NULL, NULL),
(611, 'IMPORTADO', 'Motor Hyundai G4KE', 'MOTOR COMPLETO', 'Hyundai', 'G4KE', NULL, '2008', '574-86', '250089', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 22, '2025-04-01 13:37:51', '2026-02-18 20:47:52', NULL, NULL),
(612, 'IMPORTADO', 'Motor Hyundai G4KH', 'MOTOR COMPLETO', 'Hyundai', 'G4KH', NULL, '2008', '575-87', '250089', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 22, '2025-04-01 13:39:04', '2026-02-18 20:47:52', NULL, NULL),
(613, 'IMPORTADO', 'Motor Hyundai G6EA', 'MOTOR COMPLETO', 'Hyundai', 'G6DA Santa Fe', NULL, '2010', '576-88', '250089', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 22, '2025-04-01 13:40:24', '2026-02-18 20:47:52', NULL, NULL),
(614, 'IMPORTADO', 'Motor Chevrolet Grand Vitara XL5', 'MOTOR COMPLETO', 'Chevrolet', 'Grand Vitara XL5', NULL, '2008', '577-89', '250089', 'APLICA', 'DISPONIBLE', '1.800', NULL, '1.800', 22, '2025-04-01 13:41:36', '2026-02-18 20:47:52', NULL, NULL),
(615, 'IMPORTADO', 'Motor 7/8 Toyota 5VZ Prado', 'MOTOR COMPLETO', 'Toyota', '5VZ Prado', NULL, '2008', '578-90', '250089', 'APLICA', 'DISPONIBLE', '1.800', NULL, '1.800', 22, '2025-04-01 15:49:18', '2026-02-18 20:47:52', NULL, NULL),
(616, 'IMPORTADO', 'Motor Chevrolet Cruzer 1.8L', 'MOTOR COMPLETO', 'Chevrolet', 'Cruzer', NULL, '2008', '579-91', '250089', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 22, '2025-04-01 15:50:30', '2026-02-18 20:47:52', NULL, NULL),
(617, 'IMPORTADO', 'Motor Chevrolet Ecotec 1.4L', 'MOTOR COMPLETO', 'Chevrolet', 'Ecotec', NULL, '2008', '580-92', '250089', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 22, '2025-04-01 15:52:02', '2026-02-18 20:47:52', NULL, NULL),
(618, 'IMPORTADO', 'Motor Toyota Yaris 1NZ', 'MOTOR COMPLETO', 'Toyota', '1NZ-YARIS', NULL, '2008', '581-93', '250089', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 22, '2025-04-01 19:33:40', '2026-02-18 20:47:52', NULL, NULL),
(619, 'IMPORTADO', 'Motor Toyota 2AZ Previa', 'MOTOR COMPLETO', 'Toyota', 'Previa 2AZ', NULL, '2010', '582-94', '250089', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 22, '2025-04-01 19:34:28', '2026-02-18 20:47:52', NULL, NULL),
(620, 'IMPORTADO', 'Motor Chevrolet Orlando 2.4L', 'MOTOR COMPLETO', 'Chevrolet', 'Orlando 2.4L', NULL, '2010', '583-95', '250089', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 22, '2025-04-01 19:35:36', '2026-02-18 20:47:52', NULL, NULL),
(621, 'IMPORTADO', 'Motor Chevrolet Cruze', 'MOTOR COMPLETO', 'Chevrolet', 'Cruze', NULL, '2008', '584-96', '250089', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 22, '2025-04-01 19:39:04', '2026-02-18 20:47:52', NULL, NULL),
(622, 'IMPORTADO', 'Motor Chevrolet Cruze 1.6L', 'MOTOR 7/8', 'Chevrolet', 'Cruze', NULL, '2010', '585-97', '250089', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 22, '2025-04-01 19:41:51', '2026-02-18 20:47:52', NULL, NULL),
(623, 'IMPORTADO', 'Motor Dodge Caliber 2.4L', 'MOTOR COMPLETO', 'Dodge', 'Caliber 2.4L', NULL, '2008', '586-98', '250089', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 22, '2025-04-01 19:48:09', '2026-02-18 20:47:52', NULL, NULL),
(624, 'IMPORTADO', 'Motor Chevrolet 5.3L TM', 'MOTOR COMPLETO', 'Chevrolet', '5.3L Silverado', NULL, '2008', '587-99', '250089', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 22, '2025-04-01 19:49:07', '2026-02-18 20:47:52', NULL, NULL),
(625, 'IMPORTADO', 'Motor Chevrolet 5.3L 2008', 'MOTOR COMPLETO', 'Chevrolet', 'Silverado 5.3L', NULL, '2008', '588-100', '250089', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 22, '2025-04-02 02:10:40', '2026-02-18 20:47:52', NULL, NULL),
(626, 'IMPORTADO', 'Motor Chevrolet 5.3L 2008', 'MOTOR COMPLETO', 'Chevrolet', 'Silverado 5.3L', NULL, '2008', '589-101', '250089', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 22, '2025-04-02 02:12:42', '2026-02-18 20:47:52', NULL, NULL),
(627, 'IMPORTADO', 'Motor Jeep Dodge Ram 5.7L', 'MOTOR COMPLETO', 'Jeep', 'Dodge Ram 5.7L', NULL, '2008', '590-102', '250089', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 22, '2025-04-02 02:14:53', '2026-02-18 20:47:52', NULL, NULL),
(628, 'IMPORTADO', 'Motor Chevrolet 6.2L', 'MOTOR COMPLETO', 'Chevrolet', '6.2L', NULL, '2010', '591-103', '250089', 'APLICA', 'DISPONIBLE', '4.000', NULL, '4.000', 22, '2025-04-02 02:16:47', '2026-02-18 20:47:52', NULL, NULL),
(629, 'IMPORTADO', 'Motor Jeep Grand Cherokee 4.7L 8B EGR', 'MOTOR COMPLETO', 'Jeep', 'Grand Cherokee 4.7L 8B EGR', NULL, '2008', '592-104', '250089', 'APLICA', 'DISPONIBLE', '2.300', NULL, '2.300', 22, '2025-04-02 02:18:49', '2026-02-18 20:47:52', NULL, NULL),
(630, 'IMPORTADO', 'Motor Toyota 3RZ', 'MOTOR 7/8', 'Toyota', '3RZ Meru', NULL, '2004', '593-105', '250089', 'APLICA', 'DISPONIBLE', '2.300', NULL, '2.300', 22, '2025-04-02 02:20:45', '2026-02-18 20:47:52', NULL, NULL),
(631, 'IMPORTADO', 'Motor Chevrolet Spark', 'MOTOR COMPLETO', 'Chevrolet', 'Spark', NULL, '2010', '594-106', '250089', 'APLICA', 'DISPONIBLE', '1.000', NULL, '1.000', 22, '2025-04-02 02:22:04', '2026-02-18 20:47:52', NULL, NULL),
(632, 'IMPORTADO', 'Motor Jeep 5.7L', 'MOTOR COMPLETO', 'Jeep', 'Grand Cherokee 5.7L', NULL, '2008', '595-107', '250089', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 22, '2025-04-02 02:23:39', '2026-02-18 20:47:52', NULL, NULL),
(633, 'IMPORTADO', 'Motor Jeep 4.7L 8B EGR', 'MOTOR COMPLETO', 'Jeep', 'Grand Cherokee 4.7L 8B EGR', NULL, '2008', '596-108', '250089', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 22, '2025-04-02 02:24:55', '2026-02-18 20:47:52', NULL, NULL),
(634, 'IMPORTADO', 'Motor Cummnis 4BT', 'MOTOR COMPLETO', 'Cummnis', '4BT', NULL, '2000', '597-109', '250089', 'APLICA', 'DISPONIBLE', '3.500', NULL, '3.500', 22, '2025-04-02 02:26:02', '2026-02-18 20:47:52', NULL, NULL),
(635, 'IMPORTADO', 'Motor Ford 300', 'MOTOR COMPLETO', 'Ford', '300', NULL, '1990', '598-110', '250089', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 22, '2025-04-02 02:26:51', '2026-02-18 20:47:52', NULL, NULL),
(636, 'IMPORTADO', 'Motor Ford 4.6L 3V', 'MOTOR COMPLETO', 'Ford', 'Explorer Eddie Bauer 3V', NULL, '2010', '599-111', '250089', 'APLICA', 'DISPONIBLE', '2.300', NULL, '2.300', 22, '2025-04-02 02:28:34', '2026-02-18 20:47:52', NULL, NULL),
(637, 'IMPORTADO', 'Motor Ford Explorer Eddie Bauer 3V', 'MOTOR COMPLETO', 'Ford', 'Explorer Eddie Bauer 3V', NULL, '2010', '600-112', '250089', 'APLICA', 'DISPONIBLE', '2.300', NULL, '2.300', 22, '2025-04-02 02:34:58', '2026-02-18 20:47:52', NULL, NULL),
(638, 'IMPORTADO', 'Motor Ford 4.6L 2V', 'MOTOR COMPLETO', 'Ford', '4.6L 2V', NULL, '2010', '601-113', '250089', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 22, '2025-04-02 02:36:02', '2026-02-18 20:47:52', NULL, NULL),
(639, 'IMPORTADO', 'Motor Ford 4.6L 2V', 'MOTOR COMPLETO', 'Ford', 'Fortaleza 4.6L 2V', NULL, '2008', '602-114', '250089', 'APLICA', 'DISPONIBLE', '1.600', NULL, '1.600', 22, '2025-04-02 02:37:38', '2026-02-18 20:47:52', NULL, NULL),
(640, 'IMPORTADO', 'Motor Ford 5.4L 2V', 'MOTOR COMPLETO', 'Ford', 'Tritón 5.4L 2V', NULL, '2008', '603-115', '250089', 'APLICA', 'DISPONIBLE', '2.008', NULL, '2.008', 22, '2025-04-02 02:39:10', '2026-02-18 20:47:52', NULL, NULL),
(641, 'IMPORTADO', 'Motor Ford 5.4L 2V', 'MOTOR 7/8', 'Ford', 'Tritón 5.4L 2V', NULL, '2008', '604-116', '250089', 'APLICA', 'DISPONIBLE', '1.800', NULL, '1.800', 22, '2025-04-02 02:40:30', '2026-02-18 20:47:52', NULL, NULL),
(642, 'IMPORTADO', 'Motor Chevrolet 350', 'MOTOR COMPLETO', 'Chevrolet', '350 Tipo Vortec', NULL, '1995', '605-117', '250089', 'APLICA', 'DISPONIBLE', '1.800', NULL, '1.800', 22, '2025-04-02 02:49:16', '2026-02-18 20:47:52', NULL, NULL),
(643, 'IMPORTADO', 'Motor Chevrolet 350 tipo Vortec', 'MOTOR COMPLETO', 'Chevrolet', '350 Tipo Vortec', NULL, '1995', '606-118', '250089', 'APLICA', 'DISPONIBLE', '1.800', NULL, '1.800', 22, '2025-04-02 02:50:25', '2026-02-18 20:47:52', NULL, NULL),
(644, 'IMPORTADO', 'Motor Chevrolet Orlando 2.4L', 'MOTOR COMPLETO', 'Chevrolet', 'Orlando 2.4L', NULL, '2010', '607-119', '250089', 'APLICA', 'DISPONIBLE', '1.500', NULL, '1.500', 22, '2025-04-02 02:51:41', '2026-02-18 20:47:52', NULL, NULL),
(645, 'IMPORTADO', 'Motor Dodge Caliber 2.4L', 'MOTOR COMPLETO', 'Dodge', 'Caliber 2.4L', NULL, '2008', '608-120', '250089', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 22, '2025-04-02 02:52:31', '2026-02-18 20:47:52', NULL, NULL),
(646, 'IMPORTADO', 'Motor Chevrolet 5.3L 2005', 'MOTOR COMPLETO', 'Chevrolet', '5.3L Tanque Mecánico', NULL, '2000', '609-121', '250089', 'APLICA', 'DISPONIBLE', '2.000', NULL, '2.000', 22, '2025-04-02 02:53:46', '2026-02-18 20:47:52', NULL, NULL),
(647, 'IMPORTADO', 'Motor Chevrolet Rey Camión 6.0L', 'MOTOR COMPLETO', 'Chevrolet', 'Rey Camion 6.0L', NULL, '2010', '610-122', '250089', 'APLICA', 'DISPONIBLE', '2.400', NULL, '2.400', 22, '2025-04-02 02:54:57', '2026-02-18 20:47:52', NULL, NULL),
(648, 'IMPORTADO', 'Motor Chevrolet 6.0L Rey Camion', 'MOTOR COMPLETO', 'Chevrolet', 'Rey Camión 6.0L', NULL, '2010', '611-123', '250089', 'APLICA', 'DISPONIBLE', '2.400', NULL, '2.400', 22, '2025-04-02 02:55:56', '2026-02-18 20:47:52', NULL, NULL),
(649, 'IMPORTADO', 'Motor Ford Ranger 2.3L', 'MOTOR COMPLETO', 'Ford', 'Ranger 2.3L', NULL, '2010', '612-124', '250089', 'APLICA', 'DISPONIBLE', '1.800', NULL, '1.800', 22, '2025-04-02 02:57:39', '2026-02-18 20:47:52', NULL, NULL),
(650, 'IMPORTADO', 'Motor 7/8 Ford EcoSport 2.0L', 'MOTOR 7/8', 'FORD', 'ECOSPORT 2.0L', NULL, '2010', '613-125', '250090', 'APLICA', 'DISPONIBLE', '1.300', NULL, '1.300', 21, '2025-04-02 02:58:48', '2026-02-18 20:47:52', NULL, NULL),
(651, 'IMPORTADO', 'Motor 7/8 Ford Explorer 4.6L 3V', 'MOTOR 7/8', 'Ford', 'Explorer Eddie Bauer 3V', NULL, '2010', '614-126', '250090', 'APLICA', 'DISPONIBLE', '2.300', NULL, '2.300', 21, '2025-04-02 03:00:04', '2026-02-18 20:47:52', NULL, NULL),
(652, 'IMPORTADO', 'Motor 7/8 Chevrolet Gran Vitara J3', 'MOTOR 7/8', 'Chevrolet', 'Grand Vitara J3', NULL, '2007', '615-127', '250090', 'APLICA', 'DISPONIBLE', '3.000', NULL, '3.000', 21, '2025-04-08 15:14:06', '2026-02-18 20:47:52', NULL, NULL),
(653, 'IMPORTADO', 'Motor 7/8 Ford EcoSport 2.0L', 'MOTOR 7/8', 'Ford', 'EcoSport 2.0L', NULL, '2008', '616-128', '250090', 'APLICA', 'DISPONIBLE', '1.400', NULL, '1.400', 21, '2025-04-08 15:15:08', '2026-02-18 20:47:52', NULL, NULL),
(654, 'IMPORTADO', 'Motor Chevrolet 5.3 2008', 'MOTOR COMPLETO', 'Chevrolet', '5.3L', NULL, '2008', '617-129', '250089', 'APLICA', 'DISPONIBLE', '1.900', NULL, '1.900', 22, '2025-04-08 15:16:18', '2026-02-18 20:47:52', NULL, NULL),
(655, 'IMPORTADO', 'Motor Toyota 22R', 'MOTOR COMPLETO', 'Toyota', '22R', NULL, '1990', '618-130', '250089', 'APLICA', 'DISPONIBLE', '3.000', NULL, '3.000', 22, '2025-04-08 15:17:04', '2026-02-18 20:47:52', NULL, NULL),
(656, 'IMPORTADO', 'Motor Cummnis 4BT', 'MOTOR COMPLETO', 'Cummins', '4BT', NULL, '2000', '619-131', '240222', 'APLICA', 'DISPONIBLE', '3.000', NULL, '3.000', 20, '2025-04-10 14:18:50', '2026-02-18 20:47:52', NULL, NULL),
(657, 'IMPORTADO', 'Motor Cummnis 6BT', 'MOTOR COMPLETO', 'Cummins', '6BT', NULL, '2000', '620-132', '240222', 'APLICA', 'DISPONIBLE', '6.000', NULL, '6.000', 20, '2025-04-10 14:23:06', '2026-02-18 20:47:52', NULL, NULL),
(658, 'IMPORTADO', 'Motor 7/8 Cummins 4BT', 'MOTOR 7/8', 'Cummins', '4BT', NULL, '2000', '621-133', '060325', 'APLICA', 'DISPONIBLE', '3.000', NULL, '3.000', 18, '2025-04-10 14:24:40', '2026-02-18 20:47:52', NULL, NULL),
(659, 'IMPORTADO', 'Motor 7/8 Jeep 5.7L Dodge Ram', 'MOTOR 7/8', 'Jeep', 'Dodge Ram 5.7L 4G', NULL, '2008', '622-134', '240222', 'APLICA', 'DISPONIBLE', '1.800', NULL, '1.800', 20, '2025-04-10 15:08:50', '2026-02-18 20:47:52', NULL, NULL),
(660, 'IMPORTADO', 'MOTOR 7/8 Jeep Cherokee  3.7L KJ', 'MOTOR 7/8', 'Jeep', 'Cherokee  3.7L KJ', NULL, '2006', '623-135', '240222', 'APLICA', 'DISPONIBLE', '1.600', NULL, '800', 20, '2025-04-10 15:09:41', '2026-03-14 15:33:11', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `maintenances`
--

CREATE TABLE `maintenances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `descripcion` text NOT NULL,
  `tipo` varchar(255) NOT NULL,
  `status` enum('EN ESPERA','EN PROCESO','TERMINADO','CULMINADO','CANCELADO') NOT NULL,
  `partida_id` bigint(20) UNSIGNED NOT NULL,
  `cedula_mecanico` int(11) NOT NULL,
  `nombre_mecanico` varchar(255) NOT NULL,
  `apellido_mecanico` varchar(255) NOT NULL,
  `observaciones` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `maintenance_bills`
--

CREATE TABLE `maintenance_bills` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `maintenances_id` bigint(20) UNSIGNED DEFAULT NULL,
  `multi_tools` varchar(255) DEFAULT NULL,
  `mechanic` varchar(255) DEFAULT NULL,
  `mechanic_assistant` varchar(255) DEFAULT NULL,
  `seller` varchar(255) DEFAULT NULL,
  `seller_assistant` varchar(255) DEFAULT NULL,
  `cleaning` varchar(255) DEFAULT NULL,
  `consumables` varchar(255) DEFAULT NULL,
  `forklift` varchar(255) DEFAULT NULL,
  `camera_technician` varchar(255) DEFAULT NULL,
  `camera_technical_assistant` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `maintenance_items`
--

CREATE TABLE `maintenance_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `maintenance_id` bigint(20) UNSIGNED NOT NULL,
  `description` varchar(255) NOT NULL,
  `cost` decimal(10,2) NOT NULL,
  `invoice_path` varchar(255) DEFAULT NULL,
  `type` enum('REPUESTO','SERVICIO') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `maintenance_teams`
--

CREATE TABLE `maintenance_teams` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `maintenance_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `external_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materials`
--

CREATE TABLE `materials` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `maintenances_id` bigint(20) UNSIGNED DEFAULT NULL,
  `concha_biela` varchar(255) DEFAULT NULL,
  `concha_bancada` varchar(255) DEFAULT NULL,
  `anillos` varchar(255) DEFAULT NULL,
  `empacadura_camara` varchar(255) DEFAULT NULL,
  `empacadura_carter` varchar(255) DEFAULT NULL,
  `kit_empacaduras` varchar(255) DEFAULT NULL,
  `baño_quimico` varchar(255) DEFAULT NULL,
  `goma_valvula` varchar(255) DEFAULT NULL,
  `planos` varchar(255) DEFAULT NULL,
  `valvulas` varchar(255) DEFAULT NULL,
  `rectificadora` varchar(255) DEFAULT NULL,
  `asientos` varchar(255) DEFAULT NULL,
  `camisas` varchar(255) DEFAULT NULL,
  `levas` varchar(255) DEFAULT NULL,
  `pistones` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2014_10_12_200000_add_two_factor_columns_to_users_table', 1),
(4, '2019_08_19_000000_create_failed_jobs_table', 1),
(5, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(6, '2024_02_05_123353_create_sessions_table', 1),
(7, '2024_02_05_170601_create_containers_table', 1),
(8, '2024_02_05_170602_create_partidas_table', 1),
(9, '2024_02_07_195621_create_maintenances_table', 1),
(10, '2024_02_15_152550_create_employees_table', 1),
(11, '2024_02_19_143056_create_materials_table', 1),
(12, '2024_02_19_143108_create_maintenance_bills_table', 1),
(13, '2024_02_24_144556_create_accesorio_engines_table', 1),
(14, '2024_03_21_131236_create_billings_table', 2),
(15, '2025_12_19_084106_create_billing_requests_table', 3),
(16, '2025_12_19_141144_add_rol_to_users_table', 4),
(17, '2025_12_19_142746_add_client_cedula_to_billing_requests', 5),
(18, '2025_12_19_180209_add_client_details_to_billings_table', 6),
(19, '2026_01_24_183702_add_total_to_billings_table', 7),
(20, '2026_01_24_184031_rename_estado_to_status_in_maintenances_table', 8),
(21, '2024_04_01_133934_create_bitacoras_table', 9),
(22, '2024_04_13_113633_create_reverse_bills_table', 9),
(23, '2024_06_21_114201_add_categorie_cantidad_to_partidas_table', 9),
(24, '2024_07_17_141119_add_item_to_partidas_table', 10),
(25, '2026_01_25_181913_create_permission_tables', 10),
(26, '2026_01_25_182123_create_permission_expirations_table', 11),
(27, '2026_01_25_182133_create_maintenance_details_tables', 11),
(28, '2026_01_25_183924_update_roles_and_permissions', 12),
(29, '2026_01_25_185244_ensure_superuser_access_to_scan', 13),
(30, '2026_01_25_190909_add_costo_to_partidas_table', 14),
(31, '2026_01_25_195005_make_partida_columns_nullable', 15),
(32, '2026_01_25_202029_add_origen_to_partidas_table', 16),
(33, '2026_01_25_203304_rename_partidas_to_inventarios_table', 17),
(34, '2026_02_01_151627_update_bitacoras_to_uppercase_spanish', 18),
(35, '2026_02_16_122757_add_phone_and_address_to_billing_requests_and_billings_table', 19),
(36, '2026_02_16_135828_create_exchange_rates_table', 20),
(37, '2026_02_17_113223_modify_maintenance_bills_table_remove_fields_and_rename_forklift', 21),
(38, '2026_02_17_114402_update_maintenance_status_and_bill_columns', 22),
(39, '2026_03_14_092435_add_serial_to_inventarios_table', 23);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2),
(3, 'App\\Models\\User', 5),
(5, 'App\\Models\\User', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
('josesierraaes@gmail.com', '$2y$12$/6c3bbxUU3DrgVrtqviXgeV4hGiW3rqPcmMqYy1QqudJkjq3G30uW', '2026-02-07 20:47:06'),
('yordyalejandro13@gmail.com', '$2y$12$tTIKQ6wiq.1B3acmWi8WX.x76RsBOfzr7Ra2wGsjYAPZesD.dsfqm', '2026-03-14 15:37:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'manage users', 'web', '2026-01-25 22:23:39', '2026-01-25 22:23:39'),
(2, 'manage roles', 'web', '2026-01-25 22:23:39', '2026-01-25 22:23:39'),
(3, 'manage backups', 'web', '2026-01-25 22:23:39', '2026-01-25 22:23:39'),
(4, 'view bitacora', 'web', '2026-01-25 22:23:39', '2026-01-25 22:23:39'),
(5, 'delete bitacora', 'web', '2026-01-25 22:23:39', '2026-01-25 22:23:39'),
(6, 'view maintenance', 'web', '2026-01-25 22:23:39', '2026-01-25 22:23:39'),
(7, 'create maintenance', 'web', '2026-01-25 22:23:39', '2026-01-25 22:23:39'),
(8, 'view partida', 'web', '2026-01-25 22:23:39', '2026-01-25 22:23:39'),
(9, 'manage partida', 'web', '2026-01-25 22:23:39', '2026-01-25 22:23:39'),
(10, 'view billing', 'web', '2026-01-25 22:23:39', '2026-01-25 22:23:39'),
(11, 'manage billing', 'web', '2026-01-25 22:23:39', '2026-01-25 22:23:39'),
(12, 'view reports', 'web', '2026-01-25 22:23:39', '2026-01-25 22:23:39'),
(13, 'access scan', 'web', '2026-01-25 22:41:24', '2026-01-25 22:41:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permission_expirations`
--

CREATE TABLE `permission_expirations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  `permission_name` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reverse_bills`
--

CREATE TABLE `reverse_bills` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `users_id` bigint(20) UNSIGNED NOT NULL,
  `numero_factura` varchar(255) NOT NULL,
  `numero_control` varchar(255) NOT NULL,
  `numero_nota_credito` varchar(255) NOT NULL,
  `numero_factura_afect` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'SUPERUSUARIO', 'web', '2026-01-25 22:23:39', '2026-01-25 22:23:39'),
(2, 'ADMINISTRADOR', 'web', '2026-01-25 22:23:39', '2026-01-25 22:23:39'),
(3, 'MECANICO', 'web', '2026-01-25 22:23:39', '2026-01-25 22:41:24'),
(4, 'GESTOR DE INVENTARIO', 'web', '2026-01-25 22:23:39', '2026-01-25 22:41:24'),
(5, 'FACTURACION', 'web', '2026-01-25 22:23:39', '2026-01-25 22:23:39'),
(6, 'VENDEDOR', 'web', '2026-01-25 22:41:24', '2026-01-25 22:41:24'),
(7, 'Tecnico', 'web', '2026-01-26 00:01:32', '2026-01-26 00:01:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(1, 2),
(2, 1),
(2, 2),
(3, 1),
(4, 1),
(4, 2),
(5, 1),
(6, 1),
(6, 2),
(6, 3),
(6, 7),
(7, 1),
(7, 2),
(7, 3),
(7, 7),
(8, 1),
(8, 2),
(8, 4),
(8, 6),
(9, 1),
(9, 2),
(9, 4),
(10, 1),
(10, 2),
(10, 5),
(11, 1),
(11, 2),
(11, 5),
(12, 1),
(12, 2),
(13, 1),
(13, 2),
(13, 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('1Sqbw28hGKE1Lae3hVmxDAZGOM1qkeCFWtdwOPiq', NULL, '195.211.77.141', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiZG15VkdhNjQzTUx2cTFGSTBWRlVyck5lUlBYcm9hWE9DR2Q4ak91SiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773490620),
('DJDLSWIBGtyZ51XiabWpbY2zbixNBQttN9PsHNFo', NULL, '45.148.10.187', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUGNtRGJndFJEUnNEelVIVTNjQXlNQ29ZbmVzNUFJN0NMeExZNjhMWSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDE6Imh0dHBzOi8vd3d3LmludGVybmFsLm1haWtlbGNhcnMuY29tL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773493774),
('q6BTGWNsUgHNZ3zhizximPtYQaiouhr9WJokJVLv', NULL, '195.211.77.141', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRzlzR2NjNGNWNDdvNU81ZzB3amZjUHZMRnFRak1qR2lSYjdUcUloTyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vaW50ZXJuYWwubWFpa2VsY2Fycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1773490638),
('ROGO2HsO63nhilLlZq0aI0IlKlXvML3UkWYPZcw9', NULL, '45.148.10.187', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUDhPUTVNSmhIWHZKbnRyZHJXNU9qVE9BWE5kS2EyVVZGd09GZmtEUyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHBzOi8vd3d3LmludGVybmFsLm1haWtlbGNhcnMuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773493773),
('rQIwILbLwlmmhsCJmlZ49isiC3iWPb7URP757HV6', NULL, '204.76.203.25', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.3', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicVpXUjNaTko0YWZ5WWNnekpHSTd5M0VFa2hYcDR3dlpZYWRKR2Q0MyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHBzOi8vaW50ZXJuYWwubWFpa2VsY2Fycy5jb20vbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1773492186),
('tg5kkws7NM7nBX18MpBXEr8Sm3Md0pOzc6gq0VMy', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoia3FIR1F3TEFqaG5wSVlZbjlvbk1vUTRqRlJ1R2xnTjJZUXBjVldoOSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773502636),
('UcIAKUQslW38FQMIbiJhSBAKnWVBEySPeq1utvLh', NULL, '204.76.203.25', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.3', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSTVQY1lhVHdrUUV6bzREcHc3Q2xQeUI5QnZrTGh3YXB4b05UeDN5dCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vaW50ZXJuYWwubWFpa2VsY2Fycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1773492185),
('vAVGK8TPBZNFMgL82QKIEUv2ImUDW3CEhNogktuH', NULL, '45.148.10.187', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidFQ5d0x1ekh6akFHR2R3NXhBS2lZZ0JpZmxhdERwUW1FNE9IV01uOCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHBzOi8vaW50ZXJuYWwubWFpa2VsY2Fycy5jb20vbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1773493811),
('vcQuorzB82iHLtxZDGxlF8XqFrayVmg02gwd2BSN', NULL, '45.148.10.187', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibDQ1RHB2QWJWajFlb2ZneDdNSFFWeWdsVmlSdFBFN2lWM0VHdXFNbiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vaW50ZXJuYWwubWFpa2VsY2Fycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1773493810);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `rol` varchar(255) NOT NULL DEFAULT 'user',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `two_factor_secret` text DEFAULT NULL,
  `two_factor_recovery_codes` text DEFAULT NULL,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `current_team_id` bigint(20) UNSIGNED DEFAULT NULL,
  `profile_photo_path` varchar(2048) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `rol`, `email_verified_at`, `password`, `two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`, `remember_token`, `current_team_id`, `profile_photo_path`, `created_at`, `updated_at`) VALUES
(1, 'Yordy Jimenez', 'yordyalejandro13@gmail.com', 'SUPERUSUARIO', NULL, '$2y$12$6ewUVsIlRZKBQj83f.3n/O9.WUeZ1RC8leflOcYweO/Vj5NwiGNou', NULL, NULL, NULL, 'jPHVHlbDwdveWaxU7DkwoPL6wlJZSudPzZOgBAU2QlnLudogveo3FQDfmGcP', NULL, NULL, '2024-03-09 20:30:45', '2024-03-09 20:30:45'),
(2, 'ASNEIDY', 'benitezasneidy@gmail.com', 'ADMINISTRADOR', NULL, '$2y$12$LRqLfd8nqgwbipCDIYhECOTCSSF8nA8GI4TazkVVB79irsYODfsz6', NULL, NULL, NULL, 'qzHqhd2TOYvoBiNgiNuIzSd9go1utB6ZMNkaIQpgrQ5ingO2CDEgPtdFdB9I', NULL, NULL, '2024-03-28 02:21:09', '2024-03-28 02:21:09'),
(3, 'Raiza Cordero', 'corderoraizae@gmail.com', 'FACTURACION', NULL, '$2y$12$UYaPGOwkFQmldDUTNovpReycPfiVsa.ulfBYTPiE.yX58itiSLMlu', NULL, NULL, NULL, 'nM3eRo1a1U1fnbjL9s7WvtTm7BcYbxtSsKEmMIP7Y9NPG7eI6zvqFOM49FOB', NULL, NULL, '2024-03-28 02:21:09', '2024-03-28 02:21:09'),
(5, 'JOSE SIERRA', 'josesierraaes@gmail.com', 'user', NULL, '$2y$12$JIBH4FsGSR86UaTzIe6CCuI4OVXJWPosbfeb2YkeW9cKipDL65Bye', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-01 01:07:10', '2026-02-01 01:07:10');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `accesorio_engines`
--
ALTER TABLE `accesorio_engines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `accesorio_engines_maintenances_id_foreign` (`maintenances_id`);

--
-- Indices de la tabla `billings`
--
ALTER TABLE `billings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `billings_partida_id_foreign` (`partida_id`),
  ADD KEY `billings_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `billing_requests`
--
ALTER TABLE `billing_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `billing_requests_partida_id_foreign` (`partida_id`),
  ADD KEY `billing_requests_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `bitacoras`
--
ALTER TABLE `bitacoras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bitacoras_users_id_foreign` (`users_id`);

--
-- Indices de la tabla `containers`
--
ALTER TABLE `containers`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `exchange_rates`
--
ALTER TABLE `exchange_rates`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `inventarios`
--
ALTER TABLE `inventarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `partidas_codinv_unique` (`codInv`),
  ADD KEY `partidas_container_id_foreign` (`container_id`);

--
-- Indices de la tabla `maintenances`
--
ALTER TABLE `maintenances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `maintenances_partida_id_foreign` (`partida_id`);

--
-- Indices de la tabla `maintenance_bills`
--
ALTER TABLE `maintenance_bills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `maintenance_bills_maintenances_id_foreign` (`maintenances_id`);

--
-- Indices de la tabla `maintenance_items`
--
ALTER TABLE `maintenance_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `maintenance_items_maintenance_id_foreign` (`maintenance_id`);

--
-- Indices de la tabla `maintenance_teams`
--
ALTER TABLE `maintenance_teams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `maintenance_teams_maintenance_id_foreign` (`maintenance_id`),
  ADD KEY `maintenance_teams_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `materials`
--
ALTER TABLE `materials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `materials_maintenances_id_foreign` (`maintenances_id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indices de la tabla `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indices de la tabla `permission_expirations`
--
ALTER TABLE `permission_expirations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `permission_expirations_model_type_model_id_index` (`model_type`,`model_id`),
  ADD KEY `permission_expirations_expires_at_index` (`expires_at`);

--
-- Indices de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indices de la tabla `reverse_bills`
--
ALTER TABLE `reverse_bills`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indices de la tabla `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `accesorio_engines`
--
ALTER TABLE `accesorio_engines`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `billings`
--
ALTER TABLE `billings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `billing_requests`
--
ALTER TABLE `billing_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `bitacoras`
--
ALTER TABLE `bitacoras`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT de la tabla `containers`
--
ALTER TABLE `containers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `exchange_rates`
--
ALTER TABLE `exchange_rates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inventarios`
--
ALTER TABLE `inventarios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=661;

--
-- AUTO_INCREMENT de la tabla `maintenances`
--
ALTER TABLE `maintenances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `maintenance_bills`
--
ALTER TABLE `maintenance_bills`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `maintenance_items`
--
ALTER TABLE `maintenance_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `maintenance_teams`
--
ALTER TABLE `maintenance_teams`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `materials`
--
ALTER TABLE `materials`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT de la tabla `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `permission_expirations`
--
ALTER TABLE `permission_expirations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reverse_bills`
--
ALTER TABLE `reverse_bills`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `accesorio_engines`
--
ALTER TABLE `accesorio_engines`
  ADD CONSTRAINT `accesorio_engines_maintenances_id_foreign` FOREIGN KEY (`maintenances_id`) REFERENCES `maintenances` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `billings`
--
ALTER TABLE `billings`
  ADD CONSTRAINT `billings_partida_id_foreign` FOREIGN KEY (`partida_id`) REFERENCES `inventarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `billings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `billing_requests`
--
ALTER TABLE `billing_requests`
  ADD CONSTRAINT `billing_requests_partida_id_foreign` FOREIGN KEY (`partida_id`) REFERENCES `inventarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `billing_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `bitacoras`
--
ALTER TABLE `bitacoras`
  ADD CONSTRAINT `bitacoras_users_id_foreign` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `inventarios`
--
ALTER TABLE `inventarios`
  ADD CONSTRAINT `partidas_container_id_foreign` FOREIGN KEY (`container_id`) REFERENCES `containers` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `maintenances`
--
ALTER TABLE `maintenances`
  ADD CONSTRAINT `maintenances_partida_id_foreign` FOREIGN KEY (`partida_id`) REFERENCES `inventarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `maintenance_bills`
--
ALTER TABLE `maintenance_bills`
  ADD CONSTRAINT `maintenance_bills_maintenances_id_foreign` FOREIGN KEY (`maintenances_id`) REFERENCES `maintenances` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `maintenance_items`
--
ALTER TABLE `maintenance_items`
  ADD CONSTRAINT `maintenance_items_maintenance_id_foreign` FOREIGN KEY (`maintenance_id`) REFERENCES `maintenances` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `maintenance_teams`
--
ALTER TABLE `maintenance_teams`
  ADD CONSTRAINT `maintenance_teams_maintenance_id_foreign` FOREIGN KEY (`maintenance_id`) REFERENCES `maintenances` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `maintenance_teams_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `materials`
--
ALTER TABLE `materials`
  ADD CONSTRAINT `materials_maintenances_id_foreign` FOREIGN KEY (`maintenances_id`) REFERENCES `maintenances` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
