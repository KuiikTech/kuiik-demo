<?php 
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME,'spanish');
$fecha_h=date('Y-m-d G:i:s');
$fecha=date('Y-m-d');
require_once "../../clases/conexion.php";
$obj= new conectar();
$conexion=$obj->conexion();
$conexion=$obj->conexion();

$cod_espacio=$_GET['cod_espacio'];

?>
<div class="modal-header text-center">
    <h5 class="modal-title">Ingreso de patrón</h5>
</div>
<div class="modal-body p-3">
    <svg class="patternlock" id="lock" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
        <g class="lock-actives"></g>
        <g class="lock-lines"></g>
        <g class="lock-dots">
            <circle cx="20" cy="20" r="2"/>
            <circle cx="50" cy="20" r="2"/>
            <circle cx="80" cy="20" r="2"/>

            <circle cx="20" cy="50" r="2"/>
            <circle cx="50" cy="50" r="2"/>
            <circle cx="80" cy="50" r="2"/>

            <circle cx="20" cy="80" r="2"/>
            <circle cx="50" cy="80" r="2"/>
            <circle cx="80" cy="80" r="2"/>
        </g>
        <svg>
        </div>
        <script type="text/javascript">
            var lock = new PatternLock("#lock", {
              onPattern: function(patron_ingresado)
              {
                document.getElementById('div_loader').style.display = 'block';
                $('#div_canva_patron').load('paginas/detalles/repetir_patron.php/?cod_espacio=<?php echo $cod_espacio ?>&patron_inicial='+patron_ingresado, cerrar_loader());

                //document.getElementById("input_valor_seguridad").value = patron_ingresado;
                //$('#Modal_ingreso_patron').modal('toggle');
               // document.getElementById("input_valor_seguridad").hidden = false;
           }
       });
   </script>

   <div id="qrcode"></div>
   <script type="text/javascript">
    new QRCode(document.getElementById("qrcode"), "https://www.ribosomatic.com");
</script>