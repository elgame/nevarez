$(document).ready(function(){
	//Si existe entro a la opcion de ver compra
	//de lo contrario entra a agregar compra o gasto
	if($("#view").length == 0){
		$("#dfecha").datepicker({
			 dateFormat: 'yy-mm-dd', //formato de la fecha - dd,mm,yy=dia,mes,año numericos  DD,MM=dia,mes en texto
			 //minDate: '-2Y', maxDate: '+1M +10D', //restringen a un rango el calendario - ej. +10D,-2M,+1Y,-3W(W=semanas) o alguna fecha
			 changeMonth: true, //permite modificar los meses (true o false)
			 changeYear: true, //permite modificar los años (true o false)
			 //yearRange: (fecha_hoy.getFullYear()-70)+':'+fecha_hoy.getFullYear(),
			 numberOfMonths: 1 //muestra mas de un mes en el calendario, depende del numero
		 });
		
		//Cambio credito, contado
		$("#dcondicion_pago").on("change", function(){
			if($(this).val() == "cr")
				$("#vplazo_credito").show();
			else
				$("#vplazo_credito").hide();
		});
		
		//Asigna autocomplete de Proveedores
		$("#dproveedor").autocomplete({
			source: base_url+'panel/proveedores/ajax_get_proveedores',
			minLength: 1,
			selectFirst: true,
			select: function( event, ui ) {
				$("#did_proveedor").val(ui.item.id);
				$("#dproveedor_info").val(createInfoProveedor(ui.item.item));
				$("#dplazo_credito").val(ui.item.item.dias_credito);
				$("#dproveedor").css("background-color", "#B0FFB0");
			}
		});
		$("#dproveedor").on("keydown", function(event){
			if(event.which == 8 || event == 46){
				$("#did_proveedor").val("");
				$("#dproveedor_info").val("");
				$("#dproveedor").val("").css("background-color", "#FFD9B3");
			}
		});
		
		//Asigna autocomplete de Codigo Productos
		$("#a_codigo").autocomplete({
			source: base_url+'panel/productos/ajax_get_productos/?tipo=codigo',
			minLength: 1,
			selectFirst: true,
			select: function( event, ui ) {
				$("#a_id_producto").val(ui.item.id);
				$("#a_codigo").val(ui.item.item.codigo);
				$("#a_nombre").val(ui.item.item.nombre);
			}
		});
		//Asigna autocomplete de Nombre Productos
		$("#a_nombre").autocomplete({
			source: base_url+'panel/productos/ajax_get_productos/?tipo=nombre',
			minLength: 1,
			selectFirst: true,
			select: function( event, ui ) {
				$("#a_id_producto").val(ui.item.id);
				$("#a_codigo").val(ui.item.item.codigo);
				$("#a_nombre").val(ui.item.item.nombre);
			}
		});
		$("#a_codigo, #a_nombre, #a_cantidad, #a_pu, #a_iva").on("keydown", function(event){
			if(event.keyCode == 13){
				event.preventDefault();
				return false;
			}
		});
		
		//evento para agregar productos
		$("#btnAddProducto").on("click", agregarProductos);
		
		
		//asigna eventos para la seccion gastos
		if($("#dis_gasto").length > 0){
			$("#dtsubtotal, #dtiva").on("change", calculaTotalesGasto);
		}
	}else{ //ver compra
		if($("#tbl_productos").length > 0)
			calculaTotales();
		else
			calculaTotalesGasto();
	}
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
	var importe=0, iva=0, total=0;
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

