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
	<div class="titulo ajus w100 am-c">Enviar Documentos por email</div>
	<form action="<?php echo  base_url('panel/facturacion/enviar_documentos/?send=1&'.String::getVarsLink(array('msg')));?>" method="post">
    	<br /><br />
		<div class="frmsec-left w90 f-l">
        	<p>
        		<span>Otros emails: </span>
                 <input type="text" name="pextras" id="pextras"  placeholder="email1@gmail.com, email2@hotmail.com" style="width:340px;">
            </p>
            <p>
                <span>Email Default: </span>
                <span><?php echo ($emails_default!='')? $emails_default : '<span style="color:red;">El cliente no cuenta con emails.</span>'; ?></span>
            </p>
			<input type="submit" name="enviar" value="Enviar" class="btn-blue corner-all f-r">
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

</body>
</html>