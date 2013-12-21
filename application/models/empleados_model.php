<?php
session_start();

class empleados_model extends privilegios_model{
	
	function __construct(){
		parent::__construct();
	}
	
	/**
	 * Obtiene el listado de empleados
	 */
	public function getEmpleados($paginados = true){
		$sql = '';
		//paginacion
		if($paginados){
			$params = array(
					'result_items_per_page' => '30',
					'result_page' => (isset($_GET['pag'])? $_GET['pag']: 0)
			);
			if($params['result_page'] % $params['result_items_per_page'] == 0)
				$params['result_page'] = ($params['result_page']/$params['result_items_per_page']);
		}
		//Filtros para buscar
		if($this->input->get('fnombre') != '')
			$sql = "WHERE lower((apellido_paterno || ' ' || apellido_materno || ' ' || nombre)) 
				LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%'";
		if($this->input->get('fstatus') != 'todos'){
			$_GET['fstatus'] = $this->input->get('fstatus')==''? 'contratado': $this->input->get('fstatus');
			$sql .= ($sql==''? 'WHERE': ' AND')." lower(status) LIKE '".mb_strtolower($this->input->get('fstatus'), 'UTF-8')."'";
		}
		
		$query = BDUtil::pagination("
				SELECT id_empleado, (apellido_paterno || ' ' || apellido_materno || ' ' || nombre) AS e_nombre, telefono, tipo_usuario, status
				FROM empleados
				".$sql."
				ORDER BY e_nombre ASC
				", $params, true);
		$res = $this->db->query($query['query']);
		
		$response = array(
				'empleados' => array(),
				'total_rows' 		=> $query['total_rows'],
				'items_per_page' 	=> $params['result_items_per_page'],
				'result_page' 		=> $params['result_page']
		);
		if($res->num_rows() > 0)
			$response['empleados'] = $res->result();
		
		return $response;
	}
	
	/**
	 * Obtiene la informacion de un empleado
	 */
	public function getInfoEmpleado($id, $info_basic=false){
		$res = $this->db
			->select('*')
			->from('empleados')
			->where("id_empleado = '".$id."'")
		->get();
		if($res->num_rows() > 0){
			$response['info'] = $res->row();
			$res->free_result();
			if($info_basic)
				return $response;
			
			//contactos
			$res = $this->db
				->select('*')
				->from('empleados_contacto')
				->where("id_empleado = '".$id."'")
			->get();
			if($res->num_rows() > 0){
				$response['contactos'] = $res->result();
			}
			$res->free_result();
			//Privilegios
			$res = $this->db
				->select('id_privilegio')
				->from('empleados_privilegios')
				->where("id_empleado = '".$id."'")
			->get();
			if($res->num_rows() > 0){
				foreach($res->result() as $priv)
					$response['privilegios'][] = $priv->id_privilegio;
			}
			$res->free_result();
			
			return $response;
		}else
			return false;
	}
	
	/**
	 * Agrega un empleado a la bd
	 */
	public function addEmpleado(){
		$path_img = '';
		//valida la imagen
		$upload_res = UploadFiles::uploadImgEmpleado();
		if(is_array($upload_res)){
			if($upload_res[0] == false)
				return array(false, $upload_res[1]);
			$path_img = APPPATH.'images/empleados/'.$upload_res[1]['file_name'];
		}
		
		$id_empleado = BDUtil::getId();
		$data = array(
			'id_empleado' => $id_empleado,
			'nombre' => $this->input->post('dnombre'),
			'apellido_paterno' => $this->input->post('dapellido_paterno'),
			'apellido_materno' => $this->input->post('dapellido_materno'),
			'url_img' => $path_img,
			'usuario' => $this->input->post('dusuario'),
			'password' => $this->input->post('dpassword'),
			'calle' => $this->input->post('dcalle'),
			'numero' => $this->input->post('dnumero'),
			'colonia' => $this->input->post('dcolonia'),
			'municipio' => $this->input->post('dmunicipio'),
			'estado' => $this->input->post('destado'),
			'cp' => $this->input->post('dcp'),
			'telefono' => $this->input->post('dtelefono'),
			'celular' => $this->input->post('dcelular'),
			'email' => $this->input->post('demail'),
			'fecha_nacimiento' => ($this->input->post('dfecha_nacimiento')!=''? $this->input->post('dfecha_nacimiento'): NULL),
			'fecha_entrada' => ($this->input->post('dfecha_entrada')==''? date("Y-m-d"): $this->input->post('dfecha_entrada')),
			'fecha_salida' => ($this->input->post('dfecha_salida')!=''? $this->input->post('dfecha_salida'): NULL),
			// 'folio_inicio' => $this->input->post('dfolio_inicio'),
			// 'folio_fin' => $this->input->post('dfolio_fin'),
			'salario' => $this->input->post('dsalario'),
			'hora_entrada' => $this->input->post('dhora_entrada'),
			'tipo_usuario' => $this->input->post('dtipo_usuario'),
			'status' => $this->input->post('dstatus')
		);
		$this->db->insert('empleados', $data);
		
		//Privilegios
		if(isset($_POST['dprivilegios'])){
			$data_priv = array();
			foreach($_POST['dprivilegios'] as $priv){
				$data_priv[] = array(
					'id_empleado' => $id_empleado,
					'id_privilegio' => $priv
				);
			}
			
			if(count($data_priv) > 0)
				$this->db->insert_batch('empleados_privilegios', $data_priv);
		}
		//Contacto
		if(isset($_POST['dcnombre']{0})){
			$this->addContacto($id_empleado);
		}
		
		return array(true, '');
	}
	
	/**
	 * Modifica la info de un empleado a la bd
	 */
	public function updateEmpleado(){
		$empleado = $this->getInfoEmpleado($_GET['id'], true);
		if(is_array($empleado)){
			$path_img = '';
			//valida la imagen
			$upload_res = UploadFiles::uploadImgEmpleado();
			if(is_array($upload_res)){
				if($upload_res[0] == false)
					return array(false, $upload_res[1]);
				$path_img = APPPATH.'images/empleados/'.$upload_res[1]['file_name'];
				UploadFiles::deleteFile($empleado['info']->url_img);
			}else
				$path_img = $empleado['info']->url_img;
			
			$data = array(
				'nombre' => $this->input->post('dnombre'),
				'apellido_paterno' => $this->input->post('dapellido_paterno'),
				'apellido_materno' => $this->input->post('dapellido_materno'),
				'url_img' => $path_img,
				'calle' => $this->input->post('dcalle'),
				'numero' => $this->input->post('dnumero'),
				'colonia' => $this->input->post('dcolonia'),
				'municipio' => $this->input->post('dmunicipio'),
				'estado' => $this->input->post('destado'),
				'cp' => $this->input->post('dcp'),
				'telefono' => $this->input->post('dtelefono'),
				'celular' => $this->input->post('dcelular'),
				'email' => $this->input->post('demail'),
				'fecha_nacimiento' => ($this->input->post('dfecha_nacimiento')!=''? $this->input->post('dfecha_nacimiento'): NULL),
				'fecha_entrada' => ($this->input->post('dfecha_entrada')==''? date("Y-m-d"): $this->input->post('dfecha_entrada')),
				'fecha_salida' => ($this->input->post('dfecha_salida')!=''? $this->input->post('dfecha_salida'): NULL),
				// 'folio_inicio' => $this->input->post('dfolio_inicio'),
				// 'folio_fin' => $this->input->post('dfolio_fin'),
				'salario' => $this->input->post('dsalario'),
				'hora_entrada' => $this->input->post('dhora_entrada'),
				'tipo_usuario' => $this->input->post('dtipo_usuario'),
				'status' => $this->input->post('dstatus')
			);
			if($this->input->post('dusuario') != '')
				$data['usuario'] = $this->input->post('dusuario');
			if($this->input->post('dpassword') != '')
				$data['password'] = $this->input->post('dpassword');
			
			$this->db->update('empleados', $data, "id_empleado = '".$_GET['id']."'");
		
			//Privilegios
			if(isset($_POST['dprivilegios']) && isset($_POST['dmod_privilegios']{0})){
				$this->db->delete('empleados_privilegios', "id_empleado = '".$_GET['id']."'");
				$data_priv = array();
				foreach($_POST['dprivilegios'] as $priv){
					$data_priv[] = array(
						'id_empleado' => $_GET['id'],
						'id_privilegio' => $priv
					);
				}
					
				if(count($data_priv) > 0)
					$this->db->insert_batch('empleados_privilegios', $data_priv);
			}
		}
	
		return array(true, '');
	}
	
	/**
	 * Descontrata a un empleado, cambia su status a no_contratado
	 */
	public function descontratarEmpleado(){
		$this->db->update('empleados', array('status' => 'no_contratado'), "id_empleado = '".$_GET['id']."'");
		return array(true, '');
	}
	
	/**
	 * Agrega contactos al empleado
	 * @param unknown_type $id_empleado
	 */
	public function addContacto($id_empleado=null){
		$id_empleado = $id_empleado==null? $this->input->post('id'): $id_empleado;
		
		$id_conta = BDUtil::getId();
		$data = array(
			'id_contacto' => $id_conta,
			'id_empleado' => $id_empleado,
			'nombre' => $this->input->post('dcnombre'),
			'domicilio' => $this->input->post('dcdomicilio'),
			'municipio' => $this->input->post('dcmunicipio'),
			'estado' => $this->input->post('dcestado'),
			'telefono' => $this->input->post('dctelefono'),
			'celular' => $this->input->post('dccelular')
		);
		$this->db->insert('empleados_contacto', $data);
		return array(true, 'Se agregó el contacto correctamente.', $id_conta);
	}
	
	public function deleteContacto($id_contacto){
		$this->db->delete('empleados_contacto', "id_contacto = '".$id_contacto."'");
		return array(true, '');
	}
	
	
	/**
	 * verifica el login y crea la sesion del usuario
	 */
	public function login(){
		if($_POST['usuario']!='' && $_POST['pass']!=''){
			$res = $this->db
				->select('id_empleado, nombre')
				->from('empleados')
				->where("usuario = '".$_POST['usuario']."' AND password = '".$_POST['pass']."'")->where_in('status', array('contratado', 'usuario'))
			->get();
			if($res->num_rows() > 0){
				$data = $res->row();
				$_SESSION['id_empleado'] = $data->id_empleado;
				$_SESSION['nombre'] = $data->nombre;
				return array(true, '');
			}
		}
		return array(false, 'El nombre de usuario o la contraseña introducidos no son correctos.');
	}
	
	/**
	 * Revisa si la sesion del usuario esta activa
	 */
	public function checkSession(){
		if(isset($_SESSION['id_empleado']) && isset($_SESSION['nombre'])){
			if($_SESSION['id_empleado']!='' && $_SESSION['nombre']!=''){
				return true;
			}
		}
		return false;
	}
	
	public function ajax_get_trabajadores(){
		$sql = '';
		$res = $this->db->query("
				(SELECT id_empleado as id, (nombre || ' ' || apellido_paterno) as nombre, 'tr' as tipo_trabajador
				FROM empleados
				WHERE status='contratado' AND tipo_usuario='empleado' AND lower(nombre) LIKE '%".mb_strtolower($this->input->get('term'), 'UTF-8')."%')
				UNION
				(SELECT id_proveedor as id, nombre, 'pi' as tipo_trabajador
				FROM proveedores
				WHERE status='ac' AND tipo='pi' AND lower(nombre) LIKE '%".mb_strtolower($this->input->get('term'), 'UTF-8')."%')
				ORDER BY nombre ASC
				LIMIT 20
				");
	
		$response = array();
		if($res->num_rows() > 0){
			foreach($res->result() as $itm){
				$response[] = array(
						'id' => $itm->id,
						'label' => $itm->nombre,
						'value' => $itm->nombre,
						'item' => $itm,
				);
			}
		}
	
		return $response;
	}

	/**
	 * Genera el reporte de las asistencias de empleados
	 * @param unknown_type $data
	 */
	public function pdf_rda($data){

		$semanas = String::obtenerSemanasDelAnio($_GET['fanio'],true);
		foreach ($semanas as $s) {
			if ($s['semana'] == $_GET['fsemana']) {
				$finicio_semana = $s['fecha_inicio'];
				$ffin_semana = $s['fecha_final'];
				break;
			}
		}
		$labelFechas = "Semana {$_GET['fsemana']} del $finicio_semana al $ffin_semana";
	
		$this->load->library('mypdf');
		// Creación del objeto de la clase heredada
		$pdf = new MYpdf('P', 'mm', 'Letter');
		$pdf->show_head = true;
		$pdf->titulo2 = 'Reporte de Asistencias';

		$pdf->titulo3 =  "Año {$_GET['fanio']} \n". $labelFechas;
		$pdf->AliasNbPages();
		$pdf->AddPage();
			
		// $links = array('', '', '', '', '', '', '');
		$aligns = array('C', 'C', 'C', 'C', 'C');
		$widths = array(105, 30, 20, 20, 30);
		$header = array('Empleado', 'Dias Asistidos', 'Retardos', 'Faltas', 'Dias Trabajados');

		foreach($data['empleados'] as $key => $item){
				if($pdf->GetY() >= $pdf->limiteY || $key==0){ //salta de pagina si exede el max
					if($key > 0)
						$pdf->AddPage();
						
					$pdf->SetFont('Arial','B',9);
					$pdf->SetTextColor(255,255,255);
					$pdf->SetFillColor(140,140,140);
					$pdf->SetX(6);
					$pdf->SetAligns($aligns);
					$pdf->SetWidths($widths);
					$pdf->Row($header, true);
				}
					
				$pdf->SetFont('Arial','',8);
				$pdf->SetTextColor(0,0,0);

				$datos = array($item->apellido_paterno.' '.$item->apellido_materno.' '.$item->nombre, $item->dias_asistidos, $item->retardos, $item->dias_faltados, 
											$item->dias_trabajados);
					
				$pdf->SetX(6);
				$pdf->SetAligns($aligns);
				$pdf->SetWidths($widths);
				$pdf->Row($datos, false);
		}

		// if ( count($data['empleados']) == 0 ) {
		// 	$y = $pdf->GetY();
		// 	$pdf->SetFont('Arial','B',9);

		// 	if($pdf->GetY()+6 >= $pdf->limiteY){
		// 		$pdf->AddPage();
		// 		$y = $pdf->GetY();
		// 	}
		// 	$pdf->SetTextColor(0,0,0);
		// 	$pdf->SetFillColor(255,255,255);
		// 	$pdf->SetXY(106, $y+5);
		// 	$pdf->Cell(30, 6, 'De los Cuales: ', 0, 0, 'C', 1);

		// 	$y = $y + 5;
		// 	$pdf->SetFont('Arial','',8);
		// 	foreach ($data['tipos'] as $key => $tipo) {
		// 		if($pdf->GetY() >= $pdf->limiteY || $key==0){ //salta de pagina si exede el max
		// 			if($key > 0)
		// 				$pdf->AddPage();
		// 		}
		// 		$y += 6;
		// 		$pdf->SetTextColor(255,255,255);
		// 		$pdf->SetFillColor(140,140,140);
		// 		$pdf->SetXY(106, $y);
		// 		$pdf->Cell(30, 6, $tipo->nombre , 1, 0, 'C',1);
		// 		$pdf->SetXY(151, $y);
		// 		$pdf->Cell(30, 6, 'Sub-Total' , 1, 0, 'C',1);

		// 		$pdf->SetTextColor(0,0,0);
		// 		$pdf->SetFillColor(255,255,255);	
		// 		$pdf->SetXY(136, $y);
		// 		$pdf->Cell(15, 6, $tipo->tvuelos , 1, 0, 'C',1);
		// 		$pdf->SetXY(181, $y);
		// 		$pdf->Cell(30, 6, String::formatoNumero($tipo->ttotal), 1, 0, 'C',1);
		// 	}

		// 	$pdf->SetFont('Arial','B',9);

		// 	$pdf->SetTextColor(255,255,255);
		// 	$pdf->SetFillColor(140,140,140);
		// 	$pdf->SetXY(106, ($y+5));
		// 	$pdf->Cell(30, 6, 'Total Vuelos' , 1, 0, 'C',1);
		// 	$pdf->SetXY(151, ($y+5));
		// 	$pdf->Cell(30, 6, 'Total' , 1, 0, 'C',1);

		// 	$pdf->SetTextColor(0,0,0);
		// 	$pdf->SetFillColor(255,255,255);	
		// 	$pdf->SetXY(136, ($y+5));
		// 	$pdf->Cell(15, 6, $tvuelos , 1, 0, 'C',1);
		// 	$pdf->SetXY(181, ($y+5));
		// 	$pdf->Cell(30, 6, String::formatoNumero($ttotal), 1, 0, 'C',1);
		// }			

		$pdf->Output('reporte_asistencias.pdf', 'I');
	}
}