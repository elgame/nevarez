$(function() {
	
	$("select#fsemana, input#fanio").on("change",function(){
		$("form#form-data").submit();
	});

	$("input#fanio").on('click', '$("selector")', function(event) {
		event.preventDefault();
		// Act on the event
	});

	$('input[type="number"]#fdias_trabajados').on('change',function(){
		calculaTotales();
	});

	// $('input[type="checkbox"]#semana').on('change',function(){
	// 		muestraColumnaSemana($(this).is(':checked'));
	// });

	$('body').on('change', "input[type='checkbox']#semana-single" ,function(){
			setPremioPuntualidad(this);
	});

	$('input[type="checkbox"]#vacaciones').on('change',function(){
			muestraColumnaVacaciones($(this).is(':checked'));
	});

	$('body').on('change', "input[type='checkbox']#vacaciones-single" ,function(){
			setVacaciones(this);
	});

	$('input[type="checkbox"]#aguinaldo').on('change',function(){
			muestraColumnaAguinaldo($(this).is(':checked'));
	});

	$('body').on('change', "input[type='checkbox']#aguinaldo-single" ,function(){
			setAguinaldo(this);
	});

	$("body").on("change","input#faguinaldo",function(){
			calculaTotales();
	});

	$('select#eficiencia').on("change", function(){
		var val_selecc = $(this).val();
		var total = 0;
		var parent_id = $(this).parent().parent().attr("id");
		if (val_selecc != 0) {
			var aux = false;
			$('select#eficiencia').not(this).each(function(){
				if (val_selecc == $(this).val()) {
					aux = $(this).val();
					return false;
				};
			});
			if (aux == 1) {
				msb.info('Ya fue asignado un empleado con el 1er lugar');
				$(this).val(total);
			}
			else if (aux == 2) {
				msb.info('Ya fue asignado un empleado con el 2do lugar');
				$(this).val(total);
			}
			else {
				if (val_selecc == 1) total = 200;
				else total = 100;
			}
		}
		$('#'+parent_id+' input#fpremio_eficiencia').val(total);
		$('#'+parent_id+' td#total_premio_eficiencia').html(util.darFormatoNum(total));
		calculaTotales();
	});

});

function calculaTotales () {
	
	var parent_id, new_sueldo_semanal, new_dias, new_neto_pagar=0;

	var ttotal_semanal = 0
	var ttotal_puntualidad = 0
	var ttotal_eficiencia = 0
	var ttotal_vacaciones = 0
	var ttotal_aguinaldo = 0
	var ttotal_pagar=0;

	semanal = 0;
	puntualidad = 0;
	eficiencia = 0;
	vacaciones = 0;
	aguinaldo = 0;

	$('input[type="number"]#fdias_trabajados').each(function(){
		parent_id	= $(this).parent().parent().attr('id');
		new_dias	=  $(this).val();
		
		// Calcula el nuevo suelo semanal
		new_sueldo_semanal = parseFloat($('#'+parent_id+' input#fsalario_diario').val(),2) * parseFloat(new_dias);
		$('#'+parent_id+' input#fsueldo_semanal').val(parseFloat(new_sueldo_semanal,2));

		// Calcula Neto a pagar
		ttotal_semanal 			+=	semanal	+= parseFloat($('#'+parent_id+' input#fsueldo_semanal').val(),2);
		ttotal_puntualidad	+= puntualidad += parseFloat($('#'+parent_id+' input#fpremio_puntualidad').val(),2);
		ttotal_eficiencia		+= eficiencia += parseFloat($('#'+parent_id+' input#fpremio_eficiencia').val(),2);
		ttotal_vacaciones		+= vacaciones += parseFloat($('#'+parent_id+' input#fvacaciones').val(),2);
		ttotal_aguinaldo		+= aguinaldo += parseFloat($('#'+parent_id+' input#faguinaldo').val(),2);

		new_neto_pagar = semanal + puntualidad + eficiencia + vacaciones + aguinaldo;

		// ttotal_semanal += parseFloat(new_sueldo_semanal,2);
		ttotal_pagar	 += parseFloat(new_neto_pagar,2);

		// Asigna los nuevos valores a los input#hidden
		$('#'+parent_id+' input#ftotal_pagar').val(parseFloat(new_neto_pagar,2));

		$('input#ttotal_semanal').val(ttotal_semanal);
		$('input#ttotal_pagar').val(ttotal_pagar);

		// Actualiza los valores de la tabla
		$('#'+parent_id+' td#fsueldo_semanal').html(util.darFormatoNum(new_sueldo_semanal));
		$('#'+parent_id+' td#ftotal_pagar').html(util.darFormatoNum(new_neto_pagar));
		
		$('td#ttotal_semanal').html(util.darFormatoNum(ttotal_semanal));
		$('td#ttotal_pagar').html(util.darFormatoNum(ttotal_pagar));

		$('td#ttotal_puntualidad').html(util.darFormatoNum(ttotal_puntualidad));
		$('td#ttotal_eficiencia').html(util.darFormatoNum(ttotal_eficiencia));
		$('td#ttotal_vacaciones').html(util.darFormatoNum(ttotal_vacaciones));
		$('td#ttotal_aguinaldo').html(util.darFormatoNum(ttotal_aguinaldo));
		

		new_neto_pagar=0;
		new_sueldo_semanal=0;

		semanal = 0
		puntualidad = 0
		eficiencia = 0
		vacaciones = 0
		aguinaldo = 0
	});
	
}

// function muestraColumnaSemana (checked) {
// 	if (checked) {
// 		tdspan(1);
// 		$('.header-sema, .header-sema-total, td#semana, #ttotal_puntualidad').css({"display":""});
// 		$('td#semana').html('<input type="checkbox" id="semana-single" checked/>');

// 		$('input#fpremio_puntualidad').val(100);
// 		$('td#total_premio_puntialidad').css({"display":""}).html(util.darFormatoNum(100));
// 	}
// 	else {
// 		tdspan(-1);
// 		$('.header-sema, .header-sema-total, td#semana, #ttotal_puntualidad').css({"display":"none"});
// 		$('td#semana').html("");

// 		$('input#fpremio_puntualidad').val(0);
// 		$('td#total_premio_puntialidad').css({"display":"none"}).html(util.darFormatoNum(0));
// 	}calculaTotales();
// }

function setPremioPuntualidad (obj) {
	parent_id	= $(obj).parent().parent().attr('id');
	if ($(obj).is(":checked")) {
		$('#'+parent_id+' input#fpremio_puntualidad').val(100)
		$('#'+parent_id+' td#total_premio_puntialidad').html(util.darFormatoNum(100));
	}
	else {
		$('#'+parent_id+' input#fpremio_puntualidad').val(0);
		$('#'+parent_id+' td#total_premio_puntialidad').html(util.darFormatoNum(0));
	}calculaTotales();
}

function muestraColumnaVacaciones (checked) {
	if (checked) {
		tdspan(1);
		$('.header-vaca, .header-vaca-total, td#vacaciones, #ttotal_vacaciones').css({"display":""});
		$('td#vacaciones').html('<input type="checkbox" id="vacaciones-single"/>');
		// $('input#fvacaciones').val(100);
		$('td#total_vaca').css({"display":""}).html(util.darFormatoNum(0));
	}
	else {
		tdspan(-1);
		$('.header-vaca, .header-vaca-total, td#vacaciones, #ttotal_vacaciones').css({"display":"none"});
		$('td#vacaciones').html("");
		$('input#fvacaciones').val(0);
		$('td#total_vaca').css({"display":"none"}).html(util.darFormatoNum(0));
	}calculaTotales();
}

function setVacaciones (obj) {
	parent_id	= $(obj).parent().parent().attr('id');
	if ($(obj).is(":checked")) {
		tvacaciones = 7 * parseFloat($('#'+parent_id+' input#fsalario_diario').val());
		$('#'+parent_id+' input#fvacaciones').val(tvacaciones)
		$('#'+parent_id+' td#total_vaca').html(util.darFormatoNum(tvacaciones));
	}
	else {
		$('#'+parent_id+' input#fvacaciones').val(0);
		$('#'+parent_id+' td#total_vaca').html(util.darFormatoNum(0));
	}calculaTotales();
}

function muestraColumnaAguinaldo (checked) {
	if (checked) {
		tdspan(1);
		$('.header-agui, .header-agui-total, td#aguinaldo, #ttotal_aguinaldo').css({"display":""});
		$('td#aguinaldo').html('<input type="checkbox" id="aguinaldo-single"/>');
		// $('input#faguinaldo').val(100);
		$('td#total_agui').css({"display":""});
	}
	else {
		tdspan(-1);
		$('.header-agui, .header-agui-total, td#aguinaldo, #ttotal_aguinaldo').css({"display":"none"});
		$('td#aguinaldo').html("");
		$('input#faguinaldo').val(0).attr('readonly','readonly');
		$('td#total_agui').css({"display":"none"});
	}calculaTotales();
}

function setAguinaldo (obj) {
	parent_id	= $(obj).parent().parent().attr('id');
	if ($(obj).is(":checked")) {

		taguinaldo = $("#"+parent_id+" input#faguinaldo_aux").val();
		$('#'+parent_id+' input#faguinaldo').val(taguinaldo).removeAttr('readonly');
		// $('#'+parent_id+' td#total_agui').html(util.darFormatoNum(taguinaldo));
	}
	else {
		$('#'+parent_id+' input#faguinaldo').val(0).attr('readonly','readonly');
		// $('#'+parent_id+' td#total_agui').html(util.darFormatoNum(0));
	}calculaTotales();
}

function tdspan (val) {
	colspan = parseInt($("#ttotales td:first-child").attr('colspan')) + (val);
	$("#ttotales td:first-child").attr('colspan',colspan);
}