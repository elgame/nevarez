<?php
class inventario_model extends CI_Model{
	
	function __construct(){
		parent::__construct();
	}
	
	/**
	 * Nivelar inventario
	 */
	public function nivelar(){
		$compras = array();
		$ordenest = array();
		$total_compra = 0;
		$total_ordent = 0;
		
		foreach($_POST['id_producto'] as $key => $prod){
			if($_POST['diferie'][$key] != '' && $_POST['diferie'][$key] != 0){
				$cantidad = abs($_POST['diferie'][$key]);
				if($_POST['diferie'][$key] > 0){ //se agrega una compra
					$compras[] = array(
							'id_compra' 	=> '',
							'id_producto' 	=> $prod,
							'cantidad' 		=> $cantidad,
							'precio_unitario' => $_POST['precio_u'][$key],
							'importe' 		=> ($cantidad * $_POST['precio_u'][$key]),
							'total' 		=> ($cantidad * $_POST['precio_u'][$key])
					);
					$total_compra += ($cantidad * $_POST['precio_u'][$key]);
				}else{ //se agrega una venta
					$ordenest[] = array(
						'id_salida' 	=> '',
						'id_producto' 	=> $prod,
						'cantidad' 		=> $cantidad,
						'precio_unitario' => $_POST['precio_u'][$key],
						'importe' 		=> ($cantidad * $_POST['precio_u'][$key]),
						'total' 		=> ($cantidad * $_POST['precio_u'][$key])
					);
					$total_ordent += ($cantidad * $_POST['precio_u'][$key]);
				}
			}
		}
		
		//Agregamos una compra para nivelar
		if(count($compras) > 0){
			$id_compra = BDUtil::getId();
			$compra = array(
				'id_compra' => $id_compra,
				'id_proveedor' => '1',
				'id_empleado' => $_SESSION['id_empleado'],
				'subtotal' => $total_compra,
				'total' => $total_compra,
				'status' => 'n'
			);
			$this->db->insert('compras', $compra);
			
			//productos de la compra
			foreach($compras as $key => $co)
				$compras[$key]['id_compra'] = $id_compra;
			$this->db->insert_batch('compras_productos', $compras);//compras_productos_inv
		}
		
		//Agregamos una orden t para nivelar
		if(count($ordenest) > 0){
			$id_ordent = BDUtil::getId();
			$ordent = array(
				'id_salida' => $id_ordent,
// 				'id_cliente' => '1',
// 				'id_vendedor' => $_SESSION['id_empleado'],
				'id_usuario' => $_SESSION['id_empleado'],
// 				'subtotal' => $total_ordent,
// 				'total' => $total_ordent,
				'folio' => 0,
				'status' => 'n'
			);
			$this->db->insert('salidas', $ordent);
				
			//productos de la orden t
			foreach($ordenest as $key => $co)
				$ordenest[$key]['id_salida'] = $id_ordent;
			$this->db->insert_batch('salidas_productos', $ordenest);
		}
	}
	
	
	/**
	 * ************** REPORTE UEPS ************** *
	 * Genera el reporte ueps
	 * @param unknown_type $bycodigo
	 * @param unknown_type $producto
	 * @param unknown_type $fecha_inicio
	 * @param unknown_type $fecha_final
	 * @param unknown_type $pdf
	 * @param unknown_type $returnTotales
	 * @param unknown_type $buscaAnterior
	 */
	public function ueps($bycodigo,$producto,$fecha_inicio = '', $fecha_final = '', $pdf = true, $returnTotales = false, $buscaAnterior = true){
		$data_valoresanteriores = array();
		if($fecha_inicio!='' && $buscaAnterior==true){
			$feche_anterior = String::suma_fechas($fecha_inicio,-1);
			$data_valoresanteriores = $this->ueps($bycodigo, $producto, '', $feche_anterior, false,true,true);
			$data_valoresanteriores['fecha'] = 'Anterior';
		}
	
		$labelFechas = '';
		if($fecha_inicio!='' && $fecha_final!='')
			$labelFechas = "Desde la fecha $fecha_inicio hasta $fecha_final";
		elseif($fecha_inicio!="")
			$labelFechas = "Desde la fecha $fecha_inicio";
		elseif($fecha_final!='')
			$labelFechas = "Hasta la fecha $fecha_final";
			
		$fecha_inicio = ($fecha_inicio!='')? strtotime($fecha_inicio) : 0;
		$fecha_final = ($fecha_final!='')? strtotime($fecha_final) : 0;
	
		$condicion = ($bycodigo==false)? " AND p.id_producto = '".$producto."' " : " AND LOWER(p.codigo)=LOWER('".$producto."') ";
	
		$resP = $this->db->query("SELECT nombre FROM productos AS p WHERE status='ac' ".$condicion);
		$dato = $resP->row();
		$nombre_producto = isset($dato->nombre)? $dato->nombre: '';
	
		$resC = $this->db->query("
			SELECT co.id_compra, DATE(co.fecha) AS fecha, 
				p.id_producto, p.nombre, pu.abreviatura, '' AS abreviaturas, 
				cp.cantidad, cp.cantidad AS cantidad_disponible, cp.precio_unitario,
				cp.importe, '' AS cantidads, '' AS cantidad_disponibles, '' AS precio_unitarios, '' AS importes 
			FROM compras AS co 
				INNER JOIN compras_productos AS cp ON co.id_compra = cp.id_compra 
				INNER JOIN productos AS p ON cp.id_producto = p.id_producto 
				INNER JOIN productos_unidades AS pu ON p.id_unidad = pu.id_unidad 
			WHERE co.status IN ('p','pa','n') ".$condicion." ORDER BY fecha ASC,co.id_compra ASC");
		$resC = $resC->result_array();
		
		$resS = $this->db->query("
			SELECT DATE(s.fecha) AS fecha, p.id_producto, p.nombre, '' AS abreviatura, 
				pu.abreviatura AS abreviaturas, '' AS cantidad, '' AS cantidad_disponible, 
				'' AS precio_unitario, '' AS importe, sp.cantidad AS cantidads, 
				sp.cantidad AS cantidad_disponibles
			FROM salidas AS s
				INNER JOIN salidas_productos AS sp ON s.id_salida = sp.id_salida
				INNER JOIN productos AS p ON sp.id_producto = p.id_producto
				INNER JOIN productos_unidades AS pu ON p.id_unidad = pu.id_unidad
			WHERE s.status IN ('sa','ba','n') AND sp.status='t'".$condicion." ORDER BY s.fecha ASC");//IN ('p','pa','n') 
		$resS = $resS->result_array();
		
		/*//Quita las notas de credito a las compras
		$fechaNC = $fecha_final==0? date("Y-m-d"): ($returnTotales==false? date("Y-m-d"): date("Y-m-d", $fecha_final));
		foreach($resC as $key => $compra){
			$notas_credito = $this->dao->selectRows("Date(ca.fecha) AS fecha, cnp.id_orden, cnp.id_producto, Sum(cnp.cantidad) AS cantidad,
					cnp.precio_unitario",
					"compras_abonos AS ca INNER JOIN compras_nc_productos AS cnp ON ca.id_abono=cnp.id_abono",
					"WHERE cnp.id_orden = ".$compra['id_orden']." AND cnp.id_producto = ".$compra['id_producto']."
					AND Date(ca.fecha) <= '".date("Y-m-d")."'
					GROUP BY ca.fecha, cnp.id_orden, cnp.id_producto,cnp.precio_unitario", true);
			if(count($notas_credito) > 0){
				$resC[$key]['cantidad'] -= $notas_credito[0]['cantidad'];
				$resC[$key]['cantidad_disponible'] -= $notas_credito[0]['cantidad'];
				$resC[$key]['importe'] -= ($notas_credito[0]['cantidad']*$notas_credito[0]['precio_unitario']);
			}
		}*/
	
		$unidades = 0;
		$valor = 0;
		$nCompras = count($resC);
		$nVentas = count($resS);
		$posVenta = 0;
		$data = array();
	
		$totalUnidadesCompradas = 0;
		$totalCosto = 0;
		$totalUnidadesVendidas = 0;
		$totalVentas = 0;
		$abreviatura = '';
	
		for($i=0; $i<$nCompras; ++$i){
			$fechaCompra = strtotime($resC[$i]['fecha']);
	
			$unidades += floatval($resC[$i]['cantidad']);
			$valor += floatval($resC[$i]['importe']);
			$resC[$i]['unidades'] = $unidades;
			$resC[$i]['valor'] = $valor;
			$abreviatura = $resC[$i]['abreviatura'];
			if($fechaCompra>=$fecha_inicio && ($fecha_final==0 || $fechaCompra<=$fecha_final)){
				$data[] = $resC[$i];
			}
			if($fecha_final==0 || $fechaCompra<=$fecha_final){
				$totalUnidadesCompradas += floatval($resC[$i]['cantidad']);
				$totalCosto += floatval($resC[$i]['importe']);
			}
	
			$posAux = $i;
			$nexFechaCompra = isset($resC[$i+1]['fecha'])? strtotime($resC[$i+1]['fecha']) : 0;
			while($posVenta<$nVentas && ($nexFechaCompra == 0 || strtotime($resS[$posVenta]['fecha'])<$nexFechaCompra)){
				$fechaVenta = strtotime($resS[$posVenta]['fecha']);
				$cantidad_salida = 0;
				$precio_salida = 0;
				$bandera = true;
				while($bandera == true){//OBTENGO LA ULTIMA COMPRA CON CANTIDAD DISPONIBLE
					$cantidad_salida = floatval($resS[$posVenta]['cantidads']);
					$disponible = floatval((isset($resC[$posAux]['cantidad_disponible'])? $resC[$posAux]['cantidad_disponible']: 0));
					if($disponible>=$cantidad_salida){
						$resC[$posAux]['cantidad_disponible'] = $disponible-$cantidad_salida;
						$precio_salida = floatval($resC[$posAux]['precio_unitario']);
						$bandera = false;
					}elseif($disponible>0){
						$itemAux = $resS[$posVenta];
						$resS[$posVenta]['cantidads'] = $cantidad_salida - $disponible;
						$precio_salida = floatval($resC[$posAux]['precio_unitario']);
						$cantidad_salida = $disponible;
							
						$importe_salida = ($cantidad_salida * $precio_salida);
						$unidades -= $cantidad_salida;
						$valor -= $importe_salida;
	
						$itemAux['cantidads'] = $cantidad_salida;
						$itemAux['precio_unitarios'] = $precio_salida;
						$itemAux['importes'] = $importe_salida;
						$itemAux['unidades'] = $unidades;
						$itemAux['valor'] = $valor;
	
						if($fechaVenta>=$fecha_inicio && ($fecha_final==0 || $fechaVenta<=$fecha_final))
							$data[] = $itemAux;
	
						if($fecha_final==0 || $fechaVenta<=$fecha_final){
							$totalUnidadesVendidas += $cantidad_salida;
							$totalVentas += $importe_salida;
						}
	
						$posAux -= 1;
					}else{
						$posAux -= 1;
						$bandera = ($posAux<0)? false : true;
					}
				}
	
				$importe_salida = ($cantidad_salida * $precio_salida);
				$unidades -= $cantidad_salida;
				$valor -= $importe_salida;
	
				$resS[$posVenta]['precio_unitarios'] = $precio_salida;
				$resS[$posVenta]['importes'] = $importe_salida;
				$resS[$posVenta]['unidades'] = $unidades;
				$resS[$posVenta]['valor'] = $valor;
	
				if($fechaVenta>=$fecha_inicio && ($fecha_final==0 || $fechaVenta<=$fecha_final))
					$data[] = $resS[$posVenta];
	
				if($fecha_final==0 || $fechaVenta<=$fecha_final){
					$totalUnidadesVendidas += $cantidad_salida;
					$totalVentas += $importe_salida;
				}
					
				$posVenta += 1;
			}
		}
	
		$itemAux = array();
		$itemAux['fecha'] = 'Totales';
		$itemAux['cantidad'] = $totalUnidadesCompradas.' '.$abreviatura;
		$itemAux['importe'] = $totalCosto;
		$itemAux['cantidads'] = $totalUnidadesVendidas.' '.$abreviatura;
		$itemAux['importes'] = $totalVentas;
		$itemAux['unidades'] = ($totalUnidadesCompradas - $totalUnidadesVendidas).' '.$abreviatura;
		$itemAux['valor'] = $totalCosto - $totalVentas;
		$data[] = $itemAux;
	
		if($pdf == true){
			$this->pdfUeps($nombre_producto, $labelFechas, $data, $data_valoresanteriores);
		}elseif($returnTotales==true){
			return $itemAux;
		}else{
			//$data[] = $data_valoresanteriores;
			return $data;
		}
	}
	
	public function pdfUeps($nombre_producto, $fechas, $data, $data_valoresanteriores, $view = 'V'){
			
		$this->load->library('mypdf');
		// Creación del objeto de la clase heredada
		$pdf = new MYpdf('L', 'mm', 'Letter');
		$pdf->show_head = true;
		$pdf->titulo2 = 'Reporte de inventario UEPS';
		$pdf->titulo3 = $fechas."\n";
		$pdf->titulo3 .=  $nombre_producto;
		$pdf->AliasNbPages();
		$pdf->AddPage();
			
		$aligns = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
		$widths1 = array(28, 90, 90, 60);
		$widths2 = array(28, 30, 30, 30, 30, 30, 30, 30, 30);
		$header1 = array('Fecha', 'Entradas', 'Salidas', 'Inventario');
		$header2 = array('', 'Unidades', 'Costo unitario', 'Costo total', 'Unidades', 'Precio unitario', 'Valor', 'Unidades', 'Valor');
		
		$pdf->SetFont('Arial', 'B', 8);
		$pdf->SetTextColor(255,255,255);
		$pdf->SetFillColor(160,160,160);
		
		$pdf->SetX(6);
		$pdf->SetAligns($aligns);
		$pdf->SetWidths($widths1);
		$pdf->Row($header1, true);
		
		$pdf->SetTextColor(0,0,0);
		$pdf->SetX(6);
		$pdf->SetAligns($aligns);
		$pdf->SetWidths($widths2);
		$pdf->Row($header2, false);
		
		if(count($data_valoresanteriores)>0){
			$this->pintarRowUeps($pdf, $aligns, $widths2, $header2, $data_valoresanteriores);
		}
		
		foreach($data as $key => $item){
			$band_head = false;
			if($pdf->GetY() >= $pdf->limiteY){ //salta de pagina si exede el max
				$pdf->AddPage();
					
				$pdf->SetFont('Arial', 'B', 9);
				$pdf->SetTextColor(255, 255, 255);
				$pdf->SetFillColor(160, 160, 160);
				$pdf->SetX(6);
				$pdf->SetAligns($aligns);
				$pdf->SetWidths($widths1);
				$pdf->Row($header1, true);
				
				$pdf->SetTextColor(0,0,0);
				$pdf->SetX(6);
				$pdf->SetAligns($aligns);
				$pdf->SetWidths($widths2);
				$pdf->Row($header2, false);
			}
			
			$this->pintarRowUeps($pdf, $aligns, $widths2, $header2, $item);
		}
			
		$pdf->Output('reporte.pdf', 'I');
	}
	private function pintarRowUeps(&$pdf, $aligns, $widths2, $header2, $item){
		$pdf->SetFont('Arial','',8);
		if($item['fecha']=='Totales'){
			$pdf->SetTextColor(255,255,255);
			$pdf->SetFillColor(83,83,83);
			$fill = true;
		}elseif($item['fecha']=='Anterior'){
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(254,242,197);
			$fill = true;
		}elseif($item['cantidad']!=''){
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(240,240,240);
			$fill = true;
		}else{
			$pdf->SetTextColor(0,0,0);
			$fill = false;
		}
		
		$item['abreviatura'] = isset($item['abreviatura'])? $item['abreviatura']: '';
		$item['abreviaturas'] = isset($item['abreviaturas'])? $item['abreviaturas']: '';
		
		$data = array();
		$data[] = $item['fecha'];
		$data[] = $item['cantidad'].' '.$item['abreviatura'];
		$data[] = isset($item['precio_unitario'])? String::formatoNumero($item['precio_unitario']) : '';
		$data[] = ($item['importe']!='')? String::formatoNumero($item['importe']) : '';
		$data[] = $item['cantidads'].' '.$item['abreviaturas'];
		$data[] = isset($item['precio_unitarios'])? String::formatoNumero($item['precio_unitarios']) : '';
		$data[] = ($item['importes']!='')? String::formatoNumero($item['importes']) : '';
		$item['abreviatura'] = ($item['abreviatura']=='')? $item['abreviaturas'] : $item['abreviatura'];
		$data[] = $item['unidades'].' '.$item['abreviatura'];
		$data[] = String::formatoNumero($item['valor']);
		
		$pdf->SetX(6);
		$pdf->SetAligns($aligns);
		$pdf->SetWidths($widths2);
		$pdf->Row($data, $fill);
	}
	
	
	/**
	 * ********************************************************
	 * Obtiene la informacion para los reportes existencia por unidad y 
	 * existencia por costo
	 * @param unknown_type $fecha1
	 * @param unknown_type $fecha2
	 */
	public function epu_epc($fecha1, $fecha2){
		if($fecha1!='' && $fecha2!='')
			$sql_fecha = " BETWEEN '".$fecha1."' AND '".$fecha2."'";
		elseif($fecha1!="")
			$sql_fecha = " >= '".$fecha1."'";
		elseif($fecha2!='')
			$sql_fecha = " <= '".$fecha2."'";
		
		$sql_fam = '';
		if(isset($_GET['dfamilias'])){
			$sql_fam = " AND id_familia IN ('".implode("','", $_GET['dfamilias'])."')";
		}
		
		$response = array();
		$res_fami = $this->db->query("SELECT * FROM productos_familias WHERE status = 'ac'".$sql_fam);
		foreach($res_fami->result() as $fami){
			$res = $this->db->query("
				SELECT 
					id_producto, id_familia, nombre, abreviatura, 
					Sum(entradas) AS entradas, Sum(importe_entradas) AS importe_entradas, 
					Sum(salidas) AS salidas, Sum(importe_salidas) AS importe_salidas
				FROM 
					(
					SELECT id_producto, id_familia, nombre, abreviatura, Sum(cantidad) AS entradas, 
						Sum(importe) AS importe_entradas, 0 AS salidas, 0 AS importe_salidas
					FROM reportes_costo_existencias3
					WHERE fecha ".$sql_fecha." AND tipo = 'entrada'
					GROUP BY id_producto, id_familia, nombre, abreviatura
				
					UNION 
				
					SELECT id_producto, id_familia, nombre, abreviatura, 0 AS entradas, 0 AS importe_entradas, 
						Sum(cantidad) AS salidas, Sum(importe) AS importe_salidas
					FROM reportes_costo_existencias3
					WHERE fecha ".$sql_fecha." AND tipo = 'salida'
					GROUP BY id_producto, id_familia, nombre, abreviatura
					) AS t
				WHERE id_familia = '".$fami->id_familia."'
				GROUP BY id_producto, id_familia, nombre, abreviatura
				ORDER BY nombre ASC
				");
			
			if($res->num_rows() > 0){
				$response[str_replace('.', '_', $fami->id_familia)] = array(
					'familia' => $fami->nombre,
					'productos' => $res->result()
				);
			}
			
			$res->free_result();
		}
		
		return $response;
	}
	
	/**
	 * Genera el reporte existencias por unidad en pdf
	 * @param unknown_type $data
	 * @param unknown_type $fecha1
	 * @param unknown_type $fecha2
	 */
	public function pdfEpu($data, $fecha1, $fecha2){
		if($fecha1!='' && $fecha2!='')
			$labelFechas = "Desde la fecha ".$fecha1." hasta ".$fecha2;
		elseif($fecha1!="")
			$labelFechas = "Desde la fecha ".$fecha1;
		elseif($fecha2!='')
			$labelFechas = "Hasta la fecha ".$fecha2;
		
		$this->load->library('mypdf');
		// Creación del objeto de la clase heredada
		$pdf = new MYpdf('P', 'mm', 'Letter');
		$pdf->show_head = true;
		$pdf->titulo2 = 'Reporte de existencia por unidades';
		$pdf->titulo3 = $labelFechas."\n";
		//$pdf->titulo3 .=  $nombre_producto;
		$pdf->AliasNbPages();
		$pdf->AddPage();

		$links = array('', '', '', '');
		$aligns = array('C', 'C', 'C', 'C');
		$widths1 = array(98, 35, 35, 35);
		$header1 = array('Producto', 'Entradas', 'Salidas', 'Existencia');
		
		foreach($data as $key => $item){
			$pdf->SetFont('Arial', 'B', 9);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetX(6);
			$pdf->MultiCell(200, 8, $item['familia'], 0, 'L');
			
			$pdf->SetFont('Arial', 'B', 8);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->SetFillColor(160, 160, 160);
			$pdf->SetX(6);
			$pdf->SetAligns($aligns);
			$pdf->SetWidths($widths1);
			$pdf->Row($header1, true);
			
			$pdf->SetFont('Arial', '', 8);
			$pdf->SetTextColor(0,0,0);
			foreach($item['productos'] as $key2 => $prod){
				if($pdf->GetY() >= $pdf->limiteY){ //salta de pagina si exede el max
					$pdf->AddPage();
				}
				$datarow = array($prod->nombre, 
					$prod->entradas.' '.$prod->abreviatura, 
					$prod->salidas.' '.$prod->abreviatura, 
					($prod->entradas - $prod->salidas).' '.$prod->abreviatura);
				
				$links[0] = base_url('panel/inventario/epud_pdf/?dfecha1='.$fecha1.'&dfecha2='.$fecha2.'&id_producto='.$prod->id_producto);
				$pdf->SetX(6);
				$pdf->SetAligns($aligns);
				$pdf->SetWidths($widths1);
				$pdf->SetMyLinks($links);
				$pdf->Row($datarow, false);
			}
		}
			
		$pdf->Output('reporte.pdf', 'I');
	}
	
	/**
	 * Genera el reporte existencias por costo en pdf
	 * @param unknown_type $data
	 * @param unknown_type $fecha1
	 * @param unknown_type $fecha2
	 */
	public function pdfEpc($data, $fecha1, $fecha2){
		if($fecha1!='' && $fecha2!='')
			$labelFechas = "Desde la fecha ".$fecha1." hasta ".$fecha2;
		elseif($fecha1!="")
		$labelFechas = "Desde la fecha ".$fecha1;
		elseif($fecha2!='')
		$labelFechas = "Hasta la fecha ".$fecha2;
	
		$this->load->library('mypdf');
		// Creación del objeto de la clase heredada
		$pdf = new MYpdf('P', 'mm', 'Letter');
		$pdf->show_head = true;
		$pdf->titulo2 = 'Reporte de existencia por costo';
		$pdf->titulo3 = $labelFechas."\n";
		//$pdf->titulo3 .=  $nombre_producto;
		$pdf->AliasNbPages();
		$pdf->AddPage();
			
		$links = array('', '', '', '');
		$aligns = array('C', 'C', 'C', 'C');
		$widths1 = array(98, 35, 35, 35);
		$header1 = array('Producto', 'Entradas', 'Salidas', 'Existencia');
	
		foreach($data as $key => $item){
			$pdf->SetFont('Arial', 'B', 9);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetX(6);
			$pdf->MultiCell(200, 8, $item['familia'], 0, 'L');
				
			$pdf->SetFont('Arial', 'B', 8);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->SetFillColor(160, 160, 160);
			$pdf->SetX(6);
			$pdf->SetAligns($aligns);
			$pdf->SetWidths($widths1);
			$pdf->Row($header1, true);
				
			$pdf->SetFont('Arial', '', 8);
			$pdf->SetTextColor(0,0,0);
			foreach($item['productos'] as $key2 => $prod){
				if($pdf->GetY() >= $pdf->limiteY){ //salta de pagina si exede el max
					$pdf->AddPage();
				}
				$datarow = array($prod->nombre, 
					String::formatoNumero($prod->importe_entradas), 
					String::formatoNumero($prod->importe_salidas), 
					String::formatoNumero($prod->importe_entradas - $prod->importe_salidas)
				);
	
				$links[0] = base_url('panel/inventario/epcd_pdf/?dfecha1='.$fecha1.'&dfecha2='.$fecha2.'&id_producto='.$prod->id_producto);
				$pdf->SetX(6);
				$pdf->SetAligns($aligns);
				$pdf->SetWidths($widths1);
				$pdf->SetMyLinks($links);
				$pdf->Row($datarow, false);
			}
		}
			
		$pdf->Output('reporte.pdf', 'I');
	}
	
	
	
	/**
	 * *******************************************************
	 * Obtiene la informacion para los reportes existencia por unidad y
	 * existencia por costo desglosados
	 * @param unknown_type $id_producto
	 * @param unknown_type $fecha1
	 * @param unknown_type $fecha2
	 */
	public function epud_epcd($id_producto, $fecha1, $fecha2){
		if($fecha1!='' && $fecha2!='')
			$sql_fecha = " BETWEEN '".$fecha1."' AND '".$fecha2."'";
		elseif($fecha1!="")
			$sql_fecha = " >= '".$fecha1."'";
		elseif($fecha2!='')
			$sql_fecha = " <= '".$fecha2."'";
	
		$response = array();
		$res = $this->db->query("
			SELECT 
				fecha, nombre, abreviatura, cantidad, precio_u, importe, tipo
			FROM reportes_costo_existencias3
			WHERE id_producto = '".$id_producto."' AND fecha ".$sql_fecha."
			ORDER BY fecha ASC
			");
			
		if($res->num_rows() > 0){
			$response = array(
				'nombre' => '',
				'historial' => $res->result()
			);
			$response['nombre'] = $response['historial'][0]->nombre;
		}
	
		return $response;
	}
	
	/**
	 * Genera el reporte existencias por unidad desglosado en pdf
	 * @param unknown_type $data
	 * @param unknown_type $fecha1
	 * @param unknown_type $fecha2
	 */
	public function pdfEpud($data, $fecha1, $fecha2){
		if($fecha1!='' && $fecha2!='')
			$labelFechas = "Desde la fecha ".$fecha1." hasta ".$fecha2;
		elseif($fecha1!="")
		$labelFechas = "Desde la fecha ".$fecha1;
		elseif($fecha2!='')
		$labelFechas = "Hasta la fecha ".$fecha2;
	
		$this->load->library('mypdf');
		// Creación del objeto de la clase heredada
		$pdf = new MYpdf('P', 'mm', 'Letter');
		$pdf->show_head = true;
		$pdf->titulo2 = 'Reporte de existencia por unidades';
		$pdf->titulo3 = $labelFechas."\n";
		$pdf->titulo3 .=  $data['nombre'];
		$pdf->AliasNbPages();
		$pdf->AddPage();
		
		$aligns = array('C', 'C', 'C', 'C');
		$widths1 = array(53, 50, 50, 50);
		$header1 = array('Fecha', 'Entradas', 'Salidas', 'Existencia');
		$totales = array('Totales', 0, 0, 0);
		
		foreach($data['historial'] as $key2 => $prod){
			if($pdf->GetY() >= $pdf->limiteY || $key2 == 0){ //salta de pagina si exede el max
				if($key2 > 0)
					$pdf->AddPage();
				
				$pdf->SetFont('Arial', 'B', 8);
				$pdf->SetTextColor(255, 255, 255);
				$pdf->SetFillColor(160, 160, 160);
				$pdf->SetX(6);
				$pdf->SetAligns($aligns);
				$pdf->SetWidths($widths1);
				$pdf->Row($header1, true);
			}
			
			$entrada = $salida = 0;
			if($prod->tipo == 'salida'){
				$totales[2] += $prod->cantidad;
				$totales[3] -= $prod->cantidad; //existencias
				$salida = $prod->cantidad;
			}else{
				$totales[1] += $prod->cantidad;
				$totales[3] += $prod->cantidad; //existencias
				$entrada = $prod->cantidad;
			}
			$datarow = array($prod->fecha,
					$entrada.' '.$prod->abreviatura,
					$salida.' '.$prod->abreviatura,
					$totales[3].' '.$prod->abreviatura);

			$pdf->SetFont('Arial', '', 8);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetX(6);
			$pdf->SetAligns($aligns);
			$pdf->SetWidths($widths1);
			$pdf->Row($datarow, false);
		}
		
		$pdf->SetFont('Arial', 'B', 8);
		$pdf->SetTextColor(255, 255, 255);
		$pdf->SetFillColor(160, 160, 160);
		$pdf->SetX(6);
		$pdf->SetAligns($aligns);
		$pdf->SetWidths($widths1);
		$pdf->Row($totales, true);
			
		$pdf->Output('reporte.pdf', 'I');
	}
	
	/**
	 * Genera el reporte existencias por costo desglosado en pdf
	 * @param unknown_type $data
	 * @param unknown_type $fecha1
	 * @param unknown_type $fecha2
	 */
	public function pdfEpcd($data, $fecha1, $fecha2){
		if($fecha1!='' && $fecha2!='')
			$labelFechas = "Desde la fecha ".$fecha1." hasta ".$fecha2;
		elseif($fecha1!="")
		$labelFechas = "Desde la fecha ".$fecha1;
		elseif($fecha2!='')
		$labelFechas = "Hasta la fecha ".$fecha2;
	
		$this->load->library('mypdf');
		// Creación del objeto de la clase heredada
		$pdf = new MYpdf('P', 'mm', 'Letter');
		$pdf->show_head = true;
		$pdf->titulo2 = 'Reporte de existencia por costo';
		$pdf->titulo3 = $labelFechas."\n";
		$pdf->titulo3 .=  $data['nombre'];
		$pdf->AliasNbPages();
		$pdf->AddPage();
	
		$aligns = array('C', 'C', 'C', 'C');
		$widths1 = array(53, 50, 50, 50);
		$header1 = array('Fecha', 'Entradas', 'Salidas', 'Existencia');
		$totales = array('Totales', 0, 0, 0);
	
		foreach($data['historial'] as $key2 => $prod){
			if($pdf->GetY() >= $pdf->limiteY || $key2 == 0){ //salta de pagina si exede el max
				if($key2 > 0)
					$pdf->AddPage();
	
				$pdf->SetFont('Arial', 'B', 8);
				$pdf->SetTextColor(255, 255, 255);
				$pdf->SetFillColor(160, 160, 160);
				$pdf->SetX(6);
				$pdf->SetAligns($aligns);
				$pdf->SetWidths($widths1);
				$pdf->Row($header1, true);
			}
				
			$entrada = $salida = 0;
			if($prod->tipo == 'salida'){
				$totales[2] += $prod->importe;
				$totales[3] -= $prod->importe; //existencias
				$salida = $prod->importe;
			}else{
				$totales[1] += $prod->importe;
				$totales[3] += $prod->importe; //existencias
				$entrada = $prod->importe;
			}
			$datarow = array($prod->fecha,
					String::formatoNumero($entrada),
					String::formatoNumero($salida),
					String::formatoNumero($totales[3]));
	
			$pdf->SetFont('Arial', '', 8);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetX(6);
			$pdf->SetAligns($aligns);
			$pdf->SetWidths($widths1);
			$pdf->Row($datarow, false);
		}
		
		$totales[1] = String::formatoNumero($totales[1]);
		$totales[2] = String::formatoNumero($totales[2]);
		$totales[3] = String::formatoNumero($totales[3]);
		
		$pdf->SetFont('Arial', 'B', 8);
		$pdf->SetTextColor(255, 255, 255);
		$pdf->SetFillColor(160, 160, 160);
		$pdf->SetX(6);
		$pdf->SetAligns($aligns);
		$pdf->SetWidths($widths1);
		$pdf->Row($totales, true);
			
		$pdf->Output('reporte.pdf', 'I');
	}
}