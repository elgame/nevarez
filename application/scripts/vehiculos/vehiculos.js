$(function(){
	if($.superbox){
		$.superbox();		
	}
});

function getListadoVehiculos(){
	fnombre = (getUrlVars()["fnombre"] != undefined) ? getUrlVars()["fnombre"] : '';
	$.get(base_url+"panel/vehiculo/ajaxVehiculos/?&fnombre="+fnombre,
			function(data){
				$("#listado").html(data);
				$.superbox(".linksm[data-sbox=vehiculo]");
		});
	$("#superbox p.close a").click();
}

function getUrlVars() {
    var vars = {};
    var partes = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}
