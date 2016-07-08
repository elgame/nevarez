
<div id="contentAll" class="f-l">
	<form action="<?php echo base_url('panel/listas_precio'); ?>" method="get" class="frmfiltros corner-all8 btn-gray">
		<label for="fnombre">Nombre:</label> 
		<input type="text" name="fnombre" id="fnombre" value="<?php echo $this->input->get('fnombre'); ?>" autofocus>
		
		<label for="ffamilia">Familia:</label>
		<select name="ffamilia" id="ffamilia">
			<option value="0">Todos</option>
	<?php foreach($familias['familias'] as $row){ 
			echo '<option value="'.$row->id_familia.'"'.set_select_get('ffamilia', $row->id_familia).'>'.$row->nombre.'</option>';
	}?>
		</select>
		
		<input type="submit" name="enviar" value="Enviar" class="btn-blue corner-all">
		
		<a href="" id="lnkImprimir" class="btn-green corner-all" target="_blank">Imprimir</a>
	</form>

<?php
	
	//Creamos la tabla en html
	//checo si tiene permiso de editar precios
	$editar = $this->empleados_model->tienePrivilegioDe('', 'listas_precio/cambiar_precio/');
	$strtabla = ''; $wtabla = count($tbl_precios['tabla'][0])*120+250;
	foreach($tbl_precios['tabla'] as $key => $rows){
		$strtabla .= '<tr'.($key==0? ' class="header btn-gray"': '').'>';
		foreach($rows as $key2 => $cols){
			if($key2==0) //codigo producto
				$attr = ' class="a-c" style="width:120px;"';
			elseif($key2==1) //nombre producto
				$attr = ' style="width:350px;"';
			else{ //listas precios
				$cols = explode('|', $cols); //id_producto|precio|id_lista
				
				$attr = ' class="a-c" style="width:120px;"';
				if($editar && $key != 0){
					$cols[1] = '$<input type="text" data-producto="'.$cols[0].'" data-lista="'.$cols[2].'" 
						class="vpositive col-updatechange" value="'.$cols[1].'"  maxlength="9" size="7">';
				}elseif($key != 0)
					$cols[1] = String::formatoNumero($cols[1]);
				elseif($key == 0){
					$ccl = explode('|', $tbl_precios['tabla'][1][$key2]);
					$cols[0] = '<input type="checkbox" value="'.$cols[0].'|'.$ccl[2].'" name="printcheck" class="chkPrintLista">'.$cols[0];
				}
				
				$cols = isset($cols[1])? $cols[1]: $cols[0];
			}
			
			$strtabla .= '<td'.$attr.'>'.$cols.'</td>';
		}
		$strtabla .= '</tr>';
	}
?>
	<div style="overflow-x: auto;">
		<table class="tblListados corner-all8" style="width: <?php echo $wtabla; ?>px;">
			<?php echo $strtabla; ?>
		</table>
	</div>
	
<?php
//Paginacion
$this->pagination->initialize(array(
		'base_url' 			=> base_url($this->uri->uri_string()).'?'.String::getVarsLink(array('pag')).'&',
		'total_rows'		=> $tbl_precios['pag']['total_rows'],
		'per_page'			=> $tbl_precios['pag']['items_per_page'],
		'cur_page'			=> $tbl_precios['pag']['result_page']*$tbl_precios['pag']['items_per_page'],
		'page_query_string'	=> TRUE,
		'num_links'			=> 1,
		'anchor_class'		=> 'pags corner-all'
));
$pagination = $this->pagination->create_links();
echo '<div class="pagination w100">'.$pagination.'</div>'; 
?>
	
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

