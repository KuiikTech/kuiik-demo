<?php 
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();

$obj= new conectar();
$conexion=$obj->conexion();
$conexion=$obj->conexion();

$fecha=date('Y-m-d');
$fecha_h = date('Y-m-d G:i:s');

if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];

	$cod_espacio = $_POST['cod_espacio'];
	$clase = $_POST['clase'];
	$valor = $_POST['valor'];

	$verificacion = 1;

	$sql_espacio = "SELECT `codigo`, `nombre`, `items`, `fecha_creacion`, `cod_cliente`, `pagos`, `informacion`, `caja` FROM `espacios` WHERE codigo = '$cod_espacio'";
	$result_espacio=mysqli_query($conexion,$sql_espacio);
	$mostrar_espacio=mysqli_fetch_row($result_espacio);

	$informacion = array();
	if($mostrar_espacio[6] != '')
	{
		$informacion = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar_espacio[6]);
		$informacion = str_replace('	', ' ', $informacion);
		$informacion = json_decode($informacion,true);
	}

	if($clase == 'total_servicios')
	{
		if($valor != '')
			$informacion[$clase] = str_replace('.', '', $valor);
		else
			$verificacion = 'El total de servicios/revisión no puede ser nulo.';
	}
	else
	{
		if($clase == 'equipo')
		{
			$informacion[$clase] = $valor;
			unset($informacion['lista_info']);
			$sql = "SELECT `codigo`, `nombre`, `informacion`, `estado`, `fecha_creacion`, `creador` FROM `tipo_equipos` WHERE `codigo`='$valor'";
			$result=mysqli_query($conexion,$sql);
			while ($mostrar=mysqli_fetch_row($result)) 
			{ 
				$lista = array();
				if($mostrar[2] != '')
				{
					$lista = preg_replace("/[\r\n|\n|\r]+/", " ", $mostrar[2]);
					$lista = str_replace('  ', ' ', $lista);
					$lista = json_decode($lista,true);
				}
				$pos = 1;
				foreach ($lista as $i => $item)
				{
					$informacion['lista_info'][$pos] = $item;
					$informacion['lista_info'][$pos]['valor'] = null;
					$pos ++;
				}
			}
		}
		else if($clase == 'observaciones')
		{
			$pos = 1;
			if(!isset($informacion['observaciones']))
				$informacion['observaciones'] = array();

			if (isset($_SESSION['usuario_restaurante2']))
				$local = 'Restaurante 2';
			else
				$local = 'Restaurante 1';

			$informacion['observaciones'][$pos] = array(
				'obs' => $valor, 
				'local' => $local,
				'creador' => $usuario, 
				'fecha' => $fecha_h );
		}
		else if($clase == 'fecha_entrega')
		{
			if($valor >= $fecha)
				$informacion[$clase] = $valor;
			else
				$verificacion = 'La fecha debe ser mayor o igual a la fecha actual';
		}
		else
			$informacion[$clase] = $valor;
	}

	if($verificacion == 1)
	{
		$informacion = json_encode($informacion,JSON_UNESCAPED_UNICODE);

		$sql="UPDATE `espacios` SET `informacion`='$informacion' WHERE codigo='$cod_espacio'";

		$verificacion = mysqli_query($conexion,$sql);
	}
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);

echo json_encode($datos);

?>