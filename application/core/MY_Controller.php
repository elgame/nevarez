<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller{
	protected $info_empleado;
	
	function MY_Controller($redirect=true){
		parent::__construct();
		
		date_default_timezone_set('America/Mexico_City');

		$this->limpiaParams();
		
		$this->load->helper(array('form', 'url'));
		$this->load->library('carabiner');
		$this->carabiner->config(
			array(
			    'base_uri'   => base_url(),
			    'combine'    => false,
			    'dev'        => true
		));
	}
	
	
	private function limpiaParams(){
		foreach ($_POST as $key => $value)
    		$_POST[$key] = String::limpiarTexto(($value));
		
		foreach ($_GET as $key => $value)
			$_GET[$key] = String::limpiarTexto(($value));
	}
}
?>