<?php
date_default_timezone_set('America/Bogota');
session_set_cookie_params(7 * 24 * 60 * 60);
session_start();

class crud
{
	public function agregar_cliente($datos)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$verificacion = 1;

		$sql = "SELECT `codigo`,`id` FROM `clientes` WHERE id='$datos[0]'";
		$result = mysqli_query($conexion, $sql);
		$ver = mysqli_fetch_row($result);

		if ($ver != NULL)
			$verificacion = 'Ya existe un cliente con esa cédula/NIT';

		if ($verificacion == 1) {
			$sql = "SELECT `codigo`,`id` FROM `clientes` WHERE telefono='$datos[2]'";
			$result = mysqli_query($conexion, $sql);
			$ver = mysqli_fetch_row($result);

			if ($ver != NULL)
				$verificacion = 'Ya existe un cliente con ese telefono';
		}

		if ($verificacion == 1) {
			$sql = "INSERT INTO `clientes`(`id`, `nombre`, `telefono`, `correo`, `direccion`, `fecha_registro`) VALUES (
				'$datos[0]',
				'$datos[1]',
				'$datos[2]',
				'$datos[3]',
				'$datos[4]',
				'$fecha_h')";

			$verificacion = mysqli_query($conexion, $sql);
		}

		return $verificacion;
	}

	public function agregar_producto($datos)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$sql = "INSERT INTO `productos`(`descripción`, `inventario`, `tipo`, `barcode`, `estado`, `fecha_modificacion`) VALUES (
			'$datos[0]',
			'',
			'$datos[1]',
			'$datos[2]',
			'DISPONIBLE',
			'$fecha_h')";

		return mysqli_query($conexion, $sql);
	}

	public function agregar_mesa($nombre, $descripcion)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$sql = "INSERT INTO `mesas` (`nombre`, `descripcion`, `estado`) VALUES (
			'$nombre',
			'$descripcion',
			'LIBRE')";

		return mysqli_query($conexion, $sql);
	}

	public function agregar_salon($nombre, $color)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$sql = "INSERT INTO `salones` (`nombre`, `color`, `estado`) VALUES (
			'$nombre',
			'$color',
			'ACTIVO')";

		return mysqli_query($conexion, $sql);
	}

	public function agregar_espacio($datos)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$sql = "INSERT INTO `espacios` (`codigo`, `nombre`, `fecha_creacion`) VALUES (
			'$datos[0]',
			'$datos[1]',
			'$fecha_h')";

		return mysqli_query($conexion, $sql);
	}

	public function agregar_categoria($descripcion)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$sql = "INSERT INTO `categorias_productos`(`nombre`) VALUES (
			'$descripcion')";

		return mysqli_query($conexion, $sql);
	}

	public function agregar_gasto($datos)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$sql = "INSERT INTO `gastos`(`descripcion`, `valor`, `fecha_registro`) VALUES (
			'$datos[0]',
			'$datos[1]',
			'$fecha_h')";

		return mysqli_query($conexion, $sql);
	}

	public function agregar_compra($datos)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$usuario = $_SESSION['usuario_restaurante'];

		$sql = "INSERT INTO `compras`(`producto`, `cantidad`, `creador`, `valor`, `estado`, `fecha_registro`) VALUES (
			'$datos[0]',
			'$datos[1]',
			'$usuario',
			'$datos[2]',
			'PENDIENTE',
			'$fecha_h')";

		return mysqli_query($conexion, $sql);
	}

	public function agregar_resolucion($datos)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$usuario = $_SESSION['usuario_restaurante'];

		$sql = "INSERT INTO `resoluciones`(`prefijo`, `sufijo`, `inicio`, `fin`, `actual`, `fecha_resolucion`, `estado`, `numero`, `vigencia`, `fecha_registro`) VALUES (
			'$datos[0]',
			'$datos[1]',
			'$datos[2]',
			'$datos[3]',
			'$datos[2]',
			'$datos[4]',
			'INACTIVO',
			'$datos[5]',
			'$datos[6]',
			'$fecha_h')";

		return mysqli_query($conexion, $sql);
	}

	public function agregar_movimiento_caja_mayor($datos)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$sql = "INSERT INTO `caja_mayor`(`descripcion`, `valor`, `creador`, `fecha_registro`, `estado`, `metodo_pago`) VALUES (
			'$datos[0]',
			'$datos[1]',
			'$datos[3]',
			'$fecha_h',
			'PENDIENTE',
			'$datos[2]')";

		return mysqli_query($conexion, $sql);
	}

	public function agregar_usuario($datos)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$cedula = $datos[0];

		$sql = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `foto`, `telefono`, `rol`, `fecha_registro` FROM `usuarios` WHERE cedula = '$cedula'";
		$result = mysqli_query($conexion, $sql);
		$ver = mysqli_fetch_row($result);

		if ($ver == NULL) {
			$contraseña = md5($datos[0]);
			$permisos = '{"PDV":{"VER":"NO","AGREGAR":"NO","PROCESAR":"NO"},"Clientes":{"VER":"NO","CREAR":"NO","EDITAR":"NO","ELIMINAR":"NO"},"Productos":{"VER":"NO","CREAR":"NO","EDITAR":"NO","ELIMINAR":"NO"},"Usuarios":{"VER":"NO","CREAR":"NO","EDITAR":"NO","ELIMINAR":"NO","PERMISOS":"NO"},"Config PDV":{"VER":"NO","GENERAL":"NO","RESOLUCIÓN":"NO"},"Facturas":{"VER":"NO","CREAR":"NO"},"Ventas":{"VER":"NO","ANULAR":"NO"},"Compras":{"VER":"NO","CREAR":"NO","ELIMINAR":"NO"},"Gastos":{"VER":"NO","CREAR":"NO","ELIMINAR":"NO"},"Caja":{"VER":"NO"},"Por Cobrar":{"VER":"NO","COBRAR":"NO"},"Descuentos":{"VER":"NO","AUTORIZAR":"NO"},"Devoluciones":{"VER":"NO","GENERAR":"NO"},"Bodega":{"VER":"NO","GENERAR":"NO"},"Proveedores":{"VER":"NO","CREAR":"NO","EDITAR":"NO","ELIMINAR":"NO"}}';

			$sql = "INSERT INTO `usuarios`(`cedula`, `nombre`, `apellido`, `contraseña`, `telefono`, `rol`, `foto`, `estado`, `permisos`, `fecha_registro`) VALUES (
				'$datos[0]',
				'$datos[1]',
				'$datos[2]',
				'$contraseña',
				'$datos[3]',
				'$datos[4]',
				'user.svg',
				'ACTIVO',
				'$permisos',
				'$fecha')";

			return mysqli_query($conexion, $sql);
		} else {
			$sql = "UPDATE `usuarios` SET 
			`estado`='ACTIVO'
			WHERE codigo='$ver[0]'";
			$verificacion =  mysqli_query($conexion, $sql);

			if ($verificacion == 1)
				return 'Ya se encuentra un usuario con la cedula ingresada';
			else
				return $verificacion;
		}
	}

	public function obten_num_reservas($estado)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$sql = "SELECT count(*) FROM `reservas` WHERE estado = '$estado'";
		$result = mysqli_query($conexion, $sql);
		$ver = mysqli_fetch_row($result);

		$datos = array(
			$estado => $ver[0]
		);
		return $datos;
	}

	public function agregar_proveedor($datos)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$sql = "INSERT INTO `proveedores`(`nombre`, `telefono`, `ciudad`, `fecha_registro`) VALUES (
			'$datos[0]',
			'$datos[1]',
			'$datos[2]',
			'$fecha_h')";

		return mysqli_query($conexion, $sql);
	}

	public function agregar_caja()
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$usuario = $_SESSION['usuario_restaurante'];
		$inventario = '';

		$sql = "INSERT INTO `caja`(`fecha_registro`, `inventario`, `creador`, `estado`) VALUES (
				'$fecha_h',
				'$inventario',
				'$usuario',
				'CREADA')";

		return mysqli_query($conexion, $sql);
	}

	public function cambiar_info_usuario($datos)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$cod_usuario = $datos[0];
		$usuario = $datos[1];

		$sql = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `foto`, `telefono`, `rol`, `fecha_registro`, `estado`, `permisos`, `color` FROM `usuarios` WHERE codigo='$cod_usuario'";
		$result = mysqli_query($conexion, $sql);
		$info_registro = $result->fetch_object();

		$info_registro = array(
			'Tipo' => 'Cambio de información de usuario',
			'Información' => $info_registro
		);

		$info_registro = json_encode($info_registro, JSON_UNESCAPED_UNICODE);

		$sql = "INSERT INTO `reg_movimientos`(`descripción`, `cc_empleado`, `fecha`) VALUES (
			'$info_registro',
			'$usuario',
			'$fecha_h')";
		$verificacion = mysqli_query($conexion, $sql);

		if ($verificacion == 1) {
			$sql = "UPDATE `usuarios` SET 
			`nombre`='$datos[1]',
			`apellido`='$datos[2]',
			`telefono`='$datos[3]',
			`color`='$datos[4]'
			WHERE codigo='$cod_usuario'";
			$verificacion = mysqli_query($conexion, $sql);
		}

		return $verificacion;
	}

	public function cambiar_permiso($datos)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$sql = "SELECT `permisos` FROM `usuarios` WHERE codigo = '$datos[0]'";
		$result = mysqli_query($conexion, $sql);
		$ver = mysqli_fetch_row($result);

		$permisos = json_decode($ver[0], true);
		$pagina = $datos[1];
		$tipo = $datos[2];

		if ($tipo == 'VER' && $permisos[$pagina][$tipo] == 'SI') {
			$tipo_permisos = $permisos[$pagina];
			foreach ($tipo_permisos as $k => $valor)
				$permisos[$pagina][$k] = 'NO';
		} else {
			if ($permisos[$pagina][$tipo] == 'NO')
				$permisos[$pagina][$tipo] = 'SI';
			else
				$permisos[$pagina][$tipo] = 'NO';

			if ($permisos[$pagina][$tipo] == 'SI')
				$permisos[$pagina]['VER'] = 'SI';
		}

		$permisos = json_encode($permisos, JSON_UNESCAPED_UNICODE);

		$sql = "UPDATE `usuarios` SET 
		`permisos`='$permisos'
		WHERE codigo='$datos[0]'";

		return mysqli_query($conexion, $sql);
	}

	public function obten_datos_producto($cod_producto)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$sql = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode` FROM `productos` WHERE codigo='$cod_producto'";
		$result = mysqli_query($conexion, $sql);
		$datos = $result->fetch_object();

		return $datos;
	}

	public function obten_datos_cliente($cod_cliente)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$sql = "SELECT `codigo`, `id`, `nombre`, `telefono`, `direccion`, `ciudad`, `correo`, `fecha_registro` FROM `clientes` WHERE codigo='$cod_cliente'";
		$result = mysqli_query($conexion, $sql);
		$datos = $result->fetch_object();

		$datos = json_encode($datos, JSON_UNESCAPED_UNICODE);
		$datos = json_decode($datos, true);

		return $datos;
	}

	public function obten_datos_proveedor($cod_proveedor)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$sql = "SELECT `codigo`, `nombre`, `telefono`, `ciudad`, `fecha_registro` FROM `proveedores` WHERE codigo='$cod_proveedor'";
		$result = mysqli_query($conexion, $sql);
		$datos = $result->fetch_object();

		$datos = json_encode($datos, JSON_UNESCAPED_UNICODE);
		$datos = json_decode($datos, true);

		return $datos;
	}

	public function obten_datos_usuario($cod_usuario)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$sql = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `foto`, `telefono`, `rol`, `fecha_registro`, `estado` FROM `usuarios` WHERE codigo='$cod_usuario'";
		$result = mysqli_query($conexion, $sql);
		$ver = mysqli_fetch_row($result);

		$datos = array(
			'cod_usuario' => $ver[0],
			'cedula' => $ver[1],
			'nombre' => $ver[2],
			'apellido' => $ver[3],
			'foto' => $ver[6],
			'telefono' => $ver[6],
			'rol' => $ver[7],
			'fecha_registro' => $ver[8]
		);
		return $datos;
	}

	public function obten_datos_mesa($cod_mesa)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$sql = "SELECT `cod_mesa`, `nombre`, `descripcion`, `productos`, `estado`, `fecha_apertura` FROM `mesas` WHERE cod_mesa='$cod_mesa'";
		$result = mysqli_query($conexion, $sql);
		$ver = mysqli_fetch_row($result);

		$datos = array(
			'cod_mesa' => $ver[0],
			'nombre' => $ver[1],
			'descripcion' => $ver[2],
		);
		return $datos;
	}

	public function obten_datos_salon($cod_salon)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$sql = "SELECT `codigo`, `nombre`, `color`, `estado` FROM `salones` WHERE codigo='$cod_salon'";
		$result = mysqli_query($conexion, $sql);
		$ver = mysqli_fetch_row($result);

		$datos = array(
			'codigo' => $ver[0],
			'nombre' => $ver[1],
			'color' => $ver[2],
		);
		return $datos;
	}

	public function obten_datos_gasto($cod_gasto)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$sql = "SELECT `codigo`, `descripcion`, `valor`, `num_factura`, `fecha_registro` FROM `gastos` WHERE codigo='$cod_gasto'";
		$result = mysqli_query($conexion, $sql);
		$ver = mysqli_fetch_row($result);

		$datos = array(
			'codigo' => $ver[0],
			'descripcion' => $ver[1],
			'valor' => number_format($ver[2], 0, '.', '.'),
			'num_factura' => $ver[3]
		);
		return $datos;
	}

	public function obten_datos_compra($cod_compra)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$sql = "SELECT `codigo`, `producto`, `cantidad`, `creador`, `estado`, `fecha_registro`, `valor` FROM `compras` WHERE codigo='$cod_compra'";
		$result = mysqli_query($conexion, $sql);
		$ver = mysqli_fetch_row($result);

		$datos = array(
			'codigo' => $ver[0],
			'producto' => $ver[1],
			'cant' => $ver[2],
			'valor' => number_format($ver[6], 0, '.', '.')
		);
		return $datos;
	}

	public function actualizar_cliente($datos)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$usuario = $_SESSION['usuario_restaurante'];

		$sql = "SELECT `codigo`, `id`, `nombre`, `telefono`, `direccion`, `ciudad`, `correo`, `fecha_registro` FROM `clientes` WHERE codigo='$datos[0]'";
		$result = mysqli_query($conexion, $sql);
		$ver = mysqli_fetch_row($result);

		$info_registro = 'Modificación de Cliente: ' . $ver[0] . '$/$' . $ver[1] . '$/$' . $ver[2] . '$/$' . $ver[3] . '$/$' . $ver[4] . '$/$' . $ver[5] . '$/$' . $ver[6] . '$/$' . $ver[7];

		$sql = "INSERT INTO `reg_movimientos`(`descripción`, `cc_empleado`, `fecha`) VALUES (
			'$info_registro',
			'$usuario',
			'$fecha_h')";
		mysqli_query($conexion, $sql);

		$sql = "UPDATE `clientes` SET 
		`id`='$datos[1]',
		`nombre`='$datos[2]',
		`correo`='$datos[3]',
		`telefono`='$datos[4]',
		`direccion`='$datos[5]'
		WHERE codigo='$datos[0]'";
		return mysqli_query($conexion, $sql);
	}

	public function actualizar_proveedor($datos)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$usuario = $_SESSION['usuario_restaurante'];

		$sql = "SELECT `codigo`, `nombre`, `telefono`, `ciudad`, `fecha_registro` FROM `proveedores` WHERE codigo='$datos[0]'";
		$result = mysqli_query($conexion, $sql);
		$ver = mysqli_fetch_row($result);

		$info_registro = 'Modificación de Proveedor: ' . $ver[0] . '$/$' . $ver[1] . '$/$' . $ver[2] . '$/$' . $ver[3] . '$/$' . $ver[4];

		$sql = "INSERT INTO `reg_movimientos`(`descripción`, `cc_empleado`, `fecha`) VALUES (
			'$info_registro',
			'$usuario',
			'$fecha_h')";
		mysqli_query($conexion, $sql);

		$sql = "UPDATE `proveedores` SET 
		`nombre`='$datos[1]',
		`telefono`='$datos[2]',
		`ciudad`='$datos[3]'
		WHERE codigo='$datos[0]'";
		return mysqli_query($conexion, $sql);
	}

	public function actualizar_mesa($datos)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$usuario = $_SESSION['usuario_restaurante'];

		$sql = "SELECT `cod_mesa`, `nombre`, `descripcion`, `productos`, `estado`, `fecha_apertura` FROM `mesas` WHERE cod_mesa='$datos[0]'";
		$result = mysqli_query($conexion, $sql);
		$info_registro = $result->fetch_object();

		$info_registro = array(
			'Tipo' => 'Modificación de Mesa',
			'Información' => $info_registro
		);

		$info_registro = json_encode($info_registro,JSON_UNESCAPED_UNICODE);

		$sql = "INSERT INTO `reg_movimientos`(`descripción`, `cc_empleado`, `fecha`) VALUES (
			'$info_registro',
			'$usuario',
			'$fecha_h')";
		mysqli_query($conexion, $sql);

		$sql = "UPDATE `mesas` SET 
		`nombre`='$datos[1]',
		`descripcion`='$datos[2]',
		`salon`='$datos[3]'
		WHERE cod_mesa='$datos[0]'";
		return mysqli_query($conexion, $sql);
	}

	public function actualizar_salon($datos)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$usuario = $_SESSION['usuario_restaurante'];

		$sql = "SELECT `codigo`, `nombre`, `color`, `estado` FROM `salones` WHERE codigo='$datos[0]'";
		$result = mysqli_query($conexion, $sql);
		$info_registro = $result->fetch_object();

		$info_registro = array(
			'Tipo' => 'Modificación de Salon',
			'Información' => $info_registro
		);

		$info_registro = json_encode($info_registro,JSON_UNESCAPED_UNICODE);

		$sql = "INSERT INTO `reg_movimientos`(`descripción`, `cc_empleado`, `fecha`) VALUES (
			'$info_registro',
			'$usuario',
			'$fecha_h')";
		mysqli_query($conexion, $sql);

		$sql = "UPDATE `salones` SET 
		`nombre`='$datos[1]',
		`color`='$datos[2]'
		WHERE codigo='$datos[0]'";
		return mysqli_query($conexion, $sql);
	}

	public function actualizar_gasto($datos)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$usuario = $_SESSION['usuario_restaurante'];

		$sql = "SELECT `codigo`, `descripcion`, `valor`, `num_factura`, `fecha_registro` FROM `gastos` WHERE codigo='$datos[0]'";
		$result = mysqli_query($conexion, $sql);
		$ver = mysqli_fetch_row($result);

		$info_registro = 'Modificación de Gasto: ' . $ver[0] . '$/$' . $ver[1] . '$/$' . $ver[2] . '$/$' . $ver[3] . '$/$' . $ver[4];

		$sql = "INSERT INTO `reg_movimientos`(`descripción`, `cc_empleado`, `fecha`) VALUES (
			'$info_registro',
			'$usuario',
			'$fecha_h')";
		mysqli_query($conexion, $sql);

		$sql = "UPDATE `gastos` SET 
		`descripcion`='$datos[1]',
		`valor`='$datos[2]',
		`num_factura`='$datos[3]'
		WHERE codigo='$datos[0]'";
		return mysqli_query($conexion, $sql);
	}

	public function actualizar_compra($datos)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$usuario = $_SESSION['usuario_restaurante'];
		$cod_compra = $datos[0];

		$sql = "SELECT `codigo`, `producto`, `cantidad`, `creador`, `estado`, `fecha_registro`, `valor` FROM `compras` WHERE codigo='$cod_compra'";
		$result = mysqli_query($conexion, $sql);
		$ver = mysqli_fetch_row($result);

		$info_registro = 'Modificación de Compra: ' . $ver[0] . '$/$' . $ver[1] . '$/$' . $ver[2] . '$/$' . $ver[3] . '$/$' . $ver[4] . '$/$' . $ver[5] . '$/$' . $ver[6];

		$sql = "INSERT INTO `reg_movimientos`(`descripción`, `cc_empleado`, `fecha`) VALUES (
			'$info_registro',
			'$usuario',
			'$fecha_h')";
		mysqli_query($conexion, $sql);

		$sql = "UPDATE `compras` SET 
		`producto`='$datos[1]',
		`valor`='$datos[3]',
		`cantidad`='$datos[2]'
		WHERE codigo='$datos[0]'";
		return mysqli_query($conexion, $sql);
	}

	public function actualizar_usuario($datos)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$usuario = $_SESSION['usuario_restaurante'];
		$cod_usuario = $datos[0];
		$caja = $datos[5];

		$sql = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `foto`, `telefono`, `rol`, `fecha_registro`, `estado` FROM `usuarios` WHERE codigo='$cod_usuario'";
		$result = mysqli_query($conexion, $sql);
		$ver = mysqli_fetch_row($result);

		$info_registro = 'Modificación de Usuario: ' . $ver[0] . '$/$' . $ver[1] . '$/$' . $ver[2] . '$/$' . $ver[3] . '$/$' . $ver[4] . '$/$' . $ver[5] . '$/$0' . $ver[6] . '$/$' . $ver[7] . '$/$' . $ver[8] . '$/$' . $ver[9];

		$sql = "INSERT INTO `reg_movimientos`(`descripción`, `cc_empleado`, `fecha`) VALUES (
			'$info_registro',
			'$usuario',
			'$fecha_h')";
		mysqli_query($conexion, $sql);

		$sql = "UPDATE `usuarios` SET 
		`cedula`='$datos[1]',
		`nombre`='$datos[2]',
		`apellido`='$datos[3]',
		`telefono`='$datos[4]',
		`rol`='$datos[5]'
		WHERE codigo='$cod_usuario'";
		return mysqli_query($conexion, $sql);
	}

	public function autorizar_compra($cod_compra)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$usuario = $_SESSION['usuario_restaurante'];

		$sql = "SELECT `codigo`, `producto`, `cantidad`, `creador`, `autorizador`, `fecha_registro`, `fecha_autorización` FROM `compras` WHERE codigo='$cod_compra'";
		$result = mysqli_query($conexion, $sql);
		$ver = mysqli_fetch_row($result);

		$info_registro = 'Autorización de Compra: ' . $ver[0] . '$/$' . $ver[1] . '$/$' . $ver[2] . '$/$' . $ver[3] . '$/$' . $ver[4] . '$/$' . $ver[5] . '$/$' . $ver[6];

		$sql = "INSERT INTO `reg_movimientos`(`descripción`, `cc_empleado`, `fecha`) VALUES (
			'$info_registro',
			'$usuario',
			'$fecha_h')";
		mysqli_query($conexion, $sql);

		$cod_producto = $ver[1];

		$sql_producto = "SELECT `cod_producto`, `descripción`, `tipo`, `valor`, `inventario`, `cod_categoria`, `fecha_modificacion`, `barcode` FROM `productos` WHERE cod_producto='$cod_producto'";
		$result_producto = mysqli_query($conexion, $sql_producto);
		$ver_producto = mysqli_fetch_row($result_producto);

		if ($ver_producto[2] == 'Producto') {
			$cantidad = $ver[2];
			$sql = "UPDATE `productos` SET 
			`inventario`=(`inventario`+'$cantidad')
			WHERE cod_producto='$cod_producto'";
			$verificacion = mysqli_query($conexion, $sql);

			if ($verificacion == 1) {
				$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado` FROM `caja` WHERE estado = 'ABIERTA'";
				$result = mysqli_query($conexion, $sql);
				$mostrar = mysqli_fetch_row($result);

				$cod_caja = $mostrar[0];

				$inventario = json_decode($mostrar[4], true);

				foreach ($inventario as $i => $producto) {
					if ($producto['codigo'] == $cod_producto)
						$inventario[$i]['inventario_inicial'] += $cantidad;
				}

				$inventario = json_encode($inventario, JSON_UNESCAPED_UNICODE);
				$sql = "UPDATE `caja` SET 
				`inventario`='$inventario'
				WHERE codigo='$cod_caja'";

				$verificacion = mysqli_query($conexion, $sql);
			}
		}

		if ($verificacion == 1) {
			$sql = "UPDATE `compras` SET 
			`autorizador`='$usuario',
			`estado`='AUTORIZADO',
			`fecha_autorización`='$fecha_h'
			WHERE codigo='$cod_compra'";
			$verificacion = mysqli_query($conexion, $sql);
		}

		return $verificacion;
	}

	public function eliminar_producto($cod_producto)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$usuario = $_SESSION['usuario_restaurante'];

		$sql = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode` FROM `productos` WHERE codigo='$cod_producto'";
		$result = mysqli_query($conexion, $sql);
		$info_registro = $result->fetch_object();

		$info_registro = array(
			'Tipo' => 'Eliminación de producto',
			'Información' => $info_registro
		);

		$info_registro = json_encode($info_registro, JSON_UNESCAPED_UNICODE);

		$sql = "INSERT INTO `reg_movimientos`(`descripción`, `cc_empleado`, `fecha`) VALUES (
			'$info_registro',
			'$usuario',
			'$fecha_h')";
		$verificacion = mysqli_query($conexion, $sql);

		if ($verificacion == 1) {
			$sql = "DELETE from productos where codigo='$cod_producto'";
			$verificacion = mysqli_query($conexion, $sql);
		}
		return $verificacion;
	}

	public function eliminar_mesa($cod_mesa, $usuario)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();

		$sql = "SELECT `cod_mesa`, `nombre`, `descripcion`, `productos`, `estado`, `fecha_apertura` FROM `mesas` WHERE cod_mesa='$cod_mesa'";
		$result = mysqli_query($conexion, $sql);
		$ver = mysqli_fetch_row($result);

		$estado = $ver[4];

		if ($estado != 'OCUPADA') {
			$info_registro = 'Eliminación de Mesa: ' . $ver[0] . '$/$' . $ver[1] . '$/$' . $ver[2] . '$/$' . $ver[3] . '$/$' . $ver[4] . '$/$' . $ver[5];

			$sql = "INSERT INTO `reg_movimientos`(`descripción`, `cc_empleado`, `fecha`) VALUES (
		'$info_registro',
		'$usuario',
		'" . date('Y-m-d G:i:s') . "')";
			mysqli_query($conexion, $sql);

			$sql = "DELETE from mesas where cod_mesa='$cod_mesa'";
			$verificacion = mysqli_query($conexion, $sql);
		} else
			$verificacion = 'No se puede eliminar la mesa, porque esta OCUPADA';

		return $verificacion;
	}

	public function eliminar_cliente($cod_cliente)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$usuario = $_SESSION['usuario_restaurante'];

		$sql = "SELECT `cod_cliente`, `cedula`, `nombre`, `apellido`, `telefono`, `puntos_actuales`, `puntos_totales`, `fecha_registro`, `comensal` FROM `clientes` WHERE cod_cliente='$cod_cliente'";
		$result = mysqli_query($conexion, $sql);
		$ver = mysqli_fetch_row($result);

		$info_registro = 'Eliminación de Cliente: ' . $ver[0] . '$/$' . $ver[1] . '$/$' . $ver[2] . '$/$' . $ver[3] . '$/$' . $ver[4] . '$/$' . $ver[5] . '$/$' . $ver[6] . '$/$' . $ver[7];

		$sql = "INSERT INTO `reg_movimientos`(`descripción`, `cc_empleado`, `fecha`) VALUES (
			'$info_registro',
			'$usuario',
			'$fecha_h')";
		mysqli_query($conexion, $sql);

		$sql = "DELETE from clientes where cod_cliente='$cod_cliente'";
		$verificacion = mysqli_query($conexion, $sql);

		$datos = array(
			'consulta' => $verificacion
		);
		return $datos;
	}

	public function eliminar_proveedor($cod_proveedor)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$usuario = $_SESSION['usuario_restaurante'];

		$sql = "SELECT `codigo`, `nombre`, `telefono`, `ciudad`, `fecha_registro` FROM `proveedores` WHERE codigo='$cod_proveedor'";
		$result = mysqli_query($conexion, $sql);
		$ver = mysqli_fetch_row($result);

		$info_registro = 'Eliminación de proveedor: ' . $ver[0] . '$/$' . $ver[1] . '$/$' . $ver[2] . '$/$' . $ver[3] . '$/$' . $ver[4];

		$sql = "INSERT INTO `reg_movimientos`(`descripción`, `cc_empleado`, `fecha`) VALUES (
			'$info_registro',
			'$usuario',
			'$fecha_h')";
		mysqli_query($conexion, $sql);

		$sql = "DELETE from proveedores where codigo='$cod_proveedor'";
		$verificacion = mysqli_query($conexion, $sql);

		return $verificacion;
	}

	public function eliminar_gasto($cod_gasto)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$usuario = $_SESSION['usuario_restaurante'];

		$sql = "SELECT `codigo`, `descripcion`, `valor`, `fecha_registro` FROM `gastos` WHERE codigo ='$cod_gasto'";
		$result = mysqli_query($conexion, $sql);
		$ver = mysqli_fetch_row($result);

		$info_registro = 'Eliminación de Gasto: ' . $ver[0] . '$/$' . $ver[1] . '$/$' . $ver[2] . '$/$' . $ver[3];

		$sql = "INSERT INTO `reg_movimientos`(`descripción`, `cc_empleado`, `fecha`) VALUES (
			'$info_registro',
			'$usuario',
			'$fecha_h')";
		mysqli_query($conexion, $sql);

		$sql = "DELETE from gastos where codigo='$cod_gasto'";
		$verificacion = mysqli_query($conexion, $sql);

		$datos = array(
			'consulta' => $verificacion
		);
		return $datos;
	}

	public function eliminar_compra($cod_compra)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$usuario = $_SESSION['usuario_restaurante'];

		$sql = "SELECT `codigo`, `producto`, `cantidad`, `creador`, `estado`, `fecha_registro`, `valor` FROM `compras` WHERE codigo='$cod_compra'";
		$result = mysqli_query($conexion, $sql);
		$ver = mysqli_fetch_row($result);

		$info_registro = 'Eliminación de Compra: ' . $ver[0] . '$/$' . $ver[1] . '$/$' . $ver[2] . '$/$' . $ver[3] . '$/$' . $ver[4] . '$/$' . $ver[5] . '$/$' . $ver[6];

		$sql = "INSERT INTO `reg_movimientos`(`descripción`, `cc_empleado`, `fecha`) VALUES (
			'$info_registro',
			'$usuario',
			'$fecha_h')";
		mysqli_query($conexion, $sql);

		$sql = "DELETE from compras where codigo='$cod_compra'";
		$verificacion = mysqli_query($conexion, $sql);

		$datos = array(
			'consulta' => $verificacion
		);
		return $datos;
	}

	public function eliminar_usuario($cod_usuario)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$usuario = $_SESSION['usuario_restaurante'];

		$sql = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `foto`, `telefono`, `rol`, `fecha_registro` FROM `usuarios` WHERE codigo='$cod_usuario'";
		$result = mysqli_query($conexion, $sql);
		$ver = mysqli_fetch_row($result);

		$info_registro = 'Eliminación de Usuario: ' . $ver[0] . '$/$' . $ver[1] . '$/$' . $ver[2] . '$/$' . $ver[3] . '$/$' . $ver[4] . '$/$' . $ver[5] . '$/$' . $ver[6] . '$/$' . $ver[7] . '$/$' . $ver[8];

		$sql = "INSERT INTO `reg_movimientos`(`descripción`, `cc_empleado`, `fecha`) VALUES (
			'$info_registro',
			'$usuario',
			'$fecha_h')";
		$verificacion = mysqli_query($conexion, $sql);

		if ($verificacion == 1) {
			$sql = "UPDATE usuarios SET `estado`='ELIMINADO' WHERE codigo='$cod_usuario'";
			$verificacion = mysqli_query($conexion, $sql);
		}
		return $verificacion;
	}

	public function eliminar_categoria($cod_categoria)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$sql = "SELECT `cod_producto`, `descripción`, `tipo`, `valor`, `inventario`, `cod_categoria`, `fecha_modificacion`, `barcode`, `impuesto`, `estado` FROM `productos` WHERE cod_categoria='$cod_categoria'";
		$result = mysqli_query($conexion, $sql);
		$ver = mysqli_fetch_row($result);

		if ($ver == NULL) {
			$sql = "DELETE from categorias_productos where cod_categoria='$cod_categoria'";
			$verificacion = mysqli_query($conexion, $sql);
		} else
			$verificacion = 'La categoria no se puede eliminar. Existen productos con esta categoria asociada';

		$datos = array(
			'consulta' => $verificacion
		);
		return $datos;
	}

	public function reg_mov($datos)
	{
		$obj = new conectar();
		$conexion = $obj->conexion();
		$fecha = date('Y-m-d');
		$fecha_h = date('Y-m-d G:i:s');

		$sql = "INSERT INTO `reg_movimientos`(`descripción`, `cc_empleado`, `fecha`) VALUES (
			'$datos[0]',
			'$datos[1]',
			'$fecha_h')";
		return mysqli_query($conexion, $sql);
	}
}
