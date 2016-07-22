
<div id="contentAll" class="f-l">
	
	<form action="<?php echo base_url('panel/nomina/empleados/')?>" method="POST" id="form-data" class="frmfiltros" style="border:none;">
		<div class="frmfiltros corner-all8 btn-gray">
			<label for="fanio">Año:</label> 
			<input type="number" name="fanio" id="fanio" value="<?php echo $this->input->post('fanio'); ?>" class="a-c vpos-int">
			
			<select name="fsemana" id="fsemana">
				<?php foreach ($semanas as $s) {?>
					<option value="<?php echo $s['semana']; ?>" <?php echo (($_POST['fsemana']==$s['semana'])?'selected':''); ?>>
						<?php echo 'Semana '.$s['semana'].', DEL '.$s['fecha_inicio'].' AL '.$s['fecha_final'] ?></option>
				<?php } ?>
			</select>
		</div>
	<?php if (!$lista['historial']) { ?>
			<p class="w100" style="font-weight: bold;">
			<!-- <label class="f-l"><input type="checkbox" id="semana">Puntualidad |</label>  -->
			<label class="f-l"><input type="checkbox" id="vacaciones">Vacaciones | </label> 
			<label class="f-l"><input type="checkbox" id="aguinaldo">Aguinaldo</label>
			<input type="submit" name="guardar" value="Guardar nomina de la semana actual" class="btn-blue corner-all f-r">
			<div class="clear"></div>
		</p>
	<?php }?>
	
	<table class="tblListados corner-all8 a-c" style="font-size:0.7em;">
		<tr class="header btn-gray">
			<td class="header-sema">P. Puntualidad </td>
			<td <?php echo ($lista['historial'])?'':'style="display:none;"' ?> class="header-vaca">Activar Vacaciones</td>
			<td <?php echo ($lista['historial'])?'':'style="display:none;"' ?> class="header-agui">Activar Aguinaldo</td>
			<td>Lugar Eficiencia??</td>
			<td>A. Paterno</td>
			<td>A. Materno</td>
			<td>Nombre</td>
			<td>CURP</td>
			<td>Fecha Ingreso</td>
			<td>Dias Trabajados</td>
			<td>Salario Diario</td>
			<td>Sueldo Semanal</td>
			<td class="header-sema-total">Premio Puntualidad</td>
			<td>Premio Eficiencia</td>
			<td <?php echo ($lista['historial'])?'':'style="display:none;"' ?> class="header-vaca-total">Total de Vacaciones</td>
			<td <?php echo ($lista['historial'])?'':'style="display:none;"' ?> class="header-agui-total">Total de Aguinaldo</td>
			<td>Neto a Pagar</td>
		</tr>
		<?php
		$total_salario = 0;
		$total_semanal = 0;

		$ttotal_puntualidad = 0;
		$ttotal_eficiencia = 0;
		$ttotal_vacaciones = 0;
		$ttotal_aguinaldo = 0;

		foreach($lista['empleados'] as $emp){
			$total_salario += $emp->salario;
			$total_semanal += ($lista['historial'])?$emp->salario*$emp->dias_trabajados:$emp->salario*$emp->dias_trabajados;

			if (!$lista['historial']) {
				$ttotal_puntualidad += ($emp->dias_trabajados==7)?100:0;
			}
			

			if ($lista['historial']) {
				$ttotal_puntualidad	+= $emp->premio_puntualidad;
				$ttotal_eficiencia	+= $emp->premio_eficiencia;
				$ttotal_vacaciones	+= $emp->vacaciones;
				$ttotal_aguinaldo		+= $emp->aguinaldo;
			}

			?>
				<tr id="<?php echo str_replace('.', '-', $emp->id_empleado)?>">
					<td id="semana"> 
							<?php echo ($lista['historial'])?'<input type="checkbox" id="semana-single" '.(($emp->premio_puntualidad != 0)?'checked':'').' disabled/>':
							'<input type="checkbox" id="semana-single" '.(($emp->dias_trabajados==7 && $emp->retardos == 0)?"checked":"").' />'?> 
					</td>
					<td id="vacaciones" <?php echo ($lista['historial'])?'':'style="display:none;"' ?>> 
							<?php echo ($lista['historial'])?'<input type="checkbox" id="vacaciones-single" '.(($emp->vacaciones != 0)?'checked':'').' disabled/>':'' ?> 
					</td>
					<td id="aguinaldo" <?php echo ($lista['historial'])?'':'style="display:none;"' ?>> 
							<?php echo ($lista['historial'])?'<input type="checkbox" id="aguinaldo-single" '.(($emp->aguinaldo != 0)?'checked':'').' disabled/>':'' ?> 
					</td>
					<td>
						<select id="eficiencia" <?php echo ($lista['historial'])?'disabled':'' ?> >
							<option value="0" <?php echo (($lista['historial'])?($emp->premio_eficiencia==0?'selected':''):'') ?>>Ninguno</option>
							<option value="1" <?php echo (($lista['historial'])?($emp->premio_eficiencia==200?'selected':''):'') ?>>1er Lugar</option>
							<option value="2" <?php echo (($lista['historial'])?($emp->premio_eficiencia==100?'selected':''):'') ?>>2do Lugar</option>
						</select>
					</td>
					<td>
							<input type="hidden" name="fids[]" value="<?php echo $emp->id_empleado ?>" id="fids" />
							<input type="hidden" name="ffecha_inicio[]" value="<?php echo $emp->fecha_entrada ?>" id="ffecha_inicio" />
							<input type="hidden" name="ffecha_fin[]" value="<?php echo $emp->fecha_salida ?>" id="ffecha_fin" />
							
							<input type="hidden" name="fsalario_diario[]" value="<?php echo $emp->salario ?>" id="fsalario_diario" />
							<input type="hidden" name="fsueldo_semanal[]" value="<?php echo floatval($emp->salario*$emp->dias_trabajados) ?>" id="fsueldo_semanal" />

							<input type="hidden" name="fpremio_puntualidad[]" value="<?php echo ($emp->dias_trabajados==7)?100:0 ?>" id="fpremio_puntualidad" />
							<input type="hidden" name="fpremio_eficiencia[]" value="0" id="fpremio_eficiencia" />
							<input type="hidden" name="fvacaciones[]" value="0" id="fvacaciones" />
							<!-- <input type="hidden" name="faguinaldo[]" value="0" id="faguinaldo" /> -->
							<input type="hidden" name="faguinaldo_aux[]" value="<?php echo isset($emp->aguinaldo)?$emp->aguinaldo:0;?>" id="faguinaldo_aux" />
							<input type="hidden" name="ftotal_pagar[]" value="<?php echo floatval($emp->salario*$emp->dias_trabajados + (($emp->dias_trabajados==7)?100:0) ) ?>" id="ftotal_pagar" />
						<?php echo $emp->apellido_paterno?>
					</td>
					
					<td><?php echo $emp->apellido_materno?> </td>
					<td><?php echo $emp->nombre?> </td>
					<td><?php echo $emp->curp?> </td>
					<td><?php echo $emp->fecha_entrada?> </td>
					<td>
							<input type="number" name="fdias_trabajados[]" 
										value="<?php echo ($lista['historial'])?$emp->dias_trabajados:$emp->dias_trabajados ?>" id="fdias_trabajados" class="a-c vpos-int" size="1" min="0" max="7" <?php echo ($lista['historial'])?'readonly="readonly"': (($this->empleados_model->tienePrivilegioDe('','nomina/edt/',false))?'':'readonly="readonly"') ?>/>
					</td>
					<td><?php echo String::formatoNumero($emp->salario)?> </td>
					<td id="fsueldo_semanal">
						<?php echo String::formatoNumero(($lista['historial'])?$emp->sueldo_semanal:$emp->salario * $emp->dias_trabajados) ?> 
					</td>
					<td id="total_premio_puntialidad" style="background: rgba(108, 182, 255, .2);">
						<?php echo ($lista['historial'])?String::formatoNumero($emp->premio_puntualidad):(($emp->dias_trabajados==7)?String::formatoNumero(100):String::formatoNumero(0)); ?>
					</td>
					<td id="total_premio_eficiencia" style="background: rgba(108, 182, 255, .2)">
						<?php echo String::formatoNumero(($lista['historial'])?$emp->premio_eficiencia:0); ?> 
					</td>
					<td id="total_vaca" style="<?php echo ($lista['historial'])?'':'display: none;'?> background: rgba(108, 182, 255, .2)">
						<?php echo ($lista['historial'])?String::formatoNumero($emp->vacaciones):''; ?> 
					</td>
					<td id="total_agui" style="<?php echo ($lista['historial'])?'':'display: none;'?>background: rgba(108, 182, 255, .2)">
						<input type="text" name="faguinaldo[]" value="<?php echo ($lista['historial'])?$emp->aguinaldo:0 ?>" id="faguinaldo" class="a-c vpositive" size="4" <?php echo ($lista['historial'])?'readonly':'' ?> readonly/>
					</td>
					<td id="ftotal_pagar" style="font-weight: bold;background: rgba(108, 182, 255, .2)">
						<?php echo ($lista['historial'])?String::formatoNumero($emp->total_pagar):String::formatoNumero($emp->salario * $emp->dias_trabajados + (($emp->dias_trabajados==7)?100:0) ); ?>
					</td>
				</tr>		
					
		<?php } $ttotal_pagar = $total_semanal + $ttotal_puntualidad + $ttotal_eficiencia + $ttotal_vacaciones + $ttotal_aguinaldo;?>
				<tr style="background-color:#ccc;font-weight: bold;" id="ttotales">
					<td colspan="<?php echo ($lista['historial'])?'10':'8'?>">
						<input type="hidden" name="ttotal_salario" value="<?php echo $total_salario ?>" id="ttotal_salario" />
						<input type="hidden" name="ttotal_semanal" value="<?php echo $total_salario*$emp->dias_trabajados ?>" id="ttotal_semanal" />
						<input type="hidden" name="ttotal_pagar" value="<?php echo $total_salario*$emp->dias_trabajados ?>" id="ttotal_pagar" />
					</td>
					<td id="ttotal_salario"><?php echo String::formatoNumero($total_salario); ?></td>
					<td id="ttotal_semanal"><?php echo String::formatoNumero($total_semanal); ?></td>
					<td id="ttotal_puntualidad"><?php echo String::formatoNumero(($lista['historial'])?$ttotal_puntualidad:$ttotal_puntualidad); ?></td>
					<td id="ttotal_eficiencia"><?php echo String::formatoNumero(($lista['historial'])?$ttotal_eficiencia:0); ?></td>
					<td id="ttotal_vacaciones" <?php echo ($lista['historial'])?'':'style="display:none;"'?>><?php echo String::formatoNumero(($lista['historial'])?$ttotal_vacaciones:0); ?></td>
					<td id="ttotal_aguinaldo" <?php echo ($lista['historial'])?'':'style="display:none;"'?>><?php echo String::formatoNumero(($lista['historial'])?$ttotal_aguinaldo:0); ?></td>
					<td id="ttotal_pagar"><?php echo String::formatoNumero(($lista['historial'])?$ttotal_pagar:$ttotal_pagar); ?></td>
				</tr>
			</table>
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