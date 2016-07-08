var taza_iva = 0;
var subtotal = 0;
var iva = 0;
var total = 0;
var aux_varios_clientes = false;

var cont_aux_clientes = 0;  // Controla el total de vuelos agregados a la tabla.

var vuelos_selec = {}; // almacena los vuelos que han sido agregados
var vuelos_data = {}; //almacena la informacion de los vuelos que seran enviados por POST
var indice = 0; // indice para controlar los vuelos y productos q han sido agregados

var productos_data = {}; //almacena la informacion de los productos que seran enviados por POST

var post = {}; // Contiene todos los valores del ticket q se pasaran por POST

$(document).ready(function(){
	//Si existe entro a la opcion de ver compra
	//de lo contrario entra a agregar compra o gasto
	
	$("#dfecha").datepicker({
		 dateFormat: 'yy-mm-dd', //formato de la fecha - dd,mm,yy=dia,mes,año numericos  DD,MM=dia,mes en texto
		 //minDate: '-2Y', maxDate: '+1M +10D', //restringen a un rango el calendario - ej. +10D,-2M,+1Y,-3W(W=semanas) o alguna fecha
		 changeMonth: true, //permite modificar los meses (true o false)
		 changeYear: true, //permite modificar los años (true o false)
		 //yearRange: (fecha_hoy.getFullYear()-70)+':'+fecha_hoy.getFullYear(),
		 numberOfMonths: 1 //muestra mas de un mes en el calendario, depende del numero
	 });
	
	//Asigna autocomplete de Proveedores
	$("#dproveedor").autocomplete({
		source: base_url+'panel/proveedores/ajax_get_proveedores/?t=pi',
		minLength: 1,
		selectFirst: true,
		select: function( event, ui ) {
			$("#did_proveedor").val(ui.item.id);
			$("#dproveedor_info").val(createInfoProveedor(ui.item.item));
			$("#dplazo_credito").val(ui.item.item.dias_credito);
			$("#dproveedor").css("background-color", "#B0FFB0");
			$('.addv').html('<a href="'+base_url+'panel/vuelos/vuelos_piloto/?id='+ui.item.id+'" id="btnAddVuelo" class="linksm f-r" style="margin: 10px 0 20px 0;" rel="superbox[iframe][800x600]"> <img src="'+base_url+'application/images/privilegios/add.png" width="16" height="16"> Agregar vuelos</a>');
			$.superbox();
		}
	});
	
	$("#dproveedor").on("keydown", function(event){
		if(event.which == 8 || event == 46){
			$("#did_proveedor").val("");
			$("#dproveedor_info").val("");
			$("#dproveedor").val("").css("background-color", "#FFD9B3");
			$('.addv').html('<a href="javascript:void(0);" id="btnAddVuelo" class="linksm f-r" style="margin: 10px 0 20px 0;" onclick="alerta(\'Seleccione un Cliente !\');"> <img src="'+base_url+'application/images/privilegios/add.png" width="16" height="16"> Agregar vuelos</a>');
		}
	});
	
	$('#submit').on('click',function(){
		ajax_submit_form();
	});
});

/**
 * Crea una cadena con la informacion del proveedor para mostrarla
 * cuando se seleccione
 * @param item
 * @returns {String}
 */
function createInfoProveedor(item){
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

function ajax_get_total_vuelos(data, tipo){
	loader.create();
	$.post(base_url+'panel/compras/ajax_get_total_vuelos/', data, function(resp){

		if(resp.vuelos){
			for(var v in resp.tipos_v){
				var opc_elimi = '';
				vuelos_data[indice] = {};
				vuelos_selec[indice] = [];
				cont_aux_clientes++;
				vivat = 0;
				for(var i in resp.vuelos){
					if(resp.vuelos[i].id_vuelo==resp.tipos_v[v].id_vuelo){
						vuelos_selec[indice].push(resp.vuelos[i].id_vuelo);
						vuelos_data[indice]['vuelo'+i] = {};
						vuelos_data[indice]['vuelo'+i].id_vuelo = resp.vuelos[i].id_vuelo;
						vuelos_data[indice]['vuelo'+i].cantidad = resp.tipos_v[v].cantidad;
						vuelos_data[indice]['vuelo'+i].taza_iva = resp.tipos_v[v].taza_iva;
						vuelos_data[indice]['vuelo'+i].precio_unitario = resp.tipos_v[v].p_uni;
						vuelos_data[indice]['vuelo'+i].importe = parseFloat(resp.tipos_v[v].importe,2);
						vuelos_data[indice]['vuelo'+i].importe_iva = parseFloat(resp.tipos_v[v].importe_iva, 2);
						vuelos_data[indice]['vuelo'+i].total = parseFloat(resp.tipos_v[v].total,2);
						
						vivat 		= parseFloat(resp.tipos_v[v].importe_iva, 2);
					}
				}
				
				subtotal	+= parseFloat(resp.tipos_v[v].importe, 2);
				iva			+= parseFloat(vivat, 2);
				total		= parseFloat(subtotal+iva, 2);
				
				vals= '{indice:'+indice+',importe:'+parseFloat(resp.tipos_v[v].importe, 2)+', iva:'+vivat+', tipo:'+tipo+'}';
				
				opc_elimi = '<a href="javascript:void(0);" class="linksm"'+ 
					'onclick="msb.confirm(\'Estas seguro de eliminar el vuelo?\', '+vals+', eliminaVuelos); return false;">'+
					'<img src="'+base_url+'application/images/privilegios/delete.png" width="10" height="10">Eliminar</a>';
				
				//Agrego el tr con la informacion del vuelo agregado
				$("#tbl_vuelos tr.header:last").after(
				'<tr id="e'+indice+'">'+
				'	<td>'+resp.tipos_v[v].cantidad+'</td>'+
//				'	<td>'+resp.tipos_v[v].codigo+'</td>'+
//				'	<td>'+resp.tipos_v[v].descripcion+'</td>'+
				'	<td>$'+resp.tipos_v[v].p_uni+'</td>'+
				'	<td>$'+resp.tipos_v[v].importe+'</td>'+
				'	<td class="tdsmenu a-c" style="width: 90px;">'+
				'		<img alt="opc" src="'+base_url+'application/images/privilegios/gear.png" width="16" height="16">'+
				'		<div class="submenul">'+
				'			<p class="corner-bottom8">'+
									opc_elimi+
				'			</p>'+
				'		</div>'+
				'	</td>'+
				'</tr>');
				
				updateTablaPrecios();
				
				indice++;
			}
		}
	}, "json").complete(function(){ 
    	loader.close();
    });
}

function ajax_submit_form(){
	post.tpiloto	= $('#did_proveedor').val();
	post.tserie		= $('#dserie').val();
	post.tfolio		= $('#dfolio').val();
	post.tfecha		= $('#dfecha').val();
	post.tconcepto	= $('#dconcepto').val();
//	post.tipo_pago	= $('#dtipo_pago').val();
//	post.tdias_credito = $('#hdias_credito').val();
	post.subtotal		= parseFloat(subtotal,2);
	post.iva			= parseFloat(iva,2);
	post.total			= parseFloat(total,2);
	
	var count=0;
	for(var i in vuelos_selec)
		for(var x in vuelos_selec[i])
			count++;
	if(count>0)
		post.vuelos	= count;
	
	cont=1;
	for(var i in vuelos_data){
		for(var x in vuelos_data[i]){
			post['pvuelo'+cont]	= {};
			post['pvuelo'+cont]	= vuelos_data[i][x];
			cont++;
		}
	}
	
//	for(var i in productos_data){
//		for(var x in productos_data[i]){
//			post['pvuelo'+cont]	= {};
//			post['pvuelo'+cont]	= productos_data[i][x];
//			cont++;
//		}
//	}
	
	loader.create();
	$.post(base_url+'panel/compras/ajax_agrega_gasto_piloto/', post, function(resp){
		
		create("withIcon", {
			title: resp.msg.title, 
			text: resp.msg.msg, 
			icon: base_url+'application/images/alertas/'+resp.msg.ico+'.png' });
		if(resp.msg.ico == 'ok'){
			//si es OK se elimina el row form
			$('#tbl_vuelos tr').not('.header').remove();
		}
		if(resp[0]){
//			$('#dfolio').val(resp.folio);
			limpia_campos();
			updateTablaPrecios();
//			print_T = window.open(base_url+'panel/tickets/imprime_ticket/?&id='+resp.id_ticket+'', 'Imprimir Ticket', 'left='+((window.innerWidth/2)-210)+',top='+((window.innerHeight/2)-200)+',width=440,height=500,toolbar=0,resizable=0');
		}
	}, "json").complete(function(){ 
    	loader.close();
    });
}

function limpia_campos(){
	$('#dproveedor').val('').css('background','#FFF');
	$('#dproveedor_info').val('');
	$('#did_proveedor').val('');
	$('#dconcepto').val('');
	$('#dserie').val('');
	$('#dfolio').val('');
	
//	$('#dfecha').val(actualDate());
	
	subtotal = 0;
	iva = 0;
	total = 0;
	vuelos_selec = {};
	vuelos_data = {};
	post = {};
	indice = 0;
	
	productos_data = {};
	
	 aux_varios_clientes = false;
	 cont_aux_clientes = 0;
	 
	$('.addv').html('<a href="javascript:void(0);" id="btnAddVuelo" class="linksm f-r" style="margin: 10px 0 20px 0;" onclick="alerta(\'Seleccione un Piloto !\');"> <img src="'+base_url+'application/images/privilegios/add.png" width="16" height="16"> Agregar vuelos</a>');
}

function eliminaVuelos(vals){
	delete vuelos_selec[vals.indice];
	delete vuelos_data[vals.indice];
	$('#e'+vals.indice).remove();
	
	subtotal -= parseFloat(vals.importe,2);
	iva		 -= parseFloat(vals.iva, 2);
	total	  = parseFloat(subtotal+iva, 2);

	if(aux_varios_clientes)
		aux_varios_clientes = false;
	
	if(vals.tipo==1)
		cont_aux_clientes--;

	updateTablaPrecios();
}

function updateTablaPrecios(){
	$('#ta_subtotal').text(util.darFormatoNum(subtotal));
	$('#ta_iva').text(util.darFormatoNum(iva));
	$('#ta_total').text(util.darFormatoNum(total));
}

function alerta(msg){
	create("withIcon", {
		title: 'Avizo !',
		text: msg, 
		icon: base_url+'application/images/alertas/info.png' });
}