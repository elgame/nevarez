<?php

class clientes_model extends CI_Model{

	function __construct(){
		parent::__construct();
	}

	/**
	 * Obtiene el listado de proveedores
	 */
	public function getClientes(){
		$sql = '';
		//paginacion
		$params = array(
				'result_items_per_page' => '20',
				'result_page' => (isset($_GET['pag'])? $_GET['pag']: 0)
		);
		if($params['result_page'] % $params['result_items_per_page'] == 0)
			$params['result_page'] = ($params['result_page']/$params['result_items_per_page']);

		//Filtros para buscar
		if($this->input->get('fnombre') != '')
			$sql = " AND lower(nombre_fiscal) LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%'";

// 		if($this->input->get('fstatus') != 'todos'){
// 			$_GET['fstatus'] = $this->input->get('fstatus')==''? 'ac': $this->input->get('fstatus');
// 			$sql .= ($sql==''? 'WHERE': ' AND')." lower(status) LIKE '".mb_strtolower($this->input->get('fstatus'), 'UTF-8')."'";
// 		}

		$query = BDUtil::pagination("
				SELECT id_cliente, nombre_fiscal, telefono, email, dias_pago, recepcion_facturas
				FROM clientes
				WHERE status = 'ac'
				".$sql."
				ORDER BY nombre_fiscal ASC
				", $params, true);
		$res = $this->db->query($query['query']);

		$response = array(
			'clientes' 			=> array(),
			'total_rows' 		=> $query['total_rows'],
			'items_per_page' 	=> $params['result_items_per_page'],
			'result_page' 		=> $params['result_page']
		);
		$response['clientes'] = $res->result();
		return $response;
	}

	/**
	 * Obtiene la informacion de un cliente
	 */
	public function getInfoCliente($id, $info_basic=false){
		$res = $this->db
			->select('*')
			->from('clientes AS c')
			->where("c.id_cliente = '".$id."'")
		->get();
		if($res->num_rows() > 0){
			$response['info'] = $res->row();
			$res->free_result();
			if($info_basic)
				return $response;

			//info extra
			$res = $this->db
				->select('*')
				->from('clientes_extra')
				->where("id_cliente = '".$id."'")
			->get();
			if($res->num_rows() > 0){
				$response['info_extra'] = $res->row();
			}
			$res->free_result();

			//contactos
			$res = $this->db
				->select('*')
				->from('clientes_contacto')
				->where("id_cliente = '".$id."'")
			->get();
			if($res->num_rows() > 0){
				$response['contactos'] = $res->result();
			}
			$res->free_result();

      //cuentas
      $res = $this->db
        ->select('*')
        ->from('clientes_cuentas')
        ->where("id_cliente = '".$id."'")
      ->get();
      $response['cuentas'] = [];
      if($res->num_rows() > 0){
        $response['cuentas'] = $res->result();
      }
      $res->free_result();

			return $response;
		}else
			return false;
	}

	/**
	 * Agrega la informacion de una sucursal de una empresa, o la info de una empresa
	 * sin sucursales
	 * @param unknown_type $sucu
	 */
	public function addCliente($sucu=false){

		$id_cliente = BDUtil::getId();
		$data = array(
			'id_cliente' => $id_cliente,
			'id_lista_precio' => $this->input->post('dlista_precio'),
			'nombre_fiscal' => $this->input->post('dnombre_fiscal'),
			'calle' => $this->input->post('dcalle'),
			'no_exterior' => $this->input->post('dno_exterior'),
			'no_interior' => $this->input->post('dno_interior'),
			'colonia' => $this->input->post('dcolonia'),
			'localidad' => $this->input->post('dlocalidad'),
			'municipio' => $this->input->post('dmunicipio'),
			'estado' => $this->input->post('destado'),
			'cp' => $this->input->post('dcp'),
			'rfc' => $this->input->post('drfc'),
// 			'sucursal' => $this->input->post('dsucursal'),
// 			'no_proveedor' => $this->input->post('dno_proveedor'),
			'telefono' => $this->input->post('dtelefono'),
			'celular' => $this->input->post('dcelular'),
			'email' => $this->input->post('demail'),
			'pag_web' => $this->input->post('dpag_web'),
			'comentarios' => $this->input->post('dcomentarios'),
			'recepcion_facturas' => $this->input->post('drecepcion_facturas'),
			'dias_pago' => $this->input->post('ddias_pago'),
			'dias_credito' => $this->input->post('ddias_credito'),
			'descuento' => floatval($this->input->post('ddescuento')),
			'retencion' => floatval($this->input->post('dretencion'))
		);
		$this->db->insert('clientes', $data);

		//Informacion Extra
		$data = array(
			'id_cliente' => $id_cliente,
			'nombre' => $this->input->post('denombre'),
			'calle' => $this->input->post('decalle'),
			'no_exterior' => $this->input->post('deno_exterior'),
			'no_interior' => $this->input->post('deno_interior'),
			'colonia' => $this->input->post('decolonia'),
			'localidad' => $this->input->post('delocalidad'),
			'municipio' => $this->input->post('demunicipio'),
			'estado' => $this->input->post('deestado'),
			'cp' => $this->input->post('decp')
		);
		$this->db->insert('clientes_extra', $data);

		//Contacto
		if(isset($_POST['dcnombre']{0})){
			$this->addContacto($id_cliente);
		}

    $this->saveCuentas($id_cliente);
		$msg = 3;
		return array(true, '', $msg);
	}

	/**
	 * Modifica la informacion de una sucursal de una empresa, o la info de una empresa
	 * sin sucursales
	 */
	public function updateCliente(){
		$msg = 4;
		$data = array(
			'id_lista_precio' => $this->input->post('dlista_precio'),
			'nombre_fiscal' => $this->input->post('dnombre_fiscal'),
			'calle' => $this->input->post('dcalle'),
			'no_exterior' => $this->input->post('dno_exterior'),
			'no_interior' => $this->input->post('dno_interior'),
			'colonia' => $this->input->post('dcolonia'),
			'localidad' => $this->input->post('dlocalidad'),
			'municipio' => $this->input->post('dmunicipio'),
			'estado' => $this->input->post('destado'),
			'cp' => $this->input->post('dcp'),
			'rfc' => $this->input->post('drfc'),
			'telefono' => $this->input->post('dtelefono'),
			'celular' => $this->input->post('dcelular'),
			'email' => $this->input->post('demail'),
			'pag_web' => $this->input->post('dpag_web'),
			'comentarios' => $this->input->post('dcomentarios'),
			'recepcion_facturas' => $this->input->post('drecepcion_facturas'),
			'dias_pago' => $this->input->post('ddias_pago'),
			'dias_credito' => $this->input->post('ddias_credito'),
			'descuento' => floatval($this->input->post('ddescuento')),
			'retencion' => floatval($this->input->post('dretencion'))
		);
		$this->db->update('clientes', $data, "id_cliente = '".$_GET['id']."'");

		//Informacion Extra
		$data = array(
			'nombre' => $this->input->post('denombre'),
			'calle' => $this->input->post('decalle'),
			'no_exterior' => $this->input->post('deno_exterior'),
			'no_interior' => $this->input->post('deno_interior'),
			'colonia' => $this->input->post('decolonia'),
			'localidad' => $this->input->post('delocalidad'),
			'municipio' => $this->input->post('demunicipio'),
			'estado' => $this->input->post('deestado'),
			'cp' => $this->input->post('decp')
		);
		$this->db->update('clientes_extra', $data, "id_cliente = '".$_GET['id']."'");

    $this->saveCuentas($_GET['id']);

		return array(true, '', $msg);
	}


	/**
	 * Elimina a un cliente, cambia su status a "e":eliminado
	 */
	public function eliminarCliente(){
		$this->db->update('clientes', array('status' => 'e'), "id_cliente = '".$_GET['id']."'");
		return array(true, '');
	}

	/**
	 * Agrega contactos al cliente
	 * @param unknown_type $id_sucursal
	 */
	public function addContacto($id_cliente=null){
		$id_cliente = $id_cliente==null? $this->input->post('id'): $id_cliente;

		$id_conta = BDUtil::getId();
		$data = array(
			'id_contacto' => $id_conta,
			'id_cliente' => $id_cliente,
			'nombre' => $this->input->post('dcnombre'),
			'puesto' => $this->input->post('dcpuesto'),
			'telefono' => $this->input->post('dctelefono'),
			'extension' => $this->input->post('dcextension'),
			'celular' => $this->input->post('dccelular'),
			'nextel' => $this->input->post('dcnextel'),
			'nextel_id' => $this->input->post('dcnextel_id'),
			'fax' => $this->input->post('dcfax')
		);
		$this->db->insert('clientes_contacto', $data);
		return array(true, 'Se agregó el contacto correctamente.', $id_conta);
	}
	/**
	 * Elimina un contacto de un cliente de la bd
	 * @param unknown_type $id_contacto
	 */
	public function deleteContacto($id_contacto){
		$this->db->delete('clientes_contacto', "id_contacto = '".$id_contacto."'");
		return array(true, '');
	}

  public function saveCuentas($id_cliente=null){
    $id_cliente = $id_cliente==null? $this->input->post('id'): $id_cliente;

    $this->db->delete('clientes_cuentas', "id_cliente = '{$id_cliente}'");
    if (is_array($this->input->post('ccaleas'))) {
      foreach ($this->input->post('ccaleas') as $key => $value) {
        $id_cuenta = BDUtil::getId();
        $data = array(
          'id_cuenta'  => $id_cuenta,
          'id_cliente' => $id_cliente,
          'aleas'      => $_POST['ccaleas'][$key],
          'banco_rfc'  => $_POST['ccrfcbanco'][$key],
          'cuenta'     => $_POST['cccuenta'][$key],
        );
        $this->db->insert('clientes_cuentas', $data);
      }
    }
    return array(true, 'Se agregó el contacto correctamente.', $id_conta);
  }

	/**
	 * Obtiene el listado de clientes para usar ajax
	 */
	public function getClientesAjax(){
		$term = mb_strtolower($this->input->get('term'), 'UTF-8');
		$sql = "AND lower(nombre_fiscal) LIKE '%".$term."%'";
		if ($term == 'xaxx010101000') {
			$sql = "AND lower(rfc) LIKE '%".$term."%'";
		}
		$res = $this->db->query("
				SELECT id_cliente, nombre_fiscal, calle, no_exterior, no_interior, colonia, localidad, municipio, estado, cp, telefono, dias_credito, rfc, retencion
				FROM clientes
				WHERE status = 'ac' ".$sql."
				ORDER BY nombre_fiscal ASC
				LIMIT 20");

		$response = array();
		if($res->num_rows() > 0){
			foreach($res->result() as $itm){
				$response[] = array(
						'id' => $itm->id_cliente,
						'label' => $itm->nombre_fiscal,
						'value' => $itm->nombre_fiscal,
						'item' => $itm,
				);
			}
		}

		return $response;
	}

  public function getCuentasClienteAjax(){
    $id = $this->input->get('id');
    $res = $this->db->query("
        SELECT id_cuenta, aleas, banco_rfc, cuenta
        FROM clientes_cuentas
        WHERE id_cliente = '{$id}'");

    $response = array();
    if($res->num_rows() > 0){
      $response = $res->result();
    }

    return $response;
  }

}