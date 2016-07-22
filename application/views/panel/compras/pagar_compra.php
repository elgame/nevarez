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
	<div class="titulo ajus w100 am-c"><?php echo $seo['titulo']; ?></div>
	<form action="<?php echo base_url('panel/compras/pagar?'.String::getVarsLink()); ?>" method="post" class="frm_addmod">
		
			<p>
				<label for="ffecha1">*Fecha:</label><br>
				<input type="text" name="dfecha" id="ffecha1" value="<?php echo set_value('dfecha', date("Y-m-d")); ?>" size="8" readonly autofocus>
			</p>
			
			<p>
				<label for="dconcepto">*Concepto:</label> <br>
				<input type="text" name="dconcepto" id="dconcepto" value="<?php
					$ff = (isset($compra['info']->serie)? $compra['info']->serie: '').(isset($compra['info']->folio)? $compra['info']->folio: ''); 
					echo set_value('dconcepto', 'Pago total de la compra ('.$ff.')'); ?>" size="48" maxlength="120">
			</p>
			
			<?php /*
			<p class="w50 f-l">
				<label for="dcuenta">Cuenta:</label> <br>
				<select name="dcuenta" id="dcuenta">
				<?php
				foreach($cuentas['cuentas'] as $itm){
					echo '<option value="'.$itm->id_cuenta.'" '.set_select('dcuenta', $itm->id_cuenta).'>'.$itm->nombre.' - '.$itm->numero.'</option>';
				} 
				?>
				</select>
			</p>
			<p class="w50 f-l">
				<label for="dforma_pago">Forma de pago:</label> <br>
				<select name="dforma_pago" id="dforma_pago">
					<option value="efectivo" <?php echo set_select('dforma_pago', 'efectivo'); ?>>Efectivo</option>
					<option value="cheque" <?php echo set_select('dforma_pago', 'cheque'); ?>>Cheque</option>
					<option value="tarjeta" <?php echo set_select('dforma_pago', 'tarjeta'); ?>>Tarjeta</option>
					<option value="transferencia" <?php echo set_select('dforma_pago', 'transferencia'); ?>>Transferencia</option>
					<option value="deposito" <?php echo set_select('dforma_pago', 'deposito'); ?>>Deposito</option>
				</select>
			</p>*/?>
			
			<input type="hidden" name="dmonto" value="<?php echo (isset($compra['info']->total)? $compra['info']->total: ''); ?>" class="vpositive">
			<input type="hidden" name="dtipo" value="sa">
			<input type="hidden" name="dactor" value="<?php echo (isset($prov['info']->nombre)? $prov['info']->nombre: ''); ?>">
			<input type="hidden" name="did_actor" value="<?php echo (isset($compra['info']->id_proveedor)? $compra['info']->id_proveedor: ''); ?>">
			
			<input type="submit" name="enviar" value="Guardar" class="btn-blue corner-all f-r">
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

	<?php 
		if(isset($load_operaciones)){
			echo 'window.setTimeout(parent.recargar, 1200);';
		}
	?>
});
</script>
<?php }
}?>
<!-- Bloque de alertas -->

	
	<div class="clear"></div>
</body>
</html>