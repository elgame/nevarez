<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class pilotos extends MY_Controller {
	
	/**
	 * Evita la validacion (enfocado cuando se usa ajax). Ver mas en privilegios_model
	 * @var unknown_type
	 */
	private $excepcion_privilegio = array('pilotos/ajax_get_pilotos/');
	
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
		$this->load->model('pilotos_model');
		$this->load->library('pagination');
		
		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['opcmenu_active'] = 'Pilotos'; //activa la opcion del menu
		$params['seo'] = array(
			'titulo' => 'Administrar Pilotos'
		);
		
		$params['pilotos'] = $this->pilotos_model->getPilotos();
		
		if(isset($_GET['msg']{0}))
			$params['frm_errors'] = $this->showMsgs($_GET['msg']);
		
		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/pilotos/listado', $params);
		$this->load->view('panel/footer');
	}
	
	public function agregar(){
		$this->carabiner->css(array(
				array('general/forms.css', 'screen'),
				array('general/tables.css', 'screen')
		));
		$this->carabiner->js(array(
				array('pilotos/frm_addmod.js'),
				array('libs/jquery.numeric.js')
		));
		
		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['seo'] = array(
				'titulo' => 'Agregar Piloto'
		);
		$params['opcmenu_active'] = 'Pilotos'; //activa la opcion del menu
		$this->configAddPiloto('add');
		
		if($this->form_validation->run() == FALSE){
			$params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
		}else{
			$this->load->model('pilotos_model');
			$respons = $this->pilotos_model->addPiloto();
				
			if($respons[0])
				redirect(base_url('panel/pilotos/agregar/?'.String::getVarsLink(array('msg')).'&msg=4'));
		}
		
		if(isset($_GET['msg']{0}))
				$params['frm_errors'] = $this->showMsgs($_GET['msg']);
		
			$this->load->view('panel/header', $params);
			$this->load->view('panel/general/menu', $params);
			$this->load->view('panel/pilotos/agregar', $params);
			$this->load->view('panel/footer');		
	}
	
	public function modificar(){
		$this->carabiner->css(array(
				array('libs/jquery.msgbox.css', 'screen'),
				array('general/forms.css', 'screen'),
				array('general/tables.css', 'screen')
		));
		$this->carabiner->js(array(
				array('libs/jquery.msgbox.min.js'),
				array('general/msgbox.js'),
				array('pilotos/frm_addmod.js')
		));
		
		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['seo'] = array(
				'titulo' => 'Modificar Piloto'
		);
		$params['opcmenu_active'] = 'Pilotos'; //activa la opcion del menu
		
		if(isset($_GET['id']{0}))
		{
			$this->load->model('pilotos_model');
			$this->configAddPiloto('update');
			
			if($this->form_validation->run() == FALSE){
				$params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
			}else{
				
				$respons = $this->pilotos_model->updatePiloto();
			
				if($respons[0])
					redirect(base_url('panel/pilotos/modificar/?'.String::getVarsLink(array('msg')).'&msg=3'));
			}
			
			$params['piloto']	= $this->pilotos_model->getInfoPiloto($_GET['id']);
			
			if(!is_array($params['piloto']))
				unset($params['piloto']);
			
			if(isset($_GET['msg']{0}))
					$params['frm_errors'] = $this->showMsgs($_GET['msg']);
			
				$this->load->view('panel/header', $params);
				$this->load->view('panel/general/menu', $params);
				$this->load->view('panel/pilotos/modificar', $params);
				$this->load->view('panel/footer');
		}
		else
			$params['frm_errors'] = $this->showMsgs(1);
	}
	
	public function eliminar(){
		
		if(isset($_GET['id']{0})){
			$this->load->model('pilotos_model');
			$respons = $this->pilotos_model->delPiloto();
			
			if($respons[0])
				redirect('panel/pilotos/?&msg=5');
		}
		else
			$params['frm_errors']	= $this->showMsgs(1);
	}
	
	public function agregar_contacto(){
		
		if(isset($_GET['id']{0}))
		{
			$this->load->library('form_validation');
			
			$rules[] = array('field'	=> 'dcnombre',
					'label'		=> 'Contacto Nombre',
					'rules'		=> 'max_length[120]');
			$rules[] = array('field'	=> 'dcdomicilio',
					'label'		=> 'Contacto Domicilio',
					'rules'		=> 'max_length[200]');
			$rules[] = array('field'	=> 'dcmunicipio',
					'label'		=> 'Contacto Municipio',
					'rules'		=> 'max_length[40]');
			$rules[] = array('field'	=> 'dcestado',
					'label'		=> 'Contacto Estado',
					'rules'		=> 'max_length[40]');
			$rules[] = array('field'	=> 'dctelefono',
					'label'		=> 'Contacto Teléfono',
					'rules'		=> 'max_length[15]');
			$rules[] = array('field'	=> 'dccelular',
					'label'		=> 'Contacto Celular',
					'rules'		=> 'max_length[20]');
			
			$this->form_validation->set_rules($rules);
			
			if($this->form_validation->run() == FALSE)
			{
				$params['msg'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
			}
			else
			{
				$this->load->model('pilotos_model');
				$params['msg']	= $this->pilotos_model->addContacto($_GET['id']);
				
				if($params['msg'][0])
				{
					$res = $this->db->select('*')->from('proveedores_contactos_piloto')->where("id_contacto = '".$params['msg'][2]."'")->get();
					$params['info'] = $res->row();
					$params['msg']	= $this->showMsgs(6);
				}
			}
		}
		else
			$params['frm_errors'] = $this->showMsgs(1);
		
		echo json_encode($params);
	}
	
	public function eliminar_contacto(){
		if(isset($_GET['id']{0}))
		{
			$this->load->model('pilotos_model');
			$response['msg'] = $this->pilotos_model->delContacto($_GET['id']);
			if($response['msg'][0])
				$params['msg'] = $this->showMsgs(7);
		}
		else
			$params['msg']  = $this->showMsgs(1);
		
		echo json_encode($params);
	}
	
	/**
	 * Obtiene lostado de pilotos para el autocomplete, ajax
	 */
	public function ajax_get_pilotos(){
		$this->load->model('pilotos_model');
		$params = $this->pilotos_model->getPilotosAjax();
	
		echo json_encode($params);
	}
	
	
	private function configAddPiloto($tipo){
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
						'rules'		=> 'max_length[4]'),
				array('field'	=> 'dexpide_factura',
						'label'		=> 'Expide Factura',
						'rules'		=> 'is_natural|max_length[1]'),
				array('field'	=> 'dlicencia_avion',
						'label'		=> 'Licencia Avión',
						'rules'		=> 'required|max_length[40]'),
				array('field'	=> 'dlicencia_vehiculo',
						'label'		=> 'Licencia Vehículo',
						'rules'		=> 'max_length[40]'),
				array('field'	=> 'dvencimiento_licencia_a',
						'label'		=> 'Fecha vecimiento avión',
						'rules'		=> 'required|max_length[10]|callback_isValidDate'),
				array('field'	=> 'dvencimiento_licencia_v',
						'label'		=> 'Fecha vecimiento vehículo',
						'rules'		=> 'max_length[10]|callback_isValidDate'),
				array('field'	=> 'dfecha_vence_seguro',
						'label'		=> 'Fecha vecimiento seguro',
						'rules'		=> 'required|max_length[10]|callback_isValidDate'),
				array('field'	=> 'dfecha_nacimiento',
						'label'		=> 'Fecha Nacimiento',
						'rules'		=> 'max_length[10]|callback_isValidDate'),
				array('field'	=> 'dprecio_vuelo',
						'label'		=> 'Precio por Vuelo',
						'rules'		=> 'required|callback_val_precio_vuelo')
		);
	
		if($tipo == 'add'){
			$rules[] = array('field'	=> 'dcnombre',
					'label'		=> 'Contacto Nombre',
					'rules'		=> 'max_length[120]');
			$rules[] = array('field'	=> 'dcdomicilio',
					'label'		=> 'Contacto Domicilio',
					'rules'		=> 'max_length[200]');
			$rules[] = array('field'	=> 'dcmunicipio',
					'label'		=> 'Contacto Municipio',
					'rules'		=> 'max_length[40]');
			$rules[] = array('field'	=> 'dcestado',
					'label'		=> 'Contacto Estado',
					'rules'		=> 'max_length[40]');
			$rules[] = array('field'	=> 'dctelefono',
					'label'		=> 'Contacto Teléfono',
					'rules'		=> 'max_length[15]');
			$rules[] = array('field'	=> 'dccelular',
					'label'		=> 'Contacto Celular',
					'rules'		=> 'max_length[20]');
		}
		$this->form_validation->set_rules($rules);
	}
	
	public function val_precio_vuelo($str){
		if($str <= 0){
			$this->form_validation->set_message('val_precio_vuelo', 'El Precio por vuelo no puede ser 0, verifica los datos ingresados.');
			return false;
		}
		return true;
	}
	
	/**
	 * Form_validation: Valida su una fecha esta en formato correcto
	 */
	public function isValidDate($str){
		if($str != ''){
			if(String::isValidDate($str) == false){
				$this->form_validation->set_message('isValidDate', 'El campo %s no es una fecha valida');
				return false;
			}
		}
		return true;
	}
	
	
	/**
	 * Muestra mensajes cuando se realiza alguna accion
	 * @param unknown_type $tipo
	 * @param unknown_type $msg
	 * @param unknown_type $title
	 */
	private function showMsgs($tipo, $msg='', $title='Pilotos!'){
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
				$txt = 'El Piloto se modifico correctamente.';
				$icono = 'ok';
				break;
			case 4:
				$txt = 'El Piloto se agrego correctamente.';
				$icono = 'ok';
				break;
			case 5:
				$txt = 'El Piloto se elimino correctamente.';
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