-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-09-2026 a las 18:21:16
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
-- Base de datos: `daw2_hotel_san_antonio`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `canje_recompensa`
--

CREATE TABLE `canje_recompensa` (
  `id` int(10) UNSIGNED NOT NULL,
  `huesped_id` int(10) UNSIGNED NOT NULL,
  `recompensa_id` int(10) UNSIGNED NOT NULL,
  `reserva_id` int(10) UNSIGNED DEFAULT NULL,
  `estado` enum('pendiente','entregado','anulado') NOT NULL DEFAULT 'pendiente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria_producto`
--

CREATE TABLE `categoria_producto` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categoria_producto`
--

INSERT INTO `categoria_producto` (`id`, `nombre`) VALUES
(3, 'Desayunos'),
(1, 'Menú del día'),
(2, 'Platos a la carta');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comprobante`
--

CREATE TABLE `comprobante` (
  `id` int(10) UNSIGNED NOT NULL,
  `reserva_id` int(10) UNSIGNED DEFAULT NULL,
  `pedido_id` int(10) UNSIGNED DEFAULT NULL,
  `tipo` enum('boleta','factura') NOT NULL DEFAULT 'boleta',
  `serie` varchar(4) NOT NULL,
  `numero` int(10) UNSIGNED NOT NULL,
  `monto` decimal(9,2) NOT NULL,
  `emitido_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedido`
--

CREATE TABLE `detalle_pedido` (
  `id` int(10) UNSIGNED NOT NULL,
  `pedido_id` int(10) UNSIGNED NOT NULL,
  `producto_id` int(10) UNSIGNED NOT NULL,
  `cantidad` smallint(5) UNSIGNED NOT NULL,
  `precio_unitario` decimal(7,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `foto_tipo_habitacion`
--

CREATE TABLE `foto_tipo_habitacion` (
  `id` int(10) UNSIGNED NOT NULL,
  `tipo_id` int(10) UNSIGNED NOT NULL,
  `ruta` varchar(255) NOT NULL,
  `orden` tinyint(3) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `foto_tipo_habitacion`
--

INSERT INTO `foto_tipo_habitacion` (`id`, `tipo_id`, `ruta`, `orden`) VALUES
(1, 1, 'assets/img/economic-room/economic-room1.avif', 1),
(2, 1, 'assets/img/economic-room/economic-room2.avif', 2),
(3, 1, 'assets/img/economic-room/economic-room3.avif', 3),
(4, 1, 'assets/img/economic-room/economic-room4.avif', 4),
(5, 1, 'assets/img/economic-room/economic-room5.avif', 5),
(6, 2, 'assets/img/deluxe/hiabitacion_deluxe1.avif', 1),
(7, 2, 'assets/img/deluxe/hiabitacion_deluxe2.avif', 2),
(8, 2, 'assets/img/deluxe/hiabitacion_deluxe3.avif', 3),
(9, 2, 'assets/img/deluxe/hiabitacion_deluxe4.avif', 4),
(10, 2, 'assets/img/deluxe/hiabitacion_deluxe5.avif', 5),
(11, 2, 'assets/img/deluxe/hiabitacion_deluxe6.avif', 6),
(12, 2, 'assets/img/deluxe/hiabitacion_deluxe7.avif', 7),
(13, 2, 'assets/img/deluxe/hiabitacion_deluxe8.avif', 8),
(14, 2, 'assets/img/deluxe/9hiabitacion_deluxe.avif', 9),
(15, 3, 'assets/img/deluxe-queen/habitacion_queen1.avif', 1),
(16, 3, 'assets/img/deluxe-queen/habitacion_queen2.avif', 2),
(17, 3, 'assets/img/deluxe-queen/habitacion_queen3.avif', 3),
(18, 3, 'assets/img/deluxe-queen/habitacion_queen4.avif', 4),
(19, 4, 'assets/img/doble/habitacion-doble1.avif', 1),
(20, 4, 'assets/img/doble/habitacion-doble2.avif', 2),
(21, 4, 'assets/img/doble/habitacion-doble3.avif', 3),
(22, 4, 'assets/img/doble/habitacion-doble4.avif', 4),
(23, 4, 'assets/img/doble/habitacion-doble5.avif', 5),
(24, 4, 'assets/img/doble/habitacion-doble6.avif', 6),
(25, 4, 'assets/img/doble/habitacion-doble7.avif', 7),
(26, 4, 'assets/img/doble/habitacion-doble8.avif', 8),
(27, 4, 'assets/img/doble/habitacion-doble9.avif', 9),
(28, 4, 'assets/img/doble/habitacion-doble10.avif', 10),
(29, 4, 'assets/img/doble/habitacion-doble11.avif', 11),
(30, 4, 'assets/img/doble/habitacion-doble12.avif', 12),
(31, 4, 'assets/img/doble/habitacion-doble13.avif', 13),
(32, 4, 'assets/img/doble/habitacion-doble14.avif', 14),
(33, 4, 'assets/img/doble/habitacion-doble15.avif', 15),
(34, 4, 'assets/img/doble/habitacion-doble16.avif', 16),
(35, 4, 'assets/img/doble/habitacion-doble17.avif', 17),
(36, 4, 'assets/img/doble/habitacion-doble18.avif', 18),
(37, 5, 'assets/img/deluxe-suite/1deluxe-suite.avif', 1),
(38, 5, 'assets/img/deluxe-suite/2deluxe-suite.avif', 2),
(39, 5, 'assets/img/deluxe-suite/3deluxe-suite.avif', 3),
(40, 6, 'assets/img/junior-suite/unior-suite1.avif', 1),
(41, 6, 'assets/img/junior-suite/unior-suite2.avif', 2),
(42, 6, 'assets/img/junior-suite/unior-suite3.avif', 3),
(43, 6, 'assets/img/junior-suite/unior-suite4.avif', 4),
(44, 6, 'assets/img/junior-suite/unior-suite5.avif', 5),
(45, 6, 'assets/img/junior-suite/unior-suite6.avif', 6),
(46, 6, 'assets/img/junior-suite/unior-suite7.avif', 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `habitacion`
--

CREATE TABLE `habitacion` (
  `id` int(10) UNSIGNED NOT NULL,
  `numero` varchar(5) NOT NULL,
  `piso` tinyint(3) UNSIGNED NOT NULL,
  `tipo_id` int(10) UNSIGNED NOT NULL,
  `estado` enum('disponible','ocupada','limpieza','mantenimiento') NOT NULL DEFAULT 'disponible',
  `descripcion` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `habitacion`
--

INSERT INTO `habitacion` (`id`, `numero`, `piso`, `tipo_id`, `estado`, `descripcion`, `activo`, `created_at`, `updated_at`) VALUES
(1, '101', 1, 1, 'disponible', NULL, 1, '2026-09-28 15:25:28', '2026-09-28 15:25:28'),
(2, '102', 1, 2, 'disponible', NULL, 1, '2026-09-28 15:25:28', '2026-09-28 15:25:28'),
(3, '103', 1, 2, 'disponible', NULL, 1, '2026-09-28 15:25:28', '2026-09-28 15:25:28'),
(4, '104', 1, 1, 'disponible', NULL, 1, '2026-09-28 15:25:28', '2026-09-28 15:25:28'),
(5, '105', 1, 2, 'disponible', NULL, 1, '2026-09-28 15:25:28', '2026-09-28 15:25:28'),
(6, '106', 1, 2, 'disponible', NULL, 1, '2026-09-28 15:25:28', '2026-09-28 15:25:28'),
(7, '201', 2, 3, 'disponible', NULL, 1, '2026-09-28 15:25:28', '2026-09-28 15:25:28'),
(8, '202', 2, 4, 'disponible', NULL, 1, '2026-09-28 15:25:28', '2026-09-28 15:25:28'),
(9, '203', 2, 5, 'disponible', NULL, 1, '2026-09-28 15:25:28', '2026-09-28 15:25:28'),
(10, '204', 2, 6, 'disponible', NULL, 1, '2026-09-28 15:25:28', '2026-09-28 15:25:28'),
(11, '205', 2, 3, 'disponible', NULL, 1, '2026-09-28 15:25:28', '2026-09-28 15:25:28'),
(12, '206', 2, 4, 'disponible', NULL, 1, '2026-09-28 15:25:28', '2026-09-28 15:25:28'),
(13, '301', 3, 1, 'disponible', NULL, 1, '2026-09-28 15:25:28', '2026-09-28 15:25:28'),
(14, '302', 3, 2, 'disponible', NULL, 1, '2026-09-28 15:25:28', '2026-09-28 15:25:28'),
(15, '303', 3, 3, 'disponible', NULL, 1, '2026-09-28 15:25:28', '2026-09-28 15:25:28'),
(16, '304', 3, 4, 'disponible', NULL, 1, '2026-09-28 15:25:28', '2026-09-28 15:25:28'),
(17, '305', 3, 5, 'disponible', NULL, 1, '2026-09-28 15:25:28', '2026-09-28 15:25:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `huesped`
--

CREATE TABLE `huesped` (
  `id` int(10) UNSIGNED NOT NULL,
  `tipo_documento` enum('DNI','PASAPORTE','CE') NOT NULL DEFAULT 'DNI',
  `numero_documento` varchar(20) NOT NULL,
  `nombre_completo` varchar(160) NOT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `huesped`
--

INSERT INTO `huesped` (`id`, `tipo_documento`, `numero_documento`, `nombre_completo`, `correo`, `telefono`, `created_at`, `updated_at`) VALUES
(1, 'DNI', '70000001', 'Carlos Pérez Díaz', 'carlos@example.com', '999111222', '2026-09-28 15:25:28', '2026-09-28 15:25:28'),
(2, 'DNI', '70000002', 'María López Rojas', 'maria@example.com', '999333444', '2026-09-28 15:25:28', '2026-09-28 15:25:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lugar_turistico`
--

CREATE TABLE `lugar_turistico` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `categoria` varchar(30) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `lugar_turistico`
--

INSERT INTO `lugar_turistico` (`id`, `nombre`, `categoria`, `descripcion`, `foto`, `activo`) VALUES
(1, 'Pongo de Rentema', 'Naturaleza', 'Impresionante cañón del río Marañón, con paisajes únicos y gran belleza natural.', 'assets/img/recomendaciones/pongo-rentema.avif', 1),
(2, 'Sitio Arqueológico Las Juntas', 'Arqueología', 'Importante centro ceremonial prehispánico con historia y vistas privilegiadas.', 'assets/img/recomendaciones/las-juntas.avif', 1),
(3, 'Catarata Tsuntsuntsa', 'Cascada', 'Espectacular caída de agua rodeada de vegetación, ideal para los amantes de la naturaleza.', 'assets/img/recomendaciones/tsuntsuntsa.avif', 1),
(4, 'Catarata Nueva Esperanza (Numparket)', 'Cascada', 'Un paraíso natural de aguas cristalinas, perfecto para la aventura y el descanso.', 'assets/img/recomendaciones/nueva-esperanza.avif', 1),
(5, 'Cataratas del Bijao', 'Cascada', 'Conjunto de hermosas caídas de agua y pozas naturales en un entorno selvático.', 'assets/img/recomendaciones/bijao.avif', 1),
(6, 'Plaza de Armas de Bagua', 'Cultura', 'El corazón de la ciudad, con su iglesia, áreas verdes y el encanto de la vida local.', 'assets/img/recomendaciones/plaza-armas-bagua.avif', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensaje_contacto`
--

CREATE TABLE `mensaje_contacto` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `asunto` enum('reserva','consulta','sugerencia','reclamo') NOT NULL,
  `mensaje` varchar(500) NOT NULL,
  `leido` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimiento_puntos`
--

CREATE TABLE `movimiento_puntos` (
  `id` int(10) UNSIGNED NOT NULL,
  `huesped_id` int(10) UNSIGNED NOT NULL,
  `reserva_id` int(10) UNSIGNED DEFAULT NULL,
  `canje_id` int(10) UNSIGNED DEFAULT NULL,
  `tipo` enum('ganado','canjeado','ajuste') NOT NULL,
  `puntos` int(11) NOT NULL,
  `descripcion` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `movimiento_puntos`
--

INSERT INTO `movimiento_puntos` (`id`, `huesped_id`, `reserva_id`, `canje_id`, `tipo`, `puntos`, `descripcion`, `created_at`) VALUES
(1, 1, NULL, NULL, 'ajuste', 150, 'Puntos iniciales de prueba', '2026-09-28 15:25:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pago`
--

CREATE TABLE `pago` (
  `id` int(10) UNSIGNED NOT NULL,
  `reserva_id` int(10) UNSIGNED NOT NULL,
  `tipo` enum('adelanto','saldo','total') NOT NULL,
  `monto` decimal(9,2) NOT NULL CHECK (`monto` > 0),
  `metodo` enum('tarjeta','yape','transferencia','efectivo') NOT NULL,
  `estado` enum('pendiente','aprobado','rechazado','reembolsado') NOT NULL DEFAULT 'pendiente',
  `pasarela` varchar(30) DEFAULT NULL,
  `codigo_transaccion` varchar(80) DEFAULT NULL,
  `motivo_reembolso` varchar(255) DEFAULT NULL,
  `fecha_pago` datetime DEFAULT NULL,
  `fecha_reembolso` datetime DEFAULT NULL,
  `usuario_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `parametro`
--

CREATE TABLE `parametro` (
  `clave` varchar(50) NOT NULL,
  `valor` varchar(50) NOT NULL,
  `descripcion` varchar(150) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `parametro`
--

INSERT INTO `parametro` (`clave`, `valor`, `descripcion`, `updated_at`) VALUES
('puntos_bienvenida', '0', 'Puntos otorgados al registrarse un huesped nuevo', '2026-09-28 15:25:28'),
('soles_por_punto', '10', 'Soles gastados por cada punto ganado', '2026-09-28 15:25:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido`
--

CREATE TABLE `pedido` (
  `id` int(10) UNSIGNED NOT NULL,
  `reserva_id` int(10) UNSIGNED DEFAULT NULL,
  `tipo` enum('room_service','restaurante') NOT NULL,
  `estado` enum('pendiente','preparando','entregado','cancelado') NOT NULL DEFAULT 'pendiente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `id` int(10) UNSIGNED NOT NULL,
  `categoria_id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `descripcion` varchar(200) DEFAULT NULL,
  `precio` decimal(7,2) DEFAULT NULL CHECK (`precio` is null or `precio` > 0),
  `foto` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`id`, `categoria_id`, `nombre`, `descripcion`, `precio`, `foto`, `activo`, `created_at`, `updated_at`) VALUES
(1, 1, 'Menú S/ 12', 'Sopa del día, segundo con guarnicion y bebida natural.', 12.00, NULL, 1, '2026-09-28 15:25:28', '2026-09-28 15:25:28'),
(2, 1, 'Menú S/ 16', 'Sopa del día, segundo con guarnicion, bebida natural y postre del día.', 16.00, NULL, 1, '2026-09-28 15:25:28', '2026-09-28 15:25:28'),
(3, 2, 'Cecina con patacones', 'Tradicional sabor amazónico, acompañada de patacones dorados.', NULL, NULL, 1, '2026-09-28 15:25:28', '2026-09-28 15:25:28'),
(4, 2, 'Chaufa amazónico', 'Nuestro toque selvático del clásico chaufa, con ingredientes de la región.', NULL, NULL, 1, '2026-09-28 15:25:28', '2026-09-28 15:25:28'),
(5, 2, 'Tilapia', 'Fresca y sabrosa, preparada al momento.', NULL, NULL, 1, '2026-09-28 15:25:28', '2026-09-28 15:25:28'),
(6, 2, 'Trucha', 'Deliciosa trucha de la región, con el inconfundible sabor amazónico.', NULL, NULL, 1, '2026-09-28 15:25:28', '2026-09-28 15:25:28'),
(7, 2, 'Pato', 'Una especialidad de la selva peruana, con sabor único y tradicional.', NULL, NULL, 1, '2026-09-28 15:25:28', '2026-09-28 15:25:28'),
(8, 2, 'Gallina', 'Receta tradicional, preparada con el auténtico sabor de nuestra tierra.', NULL, NULL, 1, '2026-09-28 15:25:28', '2026-09-28 15:25:28'),
(9, 3, 'Desayuno regional', 'Desayuno con productos de la región.', NULL, NULL, 1, '2026-09-28 15:25:28', '2026-09-28 15:25:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recompensa`
--

CREATE TABLE `recompensa` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `descripcion` varchar(200) DEFAULT NULL,
  `puntos_requeridos` int(10) UNSIGNED NOT NULL,
  `tipo` enum('pago_fraccionado','descuento','producto','noche_gratis') NOT NULL,
  `valor` decimal(5,2) DEFAULT NULL,
  `producto_id` int(10) UNSIGNED DEFAULT NULL,
  `consume_puntos` tinyint(1) NOT NULL DEFAULT 1,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `recompensa`
--

INSERT INTO `recompensa` (`id`, `nombre`, `descripcion`, `puntos_requeridos`, `tipo`, `valor`, `producto_id`, `consume_puntos`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'Pago fraccionado', 'Reserva pagando solo el 50 % ahora y el resto al llegar.', 100, 'pago_fraccionado', 50.00, NULL, 0, 1, '2026-09-28 15:25:28', '2026-09-28 15:25:28'),
(2, 'Desayuno de cortesía', 'Un desayuno regional gratis durante la estadía.', 150, 'producto', NULL, 9, 1, 1, '2026-09-28 15:25:28', '2026-09-28 15:25:28'),
(3, '10 % de descuento', 'Descuento sobre el costo de la estadía.', 300, 'descuento', 10.00, NULL, 1, 1, '2026-09-28 15:25:28', '2026-09-28 15:25:28'),
(4, 'Noche de cortesía', 'Una noche gratis en habitación Simple.', 500, 'noche_gratis', NULL, NULL, 1, 1, '2026-09-28 15:25:28', '2026-09-28 15:25:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reserva`
--

CREATE TABLE `reserva` (
  `id` int(10) UNSIGNED NOT NULL,
  `codigo` varchar(12) NOT NULL,
  `huesped_id` int(10) UNSIGNED NOT NULL,
  `habitacion_id` int(10) UNSIGNED NOT NULL,
  `fecha_ingreso` date NOT NULL,
  `fecha_salida` date NOT NULL,
  `num_huespedes` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `precio_noche` decimal(8,2) NOT NULL,
  `monto_descuento` decimal(9,2) NOT NULL DEFAULT 0.00,
  `monto_adelanto` decimal(9,2) NOT NULL,
  `modalidad_pago` enum('completo','fraccionado') NOT NULL DEFAULT 'completo',
  `estado` enum('pendiente','confirmada','checkin','checkout','cancelada','no_show') NOT NULL DEFAULT 'pendiente',
  `observaciones` varchar(255) DEFAULT NULL,
  `usuario_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id`, `nombre`) VALUES
(1, 'administrador'),
(2, 'recepcionista');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicio`
--

CREATE TABLE `servicio` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `icono` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `servicio`
--

INSERT INTO `servicio` (`id`, `nombre`, `icono`) VALUES
(1, 'Baño privado', 'bi-droplet'),
(2, 'Aire acondicionado', 'bi-snow'),
(3, 'TV', 'bi-tv'),
(4, 'Armario', 'bi-door-closed'),
(5, 'Escritorio', 'bi-laptop'),
(6, 'Wi-Fi gratis', 'bi-wifi'),
(7, 'Cochera gratis', 'bi-car-front'),
(8, 'Agua caliente', 'bi-thermometer-half');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_habitacion`
--

CREATE TABLE `tipo_habitacion` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(40) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `detalle` text DEFAULT NULL,
  `capacidad` tinyint(3) UNSIGNED NOT NULL,
  `precio_noche` decimal(8,2) NOT NULL CHECK (`precio_noche` > 0),
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipo_habitacion`
--

INSERT INTO `tipo_habitacion` (`id`, `nombre`, `descripcion`, `detalle`, `capacidad`, `precio_noche`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'Simple', 'Habitación sencilla y cómoda para una persona.', 'La habitación Simple del Hotel San Antonio ofrece un ambiente tranquilo y funcional, ideal para viajeros que buscan descanso y buen precio en el corazón de Bagua.', 1, 100.00, 1, '2026-09-28 15:25:28', '2026-09-28 15:25:28'),
(2, 'Ejecutiva', 'Habitación ejecutiva con escritorio y agua caliente para una persona.', 'Pensada para quienes viajan por trabajo: escritorio, silla y aire acondicionado en un ambiente ordenado y silencioso, con baño privado con agua caliente para un mayor confort después de la jornada.', 1, 130.00, 1, '2026-09-28 15:25:28', '2026-09-28 15:25:28'),
(3, 'Matrimonial', 'Cama de dos plazas para dos personas.', 'Habitación matrimonial amplia y acogedora, ideal para parejas que visitan Bagua por turismo o descanso.', 2, 130.00, 1, '2026-09-28 15:25:28', '2026-09-28 15:25:28'),
(4, 'Doble', 'Dos camas para dos personas.', 'Habitación con dos camas individuales, perfecta para amigos, familiares o compañeros de viaje.', 2, 150.00, 1, '2026-09-28 15:25:28', '2026-09-28 15:25:28'),
(5, 'Suite', 'Suite con mayor espacio para dos personas.', 'La Suite ofrece más espacio y comodidad, con un ambiente elegante para quienes desean una estadía especial.', 2, 150.00, 1, '2026-09-28 15:25:28', '2026-09-28 15:25:28'),
(6, 'King', 'Amplia y cómoda, perfecta para una estadía de descanso.', 'La habitación King del Hotel San Antonio ofrece un ambiente amplio, elegante y acogedor, ideal para quienes buscan comodidad y tranquilidad en el corazón de la Amazonía. Disfruta de una cama king, espacios bien iluminados y una vista privilegiada a la naturaleza de Bagua.', 2, 180.00, 1, '2026-09-28 15:25:28', '2026-09-28 15:25:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_servicio`
--

CREATE TABLE `tipo_servicio` (
  `tipo_id` int(10) UNSIGNED NOT NULL,
  `servicio_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipo_servicio`
--

INSERT INTO `tipo_servicio` (`tipo_id`, `servicio_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(2, 1),
(2, 2),
(2, 3),
(2, 4),
(2, 5),
(2, 6),
(2, 7),
(2, 8),
(3, 1),
(3, 2),
(3, 3),
(3, 4),
(3, 5),
(3, 6),
(3, 7),
(3, 8),
(4, 1),
(4, 2),
(4, 3),
(4, 4),
(4, 5),
(4, 6),
(4, 7),
(4, 8),
(5, 1),
(5, 2),
(5, 3),
(5, 4),
(5, 5),
(5, 6),
(5, 7),
(5, 8),
(6, 1),
(6, 2),
(6, 3),
(6, 4),
(6, 5),
(6, 6),
(6, 7),
(6, 8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id` int(10) UNSIGNED NOT NULL,
  `rol_id` int(10) UNSIGNED NOT NULL,
  `nombres` varchar(80) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `rol_id`, `nombres`, `correo`, `clave`, `activo`, `created_at`, `updated_at`) VALUES
(1, 1, 'Administrador San Antonio', 'admin@sanantonio.pe', '$2y$10$wghTX6l0xeUQDUKKuBHiVuF6.xwUad30MKKYj7c65d2Hhg9l6Q2w6', 1, '2026-09-28 15:25:28', '2026-09-28 15:25:28'),
(2, 2, 'Recepción Turno Día', 'recepcion@sanantonio.pe', '$2y$10$wghTX6l0xeUQDUKKuBHiVuF6.xwUad30MKKYj7c65d2Hhg9l6Q2w6', 1, '2026-09-28 15:25:28', '2026-09-28 15:25:28');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_pedido`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_pedido` (
`id` int(10) unsigned
,`reserva_id` int(10) unsigned
,`tipo` enum('room_service','restaurante')
,`estado` enum('pendiente','preparando','entregado','cancelado')
,`created_at` timestamp
,`updated_at` timestamp
,`total` decimal(34,2)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_puntos_huesped`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_puntos_huesped` (
`huesped_id` int(10) unsigned
,`puntos` decimal(32,0)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_reserva`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_reserva` (
`id` int(10) unsigned
,`codigo` varchar(12)
,`huesped_id` int(10) unsigned
,`habitacion_id` int(10) unsigned
,`fecha_ingreso` date
,`fecha_salida` date
,`num_huespedes` tinyint(3) unsigned
,`precio_noche` decimal(8,2)
,`monto_descuento` decimal(9,2)
,`monto_adelanto` decimal(9,2)
,`modalidad_pago` enum('completo','fraccionado')
,`estado` enum('pendiente','confirmada','checkin','checkout','cancelada','no_show')
,`observaciones` varchar(255)
,`usuario_id` int(10) unsigned
,`created_at` timestamp
,`updated_at` timestamp
,`noches` int(7)
,`monto_total` decimal(15,2)
,`monto_pagado` decimal(31,2)
,`saldo_pendiente` decimal(32,2)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_pedido`
--
DROP TABLE IF EXISTS `vista_pedido`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_pedido`  AS SELECT `p`.`id` AS `id`, `p`.`reserva_id` AS `reserva_id`, `p`.`tipo` AS `tipo`, `p`.`estado` AS `estado`, `p`.`created_at` AS `created_at`, `p`.`updated_at` AS `updated_at`, coalesce(sum(`d`.`cantidad` * `d`.`precio_unitario`),0) AS `total` FROM (`pedido` `p` left join `detalle_pedido` `d` on(`d`.`pedido_id` = `p`.`id`)) GROUP BY `p`.`id` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_puntos_huesped`
--
DROP TABLE IF EXISTS `vista_puntos_huesped`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_puntos_huesped`  AS SELECT `h`.`id` AS `huesped_id`, coalesce(sum(`m`.`puntos`),0) AS `puntos` FROM (`huesped` `h` left join `movimiento_puntos` `m` on(`m`.`huesped_id` = `h`.`id`)) GROUP BY `h`.`id` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_reserva`
--
DROP TABLE IF EXISTS `vista_reserva`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_reserva`  AS SELECT `r`.`id` AS `id`, `r`.`codigo` AS `codigo`, `r`.`huesped_id` AS `huesped_id`, `r`.`habitacion_id` AS `habitacion_id`, `r`.`fecha_ingreso` AS `fecha_ingreso`, `r`.`fecha_salida` AS `fecha_salida`, `r`.`num_huespedes` AS `num_huespedes`, `r`.`precio_noche` AS `precio_noche`, `r`.`monto_descuento` AS `monto_descuento`, `r`.`monto_adelanto` AS `monto_adelanto`, `r`.`modalidad_pago` AS `modalidad_pago`, `r`.`estado` AS `estado`, `r`.`observaciones` AS `observaciones`, `r`.`usuario_id` AS `usuario_id`, `r`.`created_at` AS `created_at`, `r`.`updated_at` AS `updated_at`, to_days(`r`.`fecha_salida`) - to_days(`r`.`fecha_ingreso`) AS `noches`, (to_days(`r`.`fecha_salida`) - to_days(`r`.`fecha_ingreso`)) * `r`.`precio_noche` - `r`.`monto_descuento` AS `monto_total`, coalesce(`p`.`pagado`,0) AS `monto_pagado`, (to_days(`r`.`fecha_salida`) - to_days(`r`.`fecha_ingreso`)) * `r`.`precio_noche` - `r`.`monto_descuento` - coalesce(`p`.`pagado`,0) AS `saldo_pendiente` FROM (`reserva` `r` left join (select `pago`.`reserva_id` AS `reserva_id`,sum(`pago`.`monto`) AS `pagado` from `pago` where `pago`.`estado` = 'aprobado' group by `pago`.`reserva_id`) `p` on(`p`.`reserva_id` = `r`.`id`)) ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `canje_recompensa`
--
ALTER TABLE `canje_recompensa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_canje_huesped` (`huesped_id`),
  ADD KEY `fk_canje_recompensa` (`recompensa_id`),
  ADD KEY `fk_canje_reserva` (`reserva_id`);

--
-- Indices de la tabla `categoria_producto`
--
ALTER TABLE `categoria_producto`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `comprobante`
--
ALTER TABLE `comprobante`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_comprobante` (`serie`,`numero`),
  ADD KEY `fk_comp_reserva` (`reserva_id`),
  ADD KEY `fk_comp_pedido` (`pedido_id`);

--
-- Indices de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_dp_pedido` (`pedido_id`),
  ADD KEY `fk_dp_producto` (`producto_id`);

--
-- Indices de la tabla `foto_tipo_habitacion`
--
ALTER TABLE `foto_tipo_habitacion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_foto_tipo` (`tipo_id`);

--
-- Indices de la tabla `habitacion`
--
ALTER TABLE `habitacion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero` (`numero`),
  ADD KEY `fk_habitacion_tipo` (`tipo_id`);

--
-- Indices de la tabla `huesped`
--
ALTER TABLE `huesped`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_huesped_documento` (`tipo_documento`,`numero_documento`);

--
-- Indices de la tabla `lugar_turistico`
--
ALTER TABLE `lugar_turistico`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mensaje_contacto`
--
ALTER TABLE `mensaje_contacto`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `movimiento_puntos`
--
ALTER TABLE `movimiento_puntos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_mp_huesped` (`huesped_id`),
  ADD KEY `fk_mp_reserva` (`reserva_id`),
  ADD KEY `fk_mp_canje` (`canje_id`);

--
-- Indices de la tabla `pago`
--
ALTER TABLE `pago`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pago_reserva` (`reserva_id`),
  ADD KEY `fk_pago_usuario` (`usuario_id`);

--
-- Indices de la tabla `parametro`
--
ALTER TABLE `parametro`
  ADD PRIMARY KEY (`clave`);

--
-- Indices de la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pedido_reserva` (`reserva_id`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_producto_categoria` (`categoria_id`);

--
-- Indices de la tabla `recompensa`
--
ALTER TABLE `recompensa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_recompensa_producto` (`producto_id`);

--
-- Indices de la tabla `reserva`
--
ALTER TABLE `reserva`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `fk_reserva_huesped` (`huesped_id`),
  ADD KEY `fk_reserva_usuario` (`usuario_id`),
  ADD KEY `idx_reserva_fechas` (`habitacion_id`,`fecha_ingreso`,`fecha_salida`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `servicio`
--
ALTER TABLE `servicio`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `tipo_habitacion`
--
ALTER TABLE `tipo_habitacion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `tipo_servicio`
--
ALTER TABLE `tipo_servicio`
  ADD PRIMARY KEY (`tipo_id`,`servicio_id`),
  ADD KEY `fk_ts_servicio` (`servicio_id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `fk_usuario_rol` (`rol_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `canje_recompensa`
--
ALTER TABLE `canje_recompensa`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categoria_producto`
--
ALTER TABLE `categoria_producto`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `comprobante`
--
ALTER TABLE `comprobante`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `foto_tipo_habitacion`
--
ALTER TABLE `foto_tipo_habitacion`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT de la tabla `habitacion`
--
ALTER TABLE `habitacion`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `huesped`
--
ALTER TABLE `huesped`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `lugar_turistico`
--
ALTER TABLE `lugar_turistico`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `mensaje_contacto`
--
ALTER TABLE `mensaje_contacto`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `movimiento_puntos`
--
ALTER TABLE `movimiento_puntos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `pago`
--
ALTER TABLE `pago`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedido`
--
ALTER TABLE `pedido`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `recompensa`
--
ALTER TABLE `recompensa`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `reserva`
--
ALTER TABLE `reserva`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `servicio`
--
ALTER TABLE `servicio`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `tipo_habitacion`
--
ALTER TABLE `tipo_habitacion`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `canje_recompensa`
--
ALTER TABLE `canje_recompensa`
  ADD CONSTRAINT `fk_canje_huesped` FOREIGN KEY (`huesped_id`) REFERENCES `huesped` (`id`),
  ADD CONSTRAINT `fk_canje_recompensa` FOREIGN KEY (`recompensa_id`) REFERENCES `recompensa` (`id`),
  ADD CONSTRAINT `fk_canje_reserva` FOREIGN KEY (`reserva_id`) REFERENCES `reserva` (`id`);

--
-- Filtros para la tabla `comprobante`
--
ALTER TABLE `comprobante`
  ADD CONSTRAINT `fk_comp_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedido` (`id`),
  ADD CONSTRAINT `fk_comp_reserva` FOREIGN KEY (`reserva_id`) REFERENCES `reserva` (`id`);

--
-- Filtros para la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD CONSTRAINT `fk_dp_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedido` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_dp_producto` FOREIGN KEY (`producto_id`) REFERENCES `producto` (`id`);

--
-- Filtros para la tabla `foto_tipo_habitacion`
--
ALTER TABLE `foto_tipo_habitacion`
  ADD CONSTRAINT `fk_foto_tipo` FOREIGN KEY (`tipo_id`) REFERENCES `tipo_habitacion` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `habitacion`
--
ALTER TABLE `habitacion`
  ADD CONSTRAINT `fk_habitacion_tipo` FOREIGN KEY (`tipo_id`) REFERENCES `tipo_habitacion` (`id`);

--
-- Filtros para la tabla `movimiento_puntos`
--
ALTER TABLE `movimiento_puntos`
  ADD CONSTRAINT `fk_mp_canje` FOREIGN KEY (`canje_id`) REFERENCES `canje_recompensa` (`id`),
  ADD CONSTRAINT `fk_mp_huesped` FOREIGN KEY (`huesped_id`) REFERENCES `huesped` (`id`),
  ADD CONSTRAINT `fk_mp_reserva` FOREIGN KEY (`reserva_id`) REFERENCES `reserva` (`id`);

--
-- Filtros para la tabla `pago`
--
ALTER TABLE `pago`
  ADD CONSTRAINT `fk_pago_reserva` FOREIGN KEY (`reserva_id`) REFERENCES `reserva` (`id`),
  ADD CONSTRAINT `fk_pago_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`);

--
-- Filtros para la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD CONSTRAINT `fk_pedido_reserva` FOREIGN KEY (`reserva_id`) REFERENCES `reserva` (`id`);

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `fk_producto_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categoria_producto` (`id`);

--
-- Filtros para la tabla `recompensa`
--
ALTER TABLE `recompensa`
  ADD CONSTRAINT `fk_recompensa_producto` FOREIGN KEY (`producto_id`) REFERENCES `producto` (`id`);

--
-- Filtros para la tabla `reserva`
--
ALTER TABLE `reserva`
  ADD CONSTRAINT `fk_reserva_habitacion` FOREIGN KEY (`habitacion_id`) REFERENCES `habitacion` (`id`),
  ADD CONSTRAINT `fk_reserva_huesped` FOREIGN KEY (`huesped_id`) REFERENCES `huesped` (`id`),
  ADD CONSTRAINT `fk_reserva_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`);

--
-- Filtros para la tabla `tipo_servicio`
--
ALTER TABLE `tipo_servicio`
  ADD CONSTRAINT `fk_ts_servicio` FOREIGN KEY (`servicio_id`) REFERENCES `servicio` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ts_tipo` FOREIGN KEY (`tipo_id`) REFERENCES `tipo_habitacion` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`rol_id`) REFERENCES `rol` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
