
<div id="contentAll" class="f-l">
<div class="f-l w100">
	
<form action="<?php echo  base_url('panel/vehiculo/')?>" method="GET" class="frmfiltros corner-all8 btn-gray">
	
	<label for="fnombre">Nombre </label>
	<input type="text" name="fnombre" value="<?php echo  set_value_get('fnombre')?>">

	<input type="submit" name="enviar" value="enviar" class="btn-blue corner-all">	
</form>
	<div id="listado">
		<?php if (isset($datos_v['vehiculos'])) echo $lista_vehiculos?>
	</div>
</div>
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
});
</script>
<?php }
}?>
<!-- Bloque de alertas -->