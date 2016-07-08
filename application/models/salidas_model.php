<?php
class salidas_model extends CI_Model{
	
	function __construct(){
		parent::__construct();
	}
	
	/**
	 * Obtiene el listado de salidas
	 */
	public function getSalidas(){
		$sql = '';
		//paginacion
		$params = array(
				'result_items_per_page' => '30',
				'result_page' => (isset($_GET['pag'])? $_GET['pag']: 0)
		);
		if($params['result_page'] % $params['result_items_per_page'] == 0)
			$params['result_page'] = ($params['result_page']/$params['result_items_per_page']);
		
		//Filtros para buscar
		if($this->input->get('ffecha') != '')
			$sql = " AND Date(s.fecha) = '".$this->input->get('ffecha')."'";
		if($this->input->get('ftipo_salida') != '')
			$sql .= " AND s.tipo_salida = '".mb_strtolower($this->input->get('ftipo_salida'))."'";
		if($this->input->get('ftipo') != ''){
			$sql .= " AND s.status = '".$this->input->get('ftipo')."'";
		}
		
		$query = BDUtil::pagination("
				SELECT s.id_salida, Date(s.fecha) AS fecha, s.folio, s.tipo_salida, s.status
				FROM salidas as s
				WHERE s.status IN ('ba','sa') $sql
				ORDER BY (Date(s.fecha), s.folio) DESC
				", $params, true);
		
		$res = $this->db->query($query['query']);
		
		$response = array(
				'salidas' => array(),
				'total_rows' 		=> $query['total_rows'],
				'items_per_page' 	=> $params['result_items_per_page'],
				'result_page' 		=> $params['result_page']
		);
		if($res->num_rows() > 0)
			$response['salidas'] = $res->result();
		
		return $response;
	}
	
	/**
	 * Obtiene el listado de las herramientas prestadas
	 */
	public function getHerramientas(){
		$sql = '';
		//paginacion
		$params = array(
				'result_items_per_page' => '30',
				'result_page' => (isset($_GET['pag'])? $_GET['pag']: 0)
		);
		if($params['result_page'] % $params['result_items_per_page'] == 0)
			$params['result_page'] = ($params['result_page']/$params['result_items_per_page']);
	
		//Filtros para buscar
		if($this->input->get('ffecha') != '')
			$sql = " AND Date(s.fecha) = '".$this->input->get('ffecha')."'";
		if($this->input->get('fmostrar') != ''){
			if($this->input->get('fmostrar')=='pv')
				$sql .= " AND DATE(fecha_vencimiento)-DATE(now())<=3 AND DATE(fecha_vencimiento)-DATE(now())>=1";
			elseif($this->input->get('fmostrar')=='ve')
				$sql .= " AND DATE(now())>=DATE(fecha_vencimiento)";
		}
		
		if($this->input->get('ftipo') != ''){
			$sql .= " AND s.status = '".$this->input->get('ftipo')."'";
		}
	
		$query = BDUtil::pagination("
				SELECT id_alerta, tabla_obj, id_obj1, id_obj2, descripcion, DATE(fecha_vencimiento) as fecha_vencimiento, DATE(fecha_vencimiento)-DATE(now()) as dias_restantes
				FROM alertas
				WHERE tabla_obj = 'salidas_productos' $sql
				ORDER BY (Date(fecha_vencimiento)) DESC
				", $params, true);
	
				$res = $this->db->query($query['query']);
	
				$response = array(
						'herramientas' => array(),
						'total_rows' 		=> $query['total_rows'],
						'items_per_page' 	=> $params['result_items_per_page'],
						'result_page' 		=> $params['result_page']
				);
				if($res->num_rows() > 0)
					$response['herramientas'] = $res->result();
	
				return $response;
	}
	
	/**
	 * Obtiene la informacion de una compra
	 */
	public function getInfoSalida($id, $info_basic=false){
		$res = $this->db
			->select('*')
			->from('salidas')
			->where("id_salida = '".$id."'")
		->get();
		if($res->num_rows() > 0){
			$response['info'] = $res->row();
			$response['info']->fecha = substr($response['info']->fecha, 0, 10);
			$res->free_result();
			if($info_basic)
				return $response;
			
			//productos
			$res = $this->db
				->select('p.id_producto, p.codigo, p.nombre, sp.taza_iva, sp.cantidad, sp.precio_unitario,
						sp.importe, sp.importe_iva, sp.total, pu.abreviatura')
				->from('salidas_productos AS sp')
					->join('productos AS p', 'p.id_producto = sp.id_producto', 'inner')
					->join('productos_unidades AS pu', 'pu.id_unidad = p.id_unidad', 'inner')
				->where("sp.id_salida = '".$id."'")
			->get();
			if($res->num_rows() > 0){
				$response['productos'] = $res->result();
			}
			$res->free_result();
			
			if($response['info']->tipo_salida=='av'){
				$res = $this->db->select("id_avion, matricula, modelo")->from("aviones")->where("id_avion",$response['info']->id_avion)->get();
			}
			elseif($response['info']->tipo_salida=='tr'){
				if($response['info']->tipo_trabajador=='pi')
					$res = $this->db->select("id_proveedor as id_trabajador, nombre, calle, no_exterior, colonia, municipio, estado")
										->from("proveedores")->where("id_proveedor",$response['info']->id_trabajador)->get();
				elseif($response['info']->tipo_trabajador=='tr')
					$res = $this->db->select("id_empleado as id_trabajador, (nombre || ' ' apellido_paterno || ' ' || apellido_materno) as nombres, calle, numero as no_exterior, colonia, municipio, estado")
										->from("empleados")->where("id_empleado",$response['info']->id_trabajador)->get();
			}
			elseif($response['info']->tipo_salida=='ve'){
				$res = $this->db->select("id_vehiculo, nombre, placas, modelo")->from("vehiculos")->where("id_vehiculo",$response['info']->id_vehiculo)->get();
			}
			
			if($res->num_rows()>0)
				$response['info_tipo'] = $res->result();
			
			return $response;
		}else
			return false;
	}
	
	/**
	 * Agrega una salida a la bd
	 */
	public function addSalida(){
		$existen = true;
		$msg = 'La cantidad solicitada de el/los productos ';
		// Valida que los productos tengan existencias
		foreach($_POST['dpid_producto'] as $key => $producto){
			$sql = $this->db->select("nombre, existencia_producto('$producto') as existentes")->
								from("productos")->
								where("id_producto",$producto)->
								get()->row();
			if($sql->existentes < $_POST['dpcantidad'][$key]){
				$existen = false;
				$msg .= $sql->nombre.', '; 
			}
		}
		
		if($existen){
		
			$id_salida = BDUtil::getId();
			$data = array(
				'id_salida' => $id_salida,
				'folio' => $this->input->post('dfolio'),
				'fecha' => $this->input->post('dfecha') ,
				'tipo_salida' => $this->input->post('dtipo_salida'),
				'status' => $this->input->post('dtipo'),
				'id_usuario' => $_SESSION['id_empleado']
			);
			
			switch($_POST['dtipo_salida']){
				case 'av':
						$data['id_avion'] = $this->input->post('did_avion');
					break;
				case 'tr':
					$data['id_trabajador'] = $this->input->post('did_trabajador');
					$data['tipo_trabajador'] = $this->input->post('dtipo_trabajador');
					$data['fecha_entrega'] = $this->input->post('dfecha_entrega');
					break;
				case 've':
					$data['id_vehiculo'] = $this->input->post('did_vehiculo');
					break;
			}
			$this->db->insert('salidas', $data);
			
			//productos para la salida
			if(isset($_POST['dpid_producto'])){
				$this->addProductos($id_salida);
			}
			return array(true, $id_salida);
		}
		else return array(false, 'msg' => $msg.' es mayor a las existencias');
	}
	
	/**
	 * Cancela una compra, la elimina
	 */
	public function cancelSalida(){
		$this->db->update('salidas', array('status' => 'ca'), "id_salida = '".$_GET['id']."'");
		return array(true, '');
	}
	
	public function entregar_herramienta(){
		$q_result = $this->db->select("id_obj1, id_obj2")->from("alertas")->where("id_alerta",$_GET['id'])->get()->result();
		$this->db->update("salidas_productos",array('status'=>'f'),array("id_salida"=>$q_result[0]->id_obj1,"id_producto"=>$q_result[0]->id_obj2));
		$this->db->delete("alertas",array("id_alerta"=>$_GET['id']));
		return array(true,'');
		
	}
	
	public function extender_plazo_herramienta(){
		$fecha_nueva = $this->db->select("fecha_vencimiento + interval '3 day' as fecha_nueva")->from("alertas")->where('id_alerta',$_GET['id'])->get()->row()->fecha_nueva; 
		$this->db->update("alertas",array('fecha_vencimiento'=>$fecha_nueva),array("id_alerta"=>$_GET['id']));
		return array(true,'');
	}
	
	/**
	 * Agrega los productos a una compra
	 * @param unknown_type $id_compra
	 * @param unknown_type $tipo
	 */
	public function addProductos($id_salida, $tipo='add'){
		if(is_array($_POST['dpid_producto'])){
			
			$tr_nombre='';
			if($_POST['dtipo_salida']=='tr'){
				if($_POST['dtipo_trabajador']=='tr')
					$tr_nombre = $this->db->select("(nombre || ' ' || apellido_paterno) as nombre")->
											from("empleados")->
											where("id_empleado",$_POST['did_trabajador'])->
											get()->row()->nombre;
				elseif($_POST['dtipo_trabajador']=='pi')
					$tr_nombre = $this->db->select("nombre")->
											from("proveedores")->
											where("id_proveedor",$_POST['did_trabajador'])->
											get()->row()->nombre;
			}
			
			$data_productos = array();
			$data_alertas = array();
			foreach($_POST['dpid_producto'] as $key => $producto){
				//Datos de los productos de la salida
				$data_productos[] = array(
					'id_salida' => $id_salida,
					'id_producto' => $producto,
					'taza_iva' => $_POST['dptaza_iva'][$key],
					'cantidad' => $_POST['dpcantidad'][$key],
					'precio_unitario' => $_POST['dpprecio_unitario'][$key],
					'importe' => $_POST['dpimporte'][$key],
					'importe_iva' => $_POST['dpimporte_iva'][$key],
					'total' => ($_POST['dpimporte'][$key]+$_POST['dpimporte_iva'][$key]),
				);
				
				if($_POST['dtipo_salida']=='tr'){				
					$pr_nombre = $this->db->select("nombre")->
											from("productos")->
											where("id_producto",$producto)->
											get()->row()->nombre;
					
					$id_alerta = BDUtil::getId();
					$data_alertas[] = array(
							'id_alerta' => $id_alerta,
							'tabla_obj' => 'salidas_productos',
							'id_obj1' => $id_salida,
							'id_obj2' => $producto,
							'descripcion' => "Vencimiento de Prestamo Herramienta - ({$_POST['dpcantidad'][$key]}) $pr_nombre a $tr_nombre",
							'fecha_vencimiento' => $this->input->post('dfecha_entrega')
						);
				}
			}

			if(count($data_productos) > 0){
				$this->db->insert_batch('salidas_productos', $data_productos);
				
				if(count($data_alertas) > 0)
					$this->db->insert_batch('alertas', $data_alertas);
			}
			
			return array(true, '');
		}
		return array(false, '');
	}

	public function data_rsa()
	{
		$tipo = 'av';
		$_GET['dfecha1'] = (isset($_GET['dfecha1']))?$_GET['dfecha1']:date('Y-m').'-01';
		$_GET['dfecha2'] = (isset($_GET['dfecha2']))?$_GET['dfecha2']:date('Y-m-d');
		$_GET['didproducto'] = (isset($_GET['didproducto']))?$_GET['didproducto']:'';
		$_GET['ida'] = (isset($_GET['ida']))?$_GET['ida']:'';

		$sql = '';
		$inner = '';
		
		if( $this->input->get('dfecha1') != '' )
			$sql = " AND DATE(s.fecha)>='".$this->input->get('dfecha1')."'";

		if( $this->input->get('dfecha2') != '' )
			$sql .= " AND DATE(s.fecha)<='".$this->input->get('dfecha2')."'";

		if ( empty($_GET['ida']) && !isset($_GET['tp'])) // empty($_GET['didproducto']) && 
		{
			if ( $this->input->get('didproducto') != '' ) 
				$sql .= " AND sp.id_producto='{$this->input->get('didproducto')}'";

			$query = $this->db->query("SELECT a.id_avion, a.matricula as avion, SUM(sp.total) as total_salida
																	FROM aviones a
																	INNER JOIN salidas s ON a.id_avion=s.id_avion
																	INNER JOIN salidas_productos sp ON s.id_salida=sp.id_salida
																	WHERE s.status='sa' AND s.tipo_salida='av' $sql
																	GROUP BY a.id_avion, a.matricula
																	ORDER BY a.matricula ASC
													");	
		}
		else
		{
			$tipo = 'sa';
			$_GET['idp'] = (isset($_GET['idp']))?$_GET['idp']:'';
			$_GET['ida'] = (isset($_GET['ida']))?$_GET['ida']:'';
			if ( $_GET['idp'] != '' && $_GET['ida'] != ''){
				$sql .= " AND sp.id_producto='{$_GET['idp']}' AND id_avion = '{$_GET['ida']}'";
				$inner = " INNER JOIN salidas_productos sp ON s.id_salida=sp.id_salida";
			}
			elseif ( isset($_GET['tp']) ) {
				$sql .= " AND sp.id_producto='{$_GET['idp']}'";
				$inner = " INNER JOIN salidas_productos sp ON s.id_salida=sp.id_salida";	
			}
			else
				$sql .= " AND id_avion = '{$_GET['ida']}'";

			$query = $this->db->query("SELECT s.id_salida, s.folio, s.fecha
																	FROM salidas s
																	$inner
																	WHERE s.tipo_salida='av' AND s.status='sa' $sql
																	ORDER BY fecha ASC
															");

			if ($query->num_rows() > 0) {
				foreach ($query->result() as $key => $salida) {
					$sql2 = ($_GET['idp'] != '') ? " AND sp.id_producto='{$_GET['idp']}'": "";
					$salida->productos = $this->db->query("SELECT sp.id_producto, p.nombre, sp.cantidad, sp.precio_unitario, sp.total
																	FROM salidas s
																	INNER JOIN salidas_productos sp ON sp.id_salida=s.id_salida
																	INNER JOIN productos p ON sp.id_producto=p.id_producto
																	WHERE s.tipo_salida='av' AND s.status='sa' AND sp.id_salida='{$salida->id_salida}' $sql2")->result();
				}
			}
		}
		// echo '<pre>';
		// var_dump($query->result());exit;	
		// echo '</pre>';
		

		return array('data'=>$query->result(), 'tipo'=>$tipo);
	}

	public function pdf_rsa($data)
	{
		if($_GET['dfecha1']!='' && $_GET['dfecha2']!='')
			$labelFechas = "Desde la fecha ".$_GET['dfecha1']." hasta ".$_GET['dfecha2'];
		elseif($_GET['dfecha1']!="")
		$labelFechas = "Desde la fecha ".$_GET['dfecha1'];
		elseif($_GET['dfecha2']!='')
		$labelFechas = "Hasta la fecha ".$_GET['dfecha2'];
	
		$this->load->library('mypdf');
		// Creación del objeto de la clase heredada
		$pdf = new MYpdf('P', 'mm', 'Letter');
		$pdf->show_head = true;
		$pdf->titulo2 = 'Reporte Salidas Vuelos';

		$lbl_pro = '';
		if (isset($_GET['ida'])) 
		{
			if ($_GET['ida'] != '') 
			{
				$this->load->database();
				$m = $this->db->select('matricula')->from('aviones')->where('id_avion',$_GET['ida'])->get();
				$lbl_pro = " Avion Matricula {$m->row()->matricula}";
			}
		}

		$pdf->titulo3 =  "$lbl_pro \n". $labelFechas;
		$pdf->AliasNbPages();
		$pdf->AddPage();


		$links = array('', '');
		$aligns = array('C', 'C');
		$widths = array(155, 50);
		$header = array('Avión', 'Total');

		if ($data['tipo'] == 'sa') {
			$links = array('', '', '' , '');
			$aligns = array('C', 'C', 'C', 'C');
			$widths = array(55, 50, 50, 50);
			$header = array('Nombre', 'Cantidad', 'P. UNITARIO', 'Total');
		}
	
		$ttotal = 0;
		foreach($data['data'] as $key => $item)
		{

			if ($data['tipo'] != 'av') 
			{
				$pdf->SetFont('Arial', 'B', 9);
				$pdf->SetTextColor(0,0,0);
				$pdf->SetX(6);
				$pdf->MultiCell(200, 8, 'Salida Folio #'. $item->folio . ' Fecha ' . $item->fecha, 0, 'L');
					
				$pdf->SetFont('Arial', 'B', 8);
				$pdf->SetTextColor(255, 255, 255);
				$pdf->SetFillColor(160, 160, 160);
				$pdf->SetX(6);
				$pdf->SetAligns($aligns);
				$pdf->SetWidths($widths);
				$pdf->Row($header, true);

				$pdf->SetFont('Arial', '', 8);
				$pdf->SetTextColor(0,0,0);
				foreach($item->productos as $key2 => $prod) {
					if($pdf->GetY() >= $pdf->limiteY)
					{ //salta de pagina si exede el max
						$pdf->AddPage();
					}

					$ttotal += floatval($prod->total);
					$datarow = array($prod->nombre, $prod->cantidad, String::formatoNumero($prod->precio_unitario), String::formatoNumero($prod->total));
					
					$links[0] = base_url('panel/salidas/pdf_rsa/?dfecha1='.$_GET['dfecha1'].'&dfecha2='.$_GET['dfecha2'].'&idp='.$prod->id_producto.'&tp=t');
					$pdf->SetX(6);
					$pdf->SetAligns($aligns);
					$pdf->SetWidths($widths);
					$pdf->SetMyLinks($links);
					$pdf->Row($datarow, false);
				}
			}
			else 
			{
				if($pdf->GetY() >= $pdf->limiteY || $key == 0)
				{ //salta de pagina si exede el max
					if($key > 0)
						$pdf->AddPage();

					$pdf->SetFont('Arial', 'B', 8);
					$pdf->SetTextColor(255, 255, 255);
					$pdf->SetFillColor(160, 160, 160);
					$pdf->SetX(6);
					$pdf->SetAligns($aligns);
					$pdf->SetWidths($widths);
					$pdf->Row($header, true);

				}

				$ttotal += floatval($item->total_salida);
				$datarow = array($item->avion, String::formatoNumero($item->total_salida));
				$get_pid = ($_GET['didproducto'] != '' )?'&idp='.$_GET['didproducto']:'';
				$links[0] = base_url('panel/salidas/pdf_rsa/?dfecha1='.$_GET['dfecha1'].'&dfecha2='.$_GET['dfecha2'].'&ida='.$item->id_avion.$get_pid);
				$pdf->SetX(6);
				$pdf->SetAligns($aligns);
				$pdf->SetWidths($widths);
				$pdf->SetMyLinks($links);
				$pdf->Row($datarow, false);

			}
			
		}

		if ( COUNT($data['data']) > 0 ) {
			$y = $pdf->GetY();
			$pdf->SetFont('Arial','B',10);
			$pdf->SetTextColor(255,255,255);
			$pdf->SetFillColor(140,140,140);
			
			$pdf->SetXY(130, ($y+5));
			$pdf->Cell(31, 6, 'Total' , 1, 0, 'C',1);
			
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetXY(161, ($y+5));
			$pdf->Cell(50, 6,String::formatoNumero($ttotal,2) , 1, 0, 'C');			
		}
		
		$pdf->Output('reporte.pdf', 'I');
	}

}