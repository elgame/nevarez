
	<table class="tblListados corner-all8">
		<tr class="header btn-gray">
			<td>Código</td>
			<td>Nombre</td>
			<td class="a-c">Opc</td>
		</tr>
<?php foreach($familias['familias'] as $famil){ ?>
		<tr class="fams" id="rowfam<?php echo str_replace('.', '-', $famil->id_familia);?>" data-id="<?php echo $famil->id_familia;?>">
			<td><?php echo $famil->codigo; ?></td>
			<td class="data-title"><?php echo $famil->nombre; ?></td>
			<td class="tdsmenu a-c" style="width: 90px;">
				<img alt="opc" src="<?php echo base_url('application/images/privilegios/gear.png'); ?>" width="16" height="16">
				<div class="submenul">
					<p class="corner-bottom8">
						<?php 
						echo $this->empleados_model->getLinkPrivSm('productos/modificar_familia/', $famil->id_familia, '', 
								' rel="superbox[iframe][450x270]" data-sbox="familia"'); 
						echo $this->empleados_model->getLinkPrivSm('productos/desactivar_familia/', $famil->id_familia, 
								"msb.confirm('Estas seguro de Eliminar la Familia? \\n Los productos asociados tambien se eliminaran', this, deleteFamilia); return false;");
						?>
					</p>
				</div>
			</td>
		</tr>
<?php }?>
	</table>
	