<?php 
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME,'spanish');

$fecha_h=date('Y-m-d G:i:s');

$ruta = str_replace('*', ' ', $_GET['ruta']);
?>
<div class="modal-body">
	<iframe src="<?php echo $ruta ?>?nocache=<?php echo time(); ?>" onload="isLoaded()" type="application/pdf" width="100%" height="700px" id="objeto_pdf_f" name="objeto_pdf_f">
		<a href="<?php echo $ruta ?>?nocache=<?php echo time(); ?>" id="btn_alt_pdf" target="_BLANCK">Ver PDF</a>
	</iframe>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-sm btn-secondary btn-round" data-bs-dismiss="modal">Cerrar</button>
</div>

<?php 
if(isset($_GET['imprimir']))
{ 
	?>
	<script type="text/javascript">

		function isLoaded()
		{
			var pdfFrame = window.frames["objeto_pdf_f"];
			pdfFrame.focus();
			pdfFrame.print();
		}
	</script>
	<?php 
}
?>