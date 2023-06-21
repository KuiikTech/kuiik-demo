<?php 
date_default_timezone_set('America/Bogota');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();
$obj_2= new conectar();

$fecha_h=date('Y-m-d G:i:s');

$conexion=$obj_2->conexion();
$conexion_bodega=$obj_2->conexion_bodega();

if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];

	$verificacion = 1;
	$total_bono = 0;

	$input_tipo = $_POST['input_tipo'];
	$input_metodo_pago = $_POST['input_metodo_pago'];
	
	if ($input_metodo_pago =='')
		$verificacion='Seleccione método de pago';
	if ($input_tipo =='')
		$verificacion='Seleccione el tipo de bono';
	if (!isset($_SESSION['beneficiario_bono']))
		$verificacion='Seleccione un beneficiario';
	if (!isset($_SESSION['cliente_bono']))
		$verificacion='Seleccione un cliente';

	if ($verificacion == 1)
	{
		$informacion = '';
		$cliente_bono = $_SESSION['cliente_bono'];
		$beneficiario_bono = $_SESSION['beneficiario_bono'];

		if ($input_tipo == 'servicios')
		{
			if (!isset($_SESSION['lista_servicios_bono']))
				$verificacion='No existen servicios agregados. Agregue al menos 1 servicio';
			else
			{
				$lista_servicios_bono = $_SESSION['lista_servicios_bono'];
				foreach ($lista_servicios_bono as $i => $servicio)
					$total_bono += $servicio['valor'];
			}

			$informacion = array(
				'servicios' => $lista_servicios_bono,
				'metodo' => $input_metodo_pago 
			);
		}
		else
		{
			if ($_POST['input_valor'] != '')
				$total_bono = str_replace('.', '', $_POST['input_valor']);
			else
				$verificacion='Ingrese el valor del bono';
		}

		if ($verificacion == 1)
		{
			$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `ingresos`, `egresos`, `base`, `creador`, `cajero`, `finalizador`, `estado`, `base_sig` FROM `caja` WHERE estado = 'ABIERTA'";
			$result=mysqli_query($conexion,$sql);
			$mostrar_caja=mysqli_fetch_row($result);

			if($mostrar_caja != NULL)
			{
				if($informacion != '')
					$informacion = json_encode($informacion,JSON_UNESCAPED_UNICODE);

				$fecha_ven = date('Y-m-d',strtotime($fecha_h.' +1 month '));

				$sql="INSERT INTO `bonos`(`cliente`, `beneficiario`, `valor`, `informacion`, `fecha_vencimiento`, `estado`, `fecha_registro`) VALUES (
				'$cliente_bono',
				'$beneficiario_bono',
				'$total_bono',
				'$informacion',
				'$fecha_ven',
				'VIGENTE',
				'".date('Y-m-d G:i:s')."')";

				$verificacion = mysqli_query($conexion,$sql);

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

					$sql="SELECT MAX(codigo) from bonos";
					$result=mysqli_query($conexion,$sql);
					$mostrar=mysqli_fetch_row($result);

					$info = 'Pago Bono #'.str_pad($mostrar[0],5,"0",STR_PAD_LEFT);

					$ingresos[$pos]["concepto"] = $info;
					$ingresos[$pos]["valor"] = $total_bono;
					$ingresos[$pos]["metodo"] = $input_metodo_pago;
					$ingresos[$pos]["fecha"] = $fecha_h;
					$ingresos[$pos]["creador"] = $usuario;

					$ingresos = json_encode($ingresos,JSON_UNESCAPED_UNICODE);

					$sql="UPDATE `caja` SET 
					`ingresos`='$ingresos'
					WHERE codigo='$cod_caja'";

					$verificacion = mysqli_query($conexion,$sql);
				}
				else
					$verificacion = 'No se autorizó el pago. La caja NO se encuentra abierta';
			}
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
