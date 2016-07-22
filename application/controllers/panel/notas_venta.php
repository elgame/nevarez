<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class notas_venta extends MY_Controller {
	
	/**
	 * Evita la validacion (enfocado cuando se usa ajax). Ver mas en privilegios_model
	 * @var unknown_type
	 */
	private $excepcion_privilegio = array('notas_venta/ajax_get_total_tickets/','notas_venta/ajax_agrega_nota_venta/','notas_venta/imprime_nota_venta/');
	
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
	 * Default. Mustra el listado de las notas de venta para administrarlas
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
			array('notas_venta/admin.js')
		));
		$this->load->model('notas_venta_model');
		$this->load->library('pagination');
		
		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['opcmenu_active'] = 'Notas de Venta'; //activa la opcion del menu
		$params['seo'] = array(
			'titulo' => 'Administrar Notas de Venta'
		);
		
		$params['notas'] = $this->notas_venta_model->getNotasVenta();
		
		if(isset($_GET['msg']{0}))
			$params['frm_errors'] = $this->showMsgs($_GET['msg']);
		
		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/notas_venta/listado', $params);
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
				array('general/util.js'),
				array('general/msgbox.js'),
				array('notas_venta/frm_addmod.js')
		));
		
		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['seo'] = array(
				'titulo' => 'Notas de Venta'
		);
		$params['opcmenu_active'] = 'Notas de Venta'; //activa la opcion del menu
		
		$this->load->model('notas_venta_model');
		$params['nota'] = $this->notas_venta_model->getNxtFolio();
		
		if(isset($_GET['msg']{0}))
				$params['frm_errors'] = $this->showMsgs($_GET['msg']);
		
			$this->load->view('panel/header', $params);
			$this->load->view('panel/general/menu', $params);
			$this->load->view('panel/notas_venta/agregar', $params);
			$this->load->view('panel/footer');
	}
	
	public function cancelar(){
		if(isset($_GET['id']{0})){
			
			$this->load->model('notas_venta_model');
			$res = $this->notas_venta_model->cancelNotaVenta($_GET['id']);
			
			if($res[0])
				redirect(base_url('panel/notas_venta/?'.String::getVarsLink(array('id','msg')).'&msg=5'));
		}
		else
			redirect(base_url('panel/notas_venta/?'.String::getVarsLink(array('msg')).'&msg=1'));
	}
	
	public function ver(){
		if(isset($_GET['id']{0})){
			$this->carabiner->css(array(
					array('general/forms.css', 'screen'),
					array('general/tables.css', 'screen')
			));
			
			$params['info_empleado'] = $this->info_empleado['info']; //info empleado
			$params['seo'] = array(
					'titulo' => 'Ver Nota de Venta'
			);
			$params['opcmenu_active'] = 'Notas de Venta'; //activa la opcion del menu
			
			$this->load->model('notas_venta_model');
			$params['info'] = $this->notas_venta_model->getInfoNotaVenta($_GET['id']);
			
			$this->load->view('panel/header', $params);
			$this->load->view('panel/general/menu', $params);
			$this->load->view('panel/notas_venta/ver',$params);
			$this->load->view('panel/footer');
		}else redirect(base_url('panel/notas_venta/?'.String::getVarsLink().'&msg=1'));
	}
	
	public function pagar(){
		if(isset($_GET['id']{0})){
			$this->carabiner->css(array(
						array('general/forms.css', 'screen'),
						array('general/tables.css', 'screen'),
					));
			
			$this->carabiner->js(array(
						array('notas_venta/pago_nota_venta.js')
					));
			
			$this->load->model('notas_venta_model');
			
			$this->configAddPago();
			if($this->form_validation->run() == FALSE){
				$params['frm_errors']= $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
			}
			else{
				$res = $this->notas_venta_model->abonar_nota_venta(true);
				
				if($res[0]){
					$params['frm_errors'] = $this->showMsgs('6');
					$params['load'] = true;
				}
				else
					$params['frm_errors']= $this->showMsgs(2, $res['msg']);
			}
			
			$params['folio'] = $this->db->select('folio')->from('tickets_notas_venta')->where('id_nota_venta',$_GET['id'])->get()->row()->folio;
			
			$res = $this->notas_venta_model->get_info_abonos();
			$params['total'] = $res;
			
			$params['seo']['titulo'] = 'Pagar Nota de Venta';
			
			$this->load->view('panel/notas_venta/pago_nota_venta',$params);
		}
		else redirect(base_url('panel/notas_venta/?'.String::getVarsLink(array('msg')).'&msg=1'));
	}
	
	public function imprime_nota_venta(){
		if(isset($_GET['id']{0})){
			$this->load->model('notas_venta_model');
			$res = $this->notas_venta_model->getInfoNotaVenta($_GET['id']);
			
			$this->load->library('mypdf');
			// Creacion del objeto de la clase heredada
			$pdf = new MYpdf('P', 'mm', 'Letter');
			$pdf->show_head = false;
			$pdf->AddPage();
			$pdf->SetFont('Arial','',8);
			
			$y = 30;
			$pdf->Image(APPPATH.'/images/logo.png',15,10,25,25,"PNG");
			
			$pdf->SetFont('Arial','B',17);
			$pdf->SetXY(45, $y);
			$pdf->Cell(120, 6, 'F U M I G A C I O N E S   A E R E A S' , 0, 0, 'C');
			
			// ----------- FOLIO ------------------
			$pdf->SetXY(170, ($y-8));
			$pdf->Cell(30, 15, '' , 1, 0, 'C');
			
			$pdf->SetFont('Arial','B',11);
			$pdf->SetTextColor(255,255,255);
			$pdf->SetFillColor(160,160,160);
			$pdf->SetXY(170, ($y-8));
			$pdf->Cell(30, 5, 'FOLIO', 1, 0, 'C',1);
			
			$pdf->SetFont('Arial','',18);
			$pdf->SetTextColor(255,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetXY(170, ($y-3));
			$pdf->Cell(30, 10, $res[1]['cliente_info'][0]->folio , 0, 0, 'C');
			
			// ----------- FECHA ------------------
			
			$pdf->SetXY(170, ($y+8));
			$pdf->Cell(30, 12, '' , 1, 0, 'C');
				
			$pdf->SetFont('Arial','B',11);
			$pdf->SetTextColor(255,255,255);
			$pdf->SetFillColor(160,160,160);
			$pdf->SetXY(170, ($y+8));
			$pdf->Cell(30, 5, 'FECHA' , 1, 0, 'C',1);
				
			$pdf->SetFont('Arial','',15);
			$pdf->SetTextColor(255,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetXY(170, ($y+13));
			$pdf->Cell(30, 7, $res[1]['cliente_info'][0]->fecha , 1, 0, 'C',1);
			
			// ----------- DATOS CLIENTE ------------------
				
			$pdf->SetXY(15, ($y+7));
			$pdf->Cell(153, 23, '' , 1, 0, 'C');
			
			$pdf->SetFont('Arial','B',11);
			$pdf->SetTextColor('0','0','0');
			$pdf->SetXY(15, ($y+9));
			$pdf->Cell(20, 5, 'Nombre:', 0, 0, 'L');
			
			$pdf->SetXY(15, ($y+15));
			$pdf->Cell(20, 5, 'Domicilio:' , 0, 0, 'L');
			
			$pdf->SetXY(15, ($y+22));
			$pdf->Cell(20, 5, 'Lugar:' , 0, 0, 'L');
			
			$pdf->SetFont('Arial','',12);
			$pdf->SetXY(35, ($y+9));
			$pdf->Cell(130, 5, $res[1]['cliente_info'][0]->nombre_fiscal , 0, 0, 'L');
			$pdf->SetXY(35, ($y+15));
			$pdf->Cell(130, 5, $res[1]['cliente_info'][0]->domiciliof2 , 0, 0, 'L');
			$pdf->SetXY(35, ($y+22));
			$pdf->Cell(130, 5, $res[1]['cliente_info'][0]->domiciliof2 , 0, 0, 'L');
			
			// ----------- TABLA CON LOS TICKETS ------------------
			$pdf->SetY($y+33);
			$aligns = array('C', 'C', 'C');
			$widths = array(25, 127, 33);
			$header = array('Cantidad', 'Descripción', 'Importe');
			foreach($res[1]['tickets_info'] as $key => $item){
				$band_head = false;
				if($pdf->GetY() >= 200 || $key==0){ //salta de pagina si exede el max
					if($key > 0)
						$pdf->AddPage();
						
					$pdf->SetFont('Arial','B',8);
					$pdf->SetTextColor(255,255,255);
					$pdf->SetFillColor(160,160,160);
					$pdf->SetX(15);
					$pdf->SetAligns($aligns);
					$pdf->SetWidths($widths);
					$pdf->Row($header, true);
				}
					
				$pdf->SetFont('Arial','',10);
				$pdf->SetTextColor(0,0,0);
					
				$datos = array('1', 'Ticket Folio:'.$item->folio,String::formatoNumero($item->subtotal));
					
				$pdf->SetX(15);
				$pdf->SetAligns($aligns);
				$pdf->SetWidths($widths);
				$pdf->Row($datos, false);
			}
			
			//------------ SUBTOTAL, IVA ,TOTAL --------------------
			
			$y = $pdf->GetY();
			$pdf->SetFont('Arial','B',10);
			$pdf->SetTextColor(255,255,255);
			$pdf->SetFillColor(160,160,160);
			
			$pdf->SetXY(144, ($y+5));
			$pdf->Cell(23, 8, 'Subtotal' , 1, 0, 'C',1);
			$pdf->SetXY(144, ($y+13));
			$pdf->Cell(23, 8, 'IVA' , 1, 0, 'C',1);
			$pdf->SetXY(144, ($y+21));
			$pdf->Cell(23, 8, 'Total' , 1, 0, 'C',1);
			
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetXY(167, ($y+5));
			$pdf->Cell(33, 8, String::formatoNumero($res[1]['cliente_info'][0]->subtotal,2) , 1, 0, 'C');
			$pdf->SetXY(167, ($y+13));
			$pdf->Cell(33, 8, String::formatoNumero($res[1]['cliente_info'][0]->iva,2) , 1, 0, 'C');
			$pdf->SetXY(167, ($y+21));
			$pdf->Cell(33, 8, String::formatoNumero($res[1]['cliente_info'][0]->total,2) , 1, 0, 'C');
			
			//------------ TOTAL CON LETRA--------------------
			
			$pdf->SetXY(15, ($y+5));
			$pdf->Cell(125, 24, '' , 1, 0, 'C');
			
			$pdf->SetFont('Arial','B',13);
			$pdf->SetXY(15, ($y+5));
			$pdf->Cell(60, 8, 'IMPORTE CON LETRA' , 0, 0, 'L');
			
			$pdf->SetFont('Arial','',12);
			$pdf->SetXY(15, ($y+13));
			$pdf->Cell(125, 16, String::num2letras($res[1]['cliente_info'][0]->total) , 0, 0, 'C');
			
			$pdf->Output('nota_de_venta.pdf', 'I');
		}
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
		$this->form_validation->set_rules($rules);
	}
	
	public function ajax_agrega_nota_venta(){
	
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
						'rules'		=> 'required'),
				array('field'	=> 'tickets',
						'label'		=> 'Tickets',
						'rules'		=> 'required')
		);
		$this->form_validation->set_rules($rules);
	
		if($this->form_validation->run() == FALSE)
		{
			$params['msg']= $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
		}
		else
		{
			$this->load->model('notas_venta_model');
			$params	= $this->notas_venta_model->addNotaVenta();
	
			if($params[0])
				$params['msg'] = $this->showMsgs(4);
		}
	
		echo json_encode($params);
	}
	
	public function ajax_get_total_tickets(){
		$this->load->model('notas_venta_model');
		$params = $this->notas_venta_model->getTotalTicketsAjax();
	
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
				$txt = 'La nota de venta se modifico correctamente.';
				$icono = 'ok';
				break;
			case 4:
				$txt = 'La nota de venta se agrego correctamente.';
				$icono = 'ok';
				break;
			case 5:
				$txt = 'La nota de venta se cancelo correctamente.';
				$icono = 'ok';
				break;
			case 6:
				$txt = 'La nota de venta se pagó correctamente.';
				$icono = 'ok';
				break;
		}
	
		return array(
				'title' => $title,
				'msg' => $txt,
				'ico' => $icono);
	}
	
}