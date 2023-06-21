<?php 
date_default_timezone_set('America/Bogota');
$fecha_h=date('Y-m-d G:i:s');
$fecha=date('Y-m-d');

require_once "../clases/conexion.php";
require_once "../clases/permisos.php";
$obj_permisos = new permisos();
$obj= new conectar();
$conexion=$obj->conexion();
session_set_cookie_params(7*24*60*60);
session_start();

header("Location:../login.php");

  ?>