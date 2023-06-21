<?php 
date_default_timezone_set('America/Bogota');

class permisos
{
	public function buscar_permiso($cod_usuario,$pagina,$tipo)
	{
		$obj= new conectar();
		$conexion=$obj->conexion();
		$conexion=$obj->conexion();

		 // PERMISO

		$sql = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `foto`, `telefono`, `rol`, `fecha_registro`, `estado`, `permisos` FROM `usuarios` WHERE codigo = '$cod_usuario'";
		$result=mysqli_query($conexion,$sql);
		$mostrar=mysqli_fetch_row($result);

		$nombre_usuario = $mostrar[2].' '.$mostrar[3];
		$permisos = json_decode($mostrar[10],true);

		$rol = $mostrar[7];

		if ($rol == 'Administrador' || $rol == 'admin')
			$acceso = 'SI';
		else
		{
			if (isset($permisos[$pagina][$tipo]))
				$acceso = $permisos[$pagina][$tipo];
			else
				$acceso = 'NO';
		}

  		// FIN PERMISO

		return $acceso;

	}
}