
<div id="contentAll" class="f-l">
	
	<div id="prodic_familias" class="w50 f-l">
		<?php echo str_replace('<br>', '', $this->empleados_model->getLinkPrivSm('productos/agregar_familia/', 0, '', ' rel="superbox[iframe][450x280]"')); ?>
		<div id="conte_tabla">
		<?php
			//imprimimos la tabla de familias
			if(isset($tabla_familias)){
				echo $tabla_familias;
			} 
		?>
		</div>
	</div>
	
	<div id="produc_productos" class="w50 f-l">
		
	</div>
	<div class="clear"></div>

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
