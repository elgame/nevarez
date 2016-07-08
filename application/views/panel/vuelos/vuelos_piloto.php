<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<title><?php echo $seo['titulo'];?></title>
	
<?php
	if(isset($this->carabiner)){
		$this->carabiner->display('css');
		$this->carabiner->display('js');
	}
?>
<script type="text/javascript" charset="UTF-8">
	var base_url = "<?php echo base_url();?>",
	opcmenu_active = '<?php echo isset($opcmenu_active)? $opcmenu_active: 0;?>';
</script>
</head>
<body>
<div>
	<div class="titulo ajus w100 am-c"><?php echo  $seo['titulo']; ?></div>
	<form action="<?php echo base_url('panel/vuelos/vuelos_piloto/'); ?>" method="get" class="frmfiltros corner-all8 btn-gray">
		<label for="ffecha_ini">De</label> 
		<input type="text" name="ffecha_ini" id="ffecha_ini" value="<?php echo set_value_get('ffecha_ini'); ?>" class="a-c">
		
		<label for="ffecha_fin">A</label> 
		<input type="text" name="ffecha_fin" id="ffecha_fin" value="<?php echo set_value_get('ffecha_fin'); ?>" class="a-c">
		
		<input type="hidden" name="id" value="<?php echo set_value_get("id")?>">
		
		<input type="submit" name="enviar" value="Enviar" class="btn-blue corner-all">
	</form>
	<div class="frmsec-left w100 f-l">
			<table class="tblListados corner-all8" id="tbl_productos">
				<tr class="header btn-gray">
					<td>Fecha</td>
					<td>Piloto</td>
					<td>Avion</td>
					<td>Cliente</td>
					<td>Vuelos</td>
					<td></td>
				</tr>
				<?php if (isset($piloto['vuelos'])){
						foreach($piloto['vuelos'] as $vuelo){?>
							<tr>
								<td><?php echo $vuelo->fecha?></td>
								<td><?php echo $vuelo->piloto?></td>
								<td><?php echo $vuelo->matricula?></td>
								<td><?php echo $vuelo->clientes?></td>
								<td><?php echo $vuelo->total_vuelos?></td>
								<td><input type="checkbox" name="vuelos" value="<?php echo $vuelo->id_vuelo?>"></td>
							</tr>
				<?php	}}?>
			</table>
			<?php 
			//Paginacion
			$this->pagination->initialize(array(
					'base_url' 			=> base_url($this->uri->uri_string()).'?'.String::getVarsLink(array('pag')).'&',
					'total_rows'		=> $piloto['total_rows'],
					'per_page'			=> $piloto['items_per_page'],
					'cur_page'			=> $piloto['result_page']*$piloto['items_per_page'],
					'page_query_string'	=> TRUE,
					'num_links'			=> 1,
					'anchor_class'		=> 'pags corner-all'
			));
			$pagination = $this->pagination->create_links();
			echo '<div class="pagination w100">'.$pagination.'</div>'; 
			?>
			<input type="button" name="enviar" value="Cargar" class="btn-blue corner-all f-r" id="CgrVuelos">
	</div>
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

</body>
</html>