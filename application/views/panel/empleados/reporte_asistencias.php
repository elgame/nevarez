<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<title><?php echo $seo['titulo'];?></title>
	
<?php
	if(isset($this->carabiner)){
		$this->carabiner->display('css');
		$this->carabiner->display('js');
	}
?>
<script type="text/javascript" charset="UTF-8">
	var base_url = "<?php echo base_url();?>",
	opcmenu_active = '<?php echo isset($opcmenu_active)? $opcmenu_active: 0;?>';
</script>
</head>
<body>

<form action="<?php echo base_url('panel/empleados/rda_pdf')?>" id="frmreporte" class="frm_addmod f-l w20" method="GET" target="ifrmReporte">
	<fieldset>
		<legend>Seleccione Año y Semana</legend>
		<label for="fanio">Año:</label> 
		<input type="number" name="fanio" id="fanio" value="<?php echo $this->input->get('fanio'); ?>" class="a-c vpos-int">
			
		<select name="fsemana" id="fsemana">
			<?php foreach ($semanas as $s) {?>
				<option value="<?php echo $s['semana']; ?>" <?php echo (($_GET['fsemana']==$s['semana'])?'selected':''); ?>>
					<?php echo 'Semana '.$s['semana'].', DEL '.$s['fecha_inicio'].' AL '.$s['fecha_final'] ?></option>
			<?php } ?>
		</select>
	</fieldset>
	<p class="a-c">
		<input type="submit" name="submit" class="btn-green corner-all" value="Mostrar">
	<p>
</form>

<iframe name="ifrmReporte" id="iframe-reporte" class="f-r w75" src="<?php echo base_url('panel/empleados/rda_pdf')?>">Reporte</iframe>

</body>
</html>