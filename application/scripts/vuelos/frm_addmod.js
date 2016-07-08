$(function(){
	$('#dfecha').datetimepicker({
		 dateFormat: 'yy-mm-dd', //formato de la fecha - dd,mm,yy=dia,mes,año numericos  DD,MM=dia,mes en texto
		 //minDate: '-2Y', maxDate: '+1M +10D', //restringen a un rango el calendario - ej. +10D,-2M,+1Y,-3W(W=semanas) o alguna fecha
		 changeMonth: true, //permite modificar los meses (true o false)
		 changeYear: true, //permite modificar los años (true o false)
		 //yearRange: (fecha_hoy.getFullYear()-70)+':'+fecha_hoy.getFullYear(),
		 numberOfMonths: 1, //muestra mas de un mes en el calendario, depende del numero
	});
	
	$("#dcliente").autocomplete({
		source: base_url+'panel/clientes/ajax_get_clientes',
		minLength: 1,
		selectFirst: true,
		select: function( event, ui ) {
			split = ui.item.id.split('.');
			id=split[0]+''+split[1];
			opc_elimi = '<a href="javascript:void(0);" class="linksm"'+ 
							'onclick="msb.confirm(\'Estas seguro de eliminar el Cliente?\', \''+id+'\', eliminaCliente); return false;">'+
							'<img src="'+base_url+'application/images/privilegios/delete.png" width="10" height="10"></a>';
			
			tr = '<tr id="'+id+'"><td>'+ui.item.item.nombre_fiscal+'</td><td>'+createInfoCliente(ui.item.item)+'</td><td>'+opc_elimi+'</td></tr>';
			$('#tbl_clientes tr:first').after(tr);
			
			hidde = '<input type="hidden" name="hids[]" value="'+ui.item.id+'" id="'+id+'">';
			$('#hidde-ids').append(hidde);
			$("#dcliente").css("background-color", "#B0FFB0");
		}
	});
	
	// $('#dfecha').datetimepicker('setDate', (new Date()));
	
	$("#davion").autocomplete({
		source: base_url+'panel/aviones/ajax_get_aviones',
		minLength: 1,
		selectFirst: true,
		select: function( event, ui ) {
			$("#havion").val(ui.item.id);
			$("#davion_info").val(createInfoAvion(ui.item.item));
			$("#davion").css("background-color", "#B0FFB0");
		}
	});
	
	$("#dpiloto").autocomplete({
		source: base_url+'panel/pilotos/ajax_get_pilotos',
		minLength: 1,
		selectFirst: true,
		select: function( event, ui ) {
			$("#hpiloto").val(ui.item.id);
			$("#dpiloto_info").val(createInfoPiloto(ui.item.item));
			$("#hcosto_piloto").val(ui.item.item.precio_vuelo);
			$("#dPiloto").css("background-color", "#B0FFB0");
		}
	});
	
	$("input[type=text]").not('.not').on("keydown", function(event){
		if(event.which == 8 || event == 46){
			var input = this.id;
			var hidde = 'h'+input.substr(1);
			$("#"+hidde).val("");
			$("#"+input+"_info").val("");
			$("#"+input).val("").css("background-color", "#FFD9B3");
		}
	});
	
});

/**
 * Crea una cadena con la informacion del proveedor para mostrarla
 * cuando se seleccione
 * @param item
 * @returns {String}
 */
function createInfoCliente(item){
	var info = '';
	info += item.calle!=''? item.calle: '';
	info += item.no_exterior!=''? ' #'+item.no_exterior: '';
	info += item.no_interior!=''? '-'+item.no_interior: '';
	info += item.colonia!=''? ', '+item.colonia: '';
	info += "\n"+(item.localidad!=''? item.localidad: '');
	info += item.municipio!=''? ', '+item.municipio: '';
	info += item.estado!=''? ', '+item.estado: '';
	return info;
}

/**
 * Crea una cadena con la informacion del avion para mostrarla
 * cuando se seleccione
 * @param item
 * @returns {String}
 */
function createInfoAvion(item){
	var info = '';
	info += item.matricula!=''? 'Matricula:'+item.matricula: '';
	info += "\n"+(item.modelo!=''? 'Modelo:'+item.modelo: '');
	info += item.tipo!=''? ', Tipo:'+item.tipo: '';
	return info;
}

/**
 * Crea una cadena con la informacion del proveedor para mostrarla
 * cuando se seleccione
 * @param item
 * @returns {String}
 */
function createInfoPiloto(item){
	var info = '';
	info += item.licencia_avion!=''? "Licencia: "+item.licencia_avion: '';
	info += item.vencimiento_licencia_a!=''? ", Fecha Venc: "+item.vencimiento_licencia_a: '';
	info += "\n"+(item.calle!=''? ', '+item.calle: '');
	info += item.no_exterior!=''? ' #'+item.no_exterior: '';
	info += item.no_interior!=''? '-'+item.no_interior: '';
	info += item.colonia!=''? ', '+item.colonia: '';
	info += "\n"+(item.localidad!=''? item.localidad: '');
	info += item.municipio!=''? ', '+item.municipio: '';
	info += item.estado!=''? ', '+item.estado: '';
	return info;
}

function eliminaCliente(id){
	$("tr#"+id).remove();
	$("input#"+id).remove();
}