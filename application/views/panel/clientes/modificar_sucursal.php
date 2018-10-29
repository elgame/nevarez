
<div id="contentAll" class="f-l">
	<form action="<?php echo base_url('panel/clientes/modificar/?'.String::getVarsLink(array('msg'))); ?>" method="post" class="frm_addmod">
		<div class="frmsec-left w100 f-l">
			<div id="frmsec-acordion">
				<h3 class="frmsec-acordion"><a href="#">Información Facturación</a></h3>
				<div>
					<input type="hidden" name="tiene_sucursales"
						value="<?php echo (isset($info['info']->tiene_sucursales)? $info['info']->tiene_sucursales: 'f'); ?>">
					<p class="w50 f-l">
						<label for="dnombre_fiscal">*Nombre Fiscal: <span class="label_frm_msg">Requerido para facturar</span></label> <br>
						<input type="text" name="dnombre_fiscal" id="dnombre_fiscal"
							value="<?php echo (isset($info['info']->nombre_fiscal)? $info['info']->nombre_fiscal: ''); ?>" size="40" maxlength="130" autofocus>
					</p>
					<p class="w30 f-l">
						<label for="drfc">RFC: <span class="label_frm_msg">Requerido para facturar</span></label> <br>
						<input type="text" name="drfc" id="drfc"
							value="<?php echo (isset($info['info']->rfc)? $info['info']->rfc: ''); ?>" size="20" maxlength="13">
					</p>
					<div class="clear"></div>

					<p class="w40 f-l">
						<label for="dcalle">Calle: <span class="label_frm_msg">Requerido para facturar</span></label> <br>
						<input type="text" name="dcalle" id="dcalle"
							value="<?php echo (isset($info['info']->calle)? $info['info']->calle: ''); ?>" size="30" maxlength="60">
					</p>
					<p class="w30 f-l">
						<label for="dno_exterior">No exterior: <span class="label_frm_msg">Requerido para facturar</span></label> <br>
						<input type="text" name="dno_exterior" id="dno_exterior"
							value="<?php echo (isset($info['info']->no_exterior)? $info['info']->no_exterior: ''); ?>" size="20" maxlength="7">
					</p>
					<p class="w30 f-l">
						<label for="dno_interior">No interior:</label> <br>
						<input type="text" name="dno_interior" id="dno_interior"
							value="<?php echo (isset($info['info']->no_interior)? $info['info']->no_interior: ''); ?>" size="20" maxlength="7">
					</p>
					<div class="clear"></div>

					<p class="w50 f-l">
						<label for="dcolonia">Colonia: <span class="label_frm_msg">Requerido para facturar</span></label> <br>
						<input type="text" name="dcolonia" id="dcolonia"
							value="<?php echo (isset($info['info']->colonia)? $info['info']->colonia: ''); ?>" size="30" maxlength="60">
					</p>
					<p class="w50 f-l">
						<label for="dlocalidad">Localidad: <span class="label_frm_msg">Requerido para facturar</span></label> <br>
						<input type="text" name="dlocalidad" id="dlocalidad"
							value="<?php echo (isset($info['info']->localidad)? $info['info']->localidad: ''); ?>" size="30" maxlength="45">
					</p>
					<div class="clear"></div>

					<p class="w50 f-l">
						<label for="dmunicipio">Municipio / Delegación: <span class="label_frm_msg">Requerido para facturar</span></label> <br>
						<input type="text" name="dmunicipio" id="dmunicipio"
							value="<?php echo (isset($info['info']->municipio)? $info['info']->municipio: ''); ?>" size="30" maxlength="45">
					</p>
					<p class="w50 f-l">
						<label for="destado">Estado: <span class="label_frm_msg">Requerido para facturar</span></label> <br>
						<input type="text" name="destado" id="destado"
							value="<?php echo (isset($info['info']->estado)? $info['info']->estado: ''); ?>" size="30" maxlength="45">
					</p>
					<div class="clear"></div>

					<p class="w50 f-l">
						<label for="dcp">CP: <span class="label_frm_msg">Requerido para facturar</span></label> <br>
						<input type="text" name="dcp" id="dcp"
							value="<?php echo (isset($info['info']->cp)? $info['info']->cp: ''); ?>" size="20" maxlength="10">
					</p>
					<div class="clear"></div>
					<p class="w50 f-l">
						<label for="dtelefono">Teléfono:</label> <br>
						<input type="text" name="dtelefono" id="dtelefono"
							value="<?php echo (isset($info['info']->telefono)? $info['info']->telefono: ''); ?>" size="30" maxlength="15">
					</p>
					<p class="w50 f-l">
						<label for="dcelular">Celular:</label> <br>
						<input type="text" name="dcelular" id="dcelular"
							value="<?php echo (isset($info['info']->celular)? $info['info']->celular: ''); ?>" size="30" maxlength="20">
					</p>
					<div class="clear"></div>

					<p class="w50 f-l">
						<label for="demail">Email:</label> <br>
						<input type="text" name="demail" id="demail"
							value="<?php echo (isset($info['info']->email)? $info['info']->email: ''); ?>" size="30" maxlength="70">
					</p>
					<p class="w50 f-l">
						<label for="dpag_web">Pag Web:</label> <br>
						<input type="text" name="dpag_web" id="dpag_web"
							value="<?php echo (isset($info['info']->pag_web)? $info['info']->pag_web: ''); ?>" size="30" maxlength="80">
					</p>
					<div class="clear"></div>

					<p class="w50 f-l">
						<label for="dcomentarios">Comentarios:</label> <br>
						<textarea name="dcomentarios" id="dcomentarios" rows="3" cols="40"
							maxlength="400"><?php echo (isset($info['info']->comentarios)? $info['info']->comentarios: ''); ?></textarea>
					</p>
					<p class="w30 f-l">
						<label for="dlista_precio">Lista de precio:</label> <br>
						<select name="dlista_precio" id="dlista_precio">
						<?php
							if(is_array($listas)){
								foreach($listas as $itm){
									echo '<option value="'.$itm->id_lista.'" '.set_select('dlista_precio', $itm->id_lista, false,
										(isset($info['info']->id_lista_precio)? $info['info']->id_lista_precio: '')).'>'.$itm->nombre.'</option>';
								}
							}
						?>
						</select>
					</p>
					<div class="clear"></div>

					<p class="w50 f-l">
						<label for="drecepcion_facturas">Recepción facturas:</label> <br>
						<select name="drecepcion_facturas" id="drecepcion_facturas">
							<option value="Lunes" <?php echo set_select('drecepcion_facturas', 'Lunes', false,
									(isset($info['info']->recepcion_facturas)? $info['info']->recepcion_facturas: '')); ?>>Lunes</option>
							<option value="Martes" <?php echo set_select('drecepcion_facturas', 'Martes', false,
									(isset($info['info']->recepcion_facturas)? $info['info']->recepcion_facturas: '')); ?>>Martes</option>
							<option value="Miércoles" <?php echo set_select('drecepcion_facturas', 'Miércoles', false,
									(isset($info['info']->recepcion_facturas)? $info['info']->recepcion_facturas: '')); ?>>Miércoles</option>
							<option value="Jueves" <?php echo set_select('drecepcion_facturas', 'Jueves', false,
									(isset($info['info']->recepcion_facturas)? $info['info']->recepcion_facturas: '')); ?>>Jueves</option>
							<option value="Viernes" <?php echo set_select('drecepcion_facturas', 'Viernes', false,
									(isset($info['info']->recepcion_facturas)? $info['info']->recepcion_facturas: '')); ?>>Viernes</option>
							<option value="Sábado" <?php echo set_select('drecepcion_facturas', 'Sábado', false,
									(isset($info['info']->recepcion_facturas)? $info['info']->recepcion_facturas: '')); ?>>Sábado</option>
							<option value="Domingo" <?php echo set_select('drecepcion_facturas', 'Domingo', false,
									(isset($info['info']->recepcion_facturas)? $info['info']->recepcion_facturas: '')); ?>>Domingo</option>
						</select>
					</p>
					<p class="w50 f-l">
						<label for="ddias_pago">Dias pago:</label> <br>
						<select name="ddias_pago" id="ddias_pago">
							<option value="Lunes" <?php echo set_select('ddias_pago', 'Lunes', false,
									(isset($info['info']->dias_pago)? $info['info']->dias_pago: '')); ?>>Lunes</option>
							<option value="Martes" <?php echo set_select('ddias_pago', 'Martes', false,
									(isset($info['info']->dias_pago)? $info['info']->dias_pago: '')); ?>>Martes</option>
							<option value="Miércoles" <?php echo set_select('ddias_pago', 'Miércoles', false,
									(isset($info['info']->dias_pago)? $info['info']->dias_pago: '')); ?>>Miércoles</option>
							<option value="Jueves" <?php echo set_select('ddias_pago', 'Jueves', false,
									(isset($info['info']->dias_pago)? $info['info']->dias_pago: '')); ?>>Jueves</option>
							<option value="Viernes" <?php echo set_select('ddias_pago', 'Viernes', false,
									(isset($info['info']->dias_pago)? $info['info']->dias_pago: '')); ?>>Viernes</option>
							<option value="Sábado" <?php echo set_select('ddias_pago', 'Sábado', false,
									(isset($info['info']->dias_pago)? $info['info']->dias_pago: '')); ?>>Sábado</option>
							<option value="Domingo" <?php echo set_select('ddias_pago', 'Domingo', false,
									(isset($info['info']->dias_pago)? $info['info']->dias_pago: '')); ?>>Domingo</option>
						</select>
					</p>
					<div class="clear"></div>

					<p class="w50">
						<label for="ddias_credito">Dias credito:</label> <br>
						<input type="number" name="ddias_credito" id="ddias_credito" class="vpositive"
							value="<?php echo (isset($info['info']->dias_credito)? $info['info']->dias_credito: 0); ?>" size="15" min="0" max="120"> dias
					</p>
					<div class="clear"></div>

					<p class="w50 f-l">
						<label for="ddescuento">Descuento:</label> <br>
						<input type="text" name="ddescuento" id="ddescuento"
							value="<?php echo (isset($info['info']->descuento)? $info['info']->descuento: ''); ?>" class="vpositive" size="30" maxlength="3"> %
					</p>
					<p class="w50 f-l">
						<label for="dretencion">Retención ISR:</label> <br>
						<input type="checkbox" value="1" name="dretencion" id="dretencion" <?php echo set_checkbox('dretencion', '1', ($info['info']->retencion==1)? true:false); ?>>
						<?php /*
						<select name="dretencion" id="dretencion">
							<option value="0" <?php echo set_select('dretencion', '0', false,
								(isset($info['info']->retencion)? $info['info']->retencion: '')); ?>>No</option>
							<option value="66.66" <?php echo set_select('dretencion', '66.66', false,
								(isset($info['info']->retencion)? $info['info']->retencion: '')); ?>>2terceras</option>
							<option value="100" <?php echo set_select('dretencion', '100', false,
								(isset($info['info']->retencion)? $info['info']->retencion: '')); ?>>100</option>
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
						<input type="text" name="denombre" id="denombre"
							value="<?php echo (isset($info['info_extra']->nombre)? $info['info_extra']->nombre: ''); ?>" size="40" maxlength="130">
					</p>

					<p class="w40 f-l">
						<label for="decalle">Calle:</label> <br>
						<input type="text" name="decalle" id="decalle"
							value="<?php echo (isset($info['info_extra']->calle)? $info['info_extra']->calle: ''); ?>" size="30" maxlength="60">
					</p>
					<p class="w30 f-l">
						<label for="deno_exterior">No exterior:</label> <br>
						<input type="text" name="deno_exterior" id="deno_exterior"
							value="<?php echo (isset($info['info_extra']->no_exterior)? $info['info_extra']->no_exterior: ''); ?>" size="20" maxlength="7">
					</p>
					<p class="w30 f-l">
						<label for="deno_interior">No interior:</label> <br>
						<input type="text" name="deno_interior" id="deno_interior"
							value="<?php echo (isset($info['info_extra']->no_interior)? $info['info_extra']->no_interior: ''); ?>" size="20" maxlength="7">
					</p>
					<div class="clear"></div>

					<p class="w50 f-l">
						<label for="decolonia">Colonia:</label> <br>
						<input type="text" name="decolonia" id="decolonia"
							value="<?php echo (isset($info['info_extra']->colonia)? $info['info_extra']->colonia: ''); ?>" size="30" maxlength="60">
					</p>
					<p class="w50 f-l">
						<label for="delocalidad">Localidad:</label> <br>
						<input type="text" name="delocalidad" id="delocalidad"
							value="<?php echo (isset($info['info_extra']->localidad)? $info['info_extra']->localidad: ''); ?>" size="30" maxlength="45">
					</p>
					<div class="clear"></div>

					<p class="w50 f-l">
						<label for="demunicipio">Municipio / Delegación:</label> <br>
						<input type="text" name="demunicipio" id="demunicipio"
							value="<?php echo (isset($info['info_extra']->municipio)? $info['info_extra']->municipio: ''); ?>" size="30" maxlength="45">
					</p>
					<p class="w50 f-l">
						<label for="deestado">Estado:</label> <br>
						<input type="text" name="deestado" id="deestado"
							value="<?php echo (isset($info['info_extra']->estado)? $info['info_extra']->estado: ''); ?>" size="30" maxlength="45">
					</p>
					<div class="clear"></div>

					<p class="w50 f-l">
						<label for="decp">CP:</label> <br>
						<input type="text" name="decp" id="decp"
							value="<?php echo (isset($info['info_extra']->cp)? $info['info_extra']->cp: ''); ?>" size="20" maxlength="10">
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
				<?php if(isset($info['contactos'])){
					foreach($info['contactos'] as $conta){
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
										$priv_ec = $this->empleados_model->getLinkPrivSm('clientes/eliminar_contacto/',
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
								<?php echo $this->empleados_model->getLinkPrivSm('clientes/agregar_contacto/',
									$info['info']->id_cliente, "addContacto('tbl_contactos', this); return false;");

									echo (isset($priv_ec)? $priv_ec: '');
								?>
							</td>
						</tr>
					</table>
				</div>


        <h3><a href="#">Cuentas</a></h3>
        <div>
          <button type="button" class="btn-blue corner-all" id="addCuenta">+</button>
          <table class="tblListados corner-all8">
            <tr class="header btn-gray">
              <td>Aleas</td>
              <td>RFC Banco</td>
              <td>Cuenta</td>
              <td></td>
            </tr>
            <tbody id="datosCuenta">
              <?php if (is_array($info['cuentas']) && count($info['cuentas']) > 0): ?>
                <?php foreach ($info['cuentas'] as $key => $value): ?>
                <tr>
                  <td><input type="text" name="ccaleas[]" value="<?php echo $value->aleas; ?>" size="40"></td>
                  <td><input type="text" name="ccrfcbanco[]" value="<?php echo $value->banco_rfc; ?>" size="40"></td>
                  <td><input type="text" name="cccuenta[]" value="<?php echo $value->cuenta; ?>" size="40"></td>
                  <td><img src="<?php echo base_url('application/images/privilegios/delete.png') ?>" class="removeCuenta" width="10" height="10" style="cursor: pointer"></td>
                </tr>
                <?php endforeach ?>
              <?php endif ?>
            </tbody>
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

<style type="text/css">
	.label_frm_msg{
		font-size: 9px;
		color: #FD8A8A;
	}
</style>