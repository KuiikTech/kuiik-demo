<?php 
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME,'spanish');

$fecha_h=date('Y-m-d G:i:s');

$ruta = str_replace('*', ' ', $_GET['ruta']);
?>
<div class="modal-body">
	<iframe src="<?php echo $ruta ?>" onload="var pdfFrame = window.frames['objeto_pdf'];pdfFrame.focus();pdfFrame.print();" type="application/pdf" width="100%" height="700px" id="objeto_pdf_c" name="objeto_pdf_c">
		<a href="<?php echo $ruta ?>" id="btn_alt_pdf" target="_BLANCK">Ver PDF</a>
	</iframe>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-sm btn-secondary btn-round" onclick="$('#Modal_ver_caja').modal('toggle');">Cerrar</button>
</div>

<?php 
if(isset($_GET['imprimir']))
{ 
	?>
	<script type="text/javascript">

	</script>
	<?php 
}
?>