<?php
class productos_model extends CI_Model{
	
	function __construct(){
		parent::__construct();
	}
	
	/*************** Familias Productos *****************/
	/**
	 * Obtiene el listado de Familias
	 */
	public function getFamilias(){
		$res = $this->db->query("
				SELECT id_familia, codigo, nombre
				FROM productos_familias
				WHERE status = 'ac'
				ORDER BY codigo ASC
				");
		
		$response = array('familias' => array());
		if($res->num_rows() > 0)
			$response['familias'] = $res->result();
		
		return $response;
	}
	
	/**
	 * Obtiene el informacion de una Familia
	 */
	public function getInfoFamilia($id){
		$res = $this->db->query("
			SELECT id_familia, codigo, nombre, tipo
			FROM productos_familias
			WHERE id_familia = '".$id."' AND status = 'ac'
			LIMIT 1
		");
	
		$response = array();
		if($res->num_rows() > 0)
			$response = $res->result();
	
		return $response;
	}
	
	/**
	 * Agrega una familia a la bd
	 */
	public function addFamilia(){
		//reajusta a 2 digitos
		//$_POST['dcodigo'] = (strlen($_POST['dcodigo'])==1? '0': '').$_POST['dcodigo'];
		
		$data = array(
			'id_familia' => BDUtil::getId(),
			'codigo' => $this->input->post('dcodigo'),
			'nombre' => $this->input->post('dnombre'),
			'tipo'	=> $this->input->post('dtipo')
		);
		$this->db->insert('productos_familias', $data);
		return array(true, '');
	}
	
	/**
	 * Modifica una familia en la bd
	 */
	public function updateFamilia(){
		//reajusta a 2 digitos
		//$_POST['dcodigo'] = (strlen($_POST['dcodigo'])==1? '0': '').$_POST['dcodigo'];
	
		$data = array(
			'codigo' => $this->input->post('dcodigo'),
			'nombre' => $this->input->post('dnombre'),
			'tipo'	=> $this->input->post('dtipo')
		);
		$this->db->update('productos_familias', $data, "id_familia = '".$_GET['id']."'");
		
		//actualizamos el codigo
		$res = $this->db->query("
			SELECT id_producto, codigo, nombre
			FROM productos
			WHERE status = 'ac' AND id_familia = '".$_GET['id']."'");
		foreach($res->result() as $row){
			$codi = explode('-', $row->codigo);
			$codi = $this->input->post('dcodigo').'-'.$codi[1];
			$this->db->update('productos', array('codigo' => $codi), "id_producto = '".$row->id_producto."'");
		}
		
		return array(true, '');
	}
	
	/**
	 * Desactiva una familia y sus productos, pone el status en "e" eliminados
	 */
	public function desactivarFamilia(){
		$data = array(
			'status' => 'e'
		);
		$this->db->update('productos_familias', $data, "id_familia = '".$_GET['id']."'");
		$this->db->update('productos', $data, "id_familia = '".$_GET['id']."'");
		return array(true, '');
	}
	
	
	/**************** Productos ****************/
	/**
	 * Obtiene el listado de productos, si el parametro $_GET[id] existe, 
	 * el metodo regresa los productos que pertenecen a la familia,
	 * o el filtro por nombre con "fnombre" 
	 * @param unknown_type $per_page
	 * @param unknown_type $compara_id
	 * @param unknown_type $sql
	 */
	public function getProductosFamilia($per_page='30', $compara_id=true, $sql='', $orderby='codigo ASC'){
		//paginacion
		$params = array(
				'result_items_per_page' => $per_page,
				'result_page' => (isset($_GET['pag'])? $_GET['pag']: 0)
		);
		if($params['result_page'] % $params['result_items_per_page'] == 0)
			$params['result_page'] = ($params['result_page']/$params['result_items_per_page']);
	
		//filtro de familia
		if(isset($_GET['id']{0}) && $compara_id)
			$sql .= " AND id_familia = '".$_GET['id']."'";
		//Filtros para buscar
		if($this->input->get('fnombre') != '')
			$sql .= " AND lower(nombre) LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%'";
		
		$query = BDUtil::pagination("
				SELECT id_producto, codigo, nombre
				FROM productos
				WHERE status = 'ac' ".$sql."
				ORDER BY ".$orderby."
				", $params, true);
		$res = $this->db->query($query['query']);
	
		$response = array(
				'productos' => array(),
				'total_rows' 		=> $query['total_rows'],
				'items_per_page' 	=> $params['result_items_per_page'],
				'result_page' 		=> $params['result_page']
		);
		if($res->num_rows() > 0)
			$response['productos'] = $res->result();
	
		return $response;
	}
	
	/**
	 * Obtiene la informacion de un producto
	 * @param unknown_type $id
	 */
	public function getInfoProducto($id){
		$response = array();
		$res = $this->db
			->select('*')
			->from('productos')
			->where("id_producto = '".$id."'")
			->limit(1)
		->get();
		if($res->num_rows() > 0){
			$response['info'] = $res->row();
			$cod = explode('-', $response['info']->codigo);
			$response['info']->codigo = $cod[1];
		}
		//Productos q consume
		$res = $this->db
			->select('p.id_producto, p.nombre, pc.cantidad')
			->from('productos_consumos AS pc')
				->join('productos AS p', 'p.id_producto=pc.id_producto_c', 'inner')
			->where("pc.id_producto = '".$id."'")
		->get();
		if($res->num_rows() > 0)
			$response['consumo'] = $res->result();
		
		return $response;
	}
	
	/**
	 * Agrega un producto a la bd y los productos q consume
	 * @return multitype:boolean string
	 */
	public function addProducto(){
		/*//reajusta a 4 digitos
		for($i=strlen($_POST['dcodigo']); $i<4; ++$i)
			$_POST['dcodigo'] = '0'.$_POST['dcodigo'];*/
		$_POST['dcodigo'] = $this->input->post('codigo_familia').'-'.$_POST['dcodigo'];
		
		$tipo = is_array($this->input->post('dpcids'))? 'consumo': 'base';
		
		$id_producto = BDUtil::getId();
		$data = array(
			'id_producto' => $id_producto,
			'id_familia' => $this->input->get_post('familia'),
			'id_unidad' => $this->input->post('dunidad'),
			'codigo' => $this->input->post('dcodigo'),
			'nombre' => $this->input->post('dnombre'),
			'descripcion' => $this->input->post('ddescripcion'),
			'ubicacion' => $this->input->post('dubicacion'),
			'stock_min' => ($this->input->post('dstock_min')!=''? $this->input->post('dstock_min'): 0),
			'stock_max' => ($this->input->post('dstock_max')!=''? $this->input->post('dstock_max'): 0),
			'tipo' => $tipo
		);
		$this->db->insert('productos', $data);
		
		//Productos consumo
// 		if($tipo == 'consumo'){
// 			$data_pro = array();
// 			foreach($this->input->post('dpcids') as $key => $id){
// 				$data_pro[] = array(
// 					'id_producto' => $id_producto,
// 					'id_producto_c' => $id,
// 					'cantidad' => ($_POST['dpccantidad'][$key]!=''? $_POST['dpccantidad'][$key]: 0)
// 				);
// 			}
				
// 			if(count($data_pro) > 0)
// 				$this->db->insert_batch('productos_consumos', $data_pro);
// 		}
		
		return array(true, '');
	}
	
	/**
	 * Modifica un producto en la bd y los productos q consume
	 * @return multitype:boolean string
	 */
	public function updateProducto(){
		/*//reajusta a 4 digitos
		for($i=strlen($_POST['dcodigo']); $i<4; ++$i)
			$_POST['dcodigo'] = '0'.$_POST['dcodigo'];*/
		$_POST['dcodigo'] = $this->input->post('codigo_familia').'-'.$_POST['dcodigo'];
	
		$tipo = is_array($this->input->post('dpcids'))? 'consumo': 'base';
		
		$data = array(
				'id_familia' => $this->input->get_post('familia'),
				'id_unidad' => $this->input->post('dunidad'),
				'codigo' => $this->input->post('dcodigo'),
				'nombre' => $this->input->post('dnombre'),
				'descripcion' => $this->input->post('ddescripcion'),
				'ubicacion' => $this->input->post('dubicacion'),
				'stock_min' => ($this->input->post('dstock_min')!=''? $this->input->post('dstock_min'): 0),
				'stock_max' => ($this->input->post('dstock_max')!=''? $this->input->post('dstock_max'): 0),
				'tipo' => $tipo
		);
		$this->db->update('productos', $data, "id_producto = '".$_GET['id']."'");
	
		//Productos consumo
// 		if($tipo == 'consumo'){
// 			$this->db->delete('productos_consumos', "id_producto = '".$_GET['id']."'");
// 			$data_pro = array();
// 			foreach($this->input->post('dpcids') as $key => $id){
// 				$data_pro[] = array(
// 						'id_producto' => $_GET['id'],
// 						'id_producto_c' => $id,
// 						'cantidad' => ($_POST['dpccantidad'][$key]!=''? $_POST['dpccantidad'][$key]: 0)
// 				);
// 			}
	
// 			if(count($data_pro) > 0)
// 				$this->db->insert_batch('productos_consumos', $data_pro);
// 		}
	
		return array(true, '');
	}
	
	/**
	 * Desactiva un producto, pone el status en "e" eliminados
	 */
	public function desactivarProducto(){
		$data = array(
			'status' => 'e'
		);
		$this->db->update('productos', $data, "id_producto = '".$_GET['id']."'");
		return array(true, '');
	}
	
	/**
	 * Obtiene el listado de proveedores para usar ajax
	 */
	public function getProductosAjax(){		
		$sql = $order = '';
		if($this->input->get('tipo') == 'codigo'){
			$sql = "lower(p.codigo) LIKE '".mb_strtolower($this->input->get('term'), 'UTF-8')."%'";
			$order = "p.codigo";
			$val="codigo";
		}else{
			$sql = "lower(p.nombre) LIKE '%".mb_strtolower($this->input->get('term'), 'UTF-8')."%'";
			$order = "p.nombre";
			$val="nombre";
		}
	
		$precio = '';
		if(isset($_GET['cliente']{0})){
			$precio = ", get_precio_producto('".$this->input->get('cliente')."', p.id_producto) AS precio";
		}
		
		$tipo_familia = "AND pf.tipo<>'venta'";
		if(isset($_GET['asig'])){ // Si entra aqui se filtrarian los producto para la familia deseada por ejemplo: Avion, Trabajadores, Vehiculos
			if($_GET['asig']!='ni'){
				switch($_GET['asig']){
					case 'av':
						$tipo = 'avion';
						break;
					case 'tr':
						$tipo = 'trabajador';
						break;
					case 've':
						$tipo = 'vehiculo';
						break;
				}
				$tipo_familia = "AND pf.tipo='$tipo'";
			}	
		}
	
		$res = $this->db->query("
				SELECT p.id_producto, p.codigo, p.nombre".$precio.", pu_ultima_compra(p.id_producto) as pu
				FROM productos as p
				INNER JOIN productos_familias as pf ON p.id_familia=pf.id_familia $tipo_familia
				WHERE p.status = 'ac' AND ".$sql."
				ORDER BY ".$order." ASC
				LIMIT 100");

		$response = array();
		if($res->num_rows() > 0){
			foreach($res->result() as $itm){
				$response[] = array(
						'id' => $itm->id_producto,
						'label' => $itm->{$val},
						'value' => $itm->{$val},
						'item' => $itm,
						);
			}
		}

		return $response;
	}
	
}