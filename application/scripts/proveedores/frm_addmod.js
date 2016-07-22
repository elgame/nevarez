$(function(){
	
	$("#frmsec-acordion").accordion({
		autoHeight: false
	});
});


var contador_contacto = 0;
/**
 * Agrega un row form para registrar un nuevo contacto
 * @param id_tbl
 * @param obj
 */
function addContacto(id_tbl, obj){
	if(contador_contacto == 0){
		//creo un row form para agregar contacto
		$("#"+id_tbl).append(
			'<tr id="tr_addcontacto'+contador_contacto+'">'+
			'	<td><input type="text" name="dcnombre" value="" size="25"></td>'+
			'	<td><input type="text" name="dcdomicilio" value="" size="25"></td>'+
			'	<td><input type="text" name="dcmunicipio" value="" size="9"></td>'+
			'	<td><input type="text" name="dcestado" value="" size="9"></td>'+
			'	<td><input type="text" name="dctelefono" value="" size="9"></td>'+
			'	<td><input type="text" name="dccelular" value="" size="9"></td>'+
			'	<td><a href="'+obj.href+'" class="linksm" onclick="agregarContacto(\'tr_addcontacto'+contador_contacto+'\', this); return false;">'+
			'		<img src="'+base_url+'application/images/privilegios/add.png" width="10" height="10"> Agregar</a></td>'+
			'</tr>');
		$("#tr_addcontacto"+contador_contacto+" input[name=dcnombre]").focus();
	}
	contador_contacto++;
}
/**
 * Permite enviar la peticion por Ajax para agregar el contacto
 * @param id_tr
 * @param obj
 */
function agregarContacto(id_tr, obj){
	var data = "";
	$("#"+id_tr+" input").each(function(){
		data += this.name+"="+this.value+"&";
	});
	
	loader.create();
	$.post(obj.href, data, function(resp){
		create("withIcon", {
			title: resp.msg.title, 
			text: resp.msg.msg, 
			icon: base_url+'application/images/alertas/'+resp.msg.ico+'.png' });
		if(resp.msg.ico == 'ok'){
			//si es OK se elimina el row form
			$("#"+id_tr).remove();
			contador_contacto = 0;
		}
		
		if(resp.info){
			//comprueba si tiene el permiso de eliminar contacto
			var priv_eliminar_contacto = '';
			if($("#priv_eliminar_contacto").length > 0)
				priv_eliminar_contacto = '<a href="'+base_url+'panel/proveedores/eliminar_contacto/?id='+resp.info.id_contacto+'" class="linksm"'+ 
					'onclick="msb.confirm(\'Estas seguro de eliminar el contacto?\', this, eliminaContacto); return false;">'+
					'<img src="'+base_url+'application/images/privilegios/delete.png" width="10" height="10"> Eliminar contacto</a>';
			
			//Agrego el tr con la informacion del contacto agregado
			$("#tbl_contactos tr.header:last").after(
			'<tr>'+
			'	<td>'+resp.info.nombre+'</td>'+
			'	<td>'+resp.info.domicilio+'</td>'+
			'	<td>'+resp.info.municipio+'</td>'+
			'	<td>'+resp.info.estado+'</td>'+
			'	<td>'+resp.info.telefono+'</td>'+
			'	<td>'+resp.info.celular+'</td>'+
			'	<td class="tdsmenu a-c" style="width: 90px;">'+
			'		<img alt="opc" src="'+base_url+'application/images/privilegios/gear.png" width="16" height="16">'+
			'		<div class="submenul">'+
			'			<p class="corner-bottom8">'+
						priv_eliminar_contacto+
			'			</p>'+
			'		</div>'+
			'	</td>'+
			'</tr>');
		}
	}, "json").complete(function(){ 
    	loader.close(); 
    });
}

function eliminaContacto(obj){
	loader.create();
	$.post(obj.href, "", function(resp){
		create("withIcon", {
			title: resp.msg.title, 
			text: resp.msg.msg, 
			icon: base_url+'application/images/alertas/'+resp.msg.ico+'.png' });
		if(resp.msg.ico == 'ok'){
			//si es OK se elimina el row form
			$(obj).parents("tr").remove();
		}
	}, "json").complete(function(){ 
    	loader.close(); 
    });
}

