var json_data = {};
var ids_facturas = [];
var aux_inc = 0;

var is_t = 0;
var is_f = 0;
var tipo = '';

$(function(){
	$('#CgrTickets').on('click',function(){
		cargar_tickets();
	});
});

function cargar_tickets(){
	var is_ok = false;
	var all_ok = true;

		$(':checkbox:checked').each(function(){
			var facturas_selecc = window.parent.facturas_selecc;
			for(var i in facturas_selecc)
				for(var x in facturas_selecc[i])
				if(facturas_selecc[i][x]== $(this).val()){
					all_ok = false;break;}
		});

		if(all_ok){
			is_ok=true;
			var indice = window.parent.indice;
			$(':checkbox:checked').each(function(){
        var $parent = $(this).parent();
				window.parent.facturas_selecc[indice] = {};
        window.parent.facturas_selecc[indice].id_factura  = $(this).val();
        window.parent.facturas_selecc[indice].folio       = $parent.find('.rfolio').val();
        window.parent.facturas_selecc[indice].cliente     = $parent.find('.rcliente').val();
        window.parent.facturas_selecc[indice].total       = $parent.find('.rtotal').val();
        window.parent.facturas_selecc[indice].parcialidad = $parent.find('.rparcialidad').val();
        window.parent.facturas_selecc[indice].pagos       = $parent.find('.rpagos').val();
        window.parent.facturas_selecc[indice].abono       = $parent.parent().find('.rsaldo').val();
        window.parent.facturas_selecc[indice].uuid        = $parent.find('.ruuid').val();
        window.parent.facturas_selecc[indice].saldo       = $parent.find('.rsaldo').val();
				ids_facturas.push($(this).val());
				indice++;
			});
		}

		if(is_ok){
			window.parent.agregar_facturas_cp(ids_facturas, tipo);
			window.parent.$("p.close a").click();
		}else{alerta('Una Factura seleccionado ya fue agregada');}
}

function alerta(text){
	create("withIcon", {
		title: 'Avizo !',
		text: text,
		icon: base_url+'application/images/alertas/info.png' });
}



