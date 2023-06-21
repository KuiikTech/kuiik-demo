<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');

$fecha_h = date('Y-m-d G:i:s');

//$ruta = str_replace('*', ' ', $_GET['ruta']);
$ruta = 'paginas/vistas_pdv/preview_ticket.pdf';
?>
<div class="modal-body">
    <iframe src="<?php echo $ruta ?>?nocache=<?php echo time(); ?>" type="application/pdf" width="100%" height="700px" id="objeto_pdf" name="objeto_pdf">
        <a href="<?php echo $ruta ?>?nocache=<?php echo time(); ?>" id="btn_alt_pdf" target="_BLANCK">Ver PDF</a>
    </iframe>
</div>