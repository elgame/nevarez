
<div id="contentAll" class="f-l">
	<form action="<?php echo base_url('panel/pilotos'); ?>" method="get" class="frmfiltros corner-all8 btn-gray">
		<label for="fnombre">Nombre:</label> 
		<input type="text" name="fnombre" id="fnombre" value="<?php echo set_value_get('fnombre'); ?>" autofocus>
		
		<?php 
			/*<label for="fstatus">Estado:</label>
			<select name="fstatus" id="fstatus">
			<option value="ac" <?php echo set_select_get('fstatus', 'ac'); ?>>Activos</option>
				<option value="e" <?php echo set_select_get('fstatus', 'e'); ?>>Eliminados</option>
			</select>*/
		?>
		
		<input type="submit" name="enviar" value="Enviar" class="btn-blue corner-all">
	</form>
	
	<table class="tblListados corner-all8">
		<tr class="header btn-gray">
			<td>Nombre</td>
			<td>Telefono</td>
			<td>Email</td>
			<td>Recepción facturas</td>
			<td>Dias pago</td>
			<td class="a-c">Opc</td>
		</tr>
<?php foreach($pilotos['pilotos'] as $piloto){ ?>
		<tr>
			<td><?php echo $piloto->nombre; ?></td>
			<td><?php echo $piloto->telefono; ?></td>
			<td><?php echo $piloto->email; ?></td>
			<td><?php echo $piloto->recepcion_facturas; ?></td>
			<td><?php echo $piloto->dias_pago; ?></td>
			<td class="tdsmenu a-c" style="width: 90px;">
				<img alt="opc" src="<?php echo base_url('application/images/privilegios/gear.png'); ?>" width="16" height="16">
				<div class="submenul">
					<p class="corner-bottom8">
						<?php 
						echo $this->empleados_model->getLinkPrivSm('pilotos/modificar/', $piloto->id_proveedor); 
						echo $this->empleados_model->getLinkPrivSm('pilotos/eliminar/', $piloto->id_proveedor, 
								"msb.confirm('Estas seguro de eliminar al piloto?', this); return false;");
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
		'total_rows'		=> $pilotos['total_rows'],
		'per_page'			=> $pilotos['items_per_page'],
		'cur_page'			=> $pilotos['result_page']*$pilotos['items_per_page'],
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
