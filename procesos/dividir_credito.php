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

	require_once "../clases/permisos.php";
	$obj_permisos = new permisos();
	$acceso = $obj_permisos->buscar_permiso($usuario,'Por Cobrar','COBRAR');

	if($acceso == 'SI')
	{
		$cod_cuenta = $_POST['cod_cuenta'];
		$valor_nuevo = str_replace('.', '', $_POST['valor_nuevo']);

		$sql = "SELECT `codigo`, `cod_cliente`, `cliente`, `descripcion`, `valor`, `fecha_registro`, `fecha_pago`, `fecha_ingreso`, `creador`, `cobrador`, `cajero`, `estado` FROM `cuentas_por_cobrar` WHERE codigo='$cod_cuenta'";
		$result=mysqli_query($conexion,$sql);
		$mostrar_cuenta=mysqli_fetch_row($result);

		if($mostrar_cuenta != null)
		{
			$valor_total = $mostrar_cuenta[4];
			if($valor_total>$valor_nuevo)
			{
				$sql="UPDATE `cuentas_por_cobrar` SET 
				`valor`=(`valor`-'$valor_nuevo')
				WHERE codigo='$cod_cuenta'";

				$verificacion = mysqli_query($conexion,$sql);

				if($verificacion == 1)
				{
					$cod_cliente = $mostrar_cuenta[1];
					$cliente = $mostrar_cuenta[2];
					$descripcion = $mostrar_cuenta[3];

					if(isset($_SESSION['usuario_restaurante2']))
						$local = 'Restaurante 2';
					else
						$local = 'Restaurante 1';

					$sql="INSERT INTO `cuentas_por_cobrar`(`cod_cliente`, `cliente`, `descripcion`, `valor`, `fecha_registro`, `creador`, `estado`, `local_recepcion`) VALUES (
						'$cod_cliente',
						'$cliente',
						'$descripcion',
						'$valor_nuevo',
						'$fecha_h',
						'$usuario',
						'EN MORA',
						'$local')";

					$verificacion = mysqli_query($conexion,$sql);
				}
			}
			else
				$verificacion = 'El valor nuevo debe ser menor al actual';
		}
		else
			$verificacion = 'No se encontró la cuenta solicitada';
	}
	else
		$verificacion = 'Usted no tiene permisos para cobrar cuentas';
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);

echo json_encode($datos);

?>