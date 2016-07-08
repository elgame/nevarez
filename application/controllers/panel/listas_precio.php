<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class listas_precio extends MY_Controller {
	/**
	 * Evita la validacion (enfocado cuando se usa ajax). Ver mas en privilegios_model
	 * @var unknown_type
	 */
	private $excepcion_privilegio = array('listas_precio/imprime_lista/');
	
	
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
			array('general/tables.css', 'screen'),
			array('productos/familias_productos.css', 'screen')
		));
		$this->carabiner->js(array(
			array('libs/jquery.msgbox.min.js'),
			array('libs/jquery.numeric.js'),
			array('general/msgbox.js'),
			array('productos/listas_precio/listas_precio.js')
		));
		$this->load->library('pagination');
		$this->load->model('productos_model');
		$this->load->model('listas_precio_model');
		
		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['opcmenu_active'] = 'Almacen'; //activa la opcion del menu
		$params['seo'] = array(
			'titulo' => 'Administrar listas de precio'
		);
		
		//Filtro familia
		$params['idfamilia'] = null;
		if($this->input->get('ffamilia') != '0')
			$params['idfamilia'] = $this->input->get('ffamilia');
		
		$params['familias'] = $this->productos_model->getFamilias();
		$params['listas'] = $this->listas_precio_model->obtenListasPrecio();
		$params['tbl_precios'] = $this->listas_precio_model->createTblPrecios($params['listas'], $params['idfamilia']);
		
		
		if(isset($_GET['msg']{0}))
			$params['frm_errors'] = $this->showMsgs($_GET['msg']);
		
		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/productos/listas_precio/listado', $params);
		$this->load->view('panel/footer');
	}
	
	
	public function agregar(){
		$this->carabiner->css(array(
				array('general/forms.css', 'screen')
		));
		
		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['opcmenu_active'] = 'Almacen'; //activa la opcion del menu
		$params['seo'] = array(
				'titulo' => 'Agregar lista de precios'
		);
		
		$this->load->library('form_validation');
		$rules = array(
			array('field'	=> 'dnombre',
					'label'		=> 'Nombre',
					'rules'		=> 'required|max_length[30]'),
			array('field'	=> 'des_default',
					'label'		=> 'Precio publico',
					'rules'		=> 'max_length[2]')
		);
		$this->form_validation->set_rules($rules);
		
		if($this->form_validation->run() == FALSE){
			$params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
		}else{
			$this->load->model('listas_precio_model');
			$respons[0] = $this->listas_precio_model->addLista();
				
			if($respons[0])
				redirect(base_url('panel/listas_precio/agregar/?'.String::getVarsLink(array('msg')).'&msg=4'));
		}
		
		if(isset($_GET['msg']{0}))
			$params['frm_errors'] = $this->showMsgs($_GET['msg']);
		
		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/productos/listas_precio/agregar_lista', $params);
		$this->load->view('panel/footer');
	}
	
	/**
	 * Cambia el precio de un producto para una determinada lista de precios, con Ajax
	 */
	public function cambiar_precio(){
		if(isset($_POST['id_producto']{0}) && isset($_POST['id_lista']{0})){
			$this->load->model('listas_precio_model');
			$this->listas_precio_model->updatePrecioLista();
			$params['msg'] = $this->showMsgs(3);
		}else
			$params['msg'] = $this->showMsgs(1);
	
		echo json_encode($params);
	}
	
	/**
	 * Imprime el listado de precios de acuerdo a los filtros
	 */
	public function imprime_lista(){
		$this->load->model('productos_model');
		$this->load->model('listas_precio_model');
	
		//Filtro familia
		$params['idfamilia'] = null;
		if($this->input->get('ffamilia') != '0')
			$params['idfamilia'] = $this->input->get('ffamilia');
		$ids = "'".str_replace(',', "','", substr($this->input->get('listasid'), 1))."'";
	
		$params['familias'] = $this->productos_model->getFamilias();
		$params['listas'] = $this->listas_precio_model->obtenListasPrecio("id_lista IN(".$ids.")");
		$params['tbl_precios'] = $this->listas_precio_model->createTblPrecios($params['listas'], $params['idfamilia'],
				false);
	
	
		$this->load->library('mypdf');
		// Creación del objeto de la clase heredada
		$pdf = new MYpdf('P', 'mm', 'Letter');
		$pdf->titulo2 = 'Listas de precio';
		if($params['idfamilia'] != null)
			$pdf->titulo3 = 'Familia: '.$this->input->get('familia')."\n";
		if($this->input->get('listas') != '')
			$pdf->titulo3 .= "Listas: ".$this->input->get('listas');
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->SetFont('Arial','',8);
	
		$header = array();
		foreach($params['tbl_precios']['tabla'] as $key => $rows){
			$aligns = array();
			$widths = array();
			$datos = array();
				
			$band_head = false;
			if($pdf->GetY() >= $pdf->limiteY){ //salta de pagina si exede el max
				$pdf->AddPage();
				$band_head = true;
			}
	
			foreach($rows as $key2 => $cols){
				if($key2==0){ //codigo producto
					$aligns[$key2] = 'C';
					$widths[$key2] = 30;
				}elseif($key2==1){ //nombre producto
					$aligns[$key2] = 'L';
					$widths[$key2] = 70;
				}else{ //listas precios
					$aligns[$key2] = 'C';
					$widths[$key2] = 25;
						
					$cols = explode('|', $cols); //id_producto|precio|id_lista
					if(isset($cols[1]))
						$cols = String::formatoNumero($cols[1]);
					else
						$cols = $cols[0];
				}
				$datos[$key2] = $cols;
			}
				
			$pdf->SetFont('Arial','',8);
			$pdf->SetTextColor(0,0,0);
			if($key == 0 || $band_head){ //guardo el header de la tabla para las nuevas paginas
				if($key == 0){
					$header[] = $aligns;
					$header[] = $widths;
					$header[] = $datos;
				}
				$band_head = true;
				$pdf->SetFont('Arial','B',8);
				$pdf->SetTextColor(255,255,255);
				$pdf->SetFillColor(160,160,160);
			}
				
			$pdf->SetX(6);
			$pdf->SetAligns($aligns);
			$pdf->SetWidths($widths);
			$pdf->Row($datos, $band_head);
		}
	
		$pdf->Output('listas_de_precio.pdf', 'I');
	}
	
	
	
	/**
	 * Muestra mensajes cuando se realiza alguna accion
	 * @param unknown_type $tipo
	 * @param unknown_type $msg
	 * @param unknown_type $title
	 */
	private function showMsgs($tipo, $msg='', $title='Listas de precio!'){
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
				$txt = 'El precio se actualizo correctamente.';
				$icono = 'ok';
				break;
			case 4:
				$txt = 'La lista se agrego correctamente.';
				$icono = 'ok';
				break;
			case 5:
				$txt = 'La familia se elimino correctamente.';
				$icono = 'ok';
				break;
			case 6:
				$txt = 'El producto se agrego correctamente.';
				$icono = 'ok';
				break;
			case 7:
				$txt = 'El producto se elimino correctamente.';
				$icono = 'ok';
				break;
			case 8:
				$txt = 'El producto se modifico correctamente.';
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