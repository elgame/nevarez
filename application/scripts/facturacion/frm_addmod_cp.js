var taza_iva = 0;
var subtotal = 0;
var iva = 0;
var total = 0;

var facturas_selecc = []; // almacena los tickets que han sido agregados
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
            $('.addv').html('<a href="'+base_url+'panel/facturacion/facturas_cp/?id='+ui.item.id+'" id="btnAddTicket" class="linksm" style="margin: 0px;" rel="superbox[iframe][700x500]"> <img src="'+base_url+'application/images/privilegios/add.png" width="16" height="16">Agregar Facturas</a>');
            $.superbox();
        }
    });

    $("#dfiltro-cliente").on("keydown", function(event){
        if(event.which == 8 || event == 46){
            $('.addv').html('<a href="'+base_url+'panel/facturacion/facturas_cp/" id="btnAddTicket" class="linksm" style="margin: 0px;" rel="superbox[iframe][700x500]"> <img src="'+base_url+'application/images/privilegios/add.png" width="16" height="16">Agregar Facturas</a>');
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

    // $('#dmetodo_pago').on('change',function(){
    //     if($(this).val()!='efectivo' && $(this).val()!=''){
    //             $('#show_pago_digitos').css({'display':'block'});
    //             $('#dmetodo_pago_digitos').val('No identificado').focus();
    //     }
    //     else $('#show_pago_digitos').css({'display':'none'});
    // });

    $('#submit').on('click',function(){ajax_submit_form();});
});

function get_seriesfrom_empresa(id_empresa){
	loader.create();
    $.post(base_url+'panel/facturacion/ajax_get_seriesfromempresa/', {id_empresa:id_empresa, tipo: 'cp'}, function(resp){
        $('#dleyendaserie').html(resp.series);
		$('#dno_certificado').val(resp.numcertificado);
    }, "json").complete(function(){
    	loader.close();
	});
}

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

function agregar_facturas_cp(data){
  console.log(data, facturas_selecc);
  if (facturas_selecc.length > 0) {
    for(var factura in facturas_selecc){
      total += parseFloat(facturas_selecc[factura].saldo, 2);

      $("#tbl_tickets tr.header:last").after(
      '<tr id="e'+factura+'">'+
      '       <td>'+facturas_selecc[factura].folio+'</td>'+
      '       <td>'+facturas_selecc[factura].cliente+'</td>'+
      '       <td>'+facturas_selecc[factura].parcialidad+'</td>'+
      '       <td>'+facturas_selecc[factura].saldo+'</td>'+
      '       <td class="tdsmenu a-c" style="width: 90px;">'+
      '         <img alt="opc" src="'+base_url+'application/images/privilegios/gear.png" width="16" height="16">'+
      '           <div class="submenul">'+
      '             <p class="corner-bottom8">'+
                      '<a href="javascript:void(0);" class="linksm"'+
                      'onclick="msb.confirm(\'Estas seguro de quitar la factura?\', '+factura+', quitarFactura); return false;">'+
                      '<img src="'+base_url+'application/images/privilegios/delete.png" width="10" height="10">Quitar</a>'+
      '             </p>'+
      '           </div>'+
      '       </td>'+
      '</tr>');
    }
    updateTablaPrecios();
  }
}

function ajax_submit_form(){
//      win = window.open(base_url+'panel/facturacion/imprimir_pdf/?&id=l4fc8265798f681.79280660', 'Imprimir Factura', 'left='+((window.innerWidth/2)-240)+',top='+((window.innerHeight/2)-280)+',width=500,height=630,toolbar=0,resizable=0')

    $("#submit").hide();
    $("#submitLoader").show();
    $.post(base_url+'panel/facturacion/ajax_valida_folio/',
                    {'serie': $('#dserie').val(), 'folio': $('#dfolio').val(), 'id_empresa': $('#didempresa').val()},
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

            post.didempresa = $('#didempresa').val();
            post.fplazo_credito = $('#fplazo_credito').val();
            post.dfecha = $('#dfecha').val();
            // post.dcondicion_pago = $('#dcondicion_pago').val();
            // post.dleyendaserie = $('#dleyendaserie').val();
            post.dserie = $('#dserie').val();
            post.dfolio = $('#dfolio').val();
            post.dano_aprobacion = $('#dano_aprobacion').val();
            post.dno_aprobacion = $('#dno_aprobacion').val();
            post.dno_certificado = $('#dno_certificado').val();
            post.dtipo_comprobante = 'cp';
            post.dforma_pago = $('#dforma_pago').val();
            post.dforma_pago_parcialidad = $('#dforma_pago_parcialidad').val();
            // post.dmetodo_pago = $('#dmetodo_pago').val();
            // post.dmetodo_pago_digitos = $('#dmetodo_pago_digitos').val();
            post.duso_cfdi = 'P01';

            // post.subtotal = parseFloat(subtotal,2);
            // post.iva = parseFloat(iva,2);
            // post.total_isr = parseFloat(total_isr,2);

            // post.fobservaciones = $('#fobservaciones').val();

            post.total = parseFloat(total,2);

            // post.dtotal_letra = $('#dttotal_letra').val();

            post.facturas = facturas_selecc;

            // var count=0;
            // for(var i in facturas_selecc)
            //         for(var x in facturas_selecc[i])
            //                 count++;
            // if(count>0)
            //         post.tickets    = count;

            // cont=1;
            // for(var i in tickets_data){
            //     for(var x in tickets_data[i]){
            //             post['pticket'+cont]    = {};
            //             post['pticket'+cont]    = tickets_data[i][x];
            //             cont++;
            //     }
            // }

            loader.create();
            $.post(base_url+'panel/facturacion/ajax_agrega_cp/', post, function(resp){
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
                    window.location.reload();
                }
                $("#submit").show();
                $("#submitLoader").hide();

            }, "json").complete(function(){
                            $("#submit").show();
                            $("#submitLoader").hide();
                            loader.close();
                        });
        }
        else if(r == 2){
            $("#submit").show();
            $("#submitLoader").hide();
            alerta('El certificado para firmar las facturas ya caduco.');
        }else{
            $("#submit").show();
            $("#submitLoader").hide();
            alerta('La serie y folio ya estan en uso.');
        }
    }, "json").complete(function(){
        // $("#submit").show();
        // $("#submitLoader").hide();
        // loader.close();
    });
}

function quitarFactura(indice){
  total -= parseFloat(facturas_selecc[indice].saldo, 2);
  updateTablaPrecios();

  delete facturas_selecc[indice];
  $('tr#e'+indice).remove();
}

function updateTablaPrecios(){
  $('#ta_total').text(util.darFormatoNum(total));
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
        facturas_selecc = [];
        tickets_data = {};
        post = {};
        indice = 0;
        aux_isr = false;
        total_isr = 0;

//        $('.addv').html('<a href="javascript:void(0);" id="btnAddTicket" class="linksm f-r" style="margin: 10px 0 20px 0;" onclick="alerta(\'Seleccione un Cliente !\');"> <img src="'+base_url+'application/images/privilegios/add.png" width="16" height="16">Agregar Facturas</a>');
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