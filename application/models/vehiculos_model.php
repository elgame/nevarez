<?php
class vehiculos_model extends privilegios_model{
	
	function __construct(){
		parent::__construct();
	}
	
	public function getVehiculos($id_vehiculo=false, $order = 'nombre ASC'){
		($id_vehiculo) ? $this->db->where('id_vehiculo',$id_vehiculo) : null;
		$order = (!empty($order)) ? $order : 'nombre ASC';
		$this->db->where('status','ac');
		$this->db->like('lower(nombre)',mb_strtolower($this->input->get('fnombre'), 'UTF-8'));
		$this->db->order_by($order);
		$data = $this->db->get('vehiculos')->result();
		return $data;
	}
	
	public function addVehiculo(){
		if($this->db->select('id_vehiculo')->from('vehiculos')->where(array('nombre'=>$this->input->post('fnombre'),'placas'=>$this->input->post('fplacas')))->get()->num_rows()<1)
		{
			$id_vehiculo	= BDUtil::getId();
			$data	= array(
					'id_vehiculo'	=> $id_vehiculo,
					'nombre'		=> $this->input->post('fnombre'),
					'placas'		=> $this->input->post('fplacas'),
					'modelo'		=> $this->input->post('fmodelo'),
					'numero_serie'	=> $this->input->post('fnumserie'),
					'color'			=> strtolower($this->input->post('fcolor'))
			);
			$this->db->insert('vehiculos',$data);
			return array(true);
		}
		return array(false);
	}
	
	public function editVehiculo($id_vehiculo){
		$data	= array(
				'nombre'		=> $this->input->post('fnombre'),
				'placas'		=> $this->input->post('fplacas'),
				'modelo'		=> $this->input->post('fmodelo'),
				'numero_serie'	=> $this->input->post('fnumserie'),
				'color'			=> strtolower($this->input->post('fcolor'))
		);
		$this->db->where('id_vehiculo',$id_vehiculo);
		$this->db->update('vehiculos',$data);
	
		return array(true);
	}
	
	public function delVehiculo($id_vehiculo){
		if($this->db->select('id_vehiculo')->from('vehiculos')->where(array('id_vehiculo'=>$id_vehiculo,'status'=>'ac'))->get()->num_rows()==1){
			$this->db->update('vehiculos',array('status'=>'e'),array('id_vehiculo'=>$id_vehiculo));
			return array(true);
		}
		return array(false);
	}
	
	public function ajax_get_vehiculos(){
		$sql = '';
		$res = $this->db->query("
				SELECT id_vehiculo as id, (nombre || ' (' || placas || ')') as nombre
				FROM vehiculos
				WHERE status='ac' AND lower(placas) LIKE '%".mb_strtolower($this->input->get('term'), 'UTF-8')."%'
				ORDER BY nombre ASC
				LIMIT 20
				");
	
		$response = array();
		if($res->num_rows() > 0){
			foreach($res->result() as $itm){
				$response[] = array(
						'id' => $itm->id,
						'label' => $itm->nombre,
						'value' => $itm->nombre,
						'item' => $itm,
				);
			}
		}
	
		return $response;
	}
	
	
}