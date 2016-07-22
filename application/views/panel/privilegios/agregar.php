
<div id="contentAll" class="f-l">
	<form action="<?php echo base_url('panel/privilegios/agregar'); ?>" method="post" class="frm_addmod">
		<div class="frmsec-left w75 f-l">
			<p>
				<label for="dnombre">*Nombre:</label> <br>
				<input type="text" name="dnombre" id="dnombre" value="<?php echo set_value('dnombre'); ?>" size="40" autofocus>
			</p>
			
			<p class="w50 f-l">
				<label for="durl_accion">*Url accion:</label> <br>
				<input type="text" name="durl_accion" id="durl_accion" value="<?php echo set_value('durl_accion'); ?>" size="30">
			</p>
			<p class="w50 f-l">
				<label for="durl_icono">Url icono:</label> <br>
				<input type="text" name="durl_icono" id="durl_icono" value="<?php echo set_value('durl_icono'); ?>" size="30">
			</p>
			<div class="clear"></div>
			
			<p class="w50 f-l">
				<label for="dmostrar_menu">Mostrar menu:</label> <br>
				<input type="checkbox" name="dmostrar_menu" id="dmostrar_menu" value="si" <?php echo set_checkbox('dmostrar_menu', 'si'); ?>>
			</p>
			<p class="w50 f-l">
				<label for="dtarget_blank">Target blank:</label> <br>
				<input type="checkbox" name="dtarget_blank" id="dtarget_blank" value="si" <?php echo set_checkbox('dtarget_blank', 'si'); ?>>
			</p>
			<div class="clear"></div>
		</div>
		
		<div class="frmsec-right w25 f-l b-l">
			<div class="frmbox-r priv corner-right8">
				<?php echo $this->empleados_model->getFrmPrivilegios(0, true, 'radio'); ?>
			</div>
			
			<input type="submit" name="enviar" value="Guardar" class="btn-blue corner-all">
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
});
</script>
<?php }
}?>
<!-- Bloque de alertas -->
