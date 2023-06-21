<?php 
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME,'spanish');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();
$obj_2= new conectar();
$conexion=$obj_2->conexion();
$conexion=$obj_2->conexion();
$fecha_h=date('Y-m-d G:i:s');

require_once "../clases/permisos.php";
$obj_permisos = new permisos();

if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];

	$verificacion = 1;

	if (isset($_POST['cod_cliente_U']))
	{
		$acceso = $obj_permisos->buscar_permiso($usuario,'Clientes','EDITAR');

		if($acceso == 'SI')
		{
			if($_POST['correo_cliente_U'] == '')
				$verificacion = 'Ingrese el correo del cliente';
			if($_POST['telefono_cliente_U'] == '')
				$verificacion = 'Ingrese el número de telefono del cliente';
			if($_POST['nombre_cliente_U'] == '')
				$verificacion = 'Ingrese el nombre del cliente';
			if($_POST['identificacion_cliente_U'] == '')
				$verificacion = 'Ingrese la identificación del cliente';

			if($verificacion == 1)
			{
				$datos=array(
					$_POST['cod_cliente_U'],
					$_POST['identificacion_cliente_U'],
					ucwords($_POST['nombre_cliente_U']),
					strtolower($_POST['correo_cliente_U']),
					$_POST['telefono_cliente_U'],
					$_POST['direccion_cliente_U']
				);

				$verificacion = $obj->actualizar_cliente($datos);

				$cod_cliente = $_POST['cod_cliente_U'];

				$sql="SELECT `codigo`, `id`, `nombre`, `telefono`, `direccion`, `ciudad`, `correo`, `fecha_registro` FROM `clientes` WHERE codigo='$cod_cliente'";
				$result=mysqli_query($conexion,$sql);
				$datos = $result->fetch_object();
				
				$datos = json_encode($datos,JSON_UNESCAPED_UNICODE);
				$datos = json_decode($datos, true);
			}
		}
		else
			$verificacion = 'Usted no tiene permisos para editar clientes';
	}

	if (isset($_POST['cod_proveedor_U']))
	{
		$acceso = $obj_permisos->buscar_permiso($usuario,'Proveedores','EDITAR');

		if($acceso == 'SI')
		{
			if($_POST['ciudad_proveedor_U'] == '')
				$verificacion = 'Ingrese la ciudad del proveedor';
			if($_POST['telefono_proveedor_U'] == '')
				$verificacion = 'Ingrese el número de telefono del proveedor';
			if($_POST['nombre_proveedor_U'] == '')
				$verificacion = 'Ingrese el nombre del proveedor';

			if($verificacion == 1)
			{
				$datos=array(
					$_POST['cod_proveedor_U'],
					ucwords($_POST['nombre_proveedor_U']),
					$_POST['telefono_proveedor_U'],
					strtolower($_POST['ciudad_proveedor_U'])
				);

				$verificacion = $obj->actualizar_proveedor($datos);

				$cod_proveedor = $_POST['cod_proveedor_U'];

				$sql="SELECT `codigo`, `nombre`, `telefono`, `ciudad`, `fecha_registro` FROM `proveedores` WHERE codigo='$cod_proveedor'";
				$result=mysqli_query($conexion,$sql);
				$datos = $result->fetch_object();
				
				$datos = json_encode($datos,JSON_UNESCAPED_UNICODE);
				$datos = json_decode($datos, true);
			}
		}
		else
			$verificacion = 'Usted no tiene permisos para editar proveedores';
	}
	

	if (isset($_POST['cod_mesa_U']))
	{
		if($_POST['nombre_mesa_U'] == '')
			$verificacion = 'Escriba un nombre para la mesa';

		if($verificacion == 1)
		{
			$datos=array(
				$_POST['cod_mesa_U'],
				$_POST['nombre_mesa_U'],
				$_POST['descripcion_mesa_U'],
				$_POST['cod_salon_mesa_U']
			);

			$verificacion = $obj->actualizar_mesa($datos);
		}
	}

	if (isset($_POST['cod_salon_U']))
	{
		if($_POST['nombre_salon_U'] == '')
			$verificacion = 'Escriba un nombre para la salon';

		if($verificacion == 1)
		{
			$datos=array(
				$_POST['cod_salon_U'],
				$_POST['nombre_salon_U'],
				$_POST['color_salon_U']
			);

			$verificacion = $obj->actualizar_salon($datos);
		}
	}

	if (isset($_POST['cod_gasto_U']))
	{
		if($_POST['valor_gasto_U'] == '')
			$verificacion = 'Ingrese el valor del gasto';
		if($_POST['descripcion_gasto_U'] == '')
			$verificacion = 'Escriba la descripcion del gasto';

		if($verificacion == 1)
		{
			$valor_gasto_U = str_replace('.', '', $_POST['valor_gasto_U']);
			$datos=array(
				$_POST['cod_gasto_U'],
				$_POST['descripcion_gasto_U'],
				$valor_gasto_U,
				$_POST['num_factura_gasto_U'],
			);

			$verificacion = $obj->actualizar_gasto($datos);
		}
	}

	if (isset($_POST['cod_compra_U']))
	{
		if($_POST['cant_compra_U'] == '')
			$verificacion = 'Escriba la cantidad de producto';
		if($_POST['cod_producto_compra_U'] == '')
			$verificacion = 'Seleccione un producto';
		if($_POST['valor_compra_U'] == '')
			$verificacion = 'Ingrese el valor de la compra';

		if($verificacion == 1)
		{
			$valor_compra_U = str_replace('.', '', $_POST['valor_compra_U']);
			$datos=array(
				$_POST['cod_compra_U'],
				$_POST['cod_producto_compra_U'],
				$_POST['cant_compra_U'],
				$valor_compra_U
			);

			$verificacion = $obj->actualizar_compra($datos);
		}
	}

	if (isset($_POST['cod_usuario_U']))
	{
		$acceso = $obj_permisos->buscar_permiso($usuario,'Usuarios','EDITAR');

		if($acceso == 'SI')
		{
			if($_POST['rol_usuario_U'] == '')
				$verificacion = 'Seleccione el rol del usuario';
			if($_POST['telefono_usuario_U'] == '')
				$verificacion = 'Ingrese el número de telefono del usuario';
			if($_POST['apellido_usuario_U'] == '')
				$verificacion = 'Ingrese el  apellido del usuario';
			if($_POST['nombre_usuario_U'] == '')
				$verificacion = 'Ingrese el nombre del usuario';
			if($_POST['identificacion_usuario_U'] == '')
				$verificacion = 'Ingrese la identificación del usuario';

			if($verificacion == 1)
			{
				$datos=array(
					$_POST['cod_usuario_U'],
					$_POST['identificacion_usuario_U'],
					ucwords($_POST['nombre_usuario_U']),
					ucwords($_POST['apellido_usuario_U']),
					$_POST['telefono_usuario_U'],
					$_POST['rol_usuario_U']
				);

				$verificacion = $obj->actualizar_usuario($datos);
			}
		}
		else
			$verificacion = 'Usted no tiene permisos para editar usuarios';
	}
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);
echo json_encode($datos);


?>