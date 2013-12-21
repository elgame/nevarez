
<div id="contentAll" class="f-l">
<div class="f-l w100">
	
<form action="<?= base_url('panel/aviones/')?>" method="GET" class="frmfiltros corner-all8 btn-gray">
	
	<label for="fmatricula">Matrícula </label>
	<input type="text" name="fmatricula" value="<?php echo set_value_get('fmatricula')?>">

	<input type="submit" name="enviar" value="enviar" class="btn-blue corner-all">	
</form>
	
	
<table class="tblListados corner-all8">
		<tr class="header btn-gray">
			<td>Matrícula</td>
			<td>Modelo</td>
			<td>Tipo</td>
			<td>Venc. Tarjeta</td>
			<td class="a-c">Opc</td>
		</tr>

		<?php foreach($datos_a['aviones'] as $avn){ ?>
				<tr class="row-conte">
					<td><?php echo $avn->matricula;?></td>
					<td><?php echo $avn->modelo; ?></td>
					<td><?php echo $avn->tipo; ?></td>
					<td><?php echo $avn->fecha_vence_tarjeta; ?></td>
					<td class="tdsmenu a-c" style="width: 90px;">
						<img alt="opc" src="<?php echo base_url('application/images/privilegios/gear.png'); ?>" width="16" height="16">
						<div class="submenul">
							<p class="corner-bottom8">
								<?php echo $this->empleados_model->getLinkPrivSm('aviones/modificar/', $avn->id_avion, '', ''); ?>
								<?php echo $this->empleados_model->getLinkPrivSm('aviones/eliminar/', $avn->id_avion, 
										"msb.confirm('Estas seguro de eliminar el Avión?', this); return false;");?>
								<?php ?>
							</p>
						</div>
					</td>
				</tr>
		<?php }?>

</table>
<?php
//Paginacion
$this->pagination->initialize(array(
		'base_url' 			=> base_url($this->uri->uri_string()).'?'.String::getVarsLink(array('pag')).'&',
		'total_rows'		=> $datos_a['total_rows'],
		'per_page'			=> $datos_a['items_per_page'],
		'cur_page'			=> $datos_a['result_page']*$datos_a['items_per_page'],
		'page_query_string'	=> TRUE,
		'num_links'			=> 1,
		'anchor_class'		=> 'pags corner-all'
));
$pagination = $this->pagination->create_links();
echo '<div class="pagination w100">'.$pagination.'</div>'; 
?>

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