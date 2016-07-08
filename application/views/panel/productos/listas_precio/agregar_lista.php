
<div id="contentAll" class="f-l">
	<form action="<?php echo base_url('panel/listas_precio/agregar'); ?>" method="post" class="frm_addmod">
			<p>
				<label for="dnombre">*Nombre:</label> <br>
				<input type="text" name="dnombre" id="dnombre" value="<?php echo set_value('dnombre'); ?>" size="40" maxlength="30" autofocus>
			</p>
			
			<p>
				<label for="des_default">Precio publico:</label> <span class="ej-info">Es la lista publica?</span><br>
				<input type="checkbox" name="des_default" id="des_default" value="si" <?php echo set_checkbox('des_default', 'si'); ?>>
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