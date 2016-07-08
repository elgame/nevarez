
<div id="contentAll" class="f-l">
	<form action="<?php echo base_url('panel/salidas/ver_todos/'); ?>" method="get" id="frmFiltrosCompras" class="frmfiltros corner-all8 btn-gray">
		<?php /*
			<label for="ffecha">Fecha:</label> 
			<input type="text" name="ffecha" id="ffecha" value="<?php echo $this->input->get('ffecha'); ?>" size="10">|
		*/?>
		<label for="fmostrar">Mostrar:</label>
		<select name="fmostrar" id="fmostrar">
			<option value="">Todo</option>
			<option value="pv" <?php echo set_select_get('fmostrar', 'pv'); ?>>Por Vencer</option>
			<option value="ve" <?php echo set_select_get('fmostrar', 've'); ?>>Vencidas</option>
		</select>
		
		<input type="submit" name="enviar" value="Enviar" class="btn-blue corner-all">
	</form>
	
	<table class="tblListados corner-all8">
		<tr class="header btn-gray">
			<td>Fecha Vencimiento</td>
			<td>Descripción</td>
			<td class="a-c">Opc</td>
		</tr>
		<?php foreach($data['herramientas'] as $herra){ ?>
			<tr <?php echo $herra->style?>>
				<td><?php echo $herra->fecha_vencimiento; ?></td>
				<td><?php echo $herra->descripcion; ?></td>
				<td class="tdsmenu a-c" style="width: 90px;">
					<img alt="opc" src="<?php echo base_url('application/images/privilegios/gear.png'); ?>" width="16" height="16">
					<div class="submenul">
						<p class="corner-bottom8">
							<?php 
							echo $this->empleados_model->getLinkPrivSm('salidas/entregado/', $herra->id_alerta, 
										"msb.confirm('Estas seguro de marcar la herramienta como entregada? <br>Ya no se podrá revertir el cambio', this); return false;", '', '&r=2');
							echo $this->empleados_model->getLinkPrivSm('salidas/extender_plazo/', $herra->id_alerta, 
										"msb.confirm('Estas seguro de extender el tiempo de entrega? <br>Ya no se podrá revertir el cambio', this); return false;", '', '&r=2');
							echo $this->empleados_model->getLinkPrivSm('alertas/eliminar/', $herra->id_alerta,
									"msb.confirm('Estas seguro de eliminar esta alerta? <br>Ya no se podrá revertir el cambio', this); return false;", '', '&r=hp');
							?>
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
		'total_rows'		=> $data['total_rows'],
		'per_page'			=> $data['items_per_page'],
		'cur_page'			=> $data['result_page']*$data['items_per_page'],
		'page_query_string'	=> TRUE,
		'num_links'			=> 1,
		'anchor_class'		=> 'pags corner-all'
));
$pagination = $this->pagination->create_links();
echo '<div class="pagination w100">'.$pagination.'</div>'; 
?>
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
