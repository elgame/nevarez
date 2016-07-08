<div id="contentAll" class="f-l">
	<form action="<?php echo  base_url('panel/aviones/agregar');?>" method="post" class="frm_addmod">
		<div class="frmsec-left w75 f-l">
			<p class="f-l w50">
				<label for="dcliente" class="f-l">*Cliente</label><br>
				<input type="text" name="dcliente" value="<?php echo $factura['cnombre']?>" size="45" id="dcliente" class="f-l" readonly>
			</p>
			<p class="f-l w50">
				<label for="frfc">*RFC</label><br>
				<input type="text" name="frfc" id="frfc" value="<?php echo $factura['crfc']?>" class="not" size="35" maxlength="13" readonly>
			</p>
			<p class="f-l w50">
				<label for="fcalle">Calle</label><br>
				<input type="text" name="fcalle" id="fcalle" value="<?php echo $factura['ccalle']?>" class="not" size="45" maxlength="60" readonly >
			</p>
			<p class="f-l w25">
				<label for="fno_exterior">No. Ext.</label><br>
				<input type="text" name="fno_exterior" id="fno_exterior" value="<?php echo $factura['cno_exterior']?>" class="not" size="13" maxlength="7" readonly>
			</p>
			<p class="f-l w25">
				<label for=fno_interior>No. Int.</label><br>
				<input type="text" name="fno_interior" id="fno_interior" value="<?php echo $factura['cno_interior']?>" class="not" size="9" maxlength="7" readonly>
			</p>
			<p class="f-l w50">
				<label for="fcolonia">Colonia</label><br>
				<input type="text" name="fcolonia" id="fcolonia" value="<?php echo $factura['ccolonia']?>" class="not" size="35" maxlength="60" readonly>
			</p>
			<p class="f-l w50">
				<label for="flocalidad">Localidad</label><br>
				<input type="text" name="flocalidad" id="flocalidad" value="<?php echo $factura['clocalidad']?>" class="not" size="35" maxlength="45" readonly>
			</p>
			<p class="f-l w50">
				<label for="fmunicipio">Municipio</label><br>
				<input type="text" name="fmunicipio" id="fmunicipio" value="<?php echo $factura['cmunicipio']?>" class="not" size="35" maxlength="45" readonly>
			</p>
			<p class="f-l w50">
				<label for="festado">Estado</label><br>
				<input type="text" name="festado" id="festado" value="<?php echo $factura['cestado']?>" class="not" size="35" maxlength="45" readonly>
			</p>
			<p class="f-l w50">
				<label for="fcp">Codigo Postal</label><br>
				<input type="text" name="fcp" id="fcp" value="<?php echo $factura['ccp']?>" class="not" size="35" maxlength="10" readonly>
			</p>
			<p class="f-l w50">
				<label for="fpais">País</label><br>
				<input type="text" name="fpais" id="fpais" value="<?php echo $factura['cpais']?>" class="not" size="35" maxlength="60" readonly>
			</p>
			<p class="f-l w50">
				<label for="fplazo_credito">*Plazo de crédito:</label> <br>
				<input type="number" name="fplazo_credito" id="fplazo_credito" class="vpositive" 
					value="<?php echo $factura['plazo_credito']?>" size="15" min="0" max="120" readonly> días
			</p>
			<p class="f-l w50">
				<label for="fobservaciones">Observaciones</label><br>
				<textarea id="fobservaciones" name="fobservaciones" rows="5" cols="40"><?php echo $factura['fobservaciones']; ?></textarea>
			</p>
			
			<div class="clear"></div>
			<table class="tblListados corner-all8" id="tbl_tickets">
				<tr class="header btn-gray">
					<td>F. Ticket</td>
					<td>Cantidad</td>
					<td>Unidad</td>
					<td>Descripción</td>
					<td>Precio</td>
					<td>Importe</td>
				</tr>
				<?php foreach($factura['productos'] as $p){?>
					<tr>
						<td><?php echo $p['folio']?></td>
						<td><?php echo $p['cantidad']?></td>
						<td><?php echo $p['unidad']?></td>
						<td><?php echo $p['descripcion']?></td>
						<td><?php echo String::formatoNumero($p['precio_unit'])?></td>
						<td><?php echo String::formatoNumero($p['importe'])?></td>
					</tr>
				<?php }?>						
			</table>
			<table class="tblListados corner-all8 f-r" style="margin-right:1%;text-align:center;">
				<tr>
					<td rowspan="4">
						<label for="cp" class="lbl-gris">Importe con letra</label>
						<textarea name="dttotal_letra" id="dttotal_letra" rows="3" readonly="readonly" style="width:98%;"><?php echo $factura['total_letra']?></textarea>
					</td>
					<td style="text-align:right;">SubTotal</td>
					<td id="ta_subtotal" class="w20 a-r" style="background-color:#ccc;"><?php echo String::formatoNumero($factura['subtotal'])?></td>
				</tr>
				<tr>
					<td style="text-align:right;">IVA</td>
					<td id="ta_iva" class="a-r" style="background-color:#ccc;"><?php echo String::formatoNumero($factura['importe_iva'])?></td>
				</tr>
				<tr>
					<td style="text-align:right;">Retención ISR</td>
					<td id="ta_isr" class="a-r" style="background-color:#ccc;"><?php echo  String::formatoNumero((isset($factura['total_isr']))?$factura['total_isr']:'$0.00'); ?></td>
				</tr>
				<tr>
					<td style="text-align:right;">Total</td>
					<td id="ta_total" class="a-r" style="background-color:#ccc;"><?php echo String::formatoNumero($factura['total']); ?></td>
				</tr>
			</table>
		</div>
		
		<div class="w25 f-l b-l">
		
			<div class="frmsec-right w100 f-l">
				<div class="frmbox-r p5-tb corner-right8">
					<label for="dfecha">*Fecha</label> <br>
					<input type="text" name="dfecha" id="dfecha" value="<?php echo str_ireplace('T', ' ', $factura['fecha_xml'])?>" class="a-c" size="18" readonly>
					<p class="w100 f-l">
							<label for="dcondicion_pago">*Condicion de Pago</label> <br>
							<select name="dcondicion_pago" id="dcondicion_pago" disabled>
								<option value="credito" <?php echo set_select('dcondicion_pago', 'credito', false,$factura['condicion_pago']=='cr'?'credito':'contado')?>>Crédito</option>
								<option value="contado" <?php echo set_select('dcondicion_pago', 'contado', false,$factura['condicion_pago']=='cr'?'credito':'contado')?>>Contado</option>
							</select>
					</p>
					<div class="clear"></div>
				</div>
			</div>
            
			
			<div class="frmsec-right w100 f-l">
				<div class="frmbox-r p5-tb corner-right8">
                	<div class="w100 f-l">
                        <label for="didempresa">Empresa</label> <br>
                        <input type="text" name="didempresa" id="didempresa" value="<?php echo $factura['nombre_fiscal']?>" size="6" readonly style="width:95%; font-size:14px;"  />
                    </div>
                    
					<div class="w100 f-l">
						<label for="dleyendaserie">Leyenda-Serie</label> <br>
						<select name="dleyendaserie" id="dleyendaserie" disabled>
							<option value="">--------------------------------------</option>
							<option value="1" <?php echo set_select('dleyendaserie', '1', false,'1')?>><?php echo $factura['leyenda'].'-'.$factura['serie']?></option>
						</select>
					</div>
					<div class="w50 f-l">
						<label for="dserie">*Serie</label> <br>
						<input type="text" name="dserie" id="dserie" value="<?php echo $factura['serie']?>" class="a-c" size="8" maxlength="30" readonly style="color: red;">
					</div>
					<div class="w50 f-l">
						<label for="dfolio">*Folio</label> <br>
						<input type="text" name="dfolio" id="dfolio" value="<?php echo $factura['folio']?>" class="a-c" size="8" readonly style="color: red;">
					</div>
					<div class="clear"></div>
				</div>
			</div>
			
			<div class="frmsec-right w100 f-l">
				<div class="frmbox-r p5-tb corner-right8">
					<div class="w50 f-l">
						<label for="dano_aprobacion">*Año Aprobación</label> <br>
						<input type="text" name="dano_aprobacion" id="dano_aprobacion" value="<?php echo $factura['ano_aprobacion']?>" class="a-c" size="8" maxlength="4" readonly style="color: blue;">
					</div>
					<div class="w50 f-l">
						<label for="dno_aprobacion">*No Aprobación</label> <br>
						<input type="text" name="dno_aprobacion" id="dno_aprobacion" value="<?php echo $factura['no_aprobacion']?>" class="a-c" size="8" readonly style="color: blue;">
					</div>
					<div class="clear"></div>
				</div>
			</div>
			
			<div class="frmsec-right w100 f-l">
				<div class="frmbox-r p5-tb corner-right8">
					<div class="w100">
						<label for="dno_certificado">*No Certificado</label> <br>
						<input type="text" name="dno_certificado" id="dno_certificado" value="<?php echo $factura['no_certificado']?>" class="a-c not" size="25" maxlength="100" style="color:blue;" readonly>
					</div>
				</div>
			</div>
			
			<div class="frmsec-right w100 f-l">
				<div class="frmbox-r p5-tb corner-right8">
					<div class="w100 f-l">
						<label for="dtipo_comprobante">*Tipo de Comprobante</label> <br>
						<select name="dtipo_comprobante" id="dtipo_comprobante" disabled>					
							<option value="ingreso" <?php echo set_select('dtipo_comprobante', 'ingreso', false,$factura['tipo_comprobante'])?>>Ingreso</option>
						</select>
					</div>
					<div class="clear"></div>
				</div>
			</div>
			
			<div class="frmsec-right w100 f-l">
				<div class="frmbox-r p5-tb corner-right8">
					<label for="dforma_pago">*Forma de Pago</label> <br>
					<select name="dforma_pago" id="dforma_pago" class="a-c" disabled>
						<option value="">--------------------------------------</option>						
						<option value="0" <?php echo set_select('dforma_pago', '0', false,$factura['forma_pago_val'])?>>Pago en una sola exhibición</option>
						<option value="1" <?php echo set_select('dforma_pago', '1', false,$factura['forma_pago_val'])?>>Parcialidad 1 de X</option>
					</select>
					<?php if($factura['forma_pago_val']=="1"){?>
						<div class="w100" id="show_parcialidad">
							<input type="text" name="dforma_pago_parcialidad" id="dforma_pago_parcialidad" value="<?php echo $factura['forma_pago']?>" class="a-c not" size="22" maxlength="80" readonly style="color:red;">
						</div>
					<?php }?>
					<div class="clear"></div>
				</div>
			</div>
			
			<div class="frmsec-right w100 f-l">
				<div class="frmbox-r p5-tb corner-right8">
					<label for="dmetodo_pago">*Metodo de Pago</label> <br>
					<select name="dmetodo_pago" id="dmetodo_pago" class="a-c" disabled>
						<option value="">--------------------------------------</option>						
						<option value="efectivo" <?php echo set_select('dmetodo_pago', 'efectivo', false,$factura['metodo_pago'])?>>Efectivo</option>
						<option value="cheque" <?php echo set_select('dmetodo_pago', 'cheque', false,$factura['metodo_pago'])?>>Cheque</option>
						<option value="tarjeta de crédito o debito" <?php echo set_select('dmetodo_pago', 'tarjeta de crédito o debito', false,$factura['metodo_pago'])?>>Tarjeta de crédito o debito</option>
						<option value="depósito en cuenta" <?php echo set_select('dmetodo_pago', 'depósito en cuenta', false,$factura['metodo_pago'])?>>Depósito en cuenta</option>
						<option value="transferencia" <?php echo set_select('dmetodo_pago', 'transferencia', false,$factura['metodo_pago'])?>>Transferencia</option>
					</select>
					<?php if($factura['metodo_pago']!='efectivo'){?>
						<div class="w100" id="show_pago_digitos" >
							<label for="dmetodo_pago_digitos">*Últimos 4 dígitos</label> <br>
							<input type="text" name="dmetodo_pago_digitos" id="dmetodo_pago_digitos" value="<?php echo $factura['no_cuenta_pago']?>" class="a-c not" size="10" maxlength="4" style="color:green;">
						</div>
					<?php }?>
					<div class="clear"></div>
				</div>
				<input type="button" name="" value="Imprimir" class="btn-blue corner-all" onclick="window.open(base_url+'panel/facturacion/imprimir_pdf/?&id=<?php echo $this->input->get('id')?>', 'Imprimir Nota de Venta', 'left='+((window.innerWidth/2)-240)+',top='+((window.innerHeight/2)-280)+',width=500,height=630,toolbar=0,resizable=0')">			
				<?php if($factura['metodo_pago']!='efectivo'){?>
					<input type="button" name="" value="Actualizar" class="btn-green corner-all" onclick="actualizar('<?php echo $this->input->get('id')?>')">
				<?php }?>
			</div>
		</div>
	</form>	
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