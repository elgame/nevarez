<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class cfdi{
  public $path_certificado_org = '';
  public $path_certificado     = '';
  public $path_key             = '';
  public $pass_key             = '12345678a';//CHONITA09

	public $version = '3.2';

  private $rfc            = 'NEDR620710H76';
  private $razon_social   = 'ROBERTO NEVAREZ DOMINGUEZ';
  private $regimen_fiscal = 'Actividad empresarial, régimen general de ley'; //'Actividad empresarial y profesional, Régimen de honorarios';
  private $calle          = 'Pista Aérea';
  private $no_exterior    = 'S/N';
  private $no_interior    = '';
  private $colonia        = 'Ranchito';
  private $localidad      = 'Ranchito';
  private $municipio      = 'Michoacán';
  private $estado         = 'Michoacán';
  private $pais           = 'México';
  private $cp             = '60800';

	public $default_id_empresa = 3; //informacion fiscal guardada en la bd

	/**
   * Inicializa las rutas del certificado, certificado.pem y key.pem
   *
   * @return void
   */
  public function __construct()
  {
		/*$this->path_certificado_org = APPPATH.'CFDI/certificados/aad990814bp7_1210261233s.cer';
		$this->path_certificado = APPPATH.'CFDI/certificados/aad990814bp7_1210261233s.cer.pem';
		$this->path_key = APPPATH.'CFDI/certificados/aad990814bp7_1210261233s.key.pem';*/
	}

  /**
   * Obtiene el numero de certificado de la empresa.
   *
   * @param  string $path_certificado_org
   * @return string
   */
	public function obtenNoCertificado($path_certificado_org = null)
  {
    $path_certificado_org = $path_certificado_org==null? $this->path_certificado_org: $path_certificado_org;
    $datos_cer            = file_get_contents($path_certificado_org);
    $num_certificado      = substr($datos_cer, 15, 20);

		return $num_certificado;
	}

  /**
   * Obtiene la fecha del certificado.
   *
   * @param  string $path_certificado_org
   * @return string
   */
	public function obtenFechaCertificado($path_certificado_org=null)
  {
    $path_certificado_org = $path_certificado_org==null? $this->path_certificado_org: $path_certificado_org;
    $datos_cer            = file_get_contents($path_certificado_org);
    $fecha_certificado    = substr($datos_cer, (strpos($datos_cer, "Z")+3), 6);
    $fecha_certificado    = '20'.substr($fecha_certificado, 0, 2).'-'.substr($fecha_certificado, 2, 2).'-'.substr($fecha_certificado, 4, 2);

    return $fecha_certificado;
	}

  /**
   * Obtiene el Sello.
   *
   * @param  string $cadena_original
   * @return string
   */
	public function obtenSello($cadena_original)
  {
    $pkeyid = openssl_pkey_get_private(file_get_contents($this->path_key), $this->pass_key);
	openssl_sign($cadena_original, $crypttext, $pkeyid, OPENSSL_ALGO_SHA1);
		openssl_free_key($pkeyid);

		$sello = base64_encode($crypttext);

		return $sello;
	}

  /**
   * Obtiene el .key.pem y lo pasa a base64.
   *
   * @return string
   */
  public function obtenKey()
  {
    $text = file_get_contents($this->path_key);
    $data = base64_encode($text);
    return $data;
  }

  /**
   * Obtiene el contenido del .cer.pem y lo pasa a base64.
   *
   * @return string
   */
  public function obtenCer()
  {
    $text = file_get_contents($this->path_certificado);
    $data = base64_encode($text);
    return $data;
  }

  /**
   * Lee el contenido del certificado .pem y obtiene el contenido que se encuentra
   * entre los lineas -----BEGIN CERTIFICATE----- y -----END CERTIFICATE-----
   *
   * @param  string $path
   * @return string
   */
  public function obtenCertificado($path, $one_line=true)
  {
    // Lee el contenido del .cer.pem
    $datacer = file_get_contents($path);
    openssl_x509_export($datacer, $content);

    if($one_line){
      $cerpem = explode('-----BEGIN CERTIFICATE-----', $content);
      $cerpem = explode('-----END CERTIFICATE-----', $cerpem[1]);

      // Retorna la cadena del certificado sin espacios.
      return str_replace("\n", "", $cerpem[0]);
    }
    return $content;
  }

  /**
   * Lee el contenido de la llave key.pem y obtiene el contenido
   *
   * @param  string $path
   * @return string
   */
  public function obtenLlave($path)
  {
    // Lee el contenido del .key.pem
    $datacer = file_get_contents($path);

    return $datacer;
  }

  /**
   * Genera la Cadena Original.
   *
   * @param  array $data
   * @return array
   */
  public function obtenCadenaOriginal($data)
  {
    // Obtiene el ID de la empresa que emite la factura, si no llega
    // entonces obtiene el ID por default.
    // $id_empresa = isset($data['id_empresa']) ? $data['id_empresa'] : $this->default_id_empresa;
    $id = isset($data['id']) ? $data['id'] : $this->default_id_empresa;

    // Carga los datos de la empresa que emite la factura.
    $this->cargaDatosFiscales($id, $data['table']);

    // $cadenaOriginal = '||';

    // Array que contiene la secuencia de informacion respetando el orden expresado
    // en el anexo 20.
    $datos = array();

    // ----------> Nodo comprobante
    $datos['comprobante']['version']              = $data['version'];
    // $datos['comprobante']['serie']                = $data['serie'];
    // $datos['comprobante']['folio']                = $data['folio'];
    $datos['comprobante']['fecha']                = $data['fecha'];
    // $datos['comprobante']['noAprobacion']         = $data['noAprobacion'];
    // $datos['comprobante']['anoAprobacion']        = $data['anoAprobacion'];
    $datos['comprobante']['tipoDeComprobante']    = $data['tipoDeComprobante'];
    // $datos['comprobante']['tipoDeComprobante']    = 'egreso';
    $datos['comprobante']['formaDePago']          = $data['formaDePago'];
    $datos['comprobante']['condicionesDePago']    = $data['condicionesDePago'];
    $datos['comprobante']['subTotal']             = (float)$data['subTotal'];

    // Nomina
    // $datos['comprobante']['descuento']            = '16.67';

    // $datos['comprobante']['TipoCambio']           = $data['TipoCambio'];
    // $datos['comprobante']['Moneda']               = $data['Moneda'];
    $datos['comprobante']['total']                = (float)$data['total'];
    $datos['comprobante']['metodoDePago']         = $data['metodoDePago'];
    $datos['comprobante']['LugarExpedición']      = $this->municipio.', '.$this->estado;
    $datos['comprobante']['NumCtaPago']           = $data['NumCtaPago'];

    // $datos['comprobante']['FolioFiscalOrig']      = $data['FolioFiscalOrig'];
    // $datos['comprobante']['SerieFolioFiscalOrig'] = $data['SerieFolioFiscalOrig'];
    // $datos['comprobante']['FechaFolioFiscalOrig'] = $data['FechaFolioFiscalOrig'];
    // $datos['comprobante']['MontoFolioFiscalOrig'] = $data['MontoFolioFiscalOrig'];

    // ----------> Nodo emisor
    $datos['emisor']['rfc']    = $this->rfc;
    $datos['emisor']['nombre'] = $this->nombre_fiscal;

    // ----------> Nodo domicilioFiscal

    if ($this->calle !== null && $this->calle !== '')
      $datos['domicilioFiscal']['calle'] = $this->calle;

    if ($this->no_exterior !== null && $this->no_exterior !== '')
      $datos['domicilioFiscal']['noExterior'] = $this->no_exterior;

    if ($this->no_interior !== null && $this->no_interior !== '')
      $datos['domicilioFiscal']['noInterior'] = $this->no_interior;

    if ($this->colonia !== null && $this->colonia !== '')
      $datos['domicilioFiscal']['colonia'] = $this->colonia;

    if ($this->localidad !== null && $this->localidad !== '')
      $datos['domicilioFiscal']['localidad'] = $this->localidad;

    // $datos['domicilioFiscal']['referencia']

    if ($this->municipio !== null && $this->municipio !== '')
      $datos['domicilioFiscal']['municipio'] = $this->municipio;

    if ($this->estado !== null && $this->estado !== '')
      $datos['domicilioFiscal']['estado'] = $this->estado;

    if ($this->pais !== null && $this->pais !== '')
      $datos['domicilioFiscal']['pais'] = $this->pais;

    if ($this->cp !== null && $this->cp !== '')
      $datos['domicilioFiscal']['codigoPostal'] = $this->cp;

    // ----------> Nodo expedidoEn

    if ($this->calle !== null && $this->calle !== '')
      $datos['expedidoEn']['calle'] = $this->calle;

    if ($this->no_exterior !== null && $this->no_exterior !== '')
      $datos['expedidoEn']['noExterior'] = $this->no_exterior;

    if ($this->no_interior !== null && $this->no_interior !== '')
      $datos['expedidoEn']['noInterior'] = $this->no_interior;

    if ($this->colonia !== null && $this->colonia !== '')
      $datos['expedidoEn']['colonia'] = $this->colonia;

    if ($this->localidad !== null && $this->localidad !== '')
      $datos['expedidoEn']['localidad'] = $this->localidad;

    // ----------> $datos['expedidoEn']['referencia']

    if ($this->municipio !== null && $this->municipio !== '')
      $datos['expedidoEn']['municipio'] = $this->municipio;

    if ($this->estado !== null && $this->estado !== '')
      $datos['expedidoEn']['estado'] = $this->estado;

    if ($this->pais !== null && $this->pais !== '')
      $datos['expedidoEn']['pais'] = $this->pais;

    if ($this->cp !== null && $this->cp !== '')
      $datos['expedidoEn']['codigoPostal'] = $this->cp;

    // ----------> Nodo regimenFiscal
    $datos['regimenFiscal']['regimen'] = $this->regimen_fiscal;

    // ----------> Nodo receptor
    $datos['receptor']['rfc']    = $data['rfc'];
    $datos['receptor']['nombre'] = $data['nombre'];

    // ----------> Nodo domicilio

    if ($data['calle'] !== null && $data['calle'] !== '')
      $datos['domicilio']['calle'] = $data['calle'];

    if ($data['noExterior'] !== null && $data['noExterior'] !== '')
      $datos['domicilio']['noExterior'] = $data['noExterior'];

    if ($data['noInterior'] !== null && $data['noInterior'] !== '')
      $datos['domicilio']['noInterior'] = $data['noInterior'];

    if ($data['colonia'] !== null && $data['colonia'] !== '')
      $datos['domicilio']['colonia'] = $data['colonia'];

    if ($data['localidad'] !== null && $data['localidad'] !== '')
      $datos['domicilio']['localidad'] = $data['localidad'];

    // $datos['domicilio']['referencia']   = $data['referencia'];

    if ($data['municipio'] !== null && $data['municipio'] !== '')
      $datos['domicilio']['municipio'] = $data['municipio'];

    if ($data['estado'] !== null && $data['estado'] !== '')
      $datos['domicilio']['estado'] = $data['estado'];

    if ($data['pais'] !== null && $data['pais'] !== '')
      $datos['domicilio']['pais'] = $data['pais'];

    if ($data['codigoPostal'] !== null && $data['codigoPostal'] !== '')
      $datos['domicilio']['codigoPostal'] = $data['codigoPostal'];

    // ----------> Nodo concepto
    // cantidad
    // unidad
    // noIdentificacion
    // descripcion
    // valorUnitario
    // importe
    // cuentaPredial - numero

    $datos['concepto'] = array();
    foreach ($data['concepto'] as $key => $producto)
    {
      if ($data['sinCosto'])
      {
        if ($producto['idClasificacion'] != '49' AND $producto['idClasificacion'] != '50' AND
            $producto['idClasificacion'] != '51' AND $producto['idClasificacion'] != '52' AND
            $producto['idClasificacion'] != '53')
        {
          $datos['concepto'][] = (float)$producto['cantidad'];
          $datos['concepto'][] = $producto['unidad'];
          $datos['concepto'][] = $producto['descripcion'];
          $datos['concepto'][] = (float)$producto['valorUnitario'];
          $datos['concepto'][] = (float)$producto['importe'];
        }
      }
      else
      {
        $datos['concepto'][] = (float)$producto['cantidad'];
        $datos['concepto'][] = $producto['unidad'];
        $datos['concepto'][] = $producto['descripcion'];
        $datos['concepto'][] = (float)$producto['valorUnitario'];
        $datos['concepto'][] = (float)$producto['importe'];
      }
    }

    // ----------> Nodo retencion
    // impuesto
    // importe
    // totalImpuestosRetenidos
    $datos['retencion'] = array();
    foreach ($data['retencion'] as $key => $retencion)
    {
      $datos['retencion'][] = $retencion['impuesto'];
      $datos['retencion'][] = (float)$retencion['importe'];
    }
    $datos['retencion'][] = (float)$data['totalImpuestosRetenidos'];

    // ----------> Nodo traslado
    // Impuesto
    // tasa
    // importe
    // totalImpuestosTrasladados
    $datos['traslado'] = array();
    foreach ($data['traslado'] as $key => $traslado)
    {
      $datos['traslado'][] = $traslado['Impuesto'];
      $datos['traslado'][] = $traslado['tasa'];
      $datos['traslado'][] = (float)$traslado['importe'];
    }
    $datos['traslado'][] = (float)$data['totalImpuestosTrasladados'];

    // ----------> Nodo Nomina Si es una nomina la que se facturara.
    // $datos['nomina'] = $this->nodoNomina();
    // echo "<pre>";
    //   var_dump($datos['nomina']);
    // echo "</pre>";exit;

    $mergeDatos = array_merge(
      array_values($datos['comprobante']),
      array_values($datos['emisor']),
      array_values($datos['domicilioFiscal']),
      array_values($datos['expedidoEn']),
      array_values($datos['regimenFiscal']),
      array_values($datos['receptor']),
      array_values($datos['domicilio']),
      array_values($datos['concepto']),
      array_values($datos['retencion']),
      array_values($datos['traslado'])
      // ,array_values($datos['nomina'])
    );

    // echo "<pre>";
    //   var_dump(ltrim(rtrim(preg_replace('/\s+/', ' ', '||'.implode('|', $mergeDatos).'||'))));
    // echo "</pre>";exit;

    return array(
      'cadenaOriginal' => ltrim(rtrim(preg_replace('/\s+/', ' ', '||'.implode('|', $mergeDatos).'||'))),
      'datos' => $datos
    );
  }

  public function nodoNomina()
  {
    $nominaDatos = array(
      'Version'                => '1.1',
      // 'RegistroPatronal'       => '', // opcional
      'NumEmpleado'            => '0001',
      'CURP'                   => 'OOAG791212MCMCPD03',
      'TipoRegimen'            => '1',
      // 'NumSeguridadSocial'     => '123456789', // opcional
      'FechaPago'              => '2013-12-15',
      'FechaInicialPago'       => '2013-12-01',
      'FechaFinalPago'         => '2013-12-15',
      'NumDiasPagados'         => '15',
      'Departamento'           => 'Sistemas',
      // 'CLABE'                  => '', // opcional
      // 'Banco'                  => '', // opcional
      'FechaInicioRelLaboral'  => '2013-04-22', // opcional
      'Antiguedad'             => '30', // opcional
      'Puesto'                 => 'Desarrollador de Software', // opcional
      'TipoContrato'           => 'Base', // opcional
      'TipoJornada'            => 'continuada', // opcional
      'PeriodicidadPago'       => 'quincenal',
      // 'SalarioBaseCotApor'     => '', // opcional
      // 'RiesgoPuesto'           => '', // opcional
      // 'SalarioDiarioIntegrado' => '', // opcional
    );

    $nominaPercepciones = array(
      '1029.15', // TotalGravado
      '0', // TotalExento
    );

    $percepciones = array();
    $percepciones[] = array(
      'TipoPercepcion' => '001',
      'Clave'          => 'AAA',
      'Concepto'       => 'Sueldos, Salarios Rayas y Jornales',
      'ImporteGravado' => '1029.15',
      'ImporteExento'  => '0',
    );

    foreach ($percepciones as $key => $percepcion)
    {
      $nominaPercepciones = array_merge($nominaPercepciones, array_values($percepcion));
    }

    $nominaDeducciones = array(
      '16.67', // TotalGravado
      '0', // TotalExento
    );

    $deducciones = array();
    $deducciones[] = array(
      'TipoDeduccion' => '001',
      'Clave'          => 'BBB',
      'Concepto'       => 'Seguridad social',
      'ImporteGravado' => '16.67',
      'ImporteExento'  => '0',
    );

    $deducciones[] = array(
      'TipoDeduccion' => '002',
      'Clave'          => 'CCC',
      'Concepto'       => 'ISR',
      'ImporteGravado' => '0',
      'ImporteExento'  => '0',
    );

    foreach ($deducciones as $key => $deduccion)
    {
      $nominaDeducciones = array_merge($nominaDeducciones, array_values($deduccion));
    }

    // echo "<pre>";
    //   var_dump(array_merge(array_values($nominaDatos), array_values($nominaPercepciones),  array_values($nominaDeducciones)));
    // echo "</pre>";exit;

    return array_merge(
      array_values($nominaDatos),
      array_values($nominaPercepciones),
      array_values($nominaDeducciones)
    );

    // 'DiasIncapacidad' => '0',
    // 'TipoIncapacidad' => '0',
    // 'Descuento'       => '0',

    // 'Dias'          => '0',
    // 'TipoHoras'     => '0',
    // 'HorasExtra'    => '0',
    // 'ImportePagado' => '0',
  }


  public function obtenDatosCfdi33($data, $productosApi, $id_nc = false)
  {
    $CI =& get_instance();

    // Obtiene el ID de la empresa que emite la factura, si no llega
    // entonces obtiene el ID por default.
    // $id_empresa = isset($data['id_empresa']) ? $data['id_empresa'] : $this->default_id_empresa;
    $id = isset($data['didempresa']) ? $data['didempresa'] : $this->default_id_empresa;

    // Carga los datos de la empresa que emite la factura.
    $this->cargaDatosFiscales($id, 'empresas');

    // Obtiene los datos del receptor.
    $CI->load->model('clientes_model');
    $cliente = $CI->clientes_model->getInfoCliente($_POST['hcliente'], true);

    if ($id_nc) {
      // Obtiene los datos de la factura.
      $CI->load->model('facturacion_model');
      $factura = $CI->facturacion_model->getInfoFactura($id_nc, true);

      $cfdiRel = array(
        'tipoRelacion' => '01',
        'cfdiRelacionado' => array(
          array(
            'uuid' => $factura['info']->uuid,
          )
        ),
      );
    }

    // $CI->load->model('catalogos33_model');
    // $this->regimen_fiscal = $CI->catalogos33_model->regimenFiscales($this->regimen_fiscal);

    $tipoComprobante = 'I';
    if ($data['dtipo_comprobante'] == 'ingreso')
      $tipoComprobante = 'I';
    elseif ($data['dtipo_comprobante'] == 'egreso')
      $tipoComprobante = 'E';
    elseif ($data['dtipo_comprobante'] == 'traslado')
      $tipoComprobante = 'T';
    elseif ($data['dtipo_comprobante'] == 'nomina')
      $tipoComprobante = 'N';

    $productos = [];
    foreach ($productosApi as $key => $value) {
      $productos[] = array(
        'claveProdServ'           => $value['claveProducto']['key'],
        'claveProdServValue'      => $value['claveProducto']['value'],
        'claveUnidad'             => $value['claveUnidad']['key'],
        'claveUnidadValue'        => $value['claveUnidad']['value'],
        'unidad'                  => $value['unidad'],
        'cantidad'                => $value['cantidad'],
        'concepto'                => $value['descripcion'],
        'cuentaPredial'           => '',
        'descuentoProd'           => '0',
        'descuentoProdPorcent'    => '0',
        'importe'                 => $value['importe'],
        'noIdentificacion'        => '',
        'retencionCedular'        => '0',
        'retencionCedularPorcent' => '0',
        'retencionIsr'            => '0',
        'retencionIsrPorcent'     => '0',
        'retencionIva'            => '0',
        'retencionIvaPorcent'     => '0',
        'retencionIvc'            => '0',
        'retencionIvcPorcent'     => '0',
        'trasladoCedular'         => '0',
        'trasladoCedularPorcent'  => '0',
        'trasladoIeps'            => '0',
        'trasladoIepsPorcent'     => '0',
        'trasladoIsh'             => '0',
        'trasladoIshPorcent'      => '0',
        'trasladoIva'             => $value['importeIva'],
        'trasladoIvaPorcent'      => $value['tazaIva'],
        'valorUnitario'           => $value['valorUnitario'],
      );
    }

    $datosApi = array(
      'emisor' => array(
        'nombreFiscal'  => $this->nombre_fiscal,
        'rfc'           => $this->rfc,
        'calle'         => $this->calle,
        'noExterior'    => $this->no_exterior,
        'noInterior'    => $this->no_interior,
        'colonia'       => $this->colonia,
        'localidad'     => $this->localidad,
        'municipio'     => $this->municipio,
        'estado'        => $this->estado,
        'pais'          => $this->pais,
        'cp'            => $this->cp,
        'regimenFiscal' => $this->regimen_fiscal,
        'cer'           => $this->obtenCer($this->path_certificado),
        'key'           => $this->obtenKey($this->path_key),
      ),
      'receptor' => array(
        'nombreFiscal' => $cliente['info']->nombre_fiscal,
        'rfc'          => $cliente['info']->rfc,
        'calle'        => $cliente['info']->calle,
        'noExterior'   => $cliente['info']->no_exterior,
        'noInterior'   => $cliente['info']->no_interior,
        'colonia'      => $cliente['info']->colonia,
        'localidad'    => $cliente['info']->localidad,
        'municipio'    => $cliente['info']->municipio,
        'estado'       => $cliente['info']->estado,
        'pais'         => 'MEXICO',
        'cp'           => $cliente['info']->cp,
      ),
      'serie'             => $data['dserie'],
      'folio'             => $data['dfolio'],
      'fecha'             => $data['fecha_xml'],
      'formaDePago'       => $data['dforma_pago'],
      'condicionesDePago' => $data['dcondicion_pago'] == 'credito'? 'CREDITO': 'CONTADO',
      'moneda'            => 'MXN',
      'tipoCambio'        => 1,
      'tipoDeComprobante' => $tipoComprobante,
      'metodoDePago'      => $data['dmetodo_pago'],
      'confirmacion'      => '',
      'usoCfdi'           => $data['duso_cfdi'],
      'noCertificado'     => $data['dno_certificado'],
      'totalImporte'      => $data['subtotal'],
      'descuento'         => '0',
      'total'             => $data['total'],
      'trasladosImporte'  => array(
        'iva' => $data['iva']
      ),
      'retencionesImporte'  => array(
        'isr' => $data['total_isr']
      ),
      'productos' => $productos
    );

    if (isset($cfdiRel) && $cfdiRel) {
      $datosApi['cfdiRelacionados'] = $cfdiRel;
    }

    return $datosApi;
  }

  public function obtenDatosCfdi33ComP($data, $cuentaCliente)
  {
    // echo "<pre>";
    //   var_dump($data, $cuentaCliente);
    // echo "</pre>";exit;
    $CI =& get_instance();

    // Obtiene el ID de la empresa que emite la factura, si no llega
    // entonces obtiene el ID por default.
    $id = isset($data[0]->id_empresa) ? $data[0]->id_empresa : $this->default_id_empresa;
    // Carga los datos de la empresa que emite la factura.
    $this->cargaDatosFiscales($id, 'empresas');

    // Obtiene los datos del receptor.
    $CI->load->model('clientes_model');
    $cliente = $CI->clientes_model->getClienteInfo($data[0]->id_cliente, true);

    $CI->load->model('cuentas_cobrar_model');

    $cfdi_ext = json_decode($data[0]->cfdi_ext);

    $formaDePago = '03';
    if ($data[0]->forma_pago == 'transferencia')
      $formaDePago = '03';
    elseif ($data[0]->forma_pago == 'cheque')
      $formaDePago = '02';
    elseif ($data[0]->forma_pago == 'efectivo')
      $formaDePago = '01';

    $cfdiRel = array(
      'tipoRelacion' => '07',
      'cfdiRelacionado' => array(),
    );
    $comPago = [
      'cadenaPago'        => "",
      'certificadoPago'   => "",
      'cuentaBen'         => $data[0]->num_cuenta,
      'cuentaOrd'         => $cuentaCliente->cuenta,
      'fechaPago'         => str_replace(' ', 'T', substr($data[0]->fecha, 0, 19)),
      'formaDePago'       => $formaDePago,
      'moneda'            => $cfdi_ext->moneda,
      'monto'             => $data[0]->pago,
      'nombreBancoOrdExt' => "",
      'numOperacion'      => "1",
      'rfcEmisorCtaBen'   => $data[0]->rfc,
      'rfcEmisorCtaOrd'   => $cuentaCliente->rfc,
      'selloPago'         => "",
      'tipoCadPago'       => "",
      'tipoCambio'        => $cfdi_ext->tipoCambio,
      'doctoRelacionado'  => []
    ];
    foreach ($data as $key => $pago) {
      if (floatval($pago->version) > 3.2) {
        $cfdiRel['cfdiRelacionado'][] = array(
          'uuid' => $pago->uuid,
        );

        $saldo_factura = $CI->cuentas_cobrar_model->getDetalleVentaFacturaData($pago->id_factura, 'f', true, true);
        $saldo_factura['saldo'] = floor($saldo_factura['saldo']*100)/100;
        $saldoAnt = ($saldo_factura['saldo']+$pago->pago_factura);
        $metodoDePago = 'PPD';
        if ($saldo_factura['saldo'] == 0 && $pago->parcialidades == 1)
          $metodoDePago = 'PUE';
        $comPago['doctoRelacionado'][] = array(
          "idDocumento"    => $pago->uuid,
          "serie"          => $pago->serie,
          "folio"          => $pago->folio,
          "moneda"         => $cfdi_ext->moneda,
          "tipoCambio"     => $cfdi_ext->tipoCambio,
          "metodoDePago"   => $metodoDePago,
          "numParcialidad" => $pago->parcialidades,
          "saldoAnterior"  => $saldoAnt,
          "importePagado"  => $pago->pago_factura,
          "saldoInsoluto"  => $saldo_factura['saldo']
        );
      }
    }

    $noCertificado = $this->obtenNoCertificado();

    // xml 3.3
    $datosApi = array(
      'emisor' => array(
        'nombreFiscal'  => $this->nombre_fiscal,
        'rfc'           => $this->rfc,
        'calle'         => $this->calle,
        'noExterior'    => $this->no_exterior,
        'noInterior'    => $this->no_interior,
        'colonia'       => $this->colonia,
        'localidad'     => $this->localidad,
        'municipio'     => $this->municipio,
        'estado'        => $this->estado,
        'pais'          => $this->pais,
        'cp'            => $this->cp,
        'regimenFiscal' => $this->regimen_fiscal,
        'cer'           => $this->obtenCer($this->path_certificado),
        'key'           => $this->obtenKey($this->path_key),
      ),
      'receptor' => array(
        'nombreFiscal' => $cliente['info']->nombre_fiscal,
        'rfc'          => $cliente['info']->rfc,
        'calle'        => $cliente['info']->calle,
        'noExterior'   => $cliente['info']->no_exterior,
        'noInterior'   => $cliente['info']->no_interior,
        'colonia'      => $cliente['info']->colonia,
        'localidad'    => $cliente['info']->localidad,
        'municipio'    => $cliente['info']->municipio,
        'estado'       => $cliente['info']->estado,
        'pais'         => $cliente['info']->pais,
        'cp'           => $cliente['info']->cp,
      ),
      'serie'             => 'P',
      'folio'             => $cuentaCliente->folio,
      'fecha'             => date("Y-m-d\TH:i:s"),
      'formaDePago'       => '03',
      'condicionesDePago' => 'CONTADO',
      'moneda'            => 'XXX',
      'tipoCambio'        => '1',
      'tipoDeComprobante' => 'P',
      'metodoDePago'      => 'PUE',
      'confirmacion'      => '',
      'usoCfdi'           => 'P01',
      'noCertificado'     => $noCertificado,
      'totalImporte'      => '0',
      'descuento'         => '0',
      'total'             => '0',
      'trasladosImporte'  => array(
        'iva' => '0'
      ),
      'productos' => [
        array(
          'claveProdServ'           => '84111506',
          'claveUnidad'             => 'ACT',
          'unidad'                  => 'ACT',
          'cantidad'                => '1',
          'concepto'                => 'Pago',
          'cuentaPredial'           => '',
          'descuentoProd'           => '0',
          'descuentoProdPorcent'    => '0',
          'importe'                 => '0',
          'noIdentificacion'        => '',
          'retencionCedular'        => '0',
          'retencionCedularPorcent' => '0',
          'retencionIsr'            => '0',
          'retencionIsrPorcent'     => '0',
          'retencionIva'            => '0',
          'retencionIvaPorcent'     => '0',
          'retencionIvc'            => '0',
          'retencionIvcPorcent'     => '0',
          'trasladoCedular'         => '0',
          'trasladoCedularPorcent'  => '0',
          'trasladoIeps'            => '0',
          'trasladoIepsPorcent'     => '0',
          'trasladoIsh'             => '0',
          'trasladoIshPorcent'      => '0',
          'trasladoIva'             => '0',
          'trasladoIvaPorcent'      => '0',
          'valorUnitario'           => '0',
        )
      ],
      'pagos' => [$comPago]
    );
    if ($cfdiRel) {
      $datosApi['cfdiRelacionados'] = $cfdiRel;
    }

    return $datosApi;
  }

  /**
   * Carga los datos fiscales de la empresa|proveedor que emitira la factura.
   *
   * @param  string|int $id_empresa
   * @return void
   */
	public function cargaDatosFiscales($id, $table = 'empresas')
  {
    if ($table === 'empresas')
      $pkey = 'id_empresa';
    else
      $pkey = 'id_proveedor';

		$CI =& get_instance();
		$data = $CI->db->query(
      "SELECT *
       FROM {$table}
       WHERE {$pkey} = '{$id}'"
    )->row();

		$this->path_certificado_org = $data->cer_org;
		$this->path_certificado     = $data->cer;
		$this->path_key             = $data->key_path;
		$this->pass_key             = $data->pass;

    $this->version        = $data->cfdi_version;
    $this->rfc            = $data->rfc;
    $this->nombre_fiscal  = $data->nombre_fiscal; // razon_social
    $this->regimen_fiscal = $data->regimen_fiscal;
    $this->calle          = $data->calle;
    $this->no_exterior    = $data->no_exterior;
    $this->no_interior    = $data->no_interior;
    $this->colonia        = $data->colonia;
    $this->localidad      = $data->localidad;
    $this->municipio      = $data->municipio;
    $this->estado         = $data->estado;
    $this->pais           = $data->pais;
    $this->cp             = $data->cp;
	}

	public function generaArchivos($data)
  {
		$this->cargaDatosFiscales($data['id'], $data['table']);

		// $vers = str_replace('.', '_', $this->version);
		$pathXML = $this->guardarXML($data);
		// $this->generarUnPDF($data);

    return array('pathXML' => $pathXML);
	}

	public function actualizarArchivos($data){
		$this->cargaDatosFiscales($data['id'], $data['table']);

		$vers = str_replace('.', '_', $this->version);
		$this->guardarXML($data,true);
		$this->generarUnPDF($data,array('F'),true);
	}

  /*
   |-------------------------------------------------------------------------
   | REPORTE MENSUAL
   |-------------------------------------------------------------------------
   */

  public function descargaReporte($anio, $mes)
  {
		if($this->existeReporte($anio, $mes))
    {
			$path = APPPATH.'media/cfd/reportesMensuales/'.$anio.'/1'.$this->rfc.$mes.$anio.'.txt';
			header('Content-type: text/plain');
			header('Content-Disposition: attachment; filename="1'.$this->rfc.$mes.$anio.'.txt"');
			readfile($path);
		}
	}

	public function existeReporte($anio, $mes)
  {
		$path = APPPATH.'media/cfd/reportesMensuales/'.$anio.'/1'.$this->rfc.$mes.$anio.'.txt';
		return file_exists($path);
	}

	public function generaReporte($anio, $mes, $reporte, $ex_nombre='')
  {
		$path = APPPATH.'media/cfd/reportesMensuales/';
		if(!file_exists($path.$anio.'/'))
			$this->crearFolder($path, $anio."/");

		$path .= $anio.'/1'.$this->rfc.$mes.$anio.$ex_nombre.'.txt';
		$fp = fopen($path, 'w');
		fwrite($fp, $reporte);
		fclose($fp);
// 		$this->descargaReporte($anio, $mes);
		return array('tipo' => 0, 'mensaje' => 'El reporte se genero correctamente.');;
	}

  /**
   * Regresa el MES que corresponde en texto.
   *
   * @param  int $mes
   * @return string
   */
	private function mesToString($mes)
  {
		switch(floatval($mes))
    {
			case 1: return 'ENERO'; break;
			case 2: return 'FEBRERO'; break;
			case 3: return 'MARZO'; break;
			case 4: return 'ABRIL'; break;
			case 5: return 'MAYO'; break;
			case 6: return 'JUNIO'; break;
			case 7: return 'JULIO'; break;
			case 8: return 'AGOSTO'; break;
			case 9: return 'SEPTIEMBRE'; break;
			case 10: return 'OCTUBRE'; break;
			case 11: return 'NOVIEMBRE'; break;
			case 12: return 'DICIEMBRE'; break;
		}
	}

  /**
   * Acomoda el folio.
   *
   * @param  string $folio
   * @return string
   */
	public function acomodarFolio($folio)
  {
		$folio .= '';
		for($i=strlen($folio); $i<8; ++$i){
			$folio = '0'.$folio;
		}
		return $folio;
	}

  /**
   * Ajusta el texto.
   *
   * @param  string $cadena
   * @param  string $caracteres
   * @return string
   */
	public function ajustaTexto($cadena, $caracteres)
  {
    $res  = '';
    $len  = strlen($cadena);
    $cont = 0;

		while($cont<$len)
    {
      $res  .= substr($cadena, $cont, $caracteres)."<br>";
      $cont += $caracteres;
		}

		return $res;
	}

	/**
	 * Valida si el directorio espesificado existe o si no lo crea.
   *
   * @param string $tipo
   * @param string $path
   * @return string
	 */
	private function validaDir($tipo, $path)
  {
		$path = APPPATH.'media/cfdi/'.$path;

		if($tipo === 'anio')
      $directorio = date("Y");
    else
      $directorio = $this->mesToString(date("n"));

		if( ! file_exists($path.$directorio."/"))
			$this->crearFolder($path, $directorio."/");

		return $directorio;
	}

	/**
	 * Crea un folder en el servidor.
   *
	 * @param $path_directorio: string. ruta donde se creara el directorio.
	 * @param $nombre_directorio: string. nombre del folder a crear.
   * @return mixed array|boolean
	 */
	private function crearFolder($path_directorio, $nombre_directorio)
  {
		if($nombre_directorio != "" && file_exists($path_directorio))
    {
			if( ! file_exists($path_directorio.$nombre_directorio))
				return mkdir($path_directorio.$nombre_directorio, 0777);
			else
				return true;
		}
    else
			return false;
	}

	private function obtenFechaMes($fecha)
  {
		$fecha = explode('-', $fecha);
		return array($fecha[0],$fecha[1]);
	}

  /*
   |-------------------------------------------------------------------------
   | FUNCIONES PARA GENERAR Y GUARDAR|DESCARGAR EL XML.
   |-------------------------------------------------------------------------
   */

  /**
   * Guarda el XML en capertas especificas AÑO/MES.
   *
   * @param  array  $data
   * @param  boolean $update
   * @return void
   */
	private function guardarXML($data, $update = false)
  {
    $vers = str_replace('.', '_', $this->version);
    $xml  = $this->{'generarXML'.$vers}($data);

    if( ! $update)
    {
			$dir_anio = $this->validaDir('anio', 'facturasXML/');
			$dir_mes = $this->validaDir('mes', 'facturasXML/'.$dir_anio.'/');
		}
		else
    {
      $fecha    = $this->obtenFechaMes($data['comprobante']['fecha']);
      $dir_anio = $fecha[0];
      $dir_mes  = $this->mesToString($fecha[1]);

			if( ! file_exists(APPPATH.'media/cfdi/facturasXML/'.$dir_anio.'/'))
        $this->crearFolder(APPPATH.'media/cfdi/facturasXML/', $dir_anio.'/');

			if( ! file_exists(APPPATH.'media/cfdi/facturasXML/'.$dir_anio.'/'.$dir_mes.'/'))
				$this->crearFolder(APPPATH.'media/cfdi/facturasXML/'.$dir_anio.'/', $dir_mes.'/');
		}

		$path_guardar = APPPATH.'media/cfdi/facturasXML/'.$dir_anio.'/'.$dir_mes.'/'.
			$this->rfc.'-'.$data['comprobante']['serie'].'-'.$this->acomodarFolio($data['comprobante']['folio']).'.xml';

		$fp = fopen($path_guardar, 'w');
		fwrite($fp, $xml);
		fclose($fp);

    return $path_guardar;
	}

  /**
   * Descarga el XML.
   *
   * @param  array $data
   * @param  string $pathXML
   * @return void
   */
	public function descargarXML($data = null, $pathXML = null)
  {
    // Carga los datos fiscales de la empresa|proveedor.
    $this->cargaDatosFiscales($data['id'], $data['table']);

    // Si el parametro $data contiene datos.
    if ( ! is_null($data) && is_null($pathXML))
    {
      $vers = str_replace('.', '_', $this->version);
      $xml  = $this->{'generarXML'.$vers}($data);
    }
    else
    {
      // Obtiene el contenido del XML.
      $xml = file_get_contents($pathXML);
    }

    header('Content-type: content-type: text/xml');
    header('Content-Disposition: attachment; filename="'.$this->rfc.'-'.$data['comprobante']['serie'].'-'.$this->acomodarFolio($data['comprobante']['folio']).'.xml"');

    echo $xml;
	}

  /**
   * Genera el contentido del XML con la informacion de facturacion.
   *
   * @param  array  $data
   * @return string
   */
	public function generarXML3_2($data = array())
  {
		$xml = '';
		$xml .= '<?xml version="1.0" encoding="UTF-8"?> ';
		$xml .= '<cfdi:Comprobante xmlns:cfdi="http://www.sat.gob.mx/cfd/3" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:nomina="http://www.sat.gob.mx/nomina" xsi:schemaLocation="http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv32.xsd" ';
		$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬version="'.$this->replaceSpecialChars($data['comprobante']['version']).'" ';
    if(isset($data['comprobante']['serie']) && $data['comprobante']['serie'] !== '')
		  $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬serie="'.$data['comprobante']['serie'].'" ';
		$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬folio="'.$data['comprobante']['folio'].'" ';
    $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬fecha="'.$data['comprobante']['fecha'].'" ';
		$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬sello="'.$data['comprobante']['sello'].'" ';
    $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬formaDePago="'.$data['comprobante']['formaDePago'].'" ';
    $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬noCertificado="'.$data['comprobante']['noCertificado'].'" ';
    $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬certificado="'.$data['comprobante']['certificado'].'" ';
    $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬condicionesDePago="'.$data['comprobante']['condicionesDePago'].'" ';
    $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬subTotal="'.(float)$data['comprobante']['subTotal'].'" ';

    // Nomina ---
    // $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬descuento="16.67" ';
    // $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬motivoDescuento="Deducciones nómina" ';

    $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬total="'.(float)$data['comprobante']['total'].'" ';
    $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬tipoDeComprobante="'.$data['comprobante']['tipoDeComprobante'].'" ';
    // $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬tipoDeComprobante="egreso" ';
    $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬metodoDePago="'.$data['comprobante']['metodoDePago'].'" ';
    $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬LugarExpedicion="'.$this->municipio.', '.$this->estado.'" ';
    if($data['comprobante']['NumCtaPago'] !== '')
      $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬NumCtaPago="'.$data['comprobante']['NumCtaPago'].'" ';
		$xml .= '>';

		$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬<cfdi:Emisor rfc="'.$this->replaceSpecialChars($this->rfc).'" nombre="'.$this->replaceSpecialChars($this->nombre_fiscal).'">';
		$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬<cfdi:DomicilioFiscal ';
		$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬calle="'.$this->replaceSpecialChars($this->calle).'" ';
		$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬noExterior="'.$this->replaceSpecialChars($this->no_exterior).'" ';
		if($this->no_interior !== '')
			$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬noInterior="'.$this->replaceSpecialChars($this->no_interior).'" ';
		$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬colonia="'.$this->replaceSpecialChars($this->colonia).'" ';
		$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬localidad="'.$this->replaceSpecialChars($this->localidad).'" ';
		$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬municipio="'.$this->replaceSpecialChars($this->municipio).'" ';
		$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬estado="'.$this->replaceSpecialChars($this->estado).'" ';
		$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬pais="'.$this->replaceSpecialChars($this->pais).'" ';
		$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬codigoPostal="'.$this->replaceSpecialChars($this->cp).'"';
		$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬/>';

		$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬<cfdi:ExpedidoEn ';
		$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬calle="'.$this->replaceSpecialChars($this->calle).'" ';
		$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬noExterior="'.$this->replaceSpecialChars($this->no_exterior).'" ';
		if($this->no_interior !== '')
			$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬noInterior="'.$this->replaceSpecialChars($this->no_interior).'" ';
		$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬colonia="'.$this->replaceSpecialChars($this->colonia).'" ';
		$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬localidad="'.$this->replaceSpecialChars($this->localidad).'" ';
		$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬municipio="'.$this->replaceSpecialChars($this->municipio).'" ';
		$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬estado="'.$this->replaceSpecialChars($this->estado).'" ';
		$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬pais="'.$this->replaceSpecialChars($this->pais).'" ';
		$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬codigoPostal="'.$this->replaceSpecialChars($this->cp).'"';
		$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬/>';
		$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬<cfdi:RegimenFiscal ';
		$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬Regimen="'.$this->replaceSpecialChars($this->regimen_fiscal).'" ';
		$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬/>';
		$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬</cfdi:Emisor>';

		$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬<cfdi:Receptor rfc="'.$data['receptor']['rfc'].'" nombre="'.$this->replaceSpecialChars($data['receptor']['nombre']).'">';
		$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬<cfdi:Domicilio ';
    if (isset($data['domicilio']['calle']) && $data['domicilio']['calle'] !== '')
		  $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬calle="'.$this->replaceSpecialChars($data['domicilio']['calle']).'" ';
    if (isset($data['domicilio']['noExterior']) && $data['domicilio']['noExterior'] !== '')
		  $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬noExterior="'.$this->replaceSpecialChars($data['domicilio']['noExterior']).'" ';
		if(isset($data['domicilio']['noInterior']) && $data['domicilio']['noInterior'] !== '')
			$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬noInterior="'.$this->replaceSpecialChars($data['domicilio']['noInterior']).'" ';
    if(isset($data['domicilio']['colonia']) && $data['domicilio']['colonia'] !== '')
      $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬colonia="'.$this->replaceSpecialChars($data['domicilio']['colonia']).'" ';
    if(isset($data['domicilio']['localidad']) && $data['domicilio']['localidad'] !== '')
		  $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬localidad="'.$this->replaceSpecialChars($data['domicilio']['localidad']).'" ';
    if(isset($data['domicilio']['municipio']) && $data['domicilio']['municipio'] !== '')
		  $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬municipio="'.$this->replaceSpecialChars($data['domicilio']['municipio']).'" ';
    if(isset($data['domicilio']['estado']) && $data['domicilio']['estado'] !== '')
		  $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬estado="'.$this->replaceSpecialChars($data['domicilio']['estado']).'" ';
    if(isset($data['domicilio']['pais']) && $data['domicilio']['pais'] !== '')
		  $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬pais="'.$this->replaceSpecialChars($data['domicilio']['pais']).'" ';
    if(isset($data['domicilio']['codigoPostal']) && $data['domicilio']['codigoPostal'] !== '')
		  $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬codigoPostal="'.$this->replaceSpecialChars($data['domicilio']['codigoPostal']).'"';
		$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬/>';
		$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬</cfdi:Receptor>';

		$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬<cfdi:Conceptos>';

		foreach($data['concepto'] as $concepto)
    {
      if ($data['sinCosto'])
      {
        if ($concepto['idClasificacion'] != '49' AND $concepto['idClasificacion'] != '50' AND
            $concepto['idClasificacion'] != '51' AND $concepto['idClasificacion'] != '52' AND
            $concepto['idClasificacion'] != '53')
        {
          $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬<cfdi:Concepto ';
          $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬cantidad="'.(float)$concepto['cantidad'].'" ';
          $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬unidad="'.$concepto['unidad'].'" ';
          $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬descripcion="'.$this->replaceSpecialChars($concepto['descripcion']).'" ';
          $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬valorUnitario="'.(float)$concepto['valorUnitario'].'" ';
          $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬importe="'.(float)$concepto['importe'].'"';
          $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬>';
          $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬</cfdi:Concepto>';
        }
      }
      else
      {
        $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬<cfdi:Concepto ';
        $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬cantidad="'.(float)$concepto['cantidad'].'" ';
        $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬unidad="'.$concepto['unidad'].'" ';
        $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬descripcion="'.$this->replaceSpecialChars($concepto['descripcion']).'" ';
        $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬valorUnitario="'.(float)$concepto['valorUnitario'].'" ';
        $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬importe="'.(float)$concepto['importe'].'"';
        $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬>';
        $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬</cfdi:Concepto>';
      }
		}
		$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬</cfdi:Conceptos>';

		$totalImpuestosRetenidos = '';
		if(isset($data['totalImpuestosRetenidos']))
			$totalImpuestosRetenidos = 'totalImpuestosRetenidos="'.(float)$data['totalImpuestosRetenidos'].'"';

		$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬<cfdi:Impuestos '.$totalImpuestosRetenidos.' totalImpuestosTrasladados="'.(float)$data['totalImpuestosTrasladados'].'">';
		if(isset($data['totalImpuestosRetenidos'])){
			$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬<cfdi:Retenciones>';
			$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬<cfdi:Retencion ';
			$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬impuesto="'.$data['retencion']['impuesto'].'" ';
			$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬importe="'.(float)$data['retencion']['importe'].'"';
			$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬/>';
			$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬</cfdi:Retenciones>';
		}
		$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬<cfdi:Traslados>';
		foreach($data['traslado'] as $traslado){
			$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬<cfdi:Traslado ';
			$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬impuesto="IVA" ';
			$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬tasa="'.(float)$traslado['tasa'].'" ';
			$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬importe="'.(float)$traslado['importe'].'"';
			$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬/>';
		}
		$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬</cfdi:Traslados>';
		$xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬</cfdi:Impuestos>';

    // Nodo Complemento
    // $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬<cfdi:Complemento>';
    // // $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬<nomina:Nomina Version="1.1" RegistroPatronal=""  NumEmpleado="00001" CURP="AASO870618HCMLS02" TipoRegimen="Regimen" NumSeguridadSocial="123456789" FechaPago="2013-12-15" FechaInicialPago="2013-12-01" FechaFinalPago="2013-12-15" NumDiasPagados="15" Departamento="Sistemas" CLABE="" Banco="" FechaInicioRelLaboral="2013-04-22" Antiguedad="30" Puesto="Desarrollador de Software" TipoContrato="Base" TipoJornada="continuada" PeriodicidadPago="quincenal" SalarioBaseCotApor="" RiesgoPuesto="" SalarioDiarioIntegrado="">';
    // $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬<nomina:Nomina Version="1.1" NumEmpleado="0001" CURP="OOAG791212MCMCPD03" TipoRegimen="1" FechaPago="2013-12-15" FechaInicialPago="2013-12-01" FechaFinalPago="2013-12-15" NumDiasPagados="15" Departamento="Sistemas" FechaInicioRelLaboral="2013-04-22" Antiguedad="30" Puesto="Desarrollador de Software" TipoContrato="Base" TipoJornada="continuada" PeriodicidadPago="quincenal">';

    // $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬<nomina:Percepciones TotalGravado="1029.15" TotalExento="0">';
    // $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬<nomina:Percepcion TipoPercepcion="001" Clave="AAA" Concepto="Sueldos, Salarios Rayas y Jornales" ImporteGravado="1029.15" ImporteExento="0" />';
    // $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬</nomina:Percepciones>';

    // $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬<nomina:Deducciones TotalGravado="16.67" TotalExento="0">';
    // $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬<nomina:Deduccion TipoDeduccion="001" Clave="BBB" Concepto="Seguridad social" ImporteGravado="16.67" ImporteExento="0" />';
    // $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬<nomina:Deduccion TipoDeduccion="002" Clave="CCC" Concepto="ISR" ImporteGravado="0" ImporteExento="0" />';
    // $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬</nomina:Deducciones>';

    // // $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬<nomina:Incapacidades>';
    // // $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬<nomina:Incapacidad DiasIncapacidad="" TipoIncapacidad="" Descuento="" />';
    // // $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬</nomina:Incapacidades>';

    // // $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬<nomina:HorasExtras>';
    // // $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬<nomina:HorasExtra Dias="" TipoHoras="" HorasExtra="" ImportePagado="" />';
    // // $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬</nomina:HorasExtras>';

    // $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬¬</nomina:Nomina>';
    // $xml .= '¬¬¬¬¬¬¬¬¬¬¬¬¬</cfdi:Complemento>';

		$xml .= '</cfdi:Comprobante>';

		$xml = str_replace('¬','',$xml);

    // echo "<pre>";
    //   var_dump($xml);
    // echo "</pre>";exit;

		return $xml;
	}

  /*
   |------------------------------------------------------------------------
   | FUNCIONES HELPERS
   |------------------------------------------------------------------------
   */

  /**
   * Reemplaza los siguientes caracteres especiales segun anexo 20:
   *
   *   En el caso del & se deberá usar la secuencia &amp;
   *   En el caso del “ se deberá usar la secuencia &quot;
   *   En el caso del < se deberá usar la secuencia &lt;
   *   En el caso del > se deberá usar la secuencia &gt;
   *   En el caso del ‘ se deberá usar la secuencia &apos;
   *
   * @param  string $texto
   * @return string
   */
  private function replaceSpecialChars($texto)
  {
    $texto = preg_replace(array('/”/', '/’/'), array('"', '\''), $texto);
    return preg_replace('/&#0*39;/', '&apos;', htmlspecialchars($texto, ENT_QUOTES));

    // $caracteres = array('/&/', '/</', '/>/', '/”/', '/"/', '/\'/', '/’/');
    // $reemplazo  =  array('&amp;', '&lt;', '&gt;', '&quot;', '&quot;', '&apos;', '&apos;');
    // return preg_replace($caracteres, $reemplazo, $texto);
  }

  /**
   * Da formato numerico a una cadena
   *
   * @param string|int $number
   * @param int $decimales
   * @param string $sigini
   * @param boolean $condecim
   *
   * @return string
   */
  private function limpiaDecimales($number, $decimales=2, $sigini='$', $condecim=true)
  {
    $number = floatval($number);
    $num = explode('.', $number);
    if($condecim)
    {
      if(isset($num[1]))
        $decimales = (strlen($num[1])<$decimales? strlen($num[1]): $decimales);
      else
        $decimales = 0;
    }
    return $sigini.number_format($number, $decimales, '.', ',');
  }

  /*
   |-------------------------------------------------------------------------
   | FUNCIONES PARA GENERAR PDF's
   |-------------------------------------------------------------------------
   */

	/**
	 * FUNCIONES DE LS DISTINTAS VERSIONES DE CFD PARA LOS PDF
	 */
	public function generarPDF($data=array(), $accion=array('F'), $update=false){
		$this->cargaDatosFiscales($data['id_nv_fiscal']);
		$this->generarUnPDF($data, $accion, $update);
	}

	public function generarUnPDF($data=array(), $accion=array('F'), $update=false){
		if(count($data)>0){
			$ci =& get_instance();
			$ci->load->library('mypdf');

			// Creacion del objeto de la clase heredada
			$pdf = new MYpdf('P', 'mm', 'Letter');
			$pdf->show_head = false;
			$vers = str_replace('.', '_', $this->version);
			$this->{'generarFacturaPDF'.$vers}($pdf, $data);

			//-----------------------------------------------------------------------------------

			if(!$update){
				$dir_anio = $this->validaDir('anio', 'facturasPDF/');
				$dir_mes = $this->validaDir('mes', 'facturasPDF/'.$dir_anio.'/');
			}
			else{
				$fecha = $this->obtenFechaMes($data['fecha_xml']);
				$dir_anio = $fecha[0];
				$dir_mes = $this->mesToString($fecha[1]);

				if(!file_exists(APPPATH.'media/cfd/facturasPDF/'.$dir_anio.'/')){
					$this->crearFolder(APPPATH.'media/cfd/facturasPDF/', $dir_anio.'/');
				}
				if(!file_exists(APPPATH.'media/cfd/facturasPDF/'.$dir_anio.'/'.$dir_mes.'/')){
					$this->crearFolder(APPPATH.'media/cfd/facturasPDF/'.$dir_anio.'/', $dir_mes.'/');
				}
			}

			if(count($accion)>0){
				foreach($accion as $a){
					switch (strtolower($a)){
						case 'v': // VISUALIZA PDF EN WEB
							$pdf->Output($dir_anio.'|'.$dir_mes.'|'.$this->rfc.'-'.$data['serie'].'-'.$this->acomodarFolio($data['folio']).'.pdf', 'I');
						break;
						case 'f': // GUARDA EN DIRECTORIO facturasPDF
							$path_guardar = APPPATH.'media/cfd/facturasPDF/'.$dir_anio.'/'.$dir_mes.'/'.
															$this->rfc.'-'.$data['serie'].'-'.$this->acomodarFolio($data['folio']).'.pdf';
							$pdf->Output($path_guardar, 'F');
						break;
						case 'd':  // DESCARGA DIRECTA DEL PDF
							$pdf->Output($dir_anio.'|'.$dir_mes.'|'.$this->rfc.'-'.$data['serie'].'-'.$this->acomodarFolio($data['folio']).'.pdf', 'D');
						break;
						default: // VISUALIZA PDF EN WEB
							$pdf->Output($dir_anio.'|'.$dir_mes.'|'.$this->rfc.'-'.$data['serie'].'-'.$this->acomodarFolio($data['folio']).'.pdf', 'I');
					}
				}
			}
		}
	}
	public function generarMasPDF($data=array(), $accion='I'){
		if(count($data)>0){
			$ci =& get_instance();
			$ci->load->library('mypdf');

			// Creacion del objeto de la clase heredada
			$pdf = new MYpdf('P', 'mm', 'Letter');
			$pdf->show_head = false;

			foreach ($data as $key => $value) {
				$this->cargaDatosFiscales($value['id_nv_fiscal']);
				$vers = str_replace('.', '_', $this->version);
				$this->{'generarFacturaPDF'.$vers}($pdf, $value);
			}


			switch (strtolower($accion)){
				case 'd':  // DESCARGA DIRECTA DEL PDF
					$pdf->Output($this->rfc.'_'.date("Y-m-d").'.pdf', 'D');
				break;
				default: // VISUALIZA PDF EN WEB
					$pdf->Output($this->rfc.'_'.date("Y-m-d").'.pdf', 'I');
			}
		}
	}
	public function generarFacturaPDF2_2(&$pdf, $data){
			$pdf->AddPage();
			$pdf->SetFont('Arial','',8);

			$y = 40;
			$pdf->Image(APPPATH.'/images/logo.png',8,20,25,25,"PNG");

			$pdf->SetFont('Arial','B',17);
			$pdf->SetXY(38, $y-30);
			$pdf->Cell(120, 6, $this->razon_social , 0, 0, 'C');

			$pdf->SetFont('Arial','',13);
			$pdf->SetXY(38, $y-23);
			$pdf->MultiCell(116, 6, "R.F.C.".$this->rfc." \n Pista Aerea No. S/N \n Ranchito 60800 Ranchito Michoacan Mexico \n {$this->regimen_fiscal} " , 0,'C',0);
			$pdf->SetDrawColor(140,140,140);
			// ----------- FOLIO ------------------
			$pdf->SetFont('Arial','',13);
			$pdf->SetXY(164, ($y-29));
			$pdf->Cell(38, 7, (substr($data['fecha_xml'], 0, 10) < '2012-10-31'? 'Recibo de honorarios': 'Factura') , 0, 0, 'C');

			$pdf->SetXY(158, ($y-22));
			$pdf->Cell(50, 13, '' , 1, 0, 'C');

			$pdf->SetFont('Arial','B',11);
			$pdf->SetTextColor(255,255,255);
			$pdf->SetFillColor(140,140,140);
			$pdf->SetXY(158, ($y-22));
			$pdf->Cell(50, 5, 'Serie y Folio', 1, 0, 'C',1);

			$pdf->SetFont('Arial','',12);
			$pdf->SetTextColor(255,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetXY(158, $y-17);
			$pdf->Cell(50, 8, $data['serie'].'-'.$data['folio'] , 0, 0, 'C');

			// ----------- FECHA ------------------

			$pdf->SetXY(158, ($y-8));
			$pdf->Cell(50, 13, '' , 1, 0, 'C');

			$pdf->SetFont('Arial','B',11);
			$pdf->SetTextColor(255,255,255);
			$pdf->SetFillColor(140,140,140);
			$pdf->SetXY(158, ($y-8));
			$pdf->Cell(50, 5, 'Fecha de Expedición' , 1, 0, 'C',1);

			$pdf->SetFont('Arial','',12);
			$pdf->SetTextColor(255,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetXY(158, ($y-3));
			$pdf->Cell(50, 8, $data['fecha_xml'] , 1, 0, 'C',1);

			// ----------- No y Año aprob ------------------

			$pdf->SetXY(158, ($y+6));
			$pdf->Cell(50, 13, '' , 1, 0, 'C');

			$pdf->SetFont('Arial','B',11);
			$pdf->SetTextColor(255,255,255);
			$pdf->SetFillColor(140,140,140);
			$pdf->SetXY(158, ($y+6));
			$pdf->Cell(50, 5, 'No. y Año aprobracion' , 1, 0, 'C',1);

			$pdf->SetFont('Arial','',12);
			$pdf->SetTextColor(255,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetXY(158, ($y+11));
			$pdf->Cell(50, 8, $data['no_aprobacion'].'-'.$data['ano_aprobacion'] , 1, 0, 'C',1);

			// ----------- No Certificado ------------------

			$pdf->SetXY(158, ($y+20));
			$pdf->Cell(50, 13, '' , 1, 0, 'C');

			$pdf->SetFont('Arial','B',11);
			$pdf->SetTextColor(255,255,255);
			$pdf->SetFillColor(140,140,140);
			$pdf->SetXY(158, ($y+20));
			$pdf->Cell(50, 5, 'No. Certificado' , 1, 0, 'C',1);

			$pdf->SetFont('Arial','',12);
			$pdf->SetTextColor(255,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetXY(158, ($y+25));
			$pdf->Cell(50, 8, $data['no_certificado'] , 1, 0, 'C',1);

			// ----------- DATOS CLIENTE ------------------

			$pdf->SetXY(8, ($y+7));
			$pdf->Cell(149, 41, '' , 1, 0, 'C');

			$pdf->SetFont('Arial','B',9);
			$pdf->SetTextColor(255,255,255);
			$pdf->SetFillColor(140,140,140);

			$pdf->SetXY(8, $y+7);  // BLOQUE DATOS 1
			$pdf->Cell(16, 41, '', 0, 0, 'C',1);

			$pdf->SetXY(8, $y+9);
			$pdf->Cell(16, 6, 'R.F.C.', 0, 0, 'L');

			$pdf->SetXY(8, $y+15);
			$pdf->Cell(16, 6, 'NOMBRE' , 0, 0, 'L');

			$pdf->SetXY(8, $y+21);
			$pdf->Cell(16, 6, 'CALLE' , 0, 0, 'L');

			$pdf->SetXY(8, $y+27);
			$pdf->Cell(16, 6, 'NUMERO' , 0, 0, 'L');

			$pdf->SetXY(8, $y+33);
			$pdf->Cell(16, 6, 'COLONIA' , 0, 0, 'L');

			$pdf->SetXY(8, $y+39);
			$pdf->Cell(16, 6, 'EDO' , 0, 0, 'L');

			$pdf->SetXY(70, $y+27); // BLOQUE DATOS 2
			$pdf->Cell(18, 21, '', 0, 0, 'C',1);

			$pdf->SetXY(70, $y+27);
			$pdf->Cell(18, 6, 'INT' , 0, 0, 'L');

			$pdf->SetXY(70, $y+33);
			$pdf->Cell(18, 6, 'MUNICIPIO' , 0, 0, 'L');

			$pdf->SetXY(70, $y+39);
			$pdf->Cell(18, 6, 'PAIS' , 0, 0, 'L');

			$pdf->SetXY(117, $y+27); // BLOQUE DATOS 3
			$pdf->Cell(16, 14, '', 0, 0, 'C',1);

			$pdf->SetXY(117, $y+27);
			$pdf->Cell(18, 6, 'C.P.' , 0, 0, 'L');

			$pdf->SetXY(117, $y+33);
			$pdf->Cell(18, 6, 'CIUDAD' , 0, 0, 'L');

			$pdf->SetFont('Arial','',7);
			$pdf->SetTextColor(0,0,0);

			$pdf->SetXY(25, $y+9); // BLOQUE DATOS 1 INFO
			$pdf->Cell(132, 6, strtoupper($data['crfc']), 0, 0, 'L');

			$pdf->SetXY(25, $y+15);
			$pdf->Cell(132, 6, strtoupper($data['cnombre']), 0, 0, 'L');

			$pdf->SetXY(25, $y+21);
			$pdf->Cell(132, 6, strtoupper($data['ccalle']), 0, 0, 'L');

			$pdf->SetXY(25, $y+27);
			$pdf->Cell(44, 6, strtoupper($data['cno_exterior']), 0, 0, 'L');

			$pdf->SetXY(25, $y+33);
			$pdf->Cell(44, 6, strtoupper($data['ccolonia']), 0, 0, 'L');

			$pdf->SetXY(25, $y+39);
			$pdf->Cell(44, 6, strtoupper($data['cestado']), 0, 0, 'L');

			$pdf->SetXY(88, $y+27); // BLOQUE DATOS 2 INFO
			$pdf->Cell(28, 6, strtoupper($data['cno_interior']), 0, 0, 'L');

			$pdf->SetXY(88, $y+33);
			$pdf->Cell(28, 6, strtoupper($data['cmunicipio']), 0, 0, 'L');

			$pdf->SetXY(88, $y+39);
			$pdf->Cell(28, 6, strtoupper($data['cpais']), 0, 0, 'L');

			$pdf->SetXY(133, $y+27); // BLOQUE DATOS 3 INFO
			$pdf->Cell(24, 6, strtoupper($data['ccp']), 0, 0, 'L');

			$pdf->SetXY(133, $y+33);
			$pdf->Cell(24, 6, strtoupper($data['cmunicipio']), 0, 0, 'L');

			// ----------- TABLA CON LOS PRODUCTOS ------------------
			$pdf->SetY($y+50);
			$aligns = array('C', 'C', 'C', 'C');
			$widths = array(25, 109, 33,33);
			$header = array('CANTIDAD', 'DESCRIPCION', 'PRECIO UNIT.','IMPORTE');
			foreach($data['productos'] as $key => $item){
				$band_head = false;
				if($pdf->GetY() >= 200 || $key==0){ //salta de pagina si exede el max
					if($key > 0)
						$pdf->AddPage();

					$pdf->SetFont('Arial','B',8);
					$pdf->SetTextColor(255,255,255);
					$pdf->SetFillColor(140,140,140);
					$pdf->SetX(8);
					$pdf->SetAligns($aligns);
					$pdf->SetWidths($widths);
					$pdf->Row($header, true);
				}

				$pdf->SetFont('Arial','',10);
				$pdf->SetTextColor(0,0,0);

				$datos = array($item['cantidad'], $item['descripcion'], String::formatoNumero($item['precio_unit']),String::formatoNumero($item['importe']));

				$pdf->SetX(8);
				$pdf->SetAligns($aligns);
				$pdf->SetWidths($widths);
				$pdf->Row($datos, false);
			}

			//------------ SUBTOTAL, IVA ,TOTAL --------------------

			$y = $pdf->GetY();
			$pdf->SetFont('Arial','B',10);
			$pdf->SetTextColor(255,255,255);
			$pdf->SetFillColor(140,140,140);

			$pdf->SetXY(144, ($y+5));
			$pdf->Cell(31, 6, 'Subtotal' , 1, 0, 'C',1);
			$pdf->SetXY(144, ($y+11));

			if (strtoupper($data['crfc']) != 'XAXX010101000') {
				$pdf->Cell(31, 6, 'IVA' , 1, 0, 'C',1);
				$pdf->SetXY(144, ($y+17));
			}

			if (isset($data['total_isr'])) {
				$pdf->Cell(31, 6, 'Retencion ISR' , 1, 0, 'C',1);
				$pdf->SetXY(144, ($y+23));
			}

			$pdf->Cell(31, 6, 'Total' , 1, 0, 'C',1);

			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetXY(175, ($y+5));
			$pdf->Cell(33, 6, String::formatoNumero($data['subtotal'],2) , 1, 0, 'C');
			$pdf->SetXY(175, ($y+11));

			if (strtoupper($data['crfc']) != 'XAXX010101000') {
				$pdf->Cell(33, 6, String::formatoNumero($data['importe_iva'],2) , 1, 0, 'C');
				$pdf->SetXY(175, ($y+17));
			}

			if (isset($data['total_isr'])) {
				$pdf->Cell(33, 6, (isset($data['total_isr'])) ? String::formatoNumero($data['total_isr'],2) : '$0.00' , 1, 0, 'C');
				$pdf->SetXY(175, ($y+23));
			}

			$pdf->Cell(33, 6, String::formatoNumero($data['total'],2) , 1, 0, 'C');

			//------------ TOTAL CON LETRA--------------------

			$pdf->SetXY(8, ($y+5));
			$pdf->Cell(134, 24, '' , 1, 0, 'C');

			$pdf->SetFont('Arial','B',10);
			$pdf->SetTextColor(255,255,255);
			$pdf->SetFillColor(140,140,140);
			$pdf->SetXY(8, ($y+5));
			$pdf->Cell(134, 6, '	IMPORTE CON LETRA' , 0, 0, 'L',1);

			$pdf->SetFont('Arial','',8);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetXY(9, ($y+12));
			$pdf->MultiCell(130, 6, $data['total_letra'] , 0, 'L');

			$pdf->SetXY(9, ($y+24));
			$pdf->Cell(130, 6, "Método de Pago: {$data['metodo_pago']}".(($data['metodo_pago'] == 'efectivo')?'':" | No. Cuenta: {$data['no_cuenta_pago'] }") , 0, 0, 'L',0);

			//------------ CADENA ORIGINAL --------------------
			$y += 32;
			$pdf->SetY($y);
			$pdf->SetX(8);

			$pdf->SetFont('Arial','B',10);
			$pdf->SetTextColor(255,255,255);
			$pdf->SetFillColor(140,140,140);

			$pdf->SetAligns(array('L'));
			$pdf->SetWidths(array(200));
			$pdf->Row(array('CADENA ORIGINAL'), true);

			$pdf->SetX(8);

			$pdf->SetFont('Arial','',9);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);

			$pdf->SetAligns(array('L'));
			$pdf->SetWidths(array(200));
			$pdf->Row(array($data['cadena_original']), false);

			//------------ SELLO DIGITAL --------------------

			$y = $pdf->GetY();

			$pdf->SetY($y+3);
			$pdf->SetX(8);

			$pdf->SetFont('Arial','B',10);
			$pdf->SetTextColor(255,255,255);
			$pdf->SetFillColor(140,140,140);

			$pdf->SetAligns(array('L'));
			$pdf->SetWidths(array(200));
			$pdf->Row(array('SELLO DIGITAL'), true);

			$pdf->SetX(8);

			$pdf->SetFont('Arial','',9);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);

			$pdf->SetAligns(array('L'));
			$pdf->SetWidths(array(200));
			$pdf->Row(array($data['sello']), false);

			if($data['fobservaciones'] != ''){
				$y = $pdf->GetY();
				$pdf->SetY($y+3);
				$pdf->SetX(8);

				$pdf->SetFont('Arial','B',10);
				$pdf->SetTextColor(255,255,255);
				$pdf->SetFillColor(140,140,140);

				$pdf->SetAligns(array('L'));
				$pdf->SetWidths(array(200));
				$pdf->Row(array('OBSERVACIONES'), true);

				$pdf->SetX(8);

				$pdf->SetFont('Arial','',9);
				$pdf->SetTextColor(0,0,0);
				$pdf->SetFillColor(255,255,255);

				$pdf->SetAligns(array('L'));
				$pdf->SetWidths(array(200));
				$pdf->Row(array($data['fobservaciones']), false);
			}

			$y = $pdf->GetY();

			$pdf->SetFont('Arial','',8);
			$pdf->SetXY(8, $y+2);
			$pdf->Cell(200,5,'ESTE DOCUMENTO ES UNA IMPRESIÓN DE UN COMPROBANTE FISCAL DIGITAL',0,0,'C');

			//------------ IMAGEN CANDELADO --------------------

			if(isset($data['status'])){
				if($data['status']=='ca'){
					$pdf->Image(APPPATH.'/images/cancelado.png',20,40,190,190,"PNG");
				}
			}
	}

}