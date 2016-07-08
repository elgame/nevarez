<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<title><?php echo $seo['titulo'];?></title>
	
<?php
	if(isset($this->carabiner)){
		$this->carabiner->display('css');
		$this->carabiner->display('js');
	}
?>
<script type="text/javascript" charset="UTF-8">
	var base_url = "<?php echo base_url();?>",
	opcmenu_active = '<?php echo isset($opcmenu_active)? $opcmenu_active: 0;?>';
</script>
</head>
<body>

<form action="<?php echo base_url('panel/vuelos/rv_pdf')?>" id="frmreporte" class="frm_addmod f-l w20" method="get" target="ifrmReporte">
	<fieldset>
		<legend>Rango de fechas</legend>
		<label for="dfecha1">*Inicio:</label> <br>
		<input type="date" name="dfecha1" id="dfecha1" class="inp-fil" value="<?php echo set_value('dfecha1', $_POST['dfecha1']); ?>"> <br>
		<label for="dfecha2">*Fin:</label> <br>
		<input type="date" name="dfecha2" id="dfecha2" class="inp-fil" value="<?php echo set_value('dfecha2', $_POST['dfecha2']); ?>">
	</fieldset>
	<br>
	<fieldset>
		<legend>Cliente</legend>
		<p class="w100">
			<input type="text" name="dcliente" value="" size="27" id="dcliente" class="f-l" autofocus>
			<input type="hidden" name="did_cliente" value="" id="did_cliente">
		</p>
	</fieldset>
	 <br>
	<fieldset>
		<legend>Piloto</legend>
		<p class="w100">
			<input type="text" name="dproveedor" id="dproveedor" class="f-l" value="<?php echo set_value('dproveedor'); ?>" size="27">
			<input type="hidden" name="did_proveedor" id="did_proveedor" value="<?php echo set_value('did_proveedor'); ?>">
		</p>
	</fieldset>
	<p class="a-c">
		<input type="submit" name="submit" class="btn-green corner-all" value="Mostrar">
	<p>
</form>

<iframe name="ifrmReporte" id="iframe-reporte" class="f-r w80" src="<?php echo base_url('panel/vuelos/rv_pdf')?>">Reporte</iframe>

</body>
</html>