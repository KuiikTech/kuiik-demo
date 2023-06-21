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

	$num_item = $_POST['num_item'];

	$sql = "SELECT `codigo`, `producto`, `proveedor`, `valor`, `creador`, `fecha_registro`, `estado`, `observaciones` FROM `repuestos_cotizados` WHERE estado = 'EN PROCESO' order by fecha_registro DESC";
	$result=mysqli_query($conexion,$sql);
	$mostrar=mysqli_fetch_row($result);

	if($mostrar != NULL)
	{
		if($mostrar[1]!= '')
		{
            $productos_repuesto_nuevos = array();
			$cod_repuesto = $mostrar[0];
			$productos_repuesto = json_decode($mostrar[1],true);
            unset($productos_repuesto[$num_item]);
            $pos = 1;
            foreach ($productos_repuesto as $i => $item)
            {
                $productos_repuesto_nuevos[$pos] = $item;
                $pos ++;
            }
            if($pos == 1)
                $productos_repuesto_nuevos = '';
            else
                $productos_repuesto_nuevos = json_encode($productos_repuesto_nuevos,JSON_UNESCAPED_UNICODE);

            $sql="UPDATE `repuestos_cotizados` SET
            `producto`='$productos_repuesto_nuevos'
            WHERE codigo='$cod_repuesto'";

            $verificacion = mysqli_query($conexion,$sql);
        }
        else
            $verificacion = 'No existen items agregados';
	}
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion
);
echo json_encode($datos);
?>
