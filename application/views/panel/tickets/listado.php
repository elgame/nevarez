
<div id="contentAll" class="f-l">
	<form action="<?php echo base_url('panel/tickets'); ?>" method="get" class="frmfiltros corner-all8 btn-gray">
		<label for="fcliente">Cliente</label>
		<input type="text" name="fcliente" value="<?php echo $this->input->get('fcliente');?>" size="35" id="fcliente" class="a-c" autofocus>
		<input type="hidden" name="fidcliente" id="fidcliente" value="<?php echo $this->input->get('fidcliente');?>">
		
		<label for="ffecha_ini">De</label> 
		<input type="text" name="ffecha_ini" id="ffecha_ini" value="<?php echo set_value_get('ffecha_ini'); ?>" class="a-c" readonly>
		
		<label for="ffecha_fin">A</label> 
		<input type="text" name="ffecha_fin" id="ffecha_fin" value="<?php echo set_value_get('ffecha_fin'); ?>" class="a-c" readonly>
		
		<label for="fstatus">Estatus</label>
		<select name="fstatus">
			<option value="todos" <?php echo set_select('fstatus', 'todos', false, $this->input->get('fstatus')); ?>>TODOS</option>
			<option value="pendientes" <?php echo set_select('fstatus', 'pendientes', false, $this->input->get('fstatus')); ?>>PENDIENTES</option>
			<option value="pagados" <?php echo set_select('fstatus', 'pagados', false, $this->input->get('fstatus')); ?>>PAGADOS</option>
		</select>
		
		<input type="submit" name="enviar" value="Enviar" class="btn-blue corner-all">
	</form>
	
	<table class="tblListados corner-all8">
		<tr class="header btn-gray">
			<td>Fecha</td>
			<td>Folio</td>
			<td>Cliente</td>
			<td>Tipo de Pago</td>
			<td>Estatus</td>
			<td class="a-c">Opc</td>
		</tr>
<?php foreach($tickets['tickets'] as $ticket){ ?>
		<tr>
			<td><?php echo $ticket->fecha; ?></td>
			<td><?php echo $ticket->folio; ?></td>
			<td><?php echo $ticket->cliente; ?></td>
			<td><?php echo $ticket->tipo_pago; ?></td>
			<td><?php echo ($ticket->status == 'pa') ? 'Pagado' : 'Pendiente' ?></td>
			<td class="tdsmenu a-c" style="width: 90px;">
				<img alt="opc" src="<?php echo base_url('application/images/privilegios/gear.png'); ?>" width="16" height="16">
				<div class="submenul">
					<p class="corner-bottom8">
						<?php 
						echo $this->empleados_model->getLinkPrivSm('tickets/ver/', $ticket->id_ticket);
						if($ticket->status!='pa' && $ticket->disponible=='t')
							echo $this->empleados_model->getLinkPrivSm('tickets/pagar/', $ticket->id_ticket,'','rel="superbox[iframe][500x330]" data-sbox="ticket"','');
						if($ticket->disponible=='t')
							echo $this->empleados_model->getLinkPrivSm('tickets/cancelar/', $ticket->id_ticket, 
									"msb.confirm('Estas seguro de cancelar el ticket?', this); return false;", '', '&'.String::getVarsLink());
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
		'total_rows'		=> $tickets['total_rows'],
		'per_page'			=> $tickets['items_per_page'],
		'cur_page'			=> $tickets['result_page']*$tickets['items_per_page'],
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
