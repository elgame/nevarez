
<div id="contentAll" class="f-l">
	<div class="w15 f-l">
		<a href="<?php echo base_url('panel/cuentas_pagar/cuenta_proveedor/?'.String::getVarsLink(array('id_compra'))); ?>" class="linksm">
			<img src="<?php echo base_url('application/images/privilegios/atras.png'); ?>" width="16" height="16"> Atras</a>
		<a href="<?php echo base_url('panel/cuentas_pagar/detalle_pdf/?'.String::getVarsLink()); ?>" class="linksm" target="_blank">
			<img src="<?php echo base_url('application/images/privilegios/pdf.png'); ?>" width="20" height="20"> Imprimir</a>
	</div>
	
	<fieldset class="w40 f-r" style="color: #555; font-size: .9em;">
		<legend>Datos de la factura</legend>
		<strong>Fecha:</strong> <?php echo $cuentasp['compra']->fecha; ?> <br>
		<strong>Serie:</strong> <?php echo $cuentasp['compra']->serie; ?> 
		<strong>Folio:</strong> <?php echo $cuentasp['compra']->folio; ?> <br>
		<strong>Condicion pago: </strong> <?php echo $cuentasp['compra']->condicion_pago=='co'? 'Contado': 'Credito'; ?> 
		<strong>Plazo credito: </strong> <?php echo $cuentasp['compra']->condicion_pago=='co'? 0: $cuentasp['compra']->plazo_credito; ?> <br>
		<strong>Estado:</strong> <span id="inf_fact_estado"></span>
	</fieldset>
	
	<fieldset class="w40 f-r" style="color: #555; font-size: .9em;">
		<legend>Datos del proveedor</legend>
		<strong>Nombre:</strong> <?php echo $cuentasp['proveedor']->nombre; ?> <br>
		<strong>Dirección: </strong> 
				<?php
					$info = $cuentasp['proveedor']->calle!=''? $cuentasp['proveedor']->calle: '';
					$info .= $cuentasp['proveedor']->no_exterior!=''? ' #'.$cuentasp['proveedor']->no_exterior: '';
					$info .= $cuentasp['proveedor']->no_interior!=''? '-'.$cuentasp['proveedor']->no_interior: '';
					$info .= $cuentasp['proveedor']->colonia!=''? ', '.$cuentasp['proveedor']->colonia: '';
					$info .= "\n".($cuentasp['proveedor']->localidad!=''? $cuentasp['proveedor']->localidad: '');
					$info .= $cuentasp['proveedor']->municipio!=''? ', '.$cuentasp['proveedor']->municipio: '';
					$info .= $cuentasp['proveedor']->estado!=''? ', '.$cuentasp['proveedor']->estado: '';
					echo $info;
				?> <br>
		<strong>Teléfono: </strong> <?php echo $cuentasp['proveedor']->telefono; ?> 
		<strong>Email: </strong> <?php echo $cuentasp['proveedor']->email; ?>
	</fieldset>
	<div class="clear"></div>
	
	<div class="w40 f-r a-r" style="font-size: 1.1em;">
		<?php
		$status = (isset($cuentasp['compra']->status)? $cuentasp['compra']->status: 'pa');
		
		if($status == 'p'){
			$tien = $this->empleados_model->getLinkPrivSm('compras/pagar/', $this->input->get('id_compra'), '', ' rel="superbox[iframe][800x500]"');
			if($tien!='')
				echo '<a href="'.base_url('panel/compras/pagar/?id='.$this->input->get('id_compra').'&tipo=abono').'" class="linksm" rel="superbox[iframe][800x500]">
				<img src="'.base_url('application/images/privilegios/add.png').'" width="10" height="10"> Abonar</a>';
			echo $tien;
		}
		?>
	</div>
	
	<table class="tblListados corner-all8">
		<tr>
			<td colspan="2"></td>
			<td colspan="3" class="a-c"> <strong>Total: <?php echo String::formatoNumero(
					(isset($cuentasp['compra']->total)? $cuentasp['compra']->total: 0) ); ?></strong></td>
		</tr>
		<tr class="header btn-gray">
			<td>Fecha</td>
			<td>Concepto</td>
			<td>Abono</td>
			<td>Saldo</td>
			<td></td>
		</tr>
<?php
$total_abono = 0;
$total_saldo = $cuentasp['compra']->total;
foreach($cuentasp['cuentas'] as $cuenta){
	$total_abono += $cuenta->abono;
	$total_saldo -= $cuenta->abono;
?>
		<tr>
			<td><?php echo $cuenta->fecha; ?></td>
			<td><?php echo $cuenta->concepto; ?></td>
			<td><?php echo String::formatoNumero($cuenta->abono); ?></td>
			<td><?php echo String::formatoNumero($total_saldo); ?></td>
			<td class="tdsmenu a-c" style="width: 70px;">
			<?php
			echo $this->empleados_model->getLinkPrivSm('compras/delete_abono/', $cuenta->id_abono,
					"msb.confirm('Estas seguro de eliminar el abono?', this); return false;", '', '&'.String::getVarsLink());
			?>
			</td>
		</tr>
<?php
} ?>
		<tr style="background-color:#ccc;font-weight: bold;">
			<td colspan="2" class="a-r">Totales:</td>
			<td><?php echo String::formatoNumero($total_abono); ?></td>
			<td id="dtalle_total_saldo"><?php echo String::formatoNumero($total_saldo); ?></td>
			<td></td>
		</tr>
	</table>
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
