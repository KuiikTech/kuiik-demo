<?php 
date_default_timezone_set('America/Bogota');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();
$obj_2= new conectar();

$fecha_h=date('Y-m-d G:i:s');

$conexion=$obj_2->conexion();
$conexion=$obj_2->conexion();

if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];

	$verificacion = 1;

	$cod_proveedor = $_POST['cod_proveedor'];

	if(!isset($_POST['repuesto_cotizado']))
	{
		if($cod_proveedor != '')
		{
			$sql = "SELECT `codigo`, `productos`, `proveedor`, `creador`, `estado`, `fecha_registro` FROM `compras` WHERE estado = 'EN PROCESO' order by fecha_registro DESC";
			$result=mysqli_query($conexion,$sql);
			$mostrar=mysqli_fetch_row($result);

			if($mostrar != NULL)
			{
				$cod_compra = $mostrar[0];
				$sql_proveedor = "SELECT `codigo`, `nombre`, `telefono`, `ciudad` FROM `proveedores` WHERE codigo='$cod_proveedor'";
				$result_proveedor=mysqli_query($conexion,$sql_proveedor);
				$mostrar_proveedor=mysqli_fetch_row($result_proveedor);

				if($mostrar_proveedor != null)
				{
					$nombre_proveedor = $mostrar_proveedor[1];

					$proveedor = array(
						'codigo' => $mostrar_proveedor[0],
						'nombre' => $mostrar_proveedor[1], 
						'telefono' => $mostrar_proveedor[2], 
						'ciudad' => $mostrar_proveedor[3]
					);

					$sql_e = "SELECT `codigo`, `nombre`, `telefono`, `ciudad` FROM `proveedores` WHERE codigo='$cod_proveedor'";
					$result_e=mysqli_query($conexion,$sql_e);
					$ver_e=mysqli_fetch_row($result_e);

					$codigo = $ver_e[0];

					$proveedor = json_encode($proveedor,JSON_UNESCAPED_UNICODE);
				}
				else
					$verificacion = 'No se encontró el proveedor seleccionado';
			}
			else
				$verificacion = 'No se encontró una compra en proceso';
		}
		else
			$proveedor = '';

		if($verificacion == 1)
		{
			$sql="UPDATE `compras` SET 
			`proveedor`='$proveedor'
			WHERE codigo='$cod_compra'";

			$verificacion = mysqli_query($conexion,$sql);
		}
	}
	else
	{
		if($cod_proveedor != '')
		{
			$sql = "SELECT `codigo`, `producto`, `proveedor`, `creador`, `estado`, `fecha_registro` FROM `repuestos_cotizados` WHERE estado = 'EN PROCESO' order by fecha_registro DESC";
			$result=mysqli_query($conexion,$sql);
			$mostrar=mysqli_fetch_row($result);

			if($mostrar != NULL)
			{
				$cod_repuesto_cotizado = $mostrar[0];
				$sql_proveedor = "SELECT `codigo`, `nombre`, `telefono`, `ciudad` FROM `proveedores` WHERE codigo='$cod_proveedor'";
				$result_proveedor=mysqli_query($conexion,$sql_proveedor);
				$mostrar_proveedor=mysqli_fetch_row($result_proveedor);

				if($mostrar_proveedor != null)
				{
					$nombre_proveedor = $mostrar_proveedor[1];

					$proveedor = array(
						'codigo' => $mostrar_proveedor[0],
						'nombre' => $mostrar_proveedor[1], 
						'telefono' => $mostrar_proveedor[2], 
						'ciudad' => $mostrar_proveedor[3]
					);

					$sql_e = "SELECT `codigo`, `nombre`, `telefono`, `ciudad` FROM `proveedores` WHERE codigo='$cod_proveedor'";
					$result_e=mysqli_query($conexion,$sql_e);
					$ver_e=mysqli_fetch_row($result_e);

					$codigo = $ver_e[0];

					$proveedor = json_encode($proveedor,JSON_UNESCAPED_UNICODE);
				}
				else
					$verificacion = 'No se encontró el proveedor seleccionado';
			}
			else
				$verificacion = 'No se encontró una repuesto cotizado en proceso';
		}
		else
			$proveedor = '';

		if($verificacion == 1)
		{
			$sql="UPDATE `repuestos_cotizados` SET 
			`proveedor`='$proveedor'
			WHERE codigo='$cod_repuesto_cotizado'";

			$verificacion = mysqli_query($conexion,$sql);
		}
	}
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);
echo json_encode($datos);
?>
