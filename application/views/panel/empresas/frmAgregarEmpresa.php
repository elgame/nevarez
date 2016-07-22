
<h3 class="frmsec-acordion"><a href="#">Información Facturación</a></h3>
<div>
	<p class="w50 f-l">
		<label for="dnombre_fiscal">*Nombre Fiscal: <span class="label_frm_msg">Requerido para facturar</span></label> <br>
		<input type="text" name="dnombre_fiscal" id="dnombre_fiscal" value="<?php echo (isset($empresa['info']->nombre_fiscal))? $empresa['info']->nombre_fiscal : set_value('dnombre_fiscal'); ?>" size="40" maxlength="130" autofocus>
	</p>
	<p class="w30 f-l">
		<label for="drfc">RFC:</label> <br>
		<input type="text" name="drfc" id="drfc" value="<?php echo (isset($empresa['info']->rfc))? $empresa['info']->rfc : set_value('drfc'); ?>" size="20" maxlength="13">
	</p>
	<div class="clear"></div>
	
	<p class="w40 f-l">
		<label for="dcalle">Calle:</label> <br>
		<input type="text" name="dcalle" id="dcalle" value="<?php echo (isset($empresa['info']->calle))? $empresa['info']->calle : set_value('dcalle'); ?>" size="30" maxlength="60">
	</p>
	<p class="w30 f-l">
		<label for="dno_exterior">No exterior:</label> <br>
		<input type="text" name="dno_exterior" id="dno_exterior" value="<?php echo (isset($empresa['info']->no_exterior))? $empresa['info']->no_exterior : set_value('dno_exterior'); ?>" size="20" maxlength="7">
	</p>
	<p class="w30 f-l">
		<label for="dno_interior">No interior:</label> <br>
		<input type="text" name="dno_interior" id="dno_interior" value="<?php echo (isset($empresa['info']->no_interior))? $empresa['info']->no_interior : set_value('dno_interior'); ?>" size="20" maxlength="7">
	</p>
	<div class="clear"></div>
	
	<p class="w50 f-l">
		<label for="dcolonia">Colonia:</label> <br>
		<input type="text" name="dcolonia" id="dcolonia" value="<?php echo (isset($empresa['info']->colonia))? $empresa['info']->colonia : set_value('dcolonia'); ?>" size="30" maxlength="60">
	</p>
	<p class="w50 f-l">
		<label for="dlocalidad">Localidad:</label> <br>
		<input type="text" name="dlocalidad" id="dlocalidad" value="<?php echo (isset($empresa['info']->localidad))? $empresa['info']->localidad : set_value('dlocalidad'); ?>" size="30" maxlength="45">
	</p>
	<div class="clear"></div>
	
	<p class="w50 f-l">
		<label for="dmunicipio">Municipio / Delegación:</label> <br>
		<input type="text" name="dmunicipio" id="dmunicipio" value="<?php echo (isset($empresa['info']->municipio))? $empresa['info']->municipio : set_value('dmunicipio'); ?>" size="30" maxlength="45">
	</p>
	<p class="w50 f-l">
		<label for="destado">Estado:</label> <br>
		<input type="text" name="destado" id="destado" value="<?php echo (isset($empresa['info']->estado))? $empresa['info']->estado : set_value('destado'); ?>" size="30" maxlength="45">
	</p>
	<div class="clear"></div>
	
	<p class="w50 f-l">
		<label for="dcp">CP:</label> <br>
		<input type="text" name="dcp" id="dcp" value="<?php echo (isset($empresa['info']->cp))? $empresa['info']->cp : set_value('dcp'); ?>" size="20" maxlength="10">
	</p>
	<div class="clear"></div>
	
	<p class="w50 f-l">
		<label for="dregimen_fiscal">Regimen fiscal:</label> <br>
		<input type="text" name="dregimen_fiscal" id="dregimen_fiscal" value="<?php echo (isset($empresa['info']->regimen_fiscal))? $empresa['info']->regimen_fiscal : set_value('dregimen_fiscal'); ?>" size="30" maxlength="100">
	</p>
	<p class="w50 f-l">
		<label for="dtelefono">Teléfono:</label> <br>
		<input type="text" name="dtelefono" id="dtelefono" value="<?php echo (isset($empresa['info']->telefono))? $empresa['info']->telefono : set_value('dtelefono'); ?>" size="30" maxlength="15">
	</p>
	<div class="clear"></div>
	
	<p class="w50 f-l">
		<label for="demail">Email:</label> <br>
		<input type="text" name="demail" id="demail" value="<?php echo (isset($empresa['info']->email))? $empresa['info']->email : set_value('demail'); ?>" size="30" maxlength="70">
	</p>
	<p class="w50 f-l">
		<label for="dpag_web">Pag Web:</label> <br>
		<input type="text" name="dpag_web" id="dpag_web" value="<?php echo (isset($empresa['info']->pag_web))? $empresa['info']->pag_web : set_value('dpag_web'); ?>" size="30" maxlength="80">
	</p>
	<div class="clear"></div>
    
    <p class="w40 f-l">
		<label for="dlogo">Logo:</label> <br>
		<input type="file" name="dlogo" id="dlogo" value="Seleccionar Archivo" />
	</p>
	<p class="w30 f-l">
		<label for="dcer_org">Certificado .CER:</label> <br>
		<input type="file" name="dcer_org" id="dcer_org" value="Seleccionar Archivo" />
	</p>
	<p class="w30 f-l">
		<label for="dkey_path">Llave .KEY:</label> <br>
		<input type="file" name="dkey_path" id="dkey_path" value="Seleccionar Archivo" />
	</p>
	<div class="clear"></div>
    
    <p class="w50 f-l">
		<label for="dpass">Clave:</label> <br>
		<input type="password" name="dpass" id="dpass" value="<?php echo (isset($empresa['info']->pass))? $empresa['info']->pass : set_value('dpag_web'); ?>" size="30" maxlength="20">
	</p>
	<p class="w50 f-l">
		<label for="dcfdi_version">Version CFDI:</label> <br>
		<input type="text" name="dcfdi_version" id="dcfdi_version" value="<?php echo (isset($empresa['info']->cfdi_version))? $empresa['info']->cfdi_version : set_value('dcfdi_version'); ?>" size="30" maxlength="6">
	</p>
	<div class="clear"></div>
</div>


<style type="text/css">
	.label_frm_msg{
		font-size: 9px;
		color: #FD8A8A;
	}
</style>