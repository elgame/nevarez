<?php
class aviones_model extends privilegios_model{
	
	function __construct(){
		parent::__construct();
	}
	
	public function getAviones($id_avion=false, $order = 'matricula ASC'){
		
		//paginacion
		$params = array(
				'result_items_per_page' => '30',
				'result_page' => (isset($_GET['pag'])? $_GET['pag']: 0)
		);
		if($params['result_page'] % $params['result_items_per_page'] == 0)
			$params['result_page'] = ($params['result_page']/$params['result_items_per_page']);
		
		$order = (!empty($order)) ? $order : 'matricula ASC';
		
		($id_avion) ? $this->db->where('id_avion',$id_avion) : null;
		$this->db->where('status','ac');
		$this->db->like('lower(matricula)',mb_strtolower($this->input->get('fmatricula'), 'UTF-8'));
		$this->db->order_by($order);
		$this->db->get('aviones');	
		
		$sql	= $this->db->last_query();
		
		$query = BDUtil::pagination($sql, $params, true);
		$res = $this->db->query($query['query']);
		
		$data = array(
				'aviones' 			=> array(),
				'total_rows' 		=> $query['total_rows'],
				'items_per_page' 	=> $params['result_items_per_page'],
				'result_page' 		=> $params['result_page']
		);
		
		if($res->num_rows() > 0)
			$data['aviones'] = $res->result();
		
		return $data;
	}
	
	public function addAvion(){
		if($this->db->select('id_avion')->from('aviones')->where(array('matricula'=>$this->input->post('fmatricula'),'status'=>'ac'))->get()->num_rows()<1)
		{
			$id_avion	= BDUtil::getId();
			$data	= array(
					'id_avion'	=> $id_avion,
					'matricula'	=> $this->input->post('fmatricula'),
					'modelo'	=> $this->input->post('fmodelo'),
					'tipo'		=> $this->input->post('ftipo'),
					'fecha_vence_tarjeta'=> $this->input->post('dfecha_vence_tarjeta'),
					'fecha_vence_seguro'=> $this->input->post('dfecha_vence_seguro')
			);
			$this->db->insert('aviones',$data);
			return array(true);
		}
		return array(false);
	}
	
	public function editAvion($id_avion){
		if($this->exist('aviones',array('id_avion'=>$id_avion,'status'=>'ac')))
		{
			$data	= array(
					'matricula'	=> $this->input->post('fmatricula'),
					'modelo'	=> $this->input->post('fmodelo'),
					'tipo'		=> $this->input->post('ftipo'),
					'fecha_vence_tarjeta'=> $this->input->post('dfecha_vence_tarjeta'),
					'fecha_vence_seguro'=> $this->input->post('dfecha_vence_seguro')
			);
			$this->db->where('id_avion',$id_avion);
			$this->db->update('aviones',$data);
			
			return array(true);
		}
		return array(false);
	}
	
	public function delAvion($id_avion){
		if($this->db->select('id_avion')->from('aviones')->where(array('id_avion'=>$id_avion,'status'=>'ac'))->get()->num_rows()==1){
			$this->db->update('aviones',array('status'=>'e'),array('id_avion'=>$id_avion));
			return array(true);
		}
		return array(false);
	}
	
	public function getAvionesAjax(){
		$sql = '';
		$res = $this->db->query("
				SELECT id_avion, matricula, modelo, tipo
				FROM aviones
				WHERE status = 'ac' AND lower(matricula) LIKE '%".mb_strtolower($this->input->get('term'), 'UTF-8')."%'
				ORDER BY matricula ASC
				LIMIT 20
				");
	
		$response = array();
		if($res->num_rows() > 0){
			foreach($res->result() as $itm){
				$response[] = array(
						'id' => $itm->id_avion,
						'label' => $itm->matricula,
						'value' => $itm->matricula,
						'item' => $itm,
				);
			}
		}
	
		return $response;
	}
	
	
	public function exist($table, $sql, $return_res=false){
		$res = $this->db->get_where($table, $sql);
		if($res->num_rows() > 0){
			if($return_res)
				return $res->row();
			return TRUE;
		}
		return FALSE;
	}
	
	public function data_hva()
	{
		
		$_GET['dfecha1'] = (isset($_GET['dfecha1']))?$_GET['dfecha1']:date('Y-m').'-01';
		$_GET['dfecha2'] = (isset($_GET['dfecha2']))?$_GET['dfecha2']:date('Y-m-d');
		
		$sql = '';
		$inner = '';
		
		if( $this->input->get('dfecha1') != '' )
			$sql = " WHERE DATE(v.fecha)>='".$this->input->get('dfecha1')."'";

		if( $this->input->get('dfecha2') != '' )
			$sql .= (empty($_GET['dfecha1']))?" WHERE DATE(v.fecha)<='".$this->input->get('dfecha2')."'": " AND DATE(v.fecha)<='".$this->input->get('dfecha2')."'";

		$query = $this->db->query("SELECT a.id_avion, a.matricula, COALESCE(SUM(\"time\"(v.hora_llegada) - \"time\"(v.fecha)),'00:00:00') as horas
																FROM aviones a
																INNER JOIN vuelos v ON a.id_avion=v.id_avion
																$sql
																GROUP BY a.id_avion, a.matricula
																ORDER BY a.matricula ASC
												");		

		return array('data'=>$query->result());
	}

	public function pdf_hva($data)
	{
		if($_GET['dfecha1']!='' && $_GET['dfecha2']!='')
			$labelFechas = "Desde la fecha ".$_GET['dfecha1']." hasta ".$_GET['dfecha2'];
		elseif($_GET['dfecha1']!="")
		$labelFechas = "Desde la fecha ".$_GET['dfecha1'];
		elseif($_GET['dfecha2']!='')
		$labelFechas = "Hasta la fecha ".$_GET['dfecha2'];
	
		$this->load->library('mypdf');
		// Creación del objeto de la clase heredada
		$pdf = new MYpdf('P', 'mm', 'Letter');
		$pdf->show_head = true;
		$pdf->titulo2 = 'Reporte Horas de vuelo por avión';

		$pdf->titulo3 =  "\n". $labelFechas;
		$pdf->AliasNbPages();
		$pdf->AddPage();

		$links = array('', '');
		$aligns = array('C', 'C');
		$widths = array(155, 50);
		$header = array('Avión', 'Horas');
	
		$thoras = '00:00:00';
		foreach($data['data'] as $key => $item)
		{
				if($pdf->GetY() >= $pdf->limiteY || $key == 0)
				{ //salta de pagina si exede el max
					if($key > 0)
						$pdf->AddPage();

					$pdf->SetFont('Arial', 'B', 8);
					$pdf->SetTextColor(255, 255, 255);
					$pdf->SetFillColor(160, 160, 160);
					$pdf->SetX(6);
					$pdf->SetAligns($aligns);
					$pdf->SetWidths($widths);
					$pdf->Row($header, true);
				}
				$datarow = array($item->matricula, $item->horas.' hrs');

				$thoras = String::suma_horas_minutos_seg($thoras, $item->horas);
				$pdf->SetFont('Arial', '', 8);
				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFillColor(255, 255, 255);
				$pdf->SetX(6);
				$pdf->SetAligns($aligns);
				$pdf->SetWidths($widths);
				// $pdf->SetMyLinks($links);
				$pdf->Row($datarow, false);
		}

		if ( COUNT($data['data']) > 0 ) {
			$y = $pdf->GetY();
			$pdf->SetFont('Arial','B',10);
			$pdf->SetTextColor(255,255,255);
			$pdf->SetFillColor(140,140,140);
			
			$pdf->SetXY(130, ($y+5));
			$pdf->Cell(31, 6, 'Total Horas' , 1, 0, 'C',1);
			
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetXY(161, ($y+5));
			$pdf->Cell(50, 6, $thoras.' Hrs' , 1, 0, 'C');	
		}

		$pdf->Output('reporte_horas_vuelo_avion.pdf', 'I');
	}

	
	
}