<?php
class cuentas_cobrar_model extends CI_Model{
	
	function __construct(){
		parent::__construct();
	}
	
	/********** CUENTAS POR PAGAR ************/
	/**
	 * Obtiene el listado de proveedores y el saldo que se les debe
	 */
	public function getCuentasXCobrarData($per_pag='9999'){
		$sql = '';
		//paginacion
		$params = array(
				'result_items_per_page' => $per_pag,
				'result_page' => (isset($_GET['pag'])? $_GET['pag']: 0)
		);
		if($params['result_page'] % $params['result_items_per_page'] == 0)
			$params['result_page'] = ($params['result_page']/$params['result_items_per_page']);
		
		//Filtros para buscar
		$_GET['ffecha1'] = $this->input->get('ffecha1')==''? date("Y-m-").'01': $this->input->get('ffecha1');
		$_GET['ffecha2'] = $this->input->get('ffecha2')==''? date("Y-m-d"): $this->input->get('ffecha2');
		$fecha = $_GET['ffecha1'] > $_GET['ffecha2']? $_GET['ffecha1']: $_GET['ffecha2'];
		$_GET['ftipo'] = (isset($_GET['ftipo']))?$_GET['ftipo']:'pp';

		$sql = $this->input->get('ftipo')=='pv'? " AND (Date('".$fecha."'::timestamp with time zone)-Date(f.fecha)) > f.plazo_credito": '';
		$sqlt = $this->input->get('ftipo')=='pv'? " AND (Date('".$fecha."'::timestamp with time zone)-Date(t.fecha)) > t.dias_credito": '';
		
		
		$query = BDUtil::pagination("
			SELECT 
				id_cliente,
				nombre_fiscal as nombre,
				SUM(total) as total,
				SUM(iva) as iva, 
				SUM(saldo) as saldo
			FROM 
				(
					SELECT 
						c.id_cliente,
						c.nombre_fiscal,
						Sum(f.total) AS total,
						Sum(f.importe_iva) AS iva, 
						COALESCE(Sum(f.total) - COALESCE(faa.abonos,0), 0) AS saldo
					FROM
						clientes AS c
						INNER JOIN facturacion AS f ON c.id_cliente = f.id_cliente
						LEFT JOIN (
							SELECT 
								f.id_cliente,
								Sum(fa.total) AS abonos
							FROM
								facturacion AS f INNER JOIN facturacion_abonos AS fa ON f.id_factura = fa.id_factura
							WHERE f.status <> 'ca'
								AND fa.tipo <> 'ca' AND Date(fa.fecha) <= '".$fecha."'".$sql."
							GROUP BY f.id_cliente
						) AS faa ON c.id_cliente = faa.id_cliente
					WHERE  f.status <> 'ca' AND Date(f.fecha) <= '".$fecha."'".$sql."
					GROUP BY c.id_cliente, c.nombre_fiscal, faa.abonos

					UNION ALL

					SELECT 
						c.id_cliente,
						c.nombre_fiscal,
						Sum(t.total) AS total,
						0 AS iva,
						COALESCE(Sum(t.total) - COALESCE(taa.abonos,0), 0) AS saldo
					FROM 
						clientes AS c
						INNER JOIN tickets AS t ON c.id_cliente = t.id_cliente
						LEFT JOIN (
							SELECT 
								t.id_cliente,
								Sum(ta.total) AS abonos
							FROM
								tickets AS t INNER JOIN tickets_abonos AS ta ON t.id_ticket = ta.id_ticket
							WHERE valida_ticket_fac(t.id_ticket)='t' AND t.status <> 'ca'
								AND ta.tipo <> 'ca' AND Date(ta.fecha) <='".$fecha."'".$sqlt."
							GROUP BY t.id_cliente
						) AS taa ON c.id_cliente = taa.id_cliente
					WHERE valida_ticket_fac(t.id_ticket)='t' AND t.status <> 'ca' AND Date(t.fecha) <=  '".$fecha."'".$sqlt."
					GROUP BY c.id_cliente, c.nombre_fiscal, taa.abonos

				) AS sal

			GROUP BY id_cliente, nombre_fiscal
			ORDER BY nombre_fiscal ASC
			", $params, true);
		$res = $this->db->query($query['query']);
		
		$response = array(
				'cuentas' => array(),
				'total_rows' 		=> $query['total_rows'],
				'items_per_page' 	=> $params['result_items_per_page'],
				'result_page' 		=> $params['result_page'],
				'ttotal' => 0
		);
		if($res->num_rows() > 0)
			$response['cuentas'] = $res->result();

		foreach ($query['resultset']->result() as $cliente) {
			$response['ttotal'] += $cliente->saldo;
		}
		
		return $response;
	}
	
	/**
	 * Descarga el listado de cuentas por pagar en formato pdf
	 */
	public function cuentasXCobrarPdf(){
		$this->load->library('mypdf');
		// Creación del objeto de la clase heredada
		$pdf = new MYpdf('P', 'mm', 'Letter');
		$pdf->titulo2 = 'Cuentas por cobrar';
		$pdf->titulo3 = 'Del: '.$this->input->get('ffecha1')." Al ".$this->input->get('ffecha2')."\n";
		$pdf->titulo3 .= ($this->input->get('ftipo') == 'pv'? 'Plazo vencido': 'Pendientes por cobrar');
		$pdf->AliasNbPages();
		//$pdf->AddPage();
		$pdf->SetFont('Arial','',8);
		
		$aligns = array('L', 'R');
		$widths = array(150, 55);
		$header = array('Cliente', 'Saldo');
		
		$res = $this->getCuentasXCobrarData(40);

		$total_saldo = 0;
		foreach($res['cuentas'] as $key => $item){
			$band_head = false;
			if($pdf->GetY() >= $pdf->limiteY || $key==0){ //salta de pagina si exede el max
				$pdf->AddPage();
				
				$pdf->SetFont('Arial','B',8);
				$pdf->SetTextColor(255,255,255);
				$pdf->SetFillColor(160,160,160);
				$pdf->SetX(6);
				$pdf->SetAligns($aligns);
				$pdf->SetWidths($widths);
				$pdf->Row($header, true);
			}
			
			$pdf->SetFont('Arial','',8);
			$pdf->SetTextColor(0,0,0);
			$datos = array($item->nombre, String::formatoNumero($item->saldo));
			$total_saldo += $item->saldo;
			
			$pdf->SetX(6);
			$pdf->SetAligns($aligns);
			$pdf->SetWidths($widths);
			$pdf->Row($datos, false);
		}
		
		$pdf->SetX(6);
		$pdf->SetFont('Arial','B',8);
		$pdf->SetTextColor(255,255,255);
		$pdf->Row(array('Total:', String::formatoNumero($total_saldo)), true);
		
		$pdf->Output('cuentas_x_cobrar.pdf', 'I');
	}
	
	
	/********** CUENTAS DE PROVEEDOR ************/
	/**
	 * Desglosa las facturas de un proveedor y su estado
	 */
	public function getCuentaClienteData(){
		$sql = '';
		
		//Filtros para buscar
		$_GET['ffecha1'] = $this->input->get('ffecha1')==''? date("Y-m-").'01': $this->input->get('ffecha1');
		$_GET['ffecha2'] = $this->input->get('ffecha2')==''? date("Y-m-d"): $this->input->get('ffecha2');
		$fecha1 = $fecha2 = '';
		if($_GET['ffecha1'] > $_GET['ffecha2']){
			$fecha2 = $_GET['ffecha1'];
			$fecha1 = $_GET['ffecha2'];
		}else{
			$fecha2 = $_GET['ffecha2'];
			$fecha1 = $_GET['ffecha1'];
		}
		
		$sql = $sqlt = $sql2 = '';
		if($this->input->get('ftipo')=='pv'){
			$sql = " AND (Date('".$fecha2."'::timestamp with time zone)-Date(f.fecha)) > f.plazo_credito";
			$sqlt = " AND (Date('".$fecha2."'::timestamp with time zone)-Date(t.fecha)) > t.dias_credito";
			$sql2 = 'WHERE saldo > 0';
		}
		
		/*** Saldo anterior ***/
		$saldo_anterior = $this->db->query("
			SELECT 
				id_cliente,
				Sum(total) AS total,
				Sum(iva) AS iva, 
				Sum(abonos) AS abonos, 
				Sum(saldo)::numeric(12, 2) AS saldo,
				tipo
			FROM 
				(
					SELECT 
						c.id_cliente,
						c.nombre_fiscal,
						Sum(f.total) AS total,
						Sum(f.importe_iva) AS iva, 
						COALESCE(Sum(faa.abonos),0) as abonos, 
						COALESCE(Sum(f.total) - COALESCE(Sum(faa.abonos),0), 0) AS saldo,
						'f' as tipo
					FROM
						clientes AS c
						INNER JOIN facturacion AS f ON c.id_cliente = f.id_cliente
						LEFT JOIN (
							SELECT 
								f.id_cliente,
								f.id_factura,
								Sum(fa.total) AS abonos
							FROM
								facturacion AS f INNER JOIN facturacion_abonos AS fa ON f.id_factura = fa.id_factura
							WHERE f.status <> 'ca'
								AND f.id_cliente = '".$_GET['id_cliente']."' 
								AND fa.tipo <> 'ca' AND Date(fa.fecha) <= '".$fecha2."'".$sql."
							GROUP BY f.id_cliente, f.id_factura
						) AS faa ON f.id_cliente = faa.id_cliente AND f.id_factura = faa.id_factura
					WHERE c.id_cliente = '".$_GET['id_cliente']."' AND f.status <> 'ca'
						AND Date(f.fecha) < '".$fecha1."'".$sql."
					GROUP BY c.id_cliente, c.nombre_fiscal, faa.abonos, tipo

					UNION ALL

					SELECT 
						c.id_cliente,
						c.nombre_fiscal,
						Sum(t.total) AS total,
						0 AS iva,
						COALESCE(Sum(taa.abonos), 0) as abonos,
						COALESCE(Sum(t.total) - COALESCE(Sum(taa.abonos),0), 0) AS saldo,
						't' as tipo
					FROM 
						clientes AS c
						INNER JOIN tickets AS t ON c.id_cliente = t.id_cliente
						LEFT JOIN (
							SELECT 
								t.id_cliente,
								t.id_ticket,
								Sum(ta.total) AS abonos
							FROM
								tickets AS t INNER JOIN tickets_abonos AS ta ON t.id_ticket = ta.id_ticket
							WHERE valida_ticket_fac(t.id_ticket)='t' 
								AND t.id_cliente = '".$_GET['id_cliente']."' 
								AND t.status <> 'ca'
								AND ta.tipo <> 'ca' AND Date(ta.fecha) <= '".$fecha2."'".$sqlt."
							GROUP BY t.id_cliente, t.id_ticket
						) AS taa ON c.id_cliente = taa.id_cliente AND t.id_ticket=taa.id_ticket
					WHERE c.id_cliente = '".$_GET['id_cliente']."' AND valida_ticket_fac(t.id_ticket)='t' 
								AND t.status <> 'ca' AND Date(t.fecha) < '".$fecha1."'".$sqlt."
					GROUP BY c.id_cliente, c.nombre_fiscal, taa.abonos, tipo

				) AS sal
			".$sql2."
			GROUP BY id_cliente, tipo
		");

		/*** Facturas y tickets en el rango de fechas ***/
		$res = $this->db->query("
			(SELECT
				f.id_factura, 
				f.serie, 
				f.folio, 
				Date(f.fecha) AS fecha, 
				COALESCE(f.total, 0) AS cargo,
				COALESCE(f.importe_iva, 0) AS iva, 
				COALESCE(ac.abono, 0) AS abono,
				(COALESCE(f.total, 0) - COALESCE(ac.abono, 0)) AS saldo,
				(CASE (COALESCE(f.total, 0) - COALESCE(ac.abono, 0)) WHEN 0 THEN 'Pagada' ELSE 'Pendiente' END) AS estado,
				Date(f.fecha + (f.plazo_credito || ' days')::interval) AS fecha_vencimiento, 
				(Date('".$fecha2."'::timestamp with time zone)-Date(f.fecha)) AS dias_transc,
				'Factura' AS concepto,
				'f' as tipo
			FROM
				facturacion AS f
				LEFT JOIN (
					SELECT 
						id_factura,
						Sum(total) AS abono
					FROM
						facturacion_abonos as fa
					WHERE tipo <> 'ca' AND Date(fecha) <= '".$fecha2."'
					GROUP BY id_factura
				) AS ac ON f.id_factura = ac.id_factura ".$sql."
			WHERE id_cliente = '".$_GET['id_cliente']."' 
				AND f.status <> 'ca'
				AND (Date(f.fecha) >= '".$fecha1."' AND Date(f.fecha) <= '".$fecha2."')
				".$sql.")

			UNION ALL

			(SELECT
				t.id_ticket as id_factura, 
				'' as serie, 
				t.folio, 
				Date(t.fecha) AS fecha, 
				COALESCE(t.total, 0) AS cargo,
				0 AS iva, 
				COALESCE(ac.abono, 0) AS abono,
				(COALESCE(t.total, 0) - COALESCE(ac.abono, 0)) AS saldo,
				(CASE (COALESCE(t.total, 0) - COALESCE(ac.abono, 0)) WHEN 0 THEN 'Pagada' ELSE 'Pendiente' END) AS estado,
				Date(t.fecha + (t.dias_credito || ' days')::interval) AS fecha_vencimiento, 
				(Date('".$fecha2."'::timestamp with time zone)-Date(t.fecha)) AS dias_transc,
				'Ticket' AS concepto,
				't' as tipo
			FROM
				tickets AS t
				LEFT JOIN (
					SELECT 
						id_ticket,
						Sum(total) AS abono
					FROM
						tickets_abonos as t
					WHERE tipo <> 'ca' AND Date(fecha) <= '".$fecha2."' AND valida_ticket_fac(id_ticket)='t'
					GROUP BY id_ticket
				) AS ac ON t.id_ticket = ac.id_ticket ".$sqlt."
			WHERE id_cliente = '".$_GET['id_cliente']."' 
				AND t.status <> 'ca'
				AND (Date(t.fecha) >= '".$fecha1."' AND Date(t.fecha) <= '".$fecha2."')".$sqlt."
				AND valida_ticket_fac(t.id_ticket)='t')

			ORDER BY fecha ASC
			");
		
		
		//obtenemos la info del proveedor
		$this->load->model('clientes_model');
		$prov = $this->clientes_model->getInfoCliente($_GET['id_cliente'], true);
		
		$response = array(
				'cuentas' 			=> array(),
				'anterior'			=> array(),
				'cliente' 		=> $prov['info'],
				'fecha1' 			=> $fecha1
		);
		if($res->num_rows() > 0)
			$response['cuentas'] = $res->result();

		if($saldo_anterior->num_rows() > 0){
			$response['anterior'] = $saldo_anterior->result();
			foreach ($response['anterior'] as $key => $c) {
				if ($key > 0){
					$response['anterior'][0]->total += $c->total;
					$response['anterior'][0]->abonos += $c->abonos;
					$response['anterior'][0]->saldo += $c->saldo;
				}
			}
		}

		return $response;
	}
	
	/**
	 * Descarga el listado de cuentas por pagar en formato pdf
	 */
	public function cuentaClientePdf(){
		$res = $this->getCuentaClienteData();
		
		if (count($res['anterior']) > 0)
			$res['anterior'] = $res['anterior'][0];

		$this->load->library('mypdf');
		// Creación del objeto de la clase heredada
		$pdf = new MYpdf('L', 'mm', 'Letter');
		$pdf->titulo2 = 'Cuenta de '.$res['cliente']->nombre_fiscal;
		$pdf->titulo3 = 'Del: '.$this->input->get('ffecha1')." Al ".$this->input->get('ffecha2')."\n";
		$pdf->titulo3 .= ($this->input->get('ftipo') == 'pv'? 'Plazo vencido': 'Pendientes por cobrar');
		$pdf->AliasNbPages();
		//$pdf->AddPage();
		$pdf->SetFont('Arial','',8);
	
		$aligns = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
		$widths = array(20, 11, 20, 70, 30, 30, 30, 18, 20, 15);
		$header = array('Fecha', 'Serie', 'Folio', 'Concepto', 'Cargo', 'Abono', 'Saldo', 'Estado', 'F. Ven.', 'D. Trans.');
		
		$total_cargo = 0;
		$total_abono = 0;
		$total_saldo = 0;
		
		$bad_saldo_ante = true;
		if(isset($res['anterior']->saldo)){ //se suma a los totales del saldo anterior
			$total_cargo += $res['anterior']->total;
			$total_abono += $res['anterior']->abonos;
			$total_saldo += $res['anterior']->saldo;
		}else{
			$res['anterior'] = new stdClass();
			$res['anterior']->total = 0;
			$res['anterior']->abonos = 0;
			$res['anterior']->saldo = 0;
		}
		$res['anterior']->concepto = 'Saldo anterior a '.$res['fecha1'];
		
		foreach($res['cuentas'] as $key => $item){
			$band_head = false;
			if($pdf->GetY() >= $pdf->limiteY || $key==0){ //salta de pagina si exede el max
				$pdf->AddPage();
	
				$pdf->SetFont('Arial','B',8);
				$pdf->SetTextColor(255,255,255);
				$pdf->SetFillColor(160,160,160);
				$pdf->SetX(6);
				$pdf->SetAligns($aligns);
				$pdf->SetWidths($widths);
				$pdf->Row($header, true);
			}
			
			$pdf->SetFont('Arial','',8);
			$pdf->SetTextColor(0,0,0);
			if($bad_saldo_ante){
				$pdf->SetX(6);
				$pdf->SetAligns($aligns);
				$pdf->SetWidths($widths);
				$pdf->Row(array('', '', '', $res['anterior']->concepto, 
					String::formatoNumero($res['anterior']->total), 
					String::formatoNumero($res['anterior']->abonos), 
					String::formatoNumero($res['anterior']->saldo), 
					'', '', ''), false);
				$bad_saldo_ante = false;
			}
			
			$datos = array($item->fecha, $item->serie, $item->folio, 
					$item->concepto, String::formatoNumero($item->cargo), String::formatoNumero($item->abono), 
					String::formatoNumero($item->saldo), $item->estado, $item->fecha_vencimiento, 
					$item->dias_transc);
			
			$total_cargo += $item->cargo;
			$total_abono += $item->abono;
			$total_saldo += $item->saldo;
				
			$pdf->SetX(6);
			$pdf->SetAligns($aligns);
			$pdf->SetWidths($widths);
			$pdf->Row($datos, false);
		}
	
		$pdf->SetX(6);
		$pdf->SetFont('Arial','B',8);
		$pdf->SetTextColor(255,255,255);
		$pdf->SetWidths(array(121, 30, 30, 30));
		$pdf->Row(array('Totales:', 
				String::formatoNumero($total_cargo),
				String::formatoNumero($total_abono),
				String::formatoNumero($total_saldo)), true);
	
		$pdf->Output('cuentas_proveedor.pdf', 'I');
	}
	
	public function cuentaClienteExcel(){
		$res = $this->getCuentaClienteData();
		
		if (count($res['anterior']) > 0)
			$res['anterior'] = $res['anterior'][0];
		
		$this->load->library('myexcel');
		$xls = new myexcel();
	
		$worksheet =& $xls->workbook->addWorksheet();
	
		$xls->titulo2 = 'Cuenta de '.$res['cliente']->nombre_fiscal;
		$xls->titulo3 = 'Del: '.$this->input->get('ffecha1')." Al ".$this->input->get('ffecha2')."\n";
		$xls->titulo4 = ($this->input->get('ftipo') == 'pv'? 'Plazo vencido': 'Pendientes por cobrar');
		
		if(is_array($res['anterior'])){
			$res['anterior'] = new stdClass();
			$res['anterior']->cargo = 0;
			$res['anterior']->abono = 0;
			$res['anterior']->saldo = 0;
		}else{
			$res['anterior']->cargo = $res['anterior']->total;
			$res['anterior']->abono = $res['anterior']->abonos;
		}
		$res['anterior']->fecha = $res['anterior']->serie = $res['anterior']->folio = '';
		$res['anterior']->concepto = $res['anterior']->estado = $res['anterior']->fecha_vencimiento = '';
		$res['anterior']->dias_transc = '';
		
		array_unshift($res['cuentas'], $res['anterior']);
		
		$data_fac = $res['cuentas'];
			
		$row=0;
		//Header
		$xls->excelHead($worksheet, $row, 8, array(
				array($xls->titulo2, 'format_title2'), 
				array($xls->titulo3, 'format_title3'),
				array($xls->titulo4, 'format_title3')
		));
			
		$row +=3;
		$xls->excelContent($worksheet, $row, $data_fac, array(
				'head' => array('Fecha', 'Serie', 'Folio', 'Concepto', 'Cargo', 'Abono', 'Saldo', 'Estado', 'Fecha Vencimiento', 'Dias Trans.'),
				'conte' => array(
						array('name' => 'fecha', 'format' => 'format4', 'sum' => -1),
						array('name' => 'serie', 'format' => 'format4', 'sum' => -1),
						array('name' => 'folio', 'format' => 'format4', 'sum' => -1),
						array('name' => 'concepto', 'format' => 'format4', 'sum' => -1),
						array('name' => 'cargo', 'format' => 'format4', 'sum' => 0),
						array('name' => 'abono', 'format' => 'format4', 'sum' => 0),
						array('name' => 'saldo', 'format' => 'format4', 'sum' => 0),
						array('name' => 'estado', 'format' => 'format4', 'sum' => -1),
						array('name' => 'fecha_vencimiento', 'format' => 'format4', 'sum' => -1),
						array('name' => 'dias_transc', 'format' => 'format4', 'sum' => -1))
		));
	
		$xls->workbook->send('cuentaProveedor.xls');
		$xls->workbook->close();
	}
	
	
	/********** DETALLE DE FACTURAS ************/
	/**
	 * Desglosa los abonos que se realizaron a una factura
	 */
	public function getDetalleTicketFacturaData(){
		$sql = '';
	
		//Filtros para buscar
		$_GET['ffecha1'] = $this->input->get('ffecha1')==''? date("Y-m-").'01': $this->input->get('ffecha1');
		$_GET['ffecha2'] = $this->input->get('ffecha2')==''? date("Y-m-d"): $this->input->get('ffecha2');
		$fecha1 = $fecha2 = '';
		if($_GET['ffecha1'] > $_GET['ffecha2']){
			$fecha2 = $_GET['ffecha1'];
			$fecha1 = $_GET['ffecha2'];
		}else{
			$fecha2 = $_GET['ffecha2'];
			$fecha1 = $_GET['ffecha1'];
		}
	
		$sql = $sql2 = '';
		if($this->input->get('ftipo')=='pv'){
			$sql = " AND (Date('".$fecha2."'::timestamp with time zone)-Date(c.fecha)) > c.plazo_credito";
			$sql2 = 'WHERE saldo > 0';
		}
	

		if ($_GET['tipo'] == 'f')
		{
			$data['info'] = $this->db->query(
											"SELECT DATE(fecha) as fecha, serie, folio, condicion_pago, status, total,
												plazo_credito
												FROM facturacion
												WHERE id_factura='".$_GET['id']."'")->result();
			$sql = array('tabla' => 'facturacion_abonos', 
										'where_field' => 'id_factura');
		}
		else
		{
			$data['info'] = $this->db->query(
											"SELECT fecha, '' as serie, folio, 	
												tipo_pago as condicion_pago,
												status, total, 
												dias_credito as plazo_credito
											FROM tickets
											WHERE id_ticket='".$_GET['id']."'")->result();
			$sql = array('tabla' => 'tickets_abonos', 
										'where_field' => 'id_ticket');
		}

			//Obtenemos los abonos de la factura o ticket
			$res = $this->db->query("
				SELECT
					id_abono, 
					Date(fecha) AS fecha, 
					total AS abono, 
					concepto,
					tipo
				FROM ".$sql['tabla']."
				WHERE ".$sql['where_field']." = '".$_GET['id']."' AND tipo <> 'ca' 
					AND Date(fecha) <= '".$fecha2."' 
				ORDER BY fecha ASC
			");	
	
		//obtenemos la info del proveedor
		$this->load->model('clientes_model');
		$prov = $this->clientes_model->getInfoCliente($_GET['id_cliente'], true);
	
		$response = array(
				'abonos' 			=> array(),
				'cobro'					=> $data['info'],
				'cliente' 		=> $prov['info'],
				'fecha1' 			=> $fecha1
		);
		if($res->num_rows() > 0)
			$response['abonos'] = $res->result();
	
		return $response;
	}
	
	/**
	 * Descarga el listado de cuentas por pagar en formato pdf
	 */
	public function detalleTicketFacturaPdf(){
		$res = $this->getDetalleTicketFacturaData();
	
		$this->load->library('mypdf');
		// Creación del objeto de la clase heredada
		$pdf = new MYpdf('P', 'mm', 'Letter');
		$pdf->titulo2 = 'Detalle de factura '.$res['cobro'][0]->serie.'-'.$res['cobro'][0]->folio.' ('.$res['cobro'][0]->fecha.')';
		$pdf->titulo3 = $res['cliente']->nombre_fiscal."\n";
		$pdf->titulo3 .= 'Del: '.$this->input->get('ffecha1')." Al ".$this->input->get('ffecha2');
		$pdf->AliasNbPages();
		//$pdf->AddPage();
		$pdf->SetFont('Arial','',8);
	
		$aligns = array('C', 'C', 'C', 'C');
		$widths = array(25, 96, 40, 40);
		$header = array('Fecha', 'Concepto', 'Abono', 'Saldo');
	
		$total_abono = 0;
		$total_saldo = $res['cobro'][0]->total;
	
		$bad_cargot = true;
	
		foreach($res['abonos'] as $key => $item){
			$total_abono += $item->abono;
			$total_saldo -= $item->abono;
			
			if($pdf->GetY() >= $pdf->limiteY || $key==0){ //salta de pagina si exede el max
				$pdf->AddPage();
	
				$pdf->SetFont('Arial','B',8);
				$pdf->SetTextColor(255,255,255);
				$pdf->SetFillColor(160,160,160);
				$pdf->SetX(6);
				if($bad_cargot){
					$pdf->SetX(6);
					$pdf->SetAligns(array('R'));
					$pdf->SetWidths(array(201));
					$pdf->Row(array('Total: '.String::formatoNumero($res['cobro'][0]->total)), true);
					$bad_cargot = false;
				}
				
				$pdf->SetX(6);
				$pdf->SetAligns($aligns);
				$pdf->SetWidths($widths);
				$pdf->Row($header, true);
			}
				
			$pdf->SetFont('Arial','',8);
			$pdf->SetTextColor(0,0,0);	
			$datos = array($item->fecha, $item->concepto, 
					String::formatoNumero($item->abono), String::formatoNumero($total_saldo));
	
			$pdf->SetX(6);
			$pdf->SetAligns($aligns);
			$pdf->SetWidths($widths);
			$pdf->Row($datos, false);
		}
	
		$pdf->SetX(6);
		$pdf->SetFont('Arial','B',8);
		$pdf->SetTextColor(255,255,255);
		$pdf->SetWidths(array(121, 40, 40));
		$pdf->Row(array('Totales:',
				String::formatoNumero($total_abono),
				String::formatoNumero($total_saldo)), true);
	
		$pdf->Output('detalle.pdf', 'I');
	}
	
}