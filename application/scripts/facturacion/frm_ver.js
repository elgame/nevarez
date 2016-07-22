function actualizar(id){
	var digitos = $('#dmetodo_pago_digitos').val();
	if(digitos.length==4){
		loader.create();
		$.post(base_url+'panel/facturacion/ajax_actualiza_digitos/', {'id':id,'digitos':digitos}, function(resp){
			create("withIcon", {
				title: resp.msg.title, 
				text: resp.msg.msg, 
				icon: base_url+'application/images/alertas/'+resp.msg.ico+'.png' });
			if(resp[0]){
			}
		}, "json").complete(function(){ 
	    	loader.close();
	    });
	}else alerta('Especifique los 4 Digitos');
}
function alerta(msg){
	create("withIcon", {
		title: 'Avizo !',
		text: msg, 
		icon: base_url+'application/images/alertas/info.png' });
}