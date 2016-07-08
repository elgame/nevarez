<div id="contentAll" class="f-l">
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
		</script>

		<script FOR="GrFingerX" EVENT="SensorUnplug(id)" LANGUAGE="javascript">
			GrFingerX.CapStopCapture(id);
			document.getElementById('log').value = document.getElementById('log').value + "Stopped\n";
		</script>

		<script FOR="GrFingerX" EVENT="FingerDown(id)" LANGUAGE="javascript">
			document.getElementById('log').value = document.getElementById('log').value + "FingerDown\n";
		</script>

		<script FOR="GrFingerX" EVENT="FingerUp(id)" LANGUAGE="javascript">
			document.getElementById('log').value = document.getElementById('log').value + "FingerUp\n";
		</script>

		<script FOR="GrFingerX" EVENT="ImageAcquired(id, w, h, rawImg, res)" LANGUAGE="javascript">
			GrFingerX.CapSaveRawImageToFile(rawImg, w, h, path_img, 501);
			Start();
			if(document.getElementById('img').style.display == 'none')
				document.getElementById('img').style.display = 'block';
			CallEnroll(rawImg, w, h, res);
		</script>

		<input type="hidden" id="id_empleado" value="<?php echo $this->input->get('id'); ?>">

		<label for="">Huela 1 <input type="radio" name="huella" class="num_huella" value="1" checked></label> | 
		<label for="">Huela 2 <input type="radio" name="huella" class="num_huella" value="2"></label> | 
		<label for="">Huela 3 <input type="radio" name="huella" class="num_huella" value="3"></label>
		<div class="clear"></div>

		<img id="img" style="width: 355px;height: 390px;border: 4px solid #666;" name="refresh" />
		<script language="JavaScript" type="text/javascript">
		  <!--
		  function Start() {
		  	document.getElementById("img").src = url_img+"?ts" + encodeURIComponent( new Date().toString() );   
		  }
		  // -->
		</script> 
			
		<br/>
			  
		<textarea name="log" id = "log" rows = "10" cols = "75" ></textarea>
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