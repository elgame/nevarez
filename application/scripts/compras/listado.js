$(function(){
	$("#ffecha").datepicker({
		 dateFormat: 'yy-mm-dd', //formato de la fecha - dd,mm,yy=dia,mes,año numericos  DD,MM=dia,mes en texto
		 changeMonth: true, //permite modificar los meses (true o false)
		 changeYear: true, //permite modificar los años (true o false)
		 numberOfMonths: 1 //muestra mas de un mes en el calendario, depende del numero
	 });
	
	//Asigna autocomplete de Proveedores
	$("#fproveedor").autocomplete({
		source: base_url+'panel/proveedores/ajax_get_proveedores',
		minLength: 1,
		selectFirst: true,
		select: function( event, ui ) {
			$("#fid_proveedor").val(ui.item.id);
			$("#fproveedor").css("background-color", "#B0FFB0");
		}
	});
	$("#fproveedor").on("keydown", function(event){
		if(event.which == 8 || event == 46){
			$("#fid_proveedor").val("");
			$("#fproveedor").val("").animate({backgroundColor:"#FFD9B3"}, 100)
				.delay(2000).animate({backgroundColor:"#fff"}, 100);
			//css("background-color", "#FFD9B3");
		}
	});
	
	
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
	$("#frmFiltrosCompras").submit();
}
