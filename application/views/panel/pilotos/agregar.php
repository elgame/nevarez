
<div id="contentAll" class="f-l">
	<form action="<?php echo base_url('panel/pilotos/agregar'); ?>" method="post" class="frm_addmod">
		<div class="frmsec-left w75 f-l">
			<div id="frmsec-acordion">
				<h3 class="frmsec-acordion"><a href="#">Información</a></h3>
				<div>
					<p>
						<label for="dnombre">*Nombre:</label> <br>
						<input type="text" name="dnombre" id="dnombre" value="<?php echo set_value('dnombre'); ?>" size="40" maxlength="120" autofocus>
					</p>
					
					<p class="w40 f-l">
						<label for="dcalle">Calle:</label> <br>
						<input type="text" name="dcalle" id="dcalle" value="<?php echo set_value('dcalle'); ?>" size="30" maxlength="60">
					</p>
					<p class="w30 f-l">
						<label for="dno_exterior">No exterior:</label> <br>
						<input type="text" name="dno_exterior" id="dno_exterior" value="<?php echo set_value('dno_exterior'); ?>" size="20" maxlength="7">
					</p>
					<p class="w30 f-l">
						<label for="dno_interior">No interior:</label> <br>
						<input type="text" name="dno_interior" id="dno_interior" value="<?php echo set_value('dno_interior'); ?>" size="20" maxlength="7">
					</p>
					<div class="clear"></div>
					
					<p class="w50 f-l">
						<label for="dcolonia">Colonia:</label> <br>
						<input type="text" name="dcolonia" id="dcolonia" value="<?php echo set_value('dcolonia'); ?>" size="30" maxlength="60">
					</p>
					<p class="w50 f-l">
						<label for="dlocalidad">Localidad:</label> <br>
						<input type="text" name="dlocalidad" id="dlocalidad" value="<?php echo set_value('dlocalidad'); ?>" size="30" maxlength="45">
					</p>
					<div class="clear"></div>
					
					<p class="w50 f-l">
						<label for="dmunicipio">Municipio:</label> <br>
						<input type="text" name="dmunicipio" id="dmunicipio" value="<?php echo set_value('dmunicipio'); ?>" size="30" maxlength="45">
					</p>
					<p class="w50 f-l">
						<label for="destado">Estado:</label> <br>
						<input type="text" name="destado" id="destado" value="<?php echo set_value('destado'); ?>" size="30" maxlength="45">
					</p>
					<div class="clear"></div>
					
					<p class="w50 f-l">
						<label for="dcp">CP:</label> <br>
						<input type="text" name="dcp" id="dcp" value="<?php echo set_value('dcp'); ?>" size="20" maxlength="10">
					</p>
					<p class="w50 f-l">
						<label for="dfecha_nacimiento">Fecha Nacimiento:</label> <br>
						<input type="text" name="dfecha_nacimiento" id="dfecha_nacimiento" value="<?php echo set_value('dfecha_nacimiento'); ?>" size="20" maxlength="10">
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
					
					<p class="w80 f-l">
						<label for="dcomentarios">Comentarios:</label> <br>
						<textarea name="dcomentarios" id="dcomentarios" rows="3" cols="40" maxlength="400"><?php echo set_value('dcomentarios'); ?></textarea>
					</p>
					<div class="clear"></div>
					
					<p class="w50 f-l" style="display: none;">
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
					<p class="w50 f-l" style="display: none;">
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
					
					<p class="w50 f-l" style="display: none;">
						<label for="ddias_credito">Dias credito:</label> <br>
						<select name="ddias_credito" id="ddias_credito">
							<option value="15" <?php echo set_select('ddias_credito', '15'); ?>>15 Dias</option>
							<option value="30" <?php echo set_select('ddias_credito', '30'); ?>>30 Dias</option>
							<option value="60" <?php echo set_select('ddias_credito', '60'); ?>>60 Dias</option>
							<option value="90" <?php echo set_select('ddias_credito', '90'); ?>>90 Dias</option>
						</select>
					</p>
					<p class="w50 f-l">
						<label for="dexp_fact">Expide Factura:</label> <br>
						<select name="dexpide_factura" id="dexpide_factura">
							<option value="1" <?php echo set_select('dexpide_factura', '1'); ?>>Si</option>
							<option value="0" <?php echo set_select('dexpide_factura', '0'); ?>>No</option>
						</select>
					</p>
					<p class="w50 f-l">
						<label for="dprecio_vuelo">Precio por Vuelo:</label> <br>
						<input type="text" name="dprecio_vuelo" id="dprecio_vuelo" class="vpositive" value="<?php echo set_value('dprecio_vuelo'); ?>" size="10">
					</p>
					<div class="clear"></div>
				</div>
				
				<h3><a href="#">Contactos</a></h3>
				<div>
					<table class="tblListados corner-all8">
						<tr class="header btn-gray">
							<td>Nombre</td>
							<td>Domicilio</td>
							<td>Municipio</td>
							<td>Estado</td>
							<td>Teléfono</td>
							<td>Celular</td>
						</tr>
						<tr>
							<td><input type="text" name="dcnombre" value="<?php echo set_value('dcnombre'); ?>" size="28"></td>
							<td><input type="text" name="dcdomicilio" value="<?php echo set_value('dcdomicilio'); ?>" size="28"></td>
							<td><input type="text" name="dcmunicipio" value="<?php echo set_value('dcmunicipio'); ?>" size="10"></td>
							<td><input type="text" name="dcestado" value="<?php echo set_value('dcestado'); ?>" size="10"></td>
							<td><input type="text" name="dctelefono" value="<?php echo set_value('dctelefono'); ?>" size="10"></td>
							<td><input type="text" name="dccelular" value="<?php echo set_value('dccelular'); ?>" size="10"></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		<div class="w25 f-l">
			<div class="frmsec-right w100 f-l">
				<div class="frmbox-r p5-tb corner-right8">
					<label for="dfecha_vence_seguro">Fecha vencimiento seguro:</label> <br>
					<input type="text" name="dfecha_vence_seguro" id="dfecha_vence_seguro" value="<?php echo set_value('dfecha_vence_seguro'); ?>" class="a-c" size="15" readonly>
				</div>
			</div>
			<div class="frmsec-right w100 f-l">
				<div class="frmbox-r p5-tb corner-right8">
					<label for="dlicencia_avion">Licencia Avion:</label> <br>
					<input type="text" name="dlicencia_avion" value="<?php echo set_value('dlicencia_avion'); ?>" size="15" class="a-c">
					<br>
					<label for="dvencimiento_licencia_a">Fecha vencimiento:</label> <br>
					<input type="text" name="dvencimiento_licencia_a" id="dvencimiento_licencia_a" value="<?php echo set_value('dvencimiento_licencia_a'); ?>" class="a-c" size="15" readonly>
				</div>
			</div>
			<div class="frmsec-right w100 f-l">
				<div class="frmbox-r p5-tb corner-right8">
					<label for="dlicencia_vehiculo">Licencia Vehículo:</label> <br>
					<input type="text" name="dlicencia_vehiculo" value="<?php echo set_value('dlicencia_vehiculo'); ?>" size="15" class="a-c">
					<br>
					<label for="dvencimiento_licencia_v">Fecha vencimiento:</label> <br>
					<input type="text" name="dvencimiento_licencia_v" id="dvencimiento_licencia_v" value="<?php echo set_value('dvencimiento_licencia_v'); ?>" class="a-c" size="15" readonly>
				</div>
				<input type="submit" name="enviar" value="Guardar" class="btn-blue corner-all">
			</div>
		</div>
	</form>
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
