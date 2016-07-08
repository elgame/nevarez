<?php
class cuentas_pagar_model extends CI_Model{
	
	function __construct(){
		parent::__construct();
	}
	
	/********** CUENTAS POR PAGAR ************/
	/**
	 * Obtiene el listado de proveedores y el saldo que se les debe
	 */
	public function getCuentasXPagarData($per_pag='9999'){
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
		$sql = $this->input->get('ftipo')=='pv'? " AND (Date('".$fecha."'::timestamp with time zone)-Date(c.fecha)) > c.plazo_credito": '';
		
		$query = BDUtil::pagination("
			SELECT 
				id_proveedor,
				nombre,
				total,
				iva, 
				saldo
			FROM 
				(
					SELECT 
						p.id_proveedor,
						p.nombre,
						Sum(c.total) AS total,
						Sum(c.importe_iva) AS iva, 
						COALESCE(Sum(c.total) - COALESCE(caa.abonos,0), 0) AS saldo
					FROM
						proveedores AS p 
						INNER JOIN compras AS c ON p.id_proveedor = c.id_proveedor
						LEFT JOIN (
							SELECT 
								c.id_proveedor,
								Sum(ca.total) AS abonos
							FROM
								compras AS c INNER JOIN compras_abonos AS ca ON c.id_compra = ca.id_compra
							WHERE c.status <> 'ca' AND c.status <> 'n' 
								AND ca.tipo <> 'ca' AND Date(ca.fecha) <= '".$fecha."'".$sql."
							GROUP BY c.id_proveedor
						) AS caa ON p.id_proveedor = caa.id_proveedor
					WHERE  p.tipo <> 'pi' AND c.status <> 'ca' AND c.status <> 'n' AND Date(c.fecha) <= '".$fecha."'".$sql."
					GROUP BY p.id_proveedor, p.nombre, caa.abonos
				) AS sal
			ORDER BY nombre ASC
			", $params, true);
		$res = $this->db->query($query['query']);
		
		$response = array(
				'cuentas' => array(),
				'total_rows' 		=> $query['total_rows'],
				'items_per_page' 	=> $params['result_items_per_page'],
				'result_page' 		=> $params['result_page']
		);
		if($res->num_rows() > 0)
			$response['cuentas'] = $res->result();
		
		return $response;
	}
	
	/**
	 * Descarga el listado de cuentas por pagar en formato pdf
	 */
	public function cuentasXPagarPdf(){
		$this->load->library('mypdf');
		// Creación del objeto de la clase heredada
		$pdf = new MYpdf('P', 'mm', 'Letter');
		$pdf->titulo2 = 'Cuentas por pagar';
		$pdf->titulo3 = 'Del: '.$this->input->get('ffecha1')." Al ".$this->input->get('ffecha2')."\n";
		$pdf->titulo3 .= ($this->input->get('ftipo') == 'pv'? 'Plazo vencido': 'Pendientes por pagar');
		$pdf->AliasNbPages();
		//$pdf->AddPage();
		$pdf->SetFont('Arial','',8);
		
		$aligns = array('L', 'R');
		$widths = array(150, 55);
		$header = array('Proveedor', 'Saldo');
		
		$res = $this->getCuentasXPagarData();
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
		
		$pdf->Output('cuentas_x_pagar.pdf', 'I');
	}
	
	
	/********** CUENTAS DE PROVEEDOR ************/
	/**
	 * Desglosa las facturas de un proveedor y su estado
	 */
	public function getCuentaProveedorData(){
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
		
		/*** Saldo anterior ***/
		$saldo_anterior = $this->db->query("
			SELECT 
				id_proveedor,
				Sum(total) AS total,
				Sum(iva) AS iva, 
				Sum(abonos) AS abonos, 
				Sum(saldo)::numeric(12, 2) AS saldo
			FROM 
				(
					SELECT 
						p.id_proveedor,
						p.nombre,
						Sum(c.total) AS total,
						Sum(c.importe_iva) AS iva, 
						caa.abonos, 
						COALESCE(Sum(c.total) - COALESCE(caa.abonos,0), 0) AS saldo
					FROM
						proveedores AS p 
						INNER JOIN compras AS c ON p.id_proveedor = c.id_proveedor
						LEFT JOIN (
							SELECT 
								c.id_proveedor,
								c.id_compra,
								Sum(ca.total) AS abonos
							FROM
								compras AS c INNER JOIN compras_abonos AS ca ON c.id_compra = ca.id_compra
							WHERE c.status <> 'ca' AND c.status <> 'n' 
								AND c.id_proveedor = '".$_GET['id_proveedor']."' 
								AND ca.tipo <> 'ca' AND Date(ca.fecha) <= '".$fecha2."'".$sql."
							GROUP BY c.id_proveedor, c.id_compra
						) AS caa ON p.id_proveedor = caa.id_proveedor AND c.id_compra = caa.id_compra
					WHERE p.id_proveedor = '".$_GET['id_proveedor']."' AND c.status <> 'ca' AND c.status <> 'n' 
						AND Date(c.fecha) < '".$fecha1."'".$sql."
					GROUP BY p.id_proveedor, p.nombre, caa.abonos
				) AS sal
			".$sql2."
			GROUP BY id_proveedor
		");
		
		
		/*** Facturas en el rango de fechas ***/
		$res = $this->db->query("
			SELECT
				c.id_compra, 
				c.serie, 
				c.folio, 
				Date(c.fecha) AS fecha, 
				COALESCE(c.total, 0) AS cargo,
				COALESCE(c.importe_iva, 0) AS iva, 
				COALESCE(ac.abono, 0) AS abono,
				(COALESCE(c.total, 0) - COALESCE(ac.abono, 0)) AS saldo,
				(CASE (COALESCE(c.total, 0) - COALESCE(ac.abono, 0)) WHEN 0 THEN 'Pagada' ELSE 'Pendiente' END) AS estado,
				Date(c.fecha + (c.plazo_credito || ' days')::interval) AS fecha_vencimiento, 
				(Date('".$fecha2."'::timestamp with time zone)-Date(c.fecha)) AS dias_transc,
				c.concepto AS concepto
			FROM
				compras AS c
				LEFT JOIN (
					SELECT 
						id_compra,
						Sum(total) AS abono
					FROM
						compras_abonos
					WHERE tipo <> 'ca' AND Date(fecha) <= '".$fecha2."'
					GROUP BY id_compra
				) AS ac ON c.id_compra = ac.id_compra
			WHERE id_proveedor = '".$_GET['id_proveedor']."' 
				AND c.status <> 'ca' AND c.status <> 'n' 
				AND (Date(c.fecha) >= '".$fecha1."' AND Date(c.fecha) <= '".$fecha2."')
				".$sql."
			ORDER BY c.fecha ASC
			");
		
		
		//obtenemos la info del proveedor
		$this->load->model('proveedores_model');
		$prov = $this->proveedores_model->getInfoProveedor($_GET['id_proveedor'], true);
		
		$response = array(
				'cuentas' 			=> array(),
				'anterior'			=> array(),
				'proveedor' 		=> $prov['info'],
				'fecha1' 			=> $fecha1
		);
		if($res->num_rows() > 0)
			$response['cuentas'] = $res->result();
		if($saldo_anterior->num_rows() > 0)
			$response['anterior'] = $saldo_anterior->row();
		
		return $response;
	}
	
	/**
	 * Descarga el listado de cuentas por pagar en formato pdf
	 */
	public function cuentaProveedorPdf(){
		$res = $this->getCuentaProveedorData();
		
		$this->load->library('mypdf');
		// Creación del objeto de la clase heredada
		$pdf = new MYpdf('L', 'mm', 'Letter');
		$pdf->titulo2 = 'Cuenta de '.$res['proveedor']->nombre;
		$pdf->titulo3 = 'Del: '.$this->input->get('ffecha1')." Al ".$this->input->get('ffecha2')."\n";
		$pdf->titulo3 .= ($this->input->get('ftipo') == 'pv'? 'Plazo vencido': 'Pendientes por pagar');
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
	
	public function cuentaProveedorExcel(){
		$res = $this->getCuentaProveedorData();
		
		$this->load->library('myexcel');
		$xls = new myexcel();
	
		$worksheet =& $xls->workbook->addWorksheet();
	
		$xls->titulo2 = 'Cuenta de '.$res['proveedor']->nombre;
		$xls->titulo3 = 'Del: '.$this->input->get('ffecha1')." Al ".$this->input->get('ffecha2')."\n";
		$xls->titulo4 = ($this->input->get('ftipo') == 'pv'? 'Plazo vencido': 'Pendientes por pagar');
		
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
	public function getDetalleFacturaData(){
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
	
		//Obtenemos la info de una compra
		$this->load->model('compras_model');
		$compra = $this->compras_model->getInfoCompra($_GET['id_compra'], true);
	
	
		//Obtenemos los abonos
		$res = $this->db->query("
			SELECT
				id_abono, 
				Date(fecha) AS fecha, 
				total AS abono, 
				concepto,
				tipo
			FROM compras_abonos
			WHERE id_compra = '".$_GET['id_compra']."' AND tipo <> 'ca' 
				AND Date(fecha) <= '".$fecha2."' 
			ORDER BY fecha ASC
		");
	
	
		//obtenemos la info del proveedor
		$this->load->model('proveedores_model');
		$prov = $this->proveedores_model->getInfoProveedor($_GET['id_proveedor'], true);
	
		$response = array(
				'cuentas' 			=> array(),
				'compra'			=> $compra['info'],
				'proveedor' 		=> $prov['info'],
				'fecha1' 			=> $fecha1
		);
		if($res->num_rows() > 0)
			$response['cuentas'] = $res->result();
	
		return $response;
	}
	
	/**
	 * Descarga el listado de cuentas por pagar en formato pdf
	 */
	public function detalleFacturaPdf(){
		$res = $this->getDetalleFacturaData();
	
		$this->load->library('mypdf');
		// Creación del objeto de la clase heredada
		$pdf = new MYpdf('P', 'mm', 'Letter');
		$pdf->titulo2 = 'Detalle de factura '.$res['compra']->serie.'-'.$res['compra']->folio.' ('.$res['compra']->fecha.')';
		$pdf->titulo3 = $res['proveedor']->nombre."\n";
		$pdf->titulo3 .= 'Del: '.$this->input->get('ffecha1')." Al ".$this->input->get('ffecha2');
		$pdf->AliasNbPages();
		//$pdf->AddPage();
		$pdf->SetFont('Arial','',8);
	
		$aligns = array('C', 'C', 'C', 'C');
		$widths = array(25, 96, 40, 40);
		$header = array('Fecha', 'Concepto', 'Abono', 'Saldo');
	
		$total_abono = 0;
		$total_saldo = $res['compra']->total;
	
		$bad_cargot = true;
	
		foreach($res['cuentas'] as $key => $item){
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
					$pdf->Row(array('Total: '.String::formatoNumero($res['compra']->total)), true);
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
	
		$pdf->Output('detalle_factura.pdf', 'I');
	}
	
}