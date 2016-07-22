
<div id="contentAll" class="f-l">
	<form action="<?php echo base_url('panel/inventario'); ?>" method="get" id="frmFiltrosCotiza" class="frmfiltros corner-all8 btn-gray">
		<label for="ffamilia">Familias:</label> 
		<select name="ffamilia" id="ffamilia">
	<?php foreach($familias as $fam){ ?>
			<option value="<?php echo $fam->id_familia; ?>" <?php echo set_select_get('ffamilia', $fam->id_familia); ?>><?php echo $fam->nombre; ?></option>
	<?php } ?>
		</select>
				
		<input type="submit" name="enviar" value="Enviar" class="btn-blue corner-all">
	</form>
	
	<form action="<?php echo base_url('panel/inventario/nivelar'); ?>" method="post">
		<input type="hidden" name="id_familia" value="<?php echo $id_familia; ?>">
		
		<table class="tblListados corner-all8">
			<tr class="header btn-gray">
				<td>Nombre</td>
				<td>E. sistema</td>
				<td>E. fisica</td>
				<td>Diferencia</td>
			</tr>
	<?php
		$ver = false; 
		foreach($productos as $prod){
			$ver = true;
		?>
			<tr id="tr_<?php echo str_replace('.', '_', $prod->id_producto); ?>">
				<td><?php echo $prod->nombre; ?>
					<input type="hidden" name="id_producto[]" value="<?php echo $prod->id_producto; ?>">
					<input type="hidden" name="precio_u[]" value="<?php echo $prod->precio_u; ?>">
				</td>
				<td><?php echo $prod->existencia.' '.$prod->abreviatura; ?>
					<input type="hidden" name="es[]" value="<?php echo $prod->existencia; ?>"
						id="es_<?php echo str_replace('.', '_', $prod->id_producto); ?>" >
				</td>
				<td>
					<input type="text" name="ef[]" class="ef_clck vpositive"
						id="ef_<?php echo str_replace('.', '_', $prod->id_producto); ?>" size="15"> 
					<?php echo $prod->abreviatura; ?>
				</td>
				<td>
					<input type="text" name="diferie[]" id="diferie_<?php echo str_replace('.', '_', $prod->id_producto); ?>" size="15" readonly>
					<?php echo $prod->abreviatura; ?>
				</td>
			</tr>
	<?php }?>
		</table>
		
	<?php if($ver){ ?>
		<input type="submit" name="nivelar" value="Nivelar" class="btn-blue corner-all f-r">
	<?php } ?>
	</form>
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
