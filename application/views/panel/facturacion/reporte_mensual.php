<div id="contentAll" class="f-l">

	<form action="<?php echo base_url('panel/facturacion/reporte_mensual/'); ?>" method="post" class="frmfiltros corner-all8 btn-gray">
		<label for="fano">Año</label> 
		<input type="text" name="fano" id="fano" class="a-c" value="<?php echo (isset($_POST['fano']))?set_value('fano'):date('Y'); ?>" size="15" maxlength="10">
			
		<label for="fmes">Mes</label>
		<select name="fmes" id="fmes" class="a-c">
				<option value="01" <?php echo set_select('fmes', '01', false, $this->input->post('fmes') ); ?>>ENERO</option>
				<option value="02" <?php echo set_select('fmes', '02', false, $this->input->post('fmes') ); ?>>FEBRERO</option>
				<option value="03" <?php echo set_select('fmes', '03', false, $this->input->post('fmes') ); ?>>MARZO</option>
				<option value="04" <?php echo set_select('fmes', '04', false, $this->input->post('fmes') ); ?>>ABRIL</option>
				<option value="05" <?php echo set_select('fmes', '05', false, $this->input->post('fmes') ); ?>>MAYO</option>
				<option value="06" <?php echo set_select('fmes', '06', false, $this->input->post('fmes') ); ?>>JUNIO</option>
				<option value="07" <?php echo set_select('fmes', '07', false, $this->input->post('fmes') ); ?>>JULIO</option>
				<option value="08" <?php echo set_select('fmes', '08', false, $this->input->post('fmes') ); ?>>AGOSTO</option>
				<option value="09" <?php echo set_select('fmes', '09', false, $this->input->post('fmes') ); ?>>SEPTIEMBRE</option>
				<option value="10" <?php echo set_select('fmes', '10', false, $this->input->post('fmes') ); ?>>OCTUBRE</option>
				<option value="11" <?php echo set_select('fmes', '11', false, $this->input->post('fmes') ); ?>>NOVIEMBRE</option>
				<option value="12" <?php echo set_select('fmes', '12', false, $this->input->post('fmes')); ?>>DICIEMBRE</option>			
		</select>
		
		<input type="submit" name="enviar" value="Enviar" class="btn-blue corner-all">
	</form>
	
	<form action="<?php echo base_url('panel/facturacion/reporte_mensual/'); ?>" method="post" class="frmfiltros corner-all8 btn-gray">
		<span class="f-l" style="color: #A33800;font-weight: bold;"><?php echo isset($status)?$status:''?></span>
		<?php if(isset($s_descargar)){?>
			<a href="<?php echo base_url('panel/facturacion/pdf_rm/?fano='.$_POST['fano'].'&fmes='.$_POST['fmes'])?>" class="f-r" style="margin-right: 10px;" target="_BLANK">
				<img src="<?php echo base_url('application/images/privilegios/pdf.png')?>" title="Pdf" />
			</a>
			<a href="<?php echo base_url('panel/facturacion/descargar_rm/?fano='.$_POST['fano'].'&fmes='.$_POST['fmes'])?>" class="f-r" style="margin-right: 10px;" target="_BLANK">
				<img src="<?php echo base_url('application/images/privilegios/descargar.png')?>" title="Descargar"/>
			</a>
		<?php }?>
		<textarea rows="25" name="str_facturas" class="w100" readonly><?php echo isset($cadena)?$cadena:''?></textarea>
		<input type="hidden" name="freporte" id="freporte" class="a-c" value="1" >
		<input type="hidden" name="fano" id="freporte" class="a-c" value="<?php echo set_value('fano'); ?>" >
		<input type="hidden" name="fmes" id="freporte" class="a-c" value="<?php echo $this->input->post('fmes'); ?>" >
		<br>
		<?php if(isset($s_generar)){?>
			<input type="submit" name="send" value="Generar Reporte" class="btn-green corner-all">
		<?php }?>
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

		<?php 
			if(isset($load)){
				echo 'window.setTimeout(parent.refresh, 1200);';
			}
		?>
	
});
</script>
<?php }
}?>
<!-- Bloque de alertas -->