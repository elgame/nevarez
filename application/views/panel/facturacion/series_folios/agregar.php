<div id="contentAll" class="f-l">
	<form action="<?php echo base_url('panel/facturacion/agregar_serie_folio');?>" method="post" class="frm_addmod" enctype="multipart/form-data">
		<div class="frmsec-left w60 f-l">
        	<p class="f-l w50">
				<label for="fidempresa">*Empresa a la que pertenece</label><br>
				<select name="fidempresa" id="fidempresa">
                	<option value=""></option>
                	<?php
						foreach($empresas as $row){
							echo '<option value="'.$row->id_empresa.'">'.$row->nombre_fiscal.'</option>';
						}
					?>
                </select>
			</p>
            <p class="clear"></p>

      <p class="f-l w50">
        <label for="ftipo">Tipo</label><br>
        <select name="ftipo" id="ftipo">
          <option value="f" <?php echo set_select('ftipo', 'f', false, $this->input->post('ftipo')); ?>>Factura</option>
          <option value="cp" <?php echo set_select('ftipo', 'cp', false, $this->input->post('ftipo')); ?>>Complemento de pago</option>
        </select>
      </p>
			<p class="f-l w50">
				<label for="fserie">Serie</label><br>
				<input type="text" name="fserie" id="fserie" value="<?php echo set_value('fserie') ?>" size="30" autofocus maxlength="30">
			</p>
			<p class="f-l w50">
				<label for="fno_aprobacion">*No Aprobación</label><br>
				<input type="text" name="fno_aprobacion" id="fno_aprobacion" value="<?php echo set_value('fno_aprobacion') ?>" size="30">
			</p>
			<p class="f-l w50">
				<label for="ffolio_inicio">*Folio Inicio</label><br>
				<input type="text" name="ffolio_inicio" id="ffolio_inicio" value="<?php echo set_value('ffolio_inicio') ?>" size="30">
			</p>
			<p class="f-l w50">
				<label for="ffolio_fin">*Folio Fin</label><br>
				<input type="text" name="ffolio_fin" id="ffolio_fin" value="<?php echo set_value('ffolio_fin') ?>" size="30">
			</p>
			<p class="f-l w50">
				<label for="fano_aprobacion">*Año Aprobación</label><br>
				<input type="text" name="fano_aprobacion" id="fano_aprobacion" value="<?php echo set_value('fano_aprobacion') ?>" size="30" maxlength="4">
			</p>
			<p class="f-l w50">
				<label for="durl_img">Imagen</label><br>
				<input type="file" name="durl_img" id="durl_img" value="<?php echo set_value('durl_img') ?>" size="30">
			</p>
			<p class="f-l w100">
				<label for="fleyenda">Leyenda</label><br>
				<input type="text" name="fleyenda" id="fleyenda" value="<?php echo set_value('fleyenda') ?>" size="72">
			</p>
			<p class="f-l w100">
				<label for="fleyenda1">Leyenda 1</label><br>
				<input type="text" name="fleyenda1" id="fleyenda1" value="<?php echo set_value('fleyenda1') ?>" size="72">
			</p>
			<p class="f-l w100">
				<label for="fleyenda2">Leyenda 2</label><br>
				<input type="text" name="fleyenda2" id="fleyenda2" value="<?php echo set_value('fleyenda2') ?>" size="72">
			</p>

			<input type="submit" name="enviar" value="Guardar" class="btn-blue corner-all f-r" style="margin-right:55px;">
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