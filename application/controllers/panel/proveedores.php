<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class proveedores extends MY_Controller {
	/**
	 * Evita la validacion (enfocado cuando se usa ajax). Ver mas en privilegios_model
	 * @var unknown_type
	 */
	private $excepcion_privilegio = array('proveedores/ajax_get_proveedores/');
	
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
		$this->load->model('proveedores_model');
		$this->load->library('pagination');
		
		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['opcmenu_active'] = 'Proveedores'; //activa la opcion del menu
		$params['seo'] = array(
			'titulo' => 'Administrar proveedores'
		);
		
		$params['proveedores'] = $this->proveedores_model->getProveedores();
		
		if(isset($_GET['msg']{0}))
			$params['frm_errors'] = $this->showMsgs($_GET['msg']);
		
		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/proveedores/listado', $params);
		$this->load->view('panel/footer');
	}
	
	/**
	 * Agrega un proveedor a la bd
	 */
	public function agregar(){
		$this->carabiner->css(array(
			array('general/forms.css', 'screen'),
			array('general/tables.css', 'screen')
		));
		$this->carabiner->js(array(
			array('proveedores/frm_addmod.js')
		));
		
		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['seo'] = array(
			'titulo' => 'Agregar proveedor'
		);
		$params['opcmenu_active'] = 'Proveedores'; //activa la opcion del menu
		$this->configAddModEmpl('add');
		
		if($this->form_validation->run() == FALSE){
			$params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
		}else{
			$this->load->model('proveedores_model');
			$respons = $this->proveedores_model->addProveedor();
			
			if($respons[0])
				redirect(base_url('panel/proveedores/agregar/?'.String::getVarsLink(array('msg')).'&msg=4'));
		}
		
		if(isset($_GET['msg']{0}))
			$params['frm_errors'] = $this->showMsgs($_GET['msg']);
		
		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/proveedores/agregar', $params);
		$this->load->view('panel/footer');
	}
	
	/**
	 * Modifica la informacion de un proveedor
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
			array('proveedores/frm_addmod.js')
		));
		
		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['seo'] = array(
			'titulo' => 'Modificar proveedor'
		);
		$params['opcmenu_active'] = 'Proveedores'; //activa la opcion del menu
		
		if(isset($_GET['id']{0})){
			$this->configAddModEmpl('update');
			$this->load->model('proveedores_model');
			
			if($this->form_validation->run() == FALSE){
				$params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
			}else{
				$respons = $this->proveedores_model->updateProveedor();
			
				if($respons[0])
					redirect(base_url('panel/proveedores/modificar/?'.String::getVarsLink(array('msg')).'&msg=3'));
			}
			
			$params['proveedor'] = $this->proveedores_model->getInfoProveedor($_GET['id']);
			if(!is_array($params['proveedor']))
				unset($params['proveedor']);
			
			if(isset($_GET['msg']{0}))
				$params['frm_errors'] = $this->showMsgs($_GET['msg']);
		}else
			$params['frm_errors'] = $this->showMsgs(1);
		
		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/proveedores/modificar', $params);
		$this->load->view('panel/footer');
	}
	
	/**
	 * Elimina a un proveedor, cambia el status a "e":eliminado
	 */
	public function eliminar(){
		if(isset($_GET['id']{0})){
			$this->load->model('proveedores_model');
			$respons = $this->proveedores_model->eliminarProveedor();
			
			if($respons[0])
				redirect(base_url('panel/proveedores/?msg=5'));
		}else
			$params['frm_errors'] = $this->showMsgs(1);
	}
	
	
	
	
	/*********** CONTACTOS **************/
	/**
	 * Agrega un nuevo contacto a un proveedor utilizando Ajax
	 */
	public function agregar_contacto(){
		if(isset($_GET['id']{0})){
			$this->load->library('form_validation');
			$rules[] = array('field'	=> 'dcnombre',
					'label'		=> 'Contacto Nombre',
					'rules'		=> 'max_length[120]');
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
				$this->load->model('proveedores_model');
				$params['msg'] = $this->proveedores_model->addContacto($_GET['id']);
				if($params['msg'][0]){
					$res = $this->db
						->select('*')
						->from('proveedores_contacto')
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
	 * Elimina un contacto del proveedor utilizando Ajax
	 */
	public function eliminar_contacto(){
		if(isset($_GET['id']{0})){
			$this->load->model('proveedores_model');
			$params['msg'] = $this->proveedores_model->deleteContacto($_GET['id']);
			if($params['msg'][0])
				$params['msg'] = $this->showMsgs(7);
		}else
			$params['msg'] = $this->showMsgs(1);
		
		echo json_encode($params);
	}
	
	
	/**
	 * Obtiene lostado de proveedores para el autocomplete, ajax
	 */
	public function ajax_get_proveedores(){
		$this->load->model('proveedores_model');
		$params = $this->proveedores_model->getProveedoresAjax();
		
		echo json_encode($params);
	}
	
	
	
	/**
	 * Configura los metodos de agregar y modificar
	 */
	private function configAddModEmpl($tipo){
		$this->load->library('form_validation');
		$rules = array(
			array('field'	=> 'dnombre',
					'label'		=> 'Nombre',
					'rules'		=> 'required|max_length[120]'),
			array('field'	=> 'dcalle',
					'label'		=> 'Calle',
					'rules'		=> 'max_length[60]'),
			array('field'	=> 'dno_exterior',
					'label'		=> 'No exterior',
					'rules'		=> 'max_length[7]'),
			array('field'	=> 'dno_interior',
					'label'		=> 'No interior',
					'rules'		=> 'max_length[7]'),
			array('field'	=> 'dcolonia',
					'label'		=> 'Colonia',
					'rules'		=> 'max_length[60]'),
			array('field'	=> 'dlocalidad',
					'label'		=> 'Localidad',
					'rules'		=> 'max_length[45]'),
			array('field'	=> 'dmunicipio',
					'label'		=> 'Municipio',
					'rules'		=> 'max_length[45]'),
			array('field'	=> 'destado',
					'label'		=> 'Estado',
					'rules'		=> 'max_length[45]'),
			array('field'	=> 'dcp',
					'label'		=> 'CP',
					'rules'		=> 'max_length[10]'),
			array('field'	=> 'dtelefono',
					'label'		=> 'Teléfono',
					'rules'		=> 'max_length[15]'),
			array('field'	=> 'dcelular',
					'label'		=> 'Celular',
					'rules'		=> 'max_length[20]'),
			array('field'	=> 'demail',
					'label'		=> 'Email',
					'rules'		=> 'valid_email|max_length[70]'),
			array('field'	=> 'dpag_web',
					'label'		=> 'Pag Web',
					'rules'		=> 'max_length[80]'),
			array('field'	=> 'dcomentarios',
					'label'		=> 'Comentarios',
					'rules'		=> 'max_length[400]'),
			array('field'	=> 'drecepcion_facturas',
					'label'		=> 'Recepción facturas',
					'rules'		=> 'max_length[10]'),
			array('field'	=> 'ddias_pago',
					'label'		=> 'Dias pago',
					'rules'		=> 'max_length[10]'),
			array('field'	=> 'ddias_credito',
					'label'		=> 'Dias credito',
					'rules'		=> 'max_length[4]')
		);
		
		if($tipo == 'add'){
			$rules[] = array('field'	=> 'dcnombre',
					'label'		=> 'Contacto Nombre',
					'rules'		=> 'max_length[120]');
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
	private function showMsgs($tipo, $msg='', $title='Proveedores!'){
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
				$txt = 'El proveedor se modifico correctamente.';
				$icono = 'ok';
			break;
			case 4:
				$txt = 'El proveedor se agrego correctamente.';
				$icono = 'ok';
			break;
			case 5:
				$txt = 'El proveedor se elimino correctamente.';
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