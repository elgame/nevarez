
<div id="contentAll" class="f-l">
	<form action="<?php echo base_url('panel/proveedores/modificar/?'.String::getVarsLink(array('msg'))); ?>" method="post" class="frm_addmod" enctype="multipart/form-data">
		<div class="frmsec-left w100 f-l">
			<div id="frmsec-acordion">
				<h3 class="frmsec-acordion"><a href="#">Información</a></h3>
				<div>
					<p>
						<label for="dnombre">*Nombre:</label> <br>
						<input type="text" name="dnombre" id="dnombre" 
							value="<?php echo (isset($proveedor['info']->nombre)? $proveedor['info']->nombre: ''); ?>" size="40" maxlength="120" autofocus>
					</p>
					<div class="clear"></div>
					
					<p class="w40 f-l">
						<label for="dcalle">Calle:</label> <br>
						<input type="text" name="dcalle" id="dcalle" 
							value="<?php echo (isset($proveedor['info']->calle)? $proveedor['info']->calle: ''); ?>" size="30" maxlength="60">
					</p>
					<p class="w30 f-l">
						<label for="dno_exterior">No exterior:</label> <br>
						<input type="text" name="dno_exterior" id="dno_exterior" 
							value="<?php echo (isset($proveedor['info']->no_exterior)? $proveedor['info']->no_exterior: ''); ?>" size="20" maxlength="7">
					</p>
					<p class="w30 f-l">
						<label for="dno_interior">No interior:</label> <br>
						<input type="text" name="dno_interior" id="dno_interior" 
							value="<?php echo (isset($proveedor['info']->no_interior)? $proveedor['info']->no_interior: ''); ?>" size="20" maxlength="7">
					</p>
					<div class="clear"></div>
					
					<p class="w50 f-l">
						<label for="dcolonia">Colonia:</label> <br>
						<input type="text" name="dcolonia" id="dcolonia" 
							value="<?php echo (isset($proveedor['info']->colonia)? $proveedor['info']->colonia: ''); ?>" size="30" maxlength="60">
					</p>
					<p class="w50 f-l">
						<label for="dlocalidad">Localidad:</label> <br>
						<input type="text" name="dlocalidad" id="dlocalidad" 
							value="<?php echo (isset($proveedor['info']->localidad)? $proveedor['info']->localidad: ''); ?>" size="30" maxlength="45">
					</p>
					<div class="clear"></div>
					
					<p class="w50 f-l">
						<label for="dmunicipio">Municipio / Delegación:</label> <br>
						<input type="text" name="dmunicipio" id="dmunicipio" 
							value="<?php echo (isset($proveedor['info']->municipio)? $proveedor['info']->municipio: ''); ?>" size="30" maxlength="45">
					</p>
					<p class="w50 f-l">
						<label for="destado">Estado:</label> <br>
						<input type="text" name="destado" id="destado" 
							value="<?php echo (isset($proveedor['info']->estado)? $proveedor['info']->estado: ''); ?>" size="30" maxlength="45">
					</p>
					<p class="w50 f-l">
						<label for="dcp">CP:</label> <br>
						<input type="text" name="dcp" id="dcp" 
							value="<?php echo (isset($proveedor['info']->cp)? $proveedor['info']->cp: ''); ?>" size="30" maxlength="10">
					</p>
					<div class="clear"></div>
					
					<p class="w50 f-l">
						<label for="dtelefono">Teléfono:</label> <br>
						<input type="text" name="dtelefono" id="dtelefono" 
							value="<?php echo (isset($proveedor['info']->telefono)? $proveedor['info']->telefono: ''); ?>" size="30" maxlength="15">
					</p>
					<p class="w50 f-l">
						<label for="dcelular">Celular:</label> <br>
						<input type="text" name="dcelular" id="dcelular" 
							value="<?php echo (isset($proveedor['info']->celular)? $proveedor['info']->celular: ''); ?>" size="30" maxlength="20">
					</p>
					<div class="clear"></div>
					
					<p class="w50 f-l">
						<label for="demail">Email:</label> <br>
						<input type="text" name="demail" id="demail" 
							value="<?php echo (isset($proveedor['info']->email)? $proveedor['info']->email: ''); ?>" size="30" maxlength="70">
					</p>
					<p class="w50 f-l">
						<label for="dpag_web">Pag Web:</label> <br>
						<input type="text" name="dpag_web" id="dpag_web" 
							value="<?php echo (isset($proveedor['info']->pag_web)? $proveedor['info']->pag_web: ''); ?>" size="30" maxlength="80">
					</p>
					<div class="clear"></div>
					
					<p class="w80 f-l">
						<label for="dcomentarios">Comentarios:</label> <br>
						<textarea name="dcomentarios" id="dcomentarios" rows="3" cols="40" 
							maxlength="400"><?php echo (isset($proveedor['info']->comentarios)? $proveedor['info']->comentarios: ''); ?></textarea>
					</p>
					<div class="clear"></div>
					
					<p class="w50 f-l">
						<label for="drecepcion_facturas">Recepción facturas:</label> <br>
						<select name="drecepcion_facturas" id="drecepcion_facturas">
							<option value="Lunes" <?php echo set_select('drecepcion_facturas', 'Lunes', false, 
									(isset($proveedor['info']->recepcion_facturas)? $proveedor['info']->recepcion_facturas: '')); ?>>Lunes</option>
							<option value="Martes" <?php echo set_select('drecepcion_facturas', 'Martes', false, 
									(isset($proveedor['info']->recepcion_facturas)? $proveedor['info']->recepcion_facturas: '')); ?>>Martes</option>
							<option value="Miércoles" <?php echo set_select('drecepcion_facturas', 'Miércoles', false, 
									(isset($proveedor['info']->recepcion_facturas)? $proveedor['info']->recepcion_facturas: '')); ?>>Miércoles</option>
							<option value="Jueves" <?php echo set_select('drecepcion_facturas', 'Jueves', false, 
									(isset($proveedor['info']->recepcion_facturas)? $proveedor['info']->recepcion_facturas: '')); ?>>Jueves</option>
							<option value="Viernes" <?php echo set_select('drecepcion_facturas', 'Viernes', false, 
									(isset($proveedor['info']->recepcion_facturas)? $proveedor['info']->recepcion_facturas: '')); ?>>Viernes</option>
							<option value="Sábado" <?php echo set_select('drecepcion_facturas', 'Sábado', false, 
									(isset($proveedor['info']->recepcion_facturas)? $proveedor['info']->recepcion_facturas: '')); ?>>Sábado</option>
							<option value="Domingo" <?php echo set_select('drecepcion_facturas', 'Domingo', false, 
									(isset($proveedor['info']->recepcion_facturas)? $proveedor['info']->recepcion_facturas: '')); ?>>Domingo</option>
						</select>
					</p>
					<p class="w50 f-l">
						<label for="ddias_pago">Dias pago:</label> <br>
						<select name="ddias_pago" id="ddias_pago">
							<option value="Lunes" <?php echo set_select('ddias_pago', 'Lunes', false, 
									(isset($proveedor['info']->dias_pago)? $proveedor['info']->dias_pago: '')); ?>>Lunes</option>
							<option value="Martes" <?php echo set_select('ddias_pago', 'Martes', false, 
									(isset($proveedor['info']->dias_pago)? $proveedor['info']->dias_pago: '')); ?>>Martes</option>
							<option value="Miércoles" <?php echo set_select('ddias_pago', 'Miércoles', false, 
									(isset($proveedor['info']->dias_pago)? $proveedor['info']->dias_pago: '')); ?>>Miércoles</option>
							<option value="Jueves" <?php echo set_select('ddias_pago', 'Jueves', false, 
									(isset($proveedor['info']->dias_pago)? $proveedor['info']->dias_pago: '')); ?>>Jueves</option>
							<option value="Viernes" <?php echo set_select('ddias_pago', 'Viernes', false, 
									(isset($proveedor['info']->dias_pago)? $proveedor['info']->dias_pago: '')); ?>>Viernes</option>
							<option value="Sábado" <?php echo set_select('ddias_pago', 'Sábado', false, 
									(isset($proveedor['info']->dias_pago)? $proveedor['info']->dias_pago: '')); ?>>Sábado</option>
							<option value="Domingo" <?php echo set_select('ddias_pago', 'Domingo', false, 
									(isset($proveedor['info']->dias_pago)? $proveedor['info']->dias_pago: '')); ?>>Domingo</option>
						</select>
					</p>
					<div class="clear"></div>
					
					<p class="w50">
						<label for="ddias_credito">Dias credito:</label> <br>
						<select name="ddias_credito" id="ddias_credito">
							<option value="15" <?php echo set_select('ddias_credito', '15', false, 
									(isset($proveedor['info']->dias_credito)? $proveedor['info']->dias_credito: '')); ?>>15 Dias</option>
							<option value="30" <?php echo set_select('ddias_credito', '30', false, 
									(isset($proveedor['info']->dias_credito)? $proveedor['info']->dias_credito: '')); ?>>30 Dias</option>
							<option value="60" <?php echo set_select('ddias_credito', '60', false, 
									(isset($proveedor['info']->dias_credito)? $proveedor['info']->dias_credito: '')); ?>>60 Dias</option>
							<option value="90" <?php echo set_select('ddias_credito', '90', false, 
									(isset($proveedor['info']->dias_credito)? $proveedor['info']->dias_credito: '')); ?>>90 Dias</option>
						</select>
					</p>
					<div class="clear"></div>
				</div>
				
				<h3><a href="#">Contactos</a></h3>
				<div>
					<table class="tblListados corner-all8" id="tbl_contactos">
						<tr class="header btn-gray">
							<td>Nombre</td>
							<td>Puesto</td>
							<td>Teléfono</td>
							<td>Extensión</td>
							<td>Celular</td>
							<td>Nextel</td>
							<td>ID Nextel</td>
							<td>Fax</td>
							<td>Opc</td>
						</tr>
				<?php if(isset($proveedor['contactos'])){
					foreach($proveedor['contactos'] as $conta){
				?>
						<tr>
							<td><?php echo $conta->nombre; ?></td>
							<td><?php echo $conta->puesto; ?></td>
							<td><?php echo $conta->telefono; ?></td>
							<td><?php echo $conta->extension; ?></td>
							<td><?php echo $conta->celular; ?></td>
							<td><?php echo $conta->nextel; ?></td>
							<td><?php echo $conta->nextel_id; ?></td>
							<td><?php echo $conta->fax; ?></td>
							<td class="tdsmenu a-c" style="width: 90px;">
								<img alt="opc" src="<?php echo base_url('application/images/privilegios/gear.png'); ?>" width="16" height="16">
								<div class="submenul">
									<p class="corner-bottom8">
										<?php 
										$priv_ec = $this->empleados_model->getLinkPrivSm('proveedores/eliminar_contacto/', 
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
							<td colspan="9">
								<?php echo $this->empleados_model->getLinkPrivSm('proveedores/agregar_contacto/', 
									$proveedor['info']->id_proveedor, "addContacto('tbl_contactos', this); return false;");

									echo (isset($priv_ec)? $priv_ec: '');
								?>
							</td>
						</tr>
					</table>
				</div>
			</div>
			
			<input type="submit" name="enviar" value="Guardar" class="btn-blue corner-all m10-all f-r">
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
