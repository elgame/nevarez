
<div id="contentAll" class="f-l">
	<form action="<?php echo base_url('panel/nomina/detalle_piloto/'); ?>" method="get" class="frmfiltros corner-all8 btn-gray">
		<label for="ffecha1">Del:</label> 
		<input type="text" name="ffecha1" id="ffecha1" value="<?php echo $this->input->get('ffecha1'); ?>" size="10">
		
		<label for="ffecha2">Al:</label> 
		<input type="text" name="ffecha2" id="ffecha2" value="<?php echo $this->input->get('ffecha2'); ?>" size="10">
		<input type="hidden" name="id" value="<?php echo $_GET['id']?> ">
		<input type="submit" name="enviar" value="Enviar" class="btn-blue corner-all">
	</form>
	
	<div class="w30 m10-all f-l">
		<a href="<?php echo base_url('panel/nomina/pilotos/?'.String::getVarsLink(array('id','msg'))); ?>" class="linksm">
			<img src="<?php echo base_url('application/images/privilegios/atras.png'); ?>" width="16" height="16"> Atras</a>
		<a href="<?php echo base_url('panel/nomina/dp_pdf/?'.String::getVarsLink(array('msg'))); ?>" class="linksm" target="_blank">
			<img src="<?php echo base_url('application/images/privilegios/pdf.png'); ?>" width="20" height="20"> Imprimir</a>
		<a href="<?php echo base_url('panel/nomina/dp_xls/?'.String::getVarsLink(array('msg'))); ?>" class="linksm" target="_blank">
			<img src="<?php echo base_url('application/images/privilegios/xls.png'); ?>" width="20" height="20"> Excel</a>
	</div>
	
	<fieldset class="w60 f-r" style="color: #555; font-size: .9em;">
		<legend>Datos del Piloto</legend>
		<strong>Nombre:</strong> <?php echo $cuentasp['piloto']->nombre; ?> <br>
		<strong>Dirección: </strong> 
				<?php
					$info = $cuentasp['piloto']->calle!=''? $cuentasp['piloto']->calle: '';
					$info .= $cuentasp['piloto']->no_exterior!=''? ' #'.$cuentasp['piloto']->no_exterior: '';
					$info .= $cuentasp['piloto']->no_interior!=''? '-'.$cuentasp['piloto']->no_interior: '';
					$info .= $cuentasp['piloto']->colonia!=''? ', '.$cuentasp['piloto']->colonia: '';
					$info .= "\n".($cuentasp['piloto']->localidad!=''? $cuentasp['piloto']->localidad: '');
					$info .= $cuentasp['piloto']->municipio!=''? ', '.$cuentasp['piloto']->municipio: '';
					$info .= $cuentasp['piloto']->estado!=''? ', '.$cuentasp['piloto']->estado: '';
					echo $info;
				?> <br>
		<strong>Teléfono: </strong> <?php echo $cuentasp['piloto']->telefono; ?> 
		<strong>Email: </strong> <?php echo $cuentasp['piloto']->email; ?>
	</fieldset>
	<div class="clear"></div>
	<div class="f-r">
		<?php echo $this->empleados_model->getLinkPrivSm('nomina/abono_piloto/', $_GET['id'],'','rel="superbox[iframe][550x400]" data-sbox="nomina"','');?>
	</div>
	<table class="tblListados corner-all8">
		<tr class="header btn-gray">
			<td>Fecha</td>
			<td>Avión</td>
			<td>Cantidad</td>
			<td>Descripción</td>
			<td>Vuelos</td>
			<td>Abonos</td>
			<td>Saldo</td>
			<td class="a-c">Opc</td>
		</tr>
		<tr>
			<td></td>
			<td></td>
			<td></td>
			<td>Saldo anterior a <?php echo $_GET['ffecha1']; ?></td>
			<td><?php echo String::formatoNumero(
					(isset($cuentasp['anterior']->total_vuelos)? $cuentasp['anterior']->total_vuelos: 0) ); ?></td>
			<td><?php echo String::formatoNumero(
					(isset($cuentasp['anterior']->total_abonos)? $cuentasp['anterior']->total_abonos: 0) ); ?></td>
			<td><?php echo String::formatoNumero(
					(isset($cuentasp['anterior']->total_saldo)? $cuentasp['anterior']->total_saldo: 0) ); ?></td>
			<td></td>
		</tr>
		<?php
		$total_cargo = 0;
		$total_abono = 0;
		$total_saldo = 0;
		if(isset($cuentasp['anterior']->total_saldo)){ //se suma a los totales saldo anterior
			$total_cargo += $cuentasp['anterior']->total_vuelos;
			$total_abono += $cuentasp['anterior']->total_abonos;
			$total_saldo += $cuentasp['anterior']->total_saldo;
		}
		foreach ($cuentasp['cuentas'] as $cuenta){
			if($cuenta->tipo=='vu'){
				$total_cargo += $cuenta->total_vuelos;
				$total_saldo += $cuenta->total_vuelos;
			}
			elseif($cuenta->tipo=='ab'){
				$total_abono +=	$cuenta->total_abonos;
				$total_saldo -= $cuenta->total_abonos;
			}
		?>
			<tr>
				<td><?php echo $cuenta->fecha; ?></td>
				<td><?php echo $cuenta->matricula; ?></td>
				<td><?php echo $cuenta->tipo=='vu'?$cuenta->cantidad_vuelos:''; ?></td>
				<td><?php echo $cuenta->descripcion; ?></td>
				<td><?php echo String::formatoNumero($cuenta->total_vuelos); ?></td>
				<td><?php echo String::formatoNumero($cuenta->total_abonos); ?></td>
				<td><?php echo String::formatoNumero($total_saldo); ?></td>
				
				<?php if ($cuenta->tipo=='ab') {?>
					<td class="tdsmenu a-c" style="width: 90px;">
						<img alt="opc" src="<?php echo base_url('application/images/privilegios/gear.png'); ?>" width="16" height="16">
						<div class="submenul">
							<p class="corner-bottom8">
								<?php
								echo $this->empleados_model->getLinkPrivSm('nomina/eliminar_abono_piloto/', array('ida'=>$cuenta->id_abono), 
										"msb.confirm('Estas seguro de eliminar el abono?<br>Ya no se podra revertirl el cambio.', this); return false;", '', '&'.String::getVarsLink());
								?>
							</p>
						</div>
					</td>
				<?php }else echo '<td></td>'?>
				
			</tr>
		<?php }?>
			<tr style="background-color:#ccc;font-weight: bold;">
				<td colspan="4" class="a-r">Totales:</td>
				<td><?php echo String::formatoNumero($total_cargo); ?></td>
				<td><?php echo String::formatoNumero($total_abono); ?></td>
				<td><?php echo String::formatoNumero($total_saldo); ?></td>
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
