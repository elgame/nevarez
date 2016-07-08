<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class clientes extends MY_Controller {
	/**
	 * Evita la validacion (enfocado cuando se usa ajax). Ver mas en privilegios_model
	 * @var unknown_type
	 */
	private $excepcion_privilegio = array('clientes/ajax_frmAddGrupo/', 'clientes/ajax_frmAddSucu/',
			'productos/ajax_productos_tbl/', 'productos/ajax_productos_addmod/','clientes/ajax_get_clientes/');
	
	public function _remap($method){
		$this->carabiner->css(array(
				array('libs/jquery-ui.css', 'screen'),
				array('libs/ui.notify.css', 'screen'),
				array('libs/jquery.treeview.css', 'screen'),
				array('base.css', 'screen')
		));
		$this->carabiner->js(array(
				array('libs/jquery.min.js'),
				array('libs/jquery-ui.js'),
				array('libs/jquery.notify.min.js'),
				array('libs/jquery.treeview.js'),
				array('general/alertas.js')
		));
		
		$this->load->model("empleados_model");
		if($this->empleados_model->checkSession()){
			$this->empleados_model->excepcion_privilegio = $this->excepcion_privilegio;
			$this->info_empleado = $this->empleados_model->getInfoEmpleado($_SESSION['id_empleado'], true);
			if($this->empleados_model->tienePrivilegioDe('', get_class($this).'/'.$method.'/')){
				$this->{$method}();
			}else
				redirect(base_url('panel/home?msg=1'));
		}else
			redirect(base_url('panel/home'));
	}
	
	/**
	 * Default. Mustra el listado de proveedores para administrarlos
	 */
	public function index(){
		$this->carabiner->css(array(
			array('libs/jquery.msgbox.css', 'screen'),
			array('general/forms.css', 'screen'),
			array('general/tables.css', 'screen')
		));
		$this->carabiner->js(array(
			array('libs/jquery.msgbox.min.js'),
			array('general/msgbox.js')
		));
		$this->load->model('clientes_model');
		$this->load->library('pagination');
		
		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['opcmenu_active'] = 'Clientes'; //activa la opcion del menu
		$params['seo'] = array(
			'titulo' => 'Administrar Clientes'
		);
		
		$params['clientes'] = $this->clientes_model->getClientes();
		
// 		echo '<pre>';
// 		var_dump($params['clientes']['clientes']);
// 		echo count($params['clientes']['clientes']);
// 		echo '</pre>';
// 		exit;
		
		if(isset($_GET['msg']{0}))
			$params['frm_errors'] = $this->showMsgs($_GET['msg']);
		
		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/clientes/listado', $params);
		$this->load->view('panel/footer');
	}
	
	/**
	 * Agrega un cliente a la bd
	 */
	public function agregar(){
		$this->carabiner->css(array(
			array('general/forms.css', 'screen'),
			array('general/tables.css', 'screen')
		));
		$this->carabiner->js(array(
			array('libs/jquery.numeric.js'),
			array('clientes/frm_addmod.js')
		));
		
		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['seo'] = array(
			'titulo' => 'Agregar Cliente'
		);
		$params['opcmenu_active'] = 'Clientes'; //activa la opcion del menu
		
		$this->configAddModEmpl();
		$this->load->model('listas_precio_model');
		
		if($this->form_validation->run() == FALSE){
			$params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
		}else{
			$this->load->model('clientes_model');
				$respons = $this->clientes_model->addCliente();
			
			if($respons[0])
				redirect(base_url('panel/clientes/agregar/?'.String::getVarsLink(array('msg')).'&msg='.$respons[2]));
		}
		
		$params['listas'] = $this->listas_precio_model->obtenListasPrecio();
		
		if(isset($_GET['msg']{0}))
			$params['frm_errors'] = $this->showMsgs($_GET['msg']);
		
		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$params['html_form'] = $this->load->view('panel/clientes/frmAgregarSucu', $params, true);
		$this->load->view('panel/clientes/agregar', $params);
		$this->load->view('panel/footer');
	}
	
	/**
	 * Modificar una sucursal a un cliente a la bd
	 */
	public function modificar(){
		$this->carabiner->css(array(
				array('libs/jquery.msgbox.css', 'screen'),
				array('general/forms.css', 'screen'),
				array('general/tables.css', 'screen')
		));
		$this->carabiner->js(array(
				array('libs/jquery.msgbox.min.js'),
				array('general/msgbox.js'),
				array('libs/jquery.numeric.js'),
				array('clientes/frm_addmod.js')
		));
	
		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['seo'] = array(
				'titulo' => 'Modificar Cliente'
		);
		$params['opcmenu_active'] = 'Clientes'; //activa la opcion del menu
	
		if(isset($_GET['id']{
			0})){
				$this->configAddModEmpl();
				$this->load->model('listas_precio_model');
				$this->load->model('clientes_model');
	
				if($this->form_validation->run() == FALSE){
					$params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
				}else{
					$respons = $this->clientes_model->updateCliente();
						
					if($respons[0])
						redirect(base_url('panel/clientes/modificar/?'.String::getVarsLink(array('msg')).'&msg='.$respons[2]));
				}
	
				$params['listas'] = $this->listas_precio_model->obtenListasPrecio();
				$params['info'] = $this->clientes_model->getInfoCliente($_GET['id']);
			}else
				$params['frm_errors'] = $this->showMsgs(1);
	
			if(isset($_GET['msg']{
				0}))
					$params['frm_errors'] = $this->showMsgs($_GET['msg']);
	
				$this->load->view('panel/header', $params);
				$this->load->view('panel/general/menu', $params);
				$this->load->view('panel/clientes/modificar_sucursal', $params);
				$this->load->view('panel/footer');
	}
	
	/**
	 * Elimina a un cliente, cambia el status a "e":eliminado
	 */
	public function eliminar(){
		if(isset($_GET['id']{0})){
			$this->load->model('clientes_model');
			$respons = $this->clientes_model->eliminarCliente();
			if($respons[0])
				redirect(base_url('panel/clientes/?msg=5'));
		}else
			$params['frm_errors'] = $this->showMsgs(1);
	}
	
	/*********** CONTACTOS **************/
	/**
	 * Agrega un nuevo contacto a un cliente (sucursal) utilizando Ajax
	 */
	public function agregar_contacto(){
		if(isset($_GET['id']{
			0})){
				$this->load->library('form_validation');
				$rules[] = array('field'	=> 'dcnombre',
						'label'		=> 'Contacto Nombre',
						'rules'		=> 'required|max_length[120]');
				$rules[] = array('field'	=> 'dcpuesto',
						'label'		=> 'Contacto Puesto',
						'rules'		=> 'max_length[20]');
				$rules[] = array('field'	=> 'dctelefono',
						'label'		=> 'Contacto Teléfono',
						'rules'		=> 'max_length[15]');
				$rules[] = array('field'	=> 'dcextension',
						'label'		=> 'Contacto Extensión',
						'rules'		=> 'max_length[8]');
				$rules[] = array('field'	=> 'dccelular',
						'label'		=> 'Contacto Celular',
						'rules'		=> 'max_length[20]');
				$rules[] = array('field'	=> 'dcnextel',
						'label'		=> 'Contacto Nextel',
						'rules'		=> 'max_length[20]');
				$rules[] = array('field'	=> 'dcnextel_id',
						'label'		=> 'Contacto ID Nextel',
						'rules'		=> 'max_length[25]');
				$rules[] = array('field'	=> 'dcfax',
						'label'		=> 'Contacto Fax',
						'rules'		=> 'max_length[15]');
				$this->form_validation->set_rules($rules);
					
				if($this->form_validation->run() == FALSE){
					$params['msg'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
				}else{
					$this->load->model('clientes_model');
					$params['msg'] = $this->clientes_model->addContacto($_GET['id']);
					if($params['msg'][0]){
						$res = $this->db
						->select('*')
						->from('clientes_contacto')
						->where("id_contacto = '".$params['msg'][2]."'")
						->get();
						$params['info'] = $res->row();
						$params['msg'] = $this->showMsgs(6);
					}
				}
			}else
				$params['msg'] = $this->showMsgs(1);
	
			echo json_encode($params);
	}
	
	/**
	 * Elimina un contacto del un cliente utilizando Ajax
	 */
	public function eliminar_contacto(){
		if(isset($_GET['id']{
			0})){
				$this->load->model('clientes_model');
				$params['msg'] = $this->clientes_model->deleteContacto($_GET['id']);
				if($params['msg'][0])
					$params['msg'] = $this->showMsgs(7);
			}else
				$params['msg'] = $this->showMsgs(1);
	
			echo json_encode($params);
	}
	
	/**
	 * Obtiene lostado de clientes para el autocomplete, ajax
	 */
	public function ajax_get_clientes(){
		$this->load->model('clientes_model');
		$params = $this->clientes_model->getClientesAjax();
	
		echo json_encode($params);
	}	
	
	/**
	 * Configura los metodos de agregar y modificar
	 */
	private function configAddModEmpl(){
		$this->load->library('form_validation');
		$contacto = false;
		
			$rules = array(
				array('field'	=> 'dlista_precio',
						'label'	=> 'Lista de precio',
						'rules'	=> 'required|max_length[25]'),
				array('field'	=> 'dnombre_fiscal',
						'label'	=> 'Nombre Fiscal',
						'rules'	=> 'required|max_length[130]'),
				array('field'	=> 'drfc',
						'label'	=> 'RFC',
						'rules'	=> 'max_length[13]'),
				array('field'	=> 'dcalle',
						'label'	=> 'Calle',
						'rules'	=> 'max_length[60]'),
				array('field'	=> 'dno_exterior',
						'label'	=> 'No exterior',
						'rules'	=> 'max_length[7]'),
				array('field'	=> 'dno_interior',
						'label'	=> 'No interior',
						'rules'	=> 'max_length[7]'),
				array('field'	=> 'dcolonia',
						'label'	=> 'Colonia',
						'rules'	=> 'max_length[60]'),
				array('field'	=> 'dlocalidad',
						'label'	=> 'Localidad',
						'rules'	=> 'max_length[45]'),
				array('field'	=> 'dmunicipio',
						'label'	=> 'Municipio',
						'rules'	=> 'max_length[45]'),
				array('field'	=> 'destado',
						'label'	=> 'Estado',
						'rules'	=> 'max_length[45]'),
				array('field'	=> 'dcp',
						'label'	=> 'CP',
						'rules'	=> 'max_length[10]'),
				array('field'	=> 'dtelefono',
						'label'	=> 'Teléfono',
						'rules'	=> 'max_length[15]'),
				array('field'	=> 'dcelular',
						'label'	=> 'Celular',
						'rules'	=> 'max_length[20]'),
				array('field'	=> 'demail',
						'label'	=> 'Email',
						'rules'	=> 'valid_email|max_length[70]'),
				array('field'	=> 'dpag_web',
						'label'	=> 'Pag Web',
						'rules'	=> 'max_length[80]'),
				array('field'	=> 'dcomentarios',
						'label'	=> 'Comentarios',
						'rules'	=> 'max_length[400]'),
				array('field'	=> 'drecepcion_facturas',
						'label'	=> 'Recepción facturas',
						'rules'	=> 'max_length[10]'),
				array('field'	=> 'ddias_pago',
						'label'	=> 'Dias pago',
						'rules'	=> 'max_length[10]'),
				array('field'	=> 'ddias_credito',
						'label'	=> 'Dias credito',
						'rules'	=> 'max_length[4]'),
				array('field'	=> 'ddescuento',
						'label'	=> 'Descuento',
						'rules'	=> 'numeric|max_length[3]'),
				array('field'	=> 'dretencion',
						'label'	=> 'Retención',
						'rules'	=> 'numeric|max_length[5]'),
				
				array('field'	=> 'denombre',
						'label'	=> 'Nombre',
						'rules'	=> 'max_length[130]'),
				array('field'	=> 'decalle',
						'label'	=> 'Calle',
						'rules'	=> 'max_length[60]'),
				array('field'	=> 'deno_exterior',
						'label'	=> 'No exterior',
						'rules'	=> 'max_length[7]'),
				array('field'	=> 'deno_interior',
						'label'	=> 'No interior',
						'rules'	=> 'max_length[7]'),
				array('field'	=> 'decolonia',
						'label'	=> 'Colonia',
						'rules'	=> 'max_length[60]'),
				array('field'	=> 'delocalidad',
						'label'	=> 'Localidad',
						'rules'	=> 'max_length[45]'),
				array('field'	=> 'demunicipio',
						'label'	=> 'Municipio',
						'rules'	=> 'max_length[45]'),
				array('field'	=> 'deestado',
						'label'	=> 'Estado',
						'rules'	=> 'max_length[45]'),
				array('field'	=> 'decp',
						'label'	=> 'CP',
						'rules'	=> 'max_length[10]'),
			);
			$contacto = true;
		
		if($contacto){
			//Contacto
			$condi_rul = '';
			if(isset($_POST['dcnombre']{0}))
				$condi_rul = 'required|';
				
			$rules[] = array('field'	=> 'dcnombre',
					'label'		=> 'Contacto Nombre',
					'rules'		=> $condi_rul.'max_length[120]');
			$rules[] = array('field'	=> 'dcpuesto',
					'label'		=> 'Contacto Puesto',
					'rules'		=> 'max_length[20]');
			$rules[] = array('field'	=> 'dctelefono',
					'label'		=> 'Contacto Teléfono',
					'rules'		=> 'max_length[15]');
			$rules[] = array('field'	=> 'dcextension',
					'label'		=> 'Contacto Extensión',
					'rules'		=> 'max_length[8]');
			$rules[] = array('field'	=> 'dccelular',
					'label'		=> 'Contacto Celular',
					'rules'		=> 'max_length[20]');
			$rules[] = array('field'	=> 'dcnextel',
					'label'		=> 'Contacto Nextel',
					'rules'		=> 'max_length[20]');
			$rules[] = array('field'	=> 'dcnextel_id',
					'label'		=> 'Contacto ID Nextel',
					'rules'		=> 'max_length[25]');
			$rules[] = array('field'	=> 'dcfax',
					'label'		=> 'Contacto Fax',
					'rules'		=> 'max_length[15]');
		}
		$this->form_validation->set_rules($rules);
	}
	
	
	/**
	 * Muestra mensajes cuando se realiza alguna accion
	 * @param unknown_type $tipo
	 * @param unknown_type $msg
	 * @param unknown_type $title
	 */
	private function showMsgs($tipo, $msg='', $title='Clientes!'){
		switch($tipo){
			case 1:
				$txt = 'El campo ID es requerido.';
				$icono = 'error';
			break;
			case 2: //Cuendo se valida con form_validation
				$txt = $msg;
				$icono = 'error';
			break;
			case 3:
				$txt = 'El cliente se agrego correctamente.';
				$icono = 'ok';
			break;
			case 4:
				$txt = 'El cliente se modifico correctamente.';
				$icono = 'ok';
				break;
			case 5:
				$txt = 'El cliente se elimino correctamente.';
				$icono = 'ok';
				break;
			case 6:
				$txt = 'El contacto se agrego correctamente.';
				$icono = 'ok';
			break;
			case 7:
				$txt = 'El contacto se elimino correctamente.';
				$icono = 'ok';
			break;
		}
		
		return array(
			'title' => $title,
			'msg' => $txt,
			'ico' => $icono);
	}
}

?>