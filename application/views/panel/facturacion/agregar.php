<div id="contentAll" class="f-l">
	<form action="<?php echo  base_url('panel/aviones/agregar');?>" method="post" class="frm_addmod">
		<div class="frmsec-left w75 f-l">
			<p class="f-l w50">
				<label for="dcliente" class="f-l">*Cliente</label><br>
				<input type="text" name="dcliente" value="<?php echo set_value('dcliente');?>" size="45" id="dcliente" class="f-l" autofocus>
				<input type="hidden" name="hcliente" value="<?php echo set_value('hcliente');?>" id="hcliente">
			</p>
			<p class="f-l w50">
				<label for="frfc">*RFC</label> <label for="chpublicogener"><input type="checkbox" id="chpublicogener"></label><br>
				<input type="text" name="frfc" id="frfc" value="<?php echo  set_value('frfc') ?>" class="not" size="35" maxlength="13">
			</p>
			<p class="f-l w50">
				<label for="fcalle">Calle</label><br>
				<input type="text" name="fcalle" id="fcalle" value="<?php echo  set_value('fcalle') ?>" class="not" size="45" maxlength="60" >
			</p>
			<p class="f-l w25">
				<label for="fno_exterior">No. Ext.</label><br>
				<input type="text" name="fno_exterior" id="fno_exterior" value="<?php echo  set_value('fno_exterior') ?>" class="not" size="13" maxlength="7">
			</p>
			<p class="f-l w25">
				<label for=fno_interior>No. Int.</label><br>
				<input type="text" name="fno_interior" id="fno_interior" value="<?php echo  set_value('fno_interior') ?>" class="not" size="9" maxlength="7">
			</p>
			<p class="f-l w50">
				<label for="fcolonia">Colonia</label><br>
				<input type="text" name="fcolonia" id="fcolonia" value="<?php echo  set_value('fcolonia') ?>" class="not" size="35" maxlength="60">
			</p>
			<p class="f-l w50">
				<label for="flocalidad">Localidad</label><br>
				<input type="text" name="flocalidad" id="flocalidad" value="<?php echo  set_value('flocalidad') ?>" class="not" size="35" maxlength="45">
			</p>
			<p class="f-l w50">
				<label for="fmunicipio">Municipio</label><br>
				<input type="text" name="fmunicipio" id="fmunicipio" value="<?php echo  set_value('fmunicipio') ?>" class="not" size="35" maxlength="45">
			</p>
			<p class="f-l w50">
				<label for="festado">Estado</label><br>
				<input type="text" name="festado" id="festado" value="<?php echo  set_value('festado') ?>" class="not" size="35" maxlength="45">
			</p>
			<p class="f-l w50">
				<label for="fcp">Codigo Postal</label><br>
				<input type="text" name="fcp" id="fcp" value="<?php echo  set_value('fcp') ?>" class="not" size="35" maxlength="10">
			</p>
			<p class="f-l w50">
				<label for="fpais">País</label><br>
				<input type="text" name="fpais" id="fpais" value="<?php echo  set_value('fpais') ?>" class="not" size="35" maxlength="60">
			</p>
			<p class="f-l w50">
				<label for="fplazo_credito">*Plazo de crédito:</label> <br>
				<input type="number" name="fplazo_credito" id="fplazo_credito" class="vpositive" 
					value="<?php echo set_value('fplazo_credito', 0); ?>" size="15" min="0" max="120"> días
			</p>
			<p class="f-l w50">
				<label for="fobservaciones">Observaciones</label><br>
				<textarea id="fobservaciones" name="fobservaciones" rows="5" cols="40"><?php echo set_value('fobservaciones'); ?></textarea>
			</p>
			<div class="clear"></div>
			<fieldset>
				<legend>Selección de Tickets</legend>
				<p class="f-l w50">
					<label for="dfiltro-cliente" class="f-l">Filtro Cliente</label><br>
					<input type="text" name="dfiltro-cliente" value="<?php echo set_value('dfiltro-cliente');?>" size="45" id="dfiltro-cliente" class="f-l not">
				</p>
				<p class="f-l w50">
					<div class="addv">
						<a href="<?php echo base_url("panel/tickets/tickets_cliente/")?>" id="btnAddTicket" class="linksm" style="margin: 0px;" rel="superbox[iframe][700x500]">
						<img src="<?php echo base_url('application/images/privilegios/add.png'); ?>" width="16" height="16">Agregar Tickets</a>
					</div>
				</p>
			</fieldset>
			
			<div class="clear"></div>
			<table class="tblListados corner-all8" id="tbl_tickets">
				<tr class="header btn-gray">
					<td>Ticket</td>
					<td>Cantidad</td>
					<td>Unidad</td>
					<td>Descripción</td>
					<td>Precio</td>
					<td>Importe</td>
					<td>Opc</td>
				</tr>							
			</table>
			<table class="tblListados corner-all8 f-r" style="margin-right:1%;text-align:center;">
				<tr>
					<td rowspan="4">
						<label for="cp" class="lbl-gris">Importe con letra</label>
						<textarea name="dttotal_letra" id="dttotal_letra" rows="3" readonly="readonly" style="width:98%;"><?php echo set_value('dttotal_letra'); ?></textarea>
					</td>
					<td style="text-align:right;">SubTotal</td>
					<td id="ta_subtotal" class="w20 a-r" style="background-color:#ccc;"><?php echo String::formatoNumero(set_value('dtsubtotal', 0)); ?></td>
				</tr>
				<tr>
					<td style="text-align:right;">IVA</td>
					<td id="ta_iva" class="a-r" style="background-color:#ccc;"><?php echo String::formatoNumero(set_value('dtiva', 0)); ?></td>
				</tr>
				<tr>
					<td style="text-align:right;">Retención ISR</td>
					<td id="ta_isr" class="a-r" style="background-color:#ccc;"><?php echo String::formatoNumero(set_value('dtisr', 0)); ?></td>
				</tr>
				<tr>
					<td style="text-align:right;">Total</td>
					<td id="ta_total" class="a-r" style="background-color:#ccc;"><?php echo String::formatoNumero(set_value('dttotal', 0)); ?></td>
				</tr>
			</table>
		</div>
		
		<div class="w25 f-l b-l">
		
			<div class="frmsec-right w100 f-l">
				<div class="frmbox-r p5-tb corner-right8">
					<label for="dfecha">*Fecha</label> <br>
					<input type="text" name="dfecha" id="dfecha" value="<?php echo (set_value('dfecha')!='') ? set_value('dfecha'): date("Y-m-d H:i:s"); ?>" class="a-c" size="18" readonly>
					<p class="w100 f-l">
							<label for="dcondicion_pago">*Condicion de Pago</label> <br>
							<select name="dcondicion_pago" id="dcondicion_pago">
								<option value="credito" <?php echo set_select('dcondicion_pago', 'credito'); ?>>Crédito</option>
								<option value="contado" <?php echo set_select('dcondicion_pago', 'contado'); ?>>Contado</option>
							</select>
					</p>
					<div class="clear"></div>
				</div>
			</div>
			
			<div class="frmsec-right w100 f-l">
				<div class="frmbox-r p5-tb corner-right8">
                	<div class="w100 f-l">
						<label for="didempresa">Empresa</label> <br>
						<select name="didempresa" id="didempresa" onchange="get_seriesfrom_empresa(this.value);" style="width: 100%;">
							<option value="">---------------------------</option>
							<?php
								foreach($empresas as $row){
									echo '<option value="'.$row->id_empresa.'">'.$row->nombre_fiscal.'</option>';
								}
							?>
						</select>
					</div>
					<div class="w100 f-l">
						<label for="dleyendaserie">Leyenda-Serie</label> <br>
						<select name="dleyendaserie" id="dleyendaserie">
							<option value="">---------------------------</option>
							<?php foreach($series as $s){?>
								<option value="<?php echo  $s->id_serie_folio ?>" <?php echo  set_select('dleyendaserie', $s->id_serie_folio); ?>><?php echo  $s->serie?></option>
							<?php }?>
						</select>
					</div>
					<div class="w50 f-l">
						<label for="dserie">*Serie</label> <br>
						<input type="text" name="dserie" id="dserie" value="<?php echo set_value('dserie') ?>" class="a-c" size="8" maxlength="30" readonly style="color: red;">
					</div>
					<div class="w50 f-l">
						<label for="dfolio">*Folio</label> <br>
						<input type="text" name="dfolio" id="dfolio" value="<?php echo set_value('dfolio') ?>" class="a-c" size="8" readonly style="color: red;">
					</div>
					<div class="clear"></div>
				</div>
			</div>
			
			<div class="frmsec-right w100 f-l">
				<div class="frmbox-r p5-tb corner-right8">
					<div class="w50 f-l">
						<label for="dano_aprobacion">*Año Aprobación</label> <br>
						<input type="text" name="dano_aprobacion" id="dano_aprobacion" value="<?php echo set_value('dano_aprobacion') ?>" class="a-c" size="8" maxlength="4" readonly style="color: blue;">
					</div>
					<div class="w50 f-l">
						<label for="dno_aprobacion">*No Aprobación</label> <br>
						<input type="text" name="dno_aprobacion" id="dno_aprobacion" value="<?php echo set_value('dno_aprobacion') ?>" class="a-c" size="8" readonly style="color: blue;">
					</div>
					<div class="clear"></div>
				</div>
			</div>
			
			<div class="frmsec-right w100 f-l">
				<div class="frmbox-r p5-tb corner-right8">
					<div class="w100">
						<label for="dno_certificado">*No Certificado</label> <br>
						<input type="text" name="dno_certificado" id="dno_certificado" value="<?php echo set_value('dno_certificado') ?>" class="a-c not" size="25" maxlength="100" style="color:blue;" readonly>
					</div>
				</div>
			</div>
			
			<div class="frmsec-right w100 f-l">
				<div class="frmbox-r p5-tb corner-right8">
					<div class="w100 f-l">
						<label for="dtipo_comprobante">*Tipo de Comprobante</label> <br>
						<select name="dtipo_comprobante" id="dtipo_comprobante">
							<option value="ingreso" <?php echo set_select('dtipo_comprobante', 'ingreso'); ?>>Ingreso</option>
						</select>
					</div>
					<div class="clear"></div>
				</div>
			</div>
			
			<div class="frmsec-right w100 f-l">
				<div class="frmbox-r p5-tb corner-right8">
					<label for="dforma_pago">*Forma de Pago</label> <br>
					<select name="dforma_pago" id="dforma_pago" class="a-c">
						<option value="">--------------------------------------</option>
						<option value="0" <?php echo set_select('dforma_pago', '0'); ?>>Pago en una sola exhibición</option>
						<option value="1" <?php echo set_select('dforma_pago', '1'); ?>>Parcialidad 1 de X</option>
					</select>
					<div class="w100" id="show_parcialidad" style="display:none;">
						<input type="text" name="dforma_pago_parcialidad" id="dforma_pago_parcialidad" value="<?php echo set_value('dforma_pago_parcialidad') ?>" class="a-c not" size="22" maxlength="80">
					</div>
					<div class="clear"></div>
				</div>
			</div>
			
			<div class="frmsec-right w100 f-l">
				<div class="frmbox-r p5-tb corner-right8">
					<label for="dmetodo_pago">*Metodo de Pago</label> <br>
					<select name="dmetodo_pago" id="dmetodo_pago" class="a-c">
						<option value="">--------------------------------------</option>
						<option value="efectivo" <?php echo set_select('dmetodo_pago', 'efectivo'); ?>>Efectivo</option>
						<option value="cheque" <?php echo set_select('dmetodo_pago', 'cheque'); ?>>Cheque</option>
						<option value="tarjeta de crédito o debito" <?php echo set_select('dmetodo_pago', 'tarjeta de credito o debito'); ?>>Tarjeta de crédito o debito</option>
						<option value="depósito en cuenta" <?php echo set_select('dmetodo_pago', 'deposito en cuenta'); ?>>Depósito en cuenta</option>
						<option value="transferencia" <?php echo set_select('dmetodo_pago', '"transferencia"'); ?>>Transferencia</option>
						
					</select>
					<div class="w100" id="show_pago_digitos" style="display:none;">
						<label for="dmetodo_pago_digitos">*Últimos 4 dígitos</label> <br>
						<input type="text" name="dmetodo_pago_digitos" id="dmetodo_pago_digitos" value="<?php echo set_value('dmetodo_pago_digitos') ?>" class="a-c not" size="10" maxlength="4" style="color:red;">
					</div>
					<div class="clear"></div>
				</div>
				<input type="button" name="enviar" value="Guardar" class="btn-blue corner-all" id="submit">
				<img src="<?php echo base_url('application/images/loader.gif'); ?>" id="submitLoader" style="display:none;">
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