<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class productos extends MY_Controller {
	/**
	 * Evita la validacion (enfocado cuando se usa ajax). Ver mas en privilegios_model
	 * @var unknown_type
	 */
	private $excepcion_privilegio = array('productos/ajax_productos_familia/', 'productos/ajax_familia/', 
			'productos/ajax_productos_tbl/', 'productos/ajax_productos_addmod/', 'productos/ajax_get_productos/');
	
	
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
			array('libs/jquery.superbox.css', 'screen'),
			array('general/forms.css', 'screen'),
			array('general/tables.css', 'screen'),
			array('productos/familias_productos.css', 'screen')
		));
		$this->carabiner->js(array(
			array('libs/jquery.msgbox.min.js'),
			array('libs/jquery.superbox.js'),
			array('general/msgbox.js'),
			array('productos/familias_productos.js')
		));
		$this->load->model('productos_model');
		
		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['opcmenu_active'] = 'Almacen'; //activa la opcion del menu
		$params['seo'] = array(
			'titulo' => 'Administrar productos'
		);
		
		$params['familias'] = $this->productos_model->getFamilias();
		//generamos la tabla
		$params['tabla_familias'] = $this->load->view('panel/productos/listado_familias', $params, true);
		
		
		if(isset($_GET['msg']{0}))
			$params['frm_errors'] = $this->showMsgs($_GET['msg']);
		
		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/productos/listado', $params);
		$this->load->view('panel/footer');
	}
	
	/**
	 * Obtiene el listado de familias utilizando Ajax
	 */
	public function ajax_familia(){
		$this->load->model('productos_model');
		
		$params['familias'] = $this->productos_model->getFamilias();
			
		$this->load->view('panel/productos/listado_familias', $params);
	}
	
	/**
	 * Agrega una familia utilizando el superbox
	 */
	public function agregar_familia(){
		$this->carabiner->css(array(
				array('general/forms.css', 'screen')
		));
		
		$params['seo'] = array(
				'titulo' => 'Agregar familia'
		);
		
		$this->load->library('form_validation');
		$rules = array(
			array('field'	=> 'dnombre',
					'label'	=> 'Nombre',
					'rules'	=> 'required|max_length[60]'),
			array('field'	=> 'dcodigo',
					'label'	=> 'Código',
					'rules'	=> 'required|max_length[8]|callback_val_codigo'),
			array('field'	=> 'dtipo',
					'label'	=> 'Asignado a',
					'rules'	=> 'required'));
		$this->form_validation->set_rules($rules);
		if($this->form_validation->run() == FALSE){
			$params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()), 'Familias!');
		}else{
			$this->load->model('productos_model');
			$respons = $this->productos_model->addFamilia();
				
			if($respons[0]){
				$params['load_familias'] = true;
				$params['frm_errors'] = $this->showMsgs(4, '', 'Familias!');
			}
		}
		
		if(isset($_GET['msg']{0}))
				$params['frm_errors'] = $this->showMsgs($_GET['msg'], '', 'Familias!');
		
		$this->load->view('panel/productos/agregar_familia', $params);
	}
	
	/**
	 * Modifica una familia utilizando el superbox
	 */
	public function modificar_familia(){
		$this->carabiner->css(array(
				array('general/forms.css', 'screen')
		));
	
		$params['seo'] = array(
				'titulo' => 'Modificar familia'
		);
		
		if(isset($_GET['id']{0})){
			$this->load->model('productos_model');
			$this->load->library('form_validation');
			$rules = array(
				array('field'	=> 'dnombre',
						'label'	=> 'Nombre',
						'rules'	=> 'required|max_length[60]'),
				array('field'	=> 'dcodigo',
						'label'	=> 'Código',
						'rules'	=> 'required|max_length[8]|callback_val_codigo'),
				array('field'	=> 'dtipo',
						'label'	=> 'Asignado a',
						'rules'	=> 'required'));
			$this->form_validation->set_rules($rules);
			if($this->form_validation->run() == FALSE){
				$params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()), 'Familias!');
			}else{
				$respons = $this->productos_model->updateFamilia();
		
				if($respons[0]){
					$params['load_familias'] = true;
					$params['frm_errors'] = $this->showMsgs(3, '', 'Familias!');
				}
			}
			
			//obtiene la info de la familia
			$params['info_familia'] = $this->productos_model->getInfoFamilia($_GET['id']);	
		
			if(isset($_GET['msg']{0}))
				$params['frm_errors'] = $this->showMsgs($_GET['msg'], '', 'Familias!');
		}else
			$this->showMsgs(1, '', 'Familias!');
		
		$this->load->view('panel/productos/modificar_familia', $params);
	}
	/**
	 * Desactiva una familia y los productos de la misma
	 */
	public function desactivar_familia(){
		if(isset($_GET['id']{0})){
			$this->load->model('productos_model');
			$this->productos_model->desactivarFamilia();
			$params['msg'] = $this->showMsgs(5, '', 'Familias!');
		}else
			$params['msg'] = $this->showMsgs(1, '', 'Familias!');		
		echo json_encode($params);
	}
	
	
	
	/****** Productos *****/
	/**
	 * Agrega un producto utilizando el superbox
	 */
	public function agregar(){
		$this->carabiner->css(array(
			array('libs/jquery.msgbox.css', 'screen'),
			array('general/forms.css', 'screen'),
			array('general/tables.css', 'screen'),
			array('productos/familias_productos.css', 'screen')
		));
		$this->carabiner->js(array(
			array('libs/jquery.numeric.js'),
			array('libs/jquery.msgbox.min.js'),
			array('general/msgbox.js'),
			array('productos/frm_addmod.js')
		));
	
		$params['seo'] = array(
				'titulo' => 'Agregar producto'
		);
		
		if($this->input->get_post('familia') != false && $this->input->get_post('familia') != ''){
			$this->getConfProductos();
			$this->load->model('productos_model');
			$this->load->library('pagination');
			
			if($this->form_validation->run() == FALSE){
				$params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
			}else{
				$respons = $this->productos_model->addProducto();
		
				if($respons[0]){
					$params['load_productos'] = true;
					$params['frm_errors'] = $this->showMsgs(6);
				}
			}
			
			$params['familia'] = $this->productos_model->getInfoFamilia($this->input->get_post('familia'));
			$params['productosr'] = $this->productos_model->getProductosFamilia('30', false, '', 'nombre ASC'); //productos registrados
			//Listra de productos agregados
			$params['tabla_productos_r'] = $this->load->view('panel/productos/agregar_produc_listado', $params, true);
			
			if(isset($_GET['msg']{0}))
				$params['frm_errors'] = $this->showMsgs($_GET['msg']);
		}else
			$params['frm_errors'] = $this->showMsgs(1);
		
		$this->load->model('unidades_model');
		$params['unidades'] = $this->unidades_model->getUnidades();
		
		$this->load->view('panel/productos/agregar_producto', $params);
	}
	
	/**
	 * Modificar un producto utilizando el superbox
	 */
	public function modificar(){
		$this->carabiner->css(array(
				array('libs/jquery.msgbox.css', 'screen'),
				array('general/forms.css', 'screen'),
				array('general/tables.css', 'screen'),
				array('productos/familias_productos.css', 'screen')
		));
		$this->carabiner->js(array(
				array('libs/jquery.numeric.js'),
				array('libs/jquery.msgbox.min.js'),
				array('general/msgbox.js'),
				array('productos/frm_addmod.js')
		));
	
		$params['seo'] = array(
				'titulo' => 'Modificar producto'
		);
	
		if(isset($_GET['id']{0}) && $this->input->get_post('familia') != false && $this->input->get_post('familia') != ''){
			$this->getConfProductos();
			$this->load->model('productos_model');
			$this->load->library('pagination');
				
			if($this->form_validation->run() == FALSE){
				$params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
			}else{
				$respons = $this->productos_model->updateProducto();
	
				if($respons[0]){
					$params['load_productos'] = true;
					$params['frm_errors'] = $this->showMsgs(8);
				}
			}

			$params['producto'] = $this->productos_model->getInfoProducto($_GET['id']);
			$params['familia'] = $this->productos_model->getInfoFamilia($this->input->get_post('familia'));
			$params['productosr'] = $this->productos_model->getProductosFamilia('30', false, 
					" AND id_producto != '".$_GET['id']."'", 'nombre ASC'); //productos registrados
			//Listra de productos registrados
			$params['tabla_productos_r'] = $this->load->view('panel/productos/agregar_produc_listado', $params, true);
				
			if(isset($_GET['msg']{0}))
				$params['frm_errors'] = $this->showMsgs($_GET['msg']);
		}else
			$params['frm_errors'] = $this->showMsgs(1);
	
		$this->load->model('unidades_model');
		$params['unidades'] = $this->unidades_model->getUnidades();
	
		$this->load->view('panel/productos/modificar_producto', $params);
	}
	
	/**
	 * Desactivar (eliminar) un producto
	 */
	public function desactivar(){
		if(isset($_GET['id']{0})){
			$this->load->model('productos_model');
			$this->productos_model->desactivarProducto();
			$params['msg'] = $this->showMsgs(7);
		}else
			$params['msg'] = $this->showMsgs(1);
	
		echo json_encode($params);
	}
	
	
	/**
	 * Obtiene el listado de productos de una familia utilizando Ajax
	 */
	public function ajax_productos_familia(){
		if(isset($_GET['id']{0})){ //id familia
			$this->load->model('productos_model');
			$this->load->library('pagination');
				
			$params['title_familia'] = $this->input->get('title_familia');
			$params['productos'] = $this->productos_model->getProductosFamilia();
			$params['tabla_produtos'] = $this->load->view('panel/productos/listado_productos_tbl', $params, true);
				
			$this->load->view('panel/productos/listado_productos', $params);
		}
	}
	
	/**
	 * Obtiene el listado de productos de una familia utilizando Ajax, solo la tabla
	 * la uso para buscar productos
	 */
	public function ajax_productos_tbl(){
		if(isset($_GET['id']{0})){ //id familia
			$this->load->model('productos_model');
			$this->load->library('pagination');
			
			$params['productos'] = $this->productos_model->getProductosFamilia();
			$params['familia'] = $this->productos_model->getInfoFamilia($this->input->get('id'));
			$params['title_familia'] = $params['familia'][0]->nombre;
	
			$this->load->view('panel/productos/listado_productos_tbl', $params);
		}
	}
	
	/**
	 * Obtiene el listado de productos registrados utilizando Ajax, solo la tabla
	 * la uso para buscar productos en Agregar y Modificar productos
	 */
	public function ajax_productos_addmod(){
		$this->load->model('productos_model');
		$this->load->library('pagination');
		
		//en modificar quito el producto q se esta modificando
		$sql = isset($_GET['id_producto'])? " AND id_producto != '".$_GET['id_producto']."'": '';
		$params['productosr'] = $this->productos_model->getProductosFamilia('30', false, $sql, 'nombre ASC'); //productos registrados

		$this->load->view('panel/productos/agregar_produc_listado', $params);
	}
	
	
	/**
	 * Obtiene el listado de productos para el autocomplete usando ajax,
	 * busca por codigo o por nombre de producto
	 */
	public function ajax_get_productos(){
		$this->load->model('productos_model');
		$params = $this->productos_model->getProductosAjax();
	
		echo json_encode($params);
	}
	
	
	
	
	/**
	 * Muestra mensajes cuando se realiza alguna accion
	 * @param unknown_type $tipo
	 * @param unknown_type $msg
	 * @param unknown_type $title
	 */
	private function showMsgs($tipo, $msg='', $title='Productos!'){
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
				$txt = 'La familia se modifico correctamente.';
				$icono = 'ok';
				break;
			case 4:
				$txt = 'La familia se agrego correctamente.';
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
	
	/**
	 * Valida que el codigo de la familia no exista
	 * @param unknown_type $str
	 */
	public function val_codigo($str){
		if($str != ''){
			$sql = '';
			if(isset($_GET['id']))
				$sql = " AND id_familia != '".$_GET['id']."'";
			
			//reajusta a 2 digitos
			//$str = (strlen($str)==1? '0': '').$str;
			
			$res = $this->db->select('Count(id_familia) AS num')
				->from('productos_familias')
				->where("status = 'ac' AND codigo = '".$str."'".$sql)
			->get();
			$data = $res->row();
			if($data->num > 0){
				$this->form_validation->set_message('val_codigo', 'El código ya está siendo utilizado por otra familia');
				return false;
			}
			return true;
		}
		return false;
	}
	
	/**
	 * Valida que el codigo del producto no exista
	 * @param unknown_type $str
	 */
	public function val_codigo_producto($str){
		if($str != ''){
			$sql = '';
			if(isset($_GET['id']))
				$sql = " AND id_producto != '".$_GET['id']."'";
				
			/*//reajusta a 4 digitos
			for($i=strlen($str); $i<4; ++$i)
				$str = '0'.$str;*/
			$str = $this->input->post('codigo_familia').'-'.$str;
			
			$res = $this->db->select('Count(id_producto) AS num')
				->from('productos')
				->where("status = 'ac' AND codigo = '".$str."'".$sql)
			->get();
			$data = $res->row();
			if($data->num > 0){
				$this->form_validation->set_message('val_codigo_producto', 'El código ya está siendo utilizado por otro producto');
				return false;
			}
			return true;
		}
		return false;
	}
	
	/**
	 * Configura la validacion de formulario para agregar o modificar productos
	 */
	private function getConfProductos(){
		$this->load->library('form_validation');
		$rules = array(
			array('field'	=> 'familia',
					'label'	=> 'ID',
					'rules'	=> 'required'),
			array('field'	=> 'codigo_familia',
					'label'	=> 'Código familia',
					'rules'	=> 'required'),
				
			array('field'	=> 'dcodigo',
					'label'	=> 'Código',
					'rules'	=> 'required|max_length[8]|callback_val_codigo_producto'),
			array('field'	=> 'dnombre',
					'label'	=> 'Nombre',
					'rules'	=> 'required|max_length[70]'),
			array('field'	=> 'dunidad',
					'label'	=> 'Unidad',
					'rules'	=> 'required|max_length[25]'),
			
			array('field'	=> 'ddescripcion',
					'label'	=> 'Descripción',
					'rules'	=> 'max_length[300]'),
			array('field'	=> 'dubicacion',
					'label'	=> 'Ubicación',
					'rules'	=> 'max_length[70]'),
			array('field'	=> 'dstock_min',
					'label'	=> 'Stock min',
					'rules'	=> 'numeric'),
			array('field'	=> 'dstock_max',
					'label'	=> 'Stock max',
					'rules'	=> 'numeric'),
				
			array('field'	=> 'dpcids[]',
					'label'	=> 'ID Producto',
					'rules'	=> 'min_length[1]|max_length[25]'),
			array('field'	=> 'dpccantidad[]',
					'label'	=> 'Cantidad consumo',
					'rules'	=> 'numeric')
		);
		$this->form_validation->set_rules($rules);
	}
	
}

?>