
<div id="contentAll" class="f-l">
	<form action="<?php echo base_url('panel/empleados/modificar/?'.String::getVarsLink(array('msg'))); ?>" method="post" class="frm_addmod" enctype="multipart/form-data">
		<div class="frmsec-left w75 f-l b-r">
			<div id="frmsec-acordion">
				<h3 class="frmsec-acordion"><a href="#">Información</a></h3>
				<div>
					<p>
						<label for="dnombre">*Nombre:</label> <br>
						<input type="text" name="dnombre" id="dnombre" 
							value="<?php echo (isset($empleado['info']->nombre)? $empleado['info']->nombre: ''); ?>" size="40" autofocus>
					</p>
					
					<p class="w50 f-l">
						<label for="dapellido_paterno">Apellido paterno:</label> <br>
						<input type="text" name="dapellido_paterno" id="dapellido_paterno" 
							value="<?php echo (isset($empleado['info']->apellido_paterno)? $empleado['info']->apellido_paterno: ''); ?>" size="30">
					</p>
					<p class="w50 f-l">
						<label for="dapellido_materno">Apellido materno:</label> <br>
						<input type="text" name="dapellido_materno" id="dapellido_materno" 
							value="<?php echo (isset($empleado['info']->apellido_materno)? $empleado['info']->apellido_materno: ''); ?>" size="30">
					</p>
					<div class="clear"></div>
					
					<p>
						<label for="durl_img">Imagen:</label> <br>
						<input type="file" name="durl_img" id="durl_img">
					</p>
					
					<p class="w30 f-l">
						<label for="dusuario">Usuario:</label> <br>
						<input type="text" name="dusuario" id="dusuario" 
							value="<?php echo (isset($empleado['info']->usuario)? $empleado['info']->usuario: ''); ?>" size="20">
					</p>
					<p class="w30 f-l">
						<label for="dpassword">Contraseña:</label> <br>
						<input type="password" name="dpassword" id="dpassword" size="20">
					</p>
					<p class="w30 f-l">
						<label for="dpassword_conf">Conf contraseña:</label> <br>
						<input type="password" name="dpassword_conf" id="dpassword_conf" size="20">
					</p>
					<div class="clear"></div>
					
					<p class="w50 f-l">
						<label for="dcalle">Calle:</label> <br>
						<input type="text" name="dcalle" id="dcalle" 
							value="<?php echo (isset($empleado['info']->calle)? $empleado['info']->calle: ''); ?>" size="30">
					</p>
					<p class="w50 f-l">
						<label for="dnumero">Numero:</label> <br>
						<input type="text" name="dnumero" id="dnumero" 
							value="<?php echo (isset($empleado['info']->numero)? $empleado['info']->numero: ''); ?>" size="30">
					</p>
					<div class="clear"></div>
					
					<p class="w50 f-l">
						<label for="dcolonia">Colonia:</label> <br>
						<input type="text" name="dcolonia" id="dcolonia" 
							value="<?php echo (isset($empleado['info']->colonia)? $empleado['info']->colonia: ''); ?>" size="30">
					</p>
					<p class="w50 f-l">
						<label for="dmunicipio">Municipio:</label> <br>
						<input type="text" name="dmunicipio" id="dmunicipio" 
							value="<?php echo (isset($empleado['info']->municipio)? $empleado['info']->municipio: ''); ?>" size="30">
					</p>
					<div class="clear"></div>
					
					<p class="w50 f-l">
						<label for="destado">Estado:</label> <br>
						<input type="text" name="destado" id="destado" 
							value="<?php echo (isset($empleado['info']->estado)? $empleado['info']->estado: ''); ?>" size="30">
					</p>
					<p class="w50 f-l">
						<label for="dcp">CP:</label> <br>
						<input type="text" name="dcp" id="dcp" 
							value="<?php echo (isset($empleado['info']->cp)? $empleado['info']->cp: ''); ?>" size="30">
					</p>
					<div class="clear"></div>
					
					<p class="w50 f-l">
						<label for="dtelefono">Teléfono:</label> <br>
						<input type="text" name="dtelefono" id="dtelefono" 
							value="<?php echo (isset($empleado['info']->telefono)? $empleado['info']->telefono: ''); ?>" size="30">
					</p>
					<p class="w50 f-l">
						<label for="dcelular">Celular:</label> <br>
						<input type="text" name="dcelular" id="dcelular" 
							value="<?php echo (isset($empleado['info']->celular)? $empleado['info']->celular: ''); ?>" size="30">
					</p>
					<div class="clear"></div>
					
					<p class="w50 f-l">
						<label for="demail">Email:</label> <br>
						<input type="text" name="demail" id="demail" 
							value="<?php echo (isset($empleado['info']->email)? $empleado['info']->email: ''); ?>" size="30">
					</p>
					<p class="w50 f-l">
						<label for="dfecha_nacimiento">Fecha nacimiento:</label> <br>
						<input type="text" name="dfecha_nacimiento" id="dfecha_nacimiento" 
							value="<?php echo (isset($empleado['info']->fecha_nacimiento)? $empleado['info']->fecha_nacimiento: ''); ?>" size="30" readonly>
					</p>
					<div class="clear"></div>
					
					<p class="w50 f-l">
						<label for="dfecha_entrada">Fecha entrada:</label> <br>
						<input type="text" name="dfecha_entrada" id="dfecha_entrada" 
							value="<?php echo (isset($empleado['info']->fecha_entrada)? $empleado['info']->fecha_entrada: ''); ?>" size="30" readonly>
					</p>
					<p class="w50 f-l">
						<label for="dfecha_salida">Fecha salida:</label> <br>
						<input type="text" name="dfecha_salida" id="dfecha_salida" 
							value="<?php echo (isset($empleado['info']->fecha_salida)? $empleado['info']->fecha_salida: ''); ?>" size="30" readonly>
					</p>
					<div class="clear"></div>
					
					<p class="w50 f-l">
						<label for="dsalario">Salario Diario:</label> <br>
						<input type="text" name="dsalario" id="dsalario" 
							value="<?php echo (isset($empleado['info']->salario)? $empleado['info']->salario: ''); ?>" size="30" class="">
					</p>
					<p class="w50 f-l">
						<label for="dhora_entrada">Hora de Entrada:</label> <br>
						<input type="text" name="dhora_entrada" id="dhora_entrada" 
							value="<?php echo (isset($empleado['info']->hora_entrada)? $empleado['info']->hora_entrada: ''); ?>" size="30">
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
				<?php if(isset($empleado['contactos'])){
					foreach($empleado['contactos'] as $conta){
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
										$priv_ec = $this->empleados_model->getLinkPrivSm('empleados/eliminar_contacto/', 
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
								<?php echo $this->empleados_model->getLinkPrivSm('empleados/agregar_contacto/', 
									$empleado['info']->id_empleado,
									"addContacto('tbl_contactos', this); return false;");

									echo (isset($priv_ec)? $priv_ec: '');
								?>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		
		<div class="frmsec-right w25 f-l">
			<div class="frmbox-r p5-tb corner-right8">
				<label for="dtipo_usuario">Tipo usuario:</label> <br>
				<select name="dtipo_usuario" id="dtipo_usuario" class="w90">
					<option value="empleado" <?php echo set_select('dtipo_usuario', '"empleado"', false, 
							(isset($empleado['info']->tipo_usuario)? $empleado['info']->tipo_usuario: '')); ?>>Empleado</option>
					<option value="admin" <?php echo set_select('dtipo_usuario', 'admin', false, 
							(isset($empleado['info']->tipo_usuario)? $empleado['info']->tipo_usuario: '')); ?>>Admin</option>
				</select>
				<br>
				
				<label for="dstatus">Status:</label> <br>
				<select name="dstatus" id="dstatus" class="w90">
					<option value="contratado" <?php echo set_select('dstatus', 'contratado', false, 
							(isset($empleado['info']->status)? $empleado['info']->status: '')); ?>>Contratado</option>
					<option value="no_contratado" <?php echo set_select('dstatus', 'no_contratado', false, 
							(isset($empleado['info']->status)? $empleado['info']->status: '')); ?>>No contratado</option>
					<option value="usuario" <?php echo set_select('dstatus', 'usuario', false, 
							(isset($empleado['info']->status)? $empleado['info']->status: '')); ?>>Usuario</option>
				</select>
			</div>
			
			<div class="frmbox-r priv corner-right8">
				<input type="hidden" name="dmod_privilegios" id="dmod_privilegios" value="">
				<?php echo $this->empleados_model->getFrmPrivilegios(0, true, (isset($empleado['privilegios'])? $empleado['privilegios']: array())); ?>
			</div>
			
			<input type="submit" name="enviar" value="Guardar" class="btn-blue corner-all">
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
