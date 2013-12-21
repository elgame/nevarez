<?php
class listas_precio_model extends CI_Model{
	
	function __construct(){
		parent::__construct();
	}
	
	/**
	 * Obtiene las listas de precios que hay, id, nombre
	 */
	public function obtenListasPrecio($sql="(1 = 1)"){
		$res = $this->db
			->select('id_lista, nombre, es_default')
			->from('productos_listas')
			->where($sql)
		->get();
		$response = array();
		if($res->num_rows() > 0)
			$response = $res->result();
		
		return $response;
	}
	
	/**
	 * Obtiene el listado de precios para los productos de una familia y una lista de precios
	 * @param unknown_type $id_familia
	 * @param unknown_type $id_lista
	 * @param unknown_type $orderby
	 */
	public function getPreciosProductosLista($id_familia=null, $id_lista=null, $orderby='ORDER BY p.codigo ASC', $paginar=true){
		$sql = $sql1 = '';
		//paginacion
		if($paginar){
			$params = array(
					'result_items_per_page' => '30',
					'result_page' => (isset($_GET['pag'])? $_GET['pag']: 0)
			);
			if($params['result_page'] % $params['result_items_per_page'] == 0)
				$params['result_page'] = ($params['result_page']/$params['result_items_per_page']);
		}
	
		//filtro de familia
		if($id_familia!=null){
			$sql = "WHERE id_familia = '".$id_familia."'";
			$sql1 = " AND id_familia = '".$id_familia."'";
		}
		//filtro de lista
		if($id_lista!=null)
			$sql .= ($sql==''? 'WHERE ': ' AND ')."id_lista = '".$id_lista."'";
		//Filtros para buscar
		if($this->input->get('fnombre') != '')
			$sql1 .= " AND lower(nombre) LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%'";
		
		$query = "
				SELECT p.id_producto, p.id_familia, lp.id_lista, p.codigo, p.nombre, lp.precio
				FROM (
						SELECT id_producto, id_familia, codigo, nombre 
						FROM productos
						WHERE status = 'ac' ".$sql1."
					) AS p 
					LEFT JOIN 
					(
						SELECT * 
						FROM listas_precio 
						".$sql."
					) AS lp ON p.id_producto = lp.id_producto
				".$orderby."
				";
		
		$response = array('productos' => array());
		if($paginar){
			$query = BDUtil::pagination($query, $params, true);
			$res = $this->db->query($query['query']);
			
			$response['total_rows'] 		= $query['total_rows'];
			$response['items_per_page'] 	= $params['result_items_per_page'];
			$response['result_page'] 		= $params['result_page'];
		}else
			$res = $this->db->query($query);
		
		if($res->num_rows() > 0)
			$response['productos'] = $res->result();
	
		return $response;
	}
	
	/**
	 * Agrega una lista de precios
	 */
	public function addLista(){
		$data = array(
			'id_lista' => BDUtil::getId(),
			'nombre' => $this->input->post('dnombre'),
			'es_default' => ($this->input->post('des_default')===false? 'f': 't')
		);
		$this->db->insert('productos_listas', $data);
		return array(true, '');
	}
	
	/**
	 * Cambia el precio de un producto para una lista de precios
	 * determinada, lo inserta o lo actualiza
	 */
	public function updatePrecioLista(){
		$sql_q = "id_lista = '".$_POST['id_lista']."' AND id_producto = '".$_POST['id_producto']."'";
		$num = $this->db
			->where($sql_q)
			->count_all_results('productos_listas_precios');
		if($num > 0)
			$this->db->update('productos_listas_precios', array('precio' => $_POST['precio']), $sql_q);
		else
			$this->db->insert('productos_listas_precios', array(
					'id_lista' => $_POST['id_lista'],
					'id_producto' => $_POST['id_producto'],
					'precio' => $_POST['precio']
				));
		
		return array(true, '');
	}
	
	/**
	 * Genero la tabla de productos y listas de precio en un array
	 * para mejor manejo
	 * @param unknown_type $listas
	 * @param unknown_type $idfamilia
	 * @param unknown_type $paginar
	 */
	public function createTblPrecios($listas, $idfamilia, $paginar=true, $sql="ORDER BY p.codigo ASC"){
		$productos = array(
	 		'productos' => array(),
	 		'total_rows' 		=> 0,
	 		'items_per_page' 	=> 30,
	 		'result_page' 		=> 0
		 );
		$row1 = 0;
		$col1 = 2;
		$htds[0][0] = 'Código';
		$htds[0][1] = 'Nombre';
		foreach($listas as $lis){
			$htds[0][$col1] = $lis->nombre;
		
			$row1 = 1;
			$productos = $this->getPreciosProductosLista($idfamilia, $lis->id_lista, $sql, $paginar);
			foreach($productos['productos'] as $prod){
				$htds[$row1][0] = $prod->codigo;
				$htds[$row1][1] = $prod->nombre;
				$htds[$row1][$col1] = $prod->id_producto.'|'.$prod->precio.'|'.$lis->id_lista;
				++$row1;
			}
			++$col1;
		}
		unset($productos['productos']);
		
		return array('tabla' => $htds, 'pag' => $productos);
	}
	
}