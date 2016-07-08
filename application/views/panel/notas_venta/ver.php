
<div id="contentAll" class="f-l corner-all">
	<form action="" method="post" class="frm_addmod">
		<div class="frmsec-left w80 f-l b-r">
				<div class="corner-all">
					<p class="w40 f-l">
						<label for="dfolio">Folio:</label> <br>
						<input type="text" name="dfolio" id="dfolio" value="<?php echo  isset($info[1]['cliente_info'][0]->folio) ?$info[1]['cliente_info'][0]->folio:'';?>"readonly>
					</p>
					<div class="clear"></div>
					<p class="w100">
						<label for="dcliente" class="f-l">*Cliente</label><br>
						<input type="text" name="dcliente" value="<?php echo  isset($info[1]['cliente_info'][0]->nombre_fiscal) ? $info[1]['cliente_info'][0]->nombre_fiscal:'';?>" size="35" id="dcliente" class="f-l"  readonly>
						
						<textarea name="dcliente_info" id="dcliente_info" class="m10-l" rows="3" cols="66" ><?php echo  str_replace('<br>',', ',$info[1]['cliente_info'][0]->domicilio)?></textarea>
					</p>
					
					<div class="clear"></div>
					
					<table class="tblListados corner-all8" id="tbl_tickets">
						<tr class="header btn-gray">
							<td>Folio</td>
							<td>Fecha</td>
							<td>Total</td>
						</tr>
						<?php foreach ($info[1]['tickets_info'] as $tickets){?>
							<tr>
								<td><?php echo  $tickets->folio; ?></td>
								<td><?php echo  $tickets->fecha; ?></td>
								<td><?php echo  String::formatoNumero($tickets->total); ?></td>
							</tr>
						<?php }?>
					</table>
					
					<table class="tblListados corner-all8 f-r" style="width:24% !important;margin-right:1%;text-align:center;">
						<tr>
							<td style="text-align:right;">SubTotal</td>
							<td id="ta_subtotal" class="w20 a-r" style="background-color:#ccc;"><?php echo  String::formatoNumero($info[1]['cliente_info'][0]->subtotal); ?></td>
						</tr>
						<tr>
							<td style="text-align:right;">IVA</td>
							<td id="ta_iva" class="a-r" style="background-color:#ccc;"><?php echo  String::formatoNumero($info[1]['cliente_info'][0]->iva); ?></td>
						</tr>
						<tr>
							<td style="text-align:right;">Total</td>
							<td id="ta_total" class="a-r" style="background-color:#ccc;"><?php echo  String::formatoNumero($info[1]['cliente_info'][0]->total); ?></td>
						</tr>
					</table>
					
					
					<div class="clear"></div>
				</div>			
		</div>
		
		<div class="frmsec-right w20 f-l">
			<div class="frmbox-r p5-tb corner-right8">
				<label for="dfecha">*Fecha</label> <br>
				<input type="text" name="dfecha" id="dfecha" value="<?php echo  isset($info[1]['cliente_info'][0]->fecha) ?$info[1]['cliente_info'][0]->fecha:''; ?>" class="a-c" size="15" readonly>
				
				<p class="w100 f-l">
						<label for="dtipo_pago">*Tipo de Pago</label> <br>
						<select name="dtipo_pago" id="dtipo_pago" disabled>
							<option value="credito" <?php echo  set_select('dtipo_pago', 'credito', false,$info[1]['cliente_info'][0]->tipo_pago); ?>>Crédito</option>
							<option value="contado" <?php echo  set_select('dtipo_pago', 'contado', false,$info[1]['cliente_info'][0]->tipo_pago); ?>>Contado</option>
						</select>
				</p>
				<div class="clear"></div>
			</div>
			<input type="button" name="" value="Imprimir" class="btn-blue corner-all" onclick="window.open(base_url+'panel/notas_venta/imprime_nota_venta/?&id=<?php echo  $this->input->get('id')?>', 'Imprimir Nota de Venta', 'left='+((window.innerWidth/2)-240)+',top='+((window.innerHeight/2)-280)+',width=500,height=630,toolbar=0,resizable=0')">
		</div>
	</form>
</div>

