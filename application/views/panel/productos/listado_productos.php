
<form method="get" class="frmfiltros corner-all8 btn-gray" onsubmit="buscarProductos(0); return false;">
	<p class="f-l am-c">
		<label for="fnombre">Nombre:</label> 
		<input type="text" name="fnombre" id="fnombre" value="<?php echo set_value_get('fnombre'); ?>" autofocus>
		<input type="hidden" name="id_familia" id="id_familia" value="<?php echo $this->input->get('id'); ?>">
		
		<input type="submit" name="enviar" value="Buscar" class="btn-blue corner-all">
	</p>	
		
	<?php echo str_replace('<br>', '', $this->empleados_model->getLinkPrivSm('productos/agregar/', '0&familia='.$_GET['id'], '', ' id="link_addprod" rel="superbox[iframe][800x280]"')); ?>
	
	<div class="clear"></div>
</form>

<div id="tbl_productos">
<?php 
	if(isset($tabla_produtos)){
		echo $tabla_produtos;
	}
?>
</div>