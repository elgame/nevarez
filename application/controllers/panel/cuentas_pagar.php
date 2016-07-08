<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class cuentas_pagar extends MY_Controller {
	/**
	 * Evita la validacion (enfocado cuando se usa ajax). Ver mas en privilegios_model
	 * @var unknown_type
	 */
	private $excepcion_privilegio = array(
			'cuentas_pagar/cxp_pdf/', 'cuentas_pagar/cxp_xls/',
			'cuentas_pagar/cdp_pdf/', 'cuentas_pagar/cdp_xls/',
			'cuentas_pagar/detalle_pdf/');
	
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
	
	/************ CUENTAS X PAGAR *************/
	/**
	 * Default. Mustra el listado de proveedores a los q se les debe
	 * Es cuentas por pagar
	 */
	public function index(){
		$this->carabiner->css(array(
			array('libs/jquery.msgbox.css', 'screen'),
			array('general/forms.css', 'screen'),
			array('general/tables.css', 'screen')
		));
		$this->carabiner->js(array(
			array('libs/jquery.msgbox.min.js'),
			array('compras/cuentas_pagar/listados.js'),
			array('general/msgbox.js')
		));
		$this->load->library('pagination');
		
		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['opcmenu_active'] = 'Compras'; //activa la opcion del menu
		$params['seo'] = array(
			'titulo' => 'Cuentas por pagar'
		);
		
		$this->load->model('cuentas_pagar_model');
		$params['cuentasp'] = $this->cuentas_pagar_model->getCuentasXPagarData();
		
		if(isset($_GET['msg']{0}))
			$params['frm_errors'] = $this->showMsgs($_GET['msg']);
		
		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/compras/cuentas_pagar/cuentasXPagar', $params);
		$this->load->view('panel/footer');
	}
	
	/**
	 * Descarga el listado de cuentas por pagar en formato pdf
	 */
	public function cxp_pdf(){
		$this->load->model('cuentas_pagar_model');
		$this->cuentas_pagar_model->cuentasXPagarPdf();
	}
	
	
	/************ CUENTA DEL PROVEEDOR *************/
	/**
	 * Muestra el listado de compras de un proveedor seleccionado, 
	 * muestra el saldo de acuerdo al rango de fechas
	 */
	public function cuenta_proveedor(){
		$this->carabiner->css(array(
				array('libs/jquery.msgbox.css', 'screen'),
				array('general/forms.css', 'screen'),
				array('general/tables.css', 'screen')
		));
		$this->carabiner->js(array(
				array('libs/jquery.msgbox.min.js'),
				array('compras/cuentas_pagar/listados.js'),
				array('general/msgbox.js')
		));
		$this->load->library('pagination');
		
		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['opcmenu_active'] = 'Compras'; //activa la opcion del menu
		$params['seo'] = array(
				'titulo' => 'Cuenta de '
		);
		
		if(isset($_GET['id_proveedor']{0})){
			$this->load->model('cuentas_pagar_model');
			$params['cuentasp'] = $this->cuentas_pagar_model->getCuentaProveedorData();
			
			$params['seo']['titulo'] .= $params['cuentasp']['proveedor']->nombre;
			
			if(isset($_GET['msg']{0}))
				$params['frm_errors'] = $this->showMsgs($_GET['msg']);
		}else
			$params['frm_errors'] = $this->showMsgs(1);
	
		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/compras/cuentas_pagar/cuentaProveedor', $params);
		$this->load->view('panel/footer');
	}
	
	/**
	 * Muestra el listado de compras en PDF de un proveedor seleccionado,
	 * muestra el saldo de acuerdo al rango de fechas
	 */
	public function cdp_pdf(){
		$this->load->model('cuentas_pagar_model');
		$this->cuentas_pagar_model->cuentaProveedorPdf();
	}
	
	/**
	 * Muestra el listado de compras en XLS de un proveedor seleccionado,
	 * muestra el saldo de acuerdo al rango de fechas
	 */
	public function cdp_xls(){
		$this->load->model('cuentas_pagar_model');
		$this->cuentas_pagar_model->cuentaProveedorExcel();
	}
	
	
	/******* DETALLE DE FACTURAS *******/
	/**
	 * Muestra el listado de abonos para una compra determinada,
	 * muestra el saldo de acuerdo al rango de fechas
	 */
	public function detalle(){
		$this->carabiner->css(array(
				array('libs/jquery.msgbox.css', 'screen'),
				array('libs/jquery.superbox.css', 'screen'),
				array('general/forms.css', 'screen'),
				array('general/tables.css', 'screen')
		));
		$this->carabiner->js(array(
				array('libs/jquery.msgbox.min.js'),
				array('libs/jquery.superbox.js'),
				array('compras/cuentas_pagar/listados.js'),
				array('general/msgbox.js'),
				array('general/util.js')
		));
		$this->load->library('pagination');
	
		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['opcmenu_active'] = 'Compras'; //activa la opcion del menu
		$params['seo'] = array(
				'titulo' => 'Detalle de factura '
		);
	
		if(isset($_GET['id_proveedor']{0}) && isset($_GET['id_compra']{0})){
			$this->load->model('cuentas_pagar_model');
			$params['cuentasp'] = $this->cuentas_pagar_model->getDetalleFacturaData();
				
			$params['seo']['titulo'] .= $params['cuentasp']['compra']->serie.'-'.$params['cuentasp']['compra']->folio;
				
			if(isset($_GET['msg']{0}))
				$params['frm_errors'] = $this->showMsgs($_GET['msg']);
		}else
			$params['frm_errors'] = $this->showMsgs(1);

		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/compras/cuentas_pagar/detalle', $params);
		$this->load->view('panel/footer');
	}
	
	/**
	 * Muestra el listado de abonos en PDF para una compra determinada,
	 * muestra el saldo de acuerdo al rango de fechas
	 */
	public function detalle_pdf(){
		$this->load->model('cuentas_pagar_model');
		$this->cuentas_pagar_model->detalleFacturaPdf();
	}
	
	
	
	
	
	/**
	 * Agrega una compra a la bd
	 */
	public function agregar(){
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
			array('compras/frm_addmod.js')
		));
		
		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['opcmenu_active'] = 'Compras'; //activa la opcion del menu
		$params['seo'] = array(
			'titulo' => 'Agregar Compra'
		);
		
		$this->configAddModCompra();
		
		if($this->form_validation->run() == FALSE){
			$params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
		}else{
			$this->load->model('compras_model');
			$respons = $this->compras_model->addCompra();
			
			if($respons[0])
				redirect(base_url('panel/compras/agregar/?'.String::getVarsLink(array('msg')).'&msg=4'));
		}
		
		$params['fecha'] = date("Y-m-d");
		$params['plazo_credito'] = 7;
		
		if(isset($_GET['msg']{0}))
			$params['frm_errors'] = $this->showMsgs($_GET['msg']);
		
		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/compras/agregar', $params);
		$this->load->view('panel/footer');
	}
	
	/**
	 * Agrega una compra a la bd
	 */
	public function agregar_gasto(){
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
				array('compras/frm_addmod.js')
		));
	
		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['opcmenu_active'] = 'Compras'; //activa la opcion del menu
		$params['seo'] = array(
				'titulo' => 'Agregar Gasto'
		);
	
		$this->configAddModCompra();
	
		if($this->form_validation->run() == FALSE){
			$params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
		}else{
			$this->load->model('compras_model');
			$respons = $this->compras_model->addCompra();
				
			if($respons[0])
				redirect(base_url('panel/compras/agregar_gasto/?'.String::getVarsLink(array('msg')).'&msg=4'));
		}
	
		$params['fecha'] = date("Y-m-d");
		$params['plazo_credito'] = 7;
	
		if(isset($_GET['msg']{
			0}))
				$params['frm_errors'] = $this->showMsgs($_GET['msg']);
	
			$this->load->view('panel/header', $params);
			$this->load->view('panel/general/menu', $params);
			$this->load->view('panel/compras/agregar_gasto', $params);
			$this->load->view('panel/footer');
	}
	
	/**
	 * muesta la info de una compra sin dejar editar la info
	 */
	public function ver(){
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
			array('compras/frm_addmod.js')
		));
		
		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['opcmenu_active'] = 'Compras'; //activa la opcion del menu
		$params['seo'] = array(
			'titulo' => 'Ver compras'
		);
		
		if(isset($_GET['id']{0})){
			/*$this->configAddModCompra();
			
			if($this->form_validation->run() == FALSE){
				$params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
			}else{
				$respons = $this->empleados_model->updatePrivilegio();
				
				if($respons[0])
					redirect(base_url('panel/privilegios/modificar/?'.String::getVarsLink(array('msg')).'&msg=3'));
			}*/
			$this->load->model('compras_model');
			$params['inf'] = $this->compras_model->getInfoCompra($_GET['id']);
			if(!is_array($params['inf']))
				unset($params['inf']);
			
			if(isset($_GET['msg']{0}))
				$params['frm_errors'] = $this->showMsgs($_GET['msg']);
		}else
			$params['frm_errors'] = $this->showMsgs(1);
		
		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/compras/ver', $params);
		$this->load->view('panel/footer');
	}
	
	/**
	 * Elimina una compra de la bd (le cambia el status a ca:cancelada)
	 */
	public function eliminar(){
		if(isset($_GET['id']{0})){
			$this->load->model('compras_model');
			$respons = $this->compras_model->cancelCompra();
			
			if($respons[0])
				redirect(base_url('panel/compras/?msg=5'));
		}else
			$params['frm_errors'] = $this->showMsgs(1);
	}
	
	
	/**
	 * Configura los metodos de agregar y modificar
	 */
	private function configAddModCompra(){
		$this->load->library('form_validation');
		$rules = array(
			array('field'	=> 'did_proveedor',
					'label'		=> 'Proveedor',
					'rules'		=> 'required|max_length[25]'),
			array('field'	=> 'dserie',
					'label'		=> 'Serie',
					'rules'		=> 'max_length[4]'),
			array('field'	=> 'dfolio',
					'label'		=> 'Folio',
					'rules'		=> 'required|numeric|callback_seriefolio_check'),
			array('field'	=> 'dfecha',
					'label'		=> 'Fecha',
					'rules'		=> 'required|max_length[10]|callback_isValidDate'),
			array('field'	=> 'dtsubtotal',
					'label'		=> 'SubTotal',
					'rules'		=> 'required|numeric'),
			array('field'	=> 'dtiva',
					'label'		=> 'IVA',
					'rules'		=> 'required|numeric'),
			array('field'	=> 'dttotal',
					'label'		=> 'Total',
					'rules'		=> 'required|numeric'),
			array('field'	=> 'dconcepto',
					'label'		=> 'Concepto',
					'rules'		=> 'max_length[200]'),
			array('field'	=> 'dcondicion_pago',
					'label'		=> 'Condición de pago',
					'rules'		=> 'max_length[2]'),
			array('field'	=> 'dplazo_credito',
					'label'		=> 'Plazo de crédito',
					'rules'		=> 'numeric'),

			array('field'	=> 'dproveedor',
					'label'		=> 'Proveedor',
					'rules'		=> ''),
			array('field'	=> 'dproveedor_info',
					'label'		=> 'Proveedor',
					'rules'		=> ''),
			array('field'	=> 'dttotal_letra',
					'label'		=> 'letra',
					'rules'		=> '')
		);
		$this->form_validation->set_rules($rules);
	}
	/**
	 * Form_validation: Valida si el usuario ya esta usado por alguien mas
	 * @param unknown_type $str
	 */
	public function seriefolio_check($str){
		if($str != ''){
			$sql = '';
			if(isset($_GET['id']))
				$sql = " AND id_compra != '".$_GET['id']."'";
				
			$res = $this->db->select('Count(id_compra) AS num')
				->from('compras')
				->where("id_proveedor = '".$this->input->post('did_proveedor')."' 
						AND serie = '".mb_strtoupper($this->input->post('dserie'), 'utf-8')."' AND folio = ".$str."".$sql)
			->get();
			$data = $res->row();
			if($data->num > 0){
				$this->form_validation->set_message('seriefolio_check', 'La serie y el folio ya esta utilizado para el proveedor seleccionado.');
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
	
	
	/**
	 * Muestra mensajes cuando se realiza alguna accion
	 * @param unknown_type $tipo
	 * @param unknown_type $msg
	 * @param unknown_type $title
	 */
	private function showMsgs($tipo, $msg='', $title='Compras!'){
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
				$txt = 'El abono se elimino correctamente.';
				$icono = 'ok';
			break;
			case 4:
				$txt = 'La compra se agrego correctamente.';
				$icono = 'ok';
			break;
			case 5:
				$txt = 'La compra se cancelo correctamente.';
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