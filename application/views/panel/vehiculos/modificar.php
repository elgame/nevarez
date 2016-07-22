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
	<div class="titulo ajus w100 am-c"><?php echo  $seo['titulo']; ?></div>
	<form action="<?php echo  base_url('panel/vehiculo/modificar/?'.String::getVarsLink(array('msg')));?>" method="post">
		<div class="frmsec-left w90 f-l">
			<p class="f-l w50">
				<label for="fnombre">*Nombre</label><br>
				<input type="text" name="fnombre" id="fnombre" value="<?php echo  (isset($vehiculo[0]->nombre)? $vehiculo[0]->nombre: ''); ?>" size="40" maxlength="40" autofocus>
			</p>
			<p class="f-l w50">
				<label for="fplacas">*Placas</label><br>
				<input type="text" name="fplacas" id="fplacas" value="<?php echo  (isset($vehiculo[0]->placas)? $vehiculo[0]->placas: '') ?>" size="40" maxlength="40">
			</p>
			<p class="f-l w50">
				<label for="fmodelo">Modelo</label><br>
				<input type="text" name="fmodelo" id="fmodelo" value="<?php echo  (isset($vehiculo[0]->modelo)? $vehiculo[0]->modelo: '') ?>" size="40" maxlength="10">
			</p>
			<p class="f-l w50">
				<label for="fnumserie">Número de Serie</label><br>
				<input type=text name="fnumserie" id="fnumserie" value="<?php echo  (isset($vehiculo[0]->numero_serie)? $vehiculo[0]->numero_serie: '') ?>" size="40" maxlength="20">
			</p>
			<p class="f-l w100">
				<label for="fcolor">Color</label><br>
				<input type="text" name="fcolor" id="fcolor" value="<?php echo  (isset($vehiculo[0]->color)? $vehiculo[0]->color: '') ?>" size="40" maxlength="10">
				<input type="submit" name="enviar" value="Guardar" class="btn-blue corner-all f-r">
			</p>
		</div>
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
			if(isset($load)){
				echo 'window.setTimeout(parent.getListadoVehiculos, 1200);';
			}
		?>
});
</script>
<?php }
}?>
<!-- Bloque de alertas -->

</body>
</html>