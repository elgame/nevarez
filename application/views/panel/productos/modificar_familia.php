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
<div>
	<div class="titulo ajus w100 am-c"><?php echo $seo['titulo']; ?></div>
	<form action="<?php echo base_url('panel/productos/modificar_familia/?'.String::getVarsLink(array('msg'))); ?>" method="post" class="frm_addmod">
		
			<p>
				<label for="dcodigo">*Código:</label> <span class="ej-info">Ej. 1, 8, 12, 20</span><br>
				<input type="text" name="dcodigo" id="dcodigo" value="<?php echo (isset($info_familia[0]->codigo)? $info_familia[0]->codigo: ''); ?>" size="5" maxlength="8" autofocus>
			</p>
			
			<p>
				<label for="dnombre">*Nombre:</label> <br>
				<input type="text" name="dnombre" id="dnombre" value="<?php echo (isset($info_familia[0]->nombre)? $info_familia[0]->nombre: ''); ?>" size="40" maxlength="60">
			</p>
			
			<p>
					<label for="dtipo">*Asignado a</label> <br>
					<select name="dtipo" id="dtipo">
						<option value="avion" <?php echo set_select('dtipo','avion',false,(isset($info_familia[0]->tipo)? $info_familia[0]->tipo: '')); ?>>Avión</option>
						<option value="trabajador" <?php echo set_select('dtipo','trabajador',false,(isset($info_familia[0]->tipo)? $info_familia[0]->tipo: '')); ?>>Trabajador</option>
						<option value="vehiculo" <?php echo set_select('dtipo','vehiculo',false,(isset($info_familia[0]->tipo)? $info_familia[0]->tipo: '')); ?>>Vehículo</option>
						<option value="venta" <?php echo set_select('dtipo','venta',false,(isset($info_familia[0]->tipo)? $info_familia[0]->tipo: '')); ?>>Venta</option>
						<option value="ninguno" <?php echo set_select('dtipo','ninguno',false,(isset($info_familia[0]->tipo)? $info_familia[0]->tipo: '')); ?>>Ninguno</option>
					</select>
			</p>
			
			<input type="submit" name="enviar" value="Guardar" class="btn-blue corner-all f-r">
	</form>
</div>


<!-- Bloque de alertas -->
<?php if(isset($frm_errors)){
	if($frm_errors['msg'] != ''){ 
?>
<div id="container" style="display:none">
	<div id="withIcon">
		<a class="ui-notify-close ui-notify-cross" href="#">x</a>
		<div style="float:left;margin:0 10px 0 0"><img src="#{icon}" alt="warning" width="64" height="64"></div>
		<h1>#{title}</h1>
		<p>#{text}</p>
		<div class="clear"></div>
	</div>
</div>
<script type="text/javascript" charset="UTF-8">
$(function(){
	create("withIcon", {
		title: '<?php echo $frm_errors['title']; ?>', 
		text: '<?php echo $frm_errors['msg']; ?>', 
		icon: '<?php echo base_url('application/images/alertas/'.$frm_errors['ico'].'.png'); ?>' });

	<?php 
		if(isset($load_familias)){
			echo 'window.setTimeout(parent.getListaFamilias, 1200);';
		}
	?>
});
</script>
<?php }
}?>
<!-- Bloque de alertas -->

	
	<div class="clear"></div>
</body>
</html>