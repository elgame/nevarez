$(function(){
	$("#dfecha1, #dfecha2").datepicker({
		 dateFormat: 'yy-mm-dd', //formato de la fecha - dd,mm,yy=dia,mes,año numericos  DD,MM=dia,mes en texto
		 //minDate: '-2Y', maxDate: '+1M +10D', //restringen a un rango el calendario - ej. +10D,-2M,+1Y,-3W(W=semanas) o alguna fecha
		 changeMonth: true, //permite modificar los meses (true o false)
		 changeYear: true, //permite modificar los años (true o false)
		 //yearRange: (fecha_hoy.getFullYear()-70)+':'+fecha_hoy.getFullYear(),
		 numberOfMonths: 1 //muestra mas de un mes en el calendario, depende del numero
	 });
	
	$("#iframe-reporte").css("height", (window.innerHeight-10) + "px");

	//Asigna autocomplete de Proveedores
	$("#dproveedor").autocomplete({
		source: base_url+'panel/proveedores/ajax_get_proveedores/?t=pi',
		minLength: 1,
		selectFirst: true,
		select: function( event, ui ) {
			$("#did_proveedor").val(ui.item.id);
			$("#dproveedor").css("background-color", "#B0FFB0");
		}
	});

	$("#dcliente").autocomplete({
    source: base_url+'panel/clientes/ajax_get_clientes',
    minLength: 1,
    selectFirst: true,
    select: function( event, ui ) {
      $("#did_cliente").val(ui.item.id);
      $("#dcliente").css("background-color", "#B0FFB0");
    }
	});

	$("#dproveedor").on("keydown", function(event){
		if(event.which == 8 || event == 46){
			$("#did_proveedor").val("");
			$("#dproveedor").val("").css("background-color", "#FFF");
		}
	});

	$("#dcliente").on("keydown", function(event){
		if(event.which == 8 || event == 46){
			$("#did_cliente").val("");
			$("#dcliente").val("").css("background-color", "#FFF");
		}
	});
	

});