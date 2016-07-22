
<div id="contentAll" class="f-l">
	<form action="<?php echo base_url('panel/compras/agregar_gasto_piloto'); ?>" method="post" class="frm_addmod">
		<div class="frmsec-left w80 f-l b-r">
			<input type="hidden" name="dis_gasto" id="dis_gasto" value="t">
			<p class="w50 f-l">
				<label for="dserie">Serie:</label> <span class="ej-info">Ej. A, NV, F, etc</span> <br>
				<input type="text" name="dserie" id="dserie" value="<?php echo set_value('dserie'); ?>" size="15" maxlength="4" autofocus>
			</p>
			<p class="w50 f-l">
				<label for="dfolio">*Folio:</label> <span class="ej-info">Ej. 1, 8, 12, 103, etc</span> <br>
				<input type="number" name="dfolio" id="dfolio" value="<?php echo set_value('dfolio'); ?>" class="vpos-int" size="15" min="0" max="2147483647">
			</p>
			<div class="clear"></div>
			
			<p class="w80">
				<label for="dconcepto">Concepto:</label> <br>
				<textarea name="dconcepto" id="dconcepto" rows="2" cols="70"><?php echo set_value('dconcepto'); ?></textarea>
			</p>
			<div class="clear"></div>
			
			<p class="w100">
				<label for="dproveedor">*Piloto:</label> <br>
				<input type="text" name="dproveedor" id="dproveedor" class="f-l" 
					value="<?php echo set_value('dproveedor'); ?>" size="35">
				<input type="hidden" name="did_proveedor" id="did_proveedor" value="<?php echo set_value('did_proveedor'); ?>">
				
				<textarea name="dproveedor_info" id="dproveedor_info" class="m10-l" rows="3" cols="55" readonly><?php echo set_value('dproveedor_info'); ?></textarea>
			</p>
			<div class="clear"></div>
			
			<div class="addv">
				<a href="javascript:void(0);" id="btnAddVuelo" class="linksm f-r" style="margin: 10px 0 20px 0;" onclick="alerta('Seleccione un Piloto !');">
				<img src="<?php echo base_url('application/images/privilegios/add.png'); ?>" width="16" height="16"> Agregar vuelos</a>
			</div>	
			
			<div class="clear"></div>
					
			<table class="tblListados corner-all8" id="tbl_vuelos">
				<tr class="header btn-gray">
					<td>Cantidad</td>
					<td>Precio Unitario</td>
					<td>Importe</td>
					<td>Opc</td>
				</tr>
			</table>
			
			<table class="tblListados corner-all8 f-r" style="width:24% !important;margin-right:1%;text-align:center;">
				<tr>
					<td style="text-align:right;">SubTotal</td>
					<td id="ta_subtotal" class="w20 a-r" style="background-color:#ccc;"><?php echo String::formatoNumero(set_value('dtsubtotal', 0)); ?></td>
				</tr>
				<tr>
					<td style="text-align:right;">IVA</td>
					<td id="ta_iva" class="a-r" style="background-color:#ccc;"><?php echo String::formatoNumero(set_value('dtiva', 0)); ?></td>
				</tr>
				<tr>
					<td style="text-align:right;">Total</td>
					<td id="ta_total" class="a-r" style="background-color:#ccc;"><?php echo String::formatoNumero(set_value('dttotal', 0)); ?></td>
				</tr>
			</table>
		</div>
		
		<div class="frmsec-right w20 f-l">
			<div class="frmbox-r p5-tb corner-right8">
				<label for="dfecha">*Fecha:</label> <br>
				<input type="date" name="dfecha" id="dfecha" value="<?php echo set_value('dfecha', $fecha); ?>"> <br><br>
				
				<div style="display: none;">
					<label for="dcondicion_pago">*Condición de pago:</label> <br>
					<select name="dcondicion_pago" id="dcondicion_pago">
						<option value="co" <?php echo set_select('dcondicion_pago', 'co'); ?>>Contado</option>
						<option value="cr" <?php echo set_select('dcondicion_pago', 'cr'); ?>>Credito</option>
					</select>
					
					<p id="vplazo_credito" class="<?php echo ($this->input->post('dcondicion_pago')=='cr'? '': 'no-show'); ?>">
						<label for="dplazo_credito">*Plazo de crédito:</label> <br>
						<input type="number" name="dplazo_credito" id="dplazo_credito" class="vpositive"
							value="<?php echo set_value('dplazo_credito', $plazo_credito); ?>" size="15" min="0" max="120"> días
					</p>
				</div>
			</div>
			
			<input type="button" name="enviar" value="Guardar" class="btn-blue corner-all" id="submit">
		</div>
	</form>
</div>


<!-- Bloque de alertas -->
<div id="container" style="display:none">
	<div id="withIcon">
		<a class="ui-notify-close ui-notify-cross" href="#">x</a>
		<div style="float:left;margin:0 10px 0 0"><img src="#{icon}" alt="warning" width="64" height="64"></div>
		<h1>#{title}</h1>
		<p>#{text}</p>
		<div class="clear"></div>
	</div>
</div>
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
