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
	<div class="frmsec-left w100 f-l">
			<table class="tblListados corner-all8" id="tbl_productos">
				<tr class="header btn-gray">
					<td>Fecha</td>
					<td>Cliente</td>
					<td>Avion</td>
					<td>Piloto</td>
					<td>Vuelos</td>
					<td></td>
				</tr>
				<?php if (isset($cliente['vuelos'])){
						foreach($cliente['vuelos'] as $vuelo){?>
							<tr>
								<td><?php echo  $vuelo->fecha?></td>
								<td><?php echo  $vuelo->clientes?></td>
								<td><?php echo  $vuelo->matricula?></td>
								<td><?php echo  $vuelo->piloto?></td>
								<td><?php echo  $vuelo->total_vuelos?></td>
								<td><input type="checkbox" name="vuelos" value="<?php echo  $_GET['id'].'&'.$vuelo->id_piloto.'&'.$vuelo->id_avion.'&'.$vuelo->fecha.'&'.str_replace('<br>', '-', $vuelo->clientes).'&'.((strpos($vuelo->clientes, '<br>'))? 't' : 'f')?>"></td>
							</tr>
				<?php	 } }?>
			</table>
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