<?php
class unidades_model extends CI_Model{
	
	function __construct(){
		parent::__construct();
	}
	
	/**
	 * Obtiene el listado de unidades
	 */
	public function getUnidades(){
		$res = $this->db->query("
			SELECT id_unidad, nombre, abreviatura
			FROM productos_unidades
			WHERE status = 'ac'
			ORDER BY nombre ASC
			");
		
		$response = array();
		if($res->num_rows() > 0)
			$response = $res->result();
		
		return $response;
	}
	
}