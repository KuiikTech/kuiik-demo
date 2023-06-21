<?php 
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME,'spanish');
$fecha_h=date('Y-m-d G:i:s');
$fecha=date('Y-m-d');
require_once "../../clases/conexion.php";
$obj= new conectar();
$conexion=$obj->conexion();
$conexion_1=$obj->conexion_m1();
$conexion_2=$obj->conexion_m2();
$conexion=$obj->conexion();

$fecha_inicial = date('Y-m-d 00:00:00',strtotime($_GET['fecha_inicial']));
$fecha_final = date('Y-m-d 23:59:59',strtotime($_GET['fecha_final']));

$sql = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Empresa'";
$result=mysqli_query($conexion_1,$sql);
$ver=mysqli_fetch_row($result);

$empresa = preg_replace("/[\r\n|\n|\r]+/", " ", $ver[2]);
$empresa = str_replace('  ', ' ', $empresa);
$empresa = json_decode($empresa,true);

$sql = "SELECT `codigo`, `descripcion`, `valor`, `creador`, `fecha_registro`, `estado`, `aprobo`, `fecha_aprobacion`, `local`, `tipo`, `categoria` FROM `caja_mayor` WHERE fecha_aprobacion BETWEEN '$fecha_inicial' AND '$fecha_final' ORDER BY  fecha_registro DESC";
$result=mysqli_query($conexion,$sql);
$productos = array();

$fecha_inicial = date('d/m/y',strtotime($_GET['fecha_inicial']));
$fecha_final = date('d/m/y',strtotime($_GET['fecha_final']));

$periodo = $fecha_inicial.' - '.$fecha_final;

?>
<div class="text-center p-2">
  <h5 class="text-center">Informe de Gastos</h5>
  <hr>
</div>
<div class="p-2">
  <div class="row">
    <p class="row mb-0">
      <span class="text-left"> Periodo: <b> <?php echo $periodo ?> </b></span>
    </p>
    <p class="row mb-0">
      <span class="text-left"> Empresa: <b> <?php echo $empresa['nombre'] ?> </b></span>
    </p>
    <p class="row mb-0">
      <span class="text-left"> NIT: <b> <?php echo $empresa['nit'] ?> </b></span>
    </p>
    <p class="row mb-0">
      <span class="text-left"> Dirección: <b> <?php echo $empresa['direccion'] ?> </b></span>
    </p>
    <p class="row mb-0">
      <span class="text-left"> Ciudad: <b> <?php echo $empresa['ciudad'] ?> </b></span>
    </p>

    <div class="row m-0 mt-5" id="div_tabla_ventas">
      <div class="border-top text-center px-2">
        <table class="table text-dark table-sm Data_Table" id="tabla_caja">
          <thead>
            <tr class="text-center">
              <th class="p-1" width="20px">#</th>
              <th class="p-1" width="20px">Cod</th>
              <th class="p-1">Descripción</th>
              <th class="p-1">Valor</th>
              <th class="p-1">Local</th>
              <th class="p-1">Tipo</th>
              <th class="p-1">Fecha</th>
            </tr>
          </thead>
          <tbody class="overflow-auto">
            <?php 
            $num_item = 1;
            $total_retirado = 0;
            $total_fijos = 0;
            $total_variables = 0;
            while ($mostrar=mysqli_fetch_row($result)) 
            { 
              $codigo = $mostrar[0];
              $descripcion = $mostrar[1];
              $valor = $mostrar[2];
              $local = $mostrar[8];
              $tipo = $mostrar[9];
              $categoria = $mostrar[10];

              if($tipo == 'Retiro')
              {
                $aprobo = '---';

                $fecha_registro = strftime("%A, %e %b %Y", strtotime($mostrar[4]));
                $fecha_registro = ucfirst(iconv("ISO-8859-1","UTF-8",$fecha_registro));
                $fecha_registro .= date(' h:i A',strtotime($mostrar[4]));

                if($mostrar[7] != NULL)
                {
                  $fecha_aprobacion = strftime("%A, %e %b %Y", strtotime($mostrar[7]));
                  $fecha_aprobacion = ucfirst(iconv("ISO-8859-1","UTF-8",$fecha_aprobacion));
                  $fecha_aprobacion .= date(' h:i A',strtotime($mostrar[7]));
                }
                else
                  $fecha_aprobacion = '---';

                $estado = $mostrar[5];

                $creador = $mostrar[3];

                $text_valor = '';

                $sql_e = "SELECT nombre, apellido, foto FROM `usuarios` WHERE codigo = '$creador'";
                $result_e=mysqli_query($conexion,$sql_e);
                $ver_e=mysqli_fetch_row($result_e);

                $creador = $ver_e[0].' '.$ver_e[1];

                if($estado == 'APROBADO')
                {
                  if($valor >0)
                    $text_valor = 'text-success';
                  else
                    $text_valor = 'text-danger';

                  $aprobo = $mostrar[3];

                  $sql_e = "SELECT nombre, apellido, foto FROM `usuarios` WHERE codigo = '$aprobo'";
                  $result_e=mysqli_query($conexion,$sql_e);
                  $ver_e=mysqli_fetch_row($result_e);

                  $aprobo = $ver_e[0].' '.$ver_e[1];

                  $total_retirado += $valor;

                  if($categoria == 'Fijo')
                    $total_fijos += $valor;
                  if($categoria == 'Variable')
                    $total_variables += $valor;
                  ?>
                  <tr>
                    <td class="text-center p-1"><?php echo $num_item ?></td>
                    <td class="text-center p-1"><?php echo str_pad($codigo,3,"0",STR_PAD_LEFT) ?></td>
                    <td class="p-1"><?php echo $descripcion ?></td>
                    <td class="text-right p-1 <?php echo $text_valor ?>"><b>$<?php echo number_format($valor,0,'.','.')?></b></td>
                    <td class="text-center p-1"><b><?php echo $local ?></b></td>         
                    <td class="text-center p-1"><b><?php echo $tipo ?></b></td>
                    <td class="text-center p-1"><b><?php echo $fecha_registro ?></b></td>
                  </tr>
                  <?php 
                  $num_item ++;
                }
              }
            } 
            ?>
            <tr>
              <td class="text-center p-1" colspan="3">Total Retirado</td>
              <td class="text-right p-1"><b>$<?php echo number_format($total_retirado,0,'.','.')?></b></td>
              <td class="text-center p-1" colspan="3"></td>
            </tr>
            <tr>
              <td class="text-center p-1" colspan="3">Fijos</td>
              <td class="text-right p-1"><b>$<?php echo number_format($total_fijos,0,'.','.')?></b></td>
              <td class="text-center p-1" colspan="3"></td>
            </tr>
            <tr>
              <td class="text-center p-1" colspan="3">Variables</td>
              <td class="text-right p-1"><b>$<?php echo number_format($total_variables,0,'.','.')?></b></td>
              <td class="text-center p-1" colspan="3"></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>