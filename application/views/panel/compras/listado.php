
<div id="contentAll" class="f-l">
	<form action="<?php echo base_url('panel/compras'); ?>" method="get" id="frmFiltrosCompras" class="frmfiltros corner-all8 btn-gray">
		<label for="ffecha">Fecha:</label> 
		<input type="text" name="ffecha" id="ffecha" value="<?php echo $this->input->get('ffecha'); ?>" size="10">
		
		<label for="fserie">Serie:</label>
		<input type="text" name="fserie" id="fserie" value="<?php echo $this->input->get('fserie'); ?>" size="10">
		
		<label for="ffolio">Folio:</label>
		<input type="text" name="ffolio" id="ffolio" value="<?php echo $this->input->get('ffolio'); ?>" size="10">
		
		<label for="fproveedor">Proveedor:</label>
		<input type="text" name="fproveedor" id="fproveedor" value="<?php echo $this->input->get('fproveedor'); ?>">
		<input type="hidden" name="fid_proveedor" id="fid_proveedor" value="<?php echo $this->input->get('fid_proveedor'); ?>">
		
		<label for="ftipo">Tipo:</label>
		<select name="ftipo" id="ftipo">
			<option value="">Todo</option>
			<option value="co" <?php echo set_select_get('ftipo', 'co'); ?>>Compra</option>
			<option value="ga" <?php echo set_select_get('ftipo', 'ga'); ?>>Gasto</option>
		</select>
		
		<input type="submit" name="enviar" value="Enviar" class="btn-blue corner-all">
	</form>
	
	<table class="tblListados corner-all8">
		<tr class="header btn-gray">
			<td>Fecha</td>
			<td>Serie</td>
			<td>Folio</td>
			<td>Proveedor</td>
			<td>Status</td>
			<td class="a-c">Opc</td>
		</tr>
<?php foreach($compras['compras'] as $comp){ ?>
		<tr>
			<td><?php echo $comp->fecha; ?></td>
			<td><?php echo $comp->serie; ?></td>
			<td><?php echo $comp->folio; ?></td>
			<td><?php echo $comp->nombre; ?></td>
			<td><?php echo ($comp->status=='p'? 'Pendiente': 'Pagada'); ?></td>
			<td class="tdsmenu a-c" style="width: 90px;">
				<img alt="opc" src="<?php echo base_url('application/images/privilegios/gear.png'); ?>" width="16" height="16">
				<div class="submenul">
					<p class="corner-bottom8">
						<?php 
						echo $this->empleados_model->getLinkPrivSm('compras/ver/', $comp->id_compra, '', '', '&gasto='.$comp->is_gasto.'&tipo='.$comp->tipo);
						if($comp->status == 'p')
							echo $this->empleados_model->getLinkPrivSm('compras/pagar/', $comp->id_compra, '', ' rel="superbox[iframe][600x300]"');
						echo $this->empleados_model->getLinkPrivSm('compras/eliminar/', $comp->id_compra, 
								"msb.confirm('Estas seguro de eliminar la compra? <br>Ya no se podrá revertir el cambio', this); return false;");
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
		'total_rows'		=> $compras['total_rows'],
		'per_page'			=> $compras['items_per_page'],
		'cur_page'			=> $compras['result_page']*$compras['items_per_page'],
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
