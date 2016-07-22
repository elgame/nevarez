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

<div id="header">
	<div class="logo f-l"><a href="<?php echo base_url("panel/home/")?>" ><img alt="logo" src="<?php echo base_url('application/images/logo.png')?>" width="150" height="60"></a></div>
	<div class="titulo f-l"><?php echo $seo['titulo'];?></div>
	<div class="info_user f-l a-r">
	</div>
	<div class="clear"></div>
</div>
<div id="contentAlls" class="am-c" style="width: 900px;">
		<OBJECT ID="GrFingerX" CLASSID="CLSID:71944DD6-B5D2-4558-AD02-0435CB2B39DF"></OBJECT>
		<script type="text/javascript">
			var i = 0,
			path_img="C:\\xampp\\htdocs\\nevarez\\application\\media\\teste.bmp",
			url_img=base_url+"application/media/teste.bmp";
		</script>

		<script FOR="GrFingerX" EVENT="SensorPlug(id)" LANGUAGE="javascript">
			GrFingerX.CapStartCapture(id);
			document.getElementById('log').value ="";
			document.getElementById('log').value = document.getElementById('log').value + "Started\n";
			document.getElementById('log').scrollTop = document.getElementById('log').scrollHeight;
		</script>

		<script FOR="GrFingerX" EVENT="SensorUnplug(id)" LANGUAGE="javascript">
			GrFingerX.CapStopCapture(id);
			document.getElementById('log').value = document.getElementById('log').value + "Stopped\n";
			document.getElementById('log').scrollTop = document.getElementById('log').scrollHeight;
		</script>

		<script FOR="GrFingerX" EVENT="FingerDown(id)" LANGUAGE="javascript">
			document.getElementById('log').value = document.getElementById('log').value + "FingerDown\n";
			document.getElementById('log').scrollTop = document.getElementById('log').scrollHeight;
		</script>

		<script FOR="GrFingerX" EVENT="FingerUp(id)" LANGUAGE="javascript">
			document.getElementById('log').value = document.getElementById('log').value + "FingerUp\n";
			document.getElementById('log').scrollTop = document.getElementById('log').scrollHeight;
		</script>

		<script FOR="GrFingerX" EVENT="ImageAcquired(id, w, h, rawImg, res)" LANGUAGE="javascript">
			GrFingerX.CapSaveRawImageToFile(rawImg, w, h, path_img, 501);
			Start();
			if(document.getElementById('img').style.display == 'none')
				document.getElementById('img').style.display = 'block';
			CallIdentify(rawImg, w, h, res);
		</script>
		 <img id="img" class="f-l" style="width: 355px;height: 390px;border: 4px solid #666;" name="refresh">
			<script language="JavaScript" type="text/javascript">
				<!--
				function Start() {
				document.getElementById("img").src = url_img+"?ts" + encodeURIComponent( new Date().toString() );   
				}
				// -->
			</script> 
			
			<div class="f-l" style="width:400px;">
				<span class="f-l" style="font-size: 3em; margin: 0 15px;">=></span>
				<span class="f-l" style="display: block;width: 300px;">
					<img src="" id="img_foto"><br>
					<span id="nombre_empleado" style="font-size: 1.3em;color: #0a0;"></span>
				</span>

			</div>
			<div class="clear"></div>
		<br />
			  
		<textarea name="log" id="log" rows="10" cols="75" ></textarea>
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
	
	<div class="clear"></div>
</body>
</html>