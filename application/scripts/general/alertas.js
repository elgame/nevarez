$(function(){
	//Alertas de form
	$container = $("#container").notify();
	
	//Ajustamos el tamaño del contenido
	var sw = parseInt(220*100/$("body").width());
	$("#menu_left, #bgmenuleft").css("width", sw + "%");
	$("#contentAll").css("width", (100 - sw - 1) + "%");
	
	//Se asigna eventos del menu izq y el treeview
	$("#menu_left").accordion({
		autoHeight: false,
		active: buscarMenuActive()
	});
	$("ul.treeview").treeview({
		collapsed: false,
		unique: true,
		persist: "location"
	});
	
	//Asigna eventos para las opciones de las tablas
	$(document).on('mouseenter', 'table td.tdsmenu', function(){
		$(".submenul", this).show();
	});
	$(document).on('mouseleave', "table td.tdsmenu", function(){
		$(".submenul", this).hide();
	});
});


/*alertas de forms*/
function create( template, vars, opts ){
	return $container.notify("create", template, vars, opts);
}


/**
 * Busca la opcion del menu para ponerla activa
 * @returns {Number}
 */
function buscarMenuActive(){
	var conta=0, sel=0;
	$("#menu_left h3 a").each(function(){
		if($(this).text() == opcmenu_active){
			sel = conta;
		}
		conta++;
	});
	return sel;
}


/**
 * Obj para crear un loader cuando se use Ajax
 */
var loader = {
	create: function(){
		$("body").append('<div id="ajax-loader" class="corner-bottom8">Cargando...</div>');
	},
	close: function(){
		$("#ajax-loader").remove();
	}
};
