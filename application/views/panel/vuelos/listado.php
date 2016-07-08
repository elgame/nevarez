
<div id="contentAll" class="f-l">
	<form action="<?php echo base_url('panel/vuelos'); ?>" method="get" class="frmfiltros corner-all8 btn-gray">
		<label for="fcliente">Cliente</label>
		<input type="text" name="fcliente" value="<?php echo $this->input->get('fcliente');?>" size="35" id="fcliente" class="a-c" autofocus>
		<input type="hidden" name="fidcliente" id="fidcliente" value="<?php echo $this->input->get('fidcliente');?>">

		<label for="ffecha_ini">De</label> 
		<input type="text" name="ffecha_ini" id="ffecha_ini" value="<?php echo set_value_get('ffecha_ini'); ?>" class="a-c">
		
		<label for="ffecha_fin">A</label> 
		<input type="text" name="ffecha_fin" id="ffecha_fin" value="<?php echo set_value_get('ffecha_fin'); ?>" class="a-c">
		
		<input type="submit" name="enviar" value="Enviar" class="btn-blue corner-all">
	</form>
	
	<table class="tblListados corner-all8">
		<tr class="header btn-gray">
			<td>Nombre del Cliente</td>
			<td>Piloto</td>
			<td>Avión</td>
			<td>Fecha/Hora Salida</td>
			<td>Hora Llegada</td>
			<td class="a-c">Opc</td>
		</tr>
<?php $ttotal_pag=0; foreach($vuelos['vuelos'] as $vuelo){ 
			$ttotal_pag += $vuelo->precio;
	?>
				<tr class="row-conte" id="<?php echo  $vuelo->id_vuelo?>">
					<td><?php echo  $vuelo->clientes;?></td>
					<td><?php echo  $vuelo->piloto; ?></td>
					<td><?php echo  $vuelo->matricula; ?></td>
					<td><?php echo  substr($vuelo->fecha, 0, 19); ?></td>
					<td>
						<input type="text" class="hora_lleg" name="<?php echo 'vuelo'.$vuelo->id_vuelo ?>" value="<?php echo substr($vuelo->hora_llegada, 11, -6);?>" id="hora_llegada"  class="a-c" size="4" maxlength="5">
					</td>
					<td class="tdsmenu a-c" style="width: 90px;">
						<img alt="opc" src="<?php echo  base_url('application/images/privilegios/gear.png'); ?>" width="16" height="16">
						<div class="submenul">
							<?php if($vuelo->existe=='f'){?>
								<p class="corner-bottom8">
									<?php echo $this->empleados_model->getLinkPrivSm('vuelos/eliminar/', $vuelo->id_vuelo, 
											"msb.confirm('Estas seguro de eliminar el vuelo?', this); return false;");?>
									<?php ?>
								</p>
							<?php }?>
						</div>
					</td>
				</tr>
		<?php }?>
	</table>
<div class="clear"></div>
<table class="tblListados corner-all8 f-r" style="margin-right:1%;text-align:center; width:20% !important;">
	<tr>
		<td style="text-align:right;">Total x Pagina</td>
		<td id="ta_isr" class="a-r" style="background-color:#ccc;"><?php echo $vuelos['tvuelos']; ?></td>
	</tr>
	<tr>
		<td style="text-align:right;">Total</td>
		<td id="ta_total" class="a-r" style="background-color:#ccc;"><?php echo $vuelos['ttotal']; ?></td>
	</tr>
</table>
<div class="clear"></div>
<?php //Paginacion
$this->pagination->initialize(array(
		'base_url' 			=> base_url($this->uri->uri_string()).'?'.String::getVarsLink(array('pag')).'&',
		'total_rows'		=> $vuelos['total_rows'],
		'per_page'			=> $vuelos['items_per_page'],
		'cur_page'			=> $vuelos['result_page']*$vuelos['items_per_page'],
		'page_query_string'	=> TRUE,
		'num_links'			=> 1,
		'anchor_class'		=> 'pags corner-all'
));
$pagination = $this->pagination->create_links();
echo '<div class="pagination w100">'.$pagination.'</div>'; 
?>
</div>

<div id="container" style="display:none">
	<div id="withIcon">
		<a class="ui-notify-close ui-notify-cross" href="#">x</a>
		<div style="float:left;margin:0 10px 0 0"><img src="#{icon}" alt="warning" width="64" height="64"></div>
		<h1>#{title}</h1>
		<p>#{text}</p>
		<div class="clear"></div>
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