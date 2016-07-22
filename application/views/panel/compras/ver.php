
<div id="contentAll" class="f-l">
		
		<span id="view"></span>
		<div class="frmsec-left w80 f-l b-r">
			<a href="javascript:history.back(1);"><img src="<?php echo base_url('application/images/privilegios/atras.png'); ?>" width="16" height="16"></a>
			<div class="clear"></div>
			<p class="w50 f-l">
				<label for="dserie">Serie:</label> <br>
				<input type="text" name="dserie" id="dserie" 
					value="<?php echo (isset($inf['info']->serie)? $inf['info']->serie: ''); ?>" size="15" maxlength="4" autofocus>
			</p>
			<p class="w50 f-l">
				<label for="dfolio">Folio:</label> <br>
				<input type="number" name="dfolio" id="dfolio" 
					value="<?php echo (isset($inf['info']->folio)? $inf['info']->folio: ''); ?>" class="vpos-int" size="15" min="0" max="2147483647">
			</p>
			<div class="clear"></div>
			
			<p class="w80">
				<label for="dconcepto">Concepto:</label> <br>
				<textarea name="dconcepto" id="dconcepto" rows="2" 
					cols="70"><?php echo (isset($inf['info']->concepto)? $inf['info']->concepto: ''); ?></textarea>
			</p>
			<div class="clear"></div>
			
			<p class="w100">
				<label for="dproveedor">Proveedor:</label> <br>
				<input type="text" name="dproveedor" id="dproveedor" class="f-l" 
					value="<?php echo (isset($inf['info']->proveedor)? $inf['info']->proveedor: ''); ?>" size="35">
				<input type="hidden" name="did_proveedor" id="did_proveedor" 
					value="<?php echo (isset($inf['info']->id_proveedor)? $inf['info']->id_proveedor: ''); ?>">
				
				<textarea name="dproveedor_info" id="dproveedor_info" class="m10-l" rows="3" 
					cols="55" readonly><?php echo (isset($inf['info']->proveedor_info)? $inf['info']->proveedor_info: ''); ?></textarea>
			</p>
			<div class="clear"></div>
			
			
<?php 
	if(isset($_GET['gasto'])){
		if($_GET['gasto'] == 'f'){
?>
			<table class="tblListados corner-all8" id="tbl_productos">
				<tr class="header btn-gray">
					<td>Cantidad</td>
					<td>Código</td>
					<td>Nombre</td>
					<td>P. Unitario</td>
					<td>Importe</td>
				</tr>
				
		<?php
		if(isset($inf['productos'])){
			foreach($inf['productos'] as $key => $itm){
				echo '<tr id="trp-'.str_replace('.', '_', $itm->id_producto).'">
						<td>
							<input type="hidden" name="dpid_producto[]" value="'.$itm->id_producto.'">
							<input type="hidden" name="dpcantidad[]" value="'.$itm->cantidad.'">
							<input type="hidden" name="dpprecio_unitario[]" value="'.$itm->precio_unitario.'">
							<input type="hidden" name="dpimporte[]" value="'.$itm->importe.'" class="dpimporte">
							<input type="hidden" name="dptaza_iva[]" value="'.$itm->taza_iva.'">
							<input type="hidden" name="dpimporte_iva[]" value="'.$itm->importe_iva.'" class="dpimporte_iva">
							
							<input type="hidden" name="dpcodigo[]" value="'.$itm->codigo.'">
							<input type="hidden" name="dpnombre[]" value="'.$itm->nombre.'">
							'.$itm->cantidad.'</td>
						<td>'.$itm->codigo.'</td>
						<td>'.$itm->nombre.'</td>
						<td>'.String::formatoNumero($itm->precio_unitario).'</td>
						<td>'.String::formatoNumero($itm->importe).'</td>
					</tr>';
			}
		} 
		?>
			</table>
<?php 
	}
	else if($_GET['gasto'] == 't' && $_GET['tipo'] == 'pi'){
	?>
		<table class="tblListados corner-all8" id="tbl_productos">
			<tr class="header btn-gray">
				<td>Cantidad</td>
				<td>P. Unitario</td>
				<td>Importe</td>
			</tr>
				
		<?php
		if(isset($inf['productos'])){
			foreach($inf['productos'] as $key => $itm){
				echo '<tr id="trp-'.str_replace('.', '_', $itm->id_compra).'">
						<td>
							<input type="hidden" name="dpid_producto[]" value="'.$itm->id_compra.'">
							<input type="hidden" name="dpcantidad[]" value="'.$itm->total_vuelos.'">
							<input type="hidden" name="dpprecio_unitario[]" value="'.$itm->precio_unitario.'">
							<input type="hidden" name="dpimporte[]" value="'.$itm->importe.'" class="dpimporte">
							<input type="hidden" name="dptaza_iva[]" value="'.$itm->taza_iva.'">
							<input type="hidden" name="dpimporte_iva[]" value="'.$itm->importe_iva.'" class="dpimporte_iva">
							'.$itm->total_vuelos.'</td>
						<td>'.String::formatoNumero($itm->precio_unitario).'</td>
						<td>'.String::formatoNumero($itm->importe).'</td>
					</tr>';
			}
		}?>
			</table>
			
<?php } }?>
			<table class="tblListados corner-all8" style="text-align:center;">
				<tr>
					<td rowspan="3">
						<label for="cp" class="lbl-gris">Importe con letra</label>
						<textarea name="dttotal_letra" id="dttotal_letra" rows="3" readonly="readonly" style="width:98%;"><?php echo set_value('dttotal_letra'); ?></textarea>
						<input type="hidden" id="dtsubtotal" name="dtsubtotal" 
							value="<?php echo (isset($inf['info']->subtotal)? $inf['info']->subtotal."asdasd": ''); ?>" />
						<input type="hidden" id="dtiva" name="dtiva" 
							value="<?php echo (isset($inf['info']->importe_iva)? $inf['info']->importe_iva: ''); ?>" />
						<input type="hidden" id="dttotal" name="dttotal" 
							value="<?php echo (isset($inf['info']->total)? $inf['info']->total: ''); ?>">
					</td>
					<td style="text-align:right;">SubTotal</td>
					<td id="ta_subtotal" class="w20 a-r" 
						style="background-color:#ccc;"><?php echo String::formatoNumero((isset($inf['info']->subtotal)? $inf['info']->subtotal: '')); ?></td>
				</tr>
				<tr>
					<td style="text-align:right;">IVA</td>
					<td id="ta_iva" class="a-r" 
						style="background-color:#ccc;"><?php echo String::formatoNumero((isset($inf['info']->importe_iva)? $inf['info']->importe_iva: '')); ?></td>
				</tr>
				<tr>
					<td style="text-align:right;">Total</td>
					<td id="ta_total" class="a-r" 
						style="background-color:#ccc;"><?php echo String::formatoNumero((isset($inf['info']->total)? $inf['info']->total: '')); ?></td>
				</tr>
			</table>
		</div>
		
		<div class="frmsec-right w20 f-l">
			<div class="frmbox-r p5-tb corner-right8">
				<label for="dfecha">*Fecha:</label> <br>
				<input type="date" name="dfecha" id="dfecha" value="<?php echo (isset($inf['info']->fecha)? $inf['info']->fecha: ''); ?>"> <br><br>
				
				<?php if ($_GET['gasto'] != 't' && $_GET['tipo'] != 'pi') {?>
					<label for="dcondicion_pago">*Condición de pago:</label> <br>
					<select name="dcondicion_pago" id="dcondicion_pago">
						<option value="co" <?php echo set_select('dcondicion_pago', 'co', false, 
								(isset($inf['info']->condicion_pago)? $inf['info']->condicion_pago: '')); ?>>Contado</option>
						<option value="cr" <?php echo set_select('dcondicion_pago', 'cr', false, 
								(isset($inf['info']->condicion_pago)? $inf['info']->condicion_pago: '')); ?>>Credito</option>
					</select>
					<?php
					$show = 'no-show';
					if(isset($inf['info']->condicion_pago)){
						$show = ($inf['info']->condicion_pago=='cr'? '': 'no-show');
					}
					?>
					<p id="vplazo_credito" class="<?php echo $show; ?>">
						<label for="dplazo_credito">*Plazo de crédito:</label> <br>
						<input type="number" name="dplazo_credito" id="dplazo_credito" class="vpositive"
							value="<?php echo (isset($inf['info']->proveedor_dias_credito)? $inf['info']->proveedor_dias_credito: ''); ?>" size="15" min="0" max="120"> días
					</p>
				<?php }?>
			</div>
			
			
		</div>
	
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
