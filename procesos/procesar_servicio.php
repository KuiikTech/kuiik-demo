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

$cod_servicio = 0;
$config_imp = '';

if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];
	$caja = $_SESSION['caja_restaurante'];

	$sql_e = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `rol`, `estado`, `foto` FROM `usuarios` WHERE codigo='$usuario'";
	$result_e=mysqli_query($conexion,$sql_e);
	$ver_e=mysqli_fetch_row($result_e);

	$rol = $ver_e[5];

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
			$caja = $caja[1];

			$result=mysqli_query($conexion,$sql);
			$mostrar=mysqli_fetch_row($result);

			if($mostrar != NULL)
			{
				$cod_caja = $mostrar[0];
				require_once "../clases/permisos.php";
				$obj_permisos = new permisos();
				$acceso = $obj_permisos->buscar_permiso($usuario,'PDV','PROCESAR');

				if($acceso == 'SI')
				{
					$cod_espacio = $_POST['cod_espacio'];
					$total = 0;
					$total_descuento = 0;
					$verificacion = 1;

					if (isset($_SESSION['usuario_restaurante2']))
						$bodega = 'PDV_2';
					else
						$bodega = 'PDV_1';

					$sql_espacio = "SELECT `codigo`, `nombre`, `items`, `fecha_creacion`, `cod_cliente`, `pagos`, `informacion`, `cambios`, `caja` FROM `espacios` WHERE `codigo` = '$cod_espacio'";
					$result_espacio=mysqli_query($conexion,$sql_espacio);
					$mostrar_espacio=mysqli_fetch_row($result_espacio);

					$informacion = array();
					if($mostrar_espacio[6] != '')
					{
						$informacion = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar_espacio[6]);
						$informacion = str_replace('	', ' ', $informacion);
						$informacion = json_decode($informacion,true);
					}

					if(!isset($informacion['hora_entrega']))
						$verificacion = 'Seleccione la posible hora de entrega.';
					if(!isset($informacion['fecha_entrega']))
						$verificacion = 'Seleccione la posible fecha de entrega.';
					if($mostrar_espacio[5] == '')
						$verificacion = 'No existen abonos/pagos agregados';
					if(!isset($informacion['total_servicios']))
						$verificacion = 'Ingrese el total a pagar de servicios/revisión';
					else
					{
						if($informacion['total_servicios'] <= 0)
							$verificacion = 'El total a pagar de servicios/revisión debe ser mayor a 0.(cero)';
					}
					if($mostrar_espacio[2] == '')
						$verificacion = 'No existen daños agregados';
					if(!isset($informacion['equipo']))
						$verificacion = 'Seleccione el equipo.';
					if(!isset($informacion['tipo']))
						$verificacion = 'Seleccione el tipo de servicio.';
					if($mostrar_espacio[4] == null)
						$verificacion = 'Seleccione un cliente';

					if($verificacion == 1)
					{
						$cod_cliente = $mostrar_espacio[4];
						$sql_cliente = "SELECT `codigo`, `id`, `nombre`, `telefono` FROM `clientes` WHERE codigo = '$cod_cliente'";
						$result_cliente=mysqli_query($conexion,$sql_cliente);
						$ver_cliente=mysqli_fetch_row($result_cliente);

						$cliente = array(
							'codigo' => $cod_cliente,
							'id' => $ver_cliente[1],
							'nombre' => $ver_cliente[2],
							'telefono' => $ver_cliente[3],
						);

						$cliente = json_encode($cliente,JSON_UNESCAPED_UNICODE);

						$daños = $mostrar_espacio[2];
						$pagos = $mostrar_espacio[5];

						$fecha_entrega = date('Y-m-d G:i:s',strtotime($informacion['fecha_entrega'].' '.$informacion['hora_entrega']));

						$informacion = $mostrar_espacio[6];

						if(isset($_SESSION['usuario_restaurante2']))
							$local = 'Restaurante 2';
						else
							$local = 'Restaurante 1';

						$sql="INSERT INTO `servicios`(`daños`, `cliente`, `pagos`, `informacion`, `creador`, `estado`, `fecha_registro`, `fecha_entrega`, `local`) VALUES (
							'$daños',
							'$cliente',
							'$pagos',
							'$informacion',
							'$usuario',
							'PENDIENTE',
							'$fecha_h',
							'$fecha_entrega',
							'$local')";

						$verificacion = mysqli_query($conexion,$sql);

						if($verificacion == 1)
						{
							$sql="SELECT MAX(codigo)
							as codigo  from `servicios`";
							$result=mysqli_query($conexion,$sql);
							$ver=mysqli_fetch_row($result);
							$cod_servicio = $ver[0];
						}

						if($verificacion == 1)
						{
							$pagos = json_decode($mostrar_espacio[5],true);

							foreach ($pagos as $i => $pago)
							{
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
								$servicios[$pos]['metodo'] = $pago['tipo'];
								$servicios[$pos]['valor'] = $pago['valor'];
								$servicios[$pos]['fecha'] = $fecha_h;
								$servicios[$pos]['creador'] = $usuario;
								$servicios[$pos]['cod_unico'] = $pago['cod_unico'];

								$servicios = json_encode($servicios,JSON_UNESCAPED_UNICODE);
								if($caja == 1)
									$sql="UPDATE `caja` SET `servicios`='$servicios' WHERE codigo='$cod_caja'";
								else if($caja == 2)
									$sql="UPDATE `caja2` SET `servicios`='$servicios' WHERE codigo='$cod_caja'";
								else
									$sql="UPDATE `caja3` SET `servicios`='$servicios' WHERE codigo='$cod_caja'";

								$verificacion = mysqli_query($conexion,$sql);

								if($verificacion == 1 && $pago['tipo'] == 'Crédito')
								{
									$valor_pago = $pago['valor'];
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

						}

						if($verificacion == 1)
						{
							$sql="DELETE from `espacios` 
							WHERE `codigo`='$cod_espacio'";

							$verificacion = mysqli_query($conexion,$sql);

							$sql = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Imprimir Facturas'";
							$result=mysqli_query($conexion,$sql);
							$ver=mysqli_fetch_row($result);

							$config_imp = $ver[2];
						}
					}
				}
				else
					$verificacion = 'No tienes permisos para procesar servicios';
			}
			else
				$verificacion = 'No existe una caja ABIERTA';
		}
	}
	else
		$verificacion = 'Solo los cajeros pueden procesar servicios';
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion,
	'cod_servicio' => $cod_servicio,
	'config_imp' => $config_imp

);

echo json_encode($datos);

?>