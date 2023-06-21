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
	
	$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `ingresos`, `egresos`, `base`, `creador`, `cajero`, `finalizador`, `estado`, `base_sig` FROM `caja` WHERE estado = 'ABIERTA'";
	$result=mysqli_query($conexion,$sql);
	$mostrar_caja=mysqli_fetch_row($result);

	if($mostrar_caja != NULL)
	{
		$input_metodo_pago = $_POST['input_metodo_pago'];
		$input_valor_pago = str_replace('.', '', $_POST['input_valor_pago']);

		if ($input_metodo_pago == '')
			$verificacion = 'Seleccione un método de pago';

		if ($verificacion == 1)
		{
			if($input_metodo_pago == 'Bono')
			{
				$cod_bono = $_POST['input_cod_bono'];
				$sql_bono = "SELECT `codigo`, `cliente`, `beneficiario`, `valor`, `informacion`, `fecha_vencimiento`, `estado`, `fecha_registro` FROM `bonos` WHERE codigo = '$cod_bono'";
				$result_bono=mysqli_query($conexion,$sql_bono);
				$ver_bono=mysqli_fetch_row($result_bono);

				if($ver_bono != null)
				{
					$input_valor_pago = $ver_bono[3];

					if($ver_bono[6] == 'VIGENTE')
					{
						$sql="UPDATE `bonos` SET 
						`estado`='COBRADO'
						WHERE codigo='$cod_bono'";

						$verificacion = mysqli_query($conexion,$sql);
					}
					else
						$verificacion = 'El bono con el código ('.$cod_bono.') ya fue cobrado';
				}
				else
					$verificacion = 'No existe un bono con el código ingresado';
			}
			else
			{
				if ($input_valor_pago == '')
					$verificacion = 'Ingrese el valor del pago';
			}

			if ($verificacion == 1)
			{
				$cod_orden =  $_POST['cod_orden'];
				$item =  $_POST['item'];

				$sql = "SELECT `codigo`, `servicios`, `cliente`, `pagos`, `creador`, `fecha_registro` FROM `ordenes` WHERE codigo = '$cod_orden'";
				$result=mysqli_query($conexion,$sql);
				$mostrar=mysqli_fetch_row($result);

				$pagos = array();
				$pos = 1;
				if($mostrar[3] != '')
				{
					$pagos = json_decode($mostrar[3],true);
					$pos += count($pagos);
				}

				$pagos[$pos]['metodo'] = $input_metodo_pago;
				$pagos[$pos]['valor'] = $input_valor_pago;
				$pagos[$pos]['creador'] = $usuario;
				$pagos[$pos]['fecha_registro'] = $fecha_h;

				if($input_metodo_pago == 'Bono')
					$pagos[$pos]['cod_bono'] = $cod_bono;

				if($verificacion == 1)
				{
					$pagos = json_encode($pagos,JSON_UNESCAPED_UNICODE);
					$sql="UPDATE `ordenes` SET 
					`pagos`='$pagos'
					WHERE codigo='$cod_orden'";

					$verificacion = mysqli_query($conexion,$sql);
				}

				if($verificacion == 1)
				{
					$cod_caja = $mostrar_caja[0];
					$ingresos = array();
					$pos = 1;
					if ($mostrar_caja[4] != '')
					{
						$ingresos = json_decode($mostrar_caja[4],true);
						$pos += count($ingresos);
					}

					$info = 'Pago total o parcial orden #';

					$ingresos[$pos]["concepto"] = $info.str_pad($cod_orden,5,"0",STR_PAD_LEFT);
					$ingresos[$pos]["valor"] = $input_valor_pago;
					$ingresos[$pos]["metodo"] = $input_metodo_pago;
					$ingresos[$pos]["fecha"] = $fecha_h;
					$ingresos[$pos]["creador"] = $usuario;

					$ingresos = json_encode($ingresos,JSON_UNESCAPED_UNICODE);

					$sql="UPDATE `caja` SET 
					`ingresos`='$ingresos'
					WHERE codigo='$cod_caja'";

					$verificacion = mysqli_query($conexion,$sql);
				}
			}
		}
	}
	else
		$verificacion = 'No se autorizó el pago. La caja NO se encuentra abierta';
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);
echo json_encode($datos);
?>
