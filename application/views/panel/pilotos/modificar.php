
<div id="contentAll" class="f-l">
	<form action="<?php echo base_url('panel/pilotos/modificar/?'.String::getVarsLink(array('msg'))); ?>" method="post" class="frm_addmod" enctype="multipart/form-data">
		<div class="frmsec-left w75 f-l">
			<div id="frmsec-acordion">
				<h3 class="frmsec-acordion"><a href="#">Información</a></h3>
				<div>
					<p>
						<label for="dnombre">*Nombre:</label> <br>
						<input type="text" name="dnombre" id="dnombre" 
							value="<?php echo (isset($piloto['info']->nombre)? $piloto['info']->nombre: ''); ?>" size="40" maxlength="120" autofocus>
					</p>
					<div class="clear"></div>
					
					<p class="w40 f-l">
						<label for="dcalle">Calle:</label> <br>
						<input type="text" name="dcalle" id="dcalle" 
							value="<?php echo (isset($piloto['info']->calle)? $piloto['info']->calle: ''); ?>" size="30" maxlength="60">
					</p>
					<p class="w30 f-l">
						<label for="dno_exterior">No exterior:</label> <br>
						<input type="text" name="dno_exterior" id="dno_exterior" 
							value="<?php echo (isset($piloto['info']->no_exterior)? $piloto['info']->no_exterior: ''); ?>" size="20" maxlength="7">
					</p>
					<p class="w30 f-l">
						<label for="dno_interior">No interior:</label> <br>
						<input type="text" name="dno_interior" id="dno_interior" 
							value="<?php echo (isset($piloto['info']->no_interior)? $piloto['info']->no_interior: ''); ?>" size="20" maxlength="7">
					</p>
					<div class="clear"></div>
					
					<p class="w50 f-l">
						<label for="dcolonia">Colonia:</label> <br>
						<input type="text" name="dcolonia" id="dcolonia" 
							value="<?php echo (isset($piloto['info']->colonia)? $piloto['info']->colonia: ''); ?>" size="30" maxlength="60">
					</p>
					<p class="w50 f-l">
						<label for="dlocalidad">Localidad:</label> <br>
						<input type="text" name="dlocalidad" id="dlocalidad" 
							value="<?php echo (isset($piloto['info']->localidad)? $piloto['info']->localidad: ''); ?>" size="30" maxlength="45">
					</p>
					<div class="clear"></div>
					
					<p class="w50 f-l">
						<label for="dmunicipio">Municipio:</label> <br>
						<input type="text" name="dmunicipio" id="dmunicipio" 
							value="<?php echo (isset($piloto['info']->municipio)? $piloto['info']->municipio: ''); ?>" size="30" maxlength="45">
					</p>
					<p class="w50 f-l">
						<label for="destado">Estado:</label> <br>
						<input type="text" name="destado" id="destado" 
							value="<?php echo (isset($piloto['info']->estado)? $piloto['info']->estado: ''); ?>" size="30" maxlength="45">
					</p>
					<p class="w50 f-l">
						<label for="dcp">CP:</label> <br>
						<input type="text" name="dcp" id="dcp" 
							value="<?php echo (isset($piloto['info']->cp)? $piloto['info']->cp: ''); ?>" size="30" maxlength="10">
					</p>
					<p class="w50 f-l">
						<label for="dfecha_nacimiento">Fecha Nacimiento:</label> <br>
						<input type="text" name="dfecha_nacimiento" id="dfecha_nacimiento" value="<?php echo (isset($piloto['info']->fecha_nacimiento)? $piloto['info']->fecha_nacimiento: '')?>" size="20" maxlength="10">
					</p>
					<div class="clear"></div>
					
					<p class="w50 f-l">
						<label for="dtelefono">Teléfono:</label> <br>
						<input type="text" name="dtelefono" id="dtelefono" 
							value="<?php echo (isset($piloto['info']->telefono)? $piloto['info']->telefono: ''); ?>" size="30" maxlength="15">
					</p>
					<p class="w50 f-l">
						<label for="dcelular">Celular:</label> <br>
						<input type="text" name="dcelular" id="dcelular" 
							value="<?php echo (isset($piloto['info']->celular)? $piloto['info']->celular: ''); ?>" size="30" maxlength="20">
					</p>
					<div class="clear"></div>
					
					<p class="w50 f-l">
						<label for="demail">Email:</label> <br>
						<input type="text" name="demail" id="demail" 
							value="<?php echo (isset($piloto['info']->email)? $piloto['info']->email: ''); ?>" size="30" maxlength="70">
					</p>
					<p class="w50 f-l">
						<label for="dpag_web">Pag Web:</label> <br>
						<input type="text" name="dpag_web" id="dpag_web" 
							value="<?php echo (isset($piloto['info']->pag_web)? $piloto['info']->pag_web: ''); ?>" size="30" maxlength="80">
					</p>
					<div class="clear"></div>
					
					<p class="w80 f-l">
						<label for="dcomentarios">Comentarios:</label> <br>
						<textarea name="dcomentarios" id="dcomentarios" rows="3" cols="40" 
							maxlength="400"><?php echo (isset($piloto['info']->comentarios)? $piloto['info']->comentarios: ''); ?></textarea>
					</p>
					<div class="clear"></div>
					
					<p class="w50 f-l" style="display: none;">
						<label for="drecepcion_facturas">Recepción facturas:</label> <br>
						<select name="drecepcion_facturas" id="drecepcion_facturas">
							<option value="Lunes" <?php echo set_select('drecepcion_facturas', 'Lunes', false, 
									(isset($piloto['info']->recepcion_facturas)? $piloto['info']->recepcion_facturas: '')); ?>>Lunes</option>
							<option value="Martes" <?php echo set_select('drecepcion_facturas', 'Martes', false, 
									(isset($piloto['info']->recepcion_facturas)? $piloto['info']->recepcion_facturas: '')); ?>>Martes</option>
							<option value="Miércoles" <?php echo set_select('drecepcion_facturas', 'Miércoles', false, 
									(isset($piloto['info']->recepcion_facturas)? $piloto['info']->recepcion_facturas: '')); ?>>Miércoles</option>
							<option value="Jueves" <?php echo set_select('drecepcion_facturas', 'Jueves', false, 
									(isset($piloto['info']->recepcion_facturas)? $piloto['info']->recepcion_facturas: '')); ?>>Jueves</option>
							<option value="Viernes" <?php echo set_select('drecepcion_facturas', 'Viernes', false, 
									(isset($piloto['info']->recepcion_facturas)? $piloto['info']->recepcion_facturas: '')); ?>>Viernes</option>
							<option value="Sábado" <?php echo set_select('drecepcion_facturas', 'Sábado', false, 
									(isset($piloto['info']->recepcion_facturas)? $piloto['info']->recepcion_facturas: '')); ?>>Sábado</option>
							<option value="Domingo" <?php echo set_select('drecepcion_facturas', 'Domingo', false, 
									(isset($piloto['info']->recepcion_facturas)? $piloto['info']->recepcion_facturas: '')); ?>>Domingo</option>
						</select>
					</p>
					<p class="w50 f-l" style="display: none;">
						<label for="ddias_pago">Dias pago:</label> <br>
						<select name="ddias_pago" id="ddias_pago">
							<option value="Lunes" <?php echo set_select('ddias_pago', 'Lunes', false, 
									(isset($piloto['info']->dias_pago)? $piloto['info']->dias_pago: '')); ?>>Lunes</option>
							<option value="Martes" <?php echo set_select('ddias_pago', 'Martes', false, 
									(isset($piloto['info']->dias_pago)? $piloto['info']->dias_pago: '')); ?>>Martes</option>
							<option value="Miércoles" <?php echo set_select('ddias_pago', 'Miércoles', false, 
									(isset($piloto['info']->dias_pago)? $piloto['info']->dias_pago: '')); ?>>Miércoles</option>
							<option value="Jueves" <?php echo set_select('ddias_pago', 'Jueves', false, 
									(isset($piloto['info']->dias_pago)? $piloto['info']->dias_pago: '')); ?>>Jueves</option>
							<option value="Viernes" <?php echo set_select('ddias_pago', 'Viernes', false, 
									(isset($piloto['info']->dias_pago)? $piloto['info']->dias_pago: '')); ?>>Viernes</option>
							<option value="Sábado" <?php echo set_select('ddias_pago', 'Sábado', false, 
									(isset($piloto['info']->dias_pago)? $piloto['info']->dias_pago: '')); ?>>Sábado</option>
							<option value="Domingo" <?php echo set_select('ddias_pago', 'Domingo', false, 
									(isset($piloto['info']->dias_pago)? $piloto['info']->dias_pago: '')); ?>>Domingo</option>
						</select>
					</p>
					<div class="clear"></div>
					
					<p class="w50 f-l" style="display: none;">
						<label for="ddias_credito">Dias credito:</label> <br>
						<select name="ddias_credito" id="ddias_credito">
							<option value="15" <?php echo set_select('ddias_credito', '15', false, 
									(isset($piloto['info']->dias_credito)? $piloto['info']->dias_credito: '')); ?>>15 Dias</option>
							<option value="30" <?php echo set_select('ddias_credito', '30', false, 
									(isset($piloto['info']->dias_credito)? $piloto['info']->dias_credito: '')); ?>>30 Dias</option>
							<option value="60" <?php echo set_select('ddias_credito', '60', false, 
									(isset($piloto['info']->dias_credito)? $piloto['info']->dias_credito: '')); ?>>60 Dias</option>
							<option value="90" <?php echo set_select('ddias_credito', '90', false, 
									(isset($piloto['info']->dias_credito)? $piloto['info']->dias_credito: '')); ?>>90 Dias</option>
						</select>
					</p>
					
					<p class="w50 f-l">
						<label for="dexpide_factura">Expide Factura:</label> <br>
						<select name="dexpide_factura" id="dexpide_factura">
							<option value="1" <?php echo set_select('dexpide_factura', '1', false, 
									(isset($piloto['info']->expide_factura)? ($piloto['info']->expide_factura=='t' ? '1' : '0') : '')); ?>>Si</option>
							<option value="0" <?php echo set_select('dexpide_factura', '0', false, 
									(isset($piloto['info']->expide_factura)? ($piloto['info']->expide_factura=='t' ? '1' : '0') : '')); ?>>No</option>
						</select>
					</p>
					<p class="w50 f-l">
						<label for="dprecio_vuelo">Precio por Vuelo:</label> <br>
						<input type="text" name="dprecio_vuelo" id="dprecio_vuelo" class="vpositive" value="<?php echo isset($piloto['info']->precio_vuelo)?$piloto['info']->precio_vuelo:0; ?>" size="10">
					</p>
					<div class="clear"></div>
				</div>
				
				<h3><a href="#">Contactos</a></h3>
				<div>
					<table class="tblListados corner-all8" id="tbl_contactos">
						<tr class="header btn-gray">
							<td>Nombre</td>
							<td>Domicilio</td>
							<td>Municipio</td>
							<td>Estado</td>
							<td>Teléfono</td>
							<td>Celular</td>
							<td>Opc</td>
						</tr>
				<?php if(isset($piloto['contactos'])){
					foreach($piloto['contactos'] as $conta){
				?>
						<tr>
							<td><?php echo $conta->nombre; ?></td>
							<td><?php echo $conta->domicilio; ?></td>
							<td><?php echo $conta->municipio; ?></td>
							<td><?php echo $conta->estado; ?></td>
							<td><?php echo $conta->telefono; ?></td>
							<td><?php echo $conta->celular; ?></td>
							<td class="tdsmenu a-c" style="width: 90px;">
								<img alt="opc" src="<?php echo base_url('application/images/privilegios/gear.png'); ?>" width="16" height="16">
								<div class="submenul">
									<p class="corner-bottom8">
										<?php 
										$priv_ec = $this->empleados_model->getLinkPrivSm('pilotos/eliminar_contacto/', 
												$conta->id_contacto,
												"msb.confirm('Estas seguro de eliminar el contacto?', this, eliminaContacto); return false;");
										echo $priv_ec;
										//para el js, indica q tiene ese permiso
										$priv_ec = $priv_ec != ''? '<span id="priv_eliminar_contacto" style="display: none;"></span>': '';
										?>
									</p>
								</div>
							</td>
						</tr>
				<?php }
				}
				?>
						<tr class="foot btn-gray">
							<td colspan="7">
								<?php echo $this->empleados_model->getLinkPrivSm('pilotos/agregar_contacto/', 
									$piloto['info']->id_proveedor, "addContacto('tbl_contactos', this); return false;");

									echo (isset($priv_ec)? $priv_ec: '');
								?>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		<div class="w25 f-l">
			<div class="frmsec-right w100 f-l">
				<div class="frmbox-r p5-tb corner-right8">
					<label for="dfecha_vence_seguro">Fecha vencimiento seguro:</label> <br>
					<input type="text" name="dfecha_vence_seguro" id="dfecha_vence_seguro" value="<?php echo (isset($piloto['info']->fecha_vence_seguro))? $piloto['info']->fecha_vence_seguro: ''; ?>" class="a-c" size="15" readonly>
				</div>
			</div>
			<div class="frmsec-right w100 f-l">
				<div class="frmbox-r p5-tb corner-right8">
					<label for="dlicencia_avion">Licencia Avion:</label> <br>
					<input type="text" name="dlicencia_avion" class="a-c" value="<?php echo (isset($piloto['info']->licencia_avion)? $piloto['info']->licencia_avion: ''); ?>" size="15">
					<br>
					<label for="dvencimiento_licencia_a">Fecha vencimiento:</label> <br>
					<input type="text" name="dvencimiento_licencia_a" class="a-c" id="dvencimiento_licencia_a" value="<?php echo (isset($piloto['info']->vencimiento_licencia_a)? $piloto['info']->vencimiento_licencia_a: ''); ?>" size="15">
				</div>
			</div>
			<div class="frmsec-right w100 f-l">
				<div class="frmbox-r p5-tb corner-right8">
					<label for="dlicencia_vehiculo">Licencia Vehículo:</label> <br>
					<input type="text" name="dlicencia_vehiculo" class="a-c" value="<?php echo (isset($piloto['info']->licencia_vehiculo)? $piloto['info']->licencia_vehiculo: ''); ?>" size="15">
					<br>
					<label for="dvencimiento_licencia_v">Fecha vencimiento:</label> <br>
					<input type="text" name="dvencimiento_licencia_v" class="a-c" id="dvencimiento_licencia_v" value="<?php echo (isset($piloto['info']->vencimiento_licencia_v)? $piloto['info']->vencimiento_licencia_v: ''); ?>" size="15">
				</div>
				<input type="submit" name="enviar" value="Guardar" class="btn-blue corner-all">
			</div>
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
