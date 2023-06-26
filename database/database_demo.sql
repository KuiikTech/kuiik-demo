-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-04-2023 a las 17:49:50
-- Versión del servidor: 10.4.21-MariaDB
-- Versión de PHP: 8.0.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `demo_restaurante`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja`
--

CREATE TABLE `caja` (
  `codigo` int(11) NOT NULL,
  `fecha_registro` datetime NOT NULL,
  `fecha_apertura` datetime DEFAULT NULL,
  `fecha_cierre` datetime DEFAULT NULL,
  `inventario` longtext COLLATE utf8_spanish_ci NOT NULL,
  `ventas` longtext COLLATE utf8_spanish_ci DEFAULT NULL,
  `total_ventas` int(11) DEFAULT NULL,
  `dinero` longtext COLLATE utf8_spanish_ci DEFAULT NULL,
  `base` int(11) NOT NULL,
  `ingresos` longtext COLLATE utf8_spanish_ci DEFAULT NULL,
  `egresos` longtext COLLATE utf8_spanish_ci NOT NULL,
  `creador` int(11) NOT NULL,
  `cajero` int(11) DEFAULT NULL,
  `finalizador` int(11) DEFAULT NULL,
  `estado` text COLLATE utf8_spanish_ci NOT NULL,
  `info` longtext COLLATE utf8_spanish_ci NOT NULL,
  `kilos_inicio` float NOT NULL,
  `kilos_fin` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja_mayor`
--

CREATE TABLE `caja_mayor` (
  `codigo` int(11) NOT NULL,
  `descripcion` text COLLATE utf8_spanish_ci NOT NULL,
  `valor` int(11) NOT NULL,
  `creador` int(11) NOT NULL,
  `fecha_registro` datetime NOT NULL,
  `estado` text COLLATE utf8_spanish_ci NOT NULL,
  `aprobo` int(11) DEFAULT NULL,
  `fecha_aprobacion` datetime DEFAULT NULL,
  `metodo_pago` text COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias_productos`
--

CREATE TABLE `categorias_productos` (
  `cod_categoria` int(11) NOT NULL,
  `nombre` text COLLATE utf8_spanish_ci NOT NULL,
  `estado` text COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `codigo` int(11) NOT NULL,
  `id` text COLLATE utf8_spanish_ci NOT NULL,
  `nombre` text COLLATE utf8_spanish_ci NOT NULL,
  `telefono` text COLLATE utf8_spanish_ci NOT NULL,
  `direccion` text COLLATE utf8_spanish_ci NOT NULL,
  `ciudad` text COLLATE utf8_spanish_ci NOT NULL,
  `correo` text COLLATE utf8_spanish_ci NOT NULL,
  `fecha_registro` datetime NOT NULL,
  `tipo` text COLLATE utf8_spanish_ci NOT NULL,
  `info` longtext COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `codigo` int(11) NOT NULL,
  `productos` longtext COLLATE utf8mb4_spanish_ci NOT NULL,
  `proveedor` text COLLATE utf8mb4_spanish_ci NOT NULL,
  `creador` int(11) NOT NULL,
  `estado` text COLLATE utf8mb4_spanish_ci NOT NULL,
  `fecha_registro` datetime NOT NULL,
  `observaciones` text COLLATE utf8mb4_spanish_ci NOT NULL,
  `pagos` longtext COLLATE utf8mb4_spanish_ci NOT NULL,
  `notas` longtext COLLATE utf8mb4_spanish_ci NOT NULL,
  `factura` longtext COLLATE utf8mb4_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuraciones`
--

CREATE TABLE `configuraciones` (
  `codigo` int(11) NOT NULL,
  `descripcion` text COLLATE utf8_spanish_ci NOT NULL,
  `valor` text COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `configuraciones`
--

INSERT INTO `configuraciones` (`codigo`, `descripcion`, `valor`) VALUES
(1, 'Tipo de vista', 'Lista'),
(2, 'Descontar de inventario', 'Si'),
(3, 'Imprimir Facturas', 'Desactivada'),
(4, 'Cambios', '2'),
(5, 'Facturación Créditos', 'No'),
(6, 'Empresa', '{\"nombre\":\"Demo\",\"nit\":\"0000000000-0\",\"telefono\":\"000 000 0000\",\"direccion\":\"Calle 1. \",\"ciudad\":\"Ciudad\"}'),
(7, 'Bodega Repuestos', 'PDV_1'),
(8, 'Ticket', '{\"1\":{\"etiqueta\":\"Imagen (Logo)\",\"estado\":\"true\",\"fecha_modificacion\":\"2023-04-13 15:42:17\",\"modificador\":\"1\"},\"2\":{\"etiqueta\":\"Datos de Empresa\",\"estado\":\"DIVISION\",\"fecha_modificacion\":\"2019-03-27 11:00:00\",\"modificador\":\"1\"},\"3\":{\"etiqueta\":\"Nombre\",\"estado\":\"true\",\"fecha_modificacion\":\"2023-02-16 15:02:34\",\"modificador\":\"1\"},\"4\":{\"etiqueta\":\"NIT\",\"estado\":\"true\",\"fecha_modificacion\":\"2023-02-15 16:14:36\",\"modificador\":\"1\"},\"5\":{\"etiqueta\":\"Dirección\",\"estado\":\"true\",\"fecha_modificacion\":\"2023-02-15 16:14:36\",\"modificador\":\"1\"},\"6\":{\"etiqueta\":\"Teléfono\",\"estado\":\"true\",\"fecha_modificacion\":\"2023-02-15 14:48:43\",\"modificador\":\"1\"},\"7\":{\"etiqueta\":\"Ciudad\",\"estado\":\"true\",\"fecha_modificacion\":\"2023-02-15 16:14:37\",\"modificador\":\"1\"},\"8\":{\"etiqueta\":\"Datos de cliente\",\"estado\":\"DIVISION\",\"fecha_modificacion\":\"2019-03-27 11:00:00\",\"modificador\":\"1\"},\"9\":{\"etiqueta\":\"Nombre\",\"estado\":\"true\",\"fecha_modificacion\":\"2023-02-14 19:30:59\",\"modificador\":\"1\"},\"10\":{\"etiqueta\":\"CC/NIT\",\"estado\":\"true\",\"fecha_modificacion\":\"2023-02-16 14:38:51\",\"modificador\":\"1\"},\"11\":{\"etiqueta\":\"Teléfono\",\"estado\":\"true\",\"fecha_modificacion\":\"2023-02-15 14:43:19\",\"modificador\":\"1\"},\"12\":{\"etiqueta\":\"Dirección\",\"estado\":\"true\",\"fecha_modificacion\":\"2023-02-16 14:38:50\",\"modificador\":\"1\"},\"13\":{\"etiqueta\":\"Datos de factura\",\"estado\":\"DIVISION\",\"fecha_modificacion\":\"2019-03-27 11:00:00\",\"modificador\":\"1\"},\"14\":{\"etiqueta\":\"Método de pago\",\"estado\":\"true\",\"fecha_modificacion\":\"2023-03-17 20:06:30\",\"modificador\":\"1\"},\"15\":{\"etiqueta\":\"Cajero\",\"estado\":\"true\",\"fecha_modificacion\":\"2023-03-17 20:06:31\",\"modificador\":\"1\"},\"16\":{\"etiqueta\":\"Mensaje\",\"estado\":\"true\",\"text\":\"Mensaje\",\"fecha_modificacion\":\"2023-02-16 14:38:49\",\"modificador\":\"1\"}}'),
(9, 'Alerta Pedido', '13'),
(10, 'Mensaje Ticket', '{\"1\":{\"text\":\"Mensaje final del ticket\",\"fecha_registro\":\"2023-02-15 14:24:59\",\"creador\":\"1\"}}'),
(11, 'Control Cocina', 'Manual'),
(12, 'Control Bar', 'Automatico'),
(13, 'Control Horno', 'Manual'),
(14, 'Acceso a mesas', 'Todos'),
(15, 'Cajon', 'Automatico'),
(16, 'Cambios Cocina', '0'),
(17, 'Cambios Bar', '1'),
(18, 'Cambios Horno', '1'),
(19, 'Fecha Ranking Ventas', '2023-02-03'),
(20, 'Ranking Productos', 'SI'),
(22, 'WhatsApp', 'NO'),
(23, 'Identificador WhatsApp', ''),
(24, 'Token WhatsApp', ''),
(25, 'Validar Stock', 'NO'),
(26, 'Tipo Sistema', 'Ninguno'),
(27, 'Vista Caja', 'Individual');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentas_por_cobrar`
--

CREATE TABLE `cuentas_por_cobrar` (
  `codigo` int(11) NOT NULL,
  `cod_cliente` int(11) NOT NULL,
  `cliente` longtext COLLATE utf8_spanish_ci NOT NULL,
  `descripcion` text COLLATE utf8_spanish_ci NOT NULL,
  `valor` int(11) NOT NULL,
  `fecha_registro` datetime NOT NULL,
  `fecha_pago` datetime DEFAULT NULL,
  `fecha_ingreso` datetime DEFAULT NULL,
  `creador` int(11) NOT NULL,
  `cobrador` int(11) DEFAULT NULL,
  `cajero` int(11) DEFAULT NULL,
  `estado` text COLLATE utf8_spanish_ci NOT NULL,
  `pagos` longtext COLLATE utf8_spanish_ci NOT NULL,
  `local_recepcion` text COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `devolucion`
--

CREATE TABLE `devolucion` (
  `codigo` int(11) NOT NULL,
  `descripcion` text COLLATE utf8_spanish_ci NOT NULL,
  `producto` longtext COLLATE utf8_spanish_ci NOT NULL,
  `cambio` longtext COLLATE utf8_spanish_ci NOT NULL,
  `creador` int(11) NOT NULL,
  `fecha_registro` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturas`
--

CREATE TABLE `facturas` (
  `codigo` int(11) NOT NULL,
  `cliente` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `fecha_registro` datetime NOT NULL,
  `creador` int(11) NOT NULL,
  `estado` text COLLATE utf8_spanish_ci NOT NULL,
  `pagos` longtext COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesas`
--

CREATE TABLE `mesas` (
  `cod_mesa` int(11) NOT NULL,
  `nombre` text COLLATE utf8_spanish_ci NOT NULL,
  `descripcion` text COLLATE utf8_spanish_ci NOT NULL,
  `productos` longtext COLLATE utf8_spanish_ci NOT NULL,
  `estado` text COLLATE utf8_spanish_ci NOT NULL,
  `fecha_apertura` datetime DEFAULT NULL,
  `cod_cliente` int(11) DEFAULT NULL,
  `pagos` longtext COLLATE utf8_spanish_ci NOT NULL,
  `mesero` int(11) DEFAULT NULL,
  `descuentos` text COLLATE utf8_spanish_ci NOT NULL,
  `tipo` text COLLATE utf8_spanish_ci NOT NULL,
  `info` longtext COLLATE utf8_spanish_ci NOT NULL,
  `salon` text COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `codigo` varchar(13) COLLATE utf8_spanish_ci NOT NULL,
  `productos` longtext COLLATE utf8_spanish_ci NOT NULL,
  `mesa` int(11) NOT NULL,
  `solicitante` int(11) NOT NULL,
  `fecha_registro` datetime NOT NULL,
  `fecha_envio` datetime DEFAULT NULL,
  `fecha_entrega` datetime DEFAULT NULL,
  `estado` text COLLATE utf8_spanish_ci NOT NULL,
  `area` text COLLATE utf8_spanish_ci NOT NULL,
  `respuesta` text COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `codigo` int(11) NOT NULL,
  `descripcion` text COLLATE utf8_spanish_ci NOT NULL,
  `unidad` text COLLATE utf8_spanish_ci NOT NULL,
  `valor` int(11) NOT NULL,
  `inventario` longtext COLLATE utf8_spanish_ci NOT NULL,
  `categoria` text COLLATE utf8_spanish_ci NOT NULL,
  `imagen` text COLLATE utf8_spanish_ci NOT NULL,
  `fecha_registro` datetime NOT NULL,
  `area` text COLLATE utf8_spanish_ci NOT NULL,
  `tipo` text COLLATE utf8_spanish_ci NOT NULL,
  `estado` text COLLATE utf8_spanish_ci NOT NULL,
  `barcode` varchar(14) COLLATE utf8_spanish_ci NOT NULL,
  `movimientos` longtext COLLATE utf8_spanish_ci NOT NULL,
  `especial` text COLLATE utf8_spanish_ci NOT NULL,
  `alerta` int(11) NOT NULL,
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `codigo` int(11) NOT NULL,
  `nombre` text COLLATE utf8_spanish_ci NOT NULL,
  `telefono` text COLLATE utf8_spanish_ci NOT NULL,
  `ciudad` text COLLATE utf8_spanish_ci NOT NULL,
  `fecha_registro` datetime NOT NULL,
  `estado` text COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reg_movimientos`
--

CREATE TABLE `reg_movimientos` (
  `cod_movimiento` int(11) NOT NULL,
  `descripción` text COLLATE utf8_spanish_ci NOT NULL,
  `cc_empleado` int(11) NOT NULL,
  `fecha` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

CREATE TABLE `reservas` (
  `codigo` int(11) NOT NULL,
  `nombre` text COLLATE utf8_spanish_ci NOT NULL,
  `descripcion` text COLLATE utf8_spanish_ci NOT NULL,
  `productos` longtext COLLATE utf8_spanish_ci NOT NULL,
  `estado` text COLLATE utf8_spanish_ci NOT NULL,
  `fecha_registro` datetime DEFAULT NULL,
  `cod_cliente` int(11) DEFAULT NULL,
  `pagos` longtext COLLATE utf8_spanish_ci NOT NULL,
  `fecha_llegada` datetime DEFAULT NULL,
  `descuentos` text COLLATE utf8_spanish_ci NOT NULL,
  `code` text COLLATE utf8_spanish_ci NOT NULL,
  `creador` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resoluciones`
--

CREATE TABLE `resoluciones` (
  `codigo` int(11) NOT NULL,
  `prefijo` text COLLATE utf8_spanish_ci NOT NULL,
  `sufijo` text COLLATE utf8_spanish_ci NOT NULL,
  `inicio` int(11) NOT NULL,
  `fin` int(11) NOT NULL,
  `actual` int(11) NOT NULL,
  `fecha_resolucion` date NOT NULL,
  `estado` text COLLATE utf8_spanish_ci NOT NULL,
  `numero` text COLLATE utf8_spanish_ci NOT NULL,
  `fecha_registro` datetime NOT NULL,
  `vigencia` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `salones`
--

CREATE TABLE `salones` (
  `codigo` int(11) NOT NULL,
  `nombre` text NOT NULL,
  `estado` text NOT NULL,
  `color` text NOT NULL,
  `orden` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `codigo` int(10) NOT NULL,
  `cedula` text COLLATE utf8_spanish_ci NOT NULL,
  `nombre` text COLLATE utf8_spanish_ci NOT NULL,
  `apellido` text COLLATE utf8_spanish_ci NOT NULL,
  `contraseña` text COLLATE utf8_spanish_ci NOT NULL,
  `foto` text COLLATE utf8_spanish_ci NOT NULL,
  `telefono` text COLLATE utf8_spanish_ci NOT NULL,
  `rol` text COLLATE utf8_spanish_ci NOT NULL,
  `fecha_registro` date NOT NULL,
  `estado` text COLLATE utf8_spanish_ci NOT NULL,
  `permisos` longtext COLLATE utf8_spanish_ci NOT NULL,
  `comisiones` longtext COLLATE utf8_spanish_ci NOT NULL,
  `color` text COLLATE utf8_spanish_ci NOT NULL,
  `costos` text COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `foto`, `telefono`, `rol`, `fecha_registro`, `estado`, `permisos`, `comisiones`, `color`, `costos`) VALUES
(1, '2023', 'Kuiik - Admin', ' ', 'd53541252a763bc702ee8ae80503387b', '', '3142496556', 'Administrador', '2023-06-22', 'ACTIVO', '{\"PDV\":{\"VER\":\"SI\",\"AGREGAR\":\"SI\",\"PROCESAR\":\"SI\"},\"Clientes\":{\"VER\":\"SI\",\"CREAR\":\"SI\",\"EDITAR\":\"SI\",\"ELIMINAR\":\"SI\"},\"Productos\":{\"VER\":\"SI\",\"CREAR\":\"SI\",\"EDITAR\":\"SI\",\"ELIMINAR\":\"SI\"},\"Usuarios\":{\"VER\":\"SI\",\"CREAR\":\"SI\",\"EDITAR\":\"SI\",\"ELIMINAR\":\"SI\",\"PERMISOS\":\"SI\"},\"Config PDV\":{\"VER\":\"SI\",\"GENERAL\":\"SI\",\"RESOLUCIÓN\":\"SI\"},\"Facturas\":{\"VER\":\"SI\",\"CREAR\":\"SI\"},\"Ventas\":{\"VER\":\"SI\",\"ANULAR\":\"SI\"},\"Compras\":{\"VER\":\"SI\",\"CREAR\":\"SI\",\"ELIMINAR\":\"SI\"},\"Gastos\":{\"VER\":\"SI\",\"CREAR\":\"SI\",\"ELIMINAR\":\"SI\"},\"Caja\":{\"VER\":\"SI\"},\"Por Cobrar\":{\"VER\":\"SI\",\"COBRAR\":\"SI\"},\"Devoluciones\":{\"VER\":\"SI\",\"GENERAR\":\"SI\"},\"Bodega\":{\"VER\":\"NO\"}}', '', '#000000', '0');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `codigo` int(11) NOT NULL,
  `cliente` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `productos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `pago` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `fecha` datetime NOT NULL,
  `cobrador` int(11) NOT NULL,
  `estado` text COLLATE utf8_spanish_ci NOT NULL,
  `caja` int(11) NOT NULL,
  `info` longtext COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `caja`
--
ALTER TABLE `caja`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `caja_mayor`
--
ALTER TABLE `caja_mayor`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `categorias_productos`
--
ALTER TABLE `categorias_productos`
  ADD PRIMARY KEY (`cod_categoria`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `configuraciones`
--
ALTER TABLE `configuraciones`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `cuentas_por_cobrar`
--
ALTER TABLE `cuentas_por_cobrar`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `devolucion`
--
ALTER TABLE `devolucion`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `mesas`
--
ALTER TABLE `mesas`
  ADD PRIMARY KEY (`cod_mesa`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `reg_movimientos`
--
ALTER TABLE `reg_movimientos`
  ADD PRIMARY KEY (`cod_movimiento`);

--
-- Indices de la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `resoluciones`
--
ALTER TABLE `resoluciones`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`codigo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `caja`
--
ALTER TABLE `caja`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `caja_mayor`
--
ALTER TABLE `caja_mayor`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categorias_productos`
--
ALTER TABLE `categorias_productos`
  MODIFY `cod_categoria` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `configuraciones`
--
ALTER TABLE `configuraciones`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `cuentas_por_cobrar`
--
ALTER TABLE `cuentas_por_cobrar`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `devolucion`
--
ALTER TABLE `devolucion`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `mesas`
--
ALTER TABLE `mesas`
  MODIFY `cod_mesa` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reg_movimientos`
--
ALTER TABLE `reg_movimientos`
  MODIFY `cod_movimiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reservas`
--
ALTER TABLE `reservas`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `resoluciones`
--
ALTER TABLE `resoluciones`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `codigo` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
