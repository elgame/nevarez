
<div id="contentAll" class="f-l">
	<form action="<?php echo base_url('panel/clientes/hacer_sucursal/?'.String::getVarsLink(array('msg'))); ?>" method="post" class="frm_addmod">
		<div class="frmsec-left w100 f-l">
			<p>
				<label for="dnombre_grupo">*Nombre del grupo:</label> <br>
				<input type="text" name="dnombre_grupo" id="dnombre_grupo" 
					value="<?php echo (isset($info['info']->nombre)? $info['info']->nombre: ''); ?>" size="40" maxlength="120">
				<input type="hidden" name="id_cliente" value="<?php echo (isset($info['info']->id_cliente)? $info['info']->id_cliente: ''); ?>">
			</p>
			
			<input type="submit" name="enviar" value="Guardar" class="btn-blue corner-all m10-all f-r">
		</div>
		
	</form>
</div>


<!-- Bloque de alertas -->
<div id="container" style="display:none">
	<div id="withIcon">
		<a class="ui-notify-close ui-notify-cross" href="#">x</a>
		<div style="float:left;margin:0 10px 0 0"><img src="#{icon}" alt="warning" width="64" height="64"></div>
		<h1>#{title}</h1>
		<p>#{text}</p>
		<div class="clear"></div>
	</div>
</div>
<?php if(isset($frm_errors)){
	if($frm_errors['msg'] != ''){ 
?>
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
