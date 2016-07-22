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
<div>
	<div class="titulo ajus w100 am-c"><?php echo  $seo['titulo']; ?></div>
	<form action="<?php echo  base_url('panel/nomina/abono_piloto/?'.String::getVarsLink(array('msg')));?>" method="post">
		<div class="frmsec-left w90 f-l">
			<p class="f-l w30">
				<label for="ffecha">*Fecha</label><br>
				<input type="text" name="ffecha" id="ffecha" value="<?php echo  set_value('ffecha')!='' ? set_value('ffecha'): date("Y-m-d") ; ?>" size="20" maxlength="40" readonly>
			</p>
			<p class="f-r w70">		
				<table class="tblListados corner-all8" style="width:40% !important;margin-right:0px;">
						<tr>
							<td style="text-align:right;">Total de Vuelos</td>
							<td id="ta_total" class="a-r" style="background-color:#ccc;"><?php echo String::formatoNumero($total->total); ?></td>
						</tr>
						<tr>
							<td style="text-align:right;">Abonado</td>
							<td id="ta_total" class="a-r" style="background-color:#ccc;"><?php echo String::formatoNumero($total->abonado); ?></td>
						</tr>
						<tr>
							<td style="text-align:right;">Saldo</td>
							<td id="ta_total" class="a-r" style="background-color:#ccc;"><?php echo String::formatoNumero($total->restante); ?></td>
						</tr>
						
				</table>
			</p>
			<p>
				<label for="fabono">*Total a Abonar</label><br>
				<input type="text" name="fabono" id="fabono" class="vpositive" size="30" value="<?php echo set_value('fabono')?>">
			</p>
			<p class="f-l w100">
				<label for="fconcepto">*Concepto</label><br>
				<textarea name="fconcepto" rows="4" cols="59" autofocus><?php echo  set_value('fconcepto')!='' ? set_value('fconcepto') : "Abono para piloto" ; ?></textarea>
			</p>
			<input type="submit" name="enviar" value="Guardar" class="btn-blue corner-all f-r">
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

</body>
</html>