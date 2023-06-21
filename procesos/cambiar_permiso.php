<?php 
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj= new crud();

$obj_2= new conectar();
$conexion=$obj_2->conexion();
$conexion=$obj_2->conexion();

$cod_usuario = $_POST['cod_usuario'];
$pagina = $_POST['pagina'];
$tipo = $_POST['tipo'];

if(isset($_SESSION['usuario_restaurante']))
	$usuario = $_SESSION['usuario_restaurante'];
else
	$usuario = '9999';

$estado = '';

$datos=array(
	$cod_usuario,
	$pagina,
	$tipo
);

$verificacion = $obj->cambiar_permiso($datos);

if($verificacion == 1)
{
	$descrip_reg = array(
		'descripcion' => 'Cambio de permiso',
		'cod_usuario' => $cod_usuario,
		'pagina' => $pagina,
		'tipo' => $tipo
	);

	$datos_reg=array(
		json_encode($descrip_reg,JSON_UNESCAPED_UNICODE),
		$usuario
	);
	$verificacion = $obj->reg_mov($datos_reg);
}

$sql="SELECT `permisos` FROM `usuarios` WHERE codigo = '$cod_usuario'";
$result=mysqli_query($conexion,$sql);
$ver=mysqli_fetch_row($result);

$permisos = json_decode($ver[0],true);

$estado = $permisos[$pagina][$tipo];

$datos=array(
	'consulta' => $verificacion,
	'estado' => $estado
);

echo json_encode($datos);

?>