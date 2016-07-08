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
});

function filtrarReporte(idFrame){
	var filtros = "";
	$(".inp-fil").each(
		function(){
			tipo = $(this).attr("type");
			checked = $(this).attr("checked");
			if((tipo!="radio" && tipo!='checkbox') || ((tipo=="radio" || tipo=="checkbox") && checked=='checked')) 
				filtros += $(this).attr("name") + "=" + $(this).val() + "&";
		}
	);
	$('#'+idFrame).attr('src',$('#' + idFrame).attr('data-srcbase') + "?" + filtros);
}

function getProductos(){
	$.ajax({
		type: "GET",
		url: base_url + 'panel/inventario/get_productos',
		data: 'codigo=' + $('#codigo').val() + '&nombre=' + $('#nombre').val(),
		success: function(res){
			$('#productos').html(res);
		}
	});
}

