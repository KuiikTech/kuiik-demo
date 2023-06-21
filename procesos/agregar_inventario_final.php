<?php 
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME,'spanish');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();
$obj_2= new conectar();
$conexion=$obj_2->conexion();
$conexion=$obj_2->conexion();

$btn_cerrar = true;

if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];

	$verificacion = 1;
	$btn_cerrar = false;
	$total_productos = 0;

	$num_item = $_POST['num_item'];
	$inventario_nuevo = $_POST['inventario'];

	$sql_e = "SELECT nombre, rol, foto FROM `usuarios` WHERE codigo = '$usuario'";
	$result_e=mysqli_query($conexion,$sql_e);
	$ver_e=mysqli_fetch_row($result_e);

	$rol = $ver_e[1];

	$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado` FROM `caja` WHERE estado = 'ABIERTA'";
	$result=mysqli_query($conexion,$sql);
	$mostrar=mysqli_fetch_row($result);

	$cod_caja = $mostrar[0];

	$inventario = json_decode($mostrar[4],true);
	if($inventario[$num_item]['inventario_recibido'] >= $inventario_nuevo)
		$inventario[$num_item]['inventario_final'] = $inventario_nuevo;
	else
		$verificacion = 'El inventario final debe ser menor o igual al inventario recibido';

	if($verificacion == 1)
	{
		foreach ($inventario as $i => $producto)
		{
			if($producto['inventario_final'] == NULL)
				$btn_cerrar = true;
			else
				$total_productos += $producto['valor'] * ($producto['inventario_recibido'] - $producto['inventario_final']);
		}

		$inventario = json_encode($inventario,JSON_UNESCAPED_UNICODE);
		$sql="UPDATE `caja` SET 
		`inventario`='$inventario'
		WHERE codigo='$cod_caja'";

		$verificacion = mysqli_query($conexion,$sql);
	}
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion,
	'btn_cerrar' => $btn_cerrar,
	'total_productos' => '$ '.number_format($total_productos,0,'.','.')
);

echo json_encode($datos);

?>