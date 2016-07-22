<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class alertas extends MY_Controller {
	
	/**
	 * Evita la validacion (enfocado cuando se usa ajax). Ver mas en privilegios_model
	 * @var unknown_type
	 */
	private $excepcion_privilegio = array('');
	
	
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
		
	public function eliminar(){
		if(isset($_GET['id']{0})){
			$this->load->model('alertas_model');
			$respons = $this->alertas_model->delAlerta();
			if($respons[0]){
				$url='';
				switch ($_GET['r']) {
					case 'hp':
						$url='panel/salidas/ver_todos/?&msg=9';
						break;
					default:
						$url='panel/home/?&msg=6';
						break;
				}
					redirect(base_url($url));
			}
		}
		else
			redirect(base_url('panel/home/?&msg=1'));
	}	
}