
<h3 class="frmsec-acordion"><a href="#">Información Facturación</a></h3>
<div>
	<p class="w50 f-l">
		<label for="dnombre_fiscal">*Nombre Fiscal: <span class="label_frm_msg">Requerido para facturar</span></label> <br>
		<input type="text" name="dnombre_fiscal" id="dnombre_fiscal" value="<?php echo set_value('dnombre_fiscal'); ?>" size="40" maxlength="130" autofocus>
	</p>
	<p class="w30 f-l">
		<label for="drfc">RFC: <span class="label_frm_msg">Requerido para facturar</span></label> <br>
		<input type="text" name="drfc" id="drfc" value="<?php echo set_value('drfc'); ?>" size="20" maxlength="13">
	</p>
	<div class="clear"></div>
	
	<p class="w40 f-l">
		<label for="dcalle">Calle: <span class="label_frm_msg">Requerido para facturar</span></label> <br>
		<input type="text" name="dcalle" id="dcalle" value="<?php echo set_value('dcalle'); ?>" size="30" maxlength="60">
	</p>
	<p class="w30 f-l">
		<label for="dno_exterior">No exterior: <span class="label_frm_msg">Requerido para facturar</span></label> <br>
		<input type="text" name="dno_exterior" id="dno_exterior" value="<?php echo set_value('dno_exterior'); ?>" size="20" maxlength="7">
	</p>
	<p class="w30 f-l">
		<label for="dno_interior">No interior:</label> <br>
		<input type="text" name="dno_interior" id="dno_interior" value="<?php echo set_value('dno_interior'); ?>" size="20" maxlength="7">
	</p>
	<div class="clear"></div>
	
	<p class="w50 f-l">
		<label for="dcolonia">Colonia: <span class="label_frm_msg">Requerido para facturar</span></label> <br>
		<input type="text" name="dcolonia" id="dcolonia" value="<?php echo set_value('dcolonia'); ?>" size="30" maxlength="60">
	</p>
	<p class="w50 f-l">
		<label for="dlocalidad">Localidad: <span class="label_frm_msg">Requerido para facturar</span></label> <br>
		<input type="text" name="dlocalidad" id="dlocalidad" value="<?php echo set_value('dlocalidad'); ?>" size="30" maxlength="45">
	</p>
	<div class="clear"></div>
	
	<p class="w50 f-l">
		<label for="dmunicipio">Municipio / Delegación: <span class="label_frm_msg">Requerido para facturar</span></label> <br>
		<input type="text" name="dmunicipio" id="dmunicipio" value="<?php echo set_value('dmunicipio'); ?>" size="30" maxlength="45">
	</p>
	<p class="w50 f-l">
		<label for="destado">Estado: <span class="label_frm_msg">Requerido para facturar</span></label> <br>
		<input type="text" name="destado" id="destado" value="<?php echo set_value('destado'); ?>" size="30" maxlength="45">
	</p>
	<div class="clear"></div>
	
	<p class="w50 f-l">
		<label for="dcp">CP: <span class="label_frm_msg">Requerido para facturar</span></label> <br>
		<input type="text" name="dcp" id="dcp" value="<?php echo set_value('dcp'); ?>" size="20" maxlength="10">
	</p>
	<div class="clear"></div>
	
	<p class="w50 f-l">
		<label for="dtelefono">Teléfono:</label> <br>
		<input type="text" name="dtelefono" id="dtelefono" value="<?php echo set_value('dtelefono'); ?>" size="30" maxlength="15">
	</p>
	<p class="w50 f-l">
		<label for="dcelular">Celular:</label> <br>
		<input type="text" name="dcelular" id="dcelular" value="<?php echo set_value('dcelular'); ?>" size="30" maxlength="20">
	</p>
	<div class="clear"></div>
	
	<p class="w50 f-l">
		<label for="demail">Email:</label> <br>
		<input type="text" name="demail" id="demail" value="<?php echo set_value('demail'); ?>" size="30" maxlength="70">
	</p>
	<p class="w50 f-l">
		<label for="dpag_web">Pag Web:</label> <br>
		<input type="text" name="dpag_web" id="dpag_web" value="<?php echo set_value('dpag_web'); ?>" size="30" maxlength="80">
	</p>
	<div class="clear"></div>
	
	<p class="w50 f-l">
		<label for="dcomentarios">Comentarios:</label> <br>
		<textarea name="dcomentarios" id="dcomentarios" rows="3" cols="40" maxlength="400"><?php echo set_value('dcomentarios'); ?></textarea>
	</p>
	<p class="w30 f-l">
		<label for="dlista_precio">Lista de precio:</label> <br>
		<select name="dlista_precio" id="dlista_precio">
		<?php
			if(is_array($listas)){
				foreach($listas as $itm){
					echo '<option value="'.$itm->id_lista.'" '.set_select('dlista_precio', $itm->id_lista).'>'.$itm->nombre.'</option>';
				}
			}
		?>
		</select>
	</p>
	<div class="clear"></div>
	
	<p class="w50 f-l">
		<label for="drecepcion_facturas">Recepción facturas:</label> <br>
		<select name="drecepcion_facturas" id="drecepcion_facturas">
			<option value="Lunes" <?php echo set_select('drecepcion_facturas', 'Lunes'); ?>>Lunes</option>
			<option value="Martes" <?php echo set_select('drecepcion_facturas', 'Martes'); ?>>Martes</option>
			<option value="Miércoles" <?php echo set_select('drecepcion_facturas', 'Miércoles'); ?>>Miércoles</option>
			<option value="Jueves" <?php echo set_select('drecepcion_facturas', 'Jueves'); ?>>Jueves</option>
			<option value="Viernes" <?php echo set_select('drecepcion_facturas', 'Viernes'); ?>>Viernes</option>
			<option value="Sábado" <?php echo set_select('drecepcion_facturas', 'Sábado'); ?>>Sábado</option>
			<option value="Domingo" <?php echo set_select('drecepcion_facturas', 'Domingo'); ?>>Domingo</option>
		</select>
	</p>
	<p class="w50 f-l">
		<label for="ddias_pago">Dias pago:</label> <br>
		<select name="ddias_pago" id="ddias_pago">
			<option value="Lunes" <?php echo set_select('ddias_pago', 'Lunes'); ?>>Lunes</option>
			<option value="Martes" <?php echo set_select('ddias_pago', 'Martes'); ?>>Martes</option>
			<option value="Miércoles" <?php echo set_select('ddias_pago', 'Miércoles'); ?>>Miércoles</option>
			<option value="Jueves" <?php echo set_select('ddias_pago', 'Jueves'); ?>>Jueves</option>
			<option value="Viernes" <?php echo set_select('ddias_pago', 'Viernes'); ?>>Viernes</option>
			<option value="Sábado" <?php echo set_select('ddias_pago', 'Sábado'); ?>>Sábado</option>
			<option value="Domingo" <?php echo set_select('ddias_pago', 'Domingo'); ?>>Domingo</option>
		</select>
	</p>
	<div class="clear"></div>
	
	<p class="w50">
		<label for="ddias_credito">Dias credito:</label> <br>
		<input type="number" name="ddias_credito" id="ddias_credito" class="vpositive" 
					value="<?php echo set_value('ddias_credito', 0); ?>" size="15" min="0" max="120"> dias
	</p>
	<div class="clear"></div>
	
	<p class="w50 f-l">
		<label for="ddescuento">Descuento:</label> <br>
		<input type="text" name="ddescuento" id="ddescuento" value="<?php echo set_value('ddescuento'); ?>" class="vpositive" size="30" maxlength="3"> %
	</p>
	<p class="w50 f-l">
		<label for="dretencion">Retención ISR:</label> <br>
		<input type="checkbox" value="1" name="dretencion" id="dretencion">
		<?php /*
		<select name="dretencion" id="dretencion">
			<option value="0" <?php echo set_select('dretencion', '0'); ?>>No</option>
			<option value="66.66" <?php echo set_select('dretencion', '66.66'); ?>>2terceras</option>
			<option value="100" <?php echo set_select('dretencion', '100'); ?>>100</option>
		</select> %*/?>
	</p>
	<div class="clear"></div>
</div>

<h3 class="frmsec-acordion"><a href="#">Información Empresa</a></h3>
<div>
	<p>
		<label for="demismos_facturacion">Los mismos de Facturación:</label> <br>
		<input type="checkbox" name="demismos_facturacion" id="demismos_facturacion">
	</p>
	
	<p>
		<label for="denombre">Nombre:</label> <br>
		<input type="text" name="denombre" id="denombre" value="<?php echo set_value('denombre'); ?>" size="40" maxlength="130">
	</p>
	
	<p class="w40 f-l">
		<label for="decalle">Calle:</label> <br>
		<input type="text" name="decalle" id="decalle" value="<?php echo set_value('decalle'); ?>" size="30" maxlength="60">
	</p>
	<p class="w30 f-l">
		<label for="deno_exterior">No exterior:</label> <br>
		<input type="text" name="deno_exterior" id="deno_exterior" value="<?php echo set_value('deno_exterior'); ?>" size="20" maxlength="7">
	</p>
	<p class="w30 f-l">
		<label for="deno_interior">No interior:</label> <br>
		<input type="text" name="deno_interior" id="deno_interior" value="<?php echo set_value('deno_interior'); ?>" size="20" maxlength="7">
	</p>
	<div class="clear"></div>
	
	<p class="w50 f-l">
		<label for="decolonia">Colonia:</label> <br>
		<input type="text" name="decolonia" id="decolonia" value="<?php echo set_value('decolonia'); ?>" size="30" maxlength="60">
	</p>
	<p class="w50 f-l">
		<label for="delocalidad">Localidad:</label> <br>
		<input type="text" name="delocalidad" id="delocalidad" value="<?php echo set_value('delocalidad'); ?>" size="30" maxlength="45">
	</p>
	<div class="clear"></div>
	
	<p class="w50 f-l">
		<label for="demunicipio">Municipio / Delegación:</label> <br>
		<input type="text" name="demunicipio" id="demunicipio" value="<?php echo set_value('demunicipio'); ?>" size="30" maxlength="45">
	</p>
	<p class="w50 f-l">
		<label for="deestado">Estado:</label> <br>
		<input type="text" name="deestado" id="deestado" value="<?php echo set_value('deestado'); ?>" size="30" maxlength="45">
	</p>
	<div class="clear"></div>
	
	<p class="w50 f-l">
		<label for="decp">CP:</label> <br>
		<input type="text" name="decp" id="decp" value="<?php echo set_value('decp'); ?>" size="20" maxlength="10">
	</p>
	<div class="clear"></div>
</div>

<h3><a href="#">Contactos</a></h3>
<div>
	<table class="tblListados corner-all8">
		<tr class="header btn-gray">
			<td>Nombre</td>
			<td>Puesto</td>
			<td>Teléfono</td>
			<td>Extensión</td>
			<td>Celular</td>
			<td>Nextel</td>
			<td>ID Nextel</td>
			<td>Fax</td>
		</tr>
		<tr>
			<td><input type="text" name="dcnombre" value="<?php echo set_value('dcnombre'); ?>" size="30"></td>
			<td><input type="text" name="dcpuesto" value="<?php echo set_value('dcpuesto'); ?>" size="10"></td>
			<td><input type="text" name="dctelefono" value="<?php echo set_value('dctelefono'); ?>" size="14"></td>
			<td><input type="text" name="dcextension" value="<?php echo set_value('dcextension'); ?>" size="10"></td>
			<td><input type="text" name="dccelular" value="<?php echo set_value('dccelular'); ?>" size="14"></td>
			<td><input type="text" name="dcnextel" value="<?php echo set_value('dcnextel'); ?>" size="14"></td>
			<td><input type="text" name="dcnextel_id" value="<?php echo set_value('dcnextel_id'); ?>" size="14"></td>
			<td><input type="text" name="dcfax" value="<?php echo set_value('dcfax'); ?>" size="14"></td>
		</tr>
	</table>
</div>

<style type="text/css">
	.label_frm_msg{
		font-size: 9px;
		color: #FD8A8A;
	}
</style>