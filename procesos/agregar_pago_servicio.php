<?php 
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();

$obj= new conectar();
$conexion=$obj->conexion();
$conexion=$obj->conexion();

$fecha_h = date('Y-m-d G:i:s');

if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];

	$sql_e = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `rol`, `estado`, `foto` FROM `usuarios` WHERE codigo='$usuario'";
	$result_e=mysqli_query($conexion,$sql_e);
	$ver_e=mysqli_fetch_row($result_e);

	$rol = $ver_e[5];
	$cod_unico = uniqid();

	if($rol == 'Cajero 1' || $rol == 'Cajero 2' || $rol == 'Cajero 3')
	{
		$caja = explode(' ', $rol);
		$verificacion = 1;
		if($caja[1] == 1)
		{
			$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado` FROM `caja` WHERE estado = 'ABIERTA'";
		}
		else if($caja[1] == 2)
		{
			$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado` FROM `caja2` WHERE estado = 'ABIERTA'";
		}
		else if($caja[1] == 3)
		{
			$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado` FROM `caja3` WHERE estado = 'ABIERTA'";
		}
		else
			$verificacion = 'Error: Recarque la pagina y vuelva a intentar';

		if($verificacion == 1)
		{
			$result=mysqli_query($conexion,$sql);
			$mostrar=mysqli_fetch_row($result);

			if($mostrar != NULL)
			{
				$cod_caja = $mostrar[0];
				$caja = $_SESSION['caja_restaurante'];
				$verificacion = 1;

				if(isset($_SESSION['usuario_restaurante2']))
					$local = 'Restaurante 2';
				else
					$local = 'Restaurante 1';

				$cod_servicio = $_POST['cod_servicio'];
				$metodo_pago = $_POST['input_metodo_pago'];
				$valor_pago = str_replace('.', '', $_POST['input_valor_pago']);

				if($valor_pago == '')
					$verificacion = 'Ingrese el valor del pago';
				if($metodo_pago == '')
					$verificacion = 'Seleccione un método de pago';

				if($verificacion == 1)
				{
					if($metodo_pago == 'Devolución')
						$valor_pago *= (-1);

					$sql = "SELECT `codigo`, `daños`, `cliente`, `pagos`, `informacion`, `repuestos`, `accesorios`, `creador`, `tecnico`, `estado`, `fecha_entrega`, `fecha_registro` FROM `servicios` WHERE `codigo` = '$cod_servicio'";
					$result=mysqli_query($conexion,$sql);
					$mostrar=mysqli_fetch_row($result);

					if($mostrar[2] != '')
					{
						$cliente = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar[2]);
						$cliente = str_replace('	', ' ', $cliente);
						$cliente = json_decode($cliente,true);
					}
					else
					{
						$cliente = array(
							'codigo' => 0,
							'nombre' => 'No encontrado',
							'telefono' => '',
						);
					}

					$cod_cliente = $cliente['codigo'];

					$pagos = array();
					$pos = 1;
					if ($mostrar[3] != '')
					{
						$pagos = json_decode($mostrar[3],true);
						$pos += count($pagos);
					}

					$pagos[$pos] = array(
						'tipo' => $metodo_pago,
						'valor' => $valor_pago,
						'local' => $local,
						'caja' => $caja,
						'creador' => $usuario,
						'fecha' => $fecha_h,
						'cod_unico' => $cod_unico
					);

					$item_serv = $pos;

					$pagos = json_encode($pagos,JSON_UNESCAPED_UNICODE);

					$sql="UPDATE `servicios` SET `pagos`='$pagos' WHERE codigo='$cod_servicio'";

					$verificacion = mysqli_query($conexion,$sql);
				}
				
				if($verificacion == 1)
				{
					if($metodo_pago == 'Devolución')
						$descripcion_ingreso = 'Devolución total o parcial de orden de servicio #'.str_pad($cod_servicio,5,"0",STR_PAD_LEFT);
					else
						$descripcion_ingreso = 'Pago total o parcial de orden de servicio #'.str_pad($cod_servicio,5,"0",STR_PAD_LEFT);

					if($caja == 1)
						$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado`, `finalizador`, `egresos`, `kilos_fin` FROM `caja` WHERE codigo = '$cod_caja'";
					else if($caja == 2)
						$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado`, `finalizador`, `egresos`, `kilos_fin` FROM `caja2` WHERE codigo = '$cod_caja'";
					else
						$sql = "SELECT `codigo`, `fecha_registro`, `fecha_apertura`, `fecha_cierre`, `inventario`, `ventas`, `total_ventas`, `dinero`, `base`, `ingresos`, `creador`, `cajero`, `estado`, `finalizador`, `egresos`, `kilos_fin` FROM `caja3` WHERE codigo = '$cod_caja'";
					$result=mysqli_query($conexion,$sql);
					$mostrar=mysqli_fetch_row($result);

					$servicios = array();
					$pos = 1;
					if($mostrar[15]!= NULL)
						$servicios = json_decode($mostrar[15],true);
					$pos += count($servicios);

					$servicios[$pos]['descripcion'] = $descripcion_ingreso;
					$servicios[$pos]['metodo'] = $metodo_pago;
					$servicios[$pos]['valor'] = $valor_pago;
					$servicios[$pos]['fecha'] = $fecha_h;
					$servicios[$pos]['creador'] = $usuario;
					$servicios[$pos]['cod_unico'] = $cod_unico;

					$servicios = json_encode($servicios,JSON_UNESCAPED_UNICODE);
					if($caja == 1)
						$sql="UPDATE `caja` SET `servicios`='$servicios' WHERE codigo='$cod_caja'";
					else if($caja == 2)
						$sql="UPDATE `caja2` SET `servicios`='$servicios' WHERE codigo='$cod_caja'";
					else
						$sql="UPDATE `caja3` SET `servicios`='$servicios' WHERE codigo='$cod_caja'";

					$verificacion = mysqli_query($conexion,$sql);
				}

				if($verificacion == 1 && $metodo_pago == 'Crédito')
				{
					$cliente = json_encode($cliente,JSON_UNESCAPED_UNICODE);
					
					$descripcion = 'Servicio N° '.str_pad($cod_servicio,3,"0",STR_PAD_LEFT);
					$sql="INSERT INTO `cuentas_por_cobrar`(`cod_cliente`, `cliente`, `descripcion`, `valor`, `fecha_registro`, `creador`, `estado`, `local_recepcion`) VALUES (
						'$cod_cliente',
						'$cliente',
						'$descripcion',
						'$valor_pago',
						'$fecha_h',
						'$usuario',
						'EN MORA',
						'$local')";

					$verificacion = mysqli_query($conexion,$sql);
				}
			}
			else
				$verificacion = 'No existe una caja ABIERTA';
		}
	}
	else
		$verificacion = 'Solo los cajeros pueden agregar pagos';
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);

echo json_encode($datos);

?>