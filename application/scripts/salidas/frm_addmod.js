var importe=0, iva=0, total=0;
var tipoActual = 'av';
$(document).ready(function(){	
	
	cambiarFormTipo($("#dtipo_salida").val());

	//Cambio credito, contado
	$("#dtipo_salida").on("change", function(){
		if(total!=0){
			msb.confirm('Esta seguro de cambiar el tipo de salida? <br> Nota: Los productos seleccionados se eliminaran de la lista', $(this).val(),resetProductos);
			resetTipo();
		}
		else{cambiarFormTipo($(this).val());}
		
	});
	
	$("#davion").autocomplete({
		source: base_url+'panel/aviones/ajax_get_aviones',
		minLength: 1,
		selectFirst: true,
		select: function( event, ui ) {
			$("#did_avion").val(ui.item.id);
//			$("#davion_info").val(createInfoAvion(ui.item.item));
			$("#davion").css("background-color", "#B0FFB0");
		}
	});
	
	$("#dtrabajador").autocomplete({
		source: base_url+'panel/empleados/ajax_get_trabajadores',
		minLength: 1,
		selectFirst: true,
		select: function( event, ui ) {
			$("#did_trabajador").val(ui.item.id);
//			$("#davion_info").val(createInfoAvion(ui.item.item));
			$("#dtipo_trabajador").val(ui.item.item.tipo_trabajador);
			$("#dtrabajador").css("background-color", "#B0FFB0");
		}
	});
	
	$("#dvehiculo").autocomplete({
		source: base_url+'panel/vehiculo/ajax_get_vehiculos',
		minLength: 1,
		selectFirst: true,
		select: function( event, ui ) {
			$("#did_vehiculo").val(ui.item.id);
//			$("#davion_info").val(createInfoAvion(ui.item.item));
			$("#dvehiculo").css("background-color", "#B0FFB0");
		}
	});
	
	$("input[type=text]").not(".not").on("keydown", function(event){
		if(event.which == 8 || event == 46){
			var input = this.id;
			var hidde = 'did_'+input.substr(1);
			$("#"+hidde).val("");
//			$("#"+input+"_info").val("");
			$("#"+input).val("").css("background-color", "#FFD9B3");
		}
	});	
	
	$("#dfecha").datepicker({
		 dateFormat: 'yy-mm-dd', //formato de la fecha - dd,mm,yy=dia,mes,año numericos  DD,MM=dia,mes en texto
		 //minDate: '-2Y', maxDate: '+1M +10D', //restringen a un rango el calendario - ej. +10D,-2M,+1Y,-3W(W=semanas) o alguna fecha
		 changeMonth: true, //permite modificar los meses (true o false)
		 changeYear: true, //permite modificar los años (true o false)
		 //yearRange: (fecha_hoy.getFullYear()-70)+':'+fecha_hoy.getFullYear(),
		 numberOfMonths: 1 //muestra mas de un mes en el calendario, depende del numero
	 });
		
	$("#dfecha_entrega").datepicker({
		 dateFormat: 'yy-mm-dd', //formato de la fecha - dd,mm,yy=dia,mes,año numericos  DD,MM=dia,mes en texto
		 //minDate: '-2Y', maxDate: '+1M +10D', //restringen a un rango el calendario - ej. +10D,-2M,+1Y,-3W(W=semanas) o alguna fecha
		 changeMonth: true, //permite modificar los meses (true o false)
		 changeYear: true, //permite modificar los años (true o false)
		 //yearRange: (fecha_hoy.getFullYear()-70)+':'+fecha_hoy.getFullYear(),
		 numberOfMonths: 1 //muestra mas de un mes en el calendario, depende del numero
	 });
	
	$("#a_codigo, #a_nombre, #a_cantidad, #a_pu, #a_iva").on("keydown", function(event){
		if(event.keyCode == 13){
			event.preventDefault();
			return false;
		}
	});
		
	//evento para agregar productos
	$("#btnAddProducto").on("click", agregarProductos);
});

function cambiarFormTipo(tipo){
	if(tipo=='av'){
		tipoActual = 'av';
		$('#fields-avion').show();
		$('#fields-trabajador, #fields-vehiculo').hide();
	}
	else if(tipo=='tr'){
		tipoActual = 'tr';
		$('#fields-trabajador').show();
		$('#fields-avion, #fields-vehiculo').hide();
	}
	else if(tipo=='ve'){
		tipoActual = 've';
		$('#fields-vehiculo').show();
		$('#fields-trabajador, #fields-avion').hide();
	}
	else{
		tipoActual = 'ni';
		$(' #fields-avion, #fields-trabajador, #fields-vehiculo').hide();
	}
	limpiaProducto();
	$("#a_codigo").autocomplete( "destroy" );
	$("#a_nombre").autocomplete( "destroy" );
	$("#a_codigo").autocomplete({
		source: base_url+'panel/productos/ajax_get_productos/?tipo=codigo&asig='+tipoActual,
		minLength: 1,
		selectFirst: true,
		select: function( event, ui ) {
			$("#a_id_producto").val(ui.item.id);
			$("#a_codigo").val(ui.item.item.codigo);
			$("#a_nombre").val(ui.item.item.nombre);
			$('#a_pu').val(ui.item.item.pu);
		}
	});
	//Asigna autocomplete de Nombre Productos
	$("#a_nombre").autocomplete({
		source: base_url+'panel/productos/ajax_get_productos/?tipo=nombre&asig='+tipoActual,
		minLength: 1,
		selectFirst: true,
		select: function( event, ui ) {
			$("#a_id_producto").val(ui.item.id);
			$("#a_codigo").val(ui.item.item.codigo);
			$("#a_nombre").val(ui.item.item.nombre);
			$('#a_pu').val(ui.item.item.pu);
		}
	});
}

function resetProductos(tipo){
	cambiarFormTipo(tipo);
	resetTipo();
	$('#tbl_productos tr').not('.header').remove();
	calculaTotales();
}

function resetTipo(){
	$('#dtipo_salida').val(tipoActual);
}

/***** Productos ****/
/**
 * Agrega productos a la lista
 */
function agregarProductos(){
	var res = validaProducto();
	if(res.status){
		//Agrego el tr con la informacion del contacto agregado
		$("#tbl_productos tr.header:last").after(
		'<tr id="trp-'+res.a_id_producto.replace(".", "_")+'">'+
		'	<td>'+
		'		<input type="hidden" name="dpid_producto[]" value="'+res.a_id_producto+'">'+
		'		<input type="hidden" name="dpcantidad[]" value="'+res.a_cantidad+'">'+
		'		<input type="hidden" name="dpprecio_unitario[]" value="'+res.a_pu+'">'+
		'		<input type="hidden" name="dpimporte[]" value="'+(res.a_cantidad*res.a_pu)+'" class="dpimporte">'+
		'		<input type="hidden" name="dptaza_iva[]" value="'+res.a_iva+'">'+
		'		<input type="hidden" name="dpimporte_iva[]" value="'+(res.a_cantidad*res.a_pu*res.a_iva)+'" class="dpimporte_iva">'+
		'		<input type="hidden" name="dpcodigo[]" value="'+res.a_codigo+'">'+
		'		<input type="hidden" name="dpnombre[]" value="'+res.a_nombre+'">'+
				res.a_cantidad+'</td>'+
		'	<td>'+res.a_codigo+'</td>'+
		'	<td>'+res.a_nombre+'</td>'+
		'	<td>'+util.darFormatoNum(res.a_pu)+'</td>'+
		'	<td>'+util.darFormatoNum(res.a_cantidad*res.a_pu)+'</td>'+
		'	<td class="tdsmenu a-c" style="width: 90px;">'+
		'		<a href="javascript:void(0);" class="linksm"'+ 
		'			onclick="quitarProducto(\''+res.a_id_producto+'\');return false;">'+
		'			<img src="'+base_url+'application/images/privilegios/delete.png" width="10" height="10"> Quitar</a>'+
		'	</td>'+
		'</tr>');
		
		calculaTotales();
		limpiaProducto();
	}
}
/**
 * Quitar un producto de la lista
 * @param id
 */
function quitarProducto(id){
	$("#trp-"+id.replace(".", "_")).remove();
	calculaTotales();
}
/**
 * Calcula los tatales de la compra
 */
function calculaTotales(){
	importe=0, iva=0, total=0;
	$(".dpimporte").each(function(){
		importe += parseFloat($(this).val(), 2);
	});
	$(".dpimporte_iva").each(function(){
		iva += parseFloat($(this).val(), 2);
	});
	
	$("#dtsubtotal").val(util.darFormatoNum(importe, '', false));
	$("#dtiva").val(util.darFormatoNum(iva, '', false));
	total = util.darFormatoNum(importe+iva, '', false);
	$("#dttotal").val(total);
	
	$("#ta_subtotal").text(util.darFormatoNum(importe));
	$("#ta_iva").text(util.darFormatoNum(iva));
	$("#ta_total").text(util.darFormatoNum(importe+iva));
	
	$("#dttotal_letra").val(util.numeroToLetra.covertirNumLetras(total+""));
}
/**
 * Calcula los tatales del gasto
 */
function calculaTotalesGasto(){
	var importe=0, iva=0, total=0;
	importe = parseFloat($("#dtsubtotal").val());
	importe = isNaN(importe)? 0: importe;
	iva = parseFloat($("#dtiva").val());
	iva = isNaN(iva)? 0: iva;
	
	$("#dtsubtotal").val(util.darFormatoNum(importe, '', false));
	$("#dtiva").val(util.darFormatoNum(iva, '', false));
	total = util.darFormatoNum(importe+iva, '', false);
	$("#dttotal").val(total);
	
	$("#ta_total").text(util.darFormatoNum(importe+iva));
	
	$("#dttotal_letra").val(util.numeroToLetra.covertirNumLetras(total+""));
}
/**
 * Valida los campos de agregar producto y crea el array de valores
 * para registrarlo en la lista
 * @returns {___res0}
 */
function validaProducto(){
	var msg = '', obj, res=Object;
	
	res['status'] = true;
	obj = $("#a_id_producto");
	
	if($("#trp-"+obj.val().replace(".", "_")).length > 0){ //valida q no exista en la lista
		msb.error("El producto que selecciono ya esta en la compra.");
		res['status'] = false;
		return res;
	}
	
	if($.trim(obj.val()) == ""){
		msg += "Selecciona un producto.<br>";
		res['status'] = false;
	}
	res['a_id_producto'] = obj.val();
	
	obj = $("#a_cantidad");
	if(parseFloat(obj.val()) == 0 || isNaN(parseFloat(obj.val())) ){
		msg += "Ingresa la cantidad de productos.<br>";
		res['status'] = false;
	}
	res['a_cantidad'] = obj.val();
	
	obj = $("#a_pu");
	if(parseFloat(obj.val()) == 0 || isNaN(parseFloat(obj.val())) ){
		msg += "Ingresa el precio unitario.";
		res['status'] = false;
	}
	res['a_pu'] = obj.val();
	
	res['a_codigo'] = $("#a_codigo").val();
	res['a_nombre'] = $("#a_nombre").val();
	res['a_iva'] = $("#a_iva").val();
	
	if(msg != '')
		msb.error(msg);
	
	return res;
}
/**
 * Limpia los valores del form agregar productos a la lista
 */
function limpiaProducto(){
	$("#a_id_producto").val("");
	$("#a_cantidad").val("1");
	$("#a_pu").val("0");
	$("#a_codigo").val("").focus();
	$("#a_nombre").val("");
	$("#a_iva").val("");
}

function recargar(){
	window.location = window.location.href+"?msg=4";
}

function pdf(id){
	win = window.open(base_url+'panel/salidas/imprimir/?&id='+id, 'Imprimir Salida', 'left='+((window.innerWidth/2)-240)+',top='+((window.innerHeight/2)-280)+',width=500,height=630,toolbar=0,resizable=0');
}