<?php

class pilotos_model extends privilegios_model{
	
	function __construct(){
		parent::__construct();
	}
	
	/**
	 * Obtiene el listado de proveedores
	 */
	public function getPilotos(){
		$sql = '';
		//paginacion
		$params = array(
				'result_items_per_page' => '30',
				'result_page' => (isset($_GET['pag'])? $_GET['pag']: 0)
		);
		if($params['result_page'] % $params['result_items_per_page'] == 0)
			$params['result_page'] = ($params['result_page']/$params['result_items_per_page']);
		
		//Filtros para buscar
		if($this->input->get('fnombre') != '')
			$sql = " AND lower(nombre) LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%'";
		if($this->input->get('fstatus') != 'todos'){
			$_GET['fstatus'] = $this->input->get('fstatus')==''? 'ac': $this->input->get('fstatus');
			$sql .= " AND lower(status) LIKE '".mb_strtolower($this->input->get('fstatus'), 'UTF-8')."'";
		}
		
		$query = BDUtil::pagination("
				SELECT id_proveedor, nombre, telefono, email, recepcion_facturas, dias_pago
				FROM proveedores
				WHERE tipo='pi'
				".$sql."
				ORDER BY nombre ASC
				", $params, true);
		$res = $this->db->query($query['query']);
		
		$response = array(
				'pilotos' => array(),
				'total_rows' 		=> $query['total_rows'],
				'items_per_page' 	=> $params['result_items_per_page'],
				'result_page' 		=> $params['result_page']
		);
		if($res->num_rows() > 0)
			$response['pilotos'] = $res->result();
		
		return $response;
	}
	
	/**
	 * Obtiene la informacion de un proveedor
	 */
	public function getInfoPiloto($id, $info_basic=false){
		$res = $this->db
			->select('*')
			->from('proveedores')
			->where("id_proveedor = '".$id."'")
		->get();
		if($res->num_rows() > 0){
			$response['info'] = $res->row();
			$res->free_result();
			if($info_basic)
				return $response;
			
			//contactos
			$res = $this->db
				->select('*')
				->from('proveedores_contactos_piloto')
				->where("id_proveedor = '".$id."'")
			->get();
			if($res->num_rows() > 0){
				$response['contactos'] = $res->result();
			}
			$res->free_result();
			
			return $response;
		}else
			return false;
	}
	
	/**
	 * Agrega un proveedor a la bd
	 */
	public function addPiloto(){
		$id_proveedor = BDUtil::getId();
		$data = array(
			'id_proveedor' => $id_proveedor,
			'nombre' => $this->input->post('dnombre'),
			'calle' => $this->input->post('dcalle'),
			'no_exterior' => $this->input->post('dno_exterior'),
			'no_interior' => $this->input->post('dno_interior'),
			'colonia' => $this->input->post('dcolonia'),
			'localidad' => $this->input->post('dlocalidad'),
			'municipio' => $this->input->post('dmunicipio'),
			'estado' => $this->input->post('destado'),
			'cp' => $this->input->post('dcp'),
			'telefono' => $this->input->post('dtelefono'),
			'celular' => $this->input->post('dcelular'),
			'email' => $this->input->post('demail'),
			'pag_web' => $this->input->post('dpag_web'),
			'comentarios' => $this->input->post('dcomentarios'),
			'recepcion_facturas' => $this->input->post('drecepcion_facturas'),
			'dias_pago' => $this->input->post('ddias_pago'),
			'dias_credito' => $this->input->post('ddias_credito'),
			'expide_factura' => ($this->input->post('dexpide_factura')==1) ? 'true' : 'false' ,
			'licencia_vehiculo' => $this->input->post('dlicencia_vehiculo'),
			'licencia_avion' => $this->input->post('dlicencia_avion'),
			'fecha_vence_seguro' => $this->input->post('dfecha_vence_seguro'),
			'precio_vuelo' => $this->input->post('dprecio_vuelo'),
			'tipo' => 'pi'
		);
		
		if($this->input->post('dvencimiento_licencia_v') != '')
			$data['vencimiento_licencia_v'] = $this->input->post('dvencimiento_licencia_v');
		
		if($this->input->post('dvencimiento_licencia_a') != '')
			$data['vencimiento_licencia_a'] = $this->input->post('dvencimiento_licencia_a');
		
		if($this->input->post('dfecha_nacimiento') != '')
			$data['fecha_nacimiento'] = $this->input->post('dfecha_nacimiento');
		
		$this->db->insert('proveedores', $data);
		
		//Contacto
		if(isset($_POST['dcnombre']{0})){
			$this->addContacto($id_proveedor);
		}
		
		return array(true, '');
	}
	
	/**
	 * Modifica la info de un proveedor a la bd
	 */
	public function updatePiloto(){
		$data = array(
			'nombre' => $this->input->post('dnombre'),
			'calle' => $this->input->post('dcalle'),
			'no_exterior' => $this->input->post('dno_exterior'),
			'no_interior' => $this->input->post('dno_interior'),
			'colonia' => $this->input->post('dcolonia'),
			'localidad' => $this->input->post('dlocalidad'),
			'municipio' => $this->input->post('dmunicipio'),
			'estado' => $this->input->post('destado'),
			'cp' => $this->input->post('dcp'),
			'telefono' => $this->input->post('dtelefono'),
			'celular' => $this->input->post('dcelular'),
			'email' => $this->input->post('demail'),
			'pag_web' => $this->input->post('dpag_web'),
			'comentarios' => $this->input->post('dcomentarios'),
			'recepcion_facturas' => $this->input->post('drecepcion_facturas'),
			'dias_pago' => $this->input->post('ddias_pago'),
			'dias_credito' => $this->input->post('ddias_credito'),
			'expide_factura' => ($this->input->post('dexpide_factura')==1) ? 'true' : 'false' ,
			'licencia_vehiculo' => $this->input->post('dlicencia_vehiculo'),
			'licencia_avion' => $this->input->post('dlicencia_avion'),
			'fecha_vence_seguro' => $this->input->post('dfecha_vence_seguro'),
			'precio_vuelo' => $this->input->post('dprecio_vuelo')
		);
		
		if($this->input->post('dvencimiento_licencia_v') != '')
			$data['vencimiento_licencia_v'] = $this->input->post('dvencimiento_licencia_v');
		
		if($this->input->post('dvencimiento_licencia_a') != '')
			$data['vencimiento_licencia_a'] = $this->input->post('dvencimiento_licencia_a');
		
		if($this->input->post('dfecha_nacimiento') != '')
			$data['fecha_nacimiento'] = $this->input->post('dfecha_nacimiento');
		
		$this->db->update('proveedores', $data, "id_proveedor = '".$_GET['id']."'");
	
		return array(true, '');
	}
	
	/**
	 * Elimina a un proveedor, cambia su status a "e":eliminado
	 */
	public function delPiloto(){
		$this->db->update('proveedores', array('status' => 'e'), "id_proveedor = '".$_GET['id']."'");
		return array(true, '');
	}
	
	/**
	 * Agrega contactos al proveedor
	 * @param unknown_type $id_proveedor
	 */
	public function addContacto($id_proveedor=null){
		$id_proveedor = $id_proveedor==null? $this->input->post('id'): $id_proveedor;
		
		$id_conta = BDUtil::getId();
		$data = array(
			'id_contacto' => $id_conta,
			'id_proveedor' => $id_proveedor,
			'nombre' => $this->input->post('dcnombre'),
			'domicilio' => $this->input->post('dcdomicilio'),
			'municipio' => $this->input->post('dcmunicipio'),
			'estado' => $this->input->post('dcestado'),
			'telefono' => $this->input->post('dctelefono'),
			'celular' => $this->input->post('dccelular')
		);
		$this->db->insert('proveedores_contactos_piloto', $data);
		return array(true, 'Se agregó el contacto correctamente.', $id_conta);
	}
	/**
	 * Elimina un contacto de un proveedore de la bd
	 * @param unknown_type $id_contacto
	 */
	public function delContacto($id_contacto){
		$this->db->delete('proveedores_contactos_piloto', "id_contacto = '".$id_contacto."'");
		return array(true, '');
	}
	
	public function getPilotosAjax(){
		$sql = '';
		$res = $this->db->query("
				SELECT id_proveedor, nombre, calle, no_exterior, no_interior, colonia, localidad, municipio, estado, cp, telefono, dias_credito, licencia_avion, vencimiento_licencia_a, precio_vuelo
				FROM proveedores
				WHERE status = 'ac' AND tipo='pi' AND lower(nombre) LIKE '%".mb_strtolower($this->input->get('term'), 'UTF-8')."%'
				ORDER BY nombre ASC");
	
		$response = array();
		if($res->num_rows() > 0){
			foreach($res->result() as $itm){
				$response[] = array(
						'id' => $itm->id_proveedor,
						'label' => $itm->nombre,
						'value' => $itm->nombre,
						'item' => $itm,
				);
			}
		}
	
		return $response;
	}
	
	
}