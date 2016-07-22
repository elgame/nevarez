<div class="w50 f-l" style="padding:5px 0;">
	<div class="f-l">
		<span class="f-l" style="font-size: 1.1em;font-weight: bold;color:#D80000;margin-left:30px;"> <?php echo $total?> Herramientas Vencidas</span>
		<span class="f-l" style="font-weight: bold;margin-left:10px;">
			<a href="<?php echo base_url('panel/salidas/ver_todos/')?>" style="font-size:.7em;color:#1E70F9;text-decoration: none;" target="_BLANK">Ver Todos</a>
		</span>
	</div>
	<div class="w90 f-l" style="margin-left:30px;">
		<table class="tblListados corner-all8">
			<tr class="header btn-gray">
				<td>Descripción</td>
				<td class="a-c">Opc</td>
			</tr>
			<?php foreach($data['alertas'] as $alerta){ ?>
					<tr <?php echo $alerta->style?>>
						<td><?php echo $alerta->descripcion; ?></td>
						<td class="tdsmenu a-c" style="width: 90px;">
							<img alt="opc" src="<?php echo base_url('application/images/privilegios/gear.png'); ?>" width="16" height="16">
							<div class="submenul">
								<p class="corner-bottom8">
									<?php 
									echo $this->empleados_model->getLinkPrivSm('salidas/entregado/', $alerta->id_alerta, 
												"msb.confirm('Estas seguro de marcar la herramienta como entregada? <br>Ya no se podrá revertir el cambio', this); return false;", '', '&r=1');
									echo $this->empleados_model->getLinkPrivSm('salidas/extender_plazo/', $alerta->id_alerta, 
												"msb.confirm('Estas seguro de extender el tiempo de entrega? <br>Ya no se podrá revertir el cambio', this); return false;", '', '&r=1');
									echo $this->empleados_model->getLinkPrivSm('alertas/eliminar/', $alerta->id_alerta,
											"msb.confirm('Estas seguro de eliminar esta alerta? <br>Ya no se podrá revertir el cambio', this); return false;", '', '&r=h');
									?>
								</p>
							</div>
						</td>
					</tr>
			<?php }?>
		</table>
	</div>
</div>