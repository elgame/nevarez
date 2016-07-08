
<table class="tblListados corner-all8">
		<tr class="header btn-gray">
			<td>Nombre</td>
			<td>Placas</td>
			<td>Color</td>
			<td>Modelo</td>
			<td>Número Serie</td>
			<td class="a-c">Opc</td>
		</tr>

		<?php foreach($datos_v['vehiculos'] as $vehi){ ?>
				<tr class="row-conte">
					<td><?php echo  $vehi->nombre;?></td>
					<td><?php echo  $vehi->placas; ?></td>
					<td><?php echo  $vehi->color; ?></td>
					<td><?php echo  $vehi->modelo; ?></td>
					<td><?php echo  $vehi->numero_serie; ?></td>
					<td class="tdsmenu a-c" style="width: 90px;">
						<img alt="opc" src="<?php echo  base_url('application/images/privilegios/gear.png'); ?>" width="16" height="16">
						<div class="submenul">
							<p class="corner-bottom8">
								<?php echo  $this->empleados_model->getLinkPrivSm('vehiculo/modificar/', $vehi->id_vehiculo, '', 'rel="superbox[iframe][650x285]" data-sbox="vehiculo"'); ?>
								<?php echo $this->empleados_model->getLinkPrivSm('vehiculo/eliminar/', $vehi->id_vehiculo, 
										"msb.confirm('Estas seguro de eliminar el vehículo?', this); return false;");?>
								<?php ?>
							</p>
						</div>
					</td>
				</tr>
		<?php }?>

</table>