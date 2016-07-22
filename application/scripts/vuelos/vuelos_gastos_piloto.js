var json_data = {};
json_data['vuelos'] = {};
var aux_inc = 0;

var is_t = 0;
var is_f = 0;
var tipo = '';


$(function(){
	$('#CgrVuelos').on('click',function(){
		cargar_vuelos();
	});
	
	$(':checkbox').on('change',function(){
		verifica_vuelo_varios_clientes(this);
	});
	
});

function cargar_vuelos(){
	var is_ok = false;
	var all_ok = true;
	
	var indice = window.parent.indice;

//	if(((window.parent.aux_varios_clientes == false && is_t==1 && is_f==0) || (window.parent.aux_varios_clientes == false && is_t==0))){
//		if((is_t==1 && window.parent.cont_aux_clientes==0) || (is_t==0 && window.parent.cont_aux_clientes>=0)){
			$(':checkbox:checked').each(function(){
				var vuelos_selecc = window.parent.vuelos_selec;
				for(var i in vuelos_selecc)
					for(var x in vuelos_selecc[i]){
						if(vuelos_selecc[i][x]== $(this).val()){
							all_ok = false;break;
						}
					}
			});
			
			if(all_ok){
				is_ok=true;
//				window.parent.vuelos_selec[indice] = [];
//				var c=1;
				$(':checkbox:checked').each(function(){
					var data = $(this).val().split('&');
					json_data['vuelos']['v'+aux_inc] = {};
					json_data['vuelos']['v'+aux_inc].id_vuelo	=$(this).val();
					aux_inc++;
					
//					if(data[5]=='t'){
//						window.parent.aux_varios_clientes = true;
//						tipo = 0;
//					}
					
//					if(data[5]=='f'){
////						if(c==1)
////							window.parent.cont_aux_clientes++;
//						tipo = 1;
////						c++;
//					}
					
//					window.parent.vuelos_selec[indice].push($(this).val());
				});
			}
			
			if(is_ok){
				window.parent.ajax_get_total_vuelos(json_data['vuelos']);
				window.parent.$("p.close a").click();
			}else{alerta('Un vuelo seleccionado ya existe');}
//		}else{alerta('No puedes agregar más vuelos con otros vuelos que estan asignados a más de un cliente');}
//	}else{alerta('No puedes agregar más vuelos con otros vuelos que estan asignados a más de un cliente');}
}

function verifica_vuelo_varios_clientes(obj){
	var data = $(obj).val().split('&');	
	if(data[5]=='t')
		if($(obj).is(':checked'))
			is_t++;
		else
			is_t--;
	
	if(data[5]=='f')
		if($(obj).is(':checked'))
			is_f++;
		else
			is_f--;
	
}

function alerta(text){
	create("withIcon", {
		title: 'Avizo !',
		text: text, 
		icon: base_url+'application/images/alertas/info.png' });
}



