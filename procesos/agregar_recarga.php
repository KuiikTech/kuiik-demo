<?php 
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME,'spanish');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();
$obj_2= new conectar();
$conexion=$obj_2->conexion();
$conexion=$obj_2->conexion();

$fecha_h = date('Y-m-d G:i:s');
if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];

	$verificacion = 1;

	if(isset($_SESSION['caja_restaurante']))
	{
		$caja= $_SESSION['caja_restaurante'];

		$input_recarga = str_replace('.', '', $_POST['input_recarga']);
		$input_metodo_pago = $_POST['input_metodo_pago'];

		if($caja == 1)
			$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado`, `info` FROM `caja` WHERE estado = 'ABIERTA'";
		else if($caja == 2)
			$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado`, `info` FROM `caja2` WHERE estado = 'ABIERTA'";
		else
			$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado`, `info` FROM `caja3` WHERE estado = 'ABIERTA'";

		$result=mysqli_query($conexion,$sql);
		$mostrar=mysqli_fetch_row($result);
		if($mostrar != NULL)
		{
			$cod_caja = $mostrar[0];
			$recargas = array();
			$pos = 1;
			if($mostrar[13]!= NULL)
				$recargas = json_decode($mostrar[13],true);
			$pos += count($recargas);

			$recargas[$pos]['valor'] = $input_recarga;
			$recargas[$pos]['metodo'] = $input_metodo_pago;
			$recargas[$pos]['fecha'] = $fecha_h;
			$recargas[$pos]['creador'] = $usuario;

			$recargas = json_encode($recargas,JSON_UNESCAPED_UNICODE);
			if($caja == 1)
				$sql="UPDATE `caja` SET `info`='$recargas' WHERE codigo='$cod_caja'";
			else if($caja == 2)
				$sql="UPDATE `caja2` SET `info`='$recargas' WHERE codigo='$cod_caja'";
			else
				$sql="UPDATE `caja3` SET `info`='$recargas' WHERE codigo='$cod_caja'";

			$verificacion = mysqli_query($conexion,$sql);
		}
		else
			$verificacion = 'No se encontró una caja abierta para el cajero '.$caja;
	}
	else
		$verificacion = 'Solos los cajeros pueden procesar recargas';
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);

echo json_encode($datos);

?>