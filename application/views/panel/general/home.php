<div id="contentAll" class="f-l">
	<?php echo $this->empleados_model->getAlertPriv('alertas/productos_bajos/');?>
	<?php echo $this->empleados_model->getAlertPriv('alertas/cumpleaños/');?>
	<?php echo $this->empleados_model->getAlertPriv('alertas/herramientas/');?>
	<?php echo $this->empleados_model->getAlertPriv('alertas/aviones/');?>
	<?php echo $this->empleados_model->getAlertPriv('alertas/pilotos/');?>
	<?php echo $this->empleados_model->getAlertPriv('alertas/cobranza/');?>
	<?php echo $this->empleados_model->getAlertPriv('alertas/cuentas_pagar/');?>
	
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