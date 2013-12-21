<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class home extends MY_Controller {
	
	public function _remap($method){
		$this->carabiner->css(array(
				array('libs/jquery-ui.css', 'screen'),
				array('libs/ui.notify.css', 'screen'),
				array('base.css', 'screen')
		));
		$this->carabiner->js(array(
				array('libs/jquery.min.js'),
				array('libs/jquery-ui.js'),
				array('libs/jquery.notify.min.js'),
				array('general/alertas.js'),
		));
		
		$this->load->model("empleados_model");
		if($this->empleados_model->checkSession()){
			$this->info_empleado = $this->empleados_model->getInfoEmpleado($_SESSION['id_empleado'], true);
			$this->{$method}();
		}else
			$this->{'login'}();
	}
	
	public function index(){
		$this->carabiner->css(array(
				array('libs/jquery.treeview.css', 'screen'),
				array('libs/jquery.msgbox.css', 'screen'),
				array('general/tables.css')
		));
		$this->carabiner->js(array(
				array('libs/jquery.treeview.js'),
				array('libs/jquery.msgbox.min.js'),
				array('general/msgbox.js')
		));
		
		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['seo'] = array(
			'titulo' => 'Panel de Administración'
		);
		
		$this->load->model('privilegios_model');
		$this->load->model('alertas_model');
		
		if(isset($_GET['msg']))
			$params['frm_errors'] = $this->showMsgs($_GET['msg']);
		
		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/general/home', $params);
		$this->load->view('panel/footer');
	}
	
	/**
	 * Genera el reporte productos bajos del stock
	 */
	public function productos_bajos(){
		$prod_bajos = $this->db->query("SELECT * FROM reportes_costo_existencias1 WHERE stock_min > existencia")->result();
	
		$this->load->library('mypdf');
		// Creación del objeto de la clase heredada
		$pdf = new MYpdf('P', 'mm', 'Letter');
		$pdf->show_head = true;
		$pdf->titulo2 = 'Reporte Productos Bajos de Inventario';
		$pdf->AliasNbPages();
		$pdf->AddPage();
			
		$links = array('', '', '', '');
		$aligns = array('C', 'C', 'C', 'C');
		$widths1 = array(98, 35, 35, 35);
		$header1 = array('Producto', 'Existencia', 'Stock min', 'Faltante');
	
		foreach($prod_bajos as $key => $item){
			if($pdf->GetY() >= $pdf->limiteY || $key == 0){ //salta de pagina si exede el max
				if($key > 0)
					$pdf->AddPage();
	
				$pdf->SetFont('Arial', 'B', 8);
				$pdf->SetTextColor(255, 255, 255);
				$pdf->SetFillColor(160, 160, 160);
				$pdf->SetX(6);
				$pdf->SetAligns($aligns);
				$pdf->SetWidths($widths1);
				$pdf->Row($header1, true);
			}
				
			$pdf->SetFont('Arial', '', 8);
			$pdf->SetTextColor(0,0,0);
				
			$datarow = array($item->nombre,
					$item->existencia,
					$item->stock_min,
					$item->stock_min - $item->existencia
			);
				
			$pdf->SetX(6);
			$pdf->SetAligns($aligns);
			$pdf->SetWidths($widths1);
			$pdf->Row($datarow, false);
		}
			
		$pdf->Output('reporte.pdf', 'I');
	}
	
	/**
	 * carga el login para entrar al panel
	 */
	public function login(){
		$this->carabiner->css(array(
				array('general/forms.css', 'screen')
		));
		
		$params['seo'] = array(
			'titulo' => 'Login'
		);
		
		$this->load->library('form_validation');
		$rules = array(
			array('field'	=> 'usuario',
				'label'		=> 'Usuario',
				'rules'		=> 'required'),
			array('field'	=> 'pass',
				'label'		=> 'Contraseña',
				'rules'		=> 'required')
		);
		$this->form_validation->set_rules($rules);
		if($this->form_validation->run() == FALSE){
			$params['frm_errors'] = array(
					'title' => 'Error al Iniciar Sesión!', 
					'msg' => preg_replace("[\n|\r|\n\r]", '', validation_errors()), 
					'ico' => 'error');
		}else{
			$respons = $this->empleados_model->login();
			if($respons[0])
				redirect(base_url('panel/home'));
			else{
				$params['frm_errors'] = array(
					'title' => 'Error al Iniciar Sesión!',
					'msg' => $respons[1],
					'ico' => 'error');
			}
		}
		
		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/login', $params);
		$this->load->view('panel/footer');
	}
	
	/**
	 * cierra la sesion del usuario
	 */
	public function logout(){
		session_destroy();
		redirect(base_url('panel/home'));
	}
	
	/**
	 * Muestra mensajes cuando se realiza alguna accion
	 * @param unknown_type $tipo
	 * @param unknown_type $msg
	 * @param unknown_type $title
	 */
	private function showMsgs($tipo, $msg='', $title='Alertas!'){
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
				$txt = 'La herramienta se entrego correctamenta.';
				$icono = 'ok';
				break;
			case 4:
				$txt = 'La fecha se actualizo correctamente.';
				$icono = 'ok';
				break;
			case 5:
				$txt = $msg;
				$icono = 'ok';
				break;
			case 6:
				$txt = 'La alerta se elimino correctamente';
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