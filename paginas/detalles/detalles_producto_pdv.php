<?php 
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME,'spanish');
$fecha_h=date('Y-m-d G:i:s');
$fecha=date('Y-m-d');
require_once "../../clases/conexion.php";
$obj= new conectar();
$conexion=$obj->conexion();
$conexion=$obj->conexion();

session_start();

$rol = '';

if (isset($_SESSION['usuario_restaurante'])) {
  $usuario = $_SESSION['usuario_restaurante'];

  $sql_e = "SELECT `codigo`, `cedula`, `nombre`, `apellido`, `contraseña`, `rol`, `estado`, `foto` FROM `usuarios` WHERE codigo='$usuario'";
  $result_e = mysqli_query($conexion, $sql_e);
  $ver_e = mysqli_fetch_row($result_e);

  $cedula = $ver_e[1];

  $nombre_usuario = $ver_e[2] . ' ' . $ver_e[3];
  $rol = $ver_e[5];
}

$cod_producto = $_GET['cod_producto'];

$sql = "SELECT `codigo`, `descripcion`, `unidad`, `valor`, `inventario`, `categoria`, `imagen`, `fecha_registro`, `area`, `tipo`, `estado`, `barcode` FROM `productos` WHERE codigo = '$cod_producto'";
$result=mysqli_query($conexion,$sql);
$mostrar=mysqli_fetch_row($result);

$codigo = $mostrar[0];
$descripcion = $mostrar[1];
$categoria = $mostrar[2];
$estado = $mostrar[4];
$barcode = $mostrar[5];

$fecha_registro = strftime("%A, %e %b %Y", strtotime($mostrar[8]));
$fecha_registro = ucfirst(iconv("ISO-8859-1","UTF-8",$fecha_registro));

$fecha_registro .= date(' | h:i A',strtotime($mostrar[6]));

$sql_cat = "SELECT `cod_categoria`, `nombre` FROM `categorias_productos` WHERE cod_categoria='$categoria'";
$result_cat=mysqli_query($conexion,$sql_cat);
$mostrar_cat=mysqli_fetch_row($result_cat);

if($mostrar_cat != null)
  $categoria = ucwords(strtolower($mostrar_cat[1]));

?>
<div class="modal-header text-center p-2">
  <h5 class="modal-title">Detalles de producto</h5>
</div>
<div class="modal-body p-2">
  <div class="row m-0 p-1">
    <p class="row mb-0">
      <span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 col-5 text-right text-truncate"> Descripción: </span>
      <span class="col-lg-7 col-md-7 col-sm-7 col-xs-7 col-7 text-left"><b> <?php echo $descripcion ?> </b></span>
    </p>
    <p class="row mb-0">
      <span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 col-5 text-right text-truncate"> Categoría: </span>
      <span class="col-lg-7 col-md-7 col-sm-7 col-xs-7 col-7 text-left"><b> <?php echo $categoria ?> </b></span>
    </p>
    <p class="row mb-0">
      <span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 col-5 text-right text-truncate"> Barcode: </span>
      <span class="col-lg-7 col-md-7 col-sm-7 col-xs-7 col-7 text-left"><b> <?php echo $barcode ?> </b></span>
    </p>
    <p class="row mb-0">
      <span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 col-5 text-right text-truncate"> Estado: </span>
      <span class="col-lg-7 col-md-7 col-sm-7 col-xs-7 col-7 text-left"><b> <?php echo $estado ?> </b></span>
    </p>
    <p class="row mb-0">
      <span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 col-5 text-right text-truncate"> Fecha Registro: </span>
      <span class="col-lg-7 col-md-7 col-sm-7 col-xs-7 col-7 text-left"><b> <?php echo $fecha_registro ?> </b></span>
    </p>

    <ul class="nav nav-tabs" role="tablist">
      <li class="nav-item">
        <a class="nav-link text-gray active" id="principal-tab" data-bs-toggle="tab" href="#principal" role="tab" aria-controls="principal" aria-selected="true">Bodega principal</a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-gray" id="PDV_1-tab" data-bs-toggle="tab" href="#PDV_1" role="tab" aria-controls="PDV_1" aria-selected="false">PDV 1</a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-gray" id="PDV_2-tab" data-bs-toggle="tab" href="#PDV_2" role="tab" aria-controls="PDV_2" aria-selected="false">PDV 2</a>
      </li>
    </ul>

    <div class="tab-content">
      <div class="tab-pane show py-2 active" id="principal" role="tabpanel" aria-labelledby="principal-tab">
        <div class="row m-0" id="div_tabla_bodega_principal">
          <div class="text-center px-2">
            <h4>Lista de inventario</h4>
            <?php 
            $inventario = array();
            if ($mostrar[3] != '')
              $inventario = json_decode($mostrar[3],true);
            require('../../tablas/inventario_producto_pdv.php');
            ?>
          </div>
        </div>
      </div>

      <div class="tab-pane fade py-2" id="PDV_1" role="tabpanel" aria-labelledby="PDV_1-tab">
        <div class="row m-0" id="div_tabla_pdv_1">
          <div class="text-center px-2">
            <h4>Lista de inventario</h4>
            <?php 
            $inventario = array();
            if ($mostrar[6] != '')
              $inventario = json_decode($mostrar[6],true);
            require('../../tablas/inventario_producto_pdv.php');
            ?>
          </div>
        </div>
      </div>

      <div class="tab-pane fade py-2" id="PDV_2" role="tabpanel" aria-labelledby="PDV_2-tab">
        <div class="row m-0" id="div_tabla_pdv_2">
          <div class="text-center px-2">
            <h4>Lista de inventario</h4>
            <?php 
            $inventario = array();
            if ($mostrar[7] != '')
              $inventario = json_decode($mostrar[7],true);
            require('../../tablas/inventario_producto_pdv.php');
            ?>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>