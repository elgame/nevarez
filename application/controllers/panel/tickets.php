<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class tickets extends MY_Controller {
	
	/**
	 * Evita la validacion (enfocado cuando se usa ajax). Ver mas en privilegios_model
	 * @var unknown_type
	 */
	private $excepcion_privilegio = array('tickets/ajax_get_total_vuelos/','tickets/ajax_agrega_ticket/','tickets/imprime_ticket/','tickets/tickets_cliente/');
	
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
			array('libs/jquery.superbox.css', 'screen'),
			array('general/tables.css', 'screen'),
			array('general/forms.css', 'screen')
		));
		$this->carabiner->js(array(
			array('libs/jquery.msgbox.min.js'),
			array('libs/jquery.superbox.js'),
			array('general/msgbox.js'),
			array('tickets/admin.js')
		));
		$this->load->model('tickets_model');
		$this->load->library('pagination');
		
		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['opcmenu_active'] = 'Tickets'; //activa la opcion del menu
		$params['seo'] = array(
			'titulo' => 'Administrar Tickets'
		);
		
		$params['tickets'] = $this->tickets_model->getTickets();
		
		if(isset($_GET['msg']{0}))
			$params['frm_errors'] = $this->showMsgs($_GET['msg']);
		
		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/tickets/listado', $params);
		$this->load->view('panel/footer');
	}
	
	public function agregar(){
		$this->carabiner->css(array(
				array('libs/jquery.msgbox.css', 'screen'),
				array('libs/jquery.superbox.css', 'screen'),
				array('general/forms.css', 'screen'),
				array('general/tables.css', 'screen')
		));
		$this->carabiner->js(array(
				array('libs/jquery.msgbox.min.js'),
				array('libs/jquery.superbox.js'),
				array('libs/jquery.numeric.js'),
				array('general/util.js'),
				array('general/msgbox.js'),
				array('tickets/frm_addmod.js')
		));
		
		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['seo'] = array(
				'titulo' => 'Agregar Ticket'
		);
		$params['opcmenu_active'] = 'Tickets'; //activa la opcion del menu
		
		$this->load->model('tickets_model');
		$params['ticket'] = $this->tickets_model->getNxtFolio();
		
		$unidad = $this->db->select("id_unidad, abreviatura")->from("productos_unidades")->where("status","ac")->get();
		
		$params['unidad'] = $unidad->result();
		
		if(isset($_GET['msg']{0}))
				$params['frm_errors'] = $this->showMsgs($_GET['msg']);
		
			$this->load->view('panel/header', $params);
			$this->load->view('panel/general/menu', $params);
			$this->load->view('panel/tickets/agregar', $params);
			$this->load->view('panel/footer');
	}
	
	public function cancelar(){
		if(isset($_GET['id']{0})){
			
			$this->load->model('tickets_model');
			$res = $this->tickets_model->cancelTicket($_GET['id']);
			
			if($res[0])
				redirect(base_url('panel/tickets/?'.String::getVarsLink(array('id','msg')).'&msg=5'));
		}
		else
			redirect(base_url('panel/tickets/?'.String::getVarsLink(array('msg')).'&msg=1'));
	}
	
	public function ver(){
		if(isset($_GET['id']{0})){
			
			$this->carabiner->css(array(
					array('general/forms.css', 'screen'),
					array('general/tables.css', 'screen')
			));
			
			$params['info_empleado'] = $this->info_empleado['info']; //info empleado
			$params['seo'] = array(
					'titulo' => 'Ver Ticket'
			);
			$params['opcmenu_active'] = 'Tickets'; //activa la opcion del menu
			
			$this->load->model('tickets_model');
			$params['info'] = $this->tickets_model->getInfoTicket($_GET['id']);
			
			$this->load->view('panel/header', $params);
			$this->load->view('panel/general/menu', $params);
			$this->load->view('panel/tickets/ver',$params);
			$this->load->view('panel/footer');
		}
	}
	
	public function pagar(){
		if(isset($_GET['id']{0})){
			$this->carabiner->css(array(
						array('general/forms.css', 'screen'),
						array('general/tables.css', 'screen'),
					));
			
			$this->carabiner->js(array(
						array('tickets/pago_ticket.js')
					));
			
			$this->load->model('tickets_model');
			
			$this->configAddPago();
			if($this->form_validation->run() == FALSE){
				$params['frm_errors']= $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
			}
			else{
				
				if (isset($_GET['tipo'])){
					$res = $this->tickets_model->abonar_ticket(false, $_GET['id'], $_POST['fabono']);
					$msg = 7;
				}
				else{
					$res = $this->tickets_model->abonar_ticket(true);
					$msg = 6;
				}
				
				if($res[0]){
					$params['frm_errors'] = $this->showMsgs($msg);
					$params['load'] = true;
				}
				else
					$params['frm_errors']= $this->showMsgs(2, $params['msg']);
			}
			
			$params['folio'] = $this->db->select("folio")->from("tickets")->where("id_ticket",$_GET['id'])->get()->row()->folio;
			$res = $this->tickets_model->get_info_abonos();
			$params['total'] = $res;
			
			$params['seo']['titulo'] = 'Pagar Ticket';
			if (isset($_GET['tipo'])){
				$params['seo']['titulo'] = 'Abonar Ticket';
			}
			$this->load->view('panel/tickets/pago_ticket',$params);
		}
		else redirect(base_url('panel/tickets/?'.String::getVarsLink().'&msg=1'));
	}
	
	public function eliminar_abono(){
		if (isset($_GET['ida']{0}))
		{
			$this->load->model('tickets_model');
			$res = $this->tickets_model->eliminar_abono();
			if ($res){
				redirect(base_url('panel/cuentas_cobrar/detalle/?'.String::getVarsLink(array('msg','ida')).'&msg=3'));
			}
		}
		else redirect(base_url('panel/cuentas_cobrar/detalle/?'.String::getVarsLink(array('msg','ida')).'&msg=1'));
	}

	public function imprime_ticket(){
		if(isset($_GET['id']{0})){
			
			$this->carabiner->css(array(
							array('base.css', 'print'),
							array('tickets/print_ticket.css', 'print')
					));

			$this->carabiner->js(array(
						array('tickets/print_ticket.js')
					));		
		
			$this->load->model('tickets_model');
			$params['info'] = $this->tickets_model->getInfoTicket($_GET['id']);
			$params['seo']['titulo'] = 'Ticket';

			// $this->load->library('mypdf_ticket');
			$pdf = new mypdf_ticket();
			$pdf->SetFont('Arial','',8);
			$pdf->AddPage();

			$pdf->printTicket($params['info']);

			$pdf->AutoPrint(true);
			$pdf->Output();
			// $this->load->view('panel/tickets/print_ticket',$params);
		}
	}
	
	public function tickets_cliente(){
		$this->carabiner->css(array(
				array('general/forms.css', 'screen'),
				array('general/tables.css', 'screen')
		));
			
		$this->carabiner->js(array(
				array('tickets/tickets_notas_venta.js')
		));
		$params['seo'] = array(
				'titulo' => 'Tickets'
		);
		
		$this->load->model('tickets_model');
		$params['cliente'] = array('tickets' => $this->tickets_model->getTicketsCliente());
		
		$this->load->view('panel/tickets/tickets_cliente',$params);
	}
	
	
	public function configAddPago(){

		$this->load->library('form_validation');
		$rules = array(
				array('field'	=> 'ffecha',
						'label'		=> 'Fecha',
						'rules'		=> 'required|max_length[10]|callback_isValidDate'),
				array('field'	=> 'fconcepto',
						'label'		=> 'Concepto',
						'rules'		=> 'required|max_length[200]')
		);

		if (isset($_GET['tipo']))
			$rules[] = array('field' => 'fabono',
											'label' => 'Total a Abonar',
											'rules' => 'required|callback_verifica_abono');

		$this->form_validation->set_rules($rules);
	}
	
	public function ajax_agrega_ticket(){
	
		$this->load->library('form_validation');
		$rules = array(
				array('field'	=> 'tcliente',
						'label'		=> 'Cliente',
						'rules'		=> 'required|max_length[25]'),
				array('field'	=> 'tfolio',
						'label'		=> 'Folio',
						'rules'		=> 'required|is_natural_no_zero'),
				array('field'	=> 'tfecha',
						'label'		=> 'Fecha',
						'rules'		=> 'required|max_length[10]|callback_isValidDate'),
				array('field'	=> 'tipo_pago',
						'label'		=> 'Tipo pago',
						'rules'		=> 'required|max_length[10]'),
				array('field'	=> 'tdias_credito',
						'label'		=> 'Dias de Credito',
						'rules'		=> 'is_natural'),
				array('field'	=> 'subtotal',
						'label'		=> 'Subtotal',
						'rules'		=> 'required'),
				array('field'	=> 'iva',
						'label'		=> 'Iva',
						'rules'		=> 'required'),
				array('field'	=> 'total',
						'label'		=> 'Total',
						'rules'		=> 'required|callback_val_total'),
				array('field'	=> 'vuelos',
						'label'		=> 'Vuelos',
						'rules'		=> '')
		);
		$this->form_validation->set_rules($rules);
	
		if($this->form_validation->run() == FALSE)
		{
			$params['msg']= $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
		}
		else
		{
			$this->load->model('tickets_model');
			$params	= $this->tickets_model->addTicket();
	
			if($params[0])
				$params['msg'] = $this->showMsgs(4);
		}
	
		echo json_encode($params);
	}
	
	
	/**
	 * 
	 */
	public function ajax_get_total_vuelos(){
		$this->load->model('tickets_model');
		$params = $this->tickets_model->getTotalVuelosAjax();
	
		echo json_encode($params);
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
	
	public function val_total($str){
		if($str <= 0){
			$this->form_validation->set_message('val_total', 'El Total no puede ser 0, verifica los datos ingresados.');
			return false;
		}
		return true;
	}

	public function verifica_abono($str) {
		// $res = $this->nomina_model->get_info_abonos();
		$res = $this->tickets_model->get_info_abonos();
		$abono = floatval($str);
		if($abono > $res->restante){
			$this->form_validation->set_message('verifica_abono', 'El Abono que ingreso no puede ser mayor al Saldo');
			return false;
		}
		if($abono == 0){
			$this->form_validation->set_message('verifica_abono', 'El Abono que ingreso no puede ser de Cero (0)');
			return false;
		}
	}
	
	/**
	 * Muestra mensajes cuando se realiza alguna accion
	 * @param unknown_type $tipo
	 * @param unknown_type $msg
	 * @param unknown_type $title
	 */
	private function showMsgs($tipo, $msg='', $title='Tickets !'){
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
				$txt = 'El Ticket se modifico correctamente.';
				$icono = 'ok';
				break;
			case 4:
				$txt = 'El Ticket se agrego correctamente.';
				$icono = 'ok';
				break;
			case 5:
				$txt = 'El Ticket se cancelo correctamente.';
				$icono = 'ok';
				break;
			case 6:
				$txt = 'El Ticket se pagó correctamente.';
				$icono = 'ok';
				break;
			case 7:
				$txt = 'El abono se agrego correctamente';
				$icono = 'ok';
				break;
		}
	
		return array(
				'title' => $title,
				'msg' => $txt,
				'ico' => $icono);
	}
	
}