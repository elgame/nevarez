
<div id="contentAll" class="f-l">
<div class="f-l w100">
	
<form action="<?php echo base_url('panel/facturacion/index_series_folios')?>" method="GET" class="frmfiltros corner-all8 btn-gray">
	
	<label for="fserie">Serie</label>
	<input type="text" name="fserie" value="<?php echo set_value_get('fserie')?>">

	<input type="submit" name="enviar" value="enviar" class="btn-blue corner-all">	
</form>
	
	
<table class="tblListados corner-all8">
		<tr class="header btn-gray">
        	<td>Empresa</td>
			<td>Serie</td>
			<td>No Aprobación</td>
			<td>Folio Inicio</td>
			<td>Folio Fin</td>
			<td class="a-c">Opc</td>
		</tr>

		<?php foreach($datos_s['series'] as $serie){ ?>
				<tr class="row-conte">
                	<td><?php echo $serie->nombre_fiscal;?></td>
					<td><?php echo $serie->serie;?></td>
					<td><?php echo $serie->no_aprobacion; ?></td>
					<td><?php echo $serie->folio_inicio; ?></td>
					<td><?php echo $serie->folio_fin; ?></td>
					<td class="tdsmenu a-c" style="width: 90px;">
						<img alt="opc" src="<?php echo base_url('application/images/privilegios/gear.png'); ?>" width="16" height="16">
						<div class="submenul">
							<p class="corner-bottom8">
								<?php echo $this->empleados_model->getLinkPrivSm('facturacion/modificar_serie_folio/', $serie->id_serie_folio, '', ''); ?>
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
		'total_rows'		=> $datos_s['total_rows'],
		'per_page'			=> $datos_s['items_per_page'],
		'cur_page'			=> $datos_s['result_page']*$datos_s['items_per_page'],
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