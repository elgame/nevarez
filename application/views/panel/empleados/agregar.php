
<div id="contentAll" class="f-l">
	<form action="<?php echo base_url('panel/empleados/agregar'); ?>" method="post" class="frm_addmod" enctype="multipart/form-data">
		<div class="frmsec-left w75 f-l b-r">
			<div id="frmsec-acordion">
				<h3 class="frmsec-acordion"><a href="#">Información</a></h3>
				<div>
					<p>
						<label for="dnombre">*Nombre:</label> <br>
						<input type="text" name="dnombre" id="dnombre" value="<?php echo set_value('dnombre'); ?>" size="40" autofocus>
					</p>
					
					<p class="w50 f-l">
						<label for="dapellido_paterno">Apellido paterno:</label> <br>
						<input type="text" name="dapellido_paterno" id="dapellido_paterno" value="<?php echo set_value('dapellido_paterno'); ?>" size="30">
					</p>
					<p class="w50 f-l">
						<label for="dapellido_materno">Apellido materno:</label> <br>
						<input type="text" name="dapellido_materno" id="dapellido_materno" value="<?php echo set_value('dapellido_materno'); ?>" size="30">
					</p>
					<div class="clear"></div>
					
					<p>
						<label for="durl_img">Imagen:</label> <br>
						<input type="file" name="durl_img" id="durl_img">
					</p>
					
					<p class="w30 f-l">
						<label for="dusuario">Usuario:</label> <br>
						<input type="text" name="dusuario" id="dusuario" value="<?php echo set_value('dusuario'); ?>" size="20">
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
						<input type="text" name="dcalle" id="dcalle" value="<?php echo set_value('dcalle'); ?>" size="30">
					</p>
					<p class="w50 f-l">
						<label for="dnumero">Numero:</label> <br>
						<input type="text" name="dnumero" id="dnumero" value="<?php echo set_value('dnumero'); ?>" size="30">
					</p>
					<div class="clear"></div>
					
					<p class="w50 f-l">
						<label for="dcolonia">Colonia:</label> <br>
						<input type="text" name="dcolonia" id="dcolonia" value="<?php echo set_value('dcolonia'); ?>" size="30">
					</p>
					<p class="w50 f-l">
						<label for="dmunicipio">Municipio:</label> <br>
						<input type="text" name="dmunicipio" id="dmunicipio" value="<?php echo set_value('dmunicipio'); ?>" size="30">
					</p>
					<div class="clear"></div>
					
					<p class="w50 f-l">
						<label for="destado">Estado:</label> <br>
						<input type="text" name="destado" id="destado" value="<?php echo set_value('destado'); ?>" size="30">
					</p>
					<p class="w50 f-l">
						<label for="dcp">CP:</label> <br>
						<input type="text" name="dcp" id="dcp" value="<?php echo set_value('dcp'); ?>" size="30">
					</p>
					<div class="clear"></div>
					
					<p class="w50 f-l">
						<label for="dtelefono">Teléfono:</label> <br>
						<input type="text" name="dtelefono" id="dtelefono" value="<?php echo set_value('dtelefono'); ?>" size="30">
					</p>
					<p class="w50 f-l">
						<label for="dcelular">Celular:</label> <br>
						<input type="text" name="dcelular" id="dcelular" value="<?php echo set_value('dcelular'); ?>" size="30">
					</p>
					<div class="clear"></div>
					
					<p class="w50 f-l">
						<label for="demail">Email:</label> <br>
						<input type="text" name="demail" id="demail" value="<?php echo set_value('demail'); ?>" size="30">
					</p>
					<p class="w50 f-l">
						<label for="dfecha_nacimiento">Fecha nacimiento:</label> <br>
						<input type="text" name="dfecha_nacimiento" id="dfecha_nacimiento" value="<?php echo set_value('dfecha_nacimiento'); ?>" size="30" readonly>
					</p>
					<div class="clear"></div>
					
					<p class="w50 f-l">
						<label for="dfecha_entrada">Fecha entrada:</label> <br>
						<input type="text" name="dfecha_entrada" id="dfecha_entrada" value="<?php echo set_value('dfecha_entrada'); ?>" size="30" readonly>
					</p>
					<p class="w50 f-l">
						<label for="dfecha_salida">Fecha salida:</label> <br>
						<input type="text" name="dfecha_salida" id="dfecha_salida" value="<?php echo set_value('dfecha_salida'); ?>" size="30" readonly>
					</p>
					<div class="clear"></div>
					
					<p class="w50 f-l">
						<label for="dsalario">Salario Diario:</label> <br>
						<input type="text" name="dsalario" id="dsalario" value="<?php echo set_value('dsalario'); ?>" size="30" class="vpositive">
					</p>
					<p class="w50 f-l">
						<label for="dhora_entrada">Hora de Entrada:</label> <br>
						<input type="text" name="dhora_entrada" id="dhora_entrada" value="<?php echo set_value('dhora_entrada'); ?>" size="30">
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
		
		<div class="frmsec-right w25 f-l">
			<div class="frmbox-r p5-tb corner-right8">
				<label for="dtipo_usuario">Tipo usuario:</label> <br>
				<select name="dtipo_usuario" id="dtipo_usuario" class="w90">
					<option value="empleado" <?php echo set_select('dtipo_usuario', 'empleado'); ?>>Empleado</option>
					<option value="admin" <?php echo set_select('dtipo_usuario', 'admin'); ?>>Admin</option>
				</select>
				<br>
				
				<label for="dstatus">Status:</label> <br>
				<select name="dstatus" id="dstatus" class="w90">
					<option value="contratado" <?php echo set_select('dstatus', 'contratado'); ?>>Contratado</option>
					<option value="no_contratado" <?php echo set_select('dstatus', 'no_contratado'); ?>>No contratado</option>
					<option value="usuario" <?php echo set_select('dstatus', 'usuario'); ?>>Usuario</option>
				</select>
			</div>
			
			<div class="frmbox-r priv corner-right8">
				<?php echo $this->empleados_model->getFrmPrivilegios(0, true); ?>
			</div>
			
			<input type="submit" name="enviar" value="Guardar" class="btn-blue corner-all">
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
