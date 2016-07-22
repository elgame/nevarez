
<div id="contentAll" class="f-l">
	<form action="<?php echo base_url('panel/nomina/pilotos'); ?>" method="get" class="frmfiltros corner-all8 btn-gray">
		<label for="ffecha1">Del:</label> 
		<input type="text" name="ffecha1" id="ffecha1" value="<?php echo $this->input->get('ffecha1'); ?>" size="10" readonly>
		
		<label for="ffecha2">Al:</label> 
		<input type="text" name="ffecha2" id="ffecha2" value="<?php echo $this->input->get('ffecha2'); ?>" size="10" readonly>
		
		<input type="submit" name="enviar" value="Enviar" class="btn-blue corner-all">
	</form>
	
	<div class="w100 am-c">
		<a href="<?php echo base_url('panel/nomina/pilotos_pdf/?'.String::getVarsLink()); ?>" class="linksm" target="_blank">
			<img src="<?php echo base_url('application/images/privilegios/pdf.png'); ?>" width="20" height="20"> Imprimir</a>
		<!-- <a href="<?php echo base_url('panel/cuentas_pagar/cxp_xls/?'.String::getVarsLink()); ?>" class="linksm" target="_blank">
			<img src="<?php echo base_url('application/images/privilegios/xls.png'); ?>" width="20" height="20"> Excel</a> -->
	</div>
	
	<table class="tblListados corner-all8">
		<tr class="header btn-gray">
			<td>Piloto</td>
			<td>Vuelos</td>
			<td>Abonos</td>
			<td>Saldo</td>
		</tr>
<?php
$total_saldo = 0; 
foreach($nomina['pilotos'] as $piloto){
	$total_saldo += $piloto->total_saldo;
?>
		<tr>
			<td><a href="<?php echo base_url('panel/nomina/detalle_piloto/').'?id='.$piloto->id_proveedor.'&'.
				String::getVarsLink(array('id_proveedor')); ?>" class="linksm lkzoom"><?php echo $piloto->nombre; ?></a></td>
			<td><?php echo String::formatoNumero($piloto->total_vuelos); ?></td>
			<td><?php echo String::formatoNumero($piloto->total_abonos); ?></td>
			<td><?php echo String::formatoNumero($piloto->total_saldo); ?></td>
		</tr>		
<?php }?>
		<tr style="background-color:#ccc;font-weight: bold;">
			<td></td><td></td>
			<td class="a-r">Total:</td>
			<td><?php echo String::formatoNumero($total_saldo); ?></td>
		</tr>
	</table>
<?php
//Paginacion
$this->pagination->initialize(array(
		'base_url' 			=> base_url($this->uri->uri_string()).'?'.String::getVarsLink(array('pag')).'&',
		'total_rows'		=> $nomina['total_rows'],
		'per_page'			=> $nomina['items_per_page'],
		'cur_page'			=> $nomina['result_page']*$nomina['items_per_page'],
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
