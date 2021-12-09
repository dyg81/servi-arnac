-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-12-2021 a las 23:25:28
-- Versión del servidor: 10.4.17-MariaDB
-- Versión de PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sac_tesis`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20211011153229', '2021-11-05 19:06:53', 40),
('DoctrineMigrations\\Version20211011171655', '2021-11-05 19:06:53', 13),
('DoctrineMigrations\\Version20211106154746', '2021-11-06 16:47:52', 64),
('DoctrineMigrations\\Version20211106214452', '2021-11-06 22:44:56', 34),
('DoctrineMigrations\\Version20211106215454', '2021-11-06 22:56:18', 32),
('DoctrineMigrations\\Version20211107151401', '2021-11-07 16:14:06', 183),
('DoctrineMigrations\\Version20211108183827', '2021-11-08 19:38:35', 75),
('DoctrineMigrations\\Version20211108195514', '2021-11-08 20:55:18', 31),
('DoctrineMigrations\\Version20211108211023', '2021-11-08 22:10:28', 42),
('DoctrineMigrations\\Version20211110150751', '2021-11-10 16:07:57', 215),
('DoctrineMigrations\\Version20211110150925', '2021-11-10 16:09:28', 260),
('DoctrineMigrations\\Version20211110152004', '2021-11-10 16:20:09', 92),
('DoctrineMigrations\\Version20211110153550', '2021-11-10 16:35:53', 32),
('DoctrineMigrations\\Version20211111194552', '2021-11-11 20:45:56', 193),
('DoctrineMigrations\\Version20211112131257', '2021-11-12 14:13:03', 316),
('DoctrineMigrations\\Version20211112131341', '2021-11-12 14:13:45', 37),
('DoctrineMigrations\\Version20211120165448', '2021-11-20 17:54:57', 85),
('DoctrineMigrations\\Version20211206140807', '2021-12-06 15:08:24', 87),
('DoctrineMigrations\\Version20211206155648', '2021-12-06 16:56:53', 34),
('DoctrineMigrations\\Version20211206172035', '2021-12-06 18:20:43', 32),
('DoctrineMigrations\\Version20211206173652', '2021-12-06 18:36:58', 156),
('DoctrineMigrations\\Version20211206215908', '2021-12-06 22:59:13', 31),
('DoctrineMigrations\\Version20211206225914', '2021-12-06 23:59:17', 33);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fondo_deposito`
--

CREATE TABLE `fondo_deposito` (
  `fondo_id` int(11) NOT NULL,
  `deposito_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sac_anaquel`
--

CREATE TABLE `sac_anaquel` (
  `id` int(11) NOT NULL,
  `numero` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `identificador` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sac_anaquel`
--

INSERT INTO `sac_anaquel` (`id`, `numero`, `identificador`) VALUES
(1, '01', 'ANA01'),
(3, '03', 'ANA03'),
(6, '02', 'ANA02'),
(7, '04', 'ANA04'),
(8, '05', 'ANA05'),
(9, '06', 'ANA06'),
(10, '07', 'ANA07'),
(11, '08', 'ANA08'),
(12, '09', 'ANA09'),
(13, '10', 'ANA10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sac_categoria`
--

CREATE TABLE `sac_categoria` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `identificador` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `investigacion_precio` double NOT NULL,
  `reprografia_precio` double NOT NULL,
  `certificacion_precio` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sac_categoria`
--

INSERT INTO `sac_categoria` (`id`, `nombre`, `identificador`, `investigacion_precio`, `reprografia_precio`, `certificacion_precio`) VALUES
(19, 'Personas naturales y jurídicas, con residencia permante en Cuba', 'personas-naturales-y-juridicas-con-residencia-permante-en-cuba', 40, 25, 40),
(20, 'Personas naturales y jurídicas, con residencia en el exterior', 'personas-naturales-y-juridicas-con-residencia-en-el-exterior', 240, 120, 240),
(21, 'Ciudadanos cubanos estudiantes de pregrado', 'ciudadanos-cubanos-estudiantes-de-pregrado', 2, 2, 2),
(22, 'Jubilados sin anímos de lucro', 'jubilados-sin-animos-de-lucro', 5, 5, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sac_cliente`
--

CREATE TABLE `sac_cliente` (
  `id` int(11) NOT NULL,
  `pais_id` int(11) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `identificacion` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `correo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ocupacion` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sac_cliente`
--

INSERT INTO `sac_cliente` (`id`, `pais_id`, `categoria_id`, `nombre`, `identificacion`, `direccion`, `telefono`, `correo`, `ocupacion`) VALUES
(1, 1, 19, 'Donald Yañez González', '81060710587', 'Luyano, La Habana', '54336775', 'd@g.com', 'Electromedico'),
(2, 1, 22, 'Yadelys Díaz Hernández', '88021112345', 'En algun lugar del campo, x supuesto q lajas!!!', '58522021', 'y@h.com', 'Fiscalizadora'),
(4, 2, 20, 'John Smith', 'P58214863', '1020 Leisure Avenue, Tampa FL', '8132551344', 'js@yahoo.com', 'Investigador'),
(5, 10, 19, 'Yadiel Tito Torres', 'ESP5856314', 'Madrid', '2254563978', 'tt@hotmail.com', 'Turista');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sac_deposito`
--

CREATE TABLE `sac_deposito` (
  `id` int(11) NOT NULL,
  `numero` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `identificador` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sac_deposito`
--

INSERT INTO `sac_deposito` (`id`, `numero`, `identificador`) VALUES
(1, '03', 'DNAVE03'),
(3, '02', 'DNAVE02'),
(6, '06', 'DNAVE06'),
(7, '05', 'DNAVE05'),
(8, '04', 'DNAVE04'),
(9, '01', 'DNAVE01'),
(11, '07', 'DNAVE07'),
(12, '08', 'DNAVE08'),
(13, '09', 'DNAVE09'),
(14, '10', 'DNAVE10'),
(15, '11', 'DNAVE11'),
(16, '12', 'DNAVE12'),
(17, '13', 'DNAVE13'),
(18, '14', 'DNAVE14'),
(19, '15', 'DNAVE15'),
(20, '16', 'DNAVE16'),
(21, '17', 'DNAVE17'),
(22, '18', 'DNAVE18'),
(23, '19', 'DNAVE19'),
(24, '20', 'DNAVE20'),
(25, '21', 'DNAVE21'),
(26, '22', 'DNAVE22'),
(27, '23', 'DNAVE23'),
(28, '24', 'DNAVE24'),
(29, '25', 'DNAVE25'),
(30, '26', 'DNAVE26'),
(268, '27', 'DNAVE27'),
(269, '28', 'DNAVE28'),
(270, '29', 'DNAVE29'),
(271, '30', 'DNAVE30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sac_estante`
--

CREATE TABLE `sac_estante` (
  `id` int(11) NOT NULL,
  `numero` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `identificador` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sac_estante`
--

INSERT INTO `sac_estante` (`id`, `numero`, `identificador`) VALUES
(1, '03', 'EST03'),
(3, '05', 'EST05'),
(5, '02', 'EST02'),
(6, '07', 'EST07'),
(8, '10', 'EST10'),
(9, '04', 'EST04'),
(10, '08', 'EST08'),
(12, '06', 'EST06'),
(13, '01', 'EST01'),
(14, '09', 'EST09'),
(15, '11', 'EST11'),
(16, '12', 'EST12'),
(17, '13', 'EST13'),
(19, '14', 'EST14'),
(20, '15', 'EST15'),
(21, '16', 'EST16'),
(22, '17', 'EST17'),
(23, '18', 'EST18'),
(24, '19', 'EST19'),
(26, '20', 'EST20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sac_expediente`
--

CREATE TABLE `sac_expediente` (
  `id` int(11) NOT NULL,
  `fondo_id` int(11) NOT NULL,
  `legajo_id` int(11) NOT NULL,
  `estante_id` int(11) NOT NULL,
  `anaquel_id` int(11) NOT NULL,
  `numero` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `identificador` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `deposito_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sac_fondo`
--

CREATE TABLE `sac_fondo` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `identificador` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sac_legajo`
--

CREATE TABLE `sac_legajo` (
  `id` int(11) NOT NULL,
  `legajo` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `identificador` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sac_libro`
--

CREATE TABLE `sac_libro` (
  `id` int(11) NOT NULL,
  `deposito_id` int(11) NOT NULL,
  `fondo_id` int(11) NOT NULL,
  `estante_id` int(11) NOT NULL,
  `anaquel_id` int(11) NOT NULL,
  `tomo` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `anno` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `identificador` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sac_pais`
--

CREATE TABLE `sac_pais` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `identificador` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sac_pais`
--

INSERT INTO `sac_pais` (`id`, `nombre`, `identificador`) VALUES
(1, 'Cuba', 'cuba'),
(2, 'Estados Unidos de América', 'estados-unidos-de-america'),
(3, 'Antigua y Barbuda', 'antigua-y-barbuda'),
(6, 'Venezuela', 'venezuela'),
(7, 'Suriname', 'suriname'),
(8, 'República Checa', 'republica-checa'),
(10, 'España', 'espana');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Indices de la tabla `fondo_deposito`
--
ALTER TABLE `fondo_deposito`
  ADD PRIMARY KEY (`fondo_id`,`deposito_id`),
  ADD KEY `IDX_15F3267DAA510E89` (`fondo_id`),
  ADD KEY `IDX_15F3267D4140C3FC` (`deposito_id`);

--
-- Indices de la tabla `sac_anaquel`
--
ALTER TABLE `sac_anaquel`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_BDD01E0EF55AE19E` (`numero`);

--
-- Indices de la tabla `sac_categoria`
--
ALTER TABLE `sac_categoria`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_928D5DD0A8255881` (`identificador`);

--
-- Indices de la tabla `sac_cliente`
--
ALTER TABLE `sac_cliente`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_B2FAF20084291D2B` (`identificacion`),
  ADD KEY `IDX_B2FAF200C604D5C6` (`pais_id`),
  ADD KEY `IDX_B2FAF2003397707A` (`categoria_id`);

--
-- Indices de la tabla `sac_deposito`
--
ALTER TABLE `sac_deposito`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_1BDDF2FFF55AE19E` (`numero`);

--
-- Indices de la tabla `sac_estante`
--
ALTER TABLE `sac_estante`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_34B8B310F55AE19E` (`numero`);

--
-- Indices de la tabla `sac_expediente`
--
ALTER TABLE `sac_expediente`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_164CB7FDA8255881` (`identificador`),
  ADD KEY `IDX_164CB7FDAA510E89` (`fondo_id`),
  ADD KEY `IDX_164CB7FD602BF2CE` (`legajo_id`),
  ADD KEY `IDX_164CB7FDB3E5B35F` (`estante_id`),
  ADD KEY `IDX_164CB7FDFD9D1662` (`anaquel_id`),
  ADD KEY `IDX_164CB7FD4140C3FC` (`deposito_id`);

--
-- Indices de la tabla `sac_fondo`
--
ALTER TABLE `sac_fondo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_9F02F639A8255881` (`identificador`);

--
-- Indices de la tabla `sac_legajo`
--
ALTER TABLE `sac_legajo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_BD0A5B5932DD07F6` (`legajo`);

--
-- Indices de la tabla `sac_libro`
--
ALTER TABLE `sac_libro`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_E55BFDF7A8255881` (`identificador`),
  ADD KEY `IDX_E55BFDF74140C3FC` (`deposito_id`),
  ADD KEY `IDX_E55BFDF7AA510E89` (`fondo_id`),
  ADD KEY `IDX_E55BFDF7B3E5B35F` (`estante_id`),
  ADD KEY `IDX_E55BFDF7FD9D1662` (`anaquel_id`);

--
-- Indices de la tabla `sac_pais`
--
ALTER TABLE `sac_pais`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_106DA46A8255881` (`identificador`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sac_anaquel`
--
ALTER TABLE `sac_anaquel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `sac_categoria`
--
ALTER TABLE `sac_categoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `sac_cliente`
--
ALTER TABLE `sac_cliente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `sac_deposito`
--
ALTER TABLE `sac_deposito`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=280;

--
-- AUTO_INCREMENT de la tabla `sac_estante`
--
ALTER TABLE `sac_estante`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `sac_expediente`
--
ALTER TABLE `sac_expediente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `sac_fondo`
--
ALTER TABLE `sac_fondo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `sac_legajo`
--
ALTER TABLE `sac_legajo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `sac_libro`
--
ALTER TABLE `sac_libro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `sac_pais`
--
ALTER TABLE `sac_pais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `fondo_deposito`
--
ALTER TABLE `fondo_deposito`
  ADD CONSTRAINT `FK_15F3267D4140C3FC` FOREIGN KEY (`deposito_id`) REFERENCES `sac_deposito` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_15F3267DAA510E89` FOREIGN KEY (`fondo_id`) REFERENCES `sac_fondo` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sac_cliente`
--
ALTER TABLE `sac_cliente`
  ADD CONSTRAINT `FK_B2FAF2003397707A` FOREIGN KEY (`categoria_id`) REFERENCES `sac_categoria` (`id`),
  ADD CONSTRAINT `FK_B2FAF200C604D5C6` FOREIGN KEY (`pais_id`) REFERENCES `sac_pais` (`id`);

--
-- Filtros para la tabla `sac_expediente`
--
ALTER TABLE `sac_expediente`
  ADD CONSTRAINT `FK_164CB7FD4140C3FC` FOREIGN KEY (`deposito_id`) REFERENCES `sac_deposito` (`id`),
  ADD CONSTRAINT `FK_164CB7FD602BF2CE` FOREIGN KEY (`legajo_id`) REFERENCES `sac_legajo` (`id`),
  ADD CONSTRAINT `FK_164CB7FDAA510E89` FOREIGN KEY (`fondo_id`) REFERENCES `sac_fondo` (`id`),
  ADD CONSTRAINT `FK_164CB7FDB3E5B35F` FOREIGN KEY (`estante_id`) REFERENCES `sac_estante` (`id`),
  ADD CONSTRAINT `FK_164CB7FDFD9D1662` FOREIGN KEY (`anaquel_id`) REFERENCES `sac_anaquel` (`id`);

--
-- Filtros para la tabla `sac_libro`
--
ALTER TABLE `sac_libro`
  ADD CONSTRAINT `FK_E55BFDF74140C3FC` FOREIGN KEY (`deposito_id`) REFERENCES `sac_deposito` (`id`),
  ADD CONSTRAINT `FK_E55BFDF7AA510E89` FOREIGN KEY (`fondo_id`) REFERENCES `sac_fondo` (`id`),
  ADD CONSTRAINT `FK_E55BFDF7B3E5B35F` FOREIGN KEY (`estante_id`) REFERENCES `sac_estante` (`id`),
  ADD CONSTRAINT `FK_E55BFDF7FD9D1662` FOREIGN KEY (`anaquel_id`) REFERENCES `sac_anaquel` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
