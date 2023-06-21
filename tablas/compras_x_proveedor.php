<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');
$fecha_h = date('Y-m-d G:i:s');
$fecha = date('Y-m-d');
require_once "../clases/conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();

$cod_proveedor = $_GET['cod_proveedor'];

if (isset($_GET['fecha_inicial'])) {
  $fecha_inicial = $_GET['fecha_inicial'];
  $fecha_final = $_GET['fecha_final'];
} else {
  $fecha_inicial = $fecha;
  $fecha_final = $fecha;
}

?>

<div class="table-responsive text-dark text-center py-0 px-1">
  <table class="table text-dark table-sm table-striped Data_Table" id="tabla_compras_proveedor" width="100%">
    <thead>
      <tr class="text-center">
        <th width="20px">#</th>
        <th width="20px">Cod</th>
        <th>Proveedor</th>
        <th width="120px">Creador</th>
        <th>Total</th>
        <th>Estado</th>
        <th width="20px"></th>
        <th width="20px"></th>
      </tr>
    </thead>
    <tbody class="overflow-auto">
      <?php
      $busqueda = '%{"codigo":"' . $cod_proveedor . '"%';
      if (isset($_GET['fecha_inicial']))
        $sql = "SELECT `codigo`, `productos`, `proveedor`, `creador`, `estado`, `fecha_registro` FROM `compras` WHERE proveedor LIKE '$busqueda' AND fecha_registro BETWEEN '$fecha_inicial' AND '$fecha_final' order by fecha_registro DESC";
      else
        $sql = "SELECT `codigo`, `productos`, `proveedor`, `creador`, `estado`, `fecha_registro` FROM `compras` WHERE proveedor LIKE '$busqueda' order by fecha_registro DESC";
      $result = mysqli_query($conexion, $sql);

      $num_item = 1;
      $total_costo = 0;

      while ($mostrar = mysqli_fetch_row($result)) {
        $costo_total = 0;
        $codigo = $mostrar[0];
        $verificacion = 1;

        $fecha_registro = date('Y-m-d', strtotime($mostrar[5]));

        if (isset($_GET['fecha'])) {
          if ($_GET['fecha'] != $fecha_registro) {
            $verificacion = 0;
          }
        }

        if ($verificacion == 1) {
          $productos_compra = array();
          if ($mostrar[1] != '')
            $productos_compra = json_decode($mostrar[1], true);
          $proveedor = array();
          if ($mostrar[2] != '')
            $proveedor = json_decode($mostrar[2], true);
          $creador = $mostrar[3];
          $estado = $mostrar[4];

          $fecha_registro = date('d-m-Y h:i A', strtotime($mostrar[5]));

          $sql_e = "SELECT nombre, apellido, rol, foto, color FROM `usuarios` WHERE codigo = '$creador'";
          $result_e = mysqli_query($conexion, $sql_e);
          $ver_e = mysqli_fetch_row($result_e);
          if ($ver_e != null) {
            $nombre_aux = explode(' ', $ver_e[0]);
            $apellido_aux = explode(' ', $ver_e[1]);
            $creador = $nombre_aux[0] . ' ' . $apellido_aux[0];
          }

          foreach ($productos_compra as $i => $item) {
            $cod_producto = $item['codigo'];
            $descripcion = $item['descripcion'];
            $categoria = $item['categoria'];
            $cant_bp = $item['cant_bp'];
            $cant_b1 = $item['cant_b1'];
            $cant_b2 = $item['cant_b2'];

            $marca = $item['marca'];

            $valor_venta = $item['valor_venta'];
            $valor_venta_mayor = $item['valor_venta_mayor'];
            $costo = $item['costo'];

            $editar = 0;
            if ($cant_bp != '')
              $editar = 1;
            if ($costo > 0)
              $costo_total += ($cant_bp + $cant_b1 + $cant_b2) * $costo;
          }

          if ($estado == '')
            $estado_button = 'btn-info';
      ?>
          <tr class="text-dark">
            <td class="text-center p-1"><?php echo $num_item ?></td>
            <td class="text-center p-1"><?php echo str_pad($codigo, 3, "0", STR_PAD_LEFT) ?></td>
            <td class="text-center p-0"><?php echo $proveedor['nombre'] . ' (' . $proveedor['telefono'] . ')' ?></td>
            <td class="text-center p-1 lh-1">
              <?php echo $creador ?>
              <br>
              <small><?php echo $fecha_registro ?></small>
            </td>
            <td class="text-right p-1"><b>$<?php echo number_format($costo_total, 0, '.', '.') ?></b></td>
            <td class="text-center p-1">
              <?php
              if ($estado == '')
                echo '<b class="text-danger">PENDIENTE</b>';
              else if ($estado == 'CRÉDITO')
                echo '<b class="text-warning">CRÉDITO</b>';
              else
                echo '<b>' . $estado . '</b>';
              ?>
            </td>
            <td class="text-center p-1">
              <?php
              if ($estado == '') {
              ?>
                <button class="btn btn-sm btn-info btn-round px-2" onclick="cambiar_estado_compra('PAGADO','<?php echo $codigo ?>')">
                  PAGADO
                </button>
                <button class="btn btn-sm btn-warning btn-round px-2" onclick="cambiar_estado_compra('CRÉDITO','<?php echo $codigo ?>')">
                  CRÉDITO
                </button>
              <?php
              }
              if ($estado == 'CRÉDITO') {
              ?>
                <button class="btn btn-sm btn-info btn-round px-2" onclick="cambiar_estado_compra('PAGADO','<?php echo $codigo ?>')">
                  PAGADO
                </button>
              <?php
              }
              ?>
            </td>
            <td class="text-center p-1" width="50px">
              <button class="btn btn-outline-primary btn-round p-1" onclick="$('#Modal_Ver_compra').modal('show');document.getElementById('div_loader').style.display = 'block';$('#div_ver_compra').load('paginas/detalles/detalles_compra.php/?cod_compra=<?php echo $codigo ?>', function(){cerrar_loader();});">
                <span class="fa fa-search"></span>
              </button>
            </td>
          </tr>
      <?php
          $num_item++;
          $total_costo += $costo_total;
        }
      }
      ?>
    </tbody>
  </table>

  <table class="table table-sm table-hover table-bordered">
    <tr>
      <td class="text-right text-dark">
        <b>TOTAL:</b>
      </td>
      <td class="text-left text-dark">
        <b>$<?php echo number_format($total_costo, 0, '.', '.') ?></b>
      </td>
    </tr>
  </table>
</div>

<script type="text/javascript">
  $(document).ready(function() {
    $('#tabla_compras_proveedor').DataTable({
      columnDefs: [{
          responsivePriority: 2,
          targets: 0
        },
        {
          responsivePriority: 4,
          targets: 1
        },
        {
          responsivePriority: 1,
          targets: 2
        },
        {
          responsivePriority: 3,
          targets: 3
        },
        {
          responsivePriority: 4,
          targets: 5
        },
        {
          responsivePriority: 5,
          targets: 6
        },
        {
          responsivePriority: 2,
          targets: 4
        }
      ]
    });
  });
</script>