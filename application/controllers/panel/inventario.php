<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class inventario extends MY_Controller {
	/**
	 * Evita la validacion (enfocado cuando se usa ajax). Ver mas en privilegios_model
	 * @var unknown_type
	 */
	private $excepcion_privilegio = array('inventario/nivelar/', 
			'inventario/ueps_pdf/', 'inventario/epu_pdf/',
			'inventario/epc_pdf/', 'inventario/epud_pdf/', 
			'inventario/epcd_pdf/', 'inventario/get_productos/');
	
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
	 * Default. Mustra el listado de cotizaciones para administrarlas
	 */
	public function index(){
		$this->carabiner->css(array(
			array('libs/jquery.msgbox.css', 'screen'),
			array('general/forms.css', 'screen'),
			array('general/tables.css', 'screen')
		));
		$this->carabiner->js(array(
			array('libs/jquery.msgbox.min.js'),
			array('libs/jquery.numeric.js'),
			array('inventario/nivelar.js'),
			array('general/msgbox.js')
		));
		$this->load->library('pagination');
		
		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['opcmenu_active'] = 'Almacen'; //activa la opcion del menu
		$params['seo'] = array(
			'titulo' => 'Nivelar Inventario'
		);
		
		$this->load->model('productos_model');
		$res = $this->productos_model->getFamilias();
		$params['familias'] = $res['familias'];
		
		if(isset($_GET['ffamilia']{0}))
			$params['id_familia'] = $_GET['ffamilia'];
		else
			$params['id_familia'] = $params['familias'][0]->id_familia;
		
		$params['productos'] = array();
		$res = $this->db->query("
			SELECT id_producto, nombre, abreviatura, entradas, salidas, existencia, precio_u, importe
			FROM reportes_costo_existencias1
			WHERE id_familia = '".$params['id_familia']."'
			ORDER BY nombre ASC
			");
		if($res->num_rows() > 0)
			$params['productos'] = $res->result();
		
		
		if(isset($_GET['msg']{0}))
			$params['frm_errors'] = $this->showMsgs($_GET['msg']);
		
		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/inventario/nivelar', $params);
		$this->load->view('panel/footer');
	}
	
	/**
	 * Nivela el inventario para los productos de una familia
	 */
	public function nivelar(){
		$this->load->model('inventario_model');
		$this->inventario_model->nivelar();
		header("Location: ".base_url('panel/inventario?msg=3'));
	}
	
	
	/**
	 * muestra el fomulario para que se pueda obtener el inventario ueps
	 */
	public function ueps(){
		$this->carabiner->css(array(
			array('libs/jquery.msgbox.css', 'screen'),
			array('general/forms.css', 'screen'),
			array('general/reportes.css', 'screen')
		));
		$this->carabiner->js(array(
			array('libs/jquery.msgbox.min.js'),
			array('general/msgbox.js'),
			array('inventario/reportes.js')
		));
		$params['seo'] = array(
			'titulo' => 'Reporte de inventario UEPS'
		);
		
		$params['fecha1'] = date("Y-m").'-01';
		$params['fecha2'] = date("Y-m-d");
		
		$res = $this->db->query("SELECT id_familia, codigo, nombre FROM productos_familias WHERE status = 'ac'");
		$params['familias'] = array();
		if($res->num_rows() > 0)
			$params['familias'] = $res->result();
		
		$params['lista_productos'] = $this->load->view('panel/inventario/lista_productos', $params, true);
		
		//$this->load->model('inventario_model');
		//$this->inventario_model->ueps(false, 'l4f721650b23846.06627011', '2012-04-01', '2012-05-18', true);
		
		$this->load->view('panel/inventario/verUeps', $params);
	}
	
	/**
	 * obtiene el inventario ueps en pdf
	 */
	public function ueps_pdf(){
		$this->load->model('inventario_model');
		$this->inventario_model->ueps(false, $this->input->get('id_producto'), 
				$this->input->get('dfecha1'), 
				$this->input->get('dfecha2'), true);
	}
	
	
	/**
	 * muestra el fomulario para que se pueda obtener el reporte
	 * Existencias por unidad
	 */
	public function epu(){
		$this->carabiner->css(array(
			array('libs/jquery.msgbox.css', 'screen'),
			array('general/forms.css', 'screen'),
			array('general/reportes.css', 'screen')
		));
		$this->carabiner->js(array(
			array('libs/jquery.msgbox.min.js'),
			array('general/msgbox.js'),
			array('inventario/reportes.js')
		));
		$params['seo'] = array(
			'titulo' => 'Reporte de existencia por unidades'
		);
	
		$params['fecha1'] = date("Y-m").'-01';
		$params['fecha2'] = date("Y-m-d");
	
		$res = $this->db->query("SELECT id_familia, codigo, nombre FROM productos_familias WHERE status = 'ac'");
		$params['familias'] = array();
		if($res->num_rows() > 0)
			$params['familias'] = $res->result();
	
		$this->load->view('panel/inventario/verEpu', $params);
	}
	
	/**
	 * obtiene el reporte existencias por unidad en pdf
	 */
	public function epu_pdf(){
		$fecha1 = isset($_GET['dfecha1'])? $this->input->get('dfecha1'): date("Y-m").'-01';
		$fecha2 = isset($_GET['dfecha2'])? $this->input->get('dfecha2'): date("Y-m-d");
		
		$this->load->model('inventario_model');
		$data = $this->inventario_model->epu_epc($fecha1, $fecha2);
		$this->inventario_model->pdfEpu($data, $fecha1, $fecha2);
	}
	
	/**
	 * muestra el fomulario para que se pueda obtener el reporte
	 * Existencias por costo
	 */
	public function epc(){
		$this->carabiner->css(array(
				array('libs/jquery.msgbox.css', 'screen'),
				array('general/forms.css', 'screen'),
				array('general/reportes.css', 'screen')
		));
		$this->carabiner->js(array(
				array('libs/jquery.msgbox.min.js'),
				array('general/msgbox.js'),
				array('inventario/reportes.js')
		));
		$params['seo'] = array(
				'titulo' => 'Reporte de existencia por costo'
		);
	
		$params['fecha1'] = date("Y-m").'-01';
		$params['fecha2'] = date("Y-m-d");
	
		$res = $this->db->query("SELECT id_familia, codigo, nombre FROM productos_familias WHERE status = 'ac'");
		$params['familias'] = array();
		if($res->num_rows() > 0)
			$params['familias'] = $res->result();
	
		$this->load->view('panel/inventario/verEpc', $params);
	}
	
	/**
	 * obtiene el reporte existencias por costo en pdf
	 */
	public function epc_pdf(){
		$fecha1 = isset($_GET['dfecha1'])? $this->input->get('dfecha1'): date("Y-m").'-01';
		$fecha2 = isset($_GET['dfecha2'])? $this->input->get('dfecha2'): date("Y-m-d");
	
		$this->load->model('inventario_model');
		$data = $this->inventario_model->epu_epc($fecha1, $fecha2);
		$this->inventario_model->pdfEpc($data, $fecha1, $fecha2);
	}
	
	
	/**
	 * obtiene el reporte existencias por unidad desglosado de un producto en pdf
	 */
	public function epud_pdf(){
		$fecha1 = isset($_GET['dfecha1'])? $this->input->get('dfecha1'): date("Y-m").'-01';
		$fecha2 = isset($_GET['dfecha2'])? $this->input->get('dfecha2'): date("Y-m-d");
		$id_producto = isset($_GET['id_producto'])? $this->input->get('id_producto'): '';
	
		$this->load->model('inventario_model');
		$data = $this->inventario_model->epud_epcd($id_producto, $fecha1, $fecha2);
		$this->inventario_model->pdfEpud($data, $fecha1, $fecha2);
	}
	
	/**
	 * obtiene el reporte existencias por costo desglosado de un producto en pdf
	 */
	public function epcd_pdf(){
		$fecha1 = isset($_GET['dfecha1'])? $this->input->get('dfecha1'): date("Y-m").'-01';
		$fecha2 = isset($_GET['dfecha2'])? $this->input->get('dfecha2'): date("Y-m-d");
		$id_producto = isset($_GET['id_producto'])? $this->input->get('id_producto'): '';
	
		$this->load->model('inventario_model');
		$data = $this->inventario_model->epud_epcd($id_producto, $fecha1, $fecha2);
		$this->inventario_model->pdfEpcd($data, $fecha1, $fecha2);
	}
	
	
	/**
	 * Obtiene los productos y familias para los reportes (formato <option>)
	 */
	public function get_productos(){
		$res = $this->db->query("SELECT id_familia, codigo, nombre FROM productos_familias WHERE status = 'ac'");
		$params['familias'] = array();
		if($res->num_rows() > 0)
			$params['familias'] = $res->result();
		
		$this->load->view('panel/inventario/lista_productos', $params);
	}
	
	
	
	/**
	 * Muestra mensajes cuando se realiza alguna accion
	 * @param unknown_type $tipo
	 * @param unknown_type $msg
	 * @param unknown_type $title
	 */
	private function showMsgs($tipo, $msg='', $title='Nivelar Inventario!'){
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
				$txt = 'Los productos se nivelaron correctamente.';
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