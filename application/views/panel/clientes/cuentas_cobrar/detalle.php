
<div id="contentAll" class="f-l">
	<div class="w15 f-l">
		<a href="<?php echo base_url('panel/cuentas_cobrar/cuenta_cliente/?'.String::getVarsLink(array('id_factura'))); ?>" class="linksm">
			<img src="<?php echo base_url('application/images/privilegios/atras.png'); ?>" width="16" height="16"> Atras</a>
		<a href="<?php echo base_url('panel/cuentas_cobrar/detalle_pdf/?'.String::getVarsLink()); ?>" class="linksm" target="_blank">
			<img src="<?php echo base_url('application/images/privilegios/pdf.png'); ?>" width="20" height="20"> Imprimir</a>
	</div>
	
	<fieldset class="w40 f-r" style="color: #555; font-size: .9em;">
		<legend>Datos de<?php echo ($_GET['tipo']=='f')?' la factura':'l ticket' ?></legend>
		<strong>Fecha:</strong> <?php echo $cuentasp['cobro'][0]->fecha; ?> <br>
		<strong>Serie:</strong> <?php echo $cuentasp['cobro'][0]->serie; ?> <br>
		<strong>Folio:</strong> <?php echo $cuentasp['cobro'][0]->folio; ?> <br>
		<strong>Condicion pago: </strong> <?php echo $cuentasp['cobro'][0]->condicion_pago=='co'? 'Contado': 'Credito'; ?> 
		<strong>Plazo credito: </strong> <?php echo $cuentasp['cobro'][0]->condicion_pago=='co'? 0: $cuentasp['cobro'][0]->plazo_credito; ?> <br>
		<strong>Estado:</strong> <span id="inf_fact_estado"></span>
	</fieldset>
	
	<fieldset class="w40 f-r" style="color: #555; font-size: .9em;">
		<legend>Datos del cliente</legend>
		<strong>Nombre:</strong> <?php echo $cuentasp['cliente']->nombre_fiscal; ?> <br>
		<strong>Dirección: </strong> 
				<?php
					$info = $cuentasp['cliente']->calle!=''? $cuentasp['cliente']->calle: '';
					$info .= $cuentasp['cliente']->no_exterior!=''? ' #'.$cuentasp['cliente']->no_exterior: '';
					$info .= $cuentasp['cliente']->no_interior!=''? '-'.$cuentasp['cliente']->no_interior: '';
					$info .= $cuentasp['cliente']->colonia!=''? ', '.$cuentasp['cliente']->colonia: '';
					$info .= "\n".($cuentasp['cliente']->localidad!=''? $cuentasp['cliente']->localidad: '');
					$info .= $cuentasp['cliente']->municipio!=''? ', '.$cuentasp['cliente']->municipio: '';
					$info .= $cuentasp['cliente']->estado!=''? ', '.$cuentasp['cliente']->estado: '';
					echo $info;
				?> <br>
		<strong>Teléfono: </strong> <?php echo $cuentasp['cliente']->telefono; ?> 
		<strong>Email: </strong> <?php echo $cuentasp['cliente']->email; ?>
	</fieldset>
	<div class="clear"></div>
	
	<div class="w40 f-r a-r" style="font-size: 1.1em;">
		<?php
		$status = (isset($cuentasp['cobro'][0]->status)? $cuentasp['cobro'][0]->status: 'pa');

		if($status == 'p'){
			$controler = ($_GET['tipo'] == 'f')?'facturacion':'tickets';
			$tien = $this->empleados_model->getLinkPrivSm($controler.'/pagar/', $this->input->get('id'), '', ' rel="superbox[iframe][600x400]"');
			if($tien!='')
				echo '<a href="'.base_url('panel/'.$controler.'/pagar/?id='.$this->input->get('id').'&tipo=abono').'" class="linksm" rel="superbox[iframe][600x400]">
				<img src="'.base_url('application/images/privilegios/add.png').'" width="10" height="10"> Abonar</a>';
			echo $tien;
		}
		?>
	</div>
	
	<table class="tblListados corner-all8">
		<tr>
			<td colspan="2"></td>
			<td colspan="3" class="a-c"> <strong>Total: <?php echo String::formatoNumero(
					(isset($cuentasp['cobro'][0]->total)? $cuentasp['cobro'][0]->total: 0) ); ?></strong></td>
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
$total_saldo = $cuentasp['cobro'][0]->total;
foreach($cuentasp['abonos'] as $cuenta){
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
			if ($_GET['tipo'] == 't')
			{
				echo $this->empleados_model->getLinkPrivSm('tickets/eliminar_abono/', array('ida' => $cuenta->id_abono),
					"msb.confirm('Estas seguro de eliminar el abono?', this); return false;", '', '&'.String::getVarsLink(array('msg')));
			}
			elseif ($_GET['tipo'] == 'f')
			{
				echo $this->empleados_model->getLinkPrivSm('facturacion/eliminar_abono/', array('ida' => $cuenta->id_abono),
					"msb.confirm('Estas seguro de eliminar el abono?', this); return false;", '', '&'.String::getVarsLink(array('msg')));	
			}
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
