
<form action="<?php echo base_url('panel/home/login'); ?>" method="post" class="frmlogin corner-all8">
	<div class="title">Iniciar sesión</div>
	<p>
		<label for="usuario">Usuario</label> <br>
		<input type="text" name="usuario" id="usuario" value="<?php echo set_value('usuario'); ?>" size="31" autofocus>
	</p>
	<p>
		<label for="pass">Contraseña</label> <br>
		<input type="password" name="pass" id="pass" size="31">
	</p>
	<input type="submit" name="enviar" value="Iniciar sesión" class="btn-blue corner-all f-r">
</form>

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
