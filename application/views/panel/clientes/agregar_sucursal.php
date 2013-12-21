
<div id="contentAll" class="f-l">
	<form action="<?php echo base_url('panel/clientes/agregar_sucursal/?'.String::getVarsLink(array('msg'))); ?>" method="post" class="frm_addmod">
		<div class="frmsec-left w100 f-l">
			<div id="frmsec-acordion">
				<?php echo $html_form; ?>
			</div>
			
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
