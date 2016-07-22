
<div id="contentAll" class="f-l">
	<form action="<?php echo base_url('panel/empleados'); ?>" method="get" class="frmfiltros corner-all8 btn-gray">
		<label for="fnombre">Nombre:</label> 
		<input type="text" name="fnombre" id="fnombre" value="<?php echo set_value_get('fnombre'); ?>" autofocus>
		
		<label for="fstatus">Estado:</label>
		<select name="fstatus" id="fstatus">
			<option value="contratado" <?php echo set_select_get('fstatus', 'contratado'); ?>>Contratados</option>
			<option value="no_contratado" <?php echo set_select_get('fstatus', 'no_contratado'); ?>>No contratados</option>
			<option value="usuario" <?php echo set_select_get('fstatus', 'usuario'); ?>>Usuarios</option>
			<option value="todos" <?php echo set_select_get('fstatus', 'todos'); ?>>Todos</option>
		</select>
		
		<input type="submit" name="enviar" value="Enviar" class="btn-blue corner-all">
	</form>
	
	<table class="tblListados corner-all8">
		<tr class="header btn-gray">
			<td>Nombre</td>
			<td>Telefono</td>
			<td>Tipo usuario</td>
			<td>Estado</td>
			<td class="a-c">Opc</td>
		</tr>
<?php foreach($empleados['empleados'] as $emplea){ ?>
		<tr>
			<td><?php echo $emplea->e_nombre; ?></td>
			<td><?php echo $emplea->telefono; ?></td>
			<td><?php echo ucfirst($emplea->tipo_usuario); ?></td>
			<td><?php echo ucfirst($emplea->status); ?></td>
			<td class="tdsmenu a-c" style="width: 90px;">
				<img alt="opc" src="<?php echo base_url('application/images/privilegios/gear.png'); ?>" width="16" height="16">
				<div class="submenul">
					<p class="corner-bottom8">
						<?php 
						echo $this->empleados_model->getLinkPrivSm('empleados/modificar/', $emplea->id_empleado); 
						echo $this->empleados_model->getLinkPrivSm('empleados/descontratar/', $emplea->id_empleado, 
								"msb.confirm('Estas seguro de descontratar el empleado?', this); return false;");
						echo $this->empleados_model->getLinkPrivSm('asistencia/registrar_huella/', $emplea->id_empleado); 
						?>
					</p>
				</div>
			</td>
		</tr>
<?php }?>
	</table>
<?php
//Paginacion
$this->pagination->initialize(array(
		'base_url' 			=> base_url($this->uri->uri_string()).'?'.String::getVarsLink(array('pag')).'&',
		'total_rows'		=> $empleados['total_rows'],
		'per_page'			=> $empleados['items_per_page'],
		'cur_page'			=> $empleados['result_page']*$empleados['items_per_page'],
		'page_query_string'	=> TRUE,
		'num_links'			=> 1,
		'anchor_class'		=> 'pags corner-all'
));
$pagination = $this->pagination->create_links();
echo '<div class="pagination w100">'.$pagination.'</div>'; 
?>
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
