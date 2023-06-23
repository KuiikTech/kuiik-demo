<!-- connexion example -->
<?php
class conectar
{
	public function conexion()
	{
		$conexion=mysqli_connect('localhost',
			'root',
			'abc123',
			'database');
		$conexion->set_charset('utf8');
		return $conexion;
	}
}
?>