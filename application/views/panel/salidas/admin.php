
<div id="contentAll" class="f-l">
	<form action="<?php echo base_url('panel/salidas'); ?>" method="get" id="frmFiltrosCompras" class="frmfiltros corner-all8 btn-gray">
		<label for="ffecha">Fecha:</label> 
		<input type="text" name="ffecha" id="ffecha" value="<?php echo $this->input->get('ffecha'); ?>" size="10">
		
		<label for="ftipo_salida">Tipo:</label>
		<select name="ftipo_salida" id="ftipo_salida">
			<option value="">Todo</option>
			<option value="av" <?php echo set_select_get('ftipo_salida', 'av'); ?>>Avión</option>
			<option value="tr" <?php echo set_select_get('ftipo_salida', 'tr'); ?>>Trabajador</option>
			<option value="ve" <?php echo set_select_get('ftipo_salida', 've'); ?>>Vehículo</option>
			<option value="ni" <?php echo set_select_get('ftipo_salida', 'ni'); ?>>Ninguno</option>
		</select>
		
		<label for="ftipo">Tipo:</label>
		<select name="ftipo" id="ftipo">
			<option value="">Todo</option>
			<option value="sa" <?php echo set_select_get('ftipo', 'sa'); ?>>Salida</option>
			<option value="ba" <?php echo set_select_get('ftipo', 'ba'); ?>>Baja</option>
		</select>
		
		<input type="submit" name="enviar" value="Enviar" class="btn-blue corner-all">
	</form>
	
	<table class="tblListados corner-all8">
		<tr class="header btn-gray">
			<td>Fecha</td>
			<td>Folio</td>
			<td>Tipo Salida</td>
			<td>Tipo</td>
			<td class="a-c">Opc</td>
		</tr>
<?php foreach($salidas['salidas'] as $salida){ ?>
		<tr>
			<td><?php echo $salida->fecha; ?></td>
			<td><?php echo $salida->folio; ?></td>
			<td><?php switch ($salida->tipo_salida) {
						case 'av': echo 'Avión'; break;
						case 'tr': echo 'Trabajador'; break;
						case 've': echo 'Vehículo'; break;
						case 'ni': echo 'Ninguno'; break;
					}?></td>
			<td><?php echo ($salida->status=='ba'? 'Baja': (($salida->status=='sa')?'Salida':'Cancelada')); ?></td>
			<td class="tdsmenu a-c" style="width: 90px;">
				<img alt="opc" src="<?php echo base_url('application/images/privilegios/gear.png'); ?>" width="16" height="16">
				<div class="submenul">
					<p class="corner-bottom8">
						<?php 
						echo $this->empleados_model->getLinkPrivSm('salidas/imprimir/', $salida->id_salida,'', 'target="_BLANK"');
						if($salida->status != 'ca')
							echo $this->empleados_model->getLinkPrivSm('salidas/cancelar/', $salida->id_salida, 
									"msb.confirm('Estas seguro de cancelar la salida? <br>Ya no se podrá revertir el cambio', this); return false;", '', '&'.String::getVarsLink(array('id','msg')));
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
		'total_rows'		=> $salidas['total_rows'],
		'per_page'			=> $salidas['items_per_page'],
		'cur_page'			=> $salidas['result_page']*$salidas['items_per_page'],
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
