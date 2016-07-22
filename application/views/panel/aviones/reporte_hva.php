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

<form action="<?php echo base_url('panel/aviones/pdf_hva')?>" id="frmreporte" class="frm_addmod f-l w20" method="GET" target="ifrmReporte">
	<fieldset>
		<legend>Rango de fechas</legend>
		<label for="dfecha1">*Inicio:</label> <br>
		<input type="date" name="dfecha1" id="dfecha1" class="inp-fil" value="<?php echo set_value('dfecha1', $_GET['dfecha1']); ?>"> <br>
		<label for="dfecha2">*Fin:</label> <br>
		<input type="date" name="dfecha2" id="dfecha2" class="inp-fil" value="<?php echo set_value('dfecha2', $_GET['dfecha2']); ?>">
	</fieldset>
	<p class="a-c">
		<input type="submit" name="submit" class="btn-green corner-all" value="Mostrar">
	<p>
</form>

<iframe name="ifrmReporte" id="iframe-reporte" class="f-r w80" src="<?php echo base_url('panel/aviones/pdf_hva')?>"></iframe>

</body>
</html>