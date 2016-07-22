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

	//Asigna autocomplete de Nombre Productos
	$("#dproducto").autocomplete({
		source: base_url+'panel/productos/ajax_get_productos/?tipo=nombre&asig=av',
		minLength: 1,
		selectFirst: true,
		select: function( event, ui ) {
			$("#didproducto").val(ui.item.id);
			$("#dproducto").val(ui.item.item.nombre);
		}
	});

	$("#dproducto").on("keydown", function(event){
		if(event.which == 8 || event == 46){
			$("#didproducto").val("");
			$("#dproducto").val("").css("background-color", "#FFF");
		}
	});});