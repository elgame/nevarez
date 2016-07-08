$(function(){
	//evento del buscador de productos
	$("#buscar_pr").on('keydown', function(event){
		if(event.which == 13){
			event.preventDefault();
			buscarProductos(0);
		}
	});
	
	//seleccionar productos registrados
	$(document).on('click', ".tr-producreg", selProductoRegistrado);
	//seleccionar productos de consumo
	$(document).on('click', ".tr-produclista", selProductoConsumo);
	
	//agregar productos a la lista de consumo
	$(".btnaddpro").on("click", addProductoLista);
	//quita productos de la lista de consumo
	$(".btnquitpro").on("click", quitProductoLista);
});


/**
 * Selecciona un producto de la lista de productos de consumo
 * para quitarlo
 */
function selProductoConsumo(){
	$(".tr-produclista").removeClass("tractiva-pl");
	$(this).addClass("tractiva-pl");
}

/**
 * Selecciona un producto de la lista de productos registrados,
 * para agregarlos a consumos
 */
function selProductoRegistrado(){
	$(".tr-producreg").removeClass("tractiva-pr");
	$(this).addClass("tractiva-pr");
}

/**
 * Agrega un producto registrado al listado de productos
 * de consumo
 */
function addProductoLista(){
	var trsel = $(".tractiva-pr"), tagid="";
	if(trsel.length > 0){ //valida q este seleccionado uno
		tagid = trsel.attr("data-id").replace(".", "-");
		
		if($("#tr-pl"+tagid).length == 0){ //valida q no exista en la lista
			$("#tbl-pl").append(
			'<tr id="tr-pl'+tagid+'" class="tr-produclista">'+
			'	<td class="w80">'+$("td:first", trsel).text()+'<input type="hidden" name="dpcnombres[]" value="'+$("td:first", trsel).text()+'">'+
			'		<input type="hidden" name="dpcids[]" value="'+trsel.attr("data-id")+'"></td>'+
			'	<td class="w20"><input type="text" name="dpccantidad[]" value="1" size="5"></td>'+
			'</tr>');
		}else
			msb.info("El producto ya está agregado a la lista.");
		
		$(".tr-producreg").removeClass("tractiva-pr");
	}else
		msb.info("Selecciona un producto de la lista -Productos registrados-");
}

function quitProductoLista(){
	var trsel = $(".tractiva-pl");
	if(trsel.length > 0){
		trsel.remove();
		//$(".tr-produclista").removeClass("tractiva-pl");
	}else
		msb.info("Selecciona un producto de la lista -Productos que consume-");
}

/**
 * Busca productos registrados en agregar y modificar productos
 * @param pag
 */
function buscarProductos(pag){
	pag = parseInt(pag);
	var txtbuscar = $("#buscar_pr").val(), pagin = (pag>0? '&pag='+pag: ''), modf=$("#id_producto").val();
	
	modf = (modf != undefined? '&id_producto='+modf: '');
	
	$.get(base_url+"panel/productos/ajax_productos_addmod/",
		"fnombre="+txtbuscar+pagin+modf,
		function(data){
			$("#tbl_productos_r").html(data);
	});
}

