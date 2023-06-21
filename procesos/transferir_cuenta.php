<?php
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj = new crud();

$obj = new conectar();
$conexion = $obj->conexion();

$fecha_h = date('Y-m-d G:i:s');
if (isset($_SESSION['usuario_restaurante'])) {
	$usuario = $_SESSION['usuario_restaurante'];

	$verificacion = 1;

	$cod_mesa_1 = $_POST['cod_mesa_1'];
	$cod_mesa_2 = $_POST['cod_mesa_2'];
	$codigos_pedido = '(';

	if ($cod_mesa_2 != '') {
		$sql_mesa_1 = "SELECT `cod_mesa`, `nombre`, `productos`, `estado`, `fecha_apertura`, `cod_cliente`, `pagos`, `mesero`, `descuentos`, `tipo` FROM `mesas` WHERE cod_mesa = '$cod_mesa_1'";
		$result_mesa_1 = mysqli_query($conexion, $sql_mesa_1);
		$mostrar_mesa_1 = mysqli_fetch_row($result_mesa_1);

		$sql_mesa_2 = "SELECT `cod_mesa`, `nombre`, `productos`, `estado`, `fecha_apertura`, `cod_cliente`, `pagos`, `mesero`, `descuentos`, `tipo` FROM `mesas` WHERE cod_mesa = '$cod_mesa_2'";
		$result_mesa_2 = mysqli_query($conexion, $sql_mesa_2);
		$mostrar_mesa_2 = mysqli_fetch_row($result_mesa_2);

		if ($mostrar_mesa_2[3] == 'LIBRE') {
			if ($mostrar_mesa_1[2] != '') {
				$productos_mesa_nuevos = json_decode($mostrar_mesa_1[2], true);

				foreach ($productos_mesa_nuevos as $i => $producto) {
					$codigos_pedido .= "'" . $producto['cod_pedido'] . "',";
				}

				$codigos_pedido = substr($codigos_pedido, 0, -1) . ')';;
			} else
				$verificacion = 'No existe producto en la mesa origen';

			if ($verificacion == 1) {
				$fecha_apertura = $mostrar_mesa_1[4];
				if ($mostrar_mesa_1[5] != null)
					$cod_cliente = "'" . $mostrar_mesa_1[5] . "'";
				else
					$cod_cliente = 'NULL';
				$pagos = $mostrar_mesa_1[6];
				$mesero = $mostrar_mesa_1[7];
				$descuentos = $mostrar_mesa_1[8];
				$tipo = $mostrar_mesa_1[9];

				$productos_mesa_nuevos = json_encode($productos_mesa_nuevos, JSON_UNESCAPED_UNICODE);

				$sql = "UPDATE `mesas` SET 
						`estado`='OCUPADA',
						`productos`='$productos_mesa_nuevos',
						`fecha_apertura`='$fecha_apertura',
						`cod_cliente`= $cod_cliente,
						`pagos`='$pagos',
						`mesero`='$mesero',
						`descuentos`='$descuentos'
						WHERE cod_mesa='$cod_mesa_2'";
				$verificacion = mysqli_query($conexion, $sql);
			}

			if ($verificacion == 1) {
				$sql = "UPDATE `pedidos` SET 
				`mesa`='$cod_mesa_2'
				WHERE codigo IN $codigos_pedido";

				$verificacion = mysqli_query($conexion, $sql);
			}

			if ($verificacion == 1) {
				$sql = "UPDATE `mesas` SET 
					`productos`='',
					`estado`='LIBRE',
					`fecha_apertura`= NULL,
					`cod_cliente`= NULL,
					`pagos`= NULL,
					`mesero`= NULL,
					`descuentos`= NULL
					WHERE cod_mesa='$cod_mesa_1'";

				$verificacion = mysqli_query($conexion, $sql);
			}

			if ($verificacion == 1) {
				$sql = "UPDATE `configuraciones` SET 
				`valor`='2'
				WHERE descripcion='Cambios'";

				$verificacion = mysqli_query($conexion, $sql);
			}
		} else
			$verificacion = 'La mesa se encuentra ocupada';
	} else
		$verificacion = 'Seleccione la mesa nueva';
} else
	$verificacion = 'Reload';

$datos = array(
	'consulta' => $verificacion
);

echo json_encode($datos);
