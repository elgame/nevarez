<?php
class compras_model extends CI_Model{
	
	function __construct(){
		parent::__construct();
	}
	
	/**
	 * Obtiene el listado de compras
	 */
	public function getCompras(){
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
			$sql = " AND Date(c.fecha) = '".$this->input->get('ffecha')."'";
		if($this->input->get('fserie') != '')
			$sql .= " AND c.serie = '".mb_strtoupper($this->input->get('fserie'))."'";
		if($this->input->get('ffolio') != '')
			$sql .= " AND c.folio = '".$this->input->get('ffolio')."'";
		if($this->input->get('fid_proveedor') != '')
			$sql .= " AND p.id_proveedor = '".$this->input->get('fid_proveedor')."'";
		if($this->input->get('ftipo') != ''){
			$status = $this->input->get('ftipo')=='co'? 'f': 't';
			$sql .= " AND is_gasto = '".$status."'";
		}
		
		$query = BDUtil::pagination("
				SELECT c.id_compra, Date(c.fecha) AS fecha, c.serie, c.folio, p.nombre, c.is_gasto, c.status, p.tipo
				FROM compras AS c INNER JOIN proveedores AS p ON p.id_proveedor = c.id_proveedor
				WHERE c.status <> 'ca' AND c.status <> 'n'".$sql."
				ORDER BY (Date(c.fecha), c.serie, c.folio) DESC
				", $params, true);
		$res = $this->db->query($query['query']);
		
		$response = array(
				'compras' => array(),
				'total_rows' 		=> $query['total_rows'],
				'items_per_page' 	=> $params['result_items_per_page'],
				'result_page' 		=> $params['result_page']
		);
		if($res->num_rows() > 0)
			$response['compras'] = $res->result();
		
		return $response;
	}
	
	/**
	 * Obtiene la informacion de una compra
	 */
	public function getInfoCompra($id, $info_basic=false){
		$res = $this->db
			->select('*')
			->from('compras')
			->where("id_compra = '".$id."'")
		->get();
		if($res->num_rows() > 0){
			$response['info'] = $res->row();
			$response['info']->fecha = substr($response['info']->fecha, 0, 10);
			$res->free_result();
			if($info_basic)
				return $response;
			
			$this->load->model('proveedores_model');
			$prov = $this->proveedores_model->getInfoProveedor($response['info']->id_proveedor, true);
			$response['info']->proveedor = $prov['info']->nombre;
			$response['info']->proveedor_dias_credito = $prov['info']->dias_credito;
			$response['info']->proveedor_info .= $prov['info']->calle!=''? $prov['info']->calle: '';
			$response['info']->proveedor_info .= $prov['info']->no_exterior!=''? ' #'.$prov['info']->no_exterior: '';
			$response['info']->proveedor_info .= $prov['info']->no_interior!=''? '-'.$prov['info']->no_interior: '';
			$response['info']->proveedor_info .= $prov['info']->colonia!=''? ', '.$prov['info']->colonia: '';
			$response['info']->proveedor_info .= "\n".($prov['info']->localidad!=''? $prov['info']->localidad: '');
			$response['info']->proveedor_info .= $prov['info']->municipio!=''? ', '.$prov['info']->municipio: '';
			$response['info']->proveedor_info .= $prov['info']->estado!=''? ', '.$prov['info']->estado: '';
			
			//productos
			if($_GET['gasto'] == 'f' && $_GET['tipo'] == 'pr'){ 
				$res = $this->db
					->select('p.id_producto, p.codigo, p.nombre, cp.taza_iva, cp.cantidad, cp.precio_unitario, 
							cp.importe, cp.importe_iva, cp.total')
					->from('compras_productos AS cp')
						->join('productos AS p', 'p.id_producto = cp.id_producto', 'inner')
					->where("cp.id_compra = '".$id."'")
				->get();
			}
			elseif($_GET['gasto'] == 't' && $_GET['tipo'] == 'pi'){
				$res = $this->db
						->select('cgv.id_compra, cgv.cantidad, cgv.taza_iva, cgv.precio_unitario, cgv.importe, cgv.importe_iva, cgv.total, count(*) as total_vuelos')
						->from('compras_gastos_vuelos AS cgv')
						->where("cgv.id_compra = '".$id."'")
						->group_by('cgv.id_compra, cgv.cantidad, cgv.taza_iva, cgv.precio_unitario, cgv.importe, cgv.importe_iva, cgv.total')
						->get();
			}			
			if($res->num_rows() > 0){
				$response['productos'] = $res->result();
			}
			$res->free_result();
			
			return $response;
		}else
			return false;
	}
	
	/**
	 * Agrega una compra a la bd
	 */
	public function addCompra(){
		$status = ($this->input->post('dcondicion_pago')=='co'? 'pa': 'p');
		$id_compra = BDUtil::getId();
		$data = array(
			'id_compra' => $id_compra,
			'id_proveedor' => $this->input->post('did_proveedor'),
			'id_empleado' => $_SESSION['id_empleado'],
			'serie' => mb_strtoupper($this->input->post('dserie'), 'utf-8'),
			'folio' => $this->input->post('dfolio'),
			'fecha' => $this->input->post('dfecha'),
			'subtotal' => $this->input->post('dtsubtotal'),
			'importe_iva' => $this->input->post('dtiva'),
			'total' => $this->input->post('dttotal'),
			'concepto' => $this->input->post('dconcepto'),
			'condicion_pago' => $this->input->post('dcondicion_pago'),
			'plazo_credito' => $this->input->post('dplazo_credito'),
			'is_gasto' => (isset($_POST['dis_gasto'])? 't': 'f'),
			'status' => $status
		);
		$this->db->insert('compras', $data);
		
		//productos para las compras
		if(isset($_POST['dpid_producto'])){
			$this->addProductos($id_compra);
		}
		
		//Si es a contado se paga y se agrega un abono
		if($status == 'pa'){
			$_POST['dmonto'] = $this->input->post('dttotal');
			$this->addAbono($id_compra, "Pago al contado.");
		}
		
		return array(true, $status, $id_compra);
	}
	
	/**
	 * Cancela una compra, la elimina
	 */
	public function cancelCompra(){
		$this->db->update('compras', array('status' => 'ca'), "id_compra = '".$_GET['id']."'");
		$this->db->update('compras_abonos', array('tipo' => 'ca'), "id_compra = '".$_GET['id']."'");
		return array(true, '');
	}
	
	
	/**
	 * Agrega los productos a una compra
	 * @param unknown_type $id_compra
	 * @param unknown_type $tipo
	 */
	public function addProductos($id_compra, $tipo='add'){
		if(is_array($_POST['dpid_producto'])){
			$data_productos = array();
			foreach($_POST['dpid_producto'] as $key => $producto){
				//Datos de los productos de la compra
				$data_productos[] = array(
					'id_compra' => $id_compra,
					'id_producto' => $producto,
					'taza_iva' => $_POST['dptaza_iva'][$key],
					'cantidad' => $_POST['dpcantidad'][$key],
					'precio_unitario' => $_POST['dpprecio_unitario'][$key],
					'importe' => $_POST['dpimporte'][$key],
					'importe_iva' => $_POST['dpimporte_iva'][$key],
					'total' => ($_POST['dpimporte'][$key]+$_POST['dpimporte_iva'][$key]),
				);
			}

			if(count($data_productos) > 0){
				if($tipo != 'add' && $this->input->post('dpupdate') == 'si'){
					$this->db->delete('compras_productos', "id_compra = '".$id_compra."'");
				}
				//se insertan los productos de la compra	
				$this->db->insert_batch('compras_productos', $data_productos);
			}
			
			return array(true, '');
		}
		return array(false, '');
	}
	/**
	 * Obtiene los productos base que consume un producto que se registrara en la orden
	 * @param unknown_type $id_producto
	 * @param unknown_type $cantidad
	 * @param unknown_type $pu
	 */
	public function productosInventario($id_producto, $cantidad, $pu=null){
		$res = $this->db->query("SELECT tipo FROM productos WHERE id_producto = '".$id_producto."'");
		$data = $res->row();
		if($data->tipo == 'base'){
			if($pu == null){
				$res = $this->db->query("SELECT pu_ultima_compra('".$id_producto."') AS pu;");
				$data = $res->row();
				$pu = $data->pu;
			}
			$productos[] = array('id_producto' => $id_producto, 'cantidad' => $cantidad, 'pu' => $pu);
		}else{
			$res = $this->db->query("SELECT id_producto_c, cantidad FROM productos_consumos WHERE id_producto = '".$id_producto."'");
			foreach($res->result() as $row){
				$data_pro = $this->productosInventario($row->id_producto_c, $row->cantidad);
				foreach($data_pro as $pro){
					$pro['cantidad'] *= $cantidad;
					$productos[] = $pro;
				}
			}
		}
		return $productos;
	}
	
	/**
	 * Agrega abono a una compra o gasto
	 * @param unknown_type $id_compra
	 * @param unknown_type $concepto
	 */
	public function addAbono($id_compra=null, $concepto=null){
		$id_compra = $id_compra==null? $this->input->get('id'): $id_compra;
		$concepto = $concepto==null? $this->input->post('dconcepto'): $concepto;
		
		$data = $this->obtenTotalAbonosC($id_compra);
		if($data->abonos < $data->total){ //Evitar que se agreguen abonos si esta pagada
			$pagada = false;
			//compruebo si se pasa el abono al total de la factura y activa a pagado
			if(($this->input->post('dmonto')+$data->abonos) >= $data->total){
				if(($this->input->post('dmonto')+$data->abonos) > $data->total)
					$_POST['dmonto'] = $this->input->post('dmonto') - (($this->input->post('dmonto')+$data->abonos) - $data->total);
				$pagada = true;
			}
			
			$id_abono = BDUtil::getId();
			$data_abono = array(
				'id_abono' => $id_abono,
				'id_compra' => $id_compra,
				'fecha' => $this->input->post('dfecha'),
				'concepto' => $concepto,
				'total' => $this->input->post('dmonto')
			);
			$this->db->insert('compras_abonos', $data_abono);
			
			if($pagada){
				$this->db->update('compras', array('status' => 'pa'), "id_compra = '".$id_compra."'");
			}			
			return array(true, 'Se agregó el abono correctamente.', $id_abono);
		}
		return array(true, 'La compra ya esta pagada.', '');
	}
	
	public function deleteAbono($id_abono, $id_compra){
		$this->db->delete('compras_abonos', "id_abono = '".$id_abono."'");
		
		$data = $this->obtenTotalAbonosC($id_compra);
		if($data->abonos >= $data->total){ //si abonos es = a la factura se pone pagada
			$this->db->update('compras', array('status' => 'pa'), "id_compra = '".$id_compra."'");
		}else{ //si abonos es menor se pone pendiente
			$this->db->update('compras', array('status' => 'p'), "id_compra = '".$id_compra."'");
		}
		
		return array(true, '');
	}
	
	private function obtenTotalAbonosC($id){
		$data = $this->db->query("
			SELECT
				c.total,
				COALESCE(ab.abonos,0) AS abonos
			FROM compras AS c
				LEFT JOIN (
					SELECT id_compra, Sum(total) AS abonos
					FROM compras_abonos
					WHERE id_compra = '".$id."' AND tipo <> 'ca'
					GROUP BY id_compra
				) AS ab ON c.id_compra = ab.id_compra
			WHERE c.id_compra = '".$id."'", true);
		return $data->row();
	}
	
	public function getTotalVuelosAjax(){
		$response = array();
	
		foreach ($_POST as $v){
			$res = $this->db->query("
					SELECT v.id_vuelo, 1 as cantidad, CASE WHEN iva_piloto=0 THEN 0 ELSE 0.16 END as taza_iva, 
							v.costo_piloto as precio_unitario, v.costo_piloto as importe, v.iva_piloto as importe_iva, (v.costo_piloto+v.iva_piloto) as total  
					FROM vuelos as v
					WHERE v.id_vuelo = '{$v['id_vuelo']}'
					");
	
					if($res->num_rows()>0)
						foreach ($res->result() as $itm)
							$response['vuelos'][] = $itm;
		}
		
		if(count($response['vuelos'])>0){
			$array_vuelos = array();
			foreach ($response['vuelos'] as $v){
				if(array_key_exists($v->id_vuelo, $array_vuelos)){
					$array_vuelos[$v->id_vuelo]['cantidad'] += 1;
					$array_vuelos[$v->id_vuelo]['importe'] = String::float($array_vuelos[$v->id_vuelo]['cantidad']*$array_vuelos[$v->id_vuelo]['p_uni']);
				}
				else{
					$array_vuelos[$v->id_vuelo]['id_vuelo'] = $v->id_vuelo;
					$array_vuelos[$v->id_vuelo]['cantidad'] = 1;
					$array_vuelos[$v->id_vuelo]['taza_iva'] = String::float($v->taza_iva);
					$array_vuelos[$v->id_vuelo]['p_uni'] = String::float($v->precio_unitario);
					$array_vuelos[$v->id_vuelo]['importe'] = String::float($v->precio_unitario);
					$array_vuelos[$v->id_vuelo]['importe_iva'] = String::float($v->importe_iva);
					$array_vuelos[$v->id_vuelo]['total'] = String::float($v->total);
				}
			}
			$response['tipos_v'] = $array_vuelos;
		}
		return $response;
	}
	
	public function addGastoPiloto(){
		$id_gasto = BDUtil::getId();
		$data = array(
				'id_compra'		=> $id_gasto,
				'id_proveedor'	=> $this->input->post('tpiloto'),
				'id_empleado'	=> $_SESSION['id_empleado'],
				'serie'			=> mb_strtoupper($this->input->post('tserie'), 'utf-8'),
				'folio'			=> $this->input->post('tfolio'),
				'fecha'			=> $this->input->post('tfecha'),
				'subtotal'		=> $this->input->post('subtotal'),
				'importe_iva'	=> $this->input->post('iva'),
				'total'			=> $this->input->post('total'),
				'concepto'		=> $this->input->post('tconcepto'),
				'is_gasto'		=> 't',
				'status'		=> 'pa',
		);
	
		$this->db->insert('compras',$data);
	
		foreach ($_POST as $vuelo){
			if(is_array($vuelo)){
				$data_v = array(
						'id_compra'	=> $id_gasto,
						'id_vuelo'	=> $vuelo['id_vuelo'],
						'cantidad'	=> String::float($vuelo['cantidad']),
						'taza_iva'	=> String::float($vuelo['taza_iva']),
						'precio_unitario'	=> String::float($vuelo['precio_unitario']),
						'importe'			=> String::float($vuelo['importe']),
						'importe_iva'		=> String::float($vuelo['importe_iva']),
						'total'				=> String::float($vuelo['total'])
				);
				$this->db->insert('compras_gastos_vuelos',$data_v);
			}
		}
		return array(true);
	}
	
	
}