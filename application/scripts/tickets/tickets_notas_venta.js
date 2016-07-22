var json_data = {};
var ids_tickets = [];
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
			var tickets_selecc = window.parent.tickets_selecc;
			for(var i in tickets_selecc)
				for(var x in tickets_selecc[i])
				if(tickets_selecc[i][x]== $(this).val()){
					all_ok = false;break;}
		});
			
		if(all_ok){
			is_ok=true;
			var indice = window.parent.indice;
			$(':checkbox:checked').each(function(){
				window.parent.tickets_selecc[indice] = {};
				window.parent.tickets_selecc[indice].id_ticket= $(this).val();
				ids_tickets.push($(this).val());
				indice++;
			});
		}
			
		if(is_ok){
			window.parent.ajax_get_total_tickets(ids_tickets, tipo);
			window.parent.$("p.close a").click();
		}else{alerta('Un Ticket seleccionado ya fué agregado');}
}

function alerta(text){
	create("withIcon", {
		title: 'Avizo !',
		text: text, 
		icon: base_url+'application/images/alertas/info.png' });
}



