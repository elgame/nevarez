var taza_iva = 0;
var subtotal = 0;
var iva = 0;
var total = 0;

var tickets_selecc = {}; // almacena los tickets que han sido agregados
var tickets_data = {}; //almacena la informacion de los tickets que sera enviada por POST
var indice = 0; // indice para controlar los vuelos q han sido agregados

var post = {}; // Contiene todos los valores de la nota de venta q se pasaran por POST


$(function(){
	$('#dfecha').datepicker({
		 dateFormat: 'yy-mm-dd', //formato de la fecha - dd,mm,yy=dia,mes,año numericos  DD,MM=dia,mes en texto
		 //minDate: '-2Y', maxDate: '+1M +10D', //restringen a un rango el calendario - ej. +10D,-2M,+1Y,-3W(W=semanas) o alguna fecha
		 changeMonth: true, //permite modificar los meses (true o false)
		 changeYear: true, //permite modificar los años (true o false)
		 //yearRange: (fecha_hoy.getFullYear()-70)+':'+fecha_hoy.getFullYear(),
		 numberOfMonths: 1 //muestra mas de un mes en el calendario, depende del numero
});
	
	$("#dcliente").autocomplete({
		source: base_url+'panel/clientes/ajax_get_clientes',
		minLength: 1,
		selectFirst: true,
		select: function( event, ui ) {
			$("#hcliente").val(ui.item.id);
			$("#dcliente_info").val(createInfoCliente(ui.item.item));
			$('#hdias_credito').val(ui.item.item.dias_credito);
			$("#dcliente").css("background-color", "#B0FFB0");
			$('.addv').html('<a href="'+base_url+'panel/tickets/tickets_cliente/?id='+ui.item.id+'" id="btnAddTicket" class="linksm f-r" style="margin: 10px 0 20px 0;" rel="superbox[iframe][700x500]"> <img src="'+base_url+'application/images/privilegios/add.png" width="16" height="16">Agregar Tickets</a>');
			$.superbox();
		}
	});	
	
	$("input[type=text]").on("keydown", function(event){
		if(event.which == 8 || event == 46){
			var input = this.id;
			var hidde = 'h'+input.substr(1);
			$("#"+hidde).val("");
			$("#"+input+"_info").val("");
			$("#hdias_credito").val("");
			$("#"+input).val("").css("background-color", "#FFD9B3");
			$('.addv').html('<a href="javascript:void(0);" id="btnAddTicket" class="linksm f-r" style="margin: 10px 0 20px 0;" onclick="alerta(\'Seleccione un Cliente !\');"> <img src="'+base_url+'application/images/privilegios/add.png" width="16" height="16">Agregar Tickets</a>');
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

function ajax_get_total_tickets(data){
	loader.create();
	$.post(base_url+'panel/notas_venta/ajax_get_total_tickets/', {'tickets[]':data}, function(resp){

		if(resp.tickets){
			var opc_elimi = '';
			
			for(var i in resp.tickets){
				tickets_data[indice] = {};
				tickets_data[indice]['ticket'+i] = {};
				tickets_data[indice]['ticket'+i].id_ticket		= resp.tickets[i].id_ticket;
				tickets_data[indice]['ticket'+i].cantidad		= resp.tickets[i].cantidad;
				tickets_data[indice]['ticket'+i].taza_iva		= parseFloat(taza_iva);
				tickets_data[indice]['ticket'+i].precio_unitario= parseFloat(resp.tickets[i].precio_unitario,2);
				tickets_data[indice]['ticket'+i].importe 		= parseFloat(resp.tickets[i].precio_unitario,2);
				tickets_data[indice]['ticket'+i].importe_iva	= parseFloat(resp.tickets[i].precio_unitario*taza_iva, 2);
				tickets_data[indice]['ticket'+i].total			= parseFloat(resp.tickets[i].total_ticket,2);
			
				vals= '{indice:'+indice+', subtotal:'+resp.tickets[i].subtotal_ticket+', iva: '+resp.tickets[i].iva_ticket+', total:'+resp.tickets[i].total_ticket+'}';
				
				opc_elimi = '<a href="javascript:void(0);" class="linksm"'+ 
					'onclick="msb.confirm(\'Estas seguro de eliminar el ticket?\', '+vals+', eliminaTickets); return false;">'+
					'<img src="'+base_url+'application/images/privilegios/delete.png" width="10" height="10">Eliminar</a>';
				
				//Agrego el tr con la informacion del contacto agregado
				$("#tbl_tickets tr.header:last").after(
				'<tr id="e'+indice+'">'+
				'	<td>'+resp.tickets[i].folio+'</td>'+
				'	<td>'+resp.tickets[i].fecha+'</td>'+
				'	<td>'+resp.tickets[i].subtotal_ticket+'</td>'+
				'	<td class="tdsmenu a-c" style="width: 90px;">'+
				'		<img alt="opc" src="'+base_url+'application/images/privilegios/gear.png" width="16" height="16">'+
				'		<div class="submenul">'+
				'			<p class="corner-bottom8">'+
									opc_elimi+
				'			</p>'+
				'		</div>'+
				'	</td>'+
				'</tr>');
				
				subtotal	+= parseFloat(resp.tickets[i].subtotal_ticket, 2);
				iva			+= parseFloat(resp.tickets[i].iva_ticket, 2);//parseFloat(subtotal*taza_iva, 2);
				total		+= parseFloat(resp.tickets[i].total_ticket, 2);
				indice++;
			}
			updateTablaPrecios();
		}
	}, "json").complete(function(){ 
    	loader.close();
    });
}

function ajax_submit_form(){

	post.tcliente	= $('#hcliente').val();
	post.tfolio		= $('#dfolio').val();
	post.tfecha		= $('#dfecha').val();
	post.tipo_pago	= $('#dtipo_pago').val();
	post.tdias_credito = $('#hdias_credito').val();
	post.subtotal		= parseFloat(subtotal,2);
	post.iva			= parseFloat(iva,2);
	post.total			= parseFloat(total,2);
	
	var count=0;
	for(var i in tickets_selecc)
		for(var x in tickets_selecc[i])
			count++;
	if(count>0)
		post.tickets	= count;
	
	cont=1;
	for(var i in tickets_data){
		for(var x in tickets_data[i]){
			post['pticket'+cont]	= {};
			post['pticket'+cont]	= tickets_data[i][x];
			cont++;
		}
	}
	
	loader.create();
	$.post(base_url+'panel/notas_venta/ajax_agrega_nota_venta/', post, function(resp){
		
		create("withIcon", {
			title: resp.msg.title, 
			text: resp.msg.msg, 
			icon: base_url+'application/images/alertas/'+resp.msg.ico+'.png' });
		if(resp.msg.ico == 'ok'){
			//si es OK se elimina el row form
			$('#tbl_tickets tr').not('.header').remove();
		}
		if(resp[0]){
			$('#dfolio').val(resp.folio);
			limpia_campos();
			updateTablaPrecios();
			
			win = window.open(base_url+'panel/notas_venta/imprime_nota_venta/?&id='+resp.id_nota_venta, 'Imprimir Nota de Venta', 'left='+((window.innerWidth/2)-240)+',top='+((window.innerHeight/2)-280)+',width=500,height=630,toolbar=0,resizable=0')
			
		}
	}, "json").complete(function(){ 
    	loader.close();
    });
}

function limpia_campos(){
	$('#dcliente').val('').css('background','#FFF');
	$('#dcliente_info').val('');
	$('#hcliente').val('');
	$('#hdias_credito').val('');
	$('#dfecha').val(actualDate());
	
	subtotal = 0;
	iva = 0;
	total = 0;
	tickets_selecc = {};
	tickets_data = {};
	post = {};
	indice = 0;
	
	$('.addv').html('<a href="javascript:void(0);" id="btnAddTicket" class="linksm f-r" style="margin: 10px 0 20px 0;" onclick="alerta(\'Seleccione un Cliente !\');"> <img src="'+base_url+'application/images/privilegios/add.png" width="16" height="16">Agregar Tickets</a>');
}

function eliminaTickets(vals){
	delete tickets_selecc[vals.indice];
	delete tickets_data[vals.indice];
	$('#e'+vals.indice).remove();
	
	subtotal	-= parseFloat(vals.subtotal, 2);
	iva			-= parseFloat(vals.iva, 2);
	total		-= parseFloat(vals.total, 2);
	
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
		icon: base_url+'application/images/alertas/info.png'});
}

function actualDate(time){
	var today = new Date();
	var dd = today.getDate();
	var mm = today.getMonth()+1; //January is 0!
	var yyyy = today.getFullYear();
	if(dd<10){dd='0'+dd;} if(mm<10){mm='0'+mm;}var date = yyyy+'-'+mm+'-'+dd;
	if(time){h=today.getHours();m=today.getMinutes();s=today.getSeconds();date+=' '+h+':'+m+':'+s;}
	return date;
}