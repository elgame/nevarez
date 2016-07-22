$(function(){
	$("#ffecha1, #ffecha2").datepicker({
		 dateFormat: 'yy-mm-dd', //formato de la fecha - dd,mm,yy=dia,mes,año numericos  DD,MM=dia,mes en texto
		 changeMonth: true, //permite modificar los meses (true o false)
		 changeYear: true, //permite modificar los años (true o false)
		 numberOfMonths: 1 //muestra mas de un mes en el calendario, depende del numero
	 });


	/**************************/
	/**** Detalle facturas ***/
	/**************************/
	var estado = util.quitarFormatoNum($("#dtalle_total_saldo").text())=='0'? 'Pagada': 'Pendiente';
	$("#inf_fact_estado").text(estado);
	
	//Activamos el superbox
	if($.superbox != undefined){
		$.superbox.settings = {
			beforeHide: function(){
				recargar();
			}
		};
		$.superbox();
		$("#superbox p.close").css("display", "none");
		$("##superbox #superbox-innerbox").css("padding", "0");
	}
});


function recargar(){
	window.location = location.href;
}