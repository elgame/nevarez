<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class salidas extends MY_Controller {
	
	/**
	 * Evita la validacion (enfocado cuando se usa ajax). Ver mas en privilegios_model
	 * @var unknown_type
	 */
	private $excepcion_privilegio = array('salidas/ver_todos/', 'salidas/pdf_rsa/');
	
	
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
	
	public function index(){
		$this->carabiner->css(array(
				array('libs/jquery.msgbox.css', 'screen'),
				array('libs/jquery.superbox.css', 'screen'),
				array('general/forms.css', 'screen'),
				array('general/tables.css', 'screen')
		));
		$this->carabiner->js(array(
				array('libs/jquery.msgbox.min.js'),
				array('libs/jquery.superbox.js'),
				array('compras/listado.js'),
				array('general/msgbox.js')
		));
		$this->load->library('pagination');
		
		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['opcmenu_active'] = 'Salidas'; //activa la opcion del menu
		$params['seo'] = array(
				'titulo' => 'Administrar Salidas'
		);
		
		$this->load->model('salidas_model');
		$params['salidas'] = $this->salidas_model->getSalidas();
		
		if(isset($_GET['msg']{0}))
				$params['frm_errors'] = $this->showMsgs($_GET['msg']);
		
		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/salidas/admin', $params);
		$this->load->view('panel/footer');
	}
	
	private function agregar() {
		$this->carabiner->css(array(
			array('libs/jquery.msgbox.css', 'screen'),
			array('general/forms.css', 'screen'),
			array('general/tables.css', 'screen')
		));
		$this->carabiner->js(array(
			array('libs/jquery.msgbox.min.js'),
			array('libs/jquery.numeric.js'),
			array('general/msgbox.js'),
			array('general/util.js'),
			array('salidas/frm_addmod.js')
		));
		
		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['opcmenu_active'] = 'Salidas'; //activa la opcion del menu
		$params['seo'] = array(
			'titulo' => 'Agregar Salida'
		);
		
		$this->configAddSalida();
		if($this->form_validation->run() == FALSE){
			$params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
		}else{
			$this->load->model('salidas_model');
			$respons = $this->salidas_model->addSalida();
			
			if($respons[0])
				redirect(base_url('panel/salidas/agregar/?'.String::getVarsLink(array('msg')).'id='.$respons[1].'&msg=4'));	
			else 
				$params['frm_errors'] = $this->showMsgs(2,$respons['msg']);
		}
		
		$params['folio'] = $this->db->query("SELECT COALESCE(MAX(folio)+1,1) as folio FROM salidas")->row()->folio;
		$params['fecha'] = date("Y-m-d");
		
		if(isset($_GET['id']))
			$params['print'] = true;
		
		if(isset($_GET['msg']{0}))
			$params['frm_errors'] = $this->showMsgs($_GET['msg']);
		
		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/salidas/agregar', $params);
		$this->load->view('panel/footer');
	}
	
	private function ver_todos() {
		$this->carabiner->css(array(
				array('libs/jquery.msgbox.css', 'screen'),
				array('libs/jquery.superbox.css', 'screen'),
				array('general/forms.css', 'screen'),
				array('general/tables.css', 'screen')
		));
		$this->carabiner->js(array(
				array('libs/jquery.msgbox.min.js'),
				array('libs/jquery.superbox.js'),
				array('compras/listado.js'),
				array('general/msgbox.js')
		));
		$this->load->library('pagination');
		
		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['opcmenu_active'] = 'Salidas'; //activa la opcion del menu
		$params['seo'] = array(
				'titulo' => 'Herramientas Prestadas'
		);
		
		$this->load->model('salidas_model');
		$params['data'] = $this->salidas_model->getHerramientas();
		
		foreach ($params['data']['herramientas'] as $key => $h) {
			if($h->dias_restantes<=3 && $h->dias_restantes>=1)
				$params['data']['herramientas'][$key]->style = 'style="background-color:#FFFFD9;"';
			elseif ($h->dias_restantes<=0)
				$params['data']['herramientas'][$key]->style = 'style="background-color:#FFE1E1;"';
			else
				$params['data']['herramientas'][$key]->style = '';
		}
		
		if(isset($_GET['msg']{0}))
				$params['frm_errors'] = $this->showMsgs($_GET['msg']);
		
		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/salidas/admin_herramientas', $params);
		$this->load->view('panel/footer');
	}
	
	private function cancelar(){
		if(isset($_GET['id'])){
			$this->load->model('salidas_model');
			$res = $this->salidas_model->cancelSalida();
			if($res[0])
				redirect(base_url('panel/salidas/?'.String::getVarsLink(array('id','msg')).'&msg=5'));
		}else redirect(base_url('panel/salidas/?'.String::getVarsLink(array('msg')).'&msg=1'));
	}
	
	private function entregado(){
		if(isset($_GET['id']{0})){
			$this->load->model('salidas_model');
			$result = $this->salidas_model->entregar_herramienta();
			if($result[0]){
				if($_GET['r']==1)
					redirect(base_url('panel/home/?'.String::getVarsLink(array('id','msg','r')).'&msg=3'));
				elseif($_GET['r']==2)
					redirect(base_url('panel/salidas/ver_todos/?'.String::getVarsLink(array('id','msg','r')).'&msg=7'));
			}
				
		}redirect(base_url('panel/home/?'.String::getVarsLink(array('id','msg','r')).'&msg=1'));
	}
	
	private function extender_plazo(){
		if(isset($_GET['id']{0})){
				$this->load->model('salidas_model');
				$result = $this->salidas_model->extender_plazo_herramienta();
				if($result[0])
					if($_GET['r']==1)
						redirect(base_url('panel/home/?'.String::getVarsLink(array('id','msg','r')).'&msg=4'));
					elseif($_GET['r']==2)
						redirect(base_url('panel/salidas/ver_todos/?'.String::getVarsLink(array('id','msg','r')).'&msg=8'));
			}redirect(base_url('panel/home/?'.String::getVarsLink(array('id','msg','r')).'&msg=1'));
	}
	
	private function configAddSalida($tipo='ni'){
		$this->load->library('form_validation');
		
		$tipo = isset($_POST['dtipo_salida']) ? $this->input->post('dtipo_salida') : $tipo;
		$rules = array(
					array('field'	=> 'dtipo',
							'label'	=> 'Tipo',
							'rules'	=> 'required'),
					array('field'	=> 'dtipo_salida',
							'label'	=> 'Tipo de Salida',
						  	'rules'	=> 'required'),
					array('field'	=> 'dfecha',
							'label'	=> 'Fecha',
							'rules'	=> 'required|max_length[10]|callback_isValidDate'),
					array('field'	=> 'dfolio',
							'label'	=> 'Folio',
							'rules'	=> 'required|is_natural_no_zero|callback_check_folio'),
					array('field'	=> 'dtsubtotal',
							'label'		=> 'SubTotal',
							'rules'		=> 'required|numeric'),
					array('field'	=> 'dtiva',
							'label'		=> 'IVA',
							'rules'		=> 'required|numeric'),
					array('field'	=> 'dttotal',
							'label'		=> 'Total',
							'rules'		=> 'required|numeric|callback_val_total'),
					array('field'	=> 'dttotal_letra',
							'label'		=> '',
							'rules'		=> '')
				);
		
		if($tipo=='av'){
			$rules[] = array('field'	=> 'davion',
							'label'		=> '',
							'rules'		=> '');
			$rules[] = array('field'	=> 'did_avion',
							'label'		=> 'Avion',
							'rules'		=> 'required');
		}
		elseif($tipo=='tr'){
			$rules[] = array('field'	=> 'dtrabajador',
							'label'		=> '',
							'rules'		=> '');
			$rules[] = array('field'	=> 'did_trabajador',
							'label'		=> 'Trabajador',
							'rules'		=> 'required');
			$rules[] = array('field'	=> 'dfecha_entrega',
							'label'		=> 'Fecha de entrega',
							'rules'		=> 'required|max_length[10]|callback_isValidDate');
			$rules[] = array('field'	=> 'dtipo_trabajador',
							'label'		=> '',
							'rules'		=> '');
		}
		elseif($tipo=='ve'){
			$rules[] = array('field'	=> 'dvehiculo',
							'label'		=> '',
							'rules'		=> '');
			$rules[] = array('field'	=> 'did_vehiculo',
							'label'		=> 'Vehículo',
							'rules'		=> 'required');
		}
		$this->form_validation->set_rules($rules);
	}
	
	private function imprimir(){
		if(isset($_GET['id']{0})){
			$this->load->model('salidas_model');
			$res = $this->salidas_model->getInfoSalida($_GET['id']);
			
			$subtotal = 0;
			$iva = 0;
			$total = 0;
			
			$this->load->library('mypdf');
			// Creacion del objeto de la clase heredada
			$pdf = new MYpdf('P', 'mm', 'Letter');
			$pdf->show_head = false;
			$pdf->AddPage();
			$pdf->SetFont('Arial','',8);
				
			$y = 30;
			$pdf->Image(APPPATH.'/images/logo.png',15,10,25,25,"PNG");
			
			$txt = ($res['info']->status == 'sa')?"SALIDA DE ALMACEN":"BAJA DE PRODUCTO";
			$pdf->SetFont('Arial','B',17);
			$pdf->SetXY(45, $y-10);
			$pdf->MultiCell(120, 6, "F U M I G A C I O N E S   A E R E A S \n\n $txt" , 0, 'C',0);
				
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
			$pdf->Cell(30, 10, $res['info']->folio , 0, 0, 'C');
				
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
			$pdf->Cell(30, 7, $res['info']->fecha , 1, 0, 'C',1);
				
			// ----------- DATOS DEL AVION|TRABAJADOR|VEHICULO ------------------
	
			$pdf->SetXY(15, ($y+9));
			$pdf->Cell(153, 23, '' , 1, 0, 'C');
			
			$txt='';
			switch($res['info']->tipo_salida){
				case 'av':$txt='AVIÓN';break;
				case 'tr':$txt='TRABAJADOR';break;
				case 've':$txt='VEHÍCULO';break;
				default:$txt='NINGUNO';break;
			}
				
			$pdf->SetFont('Arial','B',11);
			$pdf->SetTextColor('0','0','0');
			$pdf->SetXY(15, ($y+9));
			$pdf->Cell(20, 5, 'Tipo:', 0, 0, 'L');
			
			$pdf->SetFont('Arial','',12);
			$pdf->SetXY(35, ($y+9));
			$pdf->Cell(130, 5, $txt , 0, 0, 'L');
			
			if($res['info']->tipo_salida=='av'){
				$pdf->SetXY(15, ($y+15));
				$pdf->Cell(20, 5, 'Matrícula:' , 0, 0, 'L');
				$pdf->SetXY(15, ($y+22));
				$pdf->Cell(20, 5, 'Modelo:' , 0, 0, 'L');
				
				$pdf->SetFont('Arial','',12);
				$pdf->SetXY(35, ($y+15));
				$pdf->Cell(130, 5, strtoupper($res['info_tipo'][0]->matricula) , 0, 0, 'L');
				$pdf->SetXY(35, ($y+22));
				$pdf->Cell(130, 5, strtoupper($res['info_tipo'][0]->modelo), 0, 0, 'L');
			}
			elseif($res['info']->tipo_salida=='tr'){
				$domicilio =  ($res['info_tipo'][0]->calle!='')?$res['info_tipo'][0]->calle:'';
				$domicilio .=  ($res['info_tipo'][0]->no_exterior!='')?' No. '.$res['info_tipo'][0]->no_exterior:'';
				$domicilio .=  ($res['info_tipo'][0]->colonia!='')?' Col. '.$res['info_tipo'][0]->colonia:'';
				
				$pdf->SetXY(15, ($y+15));
				$pdf->Cell(20, 5, 'Nombre:' , 0, 0, 'L');
				$pdf->SetXY(15, ($y+22));
				$pdf->Cell(20, 5, 'Domicilio:' , 0, 0, 'L');
				
				$pdf->SetFont('Arial','',12);
				$pdf->SetXY(35, ($y+15));
				$pdf->Cell(130, 5, strtoupper($res['info_tipo'][0]->nombre), 0, 0, 'L');
				$pdf->SetXY(35, ($y+22));
				$pdf->Cell(130, 5, strtoupper($domicilio) , 0, 0, 'L');
			}
			elseif($res['info']->tipo_salida=='ve'){
				
				$txt = ($res['info_tipo'][0]->placas!='')?$res['info_tipo'][0]->placas:'';
				$txt .= ($res['info_tipo'][0]->modelo!='')?'           MODELO: '.$res['info_tipo'][0]->modelo:'';
				
				$pdf->SetXY(15, ($y+15));
				$pdf->Cell(20, 5, 'Nombre:' , 0, 0, 'L');
				$pdf->SetXY(15, ($y+22));
				$pdf->Cell(20, 5, 'Placas:' , 0, 0, 'L');
				
				$pdf->SetFont('Arial','',12);
				$pdf->SetXY(35, ($y+15));
				$pdf->Cell(130, 5, strtoupper($res['info_tipo'][0]->nombre) , 0, 0, 'L');
				$pdf->SetXY(35, ($y+22));
				$pdf->Cell(130, 5, strtoupper($txt), 0, 0, 'L');
			}				
			// ----------- TABLA CON LOS PRODUCTOS ------------------
			$pdf->SetY($y+33);
			$aligns = array('C', 'C');
			$widths = array(40, 145);
			$header = array('Cantidad', 'Descripción');
			foreach($res['productos'] as $key => $item){
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
				
				$subtotal += floatval($item->importe);
				$iva += floatval($item->importe_iva);
				$datos = array($item->cantidad.' '.$item->abreviatura, $item->nombre);
					
				$pdf->SetX(15);
				$pdf->SetAligns($aligns);
				$pdf->SetWidths($widths);
				$pdf->Row($datos, false);
			}
				
			//------------ SUBTOTAL, IVA ,TOTAL --------------------
			
// 			$total= floatval($subtotal + $iva);
// 			$y = $pdf->GetY();
// 			$pdf->SetFont('Arial','B',10);
// 			$pdf->SetTextColor(255,255,255);
// 			$pdf->SetFillColor(160,160,160);
				
// 			$pdf->SetXY(144, ($y+5));
// 			$pdf->Cell(23, 8, 'Subtotal' , 1, 0, 'C',1);
// 			$pdf->SetXY(144, ($y+13));
// 			$pdf->Cell(23, 8, 'IVA' , 1, 0, 'C',1);
// 			$pdf->SetXY(144, ($y+21));
// 			$pdf->Cell(23, 8, 'Total' , 1, 0, 'C',1);
				
// 			$pdf->SetTextColor(0,0,0);
// 			$pdf->SetFillColor(255,255,255);
// 			$pdf->SetXY(167, ($y+5));
// 			$pdf->Cell(33, 8, String::formatoNumero($subtotal,2) , 1, 0, 'C');
// 			$pdf->SetXY(167, ($y+13));
// 			$pdf->Cell(33, 8, String::formatoNumero($iva,2) , 1, 0, 'C');
// 			$pdf->SetXY(167, ($y+21));
// 			$pdf->Cell(33, 8, String::formatoNumero($total,2) , 1, 0, 'C');
				
// 			//------------ TOTAL CON LETRA--------------------
				
// 			$pdf->SetXY(15, ($y+5));
// 			$pdf->Cell(125, 24, '' , 1, 0, 'C');
				
// 			$pdf->SetFont('Arial','B',13);
// 			$pdf->SetXY(15, ($y+5));
// 			$pdf->Cell(60, 8, 'IMPORTE CON LETRA' , 0, 0, 'L');
				
// 			$pdf->SetFont('Arial','',12);
// 			$pdf->SetXY(15, ($y+13));
// 			$pdf->Cell(125, 16, String::num2letras($total) , 0, 0, 'C');
				
			$pdf->Output('salida.pdf', 'I');
		}
	}
	
	public function rsa()
	{
		$this->carabiner->css(array(
				array('general/forms.css', 'screen')
		));
		$this->carabiner->js(array(
				array('salidas/reporte_sa.js')
		));

		if (!isset($_GET['dfecha1'])) {
			$_GET['dfecha1'] = date('Y-m').'-01';
		}

		if (!isset($_GET['dfecha2'])) {
			$_GET['dfecha2'] = date('Y-m-d');
		}

		$params['seo'] = array(
				'titulo' => 'Reporte Salida Aviones'
		);

		$this->load->view('panel/salidas/reporte_sa', $params);
	}

	public function pdf_rsa()
	{
		$this->load->model('salidas_model');
		$data = $this->salidas_model->data_rsa();

		// var_dump($data);
		$this->salidas_model->pdf_rsa($data);
	}

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
	
	public function check_folio($str){
		if($str!=''){
			$total = $this->db->select("COUNT(*) as total")->from('salidas')->where('folio',$str)->get()->row()->total;
			if($total>0){
				$this->form_validation->set_message('check_folio', 'El folio ya esta utilizado.');
				return false;
			}
		}
	}
		
	/**
	 * Muestra mensajes cuando se realiza alguna accion
	 * @param unknown_type $tipo
	 * @param unknown_type $msg
	 * @param unknown_type $title
	 */
	private function showMsgs($tipo, $msg='', $title='Salidas!'){
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
				$txt = 'La salida se modifico correctamente.';
				$icono = 'ok';
				break;
			case 4:
				$txt = 'La salida se agrego correctamente.';
				$icono = 'ok';
				break;
			case 5:
				$txt = 'La salida se cancelo correctamente.';
				$icono = 'ok';
				break;
			case 6:
				$txt = $msg;
				$icono = 'ok';
				break;
			case 7:
				$txt = 'La herramienta se entrego correctamenta.';
				$icono = 'ok';
				break;
			case 8:
				$txt = 'La fecha se actualizo correctamente.';
				$icono = 'ok';
				break;
			case 9:
				$txt = 'La alerta se elimino correctamente.';
				$icono = 'ok';
				break;
		}
	
		return array(
				'title' => $title,
				'msg' => $txt,
				'ico' => $icono);
	}
	
}