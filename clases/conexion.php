<?php
class conectar
{
	public function conexion()
	{
		$conexion=mysqli_connect('localhost',
			'root',
			'',
			'witsoftc_rancho_v2');
		$conexion->set_charset('utf8');
		return $conexion;
	}
}
?>