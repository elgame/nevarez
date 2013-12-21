var taza_iva = 0;
var subtotal = 0;
var iva = 0;
var total = 0;

var tickets_selecc = {}; // almacena los tickets que han sido agregados
var tickets_data = {}; //almacena la informacion de los tickets que sera enviada por POST
var indice = 0; // indice para controlar los vuelos q han sido agregados

var post = {}; // Contiene todos los valores de la nota de venta q se pasaran por POST

var aux_isr = false;
var total_isr = 0;
var ttcisr = 0;

$(function(){
    actualDate(true);
    $.superbox();
    $("#dcliente").autocomplete({
        source: base_url+'panel/clientes/ajax_get_clientes',
        minLength: 1,
        selectFirst: true,
        select: function( event, ui ) {
            if ($("#chpublicogener").is(":checked") == false){
                set_data_cliente(ui.item);
            }
        }
    });

    //publico en general
    $("#chpublicogener").on('click', function(){
        var vthis = $(this);
        if (vthis.is(":checked")){
            $.getJSON(base_url+'panel/clientes/ajax_get_clientes', "term=XAXX010101000", function(res){
                set_data_cliente(res[0], false);
            });
        }else
            clean_data_cliente();
    });

    $("#dfiltro-cliente").autocomplete({
        source: base_url+'panel/clientes/ajax_get_clientes',
        minLength: 1,
        selectFirst: true,
        select: function( event, ui ) {
            $("#dfiltro-cliente").css("background-color", "#B0FFB0");
            $('.addv').html('<a href="'+base_url+'panel/tickets/tickets_cliente/?id='+ui.item.id+'" id="btnAddTicket" class="linksm" style="margin: 0px;" rel="superbox[iframe][700x500]"> <img src="'+base_url+'application/images/privilegios/add.png" width="16" height="16">Agregar Tickets</a>');
            $.superbox();
        }
    });     

    $("#dfiltro-cliente").on("keydown", function(event){
        if(event.which == 8 || event == 46){
            $('.addv').html('<a href="'+base_url+'panel/tickets/tickets_cliente/" id="btnAddTicket" class="linksm" style="margin: 0px;" rel="superbox[iframe][700x500]"> <img src="'+base_url+'application/images/privilegios/add.png" width="16" height="16">Agregar Tickets</a>');
            $("#dfiltro-cliente").val("").css("background-color", "#FFD9B3");
            $.superbox();
        }
    });

    $("input[type=text]:not(.not)").on("keydown", function(event){
        if(event.which == 8 || event == 46){
            if ($("#chpublicogener").is(":checked") == false){
                clean_data_cliente();
            }
        }
    });

    $('#dleyendaserie').on('change',function(){
            var id = $('#dleyendaserie option:selected').val();
            ajax_get_folio(id);
    });

    $('#dforma_pago').on('change',function(){
        if($(this).val()==1){
                $('#show_parcialidad').css({'display':'block'});
                $('#dforma_pago_parcialidad').val('Parcialidad 1 de X').css({'color':'red'}).focus();
        }
        else $('#show_parcialidad').css({'display':'none'});
    });

    $('#dmetodo_pago').on('change',function(){
        if($(this).val()!='efectivo' && $(this).val()!=''){
                $('#show_pago_digitos').css({'display':'block'});
                $('#dmetodo_pago_digitos').val('No identificado').focus();
        }
        else $('#show_pago_digitos').css({'display':'none'});
    });

    $('#submit').on('click',function(){ajax_submit_form();});
});


function set_data_cliente(item, autocom){
    $("#hcliente").val(item.id);

    if (autocom == false)
        $("#dcliente").val(item.item.nombre_fiscal);

    $("#fplazo_credito").val(item.item.dias_credito);
    $('#frfc').val(item.item.rfc);
    $('#fcalle').val(item.item.calle);
    $('#fno_exterior').val(item.item.no_exterior);
    $('#fno_interior').val(item.item.no_interior);
    $('#fcolonia').val(item.item.colonia);
    $('#flocalidad').val(item.item.localidad);
    $('#fmunicipio').val(item.item.municipio);
    $('#festado').val(item.item.estado);
    $('#fcp').val(item.item.cp);
    $('#fpais').val('México');                      

    if(item.item.retencion==1){
            aux_isr = true;
    }else aux_isr = false;

    $("#dcliente").css("background-color", "#B0FFB0");
}

function clean_data_cliente(){
    $("#hcliente").val('');                 
    $("#fplazo_credito").val(0);
    $('#frfc').val('');
    $('#fcalle').val('');
    $('#fno_exterior').val('');
    $('#fno_interior').val('');
    $('#fcolonia').val('');
    $('#flocalidad').val('');
    $('#fmunicipio').val('');
    $('#festado').val('');
    $('#fcp').val('');
    $('#fpais').val('');
    $("#dcliente").val("").css("background-color", "#FFD9B3");
}


function ajax_get_folio(param){
    loader.create();
    $.post(base_url+'panel/facturacion/ajax_get_folio/', {id:param}, function(resp){
        if(resp[0]){
            $('#dserie').val(resp.serie);
            $('#dfolio').val(resp.folio);
            $('#dano_aprobacion').val(resp.ano_aprobacion);
            $('#dno_aprobacion').val(resp.no_aprobacion);
        }else{
            $('#dserie').val('');
            $('#dfolio').val('');
            $('#dano_aprobacion').val('');
            $('#dno_aprobacion').val('');
            alerta(resp.msg);}
    }, "json").complete(function(){
    loader.close();
}); 
}

function ajax_get_total_tickets(data){
        loader.create();
        $.post(base_url+'panel/facturacion/ajax_get_total_tickets/', {'tickets[]':data}, function(resp){

        if(resp.tickets){
                var opc_elimi = '', subtotal_vuelos_isr=0;
                
            for(var i in resp.tickets){
                    tickets_data[indice] = {};
                    tickets_data[indice]['ticket'+i] = {};
                    tickets_data[indice]['ticket'+i].id_ticket              = resp.tickets[i].id_ticket;
                    tickets_data[indice]['ticket'+i].folio                  = resp.tickets[i].folio;
//                              tickets_data[indice]['ticket'+i].cantidad               = resp.tickets[i].cantidad;
//                              tickets_data[indice]['ticket'+i].precio_unitario= parseFloat(resp.tickets[i].precio_unitario,2);
//                              tickets_data[indice]['ticket'+i].importe                = parseFloat(resp.tickets[i].precio_unitario,2);
//                              tickets_data[indice]['ticket'+i].total                  = parseFloat(resp.tickets[i].total_ticket,2);
//                              tickets_data[indice]['ticket'+i].importe_iva_0  = parseFloat(resp.tickets[i].importe_iva_0,2);
//                              tickets_data[indice]['ticket'+i].importe_iva_10 = parseFloat(resp.tickets[i].importe_iva_10,2);
//                              tickets_data[indice]['ticket'+i].importe_iva_16 = parseFloat(resp.tickets[i].importe_iva_16,2);
            
                    vals= '{indice:'+indice+', subtotal:'+resp.tickets[i].subtotal_ticket+', iva: '+resp.tickets[i].iva_ticket+', total:'+resp.tickets[i].total_ticket+'}';
                    
                    opc_elimi = '<a href="javascript:void(0);" class="linksm"'+ 
                            'onclick="msb.confirm(\'Estas seguro de eliminar el ticket?\', '+vals+', eliminaTickets); return false;">'+
                            '<img src="'+base_url+'application/images/privilegios/delete.png" width="10" height="10">Eliminar Ticket</a>';
                    
//                              //Agrego el tr con la informacion de los productos del ticket
                    id = resp.tickets[i].id_ticket;
                    for(var p in resp.productos[id]){
                            $("#tbl_tickets tr.header:last").after(
                            '<tr id="e'+indice+'" class="'+resp.productos[id][p].tipo+'" data-importe="'+resp.productos[id][p].importe+'">'+
                            '       <td></td>'+
                            '       <td>'+resp.productos[id][p].cantidad+'</td>'+
                            '       <td>'+resp.productos[id][p].unidad+'</td>'+
                            '       <td>'+resp.productos[id][p].descripcion+'</td>'+
                            '       <td>'+resp.productos[id][p].precio_unitario+'</td>'+
                            '       <td>'+resp.productos[id][p].importe+'</td>'+
                            '       <td></td>'+
                            '</tr>');
                            if(resp.productos[id][p].tipo == 'vu'){
                                    subtotal_vuelos_isr += parseFloat(resp.productos[id][p].importe);
                            }
                    }
                    
                    $("#tbl_tickets tr.header:last").after(
                                    '<tr id="e'+indice+'" style="background-color:#FFFED9">'+
                                    '       <td colspan="6">'+resp.tickets[i].folio+'</td>'+
                                    '       <td class="tdsmenu a-c" style="width: 90px;">'+
                                    '               <img alt="opc" src="'+base_url+'application/images/privilegios/gear.png" width="16" height="16">'+
                                    '               <div class="submenul">'+
                                    '                       <p class="corner-bottom8">'+
                                                                            opc_elimi+
                                    '                       </p>'+
                                    '               </div>'+
                                    '       </td>'+
                                    '</tr>');
                    
                    subtotal        += parseFloat(resp.tickets[i].subtotal_ticket, 2);
                    iva                     += parseFloat(resp.tickets[i].iva_ticket, 2);//parseFloat(subtotal*taza_iva, 2);
                    total           += parseFloat(resp.tickets[i].total_ticket, 2);
                    indice++;
            }
            if(aux_isr){
                    total_isr += parseFloat(subtotal_vuelos_isr*0.1, 2);
                    ttcisr = total-total_isr;
            }
            updateTablaPrecios();
        }
        }, "json").complete(function(){ 
        loader.close();
    });
}

function ajax_submit_form(){
//      win = window.open(base_url+'panel/facturacion/imprimir_pdf/?&id=l4fc8265798f681.79280660', 'Imprimir Factura', 'left='+((window.innerWidth/2)-240)+',top='+((window.innerHeight/2)-280)+',width=500,height=630,toolbar=0,resizable=0')
     $.post(base_url+'panel/facturacion/ajax_valida_folio/', 
                    {'serie': $('#dserie').val(), 'folio': $('#dfolio').val()}, 
                    function(r)
    {
        if (r == 0) 
        {
            post.hcliente = $('#hcliente').val();
            post.frfc = $('#frfc').val();
            
            post.dcliente = $('#dcliente').val();
            post.fcalle = $('#fcalle').val();
            post.fno_exterior = $('#fno_exterior').val();
            post.fno_interior = $('#fno_interior').val();
            post.fcolonia = $('#fcolonia').val();
            post.flocalidad = $('#flocalidad').val();
            post.fmunicipio = $('#fmunicipio').val();
            post.festado = $('#festado').val();
            post.fcp = $('#fcp').val();
            post.fpais = $('#fpais').val();
            
            post.fplazo_credito = $('#fplazo_credito').val();
            post.dfecha = $('#dfecha').val();
            post.dcondicion_pago = $('#dcondicion_pago').val();
            post.dleyendaserie = $('#dleyendaserie').val();
            post.dserie = $('#dserie').val();
            post.dfolio = $('#dfolio').val();
            post.dano_aprobacion = $('#dano_aprobacion').val();
            post.dno_aprobacion = $('#dno_aprobacion').val();
            post.dno_certificado = $('#dno_certificado').val();
            post.dtipo_comprobante = $('#dtipo_comprobante').val();
            post.dforma_pago = $('#dforma_pago').val();
            post.dforma_pago_parcialidad = $('#dforma_pago_parcialidad').val();
            post.dmetodo_pago = $('#dmetodo_pago').val();
            post.dmetodo_pago_digitos = $('#dmetodo_pago_digitos').val();
            
            post.subtotal = parseFloat(subtotal,2);
            post.iva = parseFloat(iva,2);
            post.total_isr = parseFloat(total_isr,2);

            post.fobservaciones = $('#fobservaciones').val();
            
            if(aux_isr)
                post.total = parseFloat(ttcisr,2);
            else
                post.total = parseFloat(total,2);
            
            post.dtotal_letra = $('#dttotal_letra').val();
            
            var count=0;
            for(var i in tickets_selecc)
                    for(var x in tickets_selecc[i])
                            count++;
            if(count>0)
                    post.tickets    = count;
            
            cont=1;
            for(var i in tickets_data){
                for(var x in tickets_data[i]){
                        post['pticket'+cont]    = {};
                        post['pticket'+cont]    = tickets_data[i][x];
                        cont++;
                }
            }
            
            loader.create();
            $.post(base_url+'panel/facturacion/ajax_agrega_factura/', post, function(resp){
                create("withIcon", {
                    title: resp.msg.title, 
                    text: resp.msg.msg, 
                    icon: base_url+'application/images/alertas/'+resp.msg.ico+'.png' });
                if(resp.msg.ico == 'ok'){
                    //si es OK se elimina el row form
                    $('#tbl_tickets tr').not('.header').remove();
                }
                if(resp[0]){
                    limpia_campos();
                    updateTablaPrecios();
                    
                    win = window.open(base_url+'panel/facturacion/imprimir_pdf/?&id='+resp.id_factura, 'Imprimir Factura', 'left='+((window.innerWidth/2)-240)+',top='+((window.innerHeight/2)-280)+',width=500,height=630,toolbar=0,resizable=0')
                        
                }

            }, "json").complete(function(){ 
                            loader.close();
                        });
        }
        else if(r == 2) 
            alerta('El certificado para firmar las facturas ya caduco.')
        else
            alerta('La serie y folio ya estan en uso.')
    }, "json").complete(function(){ 
        loader.close();
    });
}

function eliminaTickets(vals){
        delete tickets_selecc[vals.indice];
        delete tickets_data[vals.indice];
        var subtotal_vuelos_isr = 0;
        
        $('tr#e'+vals.indice+'.vu').each(function(){
                subtotal_vuelos_isr += parseFloat($(this).attr("data-importe"));
        });
        $('tr#e'+vals.indice).remove();
        
        subtotal        -= parseFloat(vals.subtotal, 2);
        iva                     -= parseFloat(vals.iva, 2);
        total           -= parseFloat(vals.total, 2);
        
        if(aux_isr){
                total_isr       -= parseFloat(subtotal_vuelos_isr*0.1,2);
                ttcisr = total-total_isr;
        }
        updateTablaPrecios();
}

function updateTablaPrecios(){
        $('#ta_subtotal').text(util.darFormatoNum(subtotal));
        $('#ta_iva').text(util.darFormatoNum(iva));
        $('#ta_isr').text(util.darFormatoNum(total_isr));
        if(aux_isr)
                $('#ta_total').text(util.darFormatoNum(ttcisr));
        else
                $('#ta_total').text(util.darFormatoNum(total));
        
        if(parseFloat(total,2)!=0){
                if(aux_isr)
                        $('#dttotal_letra').val(util.numeroToLetra.covertirNumLetras(ttcisr.toString()));
                else
                        $('#dttotal_letra').val(util.numeroToLetra.covertirNumLetras(total.toString()));
        }
        else
                $('#dttotal_letra').val('');
}

function limpia_campos(){
        $('#dcliente').val('').css('background','#FFF');
        $('#frfc').val('');
        $('#hcliente').val('');
        $('#fcalle').val('');
        $('#fno_exterior').val('');
        $('#fno_interior').val('');
        $('#fcolonia').val('');
        $('#flocalidad').val('');
        $('#fmunicipio').val('');
        $('#festado').val('');
        $('#fcp').val('');
        $('#fpais').val('');    
        $('#fplazo_credito').val('');
        
        $('#dfecha').val(actualDate(true));
        $('#dcondicion_pago').val('');
        $('#dleyendaserie').val('');
        $('#dserie').val('');
        $('#dfolio').val('');
        $('#dano_aprobacion').val('');
        $('#dno_aprobacion').val('');
//      $('#dno_certificado').val('');
        $('#dtipo_comprobante').val('');
        $('#dforma_pago').val('');
        $('#dforma_pago_parcialidad').val('');
        $('#dmetodo_pago').val('');
        $('#dmetodo_pago_digitos').val('');
        $('#fobservaciones').val('');
        
        subtotal = 0;
        iva = 0;
        total = 0;
        tickets_selecc = {};
        tickets_data = {};
        post = {};
        indice = 0;
        aux_isr = false;
        total_isr = 0;
        
//        $('.addv').html('<a href="javascript:void(0);" id="btnAddTicket" class="linksm f-r" style="margin: 10px 0 20px 0;" onclick="alerta(\'Seleccione un Cliente !\');"> <img src="'+base_url+'application/images/privilegios/add.png" width="16" height="16">Agregar Tickets</a>');
}

function alerta(msg){
        create("withIcon", {
                title: 'Avizo !',
                text: msg, 
                icon: base_url+'application/images/alertas/info.png' });
}

function actualDate(time){
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth()+1; //January is 0!
        
        var yyyy = today.getFullYear();
        if(dd<10){dd='0'+dd;} if(mm<10){mm='0'+mm;}
        var date = yyyy+'-'+mm+'-'+dd;
        if(time){h=today.getHours();m=today.getMinutes();s=today.getSeconds();date+=' '+h+':'+m+':'+s;}
        
        return date;
}