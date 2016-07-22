$(function(){
	//asigno el evento change para editar los precios de las listas
	$(".col-updatechange").on("change", function(){
		var vthis = $(this);
		if(vthis.val() != '' && vthis.val() != '.'){
			loader.create();
			//cambiar precio
			$.post(base_url+"panel/listas_precio/cambiar_precio/",
				"id_producto="+vthis.attr("data-producto")+"&id_lista="+vthis.attr("data-lista")+"&precio="+vthis.val(),
				function(resp){
					create("withIcon", {
						title: resp.msg.title, 
						text: resp.msg.msg, 
						icon: base_url+'application/images/alertas/'+resp.msg.ico+'.png' });
				}, "json").complete(function(){ 
			    	loader.close(); 
			    });
		}
	});
	
	$("#lnkImprimir").on("click", function(){
		var listas = "", listasid = "",
		num = $(".chkPrintLista:checked").each(function(){
			var vals = $(this).val().split("|");
			listas += vals[0]+", ";
			listasid += ","+vals[1];
		}).length;
		if(num > 0 && num < 5){
			var dat = base_url+"panel/listas_precio/imprime_lista/?ffamilia="+$("#ffamilia").val()+"&familia="+
				$("#ffamilia option:selected").text()+"&listas="+listas+"&listasid="+listasid;
			$(this).attr("href", dat);
		}else{
			msb.info("Selecciona al menos una lista y un maximo de 4.");
			return false;
		}
		
	});
});