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

<form action="" id="frmreporte" class="frm_addmod f-l w20">
	<fieldset>
		<legend>Rango de fechas</legend>
		
		<label for="dfecha1">*Inicio:</label> <br>
		<input type="date" name="dfecha1" id="dfecha1" class="inp-fil" value="<?php echo set_value('dfecha1', $fecha1); ?>"> <br>
		<label for="dfecha2">*Fin:</label> <br>
		<input type="date" name="dfecha2" id="dfecha2" class="inp-fil" value="<?php echo set_value('dfecha2', $fecha2); ?>">
	</fieldset>
	
	<fieldset>
		<legend>Buscar producto</legend>
		
		<label for="txtFecha1" class="lbl-gris">Código:</label><br />
		<input type="text" name="codigo" id="codigo" maxlength="25" /><br />
		
		<label for="txtFecha1" class="lbl-gris">Nombre:</label><br />
		<input type="text" name="nombre" id="nombre" maxlength="25" style="width:90%;" /><br />
		
		<input type="button" name="btnBuscar" value="Buscar" onclick="getProductos();" />
	</fieldset>
        
	<fieldset>
		<legend>Producto:</legend>
		<select name="id_producto" id="productos" size="10" class="inp-fil" style="width:98%; display:block; margin:0 auto;">
		<?php echo $lista_productos; ?>
		</select>
	</fieldset>
	
	<p class="a-c">
		<input type="button" name="mostrar" class="btn-green corner-all" onclick="filtrarReporte('iframe-reporte');" value="Mostrar">
	<p>
</form>

<iframe name="ifrmReporte" id="iframe-reporte" class="f-r w80" 
	src="<?php echo base_url('panel/inventario/ueps_pdf')?>" 
	data-srcbase="<?php echo base_url('panel/inventario/ueps_pdf')?>">Reporte</iframe>

</body>
</html>