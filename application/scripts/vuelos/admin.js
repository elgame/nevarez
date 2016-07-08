h_on_select = '';
$(function(){

	$("#fcliente").on("keydown", function(event){
		if(event.which == 8 || event == 46){
			var input = this.id;
			var hidde = 'h'+input.substr(1);
			$(this).val("");
			$("#fidcliente").val("");
		}
	}).autocomplete({
		source: base_url+'panel/clientes/ajax_get_clientes',
		minLength: 1,
		selectFirst: true,
		select: function( event, ui ) {
			$("#fidcliente").val(ui.item.id);
		}
	});
	
	$('#ffecha_ini').datepicker({
		 dateFormat: 'yy-mm-dd', //formato de la fecha - dd,mm,yy=dia,mes,año numericos  DD,MM=dia,mes en texto
		 //minDate: '-2Y', maxDate: '+1M +10D', //restringen a un rango el calendario - ej. +10D,-2M,+1Y,-3W(W=semanas) o alguna fecha
		 changeMonth: true, //permite modificar los meses (true o false)
		 changeYear: true, //permite modificar los años (true o false)
		 //yearRange: (fecha_hoy.getFullYear()-70)+':'+fecha_hoy.getFullYear(),
		 numberOfMonths: 1 //muestra mas de un mes en el calendario, depende del numero
	});
	$('#ffecha_fin').datepicker({
		 dateFormat: 'yy-mm-dd', //formato de la fecha - dd,mm,yy=dia,mes,año numericos  DD,MM=dia,mes en texto
		 //minDate: '-2Y', maxDate: '+1M +10D', //restringen a un rango el calendario - ej. +10D,-2M,+1Y,-3W(W=semanas) o alguna fecha
		 changeMonth: true, //permite modificar los meses (true o false)
		 changeYear: true, //permite modificar los años (true o false)
		 //yearRange: (fecha_hoy.getFullYear()-70)+':'+fecha_hoy.getFullYear(),
		 numberOfMonths: 1 //muestra mas de un mes en el calendario, depende del numero
	});

	$('input#hora_llegada').timepicker({
		onClose: function(dateText, inst) {
			if (h_on_select != dateText)
				 ajax_hora_llegada(this);
		},
		beforeShow: function(input) {
		 	h_on_select = $(input).val();
		}
	});

	$(".hora_lleg").focus(function(){
		var vthis = this;
		setTimeout(function(){
			vthis.select();
		}, 100);
	});

});


function ajax_hora_llegada(obj){
	id = $(obj).parent().parent().attr('id');
	val = $(obj).val();
	
	loader.create();
	$.post(base_url+'/panel/vuelos/ajax_hora_llegada/', {'id': id, 'val': val}, function(resp) {
	 		create("withIcon", {
                    title: resp.msg.title, 
                    text: resp.msg.msg, 
                    icon: base_url+'application/images/alertas/'+resp.msg.ico+'.png' });
	 if (resp.msg.ico=='error') {
	 		$(obj).css('background','#FFD9B3').focus();
	 }
	 else $(obj).css('background','white');
	}, "json").complete(function(){loader.close();});
	h_on_select = '';
}
