<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class empleados extends MY_Controller {
	
	/**
	 * Evita la validacion (enfocado cuando se usa ajax). Ver mas en privilegios_model
	 * @var unknown_type
	 */
	private $excepcion_privilegio = array('empleados/ajax_get_trabajadores/', 'empleados/rda_pdf/');
	
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
	 * Default. Mustra el listado de empleados para administrarlos
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
		$this->load->library('pagination');
		
		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['opcmenu_active'] = 'Recursos Humanos'; //activa la opcion del menu
		$params['seo'] = array(
			'titulo' => 'Administrar empleados'
		);
		
		$params['empleados'] = $this->empleados_model->getEmpleados();
		
		if(isset($_GET['msg']{0}))
			$params['frm_errors'] = $this->showMsgs($_GET['msg']);
		
		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/empleados/listado', $params);
		$this->load->view('panel/footer');
	}
	
	/**
	 * Agrega un empleado a la bd
	 */
	public function agregar(){
		$this->carabiner->css(array(
			array('general/forms.css', 'screen'),
			array('general/tables.css', 'screen')
		));
		$this->carabiner->js(array(
			array('empleados/frm_addmod.js'),
			array('libs/jquery.numeric.js'),
			array('libs/jquery-ui-timepicker-addon.js')
		));
		
		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['seo'] = array(
			'titulo' => 'Agregar empleado'
		);
		$params['opcmenu_active'] = 'Recursos Humanos'; //activa la opcion del menu
		$this->configAddModEmpl('add');
		
		if($this->form_validation->run() == FALSE){
			$params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
		}else{
			$respons = $this->empleados_model->addEmpleado();
			
			if($respons[0])
				redirect(base_url('panel/empleados/agregar/?'.String::getVarsLink(array('msg')).'&msg=4'));
			else
				$params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', $respons[1]));
		}
		
		if(isset($_GET['msg']{0}))
			$params['frm_errors'] = $this->showMsgs($_GET['msg']);
		
		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/empleados/agregar', $params);
		$this->load->view('panel/footer');
	}
	
	/**
	 * carga el login para entrar al panel
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
			array('empleados/frm_addmod.js'),
			array('libs/jquery.numeric.js'),
			array('libs/jquery-ui-timepicker-addon.js')
		));
		
		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['seo'] = array(
			'titulo' => 'Modificar empleado'
		);
		$params['opcmenu_active'] = 'Recursos Humanos'; //activa la opcion del menu
		
		if(isset($_GET['id']{0})){
			$this->configAddModEmpl('update');
			
			if($this->form_validation->run() == FALSE){
				$params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
			}else{
				$respons = $this->empleados_model->updateEmpleado();
			
				if($respons[0])
					redirect(base_url('panel/empleados/modificar/?'.String::getVarsLink(array('msg')).'&msg=3'));
				else
					$params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', $respons[1]));
			}
			
			$params['empleado'] = $this->empleados_model->getInfoEmpleado($_GET['id']);
			if(!is_array($params['empleado']))
				unset($params['empleado']);
			
			if(isset($_GET['msg']{0}))
				$params['frm_errors'] = $this->showMsgs($_GET['msg']);
		}else
			$params['frm_errors'] = $this->showMsgs(1);
		
		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/empleados/modificar', $params);
		$this->load->view('panel/footer');
	}
	
	/**
	 * Descontrata a un trabajador, cambia el status a no_contratado
	 */
	public function descontratar(){
		if(isset($_GET['id']{0})){
			$respons = $this->empleados_model->descontratarEmpleado();
			
			if($respons[0])
				redirect(base_url('panel/empleados/?msg=8'));
		}else
			$params['frm_errors'] = $this->showMsgs(1);
	}
	
	
	
	
	/*********** CONTACTOS **************/
	/**
	 * Agrega un nuevo contacto a un empleado utilizando Ajax
	 */
	public function agregar_contacto(){
		if(isset($_GET['id']{0})){
			$this->load->library('form_validation');
			$rules[] = array('field'	=> 'dcnombre',
					'label'		=> 'Contacto Nombre',
					'rules'		=> 'required|max_length[120]');
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
					'rules'		=> 'required|max_length[15]');
			$rules[] = array('field'	=> 'dccelular',
					'label'		=> 'Contacto Celular',
					'rules'		=> 'max_length[20]');
			$this->form_validation->set_rules($rules);
			
			if($this->form_validation->run() == FALSE){
				$params['msg'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
			}else{
				$params['msg'] = $this->empleados_model->addContacto($_GET['id']);
				if($params['msg'][0]){
					$res = $this->db
						->select('*')
						->from('empleados_contacto')
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
	 * Elimina un contacto del empleado utilizando Ajax
	 */
	public function eliminar_contacto(){
		if(isset($_GET['id']{0})){
			$params['msg'] = $this->empleados_model->deleteContacto($_GET['id']);
			if($params['msg'][0])
				$params['msg'] = $this->showMsgs(7);
		}else
			$params['msg'] = $this->showMsgs(1);
		
		echo json_encode($params);
	}
	
	/**
	 * Obtiene lostado de aviones para el autocomplete, ajax
	 */
	public function ajax_get_trabajadores(){
		$this->load->model('empleados_model');
		$params = $this->empleados_model->ajax_get_trabajadores();
	
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
					'rules'		=> 'required|max_length[30]'),
			array('field'	=> 'dapellido_paterno',
					'label'		=> 'Apellido paterno',
					'rules'		=> 'max_length[25]'),
			array('field'	=> 'dapellido_materno',
					'label'		=> 'Apellido materno',
					'rules'		=> 'max_length[25]'),
			array('field'	=> 'dusuario',
					'label'		=> 'Usuario',
					'rules'		=> 'alpha_dash|max_length[15]|callback_username_check'),
			array('field'	=> 'dpassword',
					'label'		=> 'Contraseña',
					'rules'		=> 'min_length[4]|matches[dpassword_conf]'),
			array('field'	=> 'dpassword_conf',
					'label'		=> 'Conf contraseña',
					'rules'		=> 'matches[dpassword_conf]'),
			array('field'	=> 'dcalle',
					'label'		=> 'Calle',
					'rules'		=> 'max_length[60]'),
			array('field'	=> 'dnumero',
					'label'		=> 'Numero',
					'rules'		=> 'max_length[7]'),
			array('field'	=> 'dcolonia',
					'label'		=> 'Colonia',
					'rules'		=> 'max_length[60]'),
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
			array('field'	=> 'dfecha_nacimiento',
					'label'		=> 'Fecha nacimiento',
					'rules'		=> 'max_length[10]|callback_isValidDate'),
			array('field'	=> 'dfecha_entrada',
					'label'		=> 'Fecha entrada',
					'rules'		=> 'max_length[10]|callback_isValidDate'),
			array('field'	=> 'dfecha_salida',
					'label'		=> 'Fecha salida',
					'rules'		=> 'max_length[10]|callback_isValidDate'),
			// array('field'	=> 'dfolio_inicio',
			// 		'label'		=> 'Folio inicio',
			// 		'rules'		=> 'max_length[8]'),
			// array('field'	=> 'dfolio_fin',
			// 		'label'		=> 'Folio fin',
			// 		'rules'		=> 'max_length[8]'),
			array('field'	=> 'dtipo_usuario',
					'label'		=> 'Tipo usuario',
					'rules'		=> 'max_length[16]'),
			array('field'	=> 'dstatus',
					'label'		=> 'Status',
					'rules'		=> 'max_length[15]'),
			array('field'	=> 'dprivilegios[]',
					'label'		=> 'Privilegios',
					'rules'		=> 'max_length[25]'),
			array('field'	=> 'dsalario',
					'label'		=> 'Salario Diario',
					'rules'		=> 'callback_isPositivo'),
			array('field'	=> 'dhora_entrada',
					'label'		=> 'Hora de Entrada',
					'rules'		=> '')
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
	/**
	 * Form_validation: Valida si el usuario ya esta usado por alguien mas
	 * @param unknown_type $str
	 */
	public function username_check($str){
		if($str != ''){
			$sql = '';
			if(isset($_GET['id']))
				$sql = " AND id_empleado != '".$_GET['id']."'";
			
			$res = $this->db->select('Count(id_empleado) AS num')
				->from('empleados')
				->where("usuario = '".$str."'".$sql)
			->get();
			$data = $res->row();
			if($data->num > 0){
				$this->form_validation->set_message('username_check', 'El nombre de usuario ya está siendo utilizado por otro usuario');
				return false;
			}
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
	
	public function isPositivo($str)
	{
		if(floatval($str)<0) {
			$this->form_validation->set_message('isPositivo', 'El campo %s no puede ser negativo');
			return false;	
		}
		return true;
	}


/**
	* Reporte de Asistencias
	*
  */

	public function rda()
	{
		$this->carabiner->css(array(
				array('general/forms.css', 'screen')
		));
		$this->carabiner->js(array(
				array('empleados/reporte_asistencias.js')
		));

		if (!isset($_GET['fanio'])) $_GET['fanio'] = date('Y');
		if (!isset($_GET['fsemana'])) $_GET['fsemana'] = String::obtenerSemanaActual(date('Y-m-d'));
		$params['semanas'] = String::obtenerSemanasDelAnio($_GET['fanio'],true);

		$params['seo'] = array(
				'titulo' => 'Reporte de Asistencias'
		);

		$this->load->view('panel/empleados/reporte_asistencias', $params);
	}

	public function rda_pdf()
	{
		$this->load->model('empleados_model');
		$this->load->model('nomina_model');
		$data = $this->nomina_model->getEmpleadosNomina(false, true);
		$this->empleados_model->pdf_rda($data);
	}

	/**
	 * Muestra mensajes cuando se realiza alguna accion
	 * @param unknown_type $tipo
	 * @param unknown_type $msg
	 * @param unknown_type $title
	 */
	private function showMsgs($tipo, $msg='', $title='Empleados!'){
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
				$txt = 'El empleado se modifico correctamente.';
				$icono = 'ok';
			break;
			case 4:
				$txt = 'El empleado se agrego correctamente.';
				$icono = 'ok';
			break;
			case 5:
				$txt = 'El empleado se elimino correctamente.';
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
			case 8:
				$txt = 'El empleado se descontrato correctamente.';
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