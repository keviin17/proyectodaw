-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-05-2026 a las 16:51:15
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
-- Base de datos: `velora_shop`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrito`
--

CREATE TABLE `carrito` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `talla` varchar(20) DEFAULT NULL,
  `fecha_añadido` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `genero` enum('hombre','mujer','niño','unisex') NOT NULL DEFAULT 'unisex',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`id`, `nombre`, `descripcion`, `imagen`, `genero`, `activo`, `created_at`) VALUES
(1, 'Camisetas Hombre', 'Camisetas casuales y deportivas para hombre', NULL, 'hombre', 1, '2026-05-03 17:12:49'),
(2, 'Pantalones Hombre', 'Vaqueros, chinos y pantalones de hombre', NULL, 'hombre', 1, '2026-05-03 17:12:49'),
(3, 'Camisetas Mujer', 'Camisetas y tops para mujer', NULL, 'mujer', 1, '2026-05-03 17:12:49'),
(4, 'Vestidos', 'Vestidos casuales y de fiesta', NULL, 'mujer', 1, '2026-05-03 17:12:49'),
(5, 'Ropa Niño', 'Ropa infantil para todas las edades', NULL, 'niño', 1, '2026-05-03 17:12:49'),
(6, 'Chaquetas Hombre', 'Chaquetas y abrigos para hombre', NULL, 'hombre', 1, '2026-05-10 11:02:46'),
(7, 'Calzado Hombre', 'Zapatillas y zapatos para hombre', NULL, 'hombre', 1, '2026-05-10 11:02:46'),
(8, 'Tops Mujer', 'Tops, blusas y camisas para mujer', NULL, 'mujer', 1, '2026-05-10 11:02:46'),
(9, 'Pantalones Mujer', 'Pantalones y leggings para mujer', NULL, 'mujer', 1, '2026-05-10 11:02:46'),
(10, 'Calzado Niño', 'Zapatillas y zapatos infantiles', NULL, 'niño', 1, '2026-05-10 11:02:46'),
(11, 'Pantalones Niño', 'Pantalones y joggers infantiles', NULL, 'niño', 1, '2026-05-10 11:02:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedido`
--

CREATE TABLE `detalle_pedido` (
  `id` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `precio_unitario` decimal(10,2) NOT NULL,
  `talla` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lista_deseo`
--

CREATE TABLE `lista_deseo` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `fecha_añadido` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido`
--

CREATE TABLE `pedido` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `total` decimal(10,2) NOT NULL,
  `estado` enum('pendiente','procesando','enviado','entregado','cancelado') NOT NULL DEFAULT 'pendiente',
  `direccion_envio` text NOT NULL,
  `metodo_pago` varchar(50) DEFAULT 'tarjeta',
  `notas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `id` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `precio_oferta` decimal(10,2) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `imagen` varchar(255) DEFAULT NULL,
  `id_categoria` int(11) NOT NULL,
  `destacado` tinyint(1) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `talla` varchar(50) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`id`, `nombre`, `descripcion`, `precio`, `precio_oferta`, `stock`, `imagen`, `id_categoria`, `destacado`, `activo`, `talla`, `color`, `created_at`) VALUES
(52, 'Camiseta Rayas Marineras', 'Camiseta clásica de rayas azul y blanco, algodón 100%', 22.99, NULL, 35, 'hombre/camiseta_rayas_marineras.jpg', 1, 0, 1, 'M,L,XL', NULL, '2026-05-17 13:44:32'),
(53, 'Camiseta Estampado Geométrico', 'Camiseta con print geométrico minimalista', 26.99, NULL, 28, 'hombre/camiseta_estampado_geom__trico.jpg', 1, 1, 1, 'S,M,L', NULL, '2026-05-17 13:44:32'),
(54, 'Camiseta Polo Clásica', 'Polo de piqué con cuello, corte slim', 34.99, NULL, 40, 'hombre/camiseta_polo_cl__sica.jpg', 1, 0, 1, 'S,M,L,XL', NULL, '2026-05-17 13:44:32'),
(55, 'Camiseta Tie-Dye Premium', 'Camiseta teñida a mano, edición limitada', 29.99, 24.99, 15, 'hombre/camiseta_tie-dye_premium.jpg', 1, 1, 1, 'M,L', NULL, '2026-05-17 13:44:32'),
(56, 'Camiseta Manga Larga Básica', 'Camiseta de manga larga en algodón suave', 21.99, NULL, 45, 'hombre/camiseta_manga_larga_b__sica.jpg', 1, 0, 1, 'S,M,L,XL,XXL', NULL, '2026-05-17 13:44:32'),
(57, 'Pantalón Chino Beige', 'Chino slim fit en beige, tejido resistente', 44.99, NULL, 30, 'hombre/pantal__n_chino_beige.jpg', 2, 1, 1, '30,32,34,36', NULL, '2026-05-17 13:44:32'),
(58, 'Vaquero Baggy 90s', 'Vaquero de corte ancho, tendencia retro', 59.99, NULL, 22, 'hombre/vaquero_baggy_90s.jpg', 2, 1, 1, '28,30,32,34', NULL, '2026-05-17 13:44:32'),
(59, 'Pantalón Jogger Técnico', 'Jogger con cintura elástica, tejido técnico', 39.99, 34.99, 38, 'hombre/pantal__n_jogger_t__cnico.jpg', 2, 0, 1, 'S,M,L,XL', NULL, '2026-05-17 13:44:32'),
(60, 'Shorts Deportivos', 'Bermuda deportiva con bolsillos laterales', 24.99, NULL, 50, 'hombre/shorts_deportivos.jpg', 2, 0, 1, 'S,M,L,XL', NULL, '2026-05-17 13:44:32'),
(61, 'Pantalón Cargo Urbano', 'Cargo con múltiples bolsillos, estilo urbano', 54.99, NULL, 18, 'hombre/pantal__n_cargo_urbano.jpg', 2, 1, 1, '30,32,34', NULL, '2026-05-17 13:44:32'),
(62, 'Chaqueta Denim Clásica', 'Chaqueta vaquera de corte regular, lavado oscuro', 79.99, NULL, 20, 'hombre/chaqueta_denim_cl__sica.jpg', 6, 1, 1, 'S,M,L,XL', NULL, '2026-05-17 13:44:32'),
(63, 'Bomber Universitaria', 'Bomber universitaria con ribetes en contraste', 89.99, 74.99, 12, 'hombre/bomber_universitaria.jpg', 6, 1, 1, 'M,L,XL', NULL, '2026-05-17 13:44:32'),
(64, 'Sudadera Hoodie Oversize', 'Sudadera con capucha, corte oversized', 49.99, NULL, 35, 'hombre/sudadera_hoodie_oversize.jpg', 6, 0, 1, 'M,L,XL,XXL', NULL, '2026-05-17 13:44:32'),
(65, 'Zapatillas Runner Pro', 'Zapatillas de running con amortiguación extra', 89.99, NULL, 25, 'hombre/zapatillas_runner_pro.jpg', 7, 1, 1, '40,41,42,43,44', NULL, '2026-05-17 13:44:32'),
(66, 'Sneakers Retro 80s', 'Zapatillas estilo retro, suela vulcanizada', 69.99, 59.99, 30, 'hombre/sneakers_retro_80s.jpg', 7, 1, 1, '39,40,41,42,43', NULL, '2026-05-17 13:44:32'),
(67, 'Camiseta Crop Tie-Dye', 'Crop top tie-dye, perfect fit para verano', 19.99, NULL, 40, 'mujer/camiseta_crop_tie-dye.jpg', 8, 1, 1, 'XS,S,M,L', NULL, '2026-05-17 13:44:32'),
(68, 'Top Lencero Satinado', 'Top estilo lencero en satén, elegante y versátil', 29.99, NULL, 25, 'mujer/top_lencero_satinado.jpg', 8, 1, 1, 'XS,S,M', NULL, '2026-05-17 13:44:32'),
(69, 'Camiseta Manga Globo', 'Camiseta con manga globo, estilo romántico', 27.99, NULL, 30, 'mujer/camiseta_manga_globo.jpg', 3, 0, 1, 'S,M,L', NULL, '2026-05-17 13:44:32'),
(70, 'Top Deportivo Seamless', 'Top deportivo sin costuras, tejido de alto rendimiento', 24.99, 19.99, 45, 'mujer/top_deportivo_seamless.jpg', 8, 0, 1, 'XS,S,M,L', NULL, '2026-05-17 13:44:32'),
(71, 'Blusa Bohemia Estampada', 'Blusa suelta con estampado floral y lazada', 34.99, NULL, 22, 'mujer/blusa_bohemia_estampada.jpg', 8, 1, 1, 'S,M,L,XL', NULL, '2026-05-17 13:44:32'),
(72, 'Vestido Midi Flores', 'Vestido midi con estampado de flores, tirantes finos', 64.99, NULL, 18, 'mujer/vestido_midi_flores.jpg', 4, 1, 1, 'XS,S,M,L', NULL, '2026-05-17 13:44:32'),
(73, 'Vestido Mini Punto', 'Vestido corto de punto acanalado, muy ceñido', 49.99, 44.99, 20, 'mujer/vestido_mini_punto.jpg', 4, 1, 1, 'XS,S,M', NULL, '2026-05-17 13:44:32'),
(74, 'Vestido Camisero Denim', 'Vestido tipo camisa vaquera con cinturón incluido', 59.99, NULL, 15, 'mujer/vestido_camisero_denim.jpg', 4, 0, 1, 'S,M,L', NULL, '2026-05-17 13:44:32'),
(75, 'Vestido Asimétrico Noche', 'Vestido de noche con escote asimétrico, elegante', 89.99, NULL, 10, 'mujer/vestido_asim__trico_noche.jpg', 4, 1, 1, 'XS,S,M,L', NULL, '2026-05-17 13:44:32'),
(76, 'Pantalón Palazzo Fluido', 'Palazzo de tejido fluido, cintura alta', 44.99, NULL, 28, 'mujer/pantal__n_palazzo_fluido.jpg', 9, 1, 1, 'XS,S,M,L', NULL, '2026-05-17 13:44:32'),
(77, 'Legging Sport Efecto Push-Up', 'Legging deportivo con efecto push-up y compresión', 34.99, 29.99, 50, 'mujer/legging_sport_efecto_push-up.jpg', 9, 0, 1, 'XS,S,M,L,XL', NULL, '2026-05-17 13:44:32'),
(78, 'Pantalón Mom Jeans', 'Mom jeans con lavado vintage, tiro alto', 54.99, NULL, 25, 'mujer/pantal__n_mom_jeans.jpg', 9, 1, 1, '36,38,40,42', NULL, '2026-05-17 13:44:32'),
(79, 'Pantalón Cargo Wide Leg', 'Cargo de pierna ancha con múltiples bolsillos', 49.99, NULL, 20, 'mujer/pantal__n_cargo_wide_leg.webp', 9, 0, 1, '36,38,40', NULL, '2026-05-17 13:44:32'),
(80, 'Shorts Vaqueros Bordados', 'Shorts denim con bordado floral en los bolsillos', 39.99, 34.99, 22, 'mujer/shorts_vaqueros_bordados.webp', 9, 1, 1, '36,38,40,42', NULL, '2026-05-17 13:44:32'),
(81, 'Falda Midi Plisada', 'Falda midi con pliegues, tejido satinado', 44.99, NULL, 18, 'mujer/falda_midi_plisada.jpg', 4, 1, 1, 'XS,S,M,L', NULL, '2026-05-17 13:44:32'),
(82, 'Camiseta Unicornio Brillante', 'Camiseta con estampado de unicornio con purpurina', 14.99, NULL, 55, 'nino/camiseta_unicornio_brillante.jpg', 5, 1, 1, '4,6,8,10,12', NULL, '2026-05-17 13:44:32'),
(83, 'Camiseta Superhéroe', 'Camiseta con capa simulada integrada', 16.99, NULL, 48, 'nino/camiseta_superh__roe.jpg', 5, 1, 1, '4,6,8,10', NULL, '2026-05-17 13:44:32'),
(84, 'Camiseta Mundo Espacial', 'Estampado de planetas y astronautas, fosforescente', 15.99, NULL, 40, 'nino/camiseta_mundo_espacial.jpg', 5, 0, 1, '6,8,10,12', NULL, '2026-05-17 13:44:32'),
(85, 'Camiseta Fútbol Personalizable', 'Camiseta técnica estilo fútbol, transpirable', 17.99, NULL, 60, 'nino/camiseta_f__tbol_personalizable.jpg', 5, 0, 1, '6,8,10,12,14', NULL, '2026-05-17 13:44:32'),
(86, 'Camiseta Tie-Dye Niño', 'Camiseta tie-dye de colores vibrantes', 13.99, 11.99, 35, 'nino/camiseta_tie-dye_ni__o.jpg', 5, 0, 1, '4,6,8,10', NULL, '2026-05-17 13:44:32'),
(87, 'Conjunto Jogger Estampado', 'Conjunto de sudadera y pantalón a juego', 29.99, NULL, 30, 'nino/conjunto_jogger_estampado.webp', 5, 1, 1, '4,6,8,10,12', NULL, '2026-05-17 13:44:32'),
(88, 'Pantalón Vaquero Elástico', 'Vaquero con cintura elástica, cómodo para jugar', 24.99, NULL, 35, 'nino/pantal__n_vaquero_el__stico.jpg', 11, 0, 1, '4,6,8,10,12', NULL, '2026-05-17 13:44:32'),
(89, 'Pantalón Cargo Niño', 'Cargo con bolsillos laterales, tejido resistente', 22.99, NULL, 28, 'nino/pantal__n_cargo_ni__o.jpg', 11, 0, 1, '6,8,10,12', NULL, '2026-05-17 13:44:32'),
(90, 'Legging Deportivo Niña', 'Legging técnico para deporte escolar y extraescolar', 15.99, 13.99, 45, 'nino/legging_deportivo_ni__a.jpg', 11, 0, 1, '4,6,8,10,12', NULL, '2026-05-17 13:44:32'),
(91, 'Pantalón Chandal con Rayas', 'Chándal con rayas laterales, muy cómodo', 19.99, NULL, 40, 'nino/pantal__n_chandal_con_rayas.jpg', 11, 1, 1, '4,6,8,10,12', NULL, '2026-05-17 13:44:32'),
(92, 'Shorts Estampado Tropical', 'Bermuda con estampado tropical, para el verano', 14.99, NULL, 50, 'nino/shorts_estampado_tropical.webp', 11, 0, 1, '4,6,8,10', NULL, '2026-05-17 13:44:32'),
(93, 'Chaqueta Impermeable', 'Chubasquero impermeable con bolsillos y capucha', 34.99, NULL, 20, 'nino/chaqueta_impermeable.webp', 5, 1, 1, '4,6,8,10,12', NULL, '2026-05-17 13:44:32'),
(94, 'Vestido Tutú Tul', 'Vestido con falda de tul, perfecto para fiestas', 27.99, NULL, 18, 'nino/vestido_tut___tul.jpg', 5, 1, 1, '2,4,6,8', NULL, '2026-05-17 13:44:32'),
(95, 'Pijama Estrellas', 'Pijama dos piezas con estampado de estrellas', 21.99, 18.99, 42, 'nino/pijama_estrellas.jpg', 5, 0, 1, '2,4,6,8,10', NULL, '2026-05-17 13:44:32'),
(96, 'Zapatillas Flash Runner', 'Zapatillas con luces LED en la suela', 39.99, NULL, 25, 'nino/zapatillas_flash_runner.webp', 10, 1, 1, '24,26,28,30,32', 'Blanco/Azul', '2026-05-17 13:44:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `email` varchar(200) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `rol` enum('cliente','admin') NOT NULL DEFAULT 'cliente',
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `codigo_postal` varchar(10) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `nombre`, `email`, `contraseña`, `rol`, `telefono`, `direccion`, `ciudad`, `codigo_postal`, `activo`, `fecha_registro`) VALUES
(1, 'Admin Velora', 'admin@velora.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL, NULL, NULL, NULL, 1, '2026-05-03 17:13:35'),
(2, 'cliente1', 'cliente@velora.com', '$2y$10$hIOdwxx/pjBvKv8D.FDfBudNirh11rgM3yQAofIcRSQQpaimup64e', 'cliente', NULL, NULL, NULL, NULL, 1, '2026-05-03 17:14:50'),
(3, 'Kevin', 'kevin@gmail.com', '$2y$10$wB5y1ZE.KMy3Oq7kzQpYAekXH.vu7YX2pTBpOtLFhBH1a8CqUo.Cy', 'cliente', NULL, NULL, NULL, NULL, 1, '2026-05-03 17:15:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `valoracion`
--

CREATE TABLE `valoracion` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `puntuacion` tinyint(4) NOT NULL CHECK (`puntuacion` between 1 and 5),
  `comentario` text DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_usuario_producto_talla` (`id_usuario`,`id_producto`,`talla`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `lista_deseo`
--
ALTER TABLE `lista_deseo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_usuario_producto` (`id_usuario`,`id_producto`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD KEY `id_categoria` (`id_categoria`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `valoracion`
--
ALTER TABLE `valoracion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_valoracion` (`id_usuario`,`id_producto`),
  ADD KEY `id_producto` (`id_producto`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `carrito`
--
ALTER TABLE `carrito`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `lista_deseo`
--
ALTER TABLE `lista_deseo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `pedido`
--
ALTER TABLE `pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `valoracion`
--
ALTER TABLE `valoracion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD CONSTRAINT `carrito_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `carrito_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD CONSTRAINT `detalle_pedido_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_pedido_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `lista_deseo`
--
ALTER TABLE `lista_deseo`
  ADD CONSTRAINT `lista_deseo_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lista_deseo_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD CONSTRAINT `pedido_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `valoracion`
--
ALTER TABLE `valoracion`
  ADD CONSTRAINT `valoracion_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `valoracion_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
