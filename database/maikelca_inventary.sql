-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 18-12-2025 a las 07:42:04
-- Versión del servidor: 5.7.23-23
-- Versión de PHP: 8.1.33

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
  `valve_cover` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `chain_cover` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `carter` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pescador` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `big` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `iva` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bs` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value_divisa` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `divisa` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `precio_total` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `igtf` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `numero_factura` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_control` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_nota_credito` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_factura_afect` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bitacoras`
--

CREATE TABLE `bitacoras` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `users_id` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `bitacoras`
--

INSERT INTO `bitacoras` (`id`, `users_id`, `action`, `description`, `created_at`, `updated_at`) VALUES
(1, 3, 'DELETE', 'Factura: 1 TOYOTA 4A', '2024-04-19 16:41:36', '2024-04-19 16:41:36');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `containers`
--

CREATE TABLE `containers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cod` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expediente` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tlf` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `maintenances`
--

CREATE TABLE `maintenances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('EN ESPERA','EN PROCESO','TERMINADO','NO SE PUDO CONTINUAR') COLLATE utf8mb4_unicode_ci NOT NULL,
  `partida_id` bigint(20) UNSIGNED NOT NULL,
  `cedula_mecanico` int(11) NOT NULL,
  `nombre_mecanico` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido_mecanico` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `observaciones` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `multi_tools` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `multi_equipament` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mechanic` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mechanic_assistant` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seller` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seller_assistant` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cleaning` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `drinking_water` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `consumables` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `camera_technician` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `camera_technical_assistant` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `forklift_driver` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `concha_biela` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `concha_bancada` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `anillos` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `empacadura_camara` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `empacadura_carter` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kit_empacaduras` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `baño_quimico` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `goma_valvula` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `planos` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valvulas` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rectificadora` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `asientos` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `camisas` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `levas` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pistones` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
(14, '2024_03_21_131236_create_billings_table', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `partidas`
--

CREATE TABLE `partidas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `item` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `marca` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `modelo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `año` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codInv` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expediente` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `condicion` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price_sale` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `container_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `partidas`
--

INSERT INTO `partidas` (`id`, `item`, `tipo`, `marca`, `modelo`, `año`, `codInv`, `expediente`, `condicion`, `status`, `price`, `price_sale`, `container_id`, `created_at`, `updated_at`) VALUES
(2, '3/4 Chevrolet Vitara J18', 'MOTOR 3/4', 'Chevrolet', 'Vitara J18', '2002', 'D0128', '060325', 'APLICA', 'DISPONIBLE', '700', '700', 18, '2025-03-06 23:15:49', '2025-03-06 23:15:49'),
(3, 'Hyundai Getz G4ED', 'MOTOR 3/4', 'Hyundai', 'Getz G4ED', '2005', 'D0115', '060325', 'APLICA', 'DISPONIBLE', '500', '500', 18, '2025-03-06 23:17:44', '2025-03-06 23:17:44'),
(4, 'Ford Festiva / Turpial', 'MOTOR COMPLETO', 'Ford', 'Festiva/ turpial', '2007', 'D0071', '060325', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 18, '2025-03-06 23:20:38', '2025-03-06 23:20:38'),
(8, 'Motor Ford Festiva, Turpial 1.3L Nuevo', 'MOTOR COMPLETO', 'Ford', 'Festiva, Turpial', '2000-2007', 'D0348', '060325', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 18, '2025-03-10 21:24:50', '2025-03-10 21:24:50'),
(9, 'Ford Festiva, Turpial', 'MOTOR COMPLETO', 'Ford', 'Festiva, Turpial', '2000-2007', '420', '060325', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 18, '2025-03-10 21:31:07', '2025-03-18 23:56:03'),
(10, 'Motor 7/8 Toyota 4A 1.6L', 'MOTOR 3/4', 'Toyota', 'Avila, Sapito', '1998', 'D0034', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-10 21:44:49', '2025-03-10 21:44:49'),
(11, 'Motor 3/4 Chevrolet Optra 2.0L tapa Negra', 'MOTOR 3/4', 'Chevrolet', 'Optra tapa negra Limité', '2005', '412', '060325', 'APLICA', 'DISPONIBLE', '800', '800', 18, '2025-03-10 21:46:49', '2025-03-10 21:46:49'),
(12, 'Toyota 3VZ', 'MOTOR 3/4', 'Toyota', '3VZ', '2000', '16', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-10 21:48:11', '2025-03-18 23:37:37'),
(13, 'Motor 3/4 Ford 4.6L 3V BA', 'MOTOR 3/4', 'Ford', 'Explorer 3V', '2006', '488', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-10 21:49:43', '2025-03-10 21:49:43'),
(14, 'Motor 3/4 Toyota 2ZR Corolla 2016', 'MOTOR 3/4', 'Toyota', '2ZR', '2016', 'D0004', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-10 21:51:09', '2025-03-10 21:51:09'),
(15, 'Motor 3/4 Chevrolet Cruz 1.8L', 'MOTOR 3/4', 'Chevrolet', 'Cruz 1.8L', '2010', '567', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-11 00:32:43', '2025-03-11 00:32:43'),
(16, 'Motor 7/8 Nissan QR35', 'MOTOR 7/8', 'Nissan', 'QR35', '2000', 'D0412', '060325', 'APLICA', 'DISPONIBLE', '1.200', '1.200', 18, '2025-03-11 00:34:21', '2025-03-11 00:34:21'),
(17, 'Motor 3/4 Hyundai Santa Fe 2.7L', 'MOTOR 3/4', 'Hyundai', 'Santa Fe 2.7L', '2006', 'D0019', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-11 00:35:59', '2025-03-11 00:35:59'),
(18, 'Motor 7/8 Ford 4.2L 6V Fortaleza', 'MOTOR 7/8', 'Ford', 'Fortaleza 4.2L', '2005', '439', '240001', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 16, '2025-03-11 00:57:37', '2025-03-11 00:57:37'),
(19, 'Motor 7/8 Honda K20A3', 'MOTOR 7/8', 'Honda', 'K20A3', '2000', 'D0394', '060325', 'APLICA', 'DISPONIBLE', '1.200', '1.200', 18, '2025-03-11 01:03:03', '2025-03-11 01:03:03'),
(20, 'Motor completo Ford 3.0L Carburado', 'MOTOR COMPLETO', 'Ford', '3.0L para adaptar Nuevo', '1990', 'D0393', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-11 01:04:13', '2025-03-11 01:04:13'),
(21, 'Motor 7/8 Ford Coyote 5.0L', 'MOTOR 7/8', 'Ford', 'Coyote 5.0L', '2016', '467', '240001', 'APLICA', 'DISPONIBLE', '3.500', '3.500', 16, '2025-03-11 01:05:42', '2025-03-11 01:05:42'),
(22, 'Ford Mazda 2.3L', 'MOTOR 7/8', 'Ford', 'Mazda 2.3L', '2007', 'D0086', '210262', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 9, '2025-03-11 01:07:55', '2025-03-18 23:44:18'),
(23, 'Motor 7/8 Ford Fusion 3.0L', 'MOTOR 7/8', 'Ford', 'Fisio 3.0L', '2007', 'D0331', '060325', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 18, '2025-03-11 01:10:58', '2025-03-11 01:10:58'),
(24, 'Motor 7/8 Chevrolet Optra Limite', 'MOTOR 7/8', 'Chevrolet', 'Optra 1.8L limite', '2006', '465', '240001', 'APLICA', 'DISPONIBLE', '1.200', '1.200', 16, '2025-03-11 01:22:29', '2025-03-11 01:22:29'),
(25, 'Motor 7/8 Hyundai G4ED', 'MOTOR 7/8', 'Hyundai', 'Accel, Río, Getz G4ED', '2008', 'D0708', '230145', 'APLICA', 'DISPONIBLE', '1.200', '1.200', 14, '2025-03-11 01:26:54', '2025-03-11 01:26:54'),
(26, 'Motor 3/4 Mazda 6 2.3L', 'MOTOR 3/4', 'Ford', 'Mazda 6 2.3L', '2006', 'D0343', '210262', 'APLICA', 'DISPONIBLE', '800', '800', 9, '2025-03-11 01:30:57', '2025-03-11 01:30:57'),
(27, 'Motor 7/8 Kia Espectra', 'MOTOR 7/8', 'Kia', 'Espectra', '2002', 'D0383', '220282', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 11, '2025-03-11 01:33:36', '2025-03-11 01:33:36'),
(28, 'Motor 7/8 Chevrolet 5.3L taquetes Inteligente', 'MOTOR 7/8', 'Chevrolet', 'Silverado, Tahoe, Avalancha', '2007', '441', '060325', 'APLICA', 'DISPONIBLE', '1.800', '1.800', 18, '2025-03-11 01:34:58', '2025-03-11 01:34:58'),
(29, 'Motor 7/8 Toyota 2UZ 4.7L VVTI', 'MOTOR 7/8', 'Toyota', 'Tundra 4.7L', '2007', '523', '240001', 'APLICA', 'DISPONIBLE', '3.500', '3.500', 16, '2025-03-11 01:36:51', '2025-03-11 01:36:51'),
(30, 'Motor 7/8 Jeep Grand Cherokee 4.7 8B EGR', 'MOTOR 7/8', 'Jeep', 'Grand Cherokee', '2007', 'D0362', '220329', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 12, '2025-03-11 01:40:10', '2025-03-11 01:40:10'),
(31, 'Motor 7/8 Jeep Cherokee 3.7L KJ', 'MOTOR 7/8', 'Jeep', 'Liberty 3.7L KJ', '2005', 'D0476', '220282', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 11, '2025-03-11 02:18:47', '2025-03-11 02:18:47'),
(32, 'Motor 7/8 Toyota 4A 1.6L', 'MOTOR 7/8', 'Toyota', '4A 1.6L', '1998', 'D0050', '060325', 'APLICA', 'DISPONIBLE', '1.100', '1.100', 18, '2025-03-11 02:20:17', '2025-03-11 02:22:11'),
(33, 'Motor 7/8 Toyota 4A 1.6L', 'MOTOR 7/8', 'Toyota', '4A 1.6L', '1998', '415', '060325', 'APLICA', 'DISPONIBLE', '1.100', '1.100', 18, '2025-03-11 02:21:31', '2025-03-11 02:21:31'),
(34, 'Motor 7/8 Kia Caren G4KC', 'MOTOR 7/8', 'Hyundai', 'G4KC', '2007', '418', '060325', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 18, '2025-03-11 02:26:58', '2025-03-11 02:26:58'),
(35, 'Motor 7/8 Chevrolet Traibleizer 4.2L', 'MOTOR 7/8', 'Chevrolet', 'Traibleizer 4.2L tapa Plástica', '2005', '325', '240001', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 16, '2025-03-11 02:29:39', '2025-03-11 02:29:39'),
(36, 'Motor 7/8 Toyota 2GR- Camry', 'MOTOR 7/8', 'Toyota', '2GR- Camry', '2008', '487', '060325', 'APLICA', 'DISPONIBLE', '2.100', '2.100', 18, '2025-03-11 02:31:23', '2025-03-11 02:31:23'),
(37, 'Motor 7/8 Hyundai Sonata G4NG', 'MOTOR 7/8', 'Hyundai', 'G4NG', '2010', '435', '060325', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 18, '2025-03-11 02:33:53', '2025-03-11 02:33:53'),
(38, 'Motor 7/8 Ford Escape 3.0L', 'MOTOR 7/8', 'Ford', 'Escape 3.0L tapa Plástico', '2002', '446', '060325', 'APLICA', 'DISPONIBLE', '1.200', '1.200', 18, '2025-03-11 02:34:50', '2025-03-11 02:34:50'),
(39, 'Motor 7/8 Chevrolet 5.3L 2010', 'MOTOR 7/8', 'Chevrolet', '5.3L 2010 taquete inteligente', '2010', '427', '060325', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 18, '2025-03-11 02:35:59', '2025-03-11 02:35:59'),
(40, 'Motor 7/8 Chevrolet 5.3L 2010', 'MOTOR 7/8', 'Chevrolet', '5.3L Taquete Inteligente', '2010', '422', '060325', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 18, '2025-03-11 02:36:58', '2025-03-11 02:36:58'),
(41, 'Motor 7/8 Jeep Grand Cherokee 5.7L 4G', 'MOTOR 7/8', 'Jeep', 'Grand Cherokee', '2012', '157', '240001', 'APLICA', 'DISPONIBLE', '2.800', '2.800', 16, '2025-03-11 02:38:19', '2025-03-11 02:38:19'),
(42, 'Motor 7/8 Jeep 3.7L Cherokee Liberty', 'MOTOR 7/8', 'Jeep', 'Liberty', '2005', '443', '220282', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 11, '2025-03-11 02:39:32', '2025-03-11 02:39:32'),
(43, 'Motor 7/8 Jeep Cherokee 3.7L KK', 'MOTOR 7/8', 'Jeep', 'Cherokee  3.7L KK', '2008', '321', '240001', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 16, '2025-03-11 02:41:30', '2025-03-11 02:41:30'),
(44, 'Motor 7/8 Jeep Dodge Ram 5.7L', 'MOTOR 7/8', 'Jeep', 'Dodge Ram', '2008', '546', '230319', 'APLICA', 'DISPONIBLE', '1.800', '1.800', 15, '2025-03-11 02:43:21', '2025-03-11 02:43:21'),
(45, 'Motor 7/8 Chevrolet Colorado Tapa Plástica', 'MOTOR 7/8', 'Chevrolet', 'Colorado 3.7L tapa Plástico', '2005', '474', '240001', 'APLICA', 'DISPONIBLE', '1.800', '1.800', 16, '2025-03-11 02:45:17', '2025-03-11 02:45:17'),
(46, 'Motor 7/8 Hyundai G4KJ Sorento, Santa Fe', 'MOTOR 7/8', 'Hyundai', 'Sorento Santa fe', '2010-2015', '442', '240001', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 16, '2025-03-11 02:50:26', '2025-03-11 02:50:26'),
(47, 'Motor 7/8 Jeep Rubicon 3.6L', 'MOTOR 7/8', 'Jeep', 'Rubicon', '2015-2018', '440', '230145', 'APLICA', 'DISPONIBLE', '4.000', '4.000', 14, '2025-03-11 02:52:18', '2025-03-11 02:52:18'),
(48, 'Motor Hyundai Santa Fe 2.4L G4JS', 'MOTOR 7/8', 'Hyundai', 'G4JS', '2002-2006', '426', '230145', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 14, '2025-03-11 02:55:23', '2025-03-11 02:55:23'),
(49, 'Motor 7/8 Jeep Grand Cherokee 5.7L 4G', 'MOTOR 7/8', 'Jeep', 'Grand Cherokee 5.7L 4G', '2012', '444', '240001', 'APLICA', 'DISPONIBLE', '2.800', '2.800', 16, '2025-03-11 02:59:08', '2025-03-11 02:59:08'),
(50, 'Motor 7/8 Jeep Grand Cherokee 5.7L 4G', 'MOTOR 7/8', 'Jeep', 'Grand Cherokee 5.7L 4G', '2012', '421', '240001', 'APLICA', 'DISPONIBLE', '2.800', '2.800', 16, '2025-03-11 03:00:33', '2025-03-11 03:00:33'),
(51, 'Motor 7/8 Jeep 4.7L 16 B', 'MOTOR 7/8', 'Jeep', 'Grand Cherokee 4.6L 16 B', '2009', '568', '240001', 'APLICA', 'DISPONIBLE', '2.200', '2.200', 16, '2025-03-11 03:05:58', '2025-03-11 03:05:58'),
(52, 'Motor 7/8 Volkswagen Beta', 'MOTOR 7/8', 'Volkswagen', 'Beta', '2008', '485', '240001', 'APLICA', 'DISPONIBLE', '2.500', '2.500', 16, '2025-03-11 03:08:39', '2025-03-11 03:08:39'),
(53, 'Motor 7/8 Honda R18 Emocio', 'MOTOR 7/8', 'Honda', 'R18', '2005', '160', '060325', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 18, '2025-03-11 03:10:38', '2025-03-11 03:10:38'),
(54, 'Motor 7/8 Chevrolet Cruce 1.8L', 'MOTOR COMPLETO', 'Chevrolet', 'Cruce 1.8L', '2010', '503', '240001', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 16, '2025-03-11 14:21:20', '2025-03-11 14:21:20'),
(55, 'Motor Nissan QR25', 'MOTOR COMPLETO', 'Nissan', 'QR25', '2015', '37', '210262', 'APLICA', 'DISPONIBLE', '1.800', '1.800', 9, '2025-03-11 14:23:44', '2025-03-11 14:23:44'),
(56, 'Motor Hyundai Accel 1.6L', 'MOTOR COMPLETO', 'Hyundai', 'Accel, Getz, Río 1.6L', '2008', '498', '240001', 'APLICA', 'DISPONIBLE', '1.300', '1.300', 16, '2025-03-11 14:24:48', '2025-03-11 14:24:48'),
(57, 'Motor 7/8 Jeep Caliber 2.0L', 'MOTOR 7/8', 'Jeep', 'Caliber 2.0L', '2006', '10', '220282', 'APLICA', 'DISPONIBLE', '1.300', '1.300', 11, '2025-03-11 14:25:52', '2025-03-11 14:25:52'),
(58, 'Motor Hyundai G4BA 2.7L', 'MOTOR COMPLETO', 'Hyundai', 'Santa Fe 2.7L VVTI', '2010', '520', '230145', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 14, '2025-03-11 14:27:07', '2025-03-11 14:27:07'),
(59, 'Motor Jeep Dodge 2.0L tapa Aluminio', 'MOTOR COMPLETO', 'Jeep', 'Dodge Tapa de aluminio', '2010', '518', '230145', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 14, '2025-03-11 14:28:28', '2025-03-11 14:28:28'),
(60, 'Motor Hyundai 2.4L G4KE', 'MOTOR COMPLETO', 'Hyundai', 'G4KE', '2008', '502', '240001', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 16, '2025-03-11 14:29:36', '2025-03-11 14:29:36'),
(61, 'Motor Ford Escape 3.0L tapa Platica', 'MOTOR COMPLETO', 'Ford', 'Escape 3.0L tapa de aluminio', '2004', '499', '230319', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 15, '2025-03-11 14:30:46', '2025-03-11 14:30:46'),
(62, 'Motor 7/8 Mitsubishi 6G74', 'MOTOR COMPLETO', 'Mitsubishi', '6G74', '2008', '6', '220101', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 10, '2025-03-11 14:39:11', '2025-03-11 14:39:11'),
(63, 'Motor Mazda 2.3L', 'MOTOR COMPLETO', 'Ford', 'Mazda 2.3L', '2008', '81', '230145', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 14, '2025-03-11 14:40:47', '2025-03-11 14:40:47'),
(64, 'Motor Kia G4KH Turbo', 'MOTOR COMPLETO', 'Kia', 'G4KH', '2010', '515', '230145', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 14, '2025-03-11 14:42:06', '2025-03-11 14:42:06'),
(65, 'Motor Chevrolet Impala 5.3L', 'MOTOR COMPLETO', 'Chevrolet', 'Impala 5.3L', '2010', '501', '230319', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 15, '2025-03-11 14:46:17', '2025-03-11 14:46:17'),
(66, 'Motor Hyundai Elantra G4NB', 'MOTOR COMPLETO', 'Hyundai', 'Elantra G4NB', '2015', '511', '230319', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 15, '2025-03-11 14:47:31', '2025-03-11 14:47:31'),
(67, 'Motor Mitsubishi Montero 3.5L', 'MOTOR COMPLETO', 'Mitsubishi', 'Montero G474 3.5L', '2008', '77', '210262', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 9, '2025-03-11 14:49:15', '2025-03-11 14:49:15'),
(68, 'Motor Kia Caren 6DKD', 'MOTOR COMPLETO', 'Kia', 'Caren 6DKD', '2008', '82', '240001', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 16, '2025-03-11 14:50:36', '2025-03-11 14:50:36'),
(69, 'Motor Hyundai G4FD', 'MOTOR COMPLETO', 'Hyundai', 'G4FD', '2010', '513', '230145', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 14, '2025-03-11 15:47:04', '2025-03-11 15:47:04'),
(70, 'Motor Malibú 2.5L BT8', 'MOTOR COMPLETO', 'Chevrolet', 'Malibú', '2015', '274', '230058', 'APLICA', 'DISPONIBLE', '2.500', '2.500', 13, '2025-03-11 15:50:35', '2025-03-11 15:50:35'),
(71, 'Motor Dodge Caliber 2.4L', 'MOTOR COMPLETO', 'Dodge', 'Caliber 2.4L', '2008', '508', '230145', 'APLICA', 'DISPONIBLE', '1.300', '1.300', 14, '2025-03-11 15:51:46', '2025-03-11 15:51:46'),
(72, 'Motor Jeep 3.7L Cherokee Liberty', 'MOTOR COMPLETO', 'Jeep', 'Cherokee  3.7L KJ', '2005', '320', '230145', 'APLICA', 'DISPONIBLE', '1.300', '1.300', 14, '2025-03-11 15:54:10', '2025-03-11 15:54:10'),
(73, 'Motor Hyundai G4KD 2.4L', 'MOTOR COMPLETO', 'Hyundai', 'G4KD 2.4L', '2008', '517', '230145', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 14, '2025-03-11 15:59:59', '2025-03-11 15:59:59'),
(74, 'Motor Ford Fortaleza 4.6L 2V', 'MOTOR COMPLETO', 'Ford', 'Fortaleza 4.6L 2V', '2002', '8', '210262', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 9, '2025-03-11 16:01:10', '2025-03-11 16:01:10'),
(75, 'Motor Ford Tritón 5.4L 2V', 'MOTOR COMPLETO', 'Ford', 'Tritón 5.4L 2v', '2005', '357', '240001', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 16, '2025-03-11 16:02:42', '2025-03-11 16:02:42'),
(76, 'Motor Hyundai Accel 1.6L', 'MOTOR COMPLETO', 'Hyundai', 'Accel 1.6L', '2008', '510', '230319', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 15, '2025-03-11 16:04:31', '2025-03-11 16:04:31'),
(77, 'Motor Jeep Grand Cherokee 4.7L 8B EGR', 'MOTOR COMPLETO', 'Jeep', 'Grand Cherokee 4.7L 8B EGR', '2008', '13', '220282', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 11, '2025-03-11 16:09:06', '2025-03-11 16:09:06'),
(78, 'Motor Hyundai Elantra G4NB', 'MOTOR COMPLETO', 'Hyundai', 'Elantra G4NB', '2015', '491', '230319', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 15, '2025-03-11 16:24:02', '2025-03-11 16:24:02'),
(79, 'Motor Hyundai Sonata 3.3L', 'MOTOR COMPLETO', 'Hyundai', 'Sonata 3.3L', '2008', '281', '240001', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 16, '2025-03-11 16:31:08', '2025-03-11 16:31:08'),
(80, 'Motor Ford Escapé 3.0L Tapa Plástica', 'MOTOR COMPLETO', 'Ford', 'Escape 3.0L Tapa Plástico', '2002', '519', '230145', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 14, '2025-03-11 16:33:43', '2025-03-11 16:33:43'),
(81, 'Motor Hyundai G4KE', 'MOTOR COMPLETO', 'Hyundai', 'G4KE', '2015', '512', '230145', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 14, '2025-03-11 16:34:33', '2025-03-11 16:34:33'),
(82, 'Motor Chevrolet trailer Tapa Plástico', 'MOTOR COMPLETO', 'Chevrolet', 'Traibleizer 4.2L  Tapa Plástica', '2005', '497', '230145', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 14, '2025-03-11 16:37:52', '2025-03-11 16:37:52'),
(83, 'Motor Hyundai Accel 1.6L', 'MOTOR COMPLETO', 'Hyundai', 'Accel, Getz, Rio 1.6L', '2008', '504', '230319', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 15, '2025-03-11 16:42:26', '2025-03-11 16:42:26'),
(84, 'Motor Nissan 12 valvulad', 'MOTOR COMPLETO', 'Hyundai', 'KA24 MV', '1995', '529', '060325', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 18, '2025-03-11 16:43:29', '2025-03-11 16:43:29'),
(85, 'Motor Ford Tritón 5.4L 2V', 'MOTOR COMPLETO', 'Ford', 'Tritón 5.4L 2v', '2000', '45', '230058', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 13, '2025-03-11 23:10:39', '2025-03-12 14:20:56'),
(86, 'Motor Ford Tritón 5.4L 2V', 'MOTOR COMPLETO', 'Ford', 'Tritón 5.4L 2v', '2005', '317', '230058', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 13, '2025-03-11 23:12:23', '2025-03-11 23:12:23'),
(87, 'Motor Jeep 4.7L 16Bujias', 'MOTOR COMPLETO', 'Jeep', '4.7L 16Bujias.', '2009', '296', '240001', 'APLICA', 'DISPONIBLE', '2.500', '2.500', 16, '2025-03-12 14:22:20', '2025-03-12 14:22:20'),
(88, 'Motor Ford 4.6L 2V', 'MOTOR COMPLETO', 'Ford', 'Grand Marquiz', '2000', '493', '230319', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 15, '2025-03-12 14:26:43', '2025-03-12 14:26:43'),
(89, 'Motor Toyota 3VZ 3.4L', 'MOTOR COMPLETO', 'Toyota', '3VZ', '1992', '287', '230319', 'APLICA', 'DISPONIBLE', '1.800', '1.800', 15, '2025-03-12 14:29:05', '2025-03-12 14:29:05'),
(90, 'Motor Toyota 5VZ 3.4L', 'MOTOR COMPLETO', 'Toyota', '5VZ Prado, Runner', '1995', '305', '240001', 'APLICA', 'DISPONIBLE', '1.800', '1.800', 16, '2025-03-12 14:30:06', '2025-03-12 14:30:06'),
(91, 'Motor Chevrolet Traibleizer 4.2L TP', 'MOTOR COMPLETO', 'Chevrolet', 'Traibleizer 4.2L Tapa Plástico', '2005', '359', '230058', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 13, '2025-03-12 14:31:10', '2025-03-12 14:31:10'),
(92, 'Motor Chevrolet trailer Tapa Plástico', 'MOTOR COMPLETO', 'Chevrolet', 'Traibleizer 4.2L Tapa Plástico', '2005', '360', '230058', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 13, '2025-03-12 14:32:06', '2025-03-12 14:32:06'),
(93, 'Motor Jeep 3.7L KK', 'MOTOR COMPLETO', 'Jeep', 'Cherokee  3.7L KK', '2008', '356', '230319', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 15, '2025-03-12 14:33:13', '2025-03-12 14:33:13'),
(94, 'Motor Jeep 3.7L KK', 'MOTOR COMPLETO', 'Jeep', 'Cherokee  3.7L KK', '2008', '303', '230319', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 15, '2025-03-12 14:35:51', '2025-03-12 14:35:51'),
(95, 'Motor Chevrolet 262 Vortec', 'MOTOR COMPLETO', 'Chevrolet', '262 Tipo Vortec', '1998', '298', '240001', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 16, '2025-03-12 14:37:05', '2025-03-12 14:37:05'),
(96, 'Motor Jeep 4.7L 8B EGR', 'MOTOR COMPLETO', 'Jeep', 'Grand Cherokee 4.7L 8B EGR', '2006', '295', '230145', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 14, '2025-03-12 14:39:17', '2025-03-12 14:39:17'),
(97, 'Motor Chevrolet Vitara H25', 'MOTOR COMPLETO', 'Chevrolet', 'Grand Vitara XL5', '2005', '340', '230145', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 14, '2025-03-12 14:40:20', '2025-03-12 14:40:20'),
(98, 'Motor Chevrolet Vitara H25', 'MOTOR COMPLETO', 'Chevrolet', 'Grand Vitara XL5', '2005', '530', '240001', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 16, '2025-03-12 14:41:23', '2025-03-12 14:41:23'),
(99, 'Motor Chevrolet Vitara 4 cilindros', 'MOTOR COMPLETO', 'Chevrolet', 'Vitara 4 cilindros', '1992', '339', '060325', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 18, '2025-03-12 14:43:11', '2025-03-12 14:43:11'),
(100, 'Motor Chevrolet 262 Vortec', 'MOTOR COMPLETO', 'Chevrolet', '262 Vortec', '1998', '311', '240001', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 16, '2025-03-12 14:45:04', '2025-03-12 14:45:04'),
(101, 'Motor Jeep 4.7L 8 B EGR', 'MOTOR COMPLETO', 'Jeep', 'Grand Cherokee 4.7L 8B', '1600', '1600', '240001', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 16, '2025-03-12 14:46:47', '2025-03-12 14:46:47'),
(102, 'Motor Jeep Grand Cherokee 4.7L 8B EGR', 'MOTOR COMPLETO', 'Jeep', 'Grand Cherokee', '2007', '594', '240001', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 16, '2025-03-12 14:48:17', '2025-03-12 14:48:17'),
(103, 'Motor Chevrolet 262 Vortec', 'MOTOR COMPLETO', 'Chevrolet', 'Vortec', '1400', '297', '220282', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 11, '2025-03-12 14:49:45', '2025-03-12 14:49:45'),
(104, 'Motor Chevrolet Rey Camión 6.0L', 'MOTOR COMPLETO', 'Chevrolet', 'Rey Camion 6.0L', '2010', '653', '240001', 'APLICA', 'DISPONIBLE', '2.500', '2.500', 16, '2025-03-12 14:58:48', '2025-03-12 14:58:48'),
(105, 'Motor Chevrolet 4.8L Nuevo', 'MOTOR COMPLETO', 'Chevrolet', 'Silverado 4.8L', '2008', '456', '230058', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 13, '2025-03-12 14:59:57', '2025-03-12 14:59:57'),
(106, 'Motor Toyota 1GR-II Generación', 'MOTOR 7/8', 'Toyota', '1GR-II', '2015', '527', '230058', 'APLICA', 'DISPONIBLE', '5.000', '5.000', 13, '2025-03-12 15:00:55', '2025-03-12 15:00:55'),
(107, 'Motor Chevrolet 5.3L 2003', 'MOTOR COMPLETO', 'Chevrolet', 'Silverado 5.3L', '2003', '468', '230145', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 14, '2025-03-12 15:02:37', '2025-03-12 15:02:37'),
(108, 'Motor 7/8 Chevrolet 5.3L 2010', 'MOTOR 7/8', 'Chevrolet', 'Silverado 5.3L', '2010', '668', '240001', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 16, '2025-03-12 15:04:24', '2025-03-12 15:04:24'),
(109, 'Motor 7/8 Chevrolet 5.3L 2015', 'MOTOR 7/8', 'Chevrolet', 'Silverado 5.3L', '2015', '290', '230145', 'APLICA', 'DISPONIBLE', '3.000', '3.000', 14, '2025-03-12 15:06:01', '2025-03-12 15:06:01'),
(110, 'Motor Ford 4.6L 2V', 'MOTOR 7/8', 'Ford', 'Fortaleza 4.6L 2V', '2005', '492', '230145', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 14, '2025-03-12 15:06:59', '2025-03-12 15:06:59'),
(111, 'Motor 7/8 Hyundai Tucson 2.0L', 'MOTOR 7/8', 'Hyundai', 'Tucson 2.0L', '2006', '479', '230145', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 14, '2025-03-12 15:26:25', '2025-03-12 15:26:25'),
(112, 'Motor 7/8 Chevrolet Traibleizer 4.2L TA', 'MOTOR 7/8', 'Chevrolet', 'Traibleizer 4.2L TA', '2007', '476', '240001', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 16, '2025-03-12 15:27:42', '2025-03-12 15:27:42'),
(113, 'Motor 7/8 Chevrolet Traibleizer 4.2L TA', 'MOTOR 7/8', 'Chevrolet', 'Traibleizer 4.2L tapa Aluminio', '2007', '473', '230145', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 14, '2025-03-12 15:28:52', '2025-03-12 15:28:52'),
(114, 'Motor Chevrolet Traibleizer 4.2L TP', 'MOTOR COMPLETO', 'Chevrolet', 'Traibleizer 4.2L tapa Plástica', '2005', '495', '230058', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 13, '2025-03-12 15:30:23', '2025-03-12 15:30:23'),
(115, 'Motor Chevrolet 5.3 Traibleizer', 'MOTOR COMPLETO', 'Chevrolet', 'Traibleizer 5.3L', '2008', '458', '230319', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 15, '2025-03-12 15:34:00', '2025-03-12 15:34:00'),
(116, 'Motor Chevrolet 5.3L Impala', 'MOTOR COMPLETO', 'Chevrolet', 'IMPALA 5.3L', '2008', '489', '230319', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 15, '2025-03-12 15:35:20', '2025-03-12 15:35:20'),
(117, 'Motor 7/8 Toyota 1ZZ', 'MOTOR 7/8', 'Toyota', 'Corolla 1ZZ', '2010', '250', '230058', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 13, '2025-03-12 16:10:24', '2025-03-12 16:10:24'),
(118, 'Motor 7/8 Toyota 1ZZ', 'MOTOR COMPLETO', 'Toyota', '1ZZ', '2010', '35', '210262', 'APLICA', 'DISPONIBLE', '1.700', '1.700', 9, '2025-03-12 16:11:56', '2025-03-12 16:11:56'),
(119, 'Motor 7/8 Toyota 1ZZ', 'MOTOR 7/8', 'Toyota', '1ZZ', '2010', '425', '230145', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 14, '2025-03-12 16:14:52', '2025-03-12 16:14:52'),
(120, 'Motor 7/8 Dodge Caliber 2.0L', 'MOTOR 7/8', 'Dodge', 'Caliber 2.0L', '2005', '538', '230145', 'APLICA', 'DISPONIBLE', '1.300', '1.300', 14, '2025-03-12 16:23:06', '2025-03-12 16:23:06'),
(121, 'Motor 7/8 Chevrolet Cruz 1.8L', 'MOTOR 7/8', 'Chevrolet', 'Cruze', '2010', '480', '230145', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 14, '2025-03-12 16:23:52', '2025-03-12 16:23:52'),
(122, 'Motor Honda Civic Híbrido', 'MOTOR 7/8', 'Honda', 'Civic', '2000', '406', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-12 16:25:03', '2025-03-12 16:25:03'),
(123, 'Motor 7/8 Toyota 2GR-Tacoma', 'MOTOR 7/8', 'Toyota', '2GR-TACOMA', '2015', '483', '230145', 'APLICA', 'DISPONIBLE', '3.500', '3.500', 14, '2025-03-12 16:26:29', '2025-03-12 16:26:29'),
(124, 'Motor Jeep 6.1 Azul', 'MOTOR 7/8', 'Jeep', '6.1L', '2000', '534', '060325', 'APLICA', 'DISPONIBLE', '3.000', '3.000', 18, '2025-03-12 16:28:21', '2025-03-12 16:28:21'),
(125, 'Motor Chevrolet 5.7L Ls3', 'MOTOR COMPLETO', 'Chevrolet', '5.7L LS3', '2007', '454', '060325', 'APLICA', 'DISPONIBLE', '2.500', '2.500', 18, '2025-03-12 16:29:16', '2025-03-12 16:29:16'),
(126, 'Motor Chevrolet 454 anaranjado', 'MOTOR COMPLETO', 'Chevrolet', '454', '2008', '532', '060325', 'APLICA', 'DISPONIBLE', '4.000', '4.000', 18, '2025-03-12 16:31:49', '2025-03-12 16:31:49'),
(127, 'Hyundai Sorento G6DA 3.8L', 'MOTOR 7/8', 'Hyundai', 'Sonata 3.8L', '2008', 'D0630', '230145', 'APLICA', 'DISPONIBLE', '2.300', '2.300', 14, '2025-03-14 00:15:50', '2025-03-14 00:15:50'),
(128, 'Motor 7/8 Chevrolet 5.3L BA', 'MOTOR 7/8', 'Chevrolet', 'Silverado  BA', '2008', '645', '240001', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 16, '2025-03-14 00:17:34', '2025-03-14 00:17:34'),
(129, 'Motor 7/8 Chevrolet 5.3L Taquete Mecánico', 'MOTOR 7/8', 'Chevrolet', '5.3L', '2005', '1S/C', '240001', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 16, '2025-03-14 00:19:53', '2025-03-14 00:19:53'),
(130, 'Motor 3/4 Ford Tritón 5.4L 2V', 'MOTOR 3/4', 'Ford', '5.4L 2V', '2010', '2S/C', '230145', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 14, '2025-03-14 00:24:03', '2025-03-14 00:24:03'),
(131, 'Motor 7/8 Chevrolet 5.3L 2008', 'MOTOR 7/8', 'Chevrolet', 'Silverado 5.3L', '2008', '3S/C', '230319', 'APLICA', 'DISPONIBLE', '1.900', '1.900', 15, '2025-03-14 00:31:31', '2025-03-14 00:31:31'),
(132, 'Motor 7/8 Toyota 2AZ', 'MOTOR 7/8', 'Toyota', '2AZ', '2008', '544', '230319', 'APLICA', 'DISPONIBLE', '1.700', '1.700', 15, '2025-03-14 00:32:53', '2025-03-14 00:32:53'),
(133, 'Motor Chevrolet 6.2L LS', 'MOTOR COMPLETO', 'Chevrolet', 'Ls 6.2L', '2010', '4S/C', '230319', 'APLICA', 'DISPONIBLE', '4.000', '4.000', 15, '2025-03-14 00:36:43', '2025-03-14 00:36:43'),
(134, 'Motor 7/8 Toyota 4A 1.6L', 'MOTOR 7/8', 'Toyota 4A 1.6L', 'Avila, Sapito', '1995', '5S/C', '060325', 'APLICA', 'DISPONIBLE', '1.100', '1.100', 18, '2025-03-14 00:43:24', '2025-03-14 00:43:24'),
(135, 'Motor 3\\4 Toyota 2ZR', 'MOTOR 3/4', 'TOYOTA', '2ZR VVTI', '2005', '6S/C', '230145', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 14, '2025-03-14 00:46:55', '2025-03-14 16:51:19'),
(136, 'Motor 3\\4 Ford Ecosport 2.0l', 'MOTOR 3/4', 'Ford', 'Ecosport 2.0L', '2015', '7S/C', '230145', 'APLICA', 'DISPONIBLE', '1.300', '1.300', 14, '2025-03-14 00:48:09', '2025-03-14 16:52:45'),
(137, 'Motor 7/8 Jeep Cherokee 3.7L Kj', 'MOTOR 7/8', 'Jeep', '3.7L', '2005', '8S/C', '230145', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 14, '2025-03-14 01:04:03', '2025-03-14 01:04:03'),
(138, 'Motor 7/8 Jeep Grand Cherokee 4.7L 8B EGR', 'MOTOR 7/8', 'Jeep', 'Grand Cherokee 4.7L', '2008', '9 S/C', '240001', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 16, '2025-03-14 01:06:21', '2025-03-14 01:06:21'),
(139, 'Motor 7/8 Jeep Cherokee 3.7L KJ', 'MOTOR 7/8', 'Jeep', 'Cherokee  3.7L KJ', '2005', '10S/C', '230145', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 14, '2025-03-14 01:10:09', '2025-03-14 01:10:09'),
(140, 'Motor 7/8 Chevrolet Colorado TA', 'MOTOR 7/8', 'Chevrolet', 'Colorado 3.7', '2005', '11S/C', '230145', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 14, '2025-03-14 01:14:53', '2025-03-14 01:14:53'),
(141, 'Motor 7/8 Dodge Ram 5.7L', 'MOTOR 7/8', 'Jeep', 'Dodge Ram 5.7L', '2006', '12S/C', '230058', 'APLICA', 'DISPONIBLE', '1.800', '1.800', 13, '2025-03-14 01:26:56', '2025-03-14 01:26:56'),
(142, 'Motor 7/8 Toyota 1NZ Yaris', 'MOTOR 7/8', 'Toyota', '1NZ Yaris', '2005', '13S/C', '230145', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 14, '2025-03-14 14:31:49', '2025-03-14 14:31:49'),
(143, 'Motor Toyota 2AZ Cammry- previa', 'MOTOR COMPLETO', 'Toyota', 'Camrry- Previa', '2008', '14S/C', '230319', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 15, '2025-03-14 14:34:16', '2025-03-14 14:34:16'),
(144, 'Motor 7/8 Hyundai Tucson 2.0L', 'MOTOR 7/8', 'Hyundai', 'Tucson', '2008', '15S/C', '230145', 'APLICA', 'DISPONIBLE', '1.300', '1.300', 14, '2025-03-14 14:37:30', '2025-03-14 14:37:30'),
(145, 'Motor 7/8 Toyota 5VZ 3.4L', 'MOTOR 7/8', 'Toyota', '5VZ 3.4L', '2005', '16S/C', '060325', 'APLICA', 'DISPONIBLE', '1.800', '1.800', 18, '2025-03-14 14:46:08', '2025-03-14 14:46:08'),
(146, 'Motor 3/4 Ford 5.4L 2V', 'MOTOR 7/8', 'Ford', 'Triton', '2006', '17S/C', '240001', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 16, '2025-03-14 14:47:32', '2025-03-14 14:47:32'),
(147, 'Motor Jeep 3.7L KK Hidrido', 'MOTOR COMPLETO', 'Jeep', 'Cherokee  3.7L KK Hidrido', '2008', '18S/C', '230145', 'APLICA', 'DISPONIBLE', '1.800', '1.800', 14, '2025-03-14 14:55:40', '2025-03-14 14:55:40'),
(148, 'Motor 7/8 Ford 5.4L 3V', 'MOTOR 7/8', 'Ford', 'FX4 5.4L 3V', '2008', '19S/C', '230145', 'APLICA', 'DISPONIBLE', '1.800', '1.800', 14, '2025-03-14 15:02:04', '2025-03-14 15:02:04'),
(149, 'Motor 7/8 Ford Tritón 5.4L 2V', 'MOTOR 7/8', 'Ford', '5.4L 2V', '2010', '20S/C', '230145', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 14, '2025-03-14 15:03:48', '2025-03-14 15:03:48'),
(150, 'Motor 7/8 Toyota 2ZR Corolla', 'MOTOR 7/8', 'Toyota 2ZR', 'Corolla', '2016', '21S/C', '230058', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 13, '2025-03-14 15:09:38', '2025-03-14 16:54:22'),
(151, 'Motor 7/8 Chevrolet Vitara XL5', 'MOTOR 7/8', 'Chevrolet', 'Grand Vitara XL5', '2005', '22S/C', '230058', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 13, '2025-03-14 15:10:56', '2025-03-14 15:10:56'),
(152, 'Motor 7/8 Mitsubishi 6G75 3.8L Mivec', 'MOTOR 7/8', 'Mitsubishi', '6G75 3.8L', '2002', '23S/C', '240001', 'NO APLICA', 'DISPONIBLE', '2.000', '2.000', 16, '2025-03-14 15:14:52', '2025-03-14 15:14:52'),
(153, 'Motor Chevrolet 350 Tapa Rallada', 'MOTOR COMPLETO', 'Chevrolet', '350 Tapa Rallada', '1995', '24S/C', '060325', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 18, '2025-03-14 15:20:56', '2025-03-14 15:20:56'),
(154, 'Motor Chevrolet Van Exprés 6.0L', 'MOTOR COMPLETO', 'Chevrolet', 'Van Exprés 6.0L', '2006', '25S/C', '220329', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 12, '2025-03-14 15:21:53', '2025-03-14 15:21:53'),
(155, 'Motor Chevrolet 5.3L Taquete Mecánico', 'MOTOR COMPLETO', 'Chevrolet', 'Silverado 5.3L', '2001', '26S/C', '230058', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 13, '2025-03-14 15:24:00', '2025-03-14 15:24:00'),
(156, 'Motor Toyota 1UR', 'MOTOR COMPLETO', 'Toyota', '1UR', '2015', '490', '240001', 'APLICA', 'DISPONIBLE', '3.000', '3.000', 16, '2025-03-14 16:14:10', '2025-03-14 16:14:10'),
(157, 'Motor Jeep 318 Modelo Viejo', 'MOTOR COMPLETO', 'Jeep', '318 modelo viejo', '1900', '308', '220282', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 11, '2025-03-14 16:15:23', '2025-03-14 16:15:23'),
(158, 'Motor Chevrolet Rey Camión 6.0L', 'MOTOR COMPLETO', 'Chevrolet', 'Rey Camión 6.0L', '2010', '570', '240001', 'APLICA', 'DISPONIBLE', '2.500', '2.500', 16, '2025-03-14 16:36:51', '2025-03-14 16:36:51'),
(159, 'Motor 3/4 Toyota Celica 2ZZ', 'MOTOR 3/4', 'Toyota', 'Celica 2ZZ', '2000', '27S/C', '230058', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 13, '2025-03-14 17:00:00', '2025-03-14 17:00:00'),
(160, 'Motor 3/4 Toyota Celica 2ZZ', 'MOTOR 3/4', 'Toyota', 'Celica 2ZZ', '2000', '28S/C', '230058', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 13, '2025-03-14 17:00:42', '2025-03-14 17:00:42'),
(161, 'Motor Ford Explorer 3.5L TA/ Mazda CX9', 'MOTOR COMPLETO', 'Ford', 'Explorer 3.5L/ Mazda CX9', '2010', '12', '210262', 'NO APLICA', 'DISPONIBLE', '1.000', '1.000', 9, '2025-03-18 00:38:25', '2025-03-18 00:38:25'),
(162, 'Motor Ford 3.5L Explorer TA/Mazda Cx9', 'MOTOR COMPLETO', 'Ford', 'Ford 3.5L/Mazda Cx9', '2010', '509', '210262', 'NO APLICA', 'DISPONIBLE', '1.000', '1.000', 9, '2025-03-18 00:41:36', '2025-03-18 00:41:36'),
(163, 'Motor 7/8 Ford Explorer 3.5L/MazdaCX9', 'MOTOR 7/8', 'Ford', 'Explorer 3.5L TA/Mazda CX9', '2010', '31', '060325', 'NO APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-18 00:42:54', '2025-03-18 00:42:54'),
(164, 'Motor 7/8 Ford 3.5L TA/Mazda CX9', 'MOTOR 7/8', 'Ford', 'Ford Explorer 3.5L TA/Mazda CX9', '2010', '5', '060325', 'NO APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-18 00:44:14', '2025-03-18 00:44:14'),
(165, 'Motor 7/8 Jeep Dodge Ram 5.7L', 'MOTOR 7/8', 'Jeep', 'Dodge Ram 5.7L', '2008', '166', '220329', 'APLICA', 'DISPONIBLE', '1.800', '1.800', 12, '2025-03-18 00:45:35', '2025-03-18 00:45:35'),
(166, 'Motor 7/8 Toyota 2GR-VVTI Tacoma', 'MOTOR 7/8', 'Toyota', 'Tacoma 2GR-VVTI', '2015', '216', '230319', 'APLICA', 'DISPONIBLE', '4.000', '4.000', 15, '2025-03-18 00:47:26', '2025-03-18 00:47:26'),
(167, 'Motor 7/8 Ford Explorer 3.5L TA/MazdaCX9', 'MOTOR COMPLETO', 'Ford', 'Ford Explorer 3.5L TA/Mazda CX9', '2010', '29S/C', '210262', 'NO APLICA', 'DISPONIBLE', '1.000', '1.000', 9, '2025-03-18 00:51:17', '2025-03-18 00:51:17'),
(168, 'Motor Dodge 360 Magnum', 'MOTOR COMPLETO', 'Dodge', 'Magnum', '1998', '56', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-18 00:52:53', '2025-03-18 00:52:53'),
(169, 'Motor 7/8 Ford Fortaleza 4.6L 2V', 'MOTOR 7/8', 'Ford', 'Fortaleza 4.6L 2V', '2006', '30S/C', '230319', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 15, '2025-03-18 00:54:29', '2025-03-18 00:54:29'),
(170, 'Motor 7/8 Chevrolet Traibleizer TA', 'MOTOR 7/8', 'Chevrolet', 'Traibleizer 4.2L tapa Aluminio', '2008', '324', '240001', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 16, '2025-03-18 01:02:53', '2025-03-18 01:02:53'),
(171, 'Motor 7/8 Dodge Caliber 2.0L', 'MOTOR 7/8', 'Dosge', 'Caliber 2.4L', '2008', '352', '230319', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 15, '2025-03-18 01:03:51', '2025-03-18 01:03:51'),
(172, 'Motor 7/8 Chevrolet 5.3L TM 2002', 'MOTOR 7/8', 'Chevrolet', '5.3L Taquete Mecanico', '2002', '31S\\C', '240001', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 16, '2025-03-18 01:07:44', '2025-03-18 01:07:44'),
(173, 'Motor 7/8 Chevrolet 5.3L TM 2002', 'MOTOR 7/8', 'Chevrolet', '5.3L Taquete Mecánico', '2002', '32S/C', '240001', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 16, '2025-03-18 01:09:24', '2025-03-18 01:09:24'),
(174, 'Motor 7/8 Chevrolet 5.3L 2002', 'MOTOR 7/8', 'Chevrolet', 'Silverado 5.3L', '2002', '33S/C', '240001', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 16, '2025-03-18 01:10:45', '2025-03-18 01:10:45'),
(175, 'Moro 7/8 Honda Civic F23A1', 'MOTOR 7/8', 'Honda', 'Civic F23A1', '2000', '50', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-18 01:16:19', '2025-03-18 01:16:19'),
(176, 'Motor 7/8 Honda D17A1', 'MOTOR 7/8', 'Honda', 'Civic D17A1', '2000', '628', '240001', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 16, '2025-03-18 01:17:35', '2025-03-18 01:17:35'),
(177, 'Motor 7/8 Honda Civic D16Y7', 'MOTOR 7/8', 'Honda', 'Civic D16Y7', '2000', '626', '240001', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 16, '2025-03-18 01:18:43', '2025-03-18 01:18:43'),
(178, 'Motor 7/8 Toyota 3UR 5.7L', 'MOTOR 7/8', 'Toyota', 'Tundra 5.7L', '2015', 'D0844', '240001', 'APLICA', 'DISPONIBLE', '4.000', '4.000', 16, '2025-03-18 01:19:42', '2025-03-18 01:19:42'),
(179, 'Motor Nissan KA24 FRONTIER', 'MOTOR COMPLETO', 'Nissan', 'KA24 FRONTIER', '2010', '550', '240001', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 16, '2025-03-19 01:10:37', '2025-03-19 01:10:37'),
(180, 'Motor 7/8 Ford 200', 'MOTOR 7/8', 'Ford', '200', '1990', '272', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-19 14:42:21', '2025-03-19 14:42:21'),
(181, 'Motor 7/8 Chevrolet 350', 'MOTOR 7/8', 'Chevrolet', '350 Tapa Rallada', '1995', '271', '060325', 'APLICA', 'DISPONIBLE', '1.200', '1.200', 18, '2025-03-19 14:44:02', '2025-03-19 14:44:02'),
(182, 'Motor 7/8 Hyundai Santa Fe/Sorento G4JS', 'MOTOR 7/8', 'Hyundai', 'Santa Fe/Sorento 2.4L', '2002-2006', '270', '200922', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 19, '2025-03-19 15:01:32', '2025-03-19 15:01:32'),
(183, 'Motor 7/8 Jeep Cherokee 3.7L KK', 'MOTOR 7/8', 'Jeep', 'Cherokee  3.7L KK', '2008', '275', '230145', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 14, '2025-03-19 15:03:07', '2025-03-19 15:03:07'),
(184, 'Motor Chevrolet Cruce', 'MOTOR COMPLETO', 'Jeep', 'Chevrolet Cruce', '2010', '292', '240001', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 16, '2025-03-19 15:04:17', '2025-03-19 15:04:17'),
(185, 'Motor Chevrolet Malibú MN', 'MOTOR COMPLETO', 'Chevrolet', 'Malibú MN', '2015', '273', '200922', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 19, '2025-03-19 15:06:32', '2025-03-19 15:06:32'),
(186, 'Motor Hyundai 2.4L', 'MOTOR COMPLETO', 'Hyundai 2.4L', '2.4L', '2010', '283', '240001', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 16, '2025-03-19 15:07:35', '2025-03-19 15:07:35'),
(187, 'Motor Chevrolet 5.3L LS5', 'MOTOR COMPLETO', 'Chevrolet', '+5.3L LS5 Electrico', '2015', '289', '220282', 'APLICA', 'DISPONIBLE', '4.000', '4.000', 11, '2025-03-19 15:09:46', '2025-03-19 15:26:14'),
(188, 'Motor Mitsubishi 4F63', 'MOTOR COMPLETO', 'Mitsubishi', '4F63', '1995', '276', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-19 15:10:45', '2025-03-19 15:10:45'),
(189, 'Motor 7/8 3 cilindros', 'MOTOR 7/8', '3 cilindros', 'No se conoce', '1990', '263', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-19 15:18:04', '2025-03-19 15:18:04'),
(190, 'Motor 7/8 Toyota 2UZ 4.7L', 'MOTOR 7/8', 'Toyota', '4.7L 2UZ', '2008', '262', '220101', 'APLICA', 'DISPONIBLE', '3.000', '3.000', 10, '2025-03-19 15:19:57', '2025-03-19 15:19:57'),
(191, 'Motor 7/8 Chevrolet steen', 'MOTOR 7/8', 'Chevrolet', 'Steen', '2000', '261', '230145', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 14, '2025-03-19 15:22:50', '2025-03-19 15:22:50'),
(192, 'Motor 7/8 Ford Explorer 3.5L', 'MOTOR 7/8', 'FORD', 'Explorer 3.5L', '2015', '264', '200922', 'APLICA', 'DISPONIBLE', '3.000', '3.000', 19, '2025-03-19 15:24:06', '2025-03-19 15:24:06'),
(195, 'Motor 7/8 Chevrolet 5.3L LS5', 'MOTOR 7/8', 'Chevrolet', 'Ls5', '2015', '265', '220282', 'APLICA', 'DISPONIBLE', '3.000', '3.000', 11, '2025-03-19 15:27:10', '2025-03-19 15:27:10'),
(196, 'Motor Hyundai Santa Fe 2.7L/2.5L', 'MOTOR COMPLETO', 'Hyundai', 'Santa Fe 2.7L', '2005', '288', '240001', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 16, '2025-03-19 15:28:37', '2025-03-19 15:28:37'),
(197, 'Motor Hyundai Santa Fe 2.7L/2.5L', 'MOTOR COMPLETO', 'Hyundai', 'Santa Fe 2.7L', '2005', '269', '220282', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 11, '2025-03-19 15:29:55', '2025-03-19 15:29:55'),
(198, 'Motor 7/8 Chevrolet 5.3L LS5', 'MOTOR 7/8', 'Chevrolet', '5.3L LS5', '2015', '268', '220282', 'APLICA', 'DISPONIBLE', '3.000', '3.000', 11, '2025-03-19 15:32:08', '2025-03-19 15:32:08'),
(199, 'Motor 3/4 Chevrolet 5.3L BA', 'MOTOR 3/4', 'Chevrolet', '5.3L BA', '2005', 'D0047', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-19 15:33:37', '2025-03-19 15:33:37'),
(200, 'Motor 7/8 Jeep Grand Cherokee 5.7L 4G', 'MOTOR 7/8', 'Jeep', 'Grand Cherokee 5.7L 4G', '2012', '108', '220282', 'APLICA', 'DISPONIBLE', '2.800', '2.800', 11, '2025-03-19 15:35:29', '2025-03-19 15:35:29'),
(201, 'Motor 7/8 Hyundai Sonata 3.3L', 'MOTOR 7/8', 'Hyundai', 'Sonata 3.3L', '2008', '257', '200922', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 19, '2025-03-19 15:36:39', '2025-03-19 15:36:39'),
(202, 'Motor 7/8 Hyundai Sorento 3.8L', 'MOTOR 7/8', 'Hyundai', 'Sorento 3.8L', '2008', '256', '220282', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 11, '2025-03-19 15:37:39', '2025-03-19 15:37:39'),
(203, 'Motor 7/8 Ford 200', 'MOTOR 7/8', 'Ford', '200', '1990', '259', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-19 15:38:21', '2025-03-19 15:38:21'),
(204, 'Motor 7/8 Jeep 318 MV', 'MOTOR 7/8', 'Jeep', '318', '1990', '258', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-19 15:39:06', '2025-03-19 15:39:06'),
(208, 'Motor Chevrolet 262 Tipo Vortec', 'MOTOR COMPLETO', 'Chevrolet', '262 Vortec', '1995', '355', '060325', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 18, '2025-03-19 15:41:55', '2025-03-19 15:41:55'),
(209, 'Motor 7/8 Chevrolet 366', 'MOTOR 7/8', 'Chevrolet', '366', '1990', '267', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-19 15:42:44', '2025-03-19 15:42:44'),
(210, 'Motor 7/8 Ford Explorer 3.5L', 'MOTOR 7/8', 'Ford', 'Explorer 3.5L', '2015', '255', '230145', 'APLICA', 'DISPONIBLE', '3.000', '3.000', 14, '2025-03-19 15:43:44', '2025-03-19 15:43:44'),
(211, 'Motor 7/8 Toyota 2GR-Camry', 'MOTOR 7/8', 'Toyota', '2GR-Camry', '2005', '260', '210262', 'APLICA', 'DISPONIBLE', '2.300', '2.300', 9, '2025-03-19 15:45:19', '2025-03-19 15:45:19'),
(212, 'Motor 7/8 Nissan Xtrail QR25', 'MOTOR 7/8', 'Nissan', 'Xtrail QR25', '2008', '253', '060325', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 18, '2025-03-19 15:47:08', '2025-03-19 15:47:08'),
(213, 'Motor 7/8 Chevrolet 262 Vortec', 'MOTOR 7/8', 'Chevrolet', '262 Vortec', '1996', '252', '060325', 'APLICA', 'DISPONIBLE', '1.200', '1.200', 18, '2025-03-19 15:48:22', '2025-03-19 15:48:22'),
(214, 'Motor Chevrolet 250', 'MOTOR COMPLETO', 'Chevrolet', '250', '1990', '307', '060325', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 18, '2025-03-20 14:38:57', '2025-03-20 14:38:57'),
(215, 'Motor 7/8 Hyundai Sonata 3.3L', 'MOTOR 7/8', 'Hyundai', 'Sonata 3.3L', '2005', '245', '220329', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 12, '2025-03-20 22:12:35', '2025-03-20 22:12:35'),
(216, 'Motor 7/8 Toyota 1NZ 1.5L', 'MOTOR 7/8', 'Toyota', '1NZ Yaris', '2008', '244', '230058', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 13, '2025-03-20 22:13:42', '2025-03-20 22:13:42'),
(217, 'Motor 7/8 3 cilindros', 'MOTOR 7/8', 'Sin marca', '3 cilindros', '1990', '243', '060325', 'APLICA', 'DISPONIBLE', '500', '500', 18, '2025-03-20 22:14:43', '2025-03-20 22:14:43'),
(218, 'Motor 7/8 3 cilindros', 'MOTOR 7/8', 'Sin marca', '3 cilindros', '1990', '242', '060325', 'APLICA', 'DISPONIBLE', '500', '500', 18, '2025-03-20 22:15:17', '2025-03-20 22:15:17'),
(219, 'Motor 7/8 Honda Civic K24A1', 'MOTOR 7/8', 'Honda', 'Civic K24A1', '2005', '246', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-20 22:16:56', '2025-03-20 22:16:56'),
(220, 'Motor 7/8 Chevrolet 350', 'MOTOR 7/8', 'Chevrolet', '350', '1990', '11', '060325', 'APLICA', 'DISPONIBLE', '1.200', '1.200', 18, '2025-03-20 22:17:34', '2025-03-20 22:17:34'),
(221, 'Motor Ford Sin modelo', 'MOTOR COMPLETO', 'Ford', 'Ford', '1990', '248', '060325', 'APLICA', 'DISPONIBLE', '1.200', '1.200', 18, '2025-03-20 22:18:27', '2025-03-20 22:18:27'),
(222, 'Motor 7/8 Chevrolet 262 vortec', 'MOTOR 7/8', 'Chevrolet', '262 Vortec', '1995', '249', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-20 22:19:58', '2025-03-20 22:19:58'),
(223, 'Motor Ford 3.0L 6 cilindros', 'MOTOR COMPLETO', 'Ford', '3.0L 6 cilindros', '1990', '46', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-20 22:21:06', '2025-03-20 22:21:06'),
(224, 'Motor 7/8 Chevrolet 5.3L Taquete Mecánico', 'MOTOR 7/8', 'Chevrolet', '5.3L Taquete Mecanico', '2005', '251', '060325', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 18, '2025-03-20 22:22:08', '2025-03-20 22:22:08'),
(225, 'Motor Chevrolet 262 TB1', 'MOTOR COMPLETO', 'Chevrolet', '262 TB1', '1990', '279', '060325', 'APLICA', 'DISPONIBLE', '1.200', '1.200', 18, '2025-03-20 22:23:13', '2025-03-20 22:23:13'),
(226, 'Motor Chevrolet 262 tipo TB1', 'MOTOR COMPLETO', 'Chevrolet', '262 Tipo TB1', '1990', '310', '060325', 'APLICA', 'DISPONIBLE', '1.200', '1.200', 18, '2025-03-20 22:24:01', '2025-03-20 22:24:01'),
(227, 'Motor Toyota 7M-GE', 'MOTOR COMPLETO', 'Toyota', '7M-GE', '1990', '306', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-20 22:26:01', '2025-03-20 22:26:01'),
(228, 'Motor 7/8 Mazda 3', 'MOTOR 7/8', 'Mazda', '3', '2000', '234', '200922', 'APLICA', 'DISPONIBLE', '800', '800', 19, '2025-03-20 23:02:40', '2025-03-20 23:02:40'),
(229, 'Motor 7/8 Mazda 3', 'MOTOR 7/8', 'Mazda', '3', '2000', '233', '060325', 'APLICA', 'DISPONIBLE', '800', '800', 18, '2025-03-20 23:04:11', '2025-03-20 23:04:11'),
(232, 'Motor 7/8 Mazda 3', 'MOTOR 7/8', 'Mazda', '3', '2000', '232', '200922', 'APLICA', 'DISPONIBLE', '800', '800', 19, '2025-03-20 23:07:16', '2025-03-20 23:07:16'),
(233, 'Mitsubishi 6G75 3.5L', 'MOTOR COMPLETO', 'Mitsubishi', '6G75 3.5L', '2000', '109', '060325', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 18, '2025-03-20 23:09:25', '2025-03-22 02:52:37'),
(234, 'Motor 7/8 Toyota 1ZZ', 'MOTOR 7/8', 'Toyota', '1ZZ Nueva Sensación', '2008', '236', '230145', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 14, '2025-03-20 23:10:21', '2025-03-20 23:10:21'),
(235, 'Motor 7/8 Toyota 2GR-Camry', 'MOTOR 7/8', 'Toyota', '2GR-CAMRY', '2008', '237', '220329', 'APLICA', 'DISPONIBLE', '2.500', '2.500', 12, '2025-03-20 23:11:35', '2025-03-20 23:11:35'),
(236, 'Motor Dodge Neón 2.4L', 'MOTOR COMPLETO', 'Dodge', 'Neon', '1990', '239', '230145', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 14, '2025-03-20 23:13:56', '2025-03-20 23:16:57'),
(237, 'Motor Dodge Neón 2.4L', 'MOTOR COMPLETO', 'Dodge', 'Neon 2.4L', '2000', 'D0699', '230145', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 14, '2025-03-20 23:15:58', '2025-03-20 23:15:58'),
(238, 'Motor Jeep Cherokee 4.0L', 'MOTOR COMPLETO', 'Jeep', 'Cherokee 4.0L', '1990', '313', '230319', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 15, '2025-03-20 23:19:02', '2025-03-20 23:19:02'),
(239, 'Motor Jeep 258 WAGONIER', 'MOTOR COMPLETO', 'Jeep', '258', '1978', '312', '230319', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 15, '2025-03-20 23:20:28', '2025-03-20 23:20:28'),
(240, 'Motor Jeep Cherokee 4.0L', 'MOTOR COMPLETO', 'Jeep', 'Cherokee 4.0L', '2000', '302', '230319', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 15, '2025-03-20 23:21:48', '2025-03-20 23:21:48'),
(241, 'Motor Jeep Cherokee 4.0L MV', 'MOTOR COMPLETO', 'Jeepp', '4.0L MV', '2000', '314', '240001', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 16, '2025-03-20 23:22:37', '2025-03-20 23:22:37'),
(242, 'Motor Toyota 4M', 'MOTOR COMPLETO', 'Toyota', '4M', '1970', 'D0306', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-20 23:24:53', '2025-03-20 23:24:53'),
(243, 'Motor 7/8 Motor 4 cilindros', 'MOTOR 7/8', 'Sin marca', '4 cilindros', '2000', '224', '060325', 'APLICA', 'DISPONIBLE', '500', '500', 18, '2025-03-20 23:27:31', '2025-03-20 23:27:31'),
(244, 'Motor 3/4 Nissan VQ35', 'MOTOR 3/4', 'Nissan', 'VQ35', '2000', '223', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-20 23:28:21', '2025-03-20 23:28:21'),
(245, 'Motor 7/8 Dodge Cruise', 'MOTOR 7/8', 'Dodge', 'Cruiser', '2000', '222', '060325', 'APLICA', 'DISPONIBLE', '800', '800', 18, '2025-03-20 23:30:50', '2025-03-20 23:30:50'),
(246, 'Motor 7/8 Volkswagen', 'MOTOR 7/8', 'Volkswagen', '4 cilindros', '2000', '225', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-20 23:33:24', '2025-03-20 23:33:24'),
(247, 'Motor 7/8 Daewon Nubira', 'MOTOR 7/8', 'Daewoo', 'Nubira', '2000', '226', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-20 23:34:19', '2025-03-20 23:34:19'),
(248, 'Motor 7/8 Chevrolet 262 TB1', 'MOTOR 7/8', 'Chevrolet', '262 TB1', '1992', '227', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-20 23:35:12', '2025-03-20 23:35:12'),
(249, 'Motor 7/8 Chevrolet 5.3L ÑS5', 'MOTOR 7/8', 'Chevrolet', '5.3L LS5', '2015', '228', '220329', 'APLICA', 'DISPONIBLE', '4.000', '4.000', 12, '2025-03-20 23:36:10', '2025-03-20 23:36:10'),
(250, 'Motor 7/8 Hyundai G4NB Elantra', 'MOTOR 7/8', 'Hyundai', 'G4NB', '2015', '231', '230058', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 13, '2025-03-20 23:36:58', '2025-03-20 23:36:58'),
(251, 'Motor 7/8 Ford Fusion 3.0L', 'MOTOR 7/8', 'Ford', 'Fusión 3.0L', '2008', '229', '060325', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 18, '2025-03-20 23:38:16', '2025-03-20 23:38:16'),
(252, 'Motor 7/8 Toyota 1MZ', 'MOTOR 7/8', 'Toyota', '1MZ', '2005', '230', '230058', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 13, '2025-03-20 23:39:17', '2025-03-20 23:39:17'),
(253, 'Motor 7/8 Toyota 2GR- Camry', 'MOTOR 7/8', 'Toyota', '2GR-Camry', '2008', '218', '060325', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 18, '2025-03-20 23:40:27', '2025-03-20 23:40:27'),
(254, 'Motor 7/8 Toyota 2ZR VVTI', 'MOTOR 7/8', 'Toyota', '2ZR VVTI', '2015', '219', '220101', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 10, '2025-03-20 23:41:42', '2025-03-20 23:41:42'),
(259, 'Motor 7/8 Toyota 1MZ', 'MOTOR 7/8', 'Toyota', '1MZ', '2000', '220', '230058', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 13, '2025-03-20 23:50:25', '2025-03-20 23:50:25'),
(260, 'Chevrolet Z24', 'MOTOR 7/8', 'Chevrolet', 'Z24', '1990', '221', '060325', 'APLICA', 'DISPONIBLE', '800', '800', 18, '2025-03-20 23:51:38', '2025-03-22 02:51:59'),
(261, 'Motor Chevrolet 2.5L', 'MOTOR 7/8', 'Chevrolet', '2.5L', '1990', '211', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 00:27:32', '2025-03-21 00:27:32'),
(262, 'Motor 7/8 Mitsubishi 4G63', 'MOTOR 7/8', 'Mitsubishi', '4G63', '2000', '210', '230058', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 13, '2025-03-21 00:28:48', '2025-03-21 00:28:48'),
(263, 'Motor 7/8 Toyota 1MZ', 'MOTOR 7/8', 'Toyota', '1MZ', '2000', '212', '230058', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 13, '2025-03-21 00:29:43', '2025-03-21 00:29:43'),
(264, 'Chevrolet Z24', 'MOTOR 7/8', 'Chevrolet', 'Z24', '2000', '213', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 00:30:36', '2025-03-22 02:55:04'),
(265, 'Motor 7/8 Chevrolet 5.3L 2010', 'MOTOR 7/8', 'Chevrolet', '5.3L LS4', '2010', '214', '220329', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 12, '2025-03-21 00:31:52', '2025-03-21 00:31:52'),
(266, 'Motor Ford 3.0L 6 cilindros', 'MOTOR COMPLETO', 'Ford', '3.0L 6 cilindros', '2000', '215', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 00:32:59', '2025-03-21 00:32:59'),
(267, 'Motor 7/8 Honda D16Z6 civic', 'MOTOR 7/8', 'Honda', 'D16Z6 Civic', '2000', '206', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 00:34:17', '2025-03-21 00:34:17'),
(268, 'Motor 7/8 Honda Civic D16Z6', 'MOTOR 7/8', 'Honda', 'Civic', '2000', '204', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 00:35:03', '2025-03-21 00:35:03'),
(269, 'Motor Ford 3.0L 6 cilindros', 'MOTOR COMPLETO', 'Ford', '3.0L 6 cilindros', '1990', '203', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 00:35:47', '2025-03-21 00:35:47'),
(270, 'Motor 7/8 Honda F23A1', 'MOTOR 7/8', 'Honda', 'F23A1', '2000', '196', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 00:36:48', '2025-03-21 00:36:48'),
(272, 'Motor 7/8 Jeep Cherokee 3.7L KJ', 'MOTOR 7/8', 'Jeep', 'Cherokee  3.7L KJ', '2006', '195', '230145', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 14, '2025-03-21 00:38:16', '2025-03-21 00:38:16');
INSERT INTO `partidas` (`id`, `item`, `tipo`, `marca`, `modelo`, `año`, `codInv`, `expediente`, `condicion`, `status`, `price`, `price_sale`, `container_id`, `created_at`, `updated_at`) VALUES
(273, 'Motor 7/8 Honda J30 A1', 'MOTOR 7/8', 'Honda', 'J30A1', '2000', '194', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 00:39:43', '2025-03-21 00:39:43'),
(274, 'Motor 7/8 Ford Focus Zetec', 'MOTOR 7/8', 'Ford', 'Focus Zetec', '2000', '197', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 00:40:28', '2025-03-21 00:40:28'),
(275, 'Motor 7/8 Ford Leiser', 'MOTOR 7/8', 'Ford', 'Leiser', '2000', '198', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 00:41:25', '2025-03-21 00:41:25'),
(276, 'Motor 7/8 Ford Leiser', 'MOTOR 7/8', 'Ford', 'Leiser', '2000', '199', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 00:42:04', '2025-03-21 00:42:04'),
(277, 'Motor Hyundai G4KC', 'MOTOR COMPLETO', 'Hyundai', 'G4KC 2.4L', '2010', '202', '220282', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 11, '2025-03-21 00:43:13', '2025-03-21 00:43:13'),
(278, 'Motor Ford 3.0L 6 cilindros', 'MOTOR COMPLETO', 'Ford', '3.0L', '2000', '193', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 00:47:40', '2025-03-21 00:47:40'),
(279, 'Motor 7/8 Jeep Grand Cherokee 4.7L 8 B', 'MOTOR 7/8', 'Jeep', 'Grand Cherokee 4.7L 8 B', '2008', '192', '220329', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 12, '2025-03-21 00:48:38', '2025-03-21 00:48:38'),
(280, 'Motor 7/8 Ford Focus Zetec', 'MOTOR 7/8', 'Ford', 'Focus Zetec', '2000', '191', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 00:49:42', '2025-03-21 00:49:42'),
(281, 'Motor 7/8 Honda J30A1', 'MOTOR 3/4', 'Honda', 'J30A1', '1990', '189', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 00:50:47', '2025-03-21 00:50:47'),
(282, 'Motor 7/9 Ford Focus Zetec', 'MOTOR 7/8', 'Ford', 'Focus Zetec', '2000', '190', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 00:51:28', '2025-03-21 00:51:28'),
(283, 'Motor 7/8 Honda B16A2', 'MOTOR 7/8', 'Honda', 'B16A2', '1990', '188', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 00:52:32', '2025-03-21 00:52:32'),
(284, 'Motor 7/8 Chevrolet 5.3L LS1', 'MOTOR 7/8', 'Chevrolet', '5.3L LS1', '2005', 'D0191', '060325', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 18, '2025-03-21 00:53:31', '2025-03-21 00:53:31'),
(285, 'Motor Ford Focus Zetec', 'MOTOR COMPLETO', 'Ford', 'Focus Zetec', '2000', '183', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 00:54:25', '2025-03-21 00:54:25'),
(286, 'Motor Kia Kia', 'MOTOR COMPLETO', 'Kia', 'Kia', '2000', '187', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 00:55:42', '2025-03-21 00:55:42'),
(287, 'Motor 7/8 Hyundai Elantra 2.0L', 'MOTOR 7/8', 'Hyundai', 'Elantra 2.0L', '2000', '186', '060325', 'APLICA', 'DISPONIBLE', '1.300', '1.300', 18, '2025-03-21 00:56:36', '2025-03-21 00:56:36'),
(288, 'Motor Hyundai Caren', 'MOTOR COMPLETO', 'Hyundai', 'Caren', '2010', '185', '240222', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 20, '2025-03-21 00:57:42', '2025-03-21 00:57:42'),
(289, 'Motor 7/8 Chevrolet Z24', 'MOTOR 7/8', 'Chevrolet', 'Z24', '1980', '184', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 00:59:41', '2025-03-21 00:59:41'),
(290, 'Motor 7/8 Toyota 4A', 'MOTOR 7/8', 'Toyota', '4A', '1995', '241', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 01:01:13', '2025-03-21 01:01:13'),
(291, 'Motor Nissan Almera', 'MOTOR COMPLETO', 'Nissan', 'Almera', '1990', '182', '200922', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 19, '2025-03-21 01:02:11', '2025-03-21 01:02:11'),
(292, 'Motor 7/8 Nissan Almera', 'MOTOR 7/8', 'Nissan', 'Almera', '1990', '181', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 01:03:00', '2025-03-21 01:03:00'),
(293, 'Motor Nissan Almera', 'MOTOR COMPLETO', 'Nissan', 'Almera', '1990', '180', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 01:03:38', '2025-03-21 01:03:38'),
(294, 'Motor Nissan Almera', 'MOTOR COMPLETO', 'Nissan', 'Almera', '1990', '179', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 01:28:02', '2025-03-21 01:28:02'),
(295, 'Motor Nissan Almena', 'MOTOR COMPLETO', 'Hyundai', 'Almera', '1990', '178', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 01:28:48', '2025-03-21 01:28:48'),
(296, 'Motor Nissan Almera', 'MOTOR COMPLETO', 'Nissan', 'Almera', '1990', '177', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 01:29:39', '2025-03-21 01:29:39'),
(297, 'Motor 7/8 Kia Sportag', 'MOTOR 7/8', 'Kia', 'Sportg', '1990', '176', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 01:31:14', '2025-03-21 01:31:14'),
(298, 'Motor 7/8 Kia Sportag', 'MOTOR 7/8', 'Kia', 'Sportg', '1990', '175', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 01:31:58', '2025-03-21 01:31:58'),
(299, 'Motor 7/8 Chevrolet 5.3L BA 2005', 'MOTOR 7/8', 'Chevrolet', '5.3L LS1', '2005', '174', '060325', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 18, '2025-03-21 01:36:38', '2025-03-21 01:36:38'),
(300, 'Motor 3/4 Honda Civic', 'MOTOR 3/4', 'Honda', 'Civic', '2000', '173', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 01:37:55', '2025-03-21 01:37:55'),
(301, 'Motor 7/8 Chevrolet 5.3L LS1', 'MOTOR 7/8', 'Chevrolet', '5.3L', '2005', '172', '060325', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 18, '2025-03-21 01:39:11', '2025-03-21 01:39:11'),
(302, 'Motor 7/8 Mitsubishi Montero TD', 'MOTOR 7/8', 'Mitsubishi', 'Montero TD', '2000', '24', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 01:40:58', '2025-03-21 01:40:58'),
(303, 'Motor 7/8 Volkswagen 4 cilindro', 'MOTOR 7/8', 'Volkswagen', 'S/N', '2000', '21', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 01:44:01', '2025-03-21 01:44:01'),
(304, 'Motor 7/8 Chevrolet Cavalier Z24', 'MOTOR 7/8', 'Chevrolet', 'Z24', '1990', '19', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 01:45:55', '2025-03-21 01:45:55'),
(305, 'Motor 7/8 Chevrolet Centry 3.1 Lumina', 'MOTOR 7/8', 'Chevrolet', 'Lumina 3.1L', '1990', '18', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 01:46:56', '2025-03-21 01:46:56'),
(306, 'Motor 7/8 Chevrolet Cavalier', 'MOTOR 7/8', 'Chevrolet', 'Tapa lisa Cavalier', '1990', '20', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 01:47:42', '2025-03-21 01:47:42'),
(307, 'Motor Nissan 6 cilindros', 'MOTOR COMPLETO', 'Nissan', '6 cilindros', '1990', '22', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 01:48:23', '2025-03-21 01:48:23'),
(308, 'Motor Volkswagen Beta', 'MOTOR COMPLETO', 'Volkswagen', 'Beta', '1990', '33', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 01:49:03', '2025-03-21 01:49:03'),
(309, 'Motor Mitsubishi Montero TD', 'MOTOR COMPLETO', 'Mitsubishi', '6G75 Montero', '2000', '23', '220101', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 10, '2025-03-21 01:50:08', '2025-03-21 01:50:08'),
(310, 'Motor Toyota 3VZ', 'MOTOR COMPLETO', 'Toyota', '3VZ', '1992', 'D0028', '060325', 'APLICA', 'DISPONIBLE', '1.800', '1.800', 18, '2025-03-21 01:51:21', '2025-03-21 01:51:21'),
(311, 'Motor Mercedes Benz', 'MOTOR COMPLETO', 'Mercedes', 'Benz', '1992', '28', '060325', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 18, '2025-03-21 01:51:58', '2025-03-21 01:51:58'),
(312, 'Motor 7/8 Challengers 5.7L', 'MOTOR 7/8', 'Jeep', 'Challengers', '2000', '26', '060325', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 18, '2025-03-21 01:53:17', '2025-03-21 01:53:17'),
(313, 'Motor 7/8 Chevrolet 5.3L Ls2', 'MOTOR 7/8', 'Chevrolet', '5.3L Ls2', '2008', '156', '060325', 'APLICA', 'DISPONIBLE', '1.800', '1.800', 18, '2025-03-21 01:54:34', '2025-03-21 01:54:34'),
(314, 'Motor 7/8 Chevrolet 262 TB1', 'MOTOR 7/8', 'Chevrolet', '262 TB1', '1992', '154', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 01:55:23', '2025-03-21 01:55:23'),
(315, 'Motor 7/8 Mitsubishi 4G61 Lancer', 'MOTOR 7/8', 'Mitsubishi', 'Lancer 4G61', '2000', '153', '230058', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 13, '2025-03-21 01:56:27', '2025-03-21 01:56:27'),
(316, 'Motor 7/8 Toyota 2GR -Camry', 'MOTOR 7/8', 'Toyota', '2GR-Camry', '2008', '364', '230058', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 13, '2025-03-21 01:57:30', '2025-03-21 01:57:30'),
(317, 'Motor 7/8 Toyota 2AZ 2.4L', 'MOTOR 7/8', 'Toyota', '2AZ 2.4L', '2006', '152', '060325', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 18, '2025-03-21 01:58:21', '2025-03-21 01:58:21'),
(320, 'Motor 7/8 Hyundai Sonata 3.3L', 'MOTOR 7/8', 'Hyundai', 'Sonata 3.3', '2008', '145', '200922', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 19, '2025-03-21 02:02:02', '2025-03-21 02:02:02'),
(321, 'Motor 7/8 Toyota 1ZZ', 'MOTOR 7/8', 'Toyota', '1ZZ', '2010', '158', '230319', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 15, '2025-03-21 02:03:17', '2025-03-21 02:03:17'),
(322, 'Motor 7/8 Ford Explorer 3.5L', 'MOTOR 7/8', 'Ford', '3.5L', '2012', '162', '220329', 'APLICA', 'DISPONIBLE', '3.000', '3.000', 12, '2025-03-21 02:04:13', '2025-03-21 02:04:13'),
(323, 'Motor 7/8 Chevrolet 350MV', 'MOTOR 7/8', 'Chevrolet', '350 MV', '1970', '161', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 02:05:58', '2025-03-21 02:05:58'),
(324, 'Motor 7/8 Toyota 5.7L 3UR', 'MOTOR 7/8', 'Toyota', 'Tundra 5.7L', '2015', '159', '200922', 'APLICA', 'DISPONIBLE', '4.000', '4.000', 19, '2025-03-21 02:07:53', '2025-03-21 02:07:53'),
(326, 'Motor 7/8 Toyota 2RZ', 'MOTOR 7/8', 'Toyota', '2RZ', '1992', '144', '230058', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 13, '2025-03-21 02:11:36', '2025-03-21 02:11:36'),
(327, 'Motor 7/8 Hyundai Sorento/Sonata 6GDB', 'MOTOR 7/8', 'Hyundai', 'Sonata 3.3L', '2008', '143', '200922', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 19, '2025-03-21 02:13:02', '2025-03-21 02:13:02'),
(328, 'Motor 7/8 Ford 3.5L Explorer', 'MOTOR 7/8', 'Ford', 'Explorer 3.5L', '2012', '147', '220329', 'APLICA', 'DISPONIBLE', '3.000', '3.000', 12, '2025-03-21 02:14:03', '2025-03-21 02:14:03'),
(329, 'Motor Chevrolet Centry 3.1L', 'MOTOR COMPLETO', 'Chevrolet', 'Century', '1990', '146', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 02:15:00', '2025-03-21 02:15:00'),
(330, 'Motor 7/8 Toyota Tundra 3UT', 'MOTOR 7/8', 'Toyota', '3UR', '2015', '362', '200922', 'APLICA', 'DISPONIBLE', '4.000', '4.000', 19, '2025-03-21 02:15:58', '2025-03-21 02:15:58'),
(331, 'Motor 7/8 Toyota 2AZ', 'MOTOR 7/8', 'Toyota', '2AZ', '2008', '142', '210262', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 9, '2025-03-21 02:17:15', '2025-03-21 02:17:15'),
(332, 'Motor 7/8 Mitsubishi 4G63', 'MOTOR 7/8', 'Mitsubishi', '4G63', '1990', '52', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 02:18:24', '2025-03-21 02:18:24'),
(333, 'Motor 7/8 Caribe Suzuki 4XCL', 'MOTOR 7/8', 'Suzuki', 'Caribe Suzuki 4XCL', '1990', '135', '060325', 'APLICA', 'DISPONIBLE', '800', '800', 18, '2025-03-21 02:19:41', '2025-03-21 02:19:41'),
(334, 'Motor 7/8 Vitara 1.6LV', 'MOTOR 7/8', 'Chevrolet', 'Vitara 1.6L MV', '1990', '134', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 02:20:39', '2025-03-21 02:20:39'),
(335, 'Motor 7/8 Hyundai Accel 1.6L', 'MOTOR 7/8', 'Hyundai', 'Accel 1.6L', '2008', '133', '240001', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 16, '2025-03-21 02:22:13', '2025-03-21 02:22:13'),
(336, 'Motor 7/8 Dodge Caliber 2.0L', 'MOTOR 7/8', 'Dodge', 'Caliber', '2008', 'D0141', '060325', 'APLICA', 'DISPONIBLE', '1.200', '1.200', 18, '2025-03-21 02:25:15', '2025-03-21 02:25:15'),
(337, 'Motor 7/8 Mitsubishi 4G63', 'MOTOR 7/8', 'Mitsubishi', '4G63', '2000', '131', '060325', 'APLICA', 'DISPONIBLE', '1.200', '1.200', 18, '2025-03-21 02:26:09', '2025-03-21 02:26:09'),
(338, 'Motor 7/8 Mitsubishi 4G64', 'MOTOR 7/8', 'Mitsubishi', '4G64', '1970', '376', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 02:27:26', '2025-03-21 02:27:26'),
(339, 'Motor 7/9 Ford Explorer 4 Cadena', 'MOTOR 7/8', 'Ford', 'Explorer 4 cadena', '2005', '137', '060325', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 18, '2025-03-21 02:28:56', '2025-03-21 02:28:56'),
(340, 'Motor 7/8 Jeep Grand Cherokee 4.7L EGR 8 B', 'MOTOR 7/8', 'Jeep', 'Grand Cherokee 4.7L 9 B', '2007', '330', '220282', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 11, '2025-03-21 02:30:19', '2025-03-21 02:30:19'),
(341, 'Motor 7/8 Ford 302', 'MOTOR 7/8', 'Ford', '302', '1970', '140', '060325', 'APLICA', 'DISPONIBLE', '800', '800', 18, '2025-03-21 02:31:03', '2025-03-21 02:31:03'),
(342, 'Motor 7/8 Ford Explorer 4.0L 4 cadenas', 'MOTOR 7/8', 'Ford', 'Explorer 4.0L', '2006', '139', '060325', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 18, '2025-03-21 02:33:22', '2025-03-21 02:33:22'),
(343, 'Motor 7/8 Chevrolet Rey Camión 2010', 'MOTOR 7/8', 'Chevrolet', 'Rey camion 6.0L', '2010', '141', '060325', 'NO APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 02:34:11', '2025-03-21 02:34:11'),
(344, 'Motor 7/8 Volkswagen', 'MOTOR 7/8', 'Volkswagen', 'Sin modelo', '2000', 'D0762', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 14:12:19', '2025-03-21 14:12:19'),
(345, 'Motor 7/8 Toyota 5E', 'MOTOR 7/8', 'Toyota', '5E', '2000', '372', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 14:13:26', '2025-03-21 14:13:26'),
(346, 'Motor Ford Fusión 3.0L', 'MOTOR COMPLETO', 'Ford', 'Fisio 3.0L', '2010', '373', '060325', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 18, '2025-03-21 14:14:55', '2025-03-21 14:14:55'),
(347, 'Motor 7/8 Kia Espectra', 'MOTOR 7/8', 'Kia', 'Espectra', '2000', '375', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 14:17:06', '2025-03-21 14:17:06'),
(348, 'Motor 3/4 Jeep Cherokee 3.7L Kk', 'MOTOR 3/4', 'Jeep', 'Cherokee 3.7L KK', '2006', 'D0166', '200922', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 19, '2025-03-21 14:19:30', '2025-03-21 14:19:30'),
(349, 'Motor 7/8 Toyota 2.0L Camrry', 'MOTOR 7/8', 'Toyota', 'Camry 2.0L', '2000', '377', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 14:20:47', '2025-03-21 14:20:47'),
(350, 'Motor 7/8 Toyota 2.0L Camry', 'MOTOR 7/8', 'Toyota', 'Camry 2.0L', '2000', '378', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 14:23:14', '2025-03-21 14:23:14'),
(351, 'Motor 7/8 Toyota 2.0L Camry', 'MOTOR 7/8', 'Toyota', 'Camry 2.0L', '2000', '379', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 14:24:23', '2025-03-21 14:24:23'),
(353, 'Motor 7/8 Chevrolet Impala 3.8L', 'MOTOR 7/8', 'Chevrolet', 'Impala 3.8L', '2000', '309', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 14:25:26', '2025-03-21 14:25:26'),
(354, 'Motor 7/8 Cavalier 2.2L', 'MOTOR 7/8', 'Chevrolet', 'Cavalier 2.2L', '2000', '381', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 14:26:35', '2025-03-21 14:26:35'),
(355, 'Motor 7/8 Chevrolet Cavalier 2.2L tapa rallada', 'MOTOR 7/8', 'Chevrolet', 'Cavalier 2.2L', '2000', '382', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 14:29:54', '2025-03-21 14:29:54'),
(356, 'Motor 7/8 Nissan Almera', 'MOTOR 7/8', 'Nissan', 'Almera', '1990', '383', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 14:31:01', '2025-03-21 14:31:01'),
(357, 'Motor 7/8 Nissan VQ35', 'MOTOR 7/8', 'Nissan', 'VQ35', '2000', '384', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 14:32:09', '2025-03-21 14:32:09'),
(358, 'Motor 7/8 Toyota 2AZ', 'MOTOR 7/8', 'Toyota', '2AZ', '2008', '386', '060325', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 18, '2025-03-21 14:32:53', '2025-03-21 14:32:53'),
(359, 'Motor 3/4 Dodge 360', 'MOTOR 7/8', 'Dodge', '360', '1990', '385', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 14:33:36', '2025-03-21 14:33:36'),
(360, 'Motor 7/8 Dodge 3.8L Levaron', 'MOTOR 7/8', 'Dodge', 'Levaron 3.8L', '2000', '389', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 14:34:48', '2025-03-21 14:34:48'),
(361, 'Ford Mustang', 'MOTOR 7/8', 'Ford', 'Mustang 3.8L', '2000', '387', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 14:35:32', '2025-03-22 04:13:30'),
(362, 'Motor 7/8 Ford 300', 'MOTOR 7/8', 'Ford', '300', '1970', '388', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 14:36:21', '2025-03-21 14:36:21'),
(363, 'Motor 7/8 Toyota 2UZ', 'MOTOR 7/8', 'Toyota', '2UZ', '2000', '390', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 14:39:29', '2025-03-21 14:39:29'),
(364, 'Motor 7/8 Toyota  1NZ-YARIS', 'MOTOR 7/8', 'Toyota', 'Yaris 1NZ', '2010', '59', '060325', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 18, '2025-03-21 17:41:06', '2025-03-21 17:41:06'),
(365, 'Motor 7/8 Toyota 1NZ Yaris', 'MOTOR 7/8', 'Toyota', '1NZ Yaris', '2010', '60', '060325', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 18, '2025-03-21 17:42:00', '2025-03-21 17:42:00'),
(366, 'Motor 7/8 Toyota 1NZ Yaris', 'MOTOR 7/8', 'Totota', '1NX Yaris', '2010', '61', '060325', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 18, '2025-03-21 17:42:58', '2025-03-21 17:42:58'),
(367, 'Motor 7/8 Toyota 1.6L 4A', 'MOTOR 7/8', 'Toyota', '4A 1.6L', '1992', '62', '060325', 'APLICA', 'DISPONIBLE', '1.200', '1.200', 18, '2025-03-21 17:44:02', '2025-03-21 17:44:02'),
(368, 'Motor 7/8 Toyota 2AZ', 'MOTOR 7/8', 'Toyota', '2AZ Previa', '2010', '63', '210262', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 9, '2025-03-21 17:46:09', '2025-03-21 17:46:09'),
(369, 'Motor 7/8 Mitsubishi Montero TD', 'MOTOR 7/8', 'Mitsubishi', 'Montero TD', '2006', '64', '210262', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 9, '2025-03-21 17:47:28', '2025-03-21 17:47:28'),
(370, 'Motor 7/8 Toyota 2AZ', 'MOTOR 7/8', 'Toyota', '2AZ Previa', '2008', '65', '210262', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 9, '2025-03-21 17:48:47', '2025-03-21 17:48:47'),
(371, 'Motor 7/8 Jeep 4.7L 8B EGR', 'MOTOR 7/8', 'Jeep', '4.7L 8 B EGR', '2008', '167', '220282', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 11, '2025-03-21 17:50:18', '2025-03-21 17:50:18'),
(372, 'Motor 7/8 Toyota 1ZZ Nueva Sensación', 'MOTOR 7/8', 'Toyota', '1ZZ Nueva Sensación', '2010', '165', '220282', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 11, '2025-03-21 17:51:08', '2025-03-21 17:51:08'),
(373, 'Motor 3/4 Chevrolet Van Exprés', 'MOTOR 3/4', 'Chevrolet', '4.3L van Exprés', '2005', '68', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 17:52:18', '2025-03-21 17:52:18'),
(374, 'Motor Toyota 2ZR VVTI', 'MOTOR COMPLETO', 'Toyota', '2AZ Previa', '2008', 'D0504', '230058', 'APLICA', 'DISPONIBLE', '2.500', '2.500', 13, '2025-03-21 17:53:14', '2025-03-21 19:59:25'),
(375, 'Motor 7/8 Toyota 4A', 'MOTOR 3/4', 'Toyota', '4A 1.6L', '1992', '1', '060325', 'APLICA', 'DISPONIBLE', '1.200', '1.200', 18, '2025-03-21 17:54:28', '2025-03-21 17:54:28'),
(376, 'Motor 7/8 Ford Fiesta Titanium', 'MOTOR 7/8', 'Ford', 'Fiesta Titanium', '2015', '164', '060325', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 18, '2025-03-21 17:55:38', '2025-03-21 17:55:38'),
(377, 'Motor 7/8 Toyota 1MZ', 'MOTOR 7/8', 'Toyota', '1MZ', '1990', '44', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 17:56:26', '2025-03-21 17:56:26'),
(378, 'Motor 7/8 Hyundai G4NB', 'MOTOR 7/8', 'Hyundai', 'G4NB', '2015', '71', '230058', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 13, '2025-03-21 17:57:25', '2025-03-21 17:57:25'),
(379, 'Motor 7/8 Jeep 258', 'MOTOR 7/8', 'Jeep', '258', '1970', '9', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 17:58:45', '2025-03-21 17:58:45'),
(380, 'Motor 7/8 Ford 4.6L  2VBA', 'MOTOR 7/8', 'Ford', 'Explorer 4.6L 2V BA', '2005', '72', '240001', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 16, '2025-03-21 17:59:53', '2025-03-21 17:59:53'),
(381, 'Motor 7/8 3 cilindros', 'MOTOR 7/8', 'Sin marca', '3 cilindros', '1990', '74', '060325', 'APLICA', 'DISPONIBLE', '500', '500', 18, '2025-03-21 20:25:34', '2025-03-21 20:25:34'),
(382, 'Motor 7/8 Chevrolet Vitara J18', 'MOTOR 7/8', 'Chevrolet', 'J18', '2000', '75', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 20:26:16', '2025-03-21 20:26:16'),
(383, 'Motor 7/8 Toyota 1NZ Yaris', 'MOTOR 7/8', 'Toyota', 'Yaris 1NZ', '2008', '76', '060325', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 18, '2025-03-21 20:27:05', '2025-03-21 20:27:05'),
(384, 'Motor 7/8 Mitsubishi 4 Cilindros', 'MOTOR 7/8', 'Mitsubishi', '4G93', '2000', '30', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 20:27:51', '2025-03-21 20:27:51'),
(385, 'Motor Jeep Cherokee 3.7L KJ', 'MOTOR COMPLETO', 'Jeep', 'Cherokee 3.7L KJ', '2005', '78', '240001', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 16, '2025-03-21 20:29:03', '2025-03-21 20:29:03'),
(386, 'Motor 3/4 Nissan 4 cilindros', 'MOTOR 3/4', 'Nissn', '4 cilindro', '1990', '79', '060325', 'APLICA', 'DISPONIBLE', '800', '800', 18, '2025-03-21 20:31:47', '2025-03-21 20:31:47'),
(387, 'Motor Toyota 2GR-Camry', 'MOTOR COMPLETO', 'Toyota', '2GR-CAMRY', '2008', '38', '060325', 'APLICA', 'DISPONIBLE', '2.500', '2.500', 18, '2025-03-21 20:32:47', '2025-03-21 20:32:47'),
(388, 'Motor 7/8 Ford Escape TP', 'MOTOR 7/8', 'Chevrolet', 'Escape 3.0L TP', '2002', '4', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 20:34:05', '2025-03-21 20:34:05'),
(389, 'Motor 7/8 Ford Escape 3.0L TP', 'MOTOR 7/8', 'Ford', 'Escape 3.0L', '2002', '3', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 20:35:09', '2025-03-21 20:35:09'),
(390, 'Motor 7/8 Nissan VQ35', 'MOTOR 7/8', 'Nissa', 'VQ35', '2005', '282', '200922', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 19, '2025-03-21 21:05:17', '2025-03-21 21:05:17'),
(391, 'Motor 7/8 Kia', 'MOTOR 7/8', 'Kia', 'Sin modelo', '1990', '14', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 21:06:31', '2025-03-21 21:06:31'),
(392, 'Motor 3/4 Jeep Magnum', 'MOTOR 3/4', 'Jeep', 'Mangum', '1990', '53', '060325', 'APLICA', 'DISPONIBLE', '700', '700', 18, '2025-03-21 21:07:16', '2025-03-21 21:07:16'),
(393, 'Motor 7/8 Kia Espectra', 'MOTOR 7/8', 'Kia', 'Espectra', '2000', '58', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 21:07:59', '2025-03-21 21:07:59'),
(394, 'Motor 7/8 Nissan Armada Bk56', 'MOTOR 7/8', 'Nissan', 'Armada', '2000', '86', '060325', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 18, '2025-03-21 21:08:46', '2025-03-21 21:08:46'),
(395, 'Motor 3/4 Toyota 2UZ VVTi', 'MOTOR 3/4', 'Toyota', '2UZ VVTi', '2000', '327', '240001', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 16, '2025-03-21 21:09:31', '2025-03-21 21:09:31'),
(396, 'Motor 7/8 Kia G4CS', 'MOTOR 7/8', 'Kia', 'G4CS', '200', '87', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 21:10:56', '2025-03-21 21:10:56'),
(398, 'Motor 7/8 Toyota 2.0L Camry', 'MOTOR 7/8', 'Toyota', '2.0L Camry', '1990', '88', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 21:12:08', '2025-03-21 21:12:08'),
(399, 'Motor 7/8 Caribe', 'MOTOR 7/8', 'Caribe', '4XE1', '1990', '89', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 22:28:06', '2025-03-21 22:28:06'),
(400, 'Motor 7/8 Ford Explorer 4.0L cadena', 'MOTOR 7/8', 'Ford', 'Explorer 4 Cadena', '1998', '90', '060325', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 18, '2025-03-21 22:29:01', '2025-03-21 22:29:01'),
(401, 'Motor 7/8 Mitsubishi Montero TD', 'MOTOR 7/8', 'Mitsubishi', 'Montero TD', '1990', '91', '060325', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 18, '2025-03-21 22:29:48', '2025-03-21 22:29:48'),
(402, 'Motor 7/8 Toyota 2ZR 1 VVTi', 'MOTOR 7/8', 'Toyota', '2ZR', '2010', '92', '060325', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 18, '2025-03-21 22:30:44', '2025-03-21 22:30:44'),
(403, 'Motor 7/8 Ford 4 cilindros', 'MOTOR 7/8', 'Ford', '4 Cilindro VCT', '1990', '32', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 22:31:43', '2025-03-21 22:31:43'),
(404, 'Motor 7/8 Toyota 1ZZ Nueva Sensación', 'MOTOR 7/8', 'Toyota', '1ZZ', '2010', '674', '240222', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 20, '2025-03-21 22:33:37', '2025-03-21 22:33:37'),
(405, 'Motor 7/8 Toyota Camry 2GR', 'MOTOR 7/8', 'Toyota', '2GR- Camry', '2008', '94', '210262', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 9, '2025-03-21 22:34:38', '2025-03-21 22:34:38'),
(406, 'Motor 7/8 Chevrolet 350 Vortec', 'MOTOR 7/8', 'Chevrolet', '350 vortec', '1990', '95', '060325', 'APLICA', 'DISPONIBLE', '1.200', '1.200', 18, '2025-03-21 22:35:28', '2025-03-21 22:35:28'),
(407, 'Motor Chevrolet 262 TB1', 'MOTOR COMPLETO', 'Chevrolet', '262 tipo TB1', '1992', '96', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 22:38:14', '2025-03-21 22:38:14'),
(408, 'Motor 7/8 Honda K24Z6', 'MOTOR 7/8', 'Honda', 'K24Z6', '2000', '97', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 22:39:37', '2025-03-21 22:39:37'),
(412, 'Motor 3/4 Chevrolet 5.3L TM 2008', 'MOTOR 3/4', 'Chevrolet', '5.3L 2008', '2008', '15', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 22:45:01', '2025-03-21 22:45:01'),
(413, 'Motor 7/8 Toyota Corolla 4A 1.6L', 'MOTOR 7/8', 'Toyota', 'Corolla 4A', '1992', '36', '060325', 'APLICA', 'DISPONIBLE', '1.200', '1.200', 18, '2025-03-21 22:45:56', '2025-03-21 22:45:56'),
(414, 'Motor 7/8 Toyota Camry 2.0L', 'MOTOR 7/8', 'Toyota', 'Camry 2.0L', '1992', '99', '060325', 'APLICA', 'DISPONIBLE', '1.200', '1.200', 18, '2025-03-21 22:46:59', '2025-03-21 22:46:59'),
(415, 'Jeep 4.7L 8 B', 'MOTOR 3/4', 'Jeep', 'CHEROKEE 4.7L 8B', '2008', '35S/C', '240222', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 20, '2025-03-21 23:30:30', '2025-03-21 23:33:41'),
(418, 'Toyota 2AZ Previa', 'MOTOR 7/8', 'Toyota', '2AZ', '2008', '34S/C', '060325', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 18, '2025-03-21 23:35:00', '2025-03-21 23:39:23'),
(419, 'Motor 7/8 Volkswagen', 'MOTOR 7/8', 'Volkswagen', 'S/N', '2000', '102', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 23:47:00', '2025-03-21 23:47:00'),
(420, 'Motor 7/8 Dodge Neon', 'MOTOR 7/8', 'Dodge', 'Neón 2.0L', '1990', '103', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 23:47:53', '2025-03-21 23:47:53'),
(421, 'Motor 7/8 Toyota 2RZ', 'MOTOR 7/8', 'Toyota', '2RZ', '1992', '104', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 23:48:33', '2025-03-21 23:48:33'),
(422, 'Motor 7/8 Dodge Cruise', 'MOTOR 7/8', 'Dodge', 'Cruiser', '1990', '168', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 23:49:50', '2025-03-21 23:49:50'),
(423, 'Motor Chevrolet Century 2.8L', 'MOTOR COMPLETO', 'Chevrolet', 'Century 2.8L', '1990', '106', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 23:50:39', '2025-03-21 23:50:39'),
(424, 'Motor 7/8 Ford 4 Cilindro', 'MOTOR 7/8', 'Ford', '4 Cilindro', '1990', '209', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 23:52:50', '2025-03-21 23:52:50'),
(425, 'Motor 7/8 Jeep 5.7L Challengers', 'MOTOR 7/8', 'Jeep', 'Challengers', '2000', '42', '060325', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 18, '2025-03-21 23:53:39', '2025-03-21 23:53:39'),
(426, 'Motor Honda 6 Cilindros', 'MOTOR COMPLETO', 'Honda', '6 cilindros', '2000', '34', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 23:54:23', '2025-03-21 23:54:23'),
(427, 'Motor Chevrolet Lumina 3.1L', 'MOTOR COMPLETO', 'Chevrolet', 'Lumina 3.1L', '1990', '40', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 23:55:25', '2025-03-21 23:55:25'),
(428, 'Motor 7/8 Nissan Armada BK56', 'MOTOR 7/8', 'Nissan', 'Armada BK56', '2000', '101', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 23:56:16', '2025-03-21 23:56:16'),
(429, 'Motor 7/8 Ford Focus Zetec', 'MOTOR 7/8', 'Ford', 'Focus Zetec', '2000', '51', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-21 23:57:23', '2025-03-21 23:57:23'),
(430, 'Motor 7/8 Toyota 1NZ Yaris', 'MOTOR 7/8', 'Toyota', '1NZ Yaris', '2009', '114', '240001', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 16, '2025-03-21 23:59:31', '2025-03-21 23:59:31'),
(431, 'Motor 7/8 Chevrolet 366', 'MOTOR 7/8', 'Chevrolet', '366', '1990', '57', '060325', 'APLICA', 'DISPONIBLE', '800', '800', 18, '2025-03-22 00:00:17', '2025-03-22 00:00:17'),
(433, 'Motor 7/8 Jeep 5.7 L 4G', 'MOTOR 7/8', 'Jeep', '5.7L 4G', '2012', '36S/C', '060325', 'APLICA', 'DISPONIBLE', '2.800', '2.800', 18, '2025-03-22 00:01:51', '2025-03-22 00:01:51'),
(434, 'Motor Hyundai Accel 1.6L', 'MOTOR COMPLETO', 'Hyundai', 'Accel, Río, Getz G4ED', '2008', '116', '220282', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 11, '2025-03-22 00:02:56', '2025-03-22 00:02:56'),
(435, 'Motor 7/8 Dodge Neon', 'MOTOR 7/8', 'Dodge', 'Neon', '1990', '117', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-22 00:04:41', '2025-03-22 00:04:41'),
(436, 'Motor 7/8 Toyota 2AZ', 'MOTOR 7/8', 'Toyota', '2AZ', '2008', '41', '060325', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 18, '2025-03-22 00:05:19', '2025-03-22 00:05:19'),
(437, 'Motor 7/8 Toyota 2RZ', 'MOTOR 7/8', 'Toyota', '2RZ', '1992', '354', '240222', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 20, '2025-03-22 00:06:09', '2025-03-22 00:06:09'),
(438, 'Motor 7/8 Honda F22', 'MOTOR 7/8', 'Honda', 'F22', '1990', '118', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-22 00:06:52', '2025-03-22 00:06:52'),
(439, 'Motor 7/8 Mitsubishi 6G73', 'MOTOR 7/8', 'Mitsubishi', '6G73', '1990', '119', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-22 00:08:07', '2025-03-22 00:08:07'),
(440, 'Motor Chevrolet Cavalier 2.2L', 'MOTOR COMPLETO', 'Chevrolet', 'Cavalier 2.2L', '1990', '380', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-22 00:09:19', '2025-03-22 00:09:19'),
(441, 'Motor 3/4 Toyota 22R', 'MOTOR 3/4', 'Toyota', '22R', '1992', '130', '240222', 'APLICA', 'DISPONIBLE', '800', '800', 20, '2025-03-22 00:10:42', '2025-03-22 00:10:42'),
(442, 'Motor 7/8 Ford Explorer 4 cadena', 'MOTOR 7/8', 'Ford', 'Explorer 4.0L', '2000', '120', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-22 00:11:35', '2025-03-22 00:11:35'),
(443, 'Motor Fiat', 'MOTOR COMPLETO', 'Fiat', 'Sin modelo', '1990', '121', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-22 00:12:14', '2025-03-22 00:12:14'),
(444, 'Motor 7/8 Chevrolet 262 TB1', 'MOTOR 7/8', 'Chevrolet', '262 TB1', '1992', '122', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-22 00:13:14', '2025-03-22 00:13:14'),
(445, 'Motor 7/8 Ford 200', 'MOTOR 7/8', 'Ford', '200', '1990', '123', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-22 00:13:53', '2025-03-22 00:13:53'),
(446, 'Motor 7/8 Diesel Mitsubishi 2.2L', 'MOTOR COMPLETO', 'Mitsubishi', '2.2L', '2000', '124', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-22 00:15:19', '2025-03-22 00:15:19'),
(447, 'Motor Ford 3.8L', 'MOTOR 7/8', 'Ford', '3.8L', '1990', '125', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-22 00:16:49', '2025-03-22 00:16:49'),
(448, 'Motor 7/8 Chevrolet 262 TB1', 'MOTOR 7/8', 'Chevrolet', '262 TB1', '1992', '126', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-22 00:17:32', '2025-03-22 00:17:32'),
(451, 'Motor 7/8 Toyota 2ZR', 'MOTOR 7/8', 'Toyota', '2ZR', '2016', '37S/C', '060325', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 18, '2025-03-22 00:19:29', '2025-03-22 00:19:29'),
(452, 'Motor 7/8 Chevrolet 262 TB1', 'MOTOR 7/8', 'Chevrolet', '262 TB1', '2000', '127', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-22 00:20:25', '2025-03-22 00:20:25'),
(454, 'Motor 7/8 Chévete con caja', 'MOTOR 7/8', 'Chevrolet', 'Chévete', '2000', '38S/C', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-22 00:22:24', '2025-03-22 00:22:24'),
(456, 'Motor 7/8 Chevrolet 5.3 L TM 2005', 'MOTOR 7/8', 'Chevrolet', '5.3L  TM', '2005', '39S/C', '240222', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 20, '2025-03-22 02:28:16', '2025-03-22 02:28:16'),
(457, 'Motor 7/8 Chevrolet 5.3L 2005', 'MOTOR 7/8', 'Chevrolet', '5.3L', '2005', '40S/C', '240222', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 20, '2025-03-22 02:30:43', '2025-03-22 02:30:43'),
(459, 'Motor 3/4 Ford Tritón 5.4L 2V', 'MOTOR 3/4', 'Ford', '5.4L 2v', '2005', '41S/C', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-22 02:46:46', '2025-03-22 02:46:46'),
(460, 'Motor 7/8 Toyota 2GR-CAMRY', 'MOTOR 7/8', 'Toyota', '2GR-Camry', '2008', '217', '060325', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 18, '2025-03-22 02:58:23', '2025-03-22 02:58:23'),
(461, 'Motor 7/8 Honda Civic D16', 'MOTOR 7/8', 'Honda', 'Civic D16Z6', '2000', '205', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-22 03:07:35', '2025-03-22 03:07:35'),
(463, 'Motor 7/8 Chevrolet 5.3L 2002', 'MOTOR 7/8', 'Chevrolet', '5.3L TM', '2005', '42S/C', '060325', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 18, '2025-03-22 03:09:31', '2025-03-22 03:09:31'),
(464, 'Toyota 2AZ', 'MOTOR 3/4', 'Toyota', '2AZ', '2008', '43s/c', '240222', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 20, '2025-03-22 03:17:06', '2025-03-22 04:12:51'),
(466, 'Motor 7/8 Chevrolet 262 Vortec', 'MOTOR 7/8', 'Chevrolet', '262 vortec', '1995', '44S/C', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-22 03:19:15', '2025-03-22 03:19:15'),
(468, 'Motor 7/8 Mitsubishi 16V', 'MOTOR 7/8', 'Mitsubishi', '16 valvulas', '1990', '45S/C', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-22 03:21:53', '2025-03-22 03:21:53'),
(469, 'Motor Chevrolet Captiva 3.6', 'MOTOR 7/8', 'Chevrolet', 'Captiva 3.6', '2008', 'DJ-008M', '230145', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 14, '2025-03-22 03:27:52', '2025-03-22 03:27:52'),
(470, 'Motor Jeep Rubicon 3.6L', 'MOTOR COMPLETO', 'Jeep', 'Rubicon 3.6L', '2008', '368', '230145', 'APLICA', 'DISPONIBLE', '4.000', '4.000', 14, '2025-03-22 03:29:03', '2025-03-22 03:29:03'),
(471, 'Motor Toyota 5VZ', 'MOTOR COMPLETO', 'Toyota', '5VZ', '2000', '367', '230145', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 14, '2025-03-22 03:30:08', '2025-03-22 03:30:08'),
(472, 'Motor Chevrolet 3.8L', 'MOTOR COMPLETO', 'Chevrolet', '3.8L IMPALA', '1990', '361', '060325', 'APLICA', 'DISPONIBLE', '800', '800', 18, '2025-03-22 03:31:05', '2025-03-22 03:31:05'),
(473, 'Motor 5/8 Kia 3.5L', 'MOTOR 5/8', 'Kia', '3.5L', '2009', '170', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-22 03:32:00', '2025-03-22 03:32:00'),
(474, 'Motor 7/8 Chevrolet 6.0l', 'MOTOR 7/8', 'Chevrolet', '6.0L Rey Camion', '2010', '80', '240222', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 20, '2025-03-22 03:33:40', '2025-03-22 03:33:40'),
(475, 'Motor Jeep 4.7L 8 Bujias', 'MOTOR COMPLETO', 'Jeep', '4.7L 8 B', '2007', '315', '240222', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 20, '2025-03-22 03:34:34', '2025-03-22 03:34:34'),
(476, 'Motor 7/8 Toyota 2UZ', 'MOTOR COMPLETO', 'Toyota', '2UZ', '2000', '342', '060325', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 18, '2025-03-22 03:35:36', '2025-03-22 03:35:36'),
(477, 'Motor 7/8 Toyota 2A', 'MOTOR 7/8', 'Toyota', '2AZ Previa', '2008', '46S/C', '240222', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 20, '2025-03-22 04:17:50', '2025-03-22 04:17:50'),
(478, 'Motor 7/8 Chevrolet 8.1L', 'MOTOR 7/8', 'Chevrolet', '8.1L', '1990', '55', '060325', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 18, '2025-03-22 04:21:25', '2025-03-22 04:21:25'),
(479, 'Motor 7/8 Toyota 1ZZ nueva sensación', 'MOTOR 7/8', 'TOYOTA', '1ZZ', '2008', '671', '240222', 'APLICA', 'DISPONIBLE', '1.000', '1.550', 20, '2025-03-28 00:11:02', '2025-03-28 00:11:02'),
(480, 'Toyota IZZ nueva sensación', 'MOTOR 7/8', 'TOYOTA', 'IZZ', '2008', '670', '240222', 'APLICA', 'DISPONIBLE', '1.000', '1.550', 20, '2025-03-28 00:14:16', '2025-03-28 00:14:16'),
(481, 'Motor 7/8 Nissan MR20', 'MOTOR 7/8', 'Nissan', 'MR20', '2007', '673', '240222', 'APLICA', 'DISPONIBLE', '1.000', '1.500', 20, '2025-03-28 00:20:22', '2025-03-28 00:20:22'),
(482, 'Motor 7/8 Nissan MR20', 'MOTOR 7/8', 'NISSAN', 'MR20', '2002', '672', '240222', 'APLICA', 'DISPONIBLE', '1.000', '1.500', 20, '2025-03-28 00:21:25', '2025-03-28 00:31:56'),
(483, 'Motor 7/8 chevrolet Orlando', 'MOTOR 7/8', 'Chevrolet', 'Orlando', '2010', '348', '240222', 'APLICA', 'DISPONIBLE', '1.000', '1.500', 20, '2025-03-28 00:23:01', '2025-03-28 00:23:01'),
(484, 'MOTOR 7/8 CHEVROLET ORLANDO 2.4', 'MOTOR 7/8', 'Chevrolet', 'Orlando 2.4', '2010', '47 S/C', '240222', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 20, '2025-03-28 00:34:55', '2025-03-28 00:34:55'),
(485, 'MOTOR 7/8 TOYOTA 1NZ', 'MOTOR 7/8', 'TOYOTA', '1NZ', '2008', '85', '060325', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 18, '2025-03-28 00:36:56', '2025-03-28 00:36:56'),
(486, 'MOTOR 7/8 TOYOTA INZ YARIS', 'MOTOR 7/8', 'TOYOTA', 'INZ', '2008', '630', '240222', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 20, '2025-03-28 00:38:19', '2025-03-28 00:38:19'),
(487, 'MOTOR 7/8 TOYOTA 1NZ YARIS', 'MOTOR 7/8', 'TOYOTA', 'INZ YARIS', '2008', '347', '240222', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 20, '2025-03-28 00:39:18', '2025-03-28 00:39:18'),
(488, 'MOTOR 7/8 HONDA D17A1', 'MOTOR 7/8', 'HONDA', 'CIVIC D17A1', '2005', '629', '240222', 'APLICA', 'DISPONIBLE', '1.200', '1.200', 20, '2025-03-28 00:40:19', '2025-03-28 00:40:19'),
(489, 'MOTOR 7/8 HONDA D16Y7', 'MOTOR 7/8', 'HONDA', 'CIVIC D16Y7', '2002', '627', '240222', 'APLICA', 'DISPONIBLE', '1.200', '1.200', 20, '2025-03-28 00:41:38', '2025-03-28 00:41:38'),
(490, 'MOTOR 7/8 DODGE RAM 5.7', 'MOTOR 7/8', 'DODGE', 'RAM 5.7', '2008', '49', '240222', 'APLICA', 'DISPONIBLE', '1.900', '1.900', 20, '2025-03-28 00:42:46', '2025-03-28 00:42:46'),
(491, 'MOTOR 7/8 JEEP 5.7 4G', 'MOTOR 7/8', 'JEEP', 'GRAND CHEROKEE 5.7 4G', '2012', 'D0786', '240001', 'APLICA', 'DISPONIBLE', '2.800', '2.800', 16, '2025-03-28 00:44:14', '2025-03-28 00:44:14'),
(492, 'MOTOR 7/8 JEEP 5.7 4G', 'MOTOR 7/8', 'JEEP', 'GRAND CHEROKEE 5.7 4G', '2012', '343', '240001', 'APLICA', 'DISPONIBLE', '2.800', '2.800', 16, '2025-03-28 00:45:20', '2025-03-28 00:45:20'),
(493, 'MOTOR 7/8 TOYOTA 2ZR', 'MOTOR 7/8', 'TOYOTA', 'COROLLA 2ZR', '2016', '39', '220101', 'APLICA', 'DISPONIBLE', '2.500', '2.500', 10, '2025-03-28 00:46:24', '2025-03-28 00:46:24'),
(494, 'MOTOR 7/8 TOYOTA 2ZR', 'MOTOR 7/8', 'TOYOTA', 'COROLLA 2ZR', '2016', 'D0828', '240001', 'APLICA', 'DISPONIBLE', '2.500', '2.500', 16, '2025-03-28 00:47:26', '2025-03-28 00:47:26'),
(495, 'MOTOR 7/8 TOYOTA 2AR', 'MOTOR 7/8', 'TOYOTA', 'COROLLA 2AR', '2018', '345', '240222', 'APLICA', 'DISPONIBLE', '3.000', '3.000', 20, '2025-03-28 00:48:18', '2025-03-28 00:48:18'),
(496, 'MOTOR 7/8 CHEVROLET 5.3', 'MOTOR 7/8', 'CHEVROLET', 'SILVERADO LS 5.3', '2010', '643', '240222', 'APLICA', 'DISPONIBLE', '2.200', '2.200', 20, '2025-03-28 00:50:48', '2025-03-28 00:50:48'),
(497, 'MOTOR 7/8 CHEVROLET 5.3 LS', 'MOTOR 7/8', 'CHEVROLET', 'SILVERADO 5.3 LS', '2010', '616', '240222', 'APLICA', 'DISPONIBLE', '2.200', '2.200', 20, '2025-03-28 00:55:29', '2025-03-28 00:55:29'),
(498, 'CHEVROLET SILVERADO 5.3 LS', 'MOTOR 7/8', 'CHEVROLET', 'SILVERADO 5.3 LS', '2008', '648', '240222', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 20, '2025-03-28 00:56:29', '2025-03-28 00:56:58'),
(499, 'MOTOR 7/8 CHEVROLET 5.3 LS', 'MOTOR 7/8', 'CHEVROLET', 'SILVERADO 5.3 LS', '2008', '617', '240222', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 20, '2025-03-28 00:58:10', '2025-03-28 00:58:10'),
(500, 'CHEVROLET SILVERADO 5.3 LS', 'MOTOR 7/8', 'CHEVROLET', 'SILVERADO 5.3 LS', '2010', '646', '240222', 'APLICA', 'DISPONIBLE', '2.200', '2.200', 20, '2025-03-28 00:59:28', '2025-03-28 00:59:55'),
(501, 'MOTOR 7/8 FORD EXPLORER 3.5', 'MOTOR 7/8', 'FORD', 'EXPLORER 3.5', '2012', '580', '240222', 'APLICA', 'DISPONIBLE', '2.800', '2.800', 20, '2025-03-28 01:03:11', '2025-03-28 01:03:11'),
(502, 'MOTOR 7/8 TOYOTA 2ZR', 'MOTOR 7/8', 'TOYOTA', '2ZR', '2005', '70', '220101', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 10, '2025-03-28 01:06:20', '2025-03-28 01:06:20'),
(503, 'MOTOR 7/8 TOYOTA 2ZR', 'MOTOR 7/8', 'TOYOTA', '2ZR', '2005', '69', '220101', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 10, '2025-03-28 01:07:49', '2025-03-28 01:07:49'),
(504, 'MOTOR 7/8 JEEP 5.7 4G', 'MOTOR 7/8', 'JEEP', 'GRAND CHEROKEE 5.7 4G', '2012', '600', '240222', 'APLICA', 'DISPONIBLE', '2.800', '2.800', 20, '2025-03-28 01:12:07', '2025-03-28 01:12:07'),
(505, 'MOTOR 7/8 JEEP 5.7 4G', 'MOTOR 7/8', 'JEEP', 'GRAND CHEROKEE 5.7 4G', '2012', '601', '240222', 'APLICA', 'DISPONIBLE', '2.800', '2.800', 20, '2025-03-28 01:12:55', '2025-03-28 01:12:55'),
(506, 'MOTOR 7/8 JEEP 5.7 4G', 'MOTOR 7/8', 'JEEP', 'GRAND CHEROKEE 5.7 4G', '2012', '602', '240222', 'APLICA', 'DISPONIBLE', '2.800', '2.800', 20, '2025-03-28 01:13:34', '2025-03-28 01:13:34'),
(507, 'MOTOR 7/8 JEEP 5.7 4G', 'MOTOR 7/8', 'JEEP', 'GRAND CHEROKEE 5.7 4G', '2012', '693', '240222', 'APLICA', 'DISPONIBLE', '2.800', '2.800', 20, '2025-03-28 01:14:04', '2025-03-28 01:14:04'),
(508, 'MOTOR 7/8 JEEP 5.7 4G', 'MOTOR 7/8', 'JEEP', 'GRAND CHEROKEE 5.7 4G', '2012', '334', '240001', 'APLICA', 'DISPONIBLE', '2.800', '2.800', 16, '2025-03-28 01:14:58', '2025-03-28 01:14:58'),
(509, 'MOTOR 7/8 JEEP 5.7 4G', 'MOTOR 7/8', 'JEEP', 'GRAND CHEROKEE 5.7 4G', '2012', '344', '240001', 'APLICA', 'DISPONIBLE', '2.800', '2.800', 16, '2025-03-28 01:16:04', '2025-03-28 01:16:04'),
(510, 'MOTOR HONDA K24 D17A2', 'MOTOR 7/8', 'HONDA', 'K24 D17A2', '2005', '631', '240222', 'APLICA', 'DISPONIBLE', '1.200', '1.200', 20, '2025-03-28 01:25:34', '2025-03-28 01:25:34'),
(511, 'MOTOR 7/8 CHEVROLET ORLANDO 2.4', 'MOTOR 7/8', 'CHEVROLET', 'ORLANDO 2.4', '2010', 'D0835', '240001', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 16, '2025-03-28 01:27:03', '2025-03-28 01:27:03'),
(512, 'MOTOR FORD EXPLORER 3.5', 'MOTOR 7/8', 'FORD', 'EXPLORER 3.5', '2012', '581', '240222', 'APLICA', 'DISPONIBLE', '2.800', '2.800', 20, '2025-03-28 01:29:24', '2025-03-28 01:29:24'),
(513, 'MOTOR 7/8 TOYOTA 2UZ', 'MOTOR 7/8', 'TOYOTA', 'TUNDRA 2UZ', '2008', '650', '240222', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 20, '2025-03-28 01:32:02', '2025-03-28 01:32:02'),
(514, 'MOTOR 7/8 CHEVROLET 5.3 LS', 'MOTOR 7/8', 'CHEVROLET', '5.3 LS', '2010', '644', '240222', 'APLICA', 'DISPONIBLE', '2.200', '2.200', 20, '2025-03-28 01:32:59', '2025-03-28 01:32:59'),
(515, 'MOTOR 7/8 CHEVROLET 5.3 LS', 'MOTOR 7/8', 'CHEVROLET', 'SILVERADO 5.3 LS', '2008', '647', '240222', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 20, '2025-03-28 01:33:53', '2025-03-28 01:33:53'),
(516, 'MOTOR 7/8 TOYOTA 2UZ', 'MOTOR 7/8', 'TOYOTA', 'TUNDRA 2UZ', '2008', '661', '240222', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 20, '2025-03-28 01:34:47', '2025-03-28 01:34:47'),
(517, 'MOTOR 7/8 CHEVROLET VITARA', 'MOTOR 3/4', 'CHEVROLET', 'VITARA', '2006', '48/SC', '240222', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 20, '2025-03-28 01:37:48', '2025-03-28 01:37:48'),
(518, 'MOTOR 7/8 TOYOTA 2UZ', 'MOTOR 7/8', 'TOYOTA', 'TUNDRA 2UZ', '2008', '662', '240222', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 20, '2025-03-28 01:38:41', '2025-03-28 01:38:41'),
(519, 'MOTOR 7/8 HONDA K24 D17A2', 'MOTOR 7/8', 'HONDA', 'K24 D17A2', '2006', '632', '240222', 'APLICA', 'DISPONIBLE', '1.200', '1.200', 20, '2025-03-28 01:39:51', '2025-03-28 01:39:51'),
(520, 'MOTOR 7/8 FORD COYOTE 5.0', 'MOTOR 7/8', 'FORD', 'COYOTE 5.0', '2015', '322', '240001', 'APLICA', 'DISPONIBLE', '3.000', '3.000', 16, '2025-03-28 01:41:39', '2025-03-28 01:41:39'),
(521, 'MOTOR 7/8 FORD COYOTE 5.0', 'MOTOR 7/8', 'FORD', 'COYOTE 5.0', '2015', '318', '240222', 'APLICA', 'DISPONIBLE', '3.000', '3.000', 20, '2025-03-28 01:42:39', '2025-03-28 01:42:39'),
(522, 'MOTOR 7/8 FORD COYOTE 5.0', 'MOTOR 7/8', 'FORD', 'COYOTE 5.0', '2015', '285', '240222', 'APLICA', 'DISPONIBLE', '3.000', '3.000', 20, '2025-03-28 01:44:03', '2025-03-28 01:44:03'),
(523, 'CHEVROLET SILVERADO 5.3', 'MOTOR 7/8', 'CHEVROLET', 'SILVERADO 5.3', '2008', '49 S/C', '240222', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 20, '2025-03-28 01:48:30', '2025-03-28 01:48:55'),
(525, 'Motor 7/8 Nissan Pathafinder KA24', 'MOTOR 7/8', 'Nissan', 'Pathfinder', '2010', '489-1', '250090', 'APLICA', 'DISPONIBLE', '2.300', '2.300', 21, '2025-03-29 20:57:07', '2025-03-29 20:57:07'),
(526, 'Motor 7/8 Chevrolet Vitara J20', 'MOTOR 7/8', 'Chevrolet', 'Vitara J20', '2008', '490-2', '250090', 'APLICA', 'DISPONIBLE', '2.200', '2.200', 21, '2025-03-29 20:58:18', '2025-03-29 20:58:18'),
(527, 'Motor 7/8 Hyundai Tucson 2.0L', 'MOTOR 7/8', 'Hyundai', 'Tucson 2.0L', '2009', '491-3', '250090', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 21, '2025-03-29 20:59:18', '2025-03-29 20:59:18'),
(528, 'Motor 7/8 Hyundai Tucson 2.0L', 'MOTOR 7/8', 'Hyundai', 'Tucson 2.0L', '2008', '492-4', '250090', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 21, '2025-03-29 20:59:56', '2025-03-29 20:59:56'),
(529, 'Motor 7/8 Hyundai Santa Fe VVTi', 'MOTOR 7/8', 'Hyundai', 'Santa Fe VVTI', '2009', '493-5', '250090', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 21, '2025-03-29 21:01:17', '2025-03-29 21:03:52'),
(530, 'Motor Toyota 3RZ', 'MOTOR COMPLETO', 'Toyota', '3RZ Hallix', '2009', '494-6', '250090', 'APLICA', 'DISPONIBLE', '2.500', '2.500', 21, '2025-03-29 21:03:13', '2025-03-29 21:03:13'),
(531, 'Motor 7/8 Toyota 1GR-FE', 'MOTOR 7/8', 'Toyota', '1GR-FE', '2008', '495-7', '250090', 'APLICA', 'DISPONIBLE', '4.600', '4.600', 21, '2025-03-29 21:06:43', '2025-03-29 23:06:17'),
(532, 'Motor 7/8 Toyota 1GR-FE', 'MOTOR 7/8', 'Toyota', '1GR-FE', '2008', '496-8', '250090', 'APLICA', 'DISPONIBLE', '4.600', '4.600', 21, '2025-03-29 21:08:14', '2025-03-29 23:07:33'),
(533, 'Motor 7/8 Nissan QR25', 'MOTOR 7/8', 'Nissan', 'QR25', '2008', '497-9', '250090', 'APLICA', 'DISPONIBLE', '1.800', '1.800', 21, '2025-03-29 21:09:52', '2025-03-29 23:08:29'),
(534, 'Motor 7/8 Ford Tritón 5.4L 2V', 'MOTOR 7/8', 'Ford', 'Tritón 5.4L 2V', '2008', '498-10', '250090', 'APLICA', 'DISPONIBLE', '1.800', '1.800', 21, '2025-03-29 21:12:26', '2025-03-29 23:18:16'),
(535, 'Motor 7/8 Ford Tritón 5.4L 2V', 'MOTOR 7/8', 'Ford', 'Tritón 5.4L 2V', '2008', '499-11', '250090', 'APLICA', 'DISPONIBLE', '1.800', '1.800', 21, '2025-03-29 21:13:34', '2025-03-29 23:19:05'),
(536, 'Motor 7/8 Ford Tritón 5.4L 2V', 'MOTOR 7/8', 'Ford', 'Tritón 5.4L 2v', '2008', '500-12', '250090', 'APLICA', 'DISPONIBLE', '1.800', '1.800', 21, '2025-03-29 21:14:28', '2025-03-29 23:19:24'),
(537, 'Motor 7/8 Ford Tritón 5.4L 2V', 'MOTOR 7/8', 'Ford', 'Tritón 5.4L 2v', '2008', '501-13', '250090', 'APLICA', 'DISPONIBLE', '1.800', '1.800', 21, '2025-03-29 21:15:20', '2025-03-29 23:20:11'),
(538, 'Motor 7/8 Jeep Grand Cherokee 5.7L 4G', 'MOTOR 7/8', 'Jeep', 'Grand Cherokee 5.7L 4G', '2012', '502-14', '250090', 'APLICA', 'DISPONIBLE', '2.800', '2.800', 21, '2025-03-29 21:16:50', '2025-03-29 23:20:41'),
(539, 'Motor 7/8 Jeep Grand Cherokee 5.7L 4G', 'MOTOR 7/8', 'Jeep', 'Grand Cherokee 5.7L 4G', '2012', '503-15', '250090', 'APLICA', 'DISPONIBLE', '2.800', '2.800', 21, '2025-03-29 21:17:37', '2025-03-29 23:21:13'),
(540, 'Motor 7/8 Chevrolet 5.3L 2010', 'MOTOR 7/8', 'Chevrolet', '5.3L', '2010', '504-16', '250090', 'APLICA', 'DISPONIBLE', '2.300', '2.300', 21, '2025-03-29 21:21:58', '2025-03-29 23:21:55'),
(541, 'Motor 7/8 Chevrolet 5.3L 2008', 'MOTOR 7/8', 'Chevrolet', '5.3L', '2008', '505-17', '250090', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 21, '2025-03-29 21:22:52', '2025-03-29 23:23:25'),
(542, 'Motor 7/8 Jeep 4.7L 8B EGR', 'MOTOR 7/8', 'Chevrolet', '5.3L 2008', '2008', '506-17', '250090', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 21, '2025-03-29 21:24:05', '2025-03-29 23:24:58'),
(543, 'Motor 7/8 Jeep 4.7L 8B EGR', 'MOTOR 7/8', 'Jeep', 'Grand Cherokee 4.7L 8B', '2008', '507-19', '250090', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 21, '2025-03-29 23:26:22', '2025-03-29 23:26:22'),
(544, 'Motor 7/8 Jeep 4.7L 8B EGR', 'MOTOR 7/8', 'Jeep', 'Grand Cherokee 4.7L 8B EGR', '2008', '508-20', '250090', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 21, '2025-03-29 23:27:51', '2025-03-29 23:27:51'),
(545, 'Motor 7/8 Jeep 4.7L 8B EGR', 'MOTOR 7/8', 'Jeep', 'Jeep Grand Cherokee 4.7L 8B EGR', '2008', '509-21', '250090', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 21, '2025-03-29 23:29:40', '2025-03-29 23:29:40'),
(546, 'Motor 7/8 Jeep 4.7L 8B EGR', 'MOTOR 7/8', 'Jeep', 'Jeep Grand Cherokee 4.7L 8B EGR', '2008', '510-22', '250090', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 21, '2025-03-29 23:30:38', '2025-03-29 23:30:38'),
(547, 'Motor 7/8 Ford FX4 5.4L 3V', 'MOTOR 7/8', 'Ford', 'FX4 5.4L 3V', '2007', '511-23', '250090', 'APLICA', 'DISPONIBLE', '1.900', '1.900', 21, '2025-03-30 00:15:08', '2025-03-30 00:15:08'),
(548, 'Motor 7/8 Ford FX4 5.4L 3V', 'MOTOR 7/8', 'Ford', 'Fx4 5.4L 3V', '2008', '512-24', '250090', 'APLICA', 'DISPONIBLE', '1.900', '1.900', 21, '2025-03-30 00:16:56', '2025-03-30 00:16:56'),
(549, 'Motor 7/8 Chevrolet Orlando 2.4L', 'MOTOR 7/8', 'Chevrolet', 'Orlando 2.4L', '2010', '513-25', '250090', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 21, '2025-03-30 00:18:28', '2025-03-30 00:18:28'),
(550, 'Motor 7/8 Toyota 2TR', 'MOTOR 7/8', 'Toyota', '2TR', '2015', '514-26', '250090', 'APLICA', 'DISPONIBLE', '4.000', '4.000', 21, '2025-03-30 00:19:41', '2025-03-30 00:19:41'),
(551, 'Motor 7/8 Toyota 2TR VVTi dual', 'MOTOR 7/8', 'Toyota', '2TR VVTI Dual', '2008', '515-27', '250090', 'APLICA', 'DISPONIBLE', '4.000', '4.000', 21, '2025-03-30 00:21:24', '2025-03-30 00:21:24'),
(552, 'Motor 7/8 Chevrolet Rey Camión 6.0L', 'MOTOR 7/8', 'Chevrolet', 'Rey Camión 6.0L', '2010', '516-28', '250090', 'APLICA', 'DISPONIBLE', '2.500', '2.500', 21, '2025-03-30 00:23:15', '2025-03-30 00:23:15'),
(553, 'Motor 7/8 Chevrolet Rey Camión 2010', 'MOTOR 7/8', 'Chevrolet', 'Rey Camión 6.0L', '2010', '517-29', '250090', 'APLICA', 'DISPONIBLE', '2.500', '2.500', 21, '2025-03-30 00:24:23', '2025-03-30 00:24:23'),
(554, 'Motor 7/8 Ford Explorer 3.5L', 'MOTOR 7/8', 'Ford', 'Ford Explorer 3.5L', '2015', '518-30', '250090', 'APLICA', 'DISPONIBLE', '3.000', '3.000', 21, '2025-03-30 00:25:43', '2025-03-30 00:25:43'),
(555, 'Motor 7/8 Ford Explorer 3.5L', 'MOTOR 7/8', 'Ford', 'Explorer 3.5L', '2015', '519-31', '250090', 'APLICA', 'DISPONIBLE', '3.000', '3.000', 21, '2025-03-30 00:26:44', '2025-03-30 00:26:44'),
(556, 'Motor 7/8 Ford Explorer 3.5L', 'MOTOR 7/8', 'Ford', 'Explorer 3.5L', '2015', '520-32', '250090', 'APLICA', 'DISPONIBLE', '3.000', '3.000', 21, '2025-03-30 00:27:50', '2025-03-30 00:27:50');
INSERT INTO `partidas` (`id`, `item`, `tipo`, `marca`, `modelo`, `año`, `codInv`, `expediente`, `condicion`, `status`, `price`, `price_sale`, `container_id`, `created_at`, `updated_at`) VALUES
(557, 'Motor 7/8 Ford Explorer 3.5L', 'MOTOR 7/8', 'Ford', 'Ford Explorer 3.5L', '2015', '521-33', '250090', 'APLICA', 'DISPONIBLE', '3.000', '3.000', 21, '2025-03-30 00:28:47', '2025-03-30 00:28:47'),
(558, 'Motor 7/8 Ford EcoSport 2.0L', 'MOTOR 7/8', 'Ford', 'EcoSport 2.0L', '2008', '522-34', '250090', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 21, '2025-03-30 00:30:49', '2025-03-30 00:30:49'),
(559, 'Motor 7/8 Ford EcoSport 2.0L', 'MOTOR 7/8', 'Ford', 'EcoSport 2.0L', '2008', '523-35', '250090', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 21, '2025-03-30 00:32:10', '2025-03-30 00:32:10'),
(560, 'Motor 7/8 Ford EcoSport 2.0L', 'MOTOR 7/8', 'Ford', 'EcoSport 2.0L', '2008', '524-36', '250090', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 21, '2025-03-30 00:34:59', '2025-03-30 00:34:59'),
(561, 'Motor 7/8 Ford EcoSport 2.0L', 'MOTOR 7/8', 'Ford', 'EcoSport 2.0L', '2008', '525-37', '250090', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 21, '2025-03-30 00:35:54', '2025-03-30 00:35:54'),
(562, 'Motor 7/8 Ford EcoSport 2.0L', 'MOTOR 7/8', 'Ford', 'EcoSport 2.0L', '2008', '526-38', '250090', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 21, '2025-03-30 00:36:36', '2025-03-30 00:36:36'),
(563, 'Motor 7/8 Ford Fortaleza 4.6L 2V', 'MOTOR 7/8', 'Ford', 'Fortaleza 4.6L 2V', '2008', '527-39', '250090', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 21, '2025-03-30 00:38:09', '2025-03-30 00:41:19'),
(564, 'Motor 7/8 Ford Fortaleza 4.6L 2V', 'MOTOR 7/8', 'Ford', 'Fortaleza 4.6L 2V', '2008', '528-40', '250090', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 21, '2025-03-30 00:40:40', '2025-03-30 00:40:40'),
(565, 'Motor 7/8 Ford Fortaleza 4.6L 2v', 'MOTOR 7/8', 'Ford', 'Fortaleza 4.6L 2V', '2008', '529-41', '250090', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 21, '2025-03-30 00:45:07', '2025-03-30 00:45:07'),
(566, 'Motor 7/8 Ford Fortaleza 4.6L 2v', 'MOTOR 7/8', 'Ford', 'Fortaleza 4.6L 2V', '2008', '530-42', '250090', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 21, '2025-03-30 00:47:00', '2025-03-30 00:47:00'),
(567, 'Motor 7/8 Ford Ranger 2.3L', 'MOTOR 7/8', 'Ford', 'Ranger 2.3L', '2010', '531-43', '250090', 'APLICA', 'DISPONIBLE', '1.800', '1.800', 21, '2025-04-01 00:37:52', '2025-04-01 00:37:52'),
(568, 'Motor 7/8 Ford Ranger 2.3L', 'MOTOR 7/8', 'Ford', 'Ranger 2.3L', '2008', '532-44', '250090', 'APLICA', 'DISPONIBLE', '1.800', '1.800', 21, '2025-04-01 00:40:44', '2025-04-01 00:40:44'),
(569, 'Motor 7/8 Nissan MR18', 'MOTOR 7/8', 'Nissan', 'MR18', '2008', '533-45', '250090', 'APLICA', 'DISPONIBLE', '1.800', '1.800', 21, '2025-04-01 00:41:44', '2025-04-01 00:41:44'),
(570, 'Motor 7/8 Toyota 1ZZ Nueva Sensación', 'MOTOR 7/8', 'Toyota', '1ZZ Nueva Sensación', '2008', '535-46', '250090', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 21, '2025-04-01 00:45:25', '2025-04-01 00:46:01'),
(571, 'Motor 7/8 Toyota 3RZ', 'MOTOR 7/8', 'Toyota', '3RZ', '2008', '535-47', '250090', 'APLICA', 'DISPONIBLE', '2.300', '2.300', 21, '2025-04-01 00:50:38', '2025-04-01 00:50:38'),
(572, 'Motor 7/8 Nissan KA24', 'MOTOR 7/8', 'Nissan', 'KA24', '2007', '536-48', '250090', 'APLICA', 'DISPONIBLE', '2.300', '2.300', 21, '2025-04-01 00:53:16', '2025-04-01 00:53:16'),
(573, 'Motor 7/8 Ford Escape 3.0L TA', 'MOTOR 7/8', 'Ford', 'Escape', '2008', '537-49', '250090', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 21, '2025-04-01 00:54:57', '2025-04-01 00:54:57'),
(575, 'Motor 7/8 Ford Escape 3.0L', 'MOTOR 7/8', 'Ford', 'Escape TA', '2008', '538-50', '250090', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 21, '2025-04-01 00:57:58', '2025-04-01 00:57:58'),
(576, 'Motor 7/8 Ford Fusion 3.0L', 'MOTOR 7/8', 'Ford', 'Fusión 3.0L', '2008', '539-51', '250090', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 21, '2025-04-01 01:07:15', '2025-04-01 01:07:15'),
(577, 'Motor 7/8 Ford Fusion 3.0L', 'MOTOR 7/8', 'Ford', 'Fisio 3.0L', '2008', '540-52', '250090', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 21, '2025-04-01 01:09:33', '2025-04-01 01:09:33'),
(578, 'Motor 7/8 Toyota 2UZ', 'MOTOR 7/8', 'Toyota', '2UZ 4.7L Tundra', '2008', '541-53', '250090', 'APLICA', 'DISPONIBLE', '3.000', '3.000', 21, '2025-04-01 01:11:19', '2025-04-01 01:11:19'),
(579, 'Motor 7/8 Toyota 2UZ 4.7L Tundra', 'MOTOR 7/8', 'Toyota', '2UZ 4.7L Tundra', '2008', '542-54', '250090', 'APLICA', 'DISPONIBLE', '3.000', '3.000', 21, '2025-04-01 01:12:59', '2025-04-01 01:12:59'),
(580, 'Motor 7/8 Toyota 2UZ 4.7L Tundra', 'MOTOR 7/8', 'Toyota', 'Tundra 4.7L', '2008', '543-55', '250090', 'APLICA', 'DISPONIBLE', '3.000', '3.000', 21, '2025-04-01 01:13:52', '2025-04-01 01:13:52'),
(581, 'Motor 7/8 Ford 4.6L 3V', 'MOTOR 7/8', 'Ford', 'Explorer 3V', '2008', '544-56', '250090', 'APLICA', 'DISPONIBLE', '2.300', '2.300', 21, '2025-04-01 01:15:26', '2025-04-01 01:15:26'),
(582, 'Motor 7/8 Ford 4.6L 3V', 'MOTOR 7/8', 'Ford', 'Explorer 3V', '2008', '545-57', '250090', 'APLICA', 'DISPONIBLE', '2.300', '2.300', 21, '2025-04-01 01:16:41', '2025-04-01 01:16:41'),
(583, 'Motor 7/8 Ford 4.6L 3V', 'MOTOR 7/8', 'Ford', 'Explorer 3V', '2008', '546-58', '250090', 'APLICA', 'DISPONIBLE', '2.300', '2.300', 21, '2025-04-01 01:17:40', '2025-04-01 01:17:40'),
(584, 'Motor 7/8 Ford 4.6L 3V', 'MOTOR 7/8', 'Ford', 'Explorer 3V', '2008', '547-59', '250090', 'APLICA', 'DISPONIBLE', '2.300', '2.300', 21, '2025-04-01 01:18:37', '2025-04-01 01:18:37'),
(585, 'Motor 7/8 Jeep Grand Cherokee 4.7L 16B', 'MOTOR 7/8', 'Jeep', 'Grand Cherokee 4.7L 16B', '2009', '548-60', '250090', 'APLICA', 'DISPONIBLE', '2.300', '2.300', 21, '2025-04-01 01:21:10', '2025-04-01 01:21:10'),
(586, 'Motor 7/8 Jeep Grand Cherokee 4.7L 16B', 'MOTOR 7/8', 'Jeep', 'Grand Cherokee 4.6L 16 B', '2009', '549-61', '250090', 'APLICA', 'DISPONIBLE', '2.300', '2.300', 21, '2025-04-01 01:23:12', '2025-04-01 01:23:12'),
(587, 'Motor 7/8 Jeep Grand Cherokee 4.7L 16B', 'MOTOR 7/8', 'Jeep', 'Grand Cherokee 4.7L 16B', '2010', '550-62', '250090', 'APLICA', 'DISPONIBLE', '2.300', '2.300', 21, '2025-04-01 01:24:02', '2025-04-01 01:24:02'),
(588, 'Motor 7/8 Grand Cherokee 4.7 L 8B', 'MOTOR 7/8', 'Jeep', 'Grand Cherokee 4.7L 8B', '2010', '551-63', '250090', 'APLICA', 'DISPONIBLE', '2.300', '2.300', 21, '2025-04-01 01:25:21', '2025-04-12 17:15:22'),
(589, 'Motor 7/8 Jeep Grand Cherokee 4.7L 16B', 'MOTOR 7/8', 'Jeep', 'Grand Cherokee 4.7L 16B', '2010', '552-64', '250090', 'APLICA', 'DISPONIBLE', '2.300', '2.300', 21, '2025-04-01 01:26:12', '2025-04-01 01:26:12'),
(590, 'Motor 7/8 Jeep Grand Cherokee 4.7L 16B', 'MOTOR 7/8', 'Jeep', 'Grand Cherokee 4.7L 16B', '2010', '553-65', '250090', 'APLICA', 'DISPONIBLE', '2.300', '2.300', 21, '2025-04-01 01:27:04', '2025-04-01 01:27:04'),
(591, 'Motor 7/8 Ford 300', 'MOTOR 7/8', 'Ford', '300', '1980', '554-66', '250090', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 21, '2025-04-01 01:28:04', '2025-04-01 01:28:04'),
(592, 'Motor 7/8 Ford 300', 'MOTOR 7/8', 'Ford', '300', '1980', '555-67', '250090', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 21, '2025-04-01 01:28:51', '2025-04-01 01:28:51'),
(593, 'Motor 7/8 Chevrolet Orlando 2.4L', 'MOTOR 7/8', 'Chevrolet', 'Orlando 2.4L', '1980', '556-68', '250090', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 21, '2025-04-01 01:29:50', '2025-04-01 01:29:50'),
(594, 'Motor 7/8 Nissan K24', 'MOTOR 7/8', 'Nissan', 'K24', '2007', '587-69', '250090', 'APLICA', 'DISPONIBLE', '1.800', '1.800', 21, '2025-04-01 01:31:37', '2025-04-01 01:31:37'),
(595, 'Motor 7/8 Ford Explorer 3.5L', 'MOTOR 7/8', 'Ford', 'Explorer 3.5L', '2012', '558-70', '250090', 'APLICA', 'DISPONIBLE', '2.800', '2.800', 21, '2025-04-01 01:32:21', '2025-04-01 01:32:21'),
(596, 'Motor 7/8 Chevrolet Rey Camion 6.0L', 'MOTOR 7/8', 'Chevrolet', 'Rey Camion 6.0L', '2010', '559-71', '250090', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 21, '2025-04-01 01:33:15', '2025-04-01 01:33:15'),
(597, 'Motor 7/8 Chevrolet Rey Camión 6.0L', 'MOTOR 7/8', 'Chevrolet', 'Rey Camión 6.0L', '2010', '560-72', '250090', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 21, '2025-04-01 01:34:13', '2025-04-01 01:34:13'),
(598, 'Motor 7/8 Chevrolet Rey Camión 6.0L', 'MOTOR 7/8', 'Chevrolet', 'Rey Camión 6.0L', '2010', '561-73', '250090', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 21, '2025-04-01 01:35:25', '2025-04-01 01:35:25'),
(599, 'Motor 7/8 Chevrolet Rey Camión 6.0L', 'MOTOR 7/8', 'Chevrolet', 'Rey Camión 6.0L', '2010', '562-74', '250090', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 21, '2025-04-01 01:36:30', '2025-04-01 01:36:30'),
(600, 'Motor 7/8 Ford Super Duty 6.2L', 'MOTOR 7/8', 'Ford', 'Super Duty 6.2L', '2010', '563-75', '250090', 'APLICA', 'DISPONIBLE', '4.000', '4.000', 21, '2025-04-01 01:37:42', '2025-04-01 01:37:42'),
(601, 'Motor 7/8 Ford Super Duty 6.2L', 'MOTOR 7/8', 'Ford', 'Super Duty 6.2L', '2010', '564-76', '250090', 'APLICA', 'DISPONIBLE', '4.000', '4.000', 21, '2025-04-01 01:38:36', '2025-04-01 01:38:36'),
(602, 'Motor 7/8 Ford Super Duty 6.2L', 'MOTOR 7/8', 'Motor 7/8 Ford Super Duty 6.2L', 'Super Duty 6.2L', '2010', '565-77', '250090', 'APLICA', 'DISPONIBLE', '4.000', '4.000', 21, '2025-04-01 01:40:08', '2025-04-01 01:40:08'),
(603, 'Motor 7/8 Ford Super Duty 6.2L', 'MOTOR 7/8', 'Ford', 'Super Duty 6.2L', '2010', '566-78', '250090', 'APLICA', 'DISPONIBLE', '4.000', '4.000', 21, '2025-04-01 01:40:57', '2025-04-01 01:40:57'),
(604, 'Motor 7/8 Ford 4.6L 3V', 'MOTOR 7/8', 'Ford', '4.6L 3V Explorer', '2010', '567-79', '250090', 'APLICA', 'DISPONIBLE', '2.300', '2.300', 21, '2025-04-01 01:42:03', '2025-04-01 01:42:03'),
(605, 'Motor Ford Ranger 2.3L', 'MOTOR 7/8', 'Ford', 'Explorer 4.6L 3V', '2010', '568-80', '250090', 'APLICA', 'DISPONIBLE', '2.300', '2.300', 21, '2025-04-01 01:43:42', '2025-04-01 01:43:42'),
(606, 'Motor 7/8 Ford 4.6L 3V', 'MOTOR 7/8', 'Ford', 'Explorer 3V', '2009', '569-81', '250090', 'APLICA', 'DISPONIBLE', '2.300', '2.300', 21, '2025-04-01 01:44:41', '2025-04-01 01:44:41'),
(607, 'Motor Toyota 2RZ', 'MOTOR COMPLETO', 'Toyota', '2RZ', '1995', '570-82', '250089', 'APLICA', 'DISPONIBLE', '2.200', '2.200', 22, '2025-04-01 13:33:26', '2025-04-01 13:33:26'),
(608, 'Motor Ford Mazda PY', 'MOTOR COMPLETO', 'Ford', 'Mazda', '2014', '571-83', '250089', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 22, '2025-04-01 13:34:42', '2025-04-01 13:34:42'),
(609, 'Motor Toyota 2AZ Previa-Camry', 'MOTOR COMPLETO', 'Toyota', '2AZ', '2008', '572-84', '250089', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 22, '2025-04-01 13:35:54', '2025-04-01 13:35:54'),
(610, 'Motor Toyota 5VZ Prado', 'MOTOR COMPLETO', 'Toyota', '5VZ Prado', '2002', '573-85', '250089', 'APLICA', 'DISPONIBLE', '1.900', '1.900', 22, '2025-04-01 13:36:53', '2025-04-01 13:36:53'),
(611, 'Motor Hyundai G4KE', 'MOTOR COMPLETO', 'Hyundai', 'G4KE', '2008', '574-86', '250089', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 22, '2025-04-01 13:37:51', '2025-04-01 13:37:51'),
(612, 'Motor Hyundai G4KH', 'MOTOR COMPLETO', 'Hyundai', 'G4KH', '2008', '575-87', '250089', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 22, '2025-04-01 13:39:04', '2025-04-01 13:39:04'),
(613, 'Motor Hyundai G6EA', 'MOTOR COMPLETO', 'Hyundai', 'G6DA Santa Fe', '2010', '576-88', '250089', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 22, '2025-04-01 13:40:24', '2025-04-01 13:40:24'),
(614, 'Motor Chevrolet Grand Vitara XL5', 'MOTOR COMPLETO', 'Chevrolet', 'Grand Vitara XL5', '2008', '577-89', '250089', 'APLICA', 'DISPONIBLE', '1.800', '1.800', 22, '2025-04-01 13:41:36', '2025-04-01 13:41:36'),
(615, 'Motor 7/8 Toyota 5VZ Prado', 'MOTOR COMPLETO', 'Toyota', '5VZ Prado', '2008', '578-90', '250089', 'APLICA', 'DISPONIBLE', '1.800', '1.800', 22, '2025-04-01 15:49:18', '2025-04-01 15:49:18'),
(616, 'Motor Chevrolet Cruzer 1.8L', 'MOTOR COMPLETO', 'Chevrolet', 'Cruzer', '2008', '579-91', '250089', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 22, '2025-04-01 15:50:30', '2025-04-01 15:50:30'),
(617, 'Motor Chevrolet Ecotec 1.4L', 'MOTOR COMPLETO', 'Chevrolet', 'Ecotec', '2008', '580-92', '250089', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 22, '2025-04-01 15:52:02', '2025-04-01 15:52:02'),
(618, 'Motor Toyota Yaris 1NZ', 'MOTOR COMPLETO', 'Toyota', '1NZ-YARIS', '2008', '581-93', '250089', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 22, '2025-04-01 19:33:40', '2025-04-01 19:33:40'),
(619, 'Motor Toyota 2AZ Previa', 'MOTOR COMPLETO', 'Toyota', 'Previa 2AZ', '2010', '582-94', '250089', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 22, '2025-04-01 19:34:28', '2025-04-01 19:34:28'),
(620, 'Motor Chevrolet Orlando 2.4L', 'MOTOR COMPLETO', 'Chevrolet', 'Orlando 2.4L', '2010', '583-95', '250089', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 22, '2025-04-01 19:35:36', '2025-04-01 19:35:36'),
(621, 'Motor Chevrolet Cruze', 'MOTOR COMPLETO', 'Chevrolet', 'Cruze', '2008', '584-96', '250089', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 22, '2025-04-01 19:39:04', '2025-04-01 19:39:04'),
(622, 'Motor Chevrolet Cruze 1.6L', 'MOTOR 7/8', 'Chevrolet', 'Cruze', '2010', '585-97', '250089', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 22, '2025-04-01 19:41:51', '2025-04-01 19:41:51'),
(623, 'Motor Dodge Caliber 2.4L', 'MOTOR COMPLETO', 'Dodge', 'Caliber 2.4L', '2008', '586-98', '250089', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 22, '2025-04-01 19:48:09', '2025-04-01 19:48:09'),
(624, 'Motor Chevrolet 5.3L TM', 'MOTOR COMPLETO', 'Chevrolet', '5.3L Silverado', '2008', '587-99', '250089', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 22, '2025-04-01 19:49:07', '2025-04-01 19:49:07'),
(625, 'Motor Chevrolet 5.3L 2008', 'MOTOR COMPLETO', 'Chevrolet', 'Silverado 5.3L', '2008', '588-100', '250089', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 22, '2025-04-02 02:10:40', '2025-04-02 02:10:40'),
(626, 'Motor Chevrolet 5.3L 2008', 'MOTOR COMPLETO', 'Chevrolet', 'Silverado 5.3L', '2008', '589-101', '250089', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 22, '2025-04-02 02:12:42', '2025-04-02 02:12:42'),
(627, 'Motor Jeep Dodge Ram 5.7L', 'MOTOR COMPLETO', 'Jeep', 'Dodge Ram 5.7L', '2008', '590-102', '250089', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 22, '2025-04-02 02:14:53', '2025-04-02 02:14:53'),
(628, 'Motor Chevrolet 6.2L', 'MOTOR COMPLETO', 'Chevrolet', '6.2L', '2010', '591-103', '250089', 'APLICA', 'DISPONIBLE', '4.000', '4.000', 22, '2025-04-02 02:16:47', '2025-04-02 02:16:47'),
(629, 'Motor Jeep Grand Cherokee 4.7L 8B EGR', 'MOTOR COMPLETO', 'Jeep', 'Grand Cherokee 4.7L 8B EGR', '2008', '592-104', '250089', 'APLICA', 'DISPONIBLE', '2.300', '2.300', 22, '2025-04-02 02:18:49', '2025-04-02 02:18:49'),
(630, 'Motor Toyota 3RZ', 'MOTOR 7/8', 'Toyota', '3RZ Meru', '2004', '593-105', '250089', 'APLICA', 'DISPONIBLE', '2.300', '2.300', 22, '2025-04-02 02:20:45', '2025-04-02 02:20:45'),
(631, 'Motor Chevrolet Spark', 'MOTOR COMPLETO', 'Chevrolet', 'Spark', '2010', '594-106', '250089', 'APLICA', 'DISPONIBLE', '1.000', '1.000', 22, '2025-04-02 02:22:04', '2025-04-02 02:22:04'),
(632, 'Motor Jeep 5.7L', 'MOTOR COMPLETO', 'Jeep', 'Grand Cherokee 5.7L', '2008', '595-107', '250089', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 22, '2025-04-02 02:23:39', '2025-04-02 02:23:39'),
(633, 'Motor Jeep 4.7L 8B EGR', 'MOTOR COMPLETO', 'Jeep', 'Grand Cherokee 4.7L 8B EGR', '2008', '596-108', '250089', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 22, '2025-04-02 02:24:55', '2025-04-02 02:24:55'),
(634, 'Motor Cummnis 4BT', 'MOTOR COMPLETO', 'Cummnis', '4BT', '2000', '597-109', '250089', 'APLICA', 'DISPONIBLE', '3.500', '3.500', 22, '2025-04-02 02:26:02', '2025-04-02 02:26:02'),
(635, 'Motor Ford 300', 'MOTOR COMPLETO', 'Ford', '300', '1990', '598-110', '250089', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 22, '2025-04-02 02:26:51', '2025-04-02 02:26:51'),
(636, 'Motor Ford 4.6L 3V', 'MOTOR COMPLETO', 'Ford', 'Explorer Eddie Bauer 3V', '2010', '599-111', '250089', 'APLICA', 'DISPONIBLE', '2.300', '2.300', 22, '2025-04-02 02:28:34', '2025-04-02 02:28:34'),
(637, 'Motor Ford Explorer Eddie Bauer 3V', 'MOTOR COMPLETO', 'Ford', 'Explorer Eddie Bauer 3V', '2010', '600-112', '250089', 'APLICA', 'DISPONIBLE', '2.300', '2.300', 22, '2025-04-02 02:34:58', '2025-04-02 02:46:01'),
(638, 'Motor Ford 4.6L 2V', 'MOTOR COMPLETO', 'Ford', '4.6L 2V', '2010', '601-113', '250089', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 22, '2025-04-02 02:36:02', '2025-04-02 02:47:50'),
(639, 'Motor Ford 4.6L 2V', 'MOTOR COMPLETO', 'Ford', 'Fortaleza 4.6L 2V', '2008', '602-114', '250089', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 22, '2025-04-02 02:37:38', '2025-04-02 02:37:38'),
(640, 'Motor Ford 5.4L 2V', 'MOTOR COMPLETO', 'Ford', 'Tritón 5.4L 2V', '2008', '603-115', '250089', 'APLICA', 'DISPONIBLE', '2.008', '2.008', 22, '2025-04-02 02:39:10', '2025-04-02 02:39:10'),
(641, 'Motor Ford 5.4L 2V', 'MOTOR 7/8', 'Ford', 'Tritón 5.4L 2V', '2008', '604-116', '250089', 'APLICA', 'DISPONIBLE', '1.800', '1.800', 22, '2025-04-02 02:40:30', '2025-04-02 02:40:30'),
(642, 'Motor Chevrolet 350', 'MOTOR COMPLETO', 'Chevrolet', '350 Tipo Vortec', '1995', '605-117', '250089', 'APLICA', 'DISPONIBLE', '1.800', '1.800', 22, '2025-04-02 02:49:16', '2025-04-02 02:49:16'),
(643, 'Motor Chevrolet 350 tipo Vortec', 'MOTOR COMPLETO', 'Chevrolet', '350 Tipo Vortec', '1995', '606-118', '250089', 'APLICA', 'DISPONIBLE', '1.800', '1.800', 22, '2025-04-02 02:50:25', '2025-04-02 02:50:25'),
(644, 'Motor Chevrolet Orlando 2.4L', 'MOTOR COMPLETO', 'Chevrolet', 'Orlando 2.4L', '2010', '607-119', '250089', 'APLICA', 'DISPONIBLE', '1.500', '1.500', 22, '2025-04-02 02:51:41', '2025-04-02 02:51:41'),
(645, 'Motor Dodge Caliber 2.4L', 'MOTOR COMPLETO', 'Dodge', 'Caliber 2.4L', '2008', '608-120', '250089', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 22, '2025-04-02 02:52:31', '2025-04-02 02:52:31'),
(646, 'Motor Chevrolet 5.3L 2005', 'MOTOR COMPLETO', 'Chevrolet', '5.3L Tanque Mecánico', '2000', '609-121', '250089', 'APLICA', 'DISPONIBLE', '2.000', '2.000', 22, '2025-04-02 02:53:46', '2025-04-02 02:53:46'),
(647, 'Motor Chevrolet Rey Camión 6.0L', 'MOTOR COMPLETO', 'Chevrolet', 'Rey Camion 6.0L', '2010', '610-122', '250089', 'APLICA', 'DISPONIBLE', '2.400', '2.400', 22, '2025-04-02 02:54:57', '2025-04-02 02:56:34'),
(648, 'Motor Chevrolet 6.0L Rey Camion', 'MOTOR COMPLETO', 'Chevrolet', 'Rey Camión 6.0L', '2010', '611-123', '250089', 'APLICA', 'DISPONIBLE', '2.400', '2.400', 22, '2025-04-02 02:55:56', '2025-04-02 02:55:56'),
(649, 'Motor Ford Ranger 2.3L', 'MOTOR COMPLETO', 'Ford', 'Ranger 2.3L', '2010', '612-124', '250089', 'APLICA', 'DISPONIBLE', '1.800', '1.800', 22, '2025-04-02 02:57:39', '2025-04-02 02:57:39'),
(650, 'Motor 7/8 Ford EcoSport 2.0L', 'MOTOR 7/8', 'FORD', 'ECOSPORT 2.0L', '2010', '613-125', '250090', 'APLICA', 'DISPONIBLE', '1.300', '1.300', 21, '2025-04-02 02:58:48', '2025-04-11 23:01:19'),
(651, 'Motor 7/8 Ford Explorer 4.6L 3V', 'MOTOR 7/8', 'Ford', 'Explorer Eddie Bauer 3V', '2010', '614-126', '250090', 'APLICA', 'DISPONIBLE', '2.300', '2.300', 21, '2025-04-02 03:00:04', '2025-04-08 15:09:35'),
(652, 'Motor 7/8 Chevrolet Gran Vitara J3', 'MOTOR 7/8', 'Chevrolet', 'Grand Vitara J3', '2007', '615-127', '250090', 'APLICA', 'DISPONIBLE', '3.000', '3.000', 21, '2025-04-08 15:14:06', '2025-04-08 15:14:06'),
(653, 'Motor 7/8 Ford EcoSport 2.0L', 'MOTOR 7/8', 'Ford', 'EcoSport 2.0L', '2008', '616-128', '250090', 'APLICA', 'DISPONIBLE', '1.400', '1.400', 21, '2025-04-08 15:15:08', '2025-04-08 15:15:08'),
(654, 'Motor Chevrolet 5.3 2008', 'MOTOR COMPLETO', 'Chevrolet', '5.3L', '2008', '617-129', '250089', 'APLICA', 'DISPONIBLE', '1.900', '1.900', 22, '2025-04-08 15:16:18', '2025-04-08 15:16:18'),
(655, 'Motor Toyota 22R', 'MOTOR COMPLETO', 'Toyota', '22R', '1990', '618-130', '250089', 'APLICA', 'DISPONIBLE', '3.000', '3.000', 22, '2025-04-08 15:17:04', '2025-04-08 15:17:04'),
(656, 'Motor Cummnis 4BT', 'MOTOR COMPLETO', 'Cummins', '4BT', '2000', '619-131', '240222', 'APLICA', 'DISPONIBLE', '3.000', '3.000', 20, '2025-04-10 14:18:50', '2025-04-10 14:18:50'),
(657, 'Motor Cummnis 6BT', 'MOTOR COMPLETO', 'Cummins', '6BT', '2000', '620-132', '240222', 'APLICA', 'DISPONIBLE', '6.000', '6.000', 20, '2025-04-10 14:23:06', '2025-04-10 14:23:06'),
(658, 'Motor 7/8 Cummins 4BT', 'MOTOR 7/8', 'Cummins', '4BT', '2000', '621-133', '060325', 'APLICA', 'DISPONIBLE', '3.000', '3.000', 18, '2025-04-10 14:24:40', '2025-04-10 14:24:40'),
(659, 'Motor 7/8 Jeep 5.7L Dodge Ram', 'MOTOR 7/8', 'Jeep', 'Dodge Ram 5.7L 4G', '2008', '622-134', '240222', 'APLICA', 'DISPONIBLE', '1.800', '1.800', 20, '2025-04-10 15:08:50', '2025-04-10 15:08:50'),
(660, 'Motor 7/8 Jeep Cherokee 3.7L KJ', 'MOTOR 7/8', 'Jeep', 'Cherokee  3.7L KJ', '2006', '623-135', '240222', 'APLICA', 'DISPONIBLE', '1.600', '1.600', 20, '2025-04-10 15:09:41', '2025-04-10 15:09:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('EuYElZHmxnvTaxkT4enubScwm8qyrM8sRFVqXwE7', NULL, '38.248.169.125', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiV1pSRVFwMnF0VURkTGl5UXVhNGVDYWN4dnp0d3pZR3MxVUZqZWtvMCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vaW50ZXJuYWwubWFpa2VsY2Fycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1766064192);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `two_factor_secret` text COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text COLLATE utf8mb4_unicode_ci,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_team_id` bigint(20) UNSIGNED DEFAULT NULL,
  `profile_photo_path` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`, `remember_token`, `current_team_id`, `profile_photo_path`, `created_at`, `updated_at`) VALUES
(1, 'Yordy Jimenez', 'yordyalejandro13@gmail.com', NULL, '$2y$12$6ewUVsIlRZKBQj83f.3n/O9.WUeZ1RC8leflOcYweO/Vj5NwiGNou', NULL, NULL, NULL, NULL, NULL, NULL, '2024-03-09 20:30:45', '2024-03-09 20:30:45'),
(2, 'ASNEIDY', 'benitezasneidy@gmail.com', NULL, '$2y$12$LRqLfd8nqgwbipCDIYhECOTCSSF8nA8GI4TazkVVB79irsYODfsz6', NULL, NULL, NULL, 'qzHqhd2TOYvoBiNgiNuIzSd9go1utB6ZMNkaIQpgrQ5ingO2CDEgPtdFdB9I', NULL, NULL, '2024-03-28 02:21:09', '2024-03-28 02:21:09'),
(3, 'Raiza Cordero', 'corderoraizae@gmail.com', NULL, '$2y$12$UYaPGOwkFQmldDUTNovpReycPfiVsa.ulfBYTPiE.yX58itiSLMlu', NULL, NULL, NULL, 'nM3eRo1a1U1fnbjL9s7WvtTm7BcYbxtSsKEmMIP7Y9NPG7eI6zvqFOM49FOB', NULL, NULL, '2024-03-28 02:21:09', '2024-03-28 02:21:09');

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
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

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
-- Indices de la tabla `partidas`
--
ALTER TABLE `partidas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `partidas_codinv_unique` (`codInv`),
  ADD KEY `partidas_container_id_foreign` (`container_id`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

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
-- AUTO_INCREMENT de la tabla `bitacoras`
--
ALTER TABLE `bitacoras`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT de la tabla `materials`
--
ALTER TABLE `materials`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `partidas`
--
ALTER TABLE `partidas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=661;

--
-- AUTO_INCREMENT de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  ADD CONSTRAINT `billings_partida_id_foreign` FOREIGN KEY (`partida_id`) REFERENCES `partidas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `billings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `bitacoras`
--
ALTER TABLE `bitacoras`
  ADD CONSTRAINT `bitacoras_users_id_foreign` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `maintenances`
--
ALTER TABLE `maintenances`
  ADD CONSTRAINT `maintenances_partida_id_foreign` FOREIGN KEY (`partida_id`) REFERENCES `partidas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `maintenance_bills`
--
ALTER TABLE `maintenance_bills`
  ADD CONSTRAINT `maintenance_bills_maintenances_id_foreign` FOREIGN KEY (`maintenances_id`) REFERENCES `maintenances` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `materials`
--
ALTER TABLE `materials`
  ADD CONSTRAINT `materials_maintenances_id_foreign` FOREIGN KEY (`maintenances_id`) REFERENCES `maintenances` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `partidas`
--
ALTER TABLE `partidas`
  ADD CONSTRAINT `partidas_container_id_foreign` FOREIGN KEY (`container_id`) REFERENCES `containers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
