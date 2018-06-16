<?php
class facturacion_model extends privilegios_model{

  function __construct(){
    parent::__construct();
  }

  public function getFacturas(){
    $sql = '';
    //paginacion
    $params = array(
        'result_items_per_page' => '30',
        'result_page' => (isset($_GET['pag'])? $_GET['pag']: 0)
    );
    if($params['result_page'] % $params['result_items_per_page'] == 0)
      $params['result_page'] = ($params['result_page']/$params['result_items_per_page']);

    //Filtros para buscar

    switch ($this->input->get('fstatus')){
      case 'todos':
        $sql = "f.status<>''";
        break;
      case 'pendientes':
        $sql = "f.status='p'";
        break;
      case 'pagados':
        $sql = "f.status='pa'";
        break;
    }

    if($this->input->get('fstatus') =='')
      $sql = "f.status<>''";

    if($this->input->get('ffecha_ini') != '')
      $sql .= ($this->input->get('ffecha_fin') != '') ? " AND DATE(f.fecha)>='".$this->input->get('ffecha_ini')."'" : " AND DATE(f.fecha)='".$this->input->get('ffecha_ini')."'";

    if($this->input->get('ffecha_fin') != '')
      $sql .= ($this->input->get('ffecha_ini') != '') ? " AND DATE(f.fecha)<='".$this->input->get('ffecha_fin')."'" : " AND DATE(f.fecha)='".$this->input->get('ffecha_fin')."'";

    //    if($this->input->get('ffecha_ini') == '' && $this->input->get('ffecha_fin') == '')
      //      $sql .= " AND DATE(tnv.fecha)=DATE(now())";
    if($this->input->get('fidcliente') != '')
      $sql .= " AND f.id_cliente = '".$this->input->get('fidcliente')."'";

    $query = BDUtil::pagination("
        SELECT f.id_factura, f.serie, f.folio, f.fecha, f.condicion_pago, nombre as cliente, f.status
        FROM facturacion as f
        WHERE ".$sql."
        ORDER BY (f.id_factura, DATE(f.fecha)) DESC
        ", $params, true);
        $res = $this->db->query($query['query']);

        $response = array(
            'facturas'      => array(),
            'total_rows'    => $query['total_rows'],
            'items_per_page'  => $params['result_items_per_page'],
            'result_page'     => $params['result_page']
        );
            $response['facturas'] = $res->result();
            return $response;
  }

  public function ajax_get_folio($id_serie_folio=null){
    $id_serie_folio = ($id_serie_folio!=null) ? $id_serie_folio : $_POST['id'];
    $query = $this->db->query("SELECT COALESCE(f.folio,null) as ultimo_folio, fsf.folio_inicio, fsf.folio_fin, fsf.serie, fsf.no_aprobacion, fsf.ano_aprobacion
                  FROM facturacion as f
                  RIGHT JOIN facturacion_series_folios as fsf ON f.serie=fsf.serie
                  WHERE fsf.id_serie_folio = '$id_serie_folio'
                  ORDER BY (f.id_factura, f.fecha) DESC LIMIT 1
        ");
    $result = $query->result();

    $folio=null;
    if($result[0]->ultimo_folio>=$result[0]->folio_inicio && $result[0]->ultimo_folio<$result[0]->folio_fin){
      $folio = floatval($result[0]->ultimo_folio) + 1;
    }
    elseif($result[0]->ultimo_folio==null || $result[0]->ultimo_folio<$result[0]->folio_inicio || $result[0]->ultimo_folio>$result[0]->folio_fin){
      $folio=$result[0]->folio_inicio;
    }

    $params = ($folio!=null) ? array(true,'serie'=>$result[0]->serie,'folio'=>$folio, 'ano_aprobacion'=>$result[0]->ano_aprobacion, 'no_aprobacion'=>$result[0]->no_aprobacion)
                 : array(false,'msg'=>'Ya no hay Folios disponibles');
    return $params;
  }

  public function ajax_get_total_tickets(){
    $response = array();

    foreach ($_POST['tickets'] as $t){

      $res_q1 = $this->db->query("
          SELECT t.id_ticket, t.folio, t.subtotal as subtotal_ticket, t.iva as iva_ticket, t.total as total_ticket
          FROM tickets as t
          WHERE t.id_ticket='$t'
          GROUP BY t.id_ticket, t.folio, t.subtotal, t.iva, t.total
          ");

      $res_q2 = $this->db->query("
          SELECT cantidad, unidad, descripcion, precio_unitario, importe, tipo
          FROM tickets_vuelos_productos
          WHERE id_ticket='$t'
          GROUP BY  cantidad, unidad, descripcion, precio_unitario, importe, tipo
          ");

//      $res = $this->db->query("
    //          SELECT t.id_ticket, t.folio, t.fecha, t.subtotal as subtotal_ticket, t.iva as iva_ticket, t.total as total_ticket, 1 as cantidad, t.total as precio_unitario,
    //          COALESCE(SUM(tvp16.importe_iva),0) as importe_iva_16, COALESCE(SUM(tvp10.importe_iva),0) as importe_iva_10, COALESCE(SUM(tvp0.importe_iva),0) as importe_iva_
    //          FROM tickets as t
    //          LEFT JOIN tickets_vuelos_productos as tvp16 ON t.id_ticket=tvp16.id_ticket AND tvp16.taza_iva='0.16'
    //          LEFT JOIN tickets_vuelos_productos as tvp10 ON t.id_ticket=tvp10.id_ticket AND tvp10.taza_iva='0.1'
    //          LEFT JOIN tickets_vuelos_productos as tvp0 ON t.id_ticket=tvp0.id_ticket AND tvp0.taza_iva='0'
    //          WHERE t.id_ticket='$t'
    //          GROUP BY t.id_ticket, t.folio, t.fecha, t.subtotal, t.iva, t.total
    //          ");

      if($res_q1->num_rows()>0)
        foreach ($res_q1->result() as $itm)
          $response['tickets'][] = $itm;

      if($res_q2->num_rows()>0)
        foreach ($res_q2->result() as $itm)
          $response['productos'][$t][] = $itm;
    }
    return $response;
  }

  public function ajax_actualiza_digitos(){
    $this->load->library('cfd');
    $this->db->update('facturacion',array('metodo_pago_digitos'=>$this->input->post('digitos')),array('id_factura'=>$this->input->post('id')));
    $data = $this->getDataFactura($this->input->post('id'),true);
    $cadena = $this->cfd->obtenCadenaOriginal($data);
    $sello  = $this->cfd->obtenSello($cadena); // OBTIENE EL SELLO DIGITAL

    $this->db->update('facturacion',array('cadena_original'=>$cadena, 'sello'=>$sello), array('id_factura'=>$this->input->post('id')));
    $data = $this->getDataFactura($this->input->post('id'),true);
    $this->cfd->actualizarArchivos($data);
    return array(true);
  }


  public function actualizaDatoscliente(){
    $this->load->library('cfd');
    $data = $this->db->select("*")->from('clientes')->where("nombre_fiscal <> '' AND rfc <> ''")->get();

    foreach ($data->result() as $value) {
      $this->db->update('facturacion',array(
          'calle' => $value->calle,
          'no_exterior' => $value->no_exterior,
          'no_interior' => $value->no_interior,
          'colonia' => $value->colonia,
          'localidad' => $value->localidad,
          'municipio' => $value->municipio,
          'estado' => $value->estado,
          'cp' => $value->cp
          ),
          array('id_cliente'=>$value->id_cliente) );

      echo "Factura ".$value->rfc."<br>\n";
    }
  }

  public function regeneraFacturas(){
    $this->load->library('cfd');
    $data = $this->db->select("*")->from('facturacion')->where("id_nv_fiscal = 1 OR id_nv_fiscal = 2")->order_by("folio", 'asc')->get();

    foreach ($data->result() as $value) {
      $fecha = $this->getFechaXML(substr($value->fecha, 0, 19));
      // $fecha = str_replace(' ', 'T', substr($value->fecha, 0, 19));
      $this->db->update('facturacion', array('fecha_xml'=>$fecha),
          array('id_factura'=>$value->id_factura) );

      $data_fac = $this->getDataFactura($value->id_factura, true);
      $cadena = $this->cfd->obtenCadenaOriginal($data_fac);
      $sello  = $this->cfd->obtenSello($cadena); // OBTIENE EL SELLO DIGITAL

      $this->db->update('facturacion',array('cadena_original'=>$cadena, 'sello'=>$sello),
          array('id_factura'=>$value->id_factura) );
      $data_fac = $this->getDataFactura($value->id_factura, true);
      $this->cfd->actualizarArchivos($data_fac);
      echo "Factura ".$data_fac['serie']."-".$data_fac['folio']." ".$fecha."<br>\n";
    }
  }

  public function regeneraFacturas1(){
    $this->load->library('cfd');
    $data = $this->db->select("*")->from('facturacion')->where("folio BETWEEN 245 AND 245")->order_by("folio", 'asc')->get();

    foreach ($data->result() as $value) {
      $fecha = $this->getFechaXML(substr($value->fecha, 0, 19));
      // $fecha = str_replace(' ', 'T', substr($value->fecha, 0, 19));
      $this->db->update('facturacion', array('fecha_xml'=>$fecha),
          array('id_factura'=>$value->id_factura) );

      $data_fac = $this->getDataFactura($value->id_factura, true);
      $cadena = $this->cfd->obtenCadenaOriginal($data_fac);
      $sello  = $this->cfd->obtenSello($cadena); // OBTIENE EL SELLO DIGITAL

      $this->db->update('facturacion',array('cadena_original'=>$cadena, 'sello'=>$sello),
          array('id_factura'=>$value->id_factura) );
      $data_fac = $this->getDataFactura($value->id_factura, true);
      $this->cfd->actualizarArchivos($data_fac);
      echo "Factura ".$data_fac['serie']."-".$data_fac['folio']." ".$fecha."<br>\n";
    }
  }

  public function regeneraFacturas2(){
    $this->load->library('cfd');
    $data = $this->db->select("*")->from('facturacion')->where("serie = 'FAC' AND folio BETWEEN 71 AND 249")->order_by("folio", 'asc')->get();
    $fechas = array('2013-03-01 09:10:03', '2013-03-02 10:02:22', '2013-03-03 11:02:32', '2013-03-04 08:54:34',
      '2013-03-05 10:36:20', '2013-03-06 09:03:08', '2013-03-07 09:23:08', '2013-03-08 10:03:08');
    $incrementos = array(4, 5, 6, 10, 14, 12);
    $row_x_day = ceil(count($data->result())/count($fechas));
    $contador = 0;
    $ifecha_sel = 5;
    $fechas[$contador] = strtotime($fechas[$contador]);
    foreach ($data->result() as $key => $value) {
      if (($key+1) % $row_x_day == 0) {
        $contador++;
        $fechas[$contador] = strtotime($fechas[$contador]);
        // echo "------------------------------------<br>";
      }

      // if ($contador == $ifecha_sel){
        $fechas[$contador] = strtotime('+'.$incrementos[rand(0, 5)].' minutes '.$incrementos[rand(0, 5)].' seconds', $fechas[$contador]);
        // echo "si - ".date('Y-m-d H:i:s', $fechas[$contador])."<br>";

        $fecha = $this->getFechaXML(date('Y-m-d H:i:s', $fechas[$contador]));
        // $fecha = str_replace(' ', 'T', substr($value->fecha, 0, 19));
        $this->db->update('facturacion', array('fecha_xml'=>$fecha,
              'fecha' => date('Y-m-d H:i:s', $fechas[$contador]),
              'no_certificado' => '00001000000203144869'),
            array('id_factura'=>$value->id_factura) );

        $data_fac = $this->getDataFactura($value->id_factura, true);
        $cadena = $this->cfd->obtenCadenaOriginal($data_fac);
        $sello  = $this->cfd->obtenSello($cadena); // OBTIENE EL SELLO DIGITAL

        $this->db->update('facturacion',array('cadena_original'=>$cadena, 'sello'=>$sello),
            array('id_factura'=>$value->id_factura) );
        $data_fac = $this->getDataFactura($value->id_factura, true);
        $this->cfd->actualizarArchivos($data_fac);
        echo "Factura ".$data_fac['serie']."-".$data_fac['folio']." ".$fecha."<br>\n";
      // }
    }
  }

  public function regeneraFacturas3(){
    $this->load->library('cfd');
    $res = $this->db->select("*")->from('facturacion')->where("serie = 'FAC' AND folio = 236")->order_by("folio", 'asc')->get();
    $data_fac = $res->row();

    $folio = 245;
    $fecha = date('Y-m-d H:i:s');
    $id_factura = BDUtil::getId(); // ID FACTURA
    $data = array(
        'id_factura'          => $id_factura,
        'id_cliente'          => $data_fac->id_cliente,
        'id_empleado'         => $data_fac->id_empleado,
        'serie'               => 'FAC',
        'folio'               => $folio,
        'no_aprobacion'       => $data_fac->no_aprobacion,
        'ano_aprobacion'      => $data_fac->ano_aprobacion,
        'fecha'               => $fecha,
        'importe_iva'         => $data_fac->importe_iva,
        'subtotal'            => $data_fac->subtotal,
        'total'               => $data_fac->total,
        'total_letra'         => $data_fac->total_letra,
        'tipo_comprobante'    => $data_fac->tipo_comprobante,
        'forma_pago'          => $data_fac->forma_pago,
        'sello'               => $data_fac->sello,
        'cadena_original'     => $data_fac->cadena_original,
        'no_certificado'      => $data_fac->no_certificado,
        'version'             => $data_fac->version,
        'fecha_xml'           => $this->getFechaXML($fecha),
        'metodo_pago'         => $data_fac->metodo_pago,
        'condicion_pago'      => $data_fac->condicion_pago,
        'plazo_credito'       => $data_fac->plazo_credito,
        'status'              => $data_fac->status,
        'metodo_pago_digitos' => $data_fac->metodo_pago_digitos,
        'nombre'              => $data_fac->nombre,
        'rfc'                 => $data_fac->rfc,
        'calle'               => $data_fac->calle,
        'no_exterior'         => $data_fac->no_exterior,
        'no_interior'         => $data_fac->no_interior,
        'colonia'             => $data_fac->colonia,
        'localidad'           => $data_fac->localidad,
        'municipio'           => $data_fac->municipio,
        'estado'              => $data_fac->estado,
        'cp'                  => $data_fac->cp,
        'pais'                => $data_fac->pais,
        'total_isr'           => $data_fac->total_isr,
        'observaciones'       => 'Esta factura sustitulle a la factura '.$data_fac->serie.'-'.$data_fac->folio
    );

    $this->db->insert('facturacion', $data); // INSERTA LA INFORMACION DE FACTURA
    $this->db->update('facturacion_abonos', array('id_factura' => $id_factura), "id_factura = '".$data_fac->id_factura."'");
    $this->db->update('facturacion_tickets', array('id_factura' => $id_factura), "id_factura = '".$data_fac->id_factura."'"); // INSERTA LOS TICKETS DE LA FACTURA
    echo "Factura ".$data_fac->serie."-".$data_fac->folio." ".$fecha."<br>\n";
  }

  public function getFechaXML($fecha){
    $partes = explode(' ', $fecha);
    $part_fecha = explode('-', $partes[0]);
    $part_horas = explode(':', $partes[1]);
    $fecha = '';
    foreach ($part_fecha as $key => $value) {
      $part_fecha[$key] = (strlen($value)==1? '0'.$value: $value);
    }
    foreach ($part_horas as $key => $value) {
      $part_horas[$key] = (strlen($value)==1? '0'.$value: $value);
    }
    return $part_fecha[0].'-'.$part_fecha[1].'-'.$part_fecha[2].'T'.$part_horas[0].':'.$part_horas[1].':'.$part_horas[2];
  }

  /**
    * Inicializa los datos que serviran para generar la cadena original.
    *
    * @return array
    */
    private function datosCadenaOriginal()
    {
        $anoAprobacion = explode('-', $_POST['dano_aprobacion']);

        // Obtiene la forma de pago, si es en parcialidades entonces la forma de
        // pago son las parcialidades "Parcialidad 1 de X".
        $formaPago = ($_POST['dforma_pago'] == 'Pago en parcialidades') ? $this->input->post('dforma_pago_parcialidad') : 'Pago en una sola exhibición';

    $this->load->model('clientes_model');

        // Obtiene los datos del receptor.
        $cliente = $this->clientes_model->getInfoCliente($_POST['hcliente'], true);

    //TAMBIEN SE LO AGREGUÉ (JORGE)
    $this->load->model('empresas_model');
    $infoEmpresa = $this->empresas_model->getInfoEmpresa($this->input->post('didempresa'));


        // Array con los datos necesarios para generar la cadena original.
        $data = array(
          'id'                => $this->input->post('didempresa'),
          'table'             => 'empresas',

          'version'             => $infoEmpresa['info']->cfdi_version,//jorge
          'serie'             => $this->input->post('dserie'),
          'folio'             => $this->input->post('dfolio'),
          'fecha'             => str_replace(' ', 'T', $this->input->post('dfecha')),
          'noAprobacion'      => $this->input->post('dno_aprobacion'),
          'anoAprobacion'     => $anoAprobacion[0],
          'tipoDeComprobante' => $this->input->post('dtipo_comprobante'),
          'formaDePago'       => $formaPago, //$this->input->post('dforma_pago'),
          'condicionesDePago' => $this->input->post('dcondicion_pago'),
          'subTotal'          => $this->input->post('subtotal'), //total_importe
          'total'             => $this->input->post('total'),
          'metodoDePago'      => $this->input->post('dmetodo_pago'),
          'NumCtaPago'        => ($_POST['dmetodo_pago'] === 'efectivo') ? 'No identificado' : ($_POST['dmetodo_pago_digitos'] !== '' ? $_POST['dmetodo_pago_digitos']  : 'No identificado'),

          'rfc'               => $cliente['info']->rfc,
          'nombre'            => $cliente['info']->nombre_fiscal,
          'calle'             => $cliente['info']->calle,
          'noExterior'        => $cliente['info']->no_exterior,
          'noInterior'        => $cliente['info']->no_interior,
          'colonia'           => $cliente['info']->colonia,
          'localidad'         => $cliente['info']->localidad,
          'municipio'         => $cliente['info']->municipio,
          'estado'            => $cliente['info']->estado,
          'pais'              => 'MEXICO',
          'codigoPostal'      => $cliente['info']->cp,

          'concepto'          => array(),

          'retencion'         => array(),
          'totalImpuestosRetenidos' => 0,

          'traslado'          => array(),
          'totalImpuestosTrasladados' => 0
        );

        return $data;
    }

  public function addFactura(){
    // Carga la libreria de Facturacion
    // $this->load->library('cfd');
    $this->load->library('cfdi');
    $id_factura = BDUtil::getId(); // ID FACTURA

    $_POST['fecha_xml'] = $this->getFechaXML($this->input->post('dfecha')); //str_replace(' ', 'T', $this->input->post('dfecha'));
    // $forma_pago  = ($_POST['dforma_pago']==1) ? $this->input->post('dforma_pago_parcialidad') : 'Pago en una sola exhibición';

    $no_cta_pago = '';
    $tipoDeComprobante = 'I';
    if ($this->input->post('dtipo_comprobante')=='egreso')
      $tipoDeComprobante = 'E';
    elseif ($this->input->post('dserie')=='T')
      $tipoDeComprobante = 'T';
    // if($_POST['dmetodo_pago']!='efectivo')
    //  if($_POST['dmetodo_pago_digitos']!='' || $_POST['dmetodo_pago_digitos']=='No identificado')
    //    $no_cta_pago =  $this->input->post('dmetodo_pago_digitos');

    $cfdi_ext = [
      'tipoDeComprobante' => $tipoDeComprobante,
      'usoCfdi'           => $this->input->post('duso_cfdi'),
    ];

    $this->cfdi->cargaDatosFiscales($this->input->post('didempresa'));

    // // Parametros para construir la cadena original
    // $cad_data = array(
    //      'serie'     => $this->input->post('dserie'),
    //      'folio'     => $this->input->post('dfolio'),
    //      'fecha_xml'   => $fecha_xml,
    //      'no_aprobacion' => $this->input->post('dno_aprobacion'),
    //      'ano_aprobacion'=> $this->input->post('dano_aprobacion'),
    //      'tipo_comprobante'  => $this->input->post('dtipo_comprobante'),
    //      'forma_pago'    => $this->input->post('dforma_pago'),
    //      'subtotal'      => $this->input->post('subtotal'),
    //      'total'       => $this->input->post('total'),
    //      'metodo_pago'   => $this->input->post('dmetodo_pago'),
    //      'no_cuenta_pago'  => $no_cta_pago,
    //      'moneda'      => 'pesos',

    //      'crfc'      => $this->input->post('frfc'),
    //      'cnombre'   => $this->input->post('dcliente'),
    //      'ccalle'    => $this->input->post('fcalle'),
    //      'cno_exterior'  => $this->input->post('fno_exterior'),
    //      'cno_interior'  => $this->input->post('fno_interior'),
    //      'ccolonia'    => $this->input->post('fcolonia'),
    //      'clocalidad'  => $this->input->post('flocalidad'),
    //      'cmunicipio'  => $this->input->post('fmunicipio'),
    //      'cestado'   => $this->input->post('festado'),
    //      'cpais'     => $this->input->post('fpais'),
    //      'ccp'     => $this->input->post('fcp')
    //    );
    // if(floatval($_POST['total_isr'])>0)
    //  $cad_data['total_isr'] = $this->input->post('total_isr');

    $productos = array();
    $data_t = array();

    $impuestosTrasladados = array();
    $iva_16 = 0;
    $iva_10 = 0;
    $iva_0  = 0;
    $total_iva = 0;
    $tot_prod_iva_0 = 0 ;
    // Ciclo que construye los datos de los tickets a insertar. Tambien obtiene los productos de cada ticket.
    foreach ($_POST as $ticket){
      if(is_array($ticket)){
        $data_t[] = array(
              'id_factura'  => $id_factura,
              'id_ticket'   => $ticket['id_ticket']
        );

        $res_q1= $this->db->query("
              SELECT tvp.id_ticket, tvp.id_ticket_producto, tvp.cantidad, tvp.unidad, tvp.descripcion, tvp.precio_unitario, tvp.importe,
                tvp.taza_iva, tvp.importe_iva
              FROM tickets_vuelos_productos as tvp
              WHERE tvp.id_ticket='{$ticket['id_ticket']}'
              GROUP BY tvp.id_ticket, tvp.id_ticket_producto, tvp.cantidad, tvp.unidad, tvp.descripcion, tvp.precio_unitario, tvp.importe,
                tvp.taza_iva, tvp.importe_iva
            ");

        $res_q2 = $this->db->query("SELECT
                  (SELECT COALESCE(SUM(importe_iva),0) FROM tickets_vuelos_productos WHERE id_ticket='{$ticket['id_ticket']}' AND taza_iva='0.16') as importe_iva_16,
                  (SELECT COALESCE(SUM(importe_iva),0) FROM tickets_vuelos_productos WHERE id_ticket='{$ticket['id_ticket']}' AND taza_iva='0.1') as importe_iva_10,
                  (SELECT COALESCE(SUM(importe_iva),0) FROM tickets_vuelos_productos WHERE id_ticket='{$ticket['id_ticket']}' AND taza_iva='0') as importe_iva_0,
                  (SELECT COUNT(*) FROM tickets_vuelos_productos WHERE id_ticket='{$ticket['id_ticket']}' AND taza_iva='0') as tot_prof_iva_0
                ");

        if($res_q1->num_rows>0)
          foreach ($res_q1->result() as $prod)
            $productos[] = array(
              'cantidad'      => $prod->cantidad,
              'unidad'        => $prod->unidad,
              'claveUnidad'   => [
                'key'   => 'E48',
                'value' => 'Unidad de servicio',
              ],
              'claveProducto' => [
                'key'   => '70141601',
                'value' => 'Servicios de fumigación de cultivos',
              ],
              'descripcion'   => $prod->descripcion,
              'valorUnitario' => $prod->precio_unitario,
              'importe'       => $prod->importe,
              'tazaIva'       => $prod->taza_iva,
              'importeIva'    => $prod->importe_iva,
            );

        if($res_q2->num_rows>0)
          foreach ($res_q2->result() as $iva){
            $iva_16 += floatval($iva->importe_iva_16);
            $iva_10 += floatval($iva->importe_iva_10);
            $iva_0 += floatval($iva->importe_iva_0);
            $tot_prod_iva_0 += intval($iva->tot_prof_iva_0);
          }
      }
    }

    // if($iva_16>0)
    //  $cad_data['ivas'][] = array('tasa_iva'=>'16','importe_iva'=>$iva_16);
    // if($iva_10>0)
    //  $cad_data['ivas'][] = array('tasa_iva'=>'10','importe_iva'=>$iva_10);
    // if($tot_prod_iva_0>0)
    //  $cad_data['ivas'][] = array('tasa_iva'=>'0','importe_iva'=>$iva_0);

    // if(count($cad_data['ivas']) == 0)
    //  $cad_data['ivas'][] = array('tasa_iva'=>'0','importe_iva'=>'0');

    // $cad_data['iva_total'] = $iva_16 + $iva_10 + $iva_0;
    // $cad_data['productos'] = $productos;

    // $cadena_original = $this->cfd->obtenCadenaOriginal($cad_data); // OBTIENE CADENA ORIGINAL
    // $sello   = $this->cfd->obtenSello($cadena_original); // OBTIENE EL SELLO DIGITAL

    // Datos de la factura a insertar
    $data = array(
      'id_factura'       => $id_factura,
      'id_cliente'       => $this->input->post('hcliente'),
      'id_empleado'      => $_SESSION['id_empleado'],
      'id_empresa'       => $this->input->post('didempresa'),
      'serie'            => $this->input->post('dserie'),
      'folio'            => $this->input->post('dfolio'),
      'no_aprobacion'    => $this->input->post('dno_aprobacion'),
      'ano_aprobacion'   => $this->input->post('dano_aprobacion'),
      'fecha'            => $this->input->post('dfecha'),
      'importe_iva'      => $this->input->post('iva'),
      'subtotal'         => $this->input->post('subtotal'),
      'total'            => $this->input->post('total'),
      'total_letra'      => $this->input->post('dtotal_letra'),
      'tipo_comprobante' => $this->input->post('dtipo_comprobante'),
      'sello'            => '', // $sello,
      'cadena_original'  => '', // $cadena_original,
      'no_certificado'   => $this->input->post('dno_certificado'),
      'version'          => $this->cfdi->version,
      'fecha_xml'        => $_POST['fecha_xml'],
      'metodo_pago'      => $this->input->post('dmetodo_pago'),
      'condicion_pago'   => ($_POST['dcondicion_pago']=='credito') ? 'cr' : 'co',
      'plazo_credito'    => $this->input->post('fplazo_credito'),
      'nombre'           => $this->input->post('dcliente'),
      'rfc'              => $this->input->post('frfc'),
      'calle'            => $this->input->post('fcalle'),
      'no_exterior'      => $this->input->post('fno_exterior'),
      'no_interior'      => $this->input->post('fno_interior'),
      'colonia'          => $this->input->post('fcolonia'),
      'localidad'        => $this->input->post('flocalidad'),
      'municipio'        => $this->input->post('fmunicipio'),
      'estado'           => $this->input->post('festado'),
      'cp'               => $this->input->post('fcp'),
      'pais'             => $this->input->post('fpais'),
      'total_isr'        => $this->input->post('total_isr'),
      'observaciones'    => $this->input->post('fobservaciones')
    );

    if($_POST['dforma_pago']==1)
      $data['forma_pago'] = $this->input->post('dforma_pago_parcialidad');

    if($_POST['dmetodo_pago']!='efectivo')
      if($_POST['dmetodo_pago_digitos']!='' || $_POST['dmetodo_pago_digitos']=='No identificado')
        $data['metodo_pago_digitos'] = $this->input->post('dmetodo_pago_digitos');

    if($_POST['dcondicion_pago']=='credito')
      $data['status'] = 'p';

    $this->db->insert('facturacion',$data); // INSERTA LA INFORMACION DE FACTURA
    $this->db->insert_batch('facturacion_tickets',$data_t); // INSERTA LOS TICKETS DE LA FACTURA

    if($_POST['dcondicion_pago']=='contado'){
      $concepto = "Pago total de la Venta ({$_POST['dfolio']})";
      $res = $this->abonar_factura(true,$id_factura,null,$concepto);
    }
    elseif($_POST['dcondicion_pago']=='credito'){
      $res = $this->abonar_factura(false,$id_factura,null,"");
    }

    // $data_f = $this->getDataFactura($id_factura,true);
    // $this->cfd->generaArchivos($data_f);

    /*ESTO ES LO NUEVO QUE AGREGUÉ*/

      // // Obtiene los datos para la cadena original
      // $datosCadOrig = $this->datosCadenaOriginal();

      // $datosCadOrig['sinCosto']   =  isset($_POST['dsincosto']) ? true : false;

      // // Asignamos los productos o conceptos a los datos de la cadena original.
      // $datosCadOrig['concepto']  = $cad_data['productos'];

      // // Asignamos las retenciones a los datos de la cadena original.
      //  $impuestosRetencion = array(
      //   'impuesto' => 'IVA',
      //   'importe'  => $this->input->post('total_retiva'),
      // );

      // $datosCadOrig['retencion'][] = $impuestosRetencion;
      // $datosCadOrig['totalImpuestosRetenidos'] = $this->input->post('total_retiva');

      // $impuestosTraslados = array();

      // // Si hay conceptos con traslado 11% lo agrega.
      // if ($iva_10 > 0)
      // {
      //   $impuestosTraslados[] = array(
      //  'Impuesto' => 'IVA',
      //  'tasa'     => '10',
      //  'importe'  => $iva_10,
      //   );
      // }

      // // Si hay conceptos con traslado 16% lo agrega.
      // if ($iva_16 > 0)
      // {
      //   $impuestosTraslados[] = array(
      //  'Impuesto' => 'IVA',
      //  'tasa'     => '16',
      //  'importe'  => $iva_16,
      //   );
      // }

      // // Si hay conceptos con traslado 0% lo agrega.
      // if($tot_prod_iva_0>0 || count($impuestosTraslados) == 0)
      // {
      //  $impuestosTraslados[] = array(
      //    'Impuesto' => 'IVA',
      //    'tasa'     => '0',
      //    'importe'  => '0',
      //  );
      // }


    // xml 3.3
    $datosApi = $this->cfdi->obtenDatosCfdi33($_POST, $productos);

      // // Asigna los impuestos traslados.
      // $datosCadOrig['traslado']  = $impuestosTraslados;
      // $datosCadOrig['totalImpuestosTrasladados'] = $this->input->post('total_iva');

      // $cadenaOriginal = $this->cfdi->obtenCadenaOriginal($datosCadOrig);
      // $sello          = $this->cfdi->obtenSello($cadenaOriginal['cadenaOriginal']);

      // // Obtiene el contentido del certificado.
      // $certificado = $this->cfdi->obtenCertificado($this->db
      //   ->select('cer')
      //   ->from("empresas")
      //   ->where("id_empresa", $_POST['didempresa'])
      //   ->get()->row()->cer
      // );

      // //var_dump($certificado);

      // $dataCliente = array(
      //   'id_factura'  => $id_factura,
      //   'nombre'      => $datosCadOrig['nombre'],
      //   'rfc'         => $datosCadOrig['rfc'],
      //   'calle'       => $datosCadOrig['calle'],
      //   'no_exterior' => $datosCadOrig['noExterior'],
      //   'no_interior' => $datosCadOrig['noInterior'],
      //   'colonia'     => $datosCadOrig['colonia'],
      //   'localidad'   => $datosCadOrig['localidad'],
      //   'municipio'   => $datosCadOrig['municipio'],
      //   'estado'      => $datosCadOrig['estado'],
      //   'cp'          => $datosCadOrig['codigoPostal'],
      //   'pais'        => $datosCadOrig['pais'],
      // );

      // // Datos que actualizara de la factura
      // $updateFactura = array(
      //   'cadena_original' => $cadenaOriginal['cadenaOriginal'],
      //   'sello'           => $sello,
      //   'certificado'     => $certificado
      // );
      // $this->db->update('facturacion', $updateFactura, array('id_factura' => $id_factura));

      // // Datos para el XML3.2
      // $datosXML               = $cadenaOriginal['datos'];
      // $datosXML['id']         = $this->input->post('didempresa');
      // $datosXML['sinCosto']   =  isset($_POST['dsincosto']) ? true : false;
      // $datosXML['table']      = 'empresas';
      // $datosXML['comprobante']['fecha']         = $fecha_xml;
      // $datosXML['comprobante']['serie']         = $this->input->post('dserie');
      // $datosXML['comprobante']['folio']         = $this->input->post('dfolio');
      // $datosXML['comprobante']['sello']         = $sello;
      // $datosXML['comprobante']['noCertificado'] = $this->input->post('dno_certificado');
      // $datosXML['comprobante']['certificado']   = $certificado;
      // $datosXML['concepto']                     = $cad_data['productos'];

      // $datosXML['domicilio']['calle']        = $dataCliente['calle'];
      // $datosXML['domicilio']['noExterior']   = $dataCliente['no_exterior'];
      // $datosXML['domicilio']['noInterior']   = $dataCliente['no_interior'];
      // $datosXML['domicilio']['colonia']      = $dataCliente['colonia'];
      // $datosXML['domicilio']['localidad']    = $dataCliente['localidad'];
      // $datosXML['domicilio']['municipio']    = $dataCliente['municipio'];
      // $datosXML['domicilio']['estado']       = $dataCliente['estado'];
      // $datosXML['domicilio']['pais']         = $dataCliente['pais'];
      // $datosXML['domicilio']['codigoPostal'] = $dataCliente['cp'];

      // $datosXML['totalImpuestosRetenidos']   = $this->input->post('total_retiva');
      // $datosXML['totalImpuestosTrasladados'] = $this->input->post('total_iva');

      // $datosXML['retencion'] = $impuestosRetencion;
      // $datosXML['traslado']  = $impuestosTraslados;

      // // Genera el archivo XML y lo guarda en disco.
      // $archivos = $this->cfdi->generaArchivos($datosXML);

      // Timbrado de la factura.
      $datosApi['cfdi_ext'] = $cfdi_ext;
      $result = $this->timbrar($datosApi, $id_factura);

      if ($result['passes'])
      {
        $this->load->model('documentos_model');
        $pathDocs = $this->documentos_model->creaDirectorioDocsCliente($datosApi['receptor']['nombreFiscal'], $datosApi['serie'], $datosApi['folio']);

        $this->generaFacturaPdf($id_factura, $pathDocs);

        $response = $this->facturacion_model->enviarEmail($id_factura);
        return array(true,'id_factura'=>$id_factura, 'resultado' => $result);
      }

    /*TERMINA LO NUEVO QUE AGREGUÉ*/
    return array(false,'id_factura'=>$id_factura, 'resultado' => $result);
  }

  /**
    * Realiza el timbrado de una factura.
    *
    * @param  string $xml
    * @param  string $idFactura
    * @param  boolean $delFiles
    * @return void
    */
    private function timbrar($dataXml, $idFactura, $delFiles = true)
    {
      $this->load->library('facturartebarato_api');

      // $this->facturartebarato_api->setPathXML($dataXml);

      // Realiza el timbrado usando la libreria.
      $timbrado = $this->facturartebarato_api->timbrar($dataXml);

      // echo "<pre>";
      //   var_dump($timbrado);
      // echo "</pre>";exit;

      $result = array(
        'id_factura' => $idFactura,
        'codigo'     => $timbrado->codigo,
        'result'     => $timbrado,
      );

      // Si no hubo errores al momento de realizar el timbrado.
      if ($timbrado->status)
      {
        // Si el codigo es 501:Autenticación no válida o 708:No se pudo conectar al SAT,
        // significa que el timbrado esta pendiente.
        if ($timbrado->codigo === '501' || $timbrado->codigo === '708')
        {
          // Se coloca el status de timbre de la factura como pendiente.
          $statusTimbrado = 'p';
        }
        else
        {
          // Si el timbrado se realizo correctamente.

          // Se coloca el status de timbre de la factura como timbrado.
          $statusTimbrado = 't';
        }

        // Actualiza los datos en la BDD.
        $dataTimbrado = array(
          'xml'             => $timbrado->data->xml,
          'status_timbrado' => $statusTimbrado,
          'uuid'            => $timbrado->data->uuid,
          'cadena_original' => $timbrado->data->cadenaOriginal,
          'sello'           => $timbrado->data->sello,
          'certificado'     => $dataXml['emisor']['cer'],
          'cfdi_ext'        => json_encode($dataXml),
        );
        $this->db->update('facturacion', $dataTimbrado, array('id_factura' => $idFactura));
        log_message('error', var_export($dataTimbrado, true));

        $result['passes'] = true;
      }
      else
      {
        log_message('error', var_export($timbrado, true));
        // Si es true $delFile entonces elimina todo lo relacionado con la factura.
        if ($delFiles)
        {
          $this->db->delete('facturacion', array('id_factura' => $idFactura));
          // unlink($pathXML);
        }

        // Entra si hubo un algun tipo de error de conexion a internet.
        if ($timbrado->codigo === 'ERR_INTERNET_DISCONNECTED')
          $result['msg'] = 'Error Timbrado: Internet Desconectado. Verifique su conexión para realizar el timbrado.';
        elseif ($timbrado->codigo === '500')
          $result['msg'] = 'Error en el servidor del timbrado. Pongase en contacto con el equipo de desarrollo del sistema.';
        else
          $result['msg'] = $timbrado->mensaje;

        $result['passes'] = false;
      }

       //echo "<pre>";
      // var_dump($timbrado);
      // echo "</pre>";exit;

      return $result;
    }


  public function cancelFactura($id_factura=''){
    $this->db->update('facturacion',array('status'=>'ca'),array('id_factura'=>$id_factura));
    return array(true);
  }

  /**
   * Cancela una factura. Cambia el status a 'ca'.
  *
  * @return array
   */
  public function cancelaFactura($idFactura)
  {
    $this->load->library('cfdi');
    $this->load->library('facturartebarato_api');
    $this->load->model('documentos_model');

    // Obtenemos la info de la factura a cancelar.
    $factura = $this->getDataFactura($idFactura);

    if($factura['uuid'] != '')
    {
      // Carga los datos fiscales de la empresa dentro de la lib CFDI.
      $this->cfdi->cargaDatosFiscales($factura['id_empresa']);
      $status_uuid = '708';

      // Parametros que necesita el webservice para la cancelacion.
      $params = array(
        'rfc'   => $factura['empresa_rfc'],
        'uuids' => $factura['uuid'],
        'cer'   => $this->cfdi->obtenCer(),
        'key'   => $this->cfdi->obtenKey(),
      );

      // Lama el metodo cancelar para que realiza la peticion al webservice.
      $result = $this->facturartebarato_api->cancelar($params);

      if(isset($result->data->status_uuid)){
        $status_uuid = $result->data->status_uuid;
        if ($result->data->status_uuid === '201' || $result->data->status_uuid === '202')
        {
          $this->db->update('facturacion',
          array('status' => 'ca', 'status_timbrado' => 'ca'),
          "id_factura = '{$idFactura}'"
          );

          // Regenera el PDF de la factura.
          $pathDocs = $this->documentos_model->creaDirectorioDocsCliente($factura['cnombre'], $factura['serie'], $factura['folio']);
          $this->generaFacturaPdf($idFactura, $pathDocs);

          $this->enviarEmail($idFactura);
        }
        return array('msg' => $status_uuid);
      }
      return array('msg' => 14);
    }else{
      $this->db->update('facturacion',
        array('status' => 'ca', 'status_timbrado' => 'ca'),
        "id_factura = '{$idFactura}'"
        );
      return array('msg' => 202);
    }

  }


  /**
   * @param string $id_factura -- ID de la factura
   * @param boolean $ivas -- TRUE: Agrega los IVAS al resultado FALSE: No agrega los IVAS
   * @return array
   */
  public function getDataFactura($id_factura=null, $ivass=false, $sql = ''){
    $id_factura = ($id_factura) ? $id_factura : $this->input->get('id');

    if ($sql == '')
      $sql = "id_factura = '".$id_factura."'";

    $res_q1 = $this->db->select("f.*,cl.email as email_cliente,em.email as email_empresa,
        f.id_empresa as empresa_id,em.rfc as empresa_rfc,em.nombre_fiscal as empresa_nombre_fiscal,em.logo")
      ->from('facturacion as f')->join('empresas as em','f.id_empresa = em.id_empresa','left')
      ->join('clientes as cl','cl.id_cliente = f.id_cliente','left')
      ->where($sql)->order_by('serie asc, folio asc')->get();

    foreach ($res_q1->result() as $key => $value) {
      $res_q2 = $this->db->query("
            SELECT tvp.id_ticket, tvp.id_ticket_producto, tvp.cantidad, tvp.unidad, tvp.descripcion, tvp.precio_unitario, tvp.importe,
              t.folio
            FROM facturacion as f
            INNER JOIN facturacion_tickets as ft ON f.id_factura=ft.id_factura
            INNER JOIN tickets_vuelos_productos as tvp ON ft.id_ticket=tvp.id_ticket
            INNER JOIN tickets as t ON t.id_ticket=ft.id_ticket
            WHERE f.id_factura='".$value->id_factura."'
            GROUP BY tvp.id_ticket, tvp.id_ticket_producto, tvp.cantidad, tvp.unidad, tvp.descripcion, tvp.precio_unitario, tvp.importe, t.folio
          ");

      $productos = array();
      foreach($res_q2->result() as $itm)
        $productos[] = array('folio'=>$itm->folio,'cantidad'=>$itm->cantidad, 'unidad'=>$itm->unidad, 'descripcion'=>$itm->descripcion, 'precio_unit'=>$itm->precio_unitario, 'importe'=>$itm->importe);

      $data = array(
            'id_nv_fiscal' => $value->id_nv_fiscal,

            'id_empresa' => $value->empresa_id,
            'nombre_fiscal' => $value->empresa_nombre_fiscal,
            'empresa_rfc' => $value->empresa_rfc,

            'uuid' => $value->uuid,
            'nombre' => $value->nombre,
            'logo' => $value->logo,
            'xml' => $value->xml,
            'serie' => $value->serie,
            'folio' => $value->folio,
            'no_aprobacion'   => $value->no_aprobacion,
            'ano_aprobacion'  => $value->ano_aprobacion,
            'importe_iva'   => $value->importe_iva,
            'subtotal'      => $value->subtotal,
            'total'       => $value->total,
            'total_letra'   => $value->total_letra,
            'sello'       => $value->sello,
            'cadena_original' => $value->cadena_original,
            'no_certificado'  => $value->no_certificado,
            'version'     => $value->version,
            'fecha_xml'     => $value->fecha_xml,

            'tipo_comprobante'  => $value->tipo_comprobante,
            'forma_pago'    => $value->forma_pago,
            'metodo_pago'   => $value->metodo_pago,
            'descuento'     => 0,
            'moneda'      => 'pesos',
            'no_cuenta_pago'  => $value->metodo_pago_digitos,
            'fecha' => $value->fecha,

            'id_cliente' => $value->id_cliente,
            'email' => $value->email_cliente,
            'email_empresa' => $value->email_empresa,
            'cnombre'     => $value->nombre,
            'crfc'        => $value->rfc,
            'ccalle'        => $value->calle,
            'cno_exterior'  => $value->no_exterior,
            'cno_interior'  => $value->no_interior,
            'ccolonia'    => $value->colonia,
            'clocalidad'  => $value->localidad,
            'cmunicipio'  => $value->municipio,
            'cestado'   => $value->estado,
            'ccp'   => $value->cp,
            'cpais'   => $value->pais,
            'fobservaciones'  => $value->observaciones,
            'observaciones' => $value->observaciones,
            'productos' => $productos,

            'status_timbrado' => $value->status_timbrado,
            'condicion_pago'=> $value->condicion_pago,
            'plazo_credito' => $value->plazo_credito,
            'status'    => $value->status,

            'cfdi_ext' => $value->cfdi_ext,
      );

      if(floatval($value->total_isr)>0)
        $data['total_isr'] = $value->total_isr;

      if($ivass){
        $ivas = $this->getIvas($value->id_factura);

        $data['ivas'] = $ivas['ivas'];
        $data['iva_total'] = $ivas['iva_total'];
      }

      $response[] = $data;
    }

    if (count($response) == 1)
      return $response[0];
    else
      return $response;
  }

  private function getIvas($id_factura){
    $iva_16 = 0;
    $iva_10 = 0;
    $iva_0  = 0;
    $total_iva = 0;
    $tot_prod_iva_0 = 0;

    $ivas = array();
    $res_q1= $this->db->select("id_ticket")->from("facturacion_tickets")->where("id_factura",$id_factura)->get()->result();

    // Ciclo que construye los datos de los tickets a insertar. Tambien obtiene los productos de cada ticket.
    foreach ($res_q1 as $ticket){
      $res_q2 = $this->db->query("SELECT
              (SELECT COALESCE(SUM(importe_iva),0) FROM tickets_vuelos_productos WHERE id_ticket='$ticket->id_ticket' AND taza_iva='0.16') as importe_iva_16,
              (SELECT COALESCE(SUM(importe_iva),0) FROM tickets_vuelos_productos WHERE id_ticket='$ticket->id_ticket' AND taza_iva='0.1') as importe_iva_10,
              (SELECT COALESCE(SUM(importe_iva),0) FROM tickets_vuelos_productos WHERE id_ticket='$ticket->id_ticket' AND taza_iva='0') as importe_iva_0,
              (SELECT COUNT(*) FROM tickets_vuelos_productos WHERE id_ticket='$ticket->id_ticket' AND taza_iva='0') as tot_prof_iva_0
          ");

      if($res_q2->num_rows>0)
        foreach ($res_q2->result() as $iva){
          $iva_16 += floatval($iva->importe_iva_16);
          $iva_10 += floatval($iva->importe_iva_10);
          $iva_0 += floatval($iva->importe_iva_0);
          $tot_prod_iva_0 += intval($iva->tot_prof_iva_0);
        }
    }

    $ivas['ivas'] = array();
    if($iva_16>0)
      $ivas['ivas'][] = array('tasa_iva'=>'16','importe_iva'=>$iva_16);
    if($iva_10>0)
      $ivas['ivas'][] = array('tasa_iva'=>'10','importe_iva'=>$iva_10);
    //if($tot_prod_iva_0>0)
      $ivas['ivas'][] = array('tasa_iva'=>'0','importe_iva'=>$iva_0);

    $ivas['iva_total'] = $iva_16 + $iva_10 + $iva_0;

    return $ivas;
  }

  public function abonar_factura($liquidar=false,$id_factura=null,$abono=null,$concepto=null){

    $id_factura = ($id_factura==null) ? $this->input->get('id') : $id_factura;
    $concepto = ($concepto==null) ? $this->input->post('fconcepto') : $concepto;

    $factura_info = $this->get_info_abonos($id_factura);

    if($factura_info->status=='p'){
      $pagado = false;
      $total = false;
      if($liquidar){
        if($factura_info->abonado <= $factura_info->total)
          $total = $factura_info->restante;
        elseif($factura_info->restante == $factura_info->total)
        $total = $factura_info->total;

        $pagado = true;
      }
      else{
        if(!is_null($abono)){
          $total = ($abono > $factura_info->restante)?$factura_info->restante:$abono;
          if(floatval(($total+$factura_info->abonado))>=floatval($factura_info->total))
            $pagado=true;
        }
        else{
          $total_abonado_tickets = $this->db->select("SUM(ta.total) as total_abonado_tickets")
                            ->from("tickets_abonos AS ta")
                            ->join("facturacion_tickets AS ft","ta.id_ticket=ft.id_ticket","inner")
                            ->where("ft.id_factura",$id_factura)
                            ->get()->row()->total_abonado_tickets;

          if(floatval($total_abonado_tickets)>0){
            $concepto = 'Pagos y abonos de los tickets agregados a la factura';
            $total = $total_abonado_tickets;

            if(floatval($total_abonado_tickets)>=$factura_info->total)
              $pagado=true;
          }

        }
      }

      if($total!=false){
        $id_abono = BDUtil::getId();
        $data = array(
            'id_abono'  => $id_abono,
            'id_factura'=> $id_factura,
            'fecha'   => $this->input->post('ffecha')!='' ? $this->input->post('ffecha') : date("Y-m-d"),
            'concepto'  => $concepto,
            'total'   => floatval($total)
        );
        $this->db->insert('facturacion_abonos',$data);

        if($pagado)
          $this->db->update('facturacion',array('status'=>'pa'),array('id_factura'=>$id_factura));

        return array(true);
      } return array(false, 'msg'=>'No puede realizar la operación');
    }
    else return array(false,'msg'=>'No puede realizar mas abonos porque la factura ya esta totalmente pagada');
  }

  public function get_info_abonos($id_factura=null){

    $id_factura = ($id_factura==null) ? $this->input->get('id') : $id_factura;
    $res =  $this->db->select("SUM(fa.total) AS abonado, (f.total-SUM(fa.total)) as restante, f.total, f.status")
    ->from("facturacion_abonos as fa")
    ->join("facturacion as f", "fa.id_factura=f.id_factura","inner")
    ->where(array("tipo"=>"ab","f.status !=" =>"ca","fa.id_factura"=>$id_factura))
    ->group_by("f.total, f.status")
    ->get();

    if($res->num_rows==0){
      $res =  $this->db->select('(0) as abonado, f.total as restante, f.total, f.status')
      ->from("facturacion as f")
      ->where(array("f.status !=" =>"ca","f.id_factura"=>$id_factura))
      ->get();
    }
    return $res->row();
  }

  public function eliminar_abono()
  {
    $this->db->delete('facturacion_abonos',array('id_abono' => $_GET['ida']));
    $info_abonos = $this->get_info_abonos();

    if ($info_abonos->restante != 0 )
      $this->db->update('facturacion',array('status'=>'p'),array('id_factura'=>$_GET['id']));
    return true;
  }

  public function getSeriesFolios(){

    //paginacion
    $params = array(
        'result_items_per_page' => '30',
        'result_page' => (isset($_GET['pag'])? $_GET['pag']: 0)
    );
    if($params['result_page'] % $params['result_items_per_page'] == 0)
      $params['result_page'] = ($params['result_page']/$params['result_items_per_page']);

//    if($this->input->get('fserie')!='')
//      $this->db->where('serie',$this->input->get('fserie'));

    $this->db->like('lower(serie)',mb_strtolower($this->input->get('fserie'), 'UTF-8'));
    $this->db->order_by('serie');
    $this->db->from('facturacion_series_folios as t1');
    $this->db->join('empresas as t2','t1.id_empresa = t2.id_empresa','left')->get();
    $sql  = $this->db->last_query();

    $query = BDUtil::pagination($sql, $params, true);
    $res = $this->db->query($query['query']);

    $data = array(
        'series'      => array(),
        'total_rows'    => $query['total_rows'],
        'items_per_page'  => $params['result_items_per_page'],
        'result_page'     => $params['result_page']
    );

    if($res->num_rows() > 0)
      $data['series'] = $res->result();

    return $data;
  }

  public function getInfoSerieFolio($id_serie_folio = ''){
    $id_serie_folio = ($id_serie_folio != '') ? $id_serie_folio : $this->input->get('id');

    $res = $this->db->select('*')->from('facturacion_series_folios')->where('id_serie_folio',$id_serie_folio)->get()->result();
    return $res;
  }

  public function addSerieFolio(){
    $path_img = '';
    //valida la imagen
    $upload_res = UploadFiles::uploadImgSerieFolio();

    if(is_array($upload_res)){
      if($upload_res[0] == false)
        return array(false, $upload_res[1]);
      $path_img = APPPATH.'images/series_folios/'.$upload_res[1]['file_name'];
    }

    $id_serie_folio = BDUtil::getId();
    $data = array(
        'id_serie_folio' => $id_serie_folio,
        'id_empresa'     => $this->input->post('fidempresa'),
        'serie'          => strtoupper($this->input->post('fserie')),
        'no_aprobacion'  => $this->input->post('fno_aprobacion'),
        'folio_inicio'   => $this->input->post('ffolio_inicio'),
        'folio_fin'      => $this->input->post('ffolio_fin'),
        'ano_aprobacion' => $this->input->post('fano_aprobacion'),
        'tipo'           => $this->input->post('ftipo'),
        'imagen'         => $path_img,
    );

    if($this->input->post('fleyenda')!='')
      $data['leyenda'] = $this->input->post('fleyenda');

    if($this->input->post('fleyenda1')!='')
      $data['leyenda1'] = $this->input->post('fleyenda1');

    if($this->input->post('fleyenda2')!='')
      $data['leyenda2'] = $this->input->post('fleyenda2');

    $this->db->insert('facturacion_series_folios',$data);
    return array(true);
  }

  public function editSerieFolio($id_serie_folio=''){
    $id_serie_folio = ($id_serie_folio != '') ? $id_serie_folio : $this->input->get('id');

    $data = array(
        'id_empresa'     => $this->input->post('fidempresa'),
        'serie'          => strtoupper($this->input->post('fserie')),
        'no_aprobacion'  => $this->input->post('fno_aprobacion'),
        'folio_inicio'   => $this->input->post('ffolio_inicio'),
        'folio_fin'      => $this->input->post('ffolio_fin'),
        'ano_aprobacion' => $this->input->post('fano_aprobacion'),
        'tipo'           => $this->input->post('ftipo'),
    );

    $path_img = '';
    //valida la imagen
    $upload_res = UploadFiles::uploadImgSerieFolio();

    if(is_array($upload_res)){
      if($upload_res[0] == false)
        return array(false, $upload_res[1]);
      $path_img = APPPATH.'images/series_folios/'.$upload_res[1]['file_name'];

      $old_img = $this->db->select('imagen')->from('facturacion_series_folios')->where('id_serie_folio',$id_serie_folio)->get()->row()->imagen;

      if($old_img!='')
        UploadFiles::deleteFile($old_img);

      $data['imagen'] = $path_img;
    }

    if($this->input->post('fleyenda')!='')
      $data['leyenda'] = $this->input->post('fleyenda');

    if($this->input->post('fleyenda1')!='')
      $data['leyenda1'] = $this->input->post('fleyenda1');

    if($this->input->post('fleyenda2')!='')
      $data['leyenda2'] = $this->input->post('fleyenda2');

    $this->db->update('facturacion_series_folios',$data, array('id_serie_folio'=>$id_serie_folio));

    return array(true);
  }

  public function exist($table, $sql, $return_res=false){
    $res = $this->db->get_where($table, $sql);
    if($res->num_rows() > 0){
      if($return_res)
        return $res->row();
      return TRUE;
    }
    return FALSE;
  }

  public function getFacturasReporteMensual() {
    $sql = $this->db->query("SELECT rfc, serie, folio, no_aprobacion, EXTRACT(YEAR from fecha) as anio, fecha, total, importe_iva, status
                  FROM facturacion
                  WHERE EXTRACT(YEAR from fecha) = '{$this->input->post('fano')}' AND EXTRACT(MONTH from fecha) = '{$this->input->post('fmes')}'
                  ORDER BY fecha ASC
                ");

    $str_data = "";
    if($sql->num_rows() > 0){
      $res = $sql->result();
      foreach( $res as $f){
        $s = substr($f->fecha,0,19);
        list($y, $m, $d) = explode('-',substr($s,0,10));
        list($h, $mi, $s) = explode(':', substr($s,11, 19));

        $str_data .= "|".$f->rfc."|".$f->serie."|".$f->folio."|".$f->anio.$f->no_aprobacion."|".date('d/m/Y H:i:s',mktime($h,$mi,$s, $m, $d, $y))."|".number_format($f->total,2,'.','')."|".number_format($f->importe_iva,2,'.','')."|".(($f->status == "ca")?"1":"0")."|I||||\n";
      }
    }

    return $str_data;
  }

  public function getPdfReporteMensual() {
    $_POST['fano'] = $_GET['fano'];
    $_POST['fmes'] = $_GET['fmes'];
    // $string = $this->getFacturasReporteMensual();

    $sql = $this->db->query("SELECT rfc, serie, folio, no_aprobacion, EXTRACT(YEAR from fecha) as anio, fecha, total, importe_iva, status
                  FROM facturacion
                  WHERE EXTRACT(YEAR from fecha) = '{$this->input->post('fano')}' AND EXTRACT(MONTH from fecha) = '{$this->input->post('fmes')}'
                  ORDER BY fecha ASC
                ");

    $this->load->library('mypdf');
    // Creación del objeto de la clase heredada
    $pdf = new MYpdf('P', 'mm', 'Letter');
    $pdf->show_head = true;
    $pdf->titulo2 = 'Reporte Mensual';
    $pdf->titulo3 = String::mes($_POST['fmes'])." del {$_POST['fano']}\n";
    //$pdf->titulo3 .=  $nombre_producto;
    $pdf->AliasNbPages();
    $pdf->AddPage();

    // $links = array('', '', '', '');
    $pdf->SetY(30);
    $aligns = array('C', 'C', 'C', 'C','C', 'C', 'C', 'C', 'C');
    $widths = array(25, 10, 15, 20, 24, 35, 30, 30, 18);
    $header = array('Rfc', 'Serie', 'Folio', 'Año', 'No Aprobación', 'Fecha', 'Total', 'IVA', 'Estado',);

    foreach($sql->result() as $key => $item){
      $band_head = false;
      if($pdf->GetY() >= 200 || $key==0){ //salta de pagina si exede el max
        if($key > 0)
          $pdf->AddPage();

        $pdf->SetFont('Arial','B',8);
        $pdf->SetTextColor(255,255,255);
        $pdf->SetFillColor(140,140,140);
        $pdf->SetX(5);
        $pdf->SetAligns($aligns);
        $pdf->SetWidths($widths);
        $pdf->Row($header, true);
      }

      $pdf->SetFont('Arial','',8);
      $pdf->SetTextColor(0,0,0);

      $datos = array($item->rfc, $item->serie, $item->folio, $item->anio, $item->no_aprobacion,
              str_replace('-','/',$item->fecha), String::formatoNumero($item->total),String::formatoNumero($item->importe_iva), ($item->status=='ca')?'Cancelada':'Pagada');

      $pdf->SetX(5);
      $pdf->SetAligns($aligns);
      $pdf->SetWidths($widths);
      $pdf->Row($datos, false);
    }


    // $pdf->SetXY(5, 30);
    // $pdf->SetFont('Arial','',9);
    // $pdf->SetAligns(array('L'));
    // $pdf->SetWidths(array(205));
    // $pdf->Row(array($string), false, false);


    $pdf->Output('Reporte_Mensual_'.$_POST['fano'].$_POST['fmes'].'.pdf', 'I');
  }

  /*
    |------------------------------------------------------------------------
    | FACTURA PDF
    |------------------------------------------------------------------------
    */
    public function generaFacturaPdf($idFactura, $path = null) {
      $factura = $this->getDataFactura($idFactura);
      $version = str_replace('.', '', $factura['version']);
      $this->{"generaFacturaPdf{$version}"}($factura, $path);
    }

    public function generaFacturaPdf32($factura, $path = null)
    {
        include(APPPATH.'libraries/phpqrcode/qrlib.php');

        // $factura = $this->getDataFactura($idFactura);

        $xml = simplexml_load_string(str_replace(array('cfdi:', 'tfd:'), '', $factura['xml']));

        // echo "<pre>";
        //   var_dump($factura, $xml);
        // echo "</pre>";exit;

        $this->load->library('mypdf');

        // Creación del objeto de la clase heredada
        $pdf = new MYpdf('P', 'mm', 'Letter');

        $pdf->show_head = false;

        $pdf->AliasNbPages();
        $pdf->AddPage();

        //////////
        // Logo //
        //////////

        $pdf->SetXY(0, 0);
        // $pdf->SetXY(30, 2);
        $logo = (file_exists($factura['logo'])) ? $factura['logo'] : 'application/images/logo2.png' ;
        $pdf->Image($logo, 10, null, 0, 21);

        //////////////////////////
        // Rfc y Regimen Fiscal //
        //////////////////////////

        // 0, 171, 72 = verde

        $pdf->SetFont('helvetica','B', 9);
        // $pdf->SetFillColor(214, 214, 214);
        $pdf->SetTextColor(255, 255, 255);
        // $pdf->SetXY(0, 0);
        // $pdf->Cell(108, 15, "Factura Electrónica (CFDI)", 0, 0, 'C', 1);

        // $pdf->SetTextColor(0, 0, 0);
        // $pdf->SetXY(0, $pdf->GetY());
        // $pdf->Cell(108, 4, "RFC: {$xml->Emisor[0]['rfc']}", 0, 0, 'C', 0);

        // $pdf->SetFont('helvetica','B', 12);
        $pdf->SetFillColor(140,140, 140);
        $pdf->SetTextColor(255,255,255);
        $pdf->SetXY(0, $pdf->GetY() + 4);
        $pdf->Cell(108, 4, "Emisor:", 0, 0, 'L', 1);

        $pdf->SetFont('helvetica','', 8);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetXY(0, $pdf->GetY() + 4);

        $pdf->SetX(0);
        $pdf->SetAligns(array('L', 'L'));
        $pdf->SetWidths(array(19, 93));
        $pdf->Row(array('RFC:', $xml->Emisor[0]['rfc']), false, false, null, 2, 1);
        $pdf->SetWidths(array(19, 196));
        $pdf->SetX(0);
        $pdf->Row(array('NOMBRE:', $xml->Emisor[0]['nombre']), false, false, null, 2, 1);
        $pdf->SetX(0);
        $pdf->Row(array('DOMICILIO:', $xml->Emisor->DomicilioFiscal[0]['calle'].' No. '.$xml->Emisor->DomicilioFiscal[0]['noExterior'].
                                              ((isset($xml->Emisor->DomicilioFiscal[0]['noInterior'])) ? ' Int. '.$xml->Emisor->DomicilioFiscal[0]['noInterior'] : '') ), false, false, null, 2, 1);
        $pdf->SetWidths(array(19, 83, 19, 83));
        $pdf->SetX(0);
        $pdf->Row(array('COLONIA:', $xml->Emisor->DomicilioFiscal[0]['colonia'], 'LOCALIDAD:', $xml->Emisor->DomicilioFiscal[0]['localidad']), false, false, null, 2, 1);
        $pdf->SetWidths(array(19, 65, 11, 65, 11, 40));
        $pdf->SetX(0);
        $pdf->Row(array('ESTADO:', $xml->Emisor->DomicilioFiscal[0]['estado'], 'PAIS:', $xml->Emisor->DomicilioFiscal[0]['pais'], 'CP:', $xml->Emisor->DomicilioFiscal[0]['codigoPostal']), false, false, null, 2, 1);

        $end_y = $pdf->GetY();

        /////////////////////////////////////
        // Folio Fisca, CSD, Lugar y Fecha //
        /////////////////////////////////////

        $pdf->SetFont('helvetica','B', 9);
        $pdf->SetFillColor(140,140, 140);
        $pdf->SetTextColor(255,255,255);
        $pdf->SetXY(109, 0);
        $pdf->Cell(108, 4, "Folio Fiscal:", 0, 0, 'R', 1);

        $pdf->SetXY(109, 0);
        $pdf->Cell(50, 4, (!isset($factura['id_nc']) ? 'Factura' : 'Nota de Crédito').': '.($factura['serie'].$factura['folio']) , 0, 0, 'L', 1);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetXY(109, 6);
        $pdf->Cell(108, 4, $xml->Complemento->TimbreFiscalDigital[0]['UUID'], 0, 0, 'C', 0);

        $pdf->SetTextColor(255,255,255);
        $pdf->SetXY(109, $pdf->GetY() + 4);
        $pdf->Cell(108, 4, "No de Serie del Certificado del CSD:", 0, 0, 'R', 1);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetXY(109, $pdf->GetY() + 4);
        $pdf->Cell(108, 4, $xml[0]['noCertificado'], 0, 0, 'C', 0);

        $pdf->SetFillColor(140,140, 140);
        $pdf->SetTextColor(255,255,255);
        $pdf->SetXY(109, $pdf->GetY() + 4);
        $pdf->Cell(108, 4, "Lugar. fecha y hora de emisión:", 0, 0, 'R', 1);

        $pdf->SetFont('helvetica','', 9);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetXY(109, $pdf->GetY() + 4);

        $pais   = strtoupper($xml->Emisor->DomicilioFiscal[0]['pais']);
        $estado = strtoupper($xml->Emisor->DomicilioFiscal[0]['estado']);
        $fecha = $xml[0]['fecha'];

        $pdf->Cell(108, 4, "{$pais} {$estado} {$fecha}", 0, 0, 'R', 0);

        $pdf->SetFont('helvetica','b', 9);
        $pdf->SetFillColor(140,140, 140);
        $pdf->SetTextColor(255,255,255);
        $pdf->SetXY(109, $pdf->GetY() + 4);
        $pdf->Cell(108, 4, "Régimen Fiscal:", 0, 0, 'R', 1);

        $pdf->SetFont('helvetica','', 9);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetXY(109, $pdf->GetY() + 4);
        $pdf->MultiCell(108, 4, $xml->Emisor->RegimenFiscal[0]['Regimen'], 0, 'C', 0);

        //////////////////
        // domicilioEmisor //
        //////////////////

        // $domicilioEmisor = '';
        // $domicilioEmisor .= (isset($xml->Emisor->DomicilioFiscal[0]['calle'])) ? $xml->Emisor->DomicilioFiscal[0]['calle'] : '';
        // $domicilioEmisor .= (isset($xml->Emisor->DomicilioFiscal[0]['noExterior'])) ? ' #'.$xml->Emisor->DomicilioFiscal[0]['noExterior'] : '';
        // $domicilioEmisor .= (isset($xml->Emisor->DomicilioFiscal[0]['noInterior'])) ? ' Int. '.$xml->Emisor->DomicilioFiscal[0]['noInterior'] : '';
        // $domicilioEmisor .= (isset($xml->Emisor->DomicilioFiscal[0]['colonia'])) ? ', '.$xml->Emisor->DomicilioFiscal[0]['colonia'] : '';
        // $domicilioEmisor .= (isset($xml->Emisor->DomicilioFiscal[0]['localidad'])) ? ', '.$xml->Emisor->DomicilioFiscal[0]['localidad'] : '';
        // $domicilioEmisor .= (isset($xml->Emisor->DomicilioFiscal[0]['municipio'])) ? ', '.$xml->Emisor->DomicilioFiscal[0]['municipio'] : '';
        // $domicilioEmisor .= (isset($xml->Emisor->DomicilioFiscal[0]['estado'])) ? ', '.$xml->Emisor->DomicilioFiscal[0]['estado'] : '';

        // $pdf->SetFont('helvetica','B', 9);
        // $pdf->SetFillColor(140,140, 140);
        // $pdf->SetTextColor(255,255,255);
        // $pdf->SetXY(0, $pdf->GetY() + 4);
        // $pdf->Cell(216, 4, "Domicilio:", 0, 0, 'L', 1);

        // $pdf->SetFont('helvetica','', 9);
        // $pdf->SetTextColor(0, 0, 0);
        // $pdf->SetXY(0, $pdf->GetY() + 4);
        // $pdf->Cell(216, 4, $domicilioEmisor, 0, 0, 'C', 0);

        //////////////////
        // Datos Receptor //
        //////////////////
        $pdf->setY($end_y);
        $domicilioReceptor = '';
        $domicilioReceptor .= (isset($xml->Receptor->Domicilio[0]['calle']) ? $xml->Receptor->Domicilio[0]['calle'] : '');
        $domicilioReceptor .= (isset($xml->Receptor->Domicilio[0]['noExterior']) ? ' #'.$xml->Receptor->Domicilio[0]['noExterior'] : '');
        $domicilioReceptor .= (isset($xml->Receptor->Domicilio[0]['noInterior'])) ? ' Int. '.$xml->Receptor->Domicilio[0]['noInterior'] : '';
        $domicilioReceptor .= (isset($xml->Receptor->Domicilio[0]['colonia']) ? ', '.$xml->Receptor->Domicilio[0]['colonia'] : '');
        $domicilioReceptor .= (isset($xml->Receptor->Domicilio[0]['localidad']) ? ', '.$xml->Receptor->Domicilio[0]['localidad'] : '');
        $domicilioReceptor .= (isset($xml->Receptor->Domicilio[0]['municipio'])) ? ', '.$xml->Receptor->Domicilio[0]['municipio'] : '';
        $domicilioReceptor .= (isset($xml->Receptor->Domicilio[0]['estado']) ? ', '.$xml->Receptor->Domicilio[0]['estado'] : '');

        $pdf->SetFillColor(214, 214, 214);
        $pdf->SetXY(0, $pdf->GetY() + 4);
        $pdf->Cell(216, 1, "", 0, 0, 'L', 1);

        $pdf->SetFont('helvetica','B', 9);
        $pdf->SetFillColor(140,140, 140);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetXY(0, $pdf->GetY() + 1);
        $pdf->Cell(216, 4, "Receptor:", 0, 0, 'L', 1);

        $pdf->SetFont('helvetica','', 8);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetXY(0, $pdf->GetY() + 4);


        $pdf->SetX(0);
        $pdf->SetAligns(array('L', 'L'));
        $pdf->SetWidths(array(19, 93));
        $pdf->Row(array('RFC:', $xml->Receptor[0]['rfc']), false, false, null, 2, 1);
        $pdf->SetWidths(array(19, 196));
        $pdf->SetX(0);
        $pdf->Row(array('NOMBRE:', $xml->Receptor[0]['nombre']), false, false, null, 2, 1);
        $pdf->SetX(0);
        $pdf->Row(array('DOMICILIO:', (isset($xml->Receptor->Domicilio[0]['calle']) ? $xml->Receptor->Domicilio[0]['calle'] : '').
                  ' No. '.(isset($xml->Receptor->Domicilio[0]['noExterior']) ? $xml->Receptor->Domicilio[0]['noExterior'] : '').
                  ((isset($xml->Receptor->Domicilio[0]['noInterior'])) ? ' Int. '.$xml->Receptor->Domicilio[0]['noInterior'] : '') ), false, false, null, 2, 1);
        $pdf->SetWidths(array(19, 83, 19, 83));
        $pdf->SetX(0);
        $pdf->Row(array('COLONIA:', (isset($xml->Receptor->Domicilio[0]['colonia']) ? $xml->Receptor->Domicilio[0]['colonia'] : ''),
                  'LOCALIDAD:', (isset($xml->Receptor->Domicilio[0]['localidad']) ? $xml->Receptor->Domicilio[0]['localidad'] : '')), false, false, null, 2, 1);
        $pdf->SetWidths(array(19, 65, 11, 65, 11, 40));
        $pdf->SetX(0);
        $pdf->Row(array('ESTADO:', (isset($xml->Receptor->Domicilio[0]['estado']) ? $xml->Receptor->Domicilio[0]['estado'] : ''),
                'PAIS:', (isset($xml->Receptor->Domicilio[0]['pais']) ? $xml->Receptor->Domicilio[0]['pais'] : ''),
                'CP:', (isset($xml->Receptor->Domicilio[0]['codigoPostal']) ? $xml->Receptor->Domicilio[0]['codigoPostal'] : '') ), false, false, null, 2, 1);


        // $pdf->Cell(216, 4, "Nombre: {$xml->Receptor[0]['nombre']} RFC: {$xml->Receptor[0]['rfc']}", 0, 0, 'L', 0);

        // $pdf->SetFont('helvetica','', 9);
        // $pdf->SetTextColor(0, 0, 0);
        // $pdf->SetXY(0, $pdf->GetY() + 4);
        // $pdf->Cell(216, 4, "Domicilio: {$domicilioReceptor}", 0, 0, 'L', 0);

        ///////////////
        // Productos //
        ///////////////

        $pdf->SetFillColor(214, 214, 214);
        $pdf->SetXY(0, $pdf->GetY() + 5);
        $pdf->Cell(216, 1, "", 0, 0, 'L', 1);

        $pdf->SetXY(0, $pdf->GetY());
        $aligns = array('C', 'C', 'C', 'C','C');
        $aligns2 = array('C', 'C', 'C', 'R','R');
        $widths = array(30, 35, 91, 30, 30);
        $header = array('Cantidad', 'Unidad de Medida', 'Descripcion', 'Precio Unitario', 'Importe');

        $conceptos = current($xml->Conceptos);
        if(count($conceptos) == 0)
          $conceptos = array($conceptos);
        elseif(count($conceptos) == 1){
          $conceptos = current($conceptos);
          $conceptos = array($conceptos);
        }

        // for ($i=0; $i < 30; $i++)
        //   $conceptos[] = $conceptos[$i];

        // echo "<pre>";
        //   var_dump($conceptos, is_array($conceptos));
        // echo "</pre>";exit;

        if (! is_array($conceptos))
          $conceptos = array($conceptos);

        $pdf->limiteY = 250;

        $pdf->setY($pdf->GetY() + 1);
        foreach($conceptos as $key => $item)
        {
          $band_head = false;

          if($pdf->GetY() >= $pdf->limiteY || $key === 0) //salta de pagina si exede el max
          {
            if($key > 0) $pdf->AddPage();

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFillColor(140,140, 140);
            $pdf->SetX(0);
            $pdf->SetAligns($aligns);
            $pdf->SetWidths($widths);
            $pdf->Row($header, true, true, null, 2, 1);
          }

          $pdf->SetFont('Arial', '', 8);
          $pdf->SetTextColor(0,0,0);

          $pdf->SetX(0);
          $pdf->SetAligns($aligns2);
          $pdf->SetWidths($widths);
          $pdf->Row(array(
            $item[0]['cantidad'],
            $item[0]['unidad'],
            $item[0]['descripcion'],
            String::formatoNumero($item[0]['valorUnitario'], 2, '$', false),
            String::formatoNumero($item[0]['importe'], 2, '$', false),
          ), false, true, null, 2, 1);
        }

        /////////////
        // Totales //
        /////////////

        if($pdf->GetY() + 30 >= $pdf->limiteY) //salta de pagina si exede el max
            $pdf->AddPage();

        // Traslados | IVA
        $ivas = current($xml->Impuestos->Traslados);
        if(count($ivas) == 1)
          $ivas = current($ivas);

        if ( ! is_array($ivas))
        {
          $ivas = array($ivas);
        }

        $traslado11 = 0;
        $traslado16 = 0;
        foreach ($ivas as $key => $iva)
        {
          if ($iva[0]['tasa'] == '11')
            $traslado11 = $iva[0]['importe'];
          elseif ($iva[0]['tasa'] == '16')
            $traslado16 = $iva[0]['importe'];
        }

        $pdf->SetFillColor(214, 214, 214);
        $pdf->SetXY(0, $pdf->GetY());
        $pdf->Cell(216, 1, "", 0, 0, 'L', 1);

        $h = 25 - ($traslado11 == 0 ? 5 : 0);
        $h = $h - ($xml->Impuestos->Retenciones->Retencion[0]['importe'] == 0 ? 5 : 0);

        $pdf->SetFillColor(140,140, 140);
        $pdf->SetXY(0, $pdf->GetY() + 1);
        $pdf->Cell(156, $h, "", 1, 0, 'L', 1);

        $pdf->SetFont('helvetica','B', 9);
        $pdf->SetXY(1, $pdf->GetY() + 1);
        $pdf->Cell(154, 4, "Total con letra:", 0, 0, 'L', 1);

        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetXY(0, $pdf->GetY() + 4);
        $pdf->MultiCell(156, 6, $factura['total_letra'], 0, 'C', 0);

        $pdf->SetFont('helvetica','B', 10);
        $pdf->SetXY(156, $pdf->GetY() - 11);
        $pdf->Cell(30, 5, "Subtotal", 1, 0, 'C', 1);

        $pdf->SetXY(186, $pdf->GetY());
        $pdf->Cell(30, 5, String::formatoNumero($xml[0]['subTotal'], 2, '$', false), 1, 0, 'R', 1);

        // Pinta traslados, retenciones

        if ($traslado11 != 0)
        {
          $pdf->SetXY(156, $pdf->GetY() + 5);
          $pdf->Cell(30, 5, "IVA(11%)", 1, 0, 'C', 1);

          $pdf->SetXY(186, $pdf->GetY());
          $pdf->Cell(30, 5,String::formatoNumero($traslado11, 2, '$', false), 1, 0, 'R', 1);
        }

        $pdf->SetXY(156, $pdf->GetY() + 5);
        $pdf->Cell(30, 5, "IVA(16%)", 1, 0, 'C', 1);

        $pdf->SetXY(186, $pdf->GetY());
        $pdf->Cell(30, 5,String::formatoNumero($traslado16, 2, '$', false), 1, 0, 'R', 1);

        if ($xml->Impuestos->Retenciones->Retencion[0]['importe'] != 0)
        {
          $pdf->SetXY(156, $pdf->GetY() + 5);
          $pdf->Cell(30, 5, "IVA Retenido", 1, 0, 'C', 1);

          $pdf->SetXY(186, $pdf->GetY());
          $pdf->Cell(30, 5,String::formatoNumero($xml->Impuestos->Retenciones->Retencion[0]['importe'], 2, '$', false), 1, 0, 'R', 1);
        }

        $pdf->SetXY(156, $pdf->GetY() + 5);
        $pdf->Cell(30, 5, "TOTAL", 1, 0, 'C', 1);

        $pdf->SetXY(186, $pdf->GetY());
        $pdf->Cell(30, 5,String::formatoNumero($xml[0]['total'], 2, '$', false), 1, 0, 'R', 1);

        $pdf->SetFont('helvetica','B', 9);
        $pdf->SetXY(0, $pdf->GetY()+4);
        // $pdf->Cell(78, 4, $xml[0]['formaDePago'], 0, 0, 'L', 1);
        $pdf->MultiCell(156, 4, $xml[0]['formaDePago'], 0, 'L', 1);

        $pdf->SetFont('helvetica','B', 9);
        $pdf->SetXY(0, $pdf->GetY());
        // $pdf->Cell(78, 4, "Metodo de Pago: ".String::getMetodoPago($xml[0]['metodoDePago']), 0, 0, 'L', 1);
        $pdf->MultiCell(156, 4, "Metodo de Pago: ".String::getMetodoPago($xml[0]['metodoDePago'])." | ".$xml[0]['NumCtaPago'], 0, 'L', 1);

        ///////////////////
        // Observaciones //
        ///////////////////

        $pdf->SetXY(0, $pdf->GetY() + 5);

        $width = (($pdf->GetStringWidth($factura['observaciones']) / 216) * 8) + 9;

        if($pdf->GetY() + $width >= $pdf->limiteY) //salta de pagina si exede el max
            $pdf->AddPage();

        if ( ! empty($factura['observaciones']))
        {
            $pdf->SetX(0);
            $pdf->SetFont('helvetica','B', 10);
            $pdf->SetAligns(array('L'));
            $pdf->SetWidths(array(216));
            $pdf->Row(array('Observaciones'), true);

            $pdf->SetFont('helvetica','', 9);
            $pdf->SetXY(0, $pdf->GetY());
            $pdf->SetAligns(array('L'));
            $pdf->SetWidths(array(216));
            $pdf->Row(array($factura['observaciones']), true, 1);
        }

        ////////////////////
        // Timbrado Datos //
        ////////////////////

        if($pdf->GetY() + 25 >= $pdf->limiteY) //salta de pagina si exede el max
            $pdf->AddPage();

        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetXY(10, $pdf->GetY() - 1);
        $pdf->SetAligns(array('L'));
        $pdf->SetWidths(array(196));
        $pdf->Row(array('Sello Digital del CFDI:'), false, 0);

        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetY($pdf->GetY() - 3);
        $pdf->SetAligns(array('L'));
        $pdf->SetWidths(array(196));
        $pdf->Row(array($xml->Complemento->TimbreFiscalDigital[0]['selloCFD']), false, 0);

        if($pdf->GetY() + 25 >= $pdf->limiteY) //salta de pagina si exede el max
            $pdf->AddPage();

        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetXY(10, $pdf->GetY() - 2);
        $pdf->SetAligns(array('L'));
        $pdf->SetWidths(array(196));
        $pdf->Row(array('Sello Digital del SAT:'), false, 0);

        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetY($pdf->GetY() - 3);
        $pdf->SetAligns(array('L'));
        $pdf->SetWidths(array(196));
        $pdf->Row(array($xml->Complemento->TimbreFiscalDigital[0]['selloSAT']), false, 0);

        /////////////
        // QR CODE //
        /////////////

        // formato
        // ?re=XAXX010101000&rr=XAXX010101000&tt=1234567890.123456&id=ad662d33-6934-459c-a128-BDf0393f0f44
        // 0000001213.520000

        $total = explode('.', $xml[0]['total']);

        // Obtiene la diferencia de caracteres en la parte entera.
        $diff = 10 - strlen($total[0]);

        // Agrega los 0 faltantes  a la parte entera.
        for ($i=0; $i < $diff; $i++)
          $total[0] = "0{$total[0]}";

        // Si el total no contiene decimales le asigna en la parte decimal 6 ceros.
        if (count($total) === 1)
        {
          $total[1] = '000000';
        }
        else
        {
          // Obtiene la diferencia de caracteres en la parte decimal.
          $diff = 6 - strlen($total[1]);

          // Agregar los 0 restantes en la parte decimal.
          for ($i=0; $i < $diff; $i++)
            $total[1] = "{$total[1]}0";
        }

        $code = "?re={$xml->Emisor[0]['rfc']}";
        $code .= "&rr={$xml->Receptor[0]['rfc']}";
        $code .= "&tt={$total[0]}.{$total[1]}";
        $code .= "&id={$xml->Complemento->TimbreFiscalDigital[0]['UUID']}";

        // echo "<pre>";
        //   var_dump($code, $total, $diff);
        // echo "</pre>";exit;

        QRcode::png($code, APPPATH.'media/qrtemp.png', 'H', 3);

        if($pdf->GetY() + 50 >= $pdf->limiteY) //salta de pagina si exede el max
            $pdf->AddPage();

        $pdf->SetXY(0, $pdf->GetY());
        $pdf->Image(APPPATH.'media/qrtemp.png', null, null, 40);

        // Elimina el QR generado temporalmente.
        unlink(APPPATH.'media/qrtemp.png');

        ////////////////////
        // Timbrado Datos //
        ////////////////////

        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetXY(45, $pdf->GetY() - 39);
        $pdf->SetAligns(array('L'));
        $pdf->SetWidths(array(160));
        $pdf->Row(array('Cadena Original del complemento de certificación digital del SAT:'), false, 0);

        $pdf->SetFont('helvetica', '', 8);
        $cadenaOriginalSAT = "||{$xml->Complemento->TimbreFiscalDigital[0]['version']}|{$xml->Complemento->TimbreFiscalDigital[0]['UUID']}|{$xml->Complemento->TimbreFiscalDigital[0]['FechaTimbrado']}|{$xml->Complemento->TimbreFiscalDigital[0]['selloCFD']}|{$xml->Complemento->TimbreFiscalDigital[0]['noCertificadoSAT']}||";
        $pdf->SetXY(45, $pdf->GetY() - 3);
        $pdf->Row(array($cadenaOriginalSAT), false, 0);

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor(140,140, 140);
        $pdf->SetTextColor(255,255,255);
        $pdf->SetXY(45, $pdf->GetY() + 1);
        $pdf->Cell(80, 6, "No de Serie del Certificado del SAT:", 0, 0, 'R', 1);

        $pdf->SetXY(125, $pdf->GetY());
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(65, 6, $xml->Complemento->TimbreFiscalDigital[0]['noCertificadoSAT'], 0, 0, 'C', 0);

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor(140,140, 140);
        $pdf->SetTextColor(255,255,255);
        $pdf->SetXY(45, $pdf->GetY() + 10);
        $pdf->Cell(80, 6, "Fecha y hora de certificación:", 0, 0, 'R', 1);

        $pdf->SetXY(125, $pdf->GetY());
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(65, 6, $xml->Complemento->TimbreFiscalDigital[0]['FechaTimbrado'], 0, 0, 'C', 0);


        //------------ IMAGEN CANDELADO --------------------

        if($factura['status'] === 'ca'){
          $pdf->Image(APPPATH.'/images/cancelado.png', 20, 40, 190, 190, "PNG");
        }

        if ($path)
          $pdf->Output($path.'Factura.pdf', 'F');
        else
          $pdf->Output('Factura', 'I');
    }

    public function generaFacturaPdf33($factura, $path = null)
    {
      $this->load->library('cfdi');
      include(APPPATH.'libraries/phpqrcode/qrlib.php');

      // $factura = $this->getDataFactura($idFactura);

      $xml = simplexml_load_string(str_replace(array('cfdi:', 'tfd:'), '', $factura['xml']));

      $factura['cfdi_ext'] = json_decode($factura['cfdi_ext']);

      $this->load->model('catalogos33_model');
      $metodosPago       = new MetodosPago();
      $formaPago         = new FormaPago();
      $usoCfdi           = new UsoCfdi();
      $tipoDeComprobante = new TipoDeComprobante();
      $regimenFiscal     = $this->catalogos33_model->regimenFiscales($factura['cfdi_ext']->emisor->regimenFiscal);

      // echo "<pre>";
      //   var_dump($factura, $xml);
      // echo "</pre>";exit;

      $this->load->library('mypdf');

      // Creación del objeto de la clase heredada
      $pdf = new MYpdf('P', 'mm', 'Letter');

      $pdf->show_head = false;

      $pdf->AliasNbPages();
      $pdf->AddPage();

      //////////
      // Logo //
      //////////

      $pdf->SetXY(0, 0);
      // $pdf->SetXY(30, 2);
      $logo = (file_exists($factura['logo'])) ? $factura['logo'] : 'application/images/logo2.png' ;
      $pdf->Image($logo, 10, null, 0, 21);

      //////////////////////////
      // Rfc y Regimen Fiscal //
      //////////////////////////

      // 0, 171, 72 = verde

      $pdf->SetFont('helvetica','B', 9);
      // $pdf->SetFillColor(214, 214, 214);
      $pdf->SetTextColor(255, 255, 255);

      $pdf->SetFillColor(140,140, 140);
      $pdf->SetTextColor(255,255,255);
      $pdf->SetXY(0, $pdf->GetY() + 4);
      $pdf->Cell(108, 4, "Emisor:", 0, 0, 'L', 1);

      $pdf->SetFont('helvetica','', 8);
      $pdf->SetTextColor(0, 0, 0);
      $pdf->SetXY(0, $pdf->GetY() + 4);

      $pdf->SetX(0);
      $pdf->SetAligns(array('L', 'L'));
      $pdf->SetWidths(array(19, 93));
      $pdf->Row(array('RFC:', $factura['cfdi_ext']->emisor->rfc), false, false, null, 2, 1);
      $pdf->SetWidths(array(19, 196));
      $pdf->SetX(0);
      $pdf->Row(array('NOMBRE:', $factura['cfdi_ext']->emisor->nombreFiscal), false, false, null, 2, 1);
      $pdf->SetX(0);
      $pdf->Row(array('DOMICILIO:', $factura['cfdi_ext']->emisor->calle.' No. '.$factura['cfdi_ext']->emisor->noExterior.
                                            ((isset($factura['cfdi_ext']->emisor->noInterior)) ? ' Int. '.$factura['cfdi_ext']->emisor->noInterior : '') ), false, false, null, 2, 1);
      $pdf->SetWidths(array(19, 83, 19, 83));
      $pdf->SetX(0);
      $pdf->Row(array('COLONIA:', $factura['cfdi_ext']->emisor->colonia, 'LOCALIDAD:', $factura['cfdi_ext']->emisor->localidad), false, false, null, 2, 1);
      $pdf->SetWidths(array(19, 65, 11, 65, 11, 40));
      $pdf->SetX(0);
      $pdf->Row(array('ESTADO:', $factura['cfdi_ext']->emisor->estado, 'PAIS:', $factura['cfdi_ext']->emisor->pais, 'CP:', $factura['cfdi_ext']->emisor->cp), false, false, null, 2, 1);

      $end_y = $pdf->GetY();

      /////////////////////////////////////
      // Folio Fisca, CSD, Lugar y Fecha //
      /////////////////////////////////////

      $pdf->SetFont('helvetica','B', 9);
      $pdf->SetFillColor(140,140, 140);
      $pdf->SetTextColor(255,255,255);
      $pdf->SetXY(109, 0);
      $pdf->Cell(108, 4, "Folio Fiscal:", 0, 0, 'R', 1);

      $pdf->SetXY(109, 0);
      $tipoDeComprobante = " ({$factura['cfdi_ext']->tipoDeComprobante} - {$factura['tipo_comprobante']})";
      $pdf->Cell(50, 4, (!isset($factura['id_nc']) ? 'Factura' : 'Nota de Crédito').$tipoDeComprobante.': '.($factura['serie'].$factura['folio']) , 0, 0, 'L', 1);

      $pdf->SetTextColor(0, 0, 0);
      $pdf->SetXY(109, 6);
      $pdf->Cell(108, 4, $xml->Complemento->TimbreFiscalDigital[0]['UUID'], 0, 0, 'C', 0);

      $pdf->SetTextColor(255,255,255);
      $pdf->SetXY(109, $pdf->GetY() + 4);
      $pdf->Cell(108, 4, "No de Serie del Certificado del CSD:", 0, 0, 'R', 1);

      $pdf->SetTextColor(0, 0, 0);
      $pdf->SetXY(109, $pdf->GetY() + 4);
      $pdf->Cell(108, 4, $factura['cfdi_ext']->noCertificado, 0, 0, 'C', 0);

      $pdf->SetFillColor(140,140, 140);
      $pdf->SetTextColor(255,255,255);
      $pdf->SetXY(109, $pdf->GetY() + 4);
      $pdf->Cell(108, 4, "Lugar. fecha y hora de emisión:", 0, 0, 'R', 1);

      $pdf->SetFont('helvetica','', 9);
      $pdf->SetTextColor(0, 0, 0);
      $pdf->SetXY(109, $pdf->GetY() + 4);

      $pais   = strtoupper($factura['cfdi_ext']->emisor->pais);
      $estado = strtoupper($factura['cfdi_ext']->emisor->estado);
      $fecha = $factura['cfdi_ext']->fecha;

      $pdf->Cell(108, 4, "{$pais} {$estado}, {$factura['cfdi_ext']->emisor->cp} | {$fecha}", 0, 0, 'R', 0);

      $pdf->SetFont('helvetica','b', 9);
      $pdf->SetFillColor(140,140, 140);
      $pdf->SetTextColor(255,255,255);
      $pdf->SetXY(109, $pdf->GetY() + 4);
      $pdf->Cell(108, 4, "Régimen Fiscal:", 0, 0, 'R', 1);

      $pdf->SetFont('helvetica','', 9);
      $pdf->SetTextColor(0, 0, 0);
      $pdf->SetXY(109, $pdf->GetY() + 4);
      $pdf->MultiCell(108, 4, "{$regimenFiscal->c_RegimenFiscal} - {$regimenFiscal->nombre} ", 0, 'R', 0);

      $pdf->SetXY(109, $pdf->GetY() + 1);
      $uso_cfdi = $usoCfdi->search($factura['cfdi_ext']->usoCfdi);
      $pdf->MultiCell(108, 4, "USO: {$uso_cfdi['key']} - {$uso_cfdi['value']} ", 0, 'R', 0);

      //////////////////
      // Datos Receptor //
      //////////////////
      $pdf->setY($end_y);

      $pdf->SetFillColor(214, 214, 214);
      $pdf->SetXY(0, $pdf->GetY() + 4);
      $pdf->Cell(216, 1, "", 0, 0, 'L', 1);

      $pdf->SetFont('helvetica','B', 9);
      $pdf->SetFillColor(140,140, 140);
      $pdf->SetTextColor(255, 255, 255);
      $pdf->SetXY(0, $pdf->GetY() + 1);
      $pdf->Cell(216, 4, "Receptor:", 0, 0, 'L', 1);

      $pdf->SetFont('helvetica','', 8);
      $pdf->SetTextColor(0, 0, 0);
      $pdf->SetXY(0, $pdf->GetY() + 4);


      $pdf->SetX(0);
      $pdf->SetAligns(array('L', 'L'));
      $pdf->SetWidths(array(19, 93));
      $pdf->Row(array('RFC:', $factura['cfdi_ext']->receptor->rfc), false, false, null, 2, 1);
      $pdf->SetWidths(array(19, 196));
      $pdf->SetX(0);
      $pdf->Row(array('NOMBRE:', $factura['cfdi_ext']->receptor->nombreFiscal), false, false, null, 2, 1);
      $pdf->SetX(0);
      $pdf->Row(array('DOMICILIO:', (isset($factura['cfdi_ext']->receptor->calle) ? $factura['cfdi_ext']->receptor->calle : '').
                ' No. '.(isset($factura['cfdi_ext']->receptor->noExterior) ? $factura['cfdi_ext']->receptor->noExterior : '').
                ((isset($factura['cfdi_ext']->receptor->noInterior)) ? ' Int. '.$factura['cfdi_ext']->receptor->noInterior : '') ), false, false, null, 2, 1);
      $pdf->SetWidths(array(19, 83, 19, 83));
      $pdf->SetX(0);
      $pdf->Row(array('COLONIA:', (isset($factura['cfdi_ext']->receptor->colonia) ? $factura['cfdi_ext']->receptor->colonia : ''),
                'LOCALIDAD:', (isset($factura['cfdi_ext']->receptor->localidad) ? $factura['cfdi_ext']->receptor->localidad : '')), false, false, null, 2, 1);
      $pdf->SetWidths(array(19, 65, 11, 65, 11, 40));
      $pdf->SetX(0);
      $pdf->Row(array('ESTADO:', (isset($factura['cfdi_ext']->receptor->estado) ? $factura['cfdi_ext']->receptor->estado : ''),
              'PAIS:', (isset($factura['cfdi_ext']->receptor->pais) ? $factura['cfdi_ext']->receptor->pais : ''),
              'CP:', (isset($factura['cfdi_ext']->receptor->cp) ? $factura['cfdi_ext']->receptor->cp : '') ), false, false, null, 2, 1);


      ///////////////
      // Productos //
      ///////////////

      $pdf->SetFillColor(214, 214, 214);
      $pdf->SetXY(0, $pdf->GetY() + 5);
      $pdf->Cell(216, 1, "", 0, 0, 'L', 1);

      $pdf->SetXY(0, $pdf->GetY());
      $aligns = array('C', 'C', 'C', 'C', 'C', 'C','C');
      $aligns2 = array('C', 'C', 'C', 'C', 'C', 'R','R');
      $widths = array(20, 25, 16, 20, 85, 20, 30);
      $header = array('Cantidad', 'U. Medida', 'C. Unidad', 'C. Servicio', 'Descripcion', 'Precio Unitario', 'Importe');

      $conceptos = $factura['cfdi_ext']->productos;

      $pdf->limiteY = 250;

      $pdf->setY($pdf->GetY() + 1);
      foreach($conceptos as $key => $item)
      {
        $band_head = false;

        if($pdf->GetY() >= $pdf->limiteY || $key === 0) //salta de pagina si exede el max
        {
          if($key > 0) $pdf->AddPage();

          $pdf->SetFont('Arial', 'B', 8);
          $pdf->SetTextColor(0, 0, 0);
          $pdf->SetFillColor(140,140, 140);
          $pdf->SetX(0);
          $pdf->SetAligns($aligns);
          $pdf->SetWidths($widths);
          $pdf->Row($header, true, true, null, 2, 1);
        }

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetTextColor(0,0,0);

        $pdf->SetX(0);
        $pdf->SetAligns($aligns2);
        $pdf->SetWidths($widths);
        $pdf->Row(array(
          $item->cantidad,
          $item->unidad,
          $item->claveUnidad,
          $item->claveProdServ,
          $item->concepto,
          String::formatoNumero($item->valorUnitario, 2, '$', false),
          String::formatoNumero($item->importe, 2, '$', false),
        ), false, true, null, 2, 1);
      }

      /////////////
      // Totales //
      /////////////

      if($pdf->GetY() + 30 >= $pdf->limiteY) //salta de pagina si exede el max
          $pdf->AddPage();

      $pdf->SetFillColor(214, 214, 214);
      $pdf->SetXY(0, $pdf->GetY());
      $pdf->Cell(216, 1, "", 0, 0, 'L', 1);

      $h = 20;
      $h = $h - ((isset($factura['cfdi_ext']->retencionesImporte->isr) && $factura['cfdi_ext']->retencionesImporte->isr > 0) ? 5 : 0);

      $pdf->SetFillColor(140,140, 140);
      $pdf->SetXY(0, $pdf->GetY() + 1);
      $pdf->Cell(156, $h, "", 1, 0, 'L', 1);

      $pdf->SetFont('helvetica','B', 9);
      $pdf->SetXY(1, $pdf->GetY() + 1);
      $pdf->Cell(154, 4, "Total con letra:", 0, 0, 'L', 1);

      $pdf->SetFont('helvetica', '', 10);
      $pdf->SetXY(0, $pdf->GetY() + 4);
      $pdf->MultiCell(156, 6, $factura['total_letra'], 0, 'C', 0);

      $pdf->SetFont('helvetica','B', 10);
      $pdf->SetXY(156, $pdf->GetY() - 11);
      $pdf->Cell(30, 5, "Subtotal", 1, 0, 'C', 1);

      $pdf->SetXY(186, $pdf->GetY());
      $pdf->Cell(30, 5, String::formatoNumero($factura['cfdi_ext']->totalImporte, 2, '$', false), 1, 0, 'R', 1);

      $pdf->SetXY(156, $pdf->GetY() + 5);
      $pdf->Cell(30, 5, "IVA(16%)", 1, 0, 'C', 1);

      $pdf->SetXY(186, $pdf->GetY());
      $pdf->Cell(30, 5,String::formatoNumero($factura['cfdi_ext']->trasladosImporte->iva, 2, '$', false), 1, 0, 'R', 1);

      if (isset($factura['cfdi_ext']->retencionesImporte->isr) && $factura['cfdi_ext']->retencionesImporte->isr > 0)
      {
        $pdf->SetXY(156, $pdf->GetY() + 5);
        $pdf->Cell(30, 5, "IVA Retenido", 1, 0, 'C', 1);

        $pdf->SetXY(186, $pdf->GetY());
        $pdf->Cell(30, 5,String::formatoNumero($factura['cfdi_ext']->retencionesImporte->isr, 2, '$', false), 1, 0, 'R', 1);
      }

      $pdf->SetXY(156, $pdf->GetY() + 5);
      $pdf->Cell(30, 5, "TOTAL", 1, 0, 'C', 1);

      $pdf->SetXY(186, $pdf->GetY());
      $pdf->Cell(30, 5,String::formatoNumero($factura['cfdi_ext']->total, 2, '$', false), 1, 0, 'R', 1);

      $pdf->SetFont('helvetica','B', 9);
      $pdf->SetXY(0, $pdf->GetY()+4);
      $frmPago = $formaPago->search($factura['cfdi_ext']->formaDePago);
      $pdf->MultiCell(156, 4, "Forma de Pago: {$frmPago['key']} - {$frmPago['value']}", 0, 'L', 1);

      $pdf->SetFont('helvetica','B', 9);
      $pdf->SetXY(0, $pdf->GetY());
      $metPago = $metodosPago->search($factura['cfdi_ext']->metodoDePago);
      $pdf->MultiCell(156, 4, "Metodo de Pago: {$metPago['key']} - {$metPago['value']}", 0, 'L', 1);

      ///////////////////
      // Observaciones //
      ///////////////////

      $pdf->SetXY(0, $pdf->GetY() + 5);

      $width = (($pdf->GetStringWidth($factura['observaciones']) / 216) * 8) + 9;

      if($pdf->GetY() + $width >= $pdf->limiteY) //salta de pagina si exede el max
          $pdf->AddPage();

      if ( ! empty($factura['observaciones']))
      {
          $pdf->SetX(0);
          $pdf->SetFont('helvetica','B', 10);
          $pdf->SetAligns(array('L'));
          $pdf->SetWidths(array(216));
          $pdf->Row(array('Observaciones'), true);

          $pdf->SetFont('helvetica','', 9);
          $pdf->SetXY(0, $pdf->GetY());
          $pdf->SetAligns(array('L'));
          $pdf->SetWidths(array(216));
          $pdf->Row(array($factura['observaciones']), true, 1);
      }

      ////////////////////
      // Timbrado Datos //
      ////////////////////

      if($pdf->GetY() + 25 >= $pdf->limiteY) //salta de pagina si exede el max
          $pdf->AddPage();

      $pdf->SetFont('helvetica', 'B', 8);
      $pdf->SetXY(10, $pdf->GetY() - 1);
      $pdf->SetAligns(array('L'));
      $pdf->SetWidths(array(196));
      $pdf->Row(array('Sello Digital del CFDI:'), false, 0);

      $pdf->SetFont('helvetica', '', 8);
      $pdf->SetY($pdf->GetY() - 3);
      $pdf->SetAligns(array('L'));
      $pdf->SetWidths(array(196));
      $pdf->Row(array($xml->Complemento->TimbreFiscalDigital[0]['SelloCFD']), false, 0);

      if($pdf->GetY() + 25 >= $pdf->limiteY) //salta de pagina si exede el max
          $pdf->AddPage();

      $pdf->SetFont('helvetica', 'B', 8);
      $pdf->SetXY(10, $pdf->GetY() - 2);
      $pdf->SetAligns(array('L'));
      $pdf->SetWidths(array(196));
      $pdf->Row(array('Sello Digital del SAT:'), false, 0);

      $pdf->SetFont('helvetica', '', 8);
      $pdf->SetY($pdf->GetY() - 3);
      $pdf->SetAligns(array('L'));
      $pdf->SetWidths(array(196));
      $pdf->Row(array($xml->Complemento->TimbreFiscalDigital[0]['SelloSAT']), false, 0);

      /////////////
      // QR CODE //
      /////////////

      // Genera Qr.
      $cad_sello = substr($factura['sello'], -8);
      $cadenaOriginalSAT = "https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx?&id={$factura['uuid']}&re={$factura['cfdi_ext']->emisor->rfc}&rr={$factura['cfdi_ext']->receptor->rfc}&tt={$factura['cfdi_ext']->total}&fe={$cad_sello}";

      QRcode::png($cadenaOriginalSAT, APPPATH.'media/qrtemp.png', 'H', 3);

      if($pdf->GetY() + 50 >= $pdf->limiteY) //salta de pagina si exede el max
          $pdf->AddPage();

      $pdf->SetXY(0, $pdf->GetY());
      $pdf->Image(APPPATH.'media/qrtemp.png', null, null, 40);

      // Elimina el QR generado temporalmente.
      unlink(APPPATH.'media/qrtemp.png');

      ////////////////////
      // Timbrado Datos //
      ////////////////////

      $pdf->SetFont('helvetica', 'B', 8);
      $pdf->SetXY(45, $pdf->GetY() - 39);
      $pdf->SetAligns(array('L'));
      $pdf->SetWidths(array(160));

      $pdf->SetFont('helvetica', 'B', 10);
      $pdf->SetFillColor(140,140, 140);
      $pdf->SetTextColor(255,255,255);
      $pdf->SetXY(45, $pdf->GetY() + 1);
      $pdf->Cell(80, 6, "No de Serie del Certificado del SAT:", 0, 0, 'R', 1);

      $pdf->SetXY(125, $pdf->GetY());
      $pdf->SetTextColor(0, 0, 0);
      $pdf->Cell(65, 6, $xml->Complemento->TimbreFiscalDigital[0]['NoCertificadoSAT'], 0, 0, 'C', 0);

      $pdf->SetFont('helvetica', 'B', 10);
      $pdf->SetFillColor(140,140, 140);
      $pdf->SetTextColor(255,255,255);
      $pdf->SetXY(45, $pdf->GetY() + 10);
      $pdf->Cell(80, 6, "Fecha y hora de certificación:", 0, 0, 'R', 1);

      $pdf->SetXY(125, $pdf->GetY());
      $pdf->SetTextColor(0, 0, 0);
      $pdf->Cell(65, 6, $xml->Complemento->TimbreFiscalDigital[0]['FechaTimbrado'], 0, 0, 'C', 0);

      $pdf->SetFont('helvetica', 'B', 10);
      $pdf->SetFillColor(140,140, 140);
      $pdf->SetTextColor(255,255,255);
      $pdf->SetXY(45, $pdf->GetY() + 10);
      $pdf->Cell(80, 6, "RFC Prov Certif:", 0, 0, 'R', 1);

      $pdf->SetXY(125, $pdf->GetY());
      $pdf->SetTextColor(0, 0, 0);
      $pdf->Cell(65, 6, $xml->Complemento->TimbreFiscalDigital[0]['RfcProvCertif'], 0, 0, 'C', 0);


      //------------ IMAGEN CANDELADO --------------------

      if($factura['status'] === 'ca'){
        $pdf->Image(APPPATH.'/images/cancelado.png', 20, 40, 190, 190, "PNG");
      }

      $archivo = $factura['serie'].'-'.$this->cfdi->acomodarFolio($factura['folio']);
      if ($path) {
        file_put_contents($path."{$archivo}.xml", $factura['xml']);
        $pdf->Output($path.$archivo.'.pdf', 'F');
      }
      else
        $pdf->Output($archivo, 'I');
    }

  /**
    * Descarga el ZIP con los documentos.
    *
    * @param  string $idFactura
    * @return void
    */
    public function descargarZip($idFactura)
    {
        $this->load->library('cfdi');

        // Obtiene la info de la factura.
        $factura = $this->getDataFactura($idFactura);

        $cliente = strtoupper($factura['nombre']);
        $fecha   = explode('-', $factura['fecha']);
        $ano     = $fecha[0];
        $mes     = strtoupper(String::mes(floatval($fecha[1])));
        $serie   = $factura['serie'] !== '' ? $factura['serie'].'-' : '';
        $folio   = $factura['folio'];

      //echo APPPATH."documentos/CLIENTES/{$cliente}/{$ano}/{$mes}/FACT-{$serie}{$folio}/"; exit;
        $pathDocs = APPPATH."documentos/CLIENTES/{$cliente}/{$ano}/{$mes}/FACT-{$serie}{$folio}/";

        // Scanea el directorio para obtener los archivos.
        $archivos = array_diff(scandir($pathDocs), array('..', '.'));

        $zip = new ZipArchive;
        if ($zip->open(APPPATH.'media/documentos.zip', ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE) === true)
        {
          foreach ($archivos as $archivo)
            $zip->addFile($pathDocs.$archivo, $archivo);

          $zip->close();
        }
        else
        {
          exit('Error al intentar crear el ZIP.');
        }

        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename=documentos.zip');
        readfile(APPPATH.'media/documentos.zip');

        unlink(APPPATH.'media/documentos.zip');
    }

  /**
    * Envia el email al ciente con todos los documentos que tiene asignados.
    *
    * @param  string $idFactura
    * @return void
    */
    public function enviarEmail($idFactura)
    {
        $this->load->library('my_email');

        // Obtiene la informacion de la factura.
        $factura = $this->getDataFactura($idFactura);

        // El cliente necesita tener un email para poderle enviar los documentos.

      $_POST['pextras'] = (isset($_POST['pextras']))? $_POST['pextras'] : '';

        if ( (! is_null($factura['email']) && ! empty($factura['email'])) || $_POST['pextras'] !== '')
        {
          //////////////////
          // Datos Correo //
          //////////////////

            $asunto = "Ha recibido una COMPROBANTE FISCAL DIGITAL de {$factura['nombre_fiscal']}";

            $tipoFactura = !isset($factura['id_nc']) ? 'Factura': 'Nota de Crédito';

            // Si la factura esta timbrada
            if ($factura['status_timbrado'] === "t")
            {
                $altBody = "Estimado Cliente: {$factura['cnombre']}. Usted está recibiendo un comprobante fiscal digital ({$tipoFactura} {$factura['serie']}-{$factura['folio']}) de
                {$factura['nombre_fiscal']}]";
                $body = "
                <p>Estimado Cliente: <strong>{$factura['cnombre']}</strong></p>
                <p>Usted está recibiendo un comprobante fiscal digital ({$tipoFactura} {$factura['serie']}-{$factura['folio']}) de {$factura['nombre_fiscal']}</p>
                ";
            }
            elseif ($factura['status_timbrado'] === "ca")
            {
                $altBody = "HEMOS CANCELADO EL COMPROBANTE FISCAL DIGITAL {$tipoFactura} {$factura['serie']}-{$factura['folio']}, HA QUEDADO SIN EFECTOS FISCALES PARA SU EMPRESA, POR LO QUE PEDIMOS ELIMINARLO Y NO INCLUIRLO EN SU CONTABILIDAD, YA QUE PUEDE REPRESENTAR UN PROBLEMA FISCAL PARA USTED O SU EMPRESA CUANDO EL SAT REALICE UNA FUTURA AUDITORIA EN SU CONTABILIDAD.";
                $body = "
                <p>Estimado Cliente: <strong>{$factura['cnombre']}</strong></p>
                <p>HEMOS CANCELADO EL COMPROBANTE FISCAL DIGITAL {$tipoFactura} {$factura['serie']}-{$factura['folio']}, HA QUEDADO SIN EFECTOS FISCALES PARA SU EMPRESA, POR LO QUE PEDIMOS ELIMINARLO Y NO INCLUIRLO EN SU CONTABILIDAD, YA QUE PUEDE REPRESENTAR UN PROBLEMA FISCAL PARA USTED O SU EMPRESA CUANDO EL SAT REALICE UNA FUTURA AUDITORIA EN SU CONTABILIDAD.</p>
                ";
            }

            /*<p>Si por algun motivo, desea obtener nuevamente su factura puede descargarla directamente de nuestra pagina en la seccion Facturación.<br>
                <a href="http://www.chonitabananas.com/es/facturacion/">www.chonitabananas.com</a></p>*/
            $body .= '
                <p>Si usted desea que llegue el comprobante fiscal a otro correo electronico notifiquelo a: <br>
                  '.$factura['email_empresa'].'</p>

                <br><br>
                <p>De acuerdo a la reglamentación del Servicio de Administración Tributaria (SAT) publicada en el Diario Oficial de la Federación (RMISC 2004) el 31 de mayo del 2004, la factura electrónica es 100% valida y legal.
                  A partir de ahora la entrega del documento fiscal (FACTURA ELECTRONICA) será emitida y entregada por correo electrónico a nuestros socios de negocio.
                  Cabe destacar que la factura electrónica se entregará en formato PDF y archivo XML, el cual podrá imprimir libremente e incluirla en su contabilidad (Articulo 29, Fracción IV de CFF), resguardar la impresión y archivo XML por un periodo de 5 años.
                  Importante: Contenido de la Factura Electrónica
                  En el anexo 20 del Diario Oficial de la Federación, publicado el 1 de septiembre de 2004, en párrafo 2.22.8, se estipula que la impresión de la factura electrónica, que además de los datos fiscales y comerciales, deberá contener la cadena original, el certificado de sello digital, el sello digital y la leyenda: “Este documento es una representación impresa de un CFD”.
                  <br><strong>Sistema de facturacion electrónica - Facturacion "'.$factura['nombre_fiscal'].'"</strong></p>
                ';

            //////////////////////
            // Datos del Emisor //
            //////////////////////

            $correoEmisorEm = "fumigacionesaereasnevarez@gmail.com"; // Correo con el q se emitira el correo.
            $nombreEmisor   = $factura['nombre_fiscal'];
            $correoEmisor   = "postmaster@aerofumigacionesnevarez.com"; // Correo para el auth. Gmail y mailgun (nevarezaerofumiga@gmail.com | N3V4R3zgoz)
            $contrasena     = "52c5991366b2651c05dd4c851f265fdb"; // Contraseña de $correEmisor n3v4r3zr | 4AIjcF83BCUZgfF3FpoLlw (n3v4r3zr4d)

            ////////////////////////
            // Datos del Receptor //
            ////////////////////////

            $correoDestino = array();

            if ($_POST['pextras'] !== '')
              $correoDestino = explode(',', $_POST['pextras']);

            if (isset($_POST['emails']))
            {
              foreach ($_POST['emails'] as $email)
              {
                array_push($correoDestino, $email);
              }
            }
            if($factura['email']!='')
              array_push($correoDestino, $factura['email']);

            $nombreDestino = strtoupper($factura['cnombre']);
            $datosEmail = array(
                'correoEmisorEm' => $correoEmisorEm,
                'correoEmisor'   => $correoEmisor,
                'nombreEmisor'   => $nombreEmisor,
                'contrasena'     => $contrasena,
                'asunto'         => $asunto,
                'altBody'        => $altBody,
                'body'           => $body,
                'correoDestino'  => $correoDestino,
                'nombreDestino'  => $nombreDestino,
                'cc'             => $factura['email_empresa'],
                'adjuntos'       => array()
            );

            // Adjuntos.
            // if ($factura['info']->docs_finalizados === 't' || $factura['info']->id_nc !== null)
            // {
                $this->load->model('documentos_model');
                // $docs = $this->documentos_model->getClienteDocs($factura['info']->id_factura);

                // Si tiene documentos
                // if ($docs)
                // {
                    $cliente = strtoupper($factura['nombre']);
                    $fecha   = explode('-', $factura['fecha']);
                    $ano     = $fecha[0];
                    $mes     = strtoupper(String::mes(floatval($fecha[1])));
                    $serie   = $factura['serie'] !== '' ? $factura['serie'].'-' : '';
                    $folio   = $factura['folio'];

                    $pathDocs = APPPATH."documentos/CLIENTES/{$cliente}/{$ano}/{$mes}/FACT-{$serie}{$folio}/";

                    // echo "<pre>";
                    //   var_dump($pathDocs);
                    // echo "</pre>";exit;

                    // Scanea el directorio para obtener los archivos.
                    $archivos = array_diff(scandir($pathDocs), array('..', '.'));

                    $adjuntos = array();
                    foreach ($archivos as $arch)
                        $adjuntos[$arch] = $pathDocs.$arch;

                    $datosEmail['adjuntos'] = $adjuntos;
                // }
            // }

            // Envia el email.
            $result = $this->my_email->setData($datosEmail)->zip()->send();

            $response = array(
                'passes' => true,
                'msg'    => 10
            );

            if (isset($result['error']))
            {
                $response = array(
                'passes' => false,
                'msg'    => 9
                );
            }
        }
        else
        {
          $response = array(
            'passes' => false,
            'msg'    => 8
          );
        }

        return $response;
    }
}