$(function(){
	//Cargar los productos de una familia
	$(document).on("click", "#prodic_familias .tblListados tr.fams", function(){
		$.get(base_url+"panel/productos/ajax_productos_familia/",
			"id="+$(this).attr("data-id")+"&title_familia="+$("td.data-title", this).text(),
			function(data){
			$("#produc_productos").html(data);
			$.superbox("#link_addprod");
			$.superbox("#tbl_productos .submenul a.linksm[rel^=superbox]");
		});
	});
	
	//Activamos el superbox
	$.superbox();
});


function closeBoxPrdutos(familia){
	$("#rowfam"+familia).click();
	$("#superbox p.close a").click(); //cierra el superbox
}
function buscarProductos(pag){
	pag = parseInt(pag);
	var id_fam = $("#id_familia").val(), 
	txtbuscar = $("#fnombre").val(), pagin = (pag>0? '&pag='+pag: '');
	
	$.get(base_url+"panel/productos/ajax_productos_tbl/",
		"id="+id_fam+"&fnombre="+txtbuscar+pagin,
		function(data){
			$("#tbl_productos").html(data);
			$.superbox("#tbl_productos .submenul a.linksm[rel^=superbox]");
	});
}

/**
 * Obtiene el listado de familias con ajax y las muestra
 */
function getListaFamilias(){
	$.get(base_url+"panel/productos/ajax_familia/",
		function(data){
			$("#prodic_familias #conte_tabla").html(data);
			$.superbox("#prodic_familias .linksm[data-sbox=familia]");
	});
	$("#superbox p.close a").click(); //cierra el superbox
}

/**
 * Elimina una famila de los productos
 * @param obj
 */
function deleteFamilia(obj){
	var id = obj.href.replace(/(.+)\?id=/i, "");
	id = id.replace(".", "-");
	$.get(obj.href,
		function(resp){
			create("withIcon", {
				title: resp.msg.title, 
				text: resp.msg.msg, 
				icon: base_url+'application/images/alertas/'+resp.msg.ico+'.png' });
			if(resp.msg.ico == 'ok'){
				//si es OK se elimina el row form
				$("#rowfam"+id).remove(); 
			}
	}, "json");
}

/**
 * Elimina un producto
 * @param obj
 */
function deleteProducto(obj){
	var id = obj.href.replace(/(.+)\?id=/i, "");
	id = id.replace(".", "-");
	$.get(obj.href,
		function(resp){
			create("withIcon", {
				title: resp.msg.title, 
				text: resp.msg.msg, 
				icon: base_url+'application/images/alertas/'+resp.msg.ico+'.png' });
			if(resp.msg.ico == 'ok'){
				//si es OK se elimina el row form
				$("#rowprod"+id).remove(); 
			}
	}, "json");
}
