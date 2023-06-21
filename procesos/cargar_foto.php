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
$ruta = '';

if(isset($_SESSION['usuario_restaurante']))
{
	$usuario = $_SESSION['usuario_restaurante'];

	$cod_usuario = $_GET['cod_usuario'];

	$file = $_FILES['imagen_usuario'];

	$r_foto =$file['tmp_name'];

	if ($file['name'] != '' )
	{
		if (is_uploaded_file($r_foto))
		{
			if ($file['type']== "image/jpeg" OR $file['type']== "image/png")
			{
				if($file['size'] < 5000000)
				{
					$sql = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `foto`, `telefono`, `rol`, `fecha_registro`, `estado`, `color` FROM `usuarios` WHERE codigo = '$cod_usuario'";
					$result=mysqli_query($conexion,$sql);
					$mostrar=mysqli_fetch_row($result);

					$cedula = $mostrar[1];
					$destino = __DIR__ .'/../recursos/user/'.$cedula.'.jpg';

					$ruta = 'recursos/user/'.$cedula.'.jpg';

					// IMAGEN CUADRADA
					if ($_FILES['imagen_usuario']['type']== "image/jpeg")
						$original = imagecreatefromjpeg($r_foto);
					if ($_FILES['imagen_usuario']['type']== "image/png")
						$original = imagecreatefrompng($r_foto);

					$copia = imagecreatetruecolor(200, 200);

					$alto_original = imagesy($original);
					$ancho_original = imagesx($original);

					if ($alto_original > $ancho_original)
					{
						$src_x = 0;
						$src_y = ($alto_original-$ancho_original)/2;

						$tam = $ancho_original;
					}
					else
					{
						$src_y = 0;
						$src_x = ($ancho_original-$alto_original)/2;
						$tam = $alto_original;
					}
					imagecopyresampled($copia, $original, 0, 0, $src_x, $src_y, 200, 200, $tam, $tam);

					//if (move_uploaded_file($copia,$destino))
					if (imagejpeg($copia,$destino,100))
					{
						$foto = $cedula.'.jpg';

						$sql="UPDATE `usuarios` SET 
						`foto`='$foto'
						WHERE codigo='$cod_usuario'";

						$verificacion = mysqli_query($conexion,$sql);
					}
					else
						$verificacion = 'Error al subir la Imagen';
				}
				else
					$verificacion = 'Peso maximo de la imagen 5MB';
			}
			else
				$verificacion = 'Seleccione una imagen valida';
		}
		else
			$verificacion = 'Seleccione una imagen valida';
	}
	else
		$verificacion = 'Seleccione la foto que desea subir';
}
else
	$verificacion = 'Reload';

$datos=array(
	'consulta' => $verificacion,
	'ruta' => $ruta
);

echo json_encode($datos);

?>