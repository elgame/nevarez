<?php
class alertas_model extends privilegios_model{
	
	function __construct(){
		parent::__construct();
	}
	
	// ELIMINA UNA ALERTA
	public function delAlerta(){
		$this->db->delete("alertas",array("id_alerta"=>$this->input->get("id")));
		return array(true);
	}
	
	// OBTIENE LOS PRODUCTOS QUE YA NO CUENTAN CON EXISTENCIAS
	public function productos_bajos(){
		$params['alertas'] = $this->db->query("SELECT Count(id_producto) AS bajos FROM reportes_costo_existencias1 WHERE stock_min > existencia")->result();
		$html_alert='';
		if($params['alertas'][0]->bajos > 0){
			$html_alert = $this->load->view("panel/alertas/alerta_prod_bajos.php",$params,TRUE); 
		}
		return $html_alert;
	}
	
	// OBTIENE TODAS LAS HERRAMIENTA QUE VENCIERON SU FECHA DE ENTREGA
	public function herramientas($wdate=TRUE){
		$date = '';
		if($wdate)
			$date = "AND date(now())>=date(fecha_vencimiento)";
		
		$query = $this->db->select("*, alerta_dias(fecha_vencimiento)|| descripcion as descripcion, DATE(fecha_vencimiento)-DATE(now()) as dias_restantes")->
							 from("alertas")->
							 where("tabla_obj = 'salidas_productos' $date")->
							 order_by("DATE(fecha_vencimiento)","DESC")->
							 get();
		$html_alert = '';
		if($query->num_rows() > 0){
			$params['data']['alertas'] = $query->result();
			$params['total'] = $query->num_rows();
			
			foreach ($params['data']['alertas'] as $key => $h) {
				if ($h->dias_restantes<=0)
					$params['data']['alertas'][$key]->style = 'style="background-color:#FFE1E1;"';
				else
					$params['data']['alertas'][$key]->style = '';
			}
			
			$html_alert = $this->load->view("panel/alertas/alerta_herramientas.php",$params,TRUE); 
		}
		return $html_alert; 
	}
	
	// OBTIENE LOS AVIONES QUE LES QUEDAN 2 MESES PARA VENCER SU TARJETA Y SEGURO
	public function aviones($wdate=TRUE){
		$date = '';
		if($wdate)
			$date = "AND DATE(fecha_vencimiento)-DATE(now())<=60";
		
		$query = $this->db->select("*, alerta_dias(fecha_vencimiento)|| descripcion as descripcion")->
		from("alertas")->
		where("tabla_obj = 'aviones' $date")->
		order_by("DATE(fecha_vencimiento)","DESC")->
		get();
		$html_alert = '';
		if($query->num_rows() > 0){
			$params['data']['alertas'] = $query->result();
			$params['total'] = $query->num_rows();
			$html_alert = $this->load->view("panel/alertas/alerta_aviones.php",$params,TRUE);
		}
		return $html_alert;
	}
	
	// OBTIENE LOS LAS ALERTAS DE LOS PILOTOS YA SEAN DE LICENCIA DE AVION, LICENCIA DE VEHICULO Y SEGURO
	// PARA EL SEGURO SON 15 DIAS ANTES Y PARA LIC AVION|VEHICULO SON 30 DIAS ANTES
	public function pilotos(){
		$query = $this->db->query("
				(SELECT *, alerta_dias(fecha_vencimiento)|| descripcion as descripcion
				FROM alertas
				WHERE tabla_obj='proveedores' AND id_obj2='seguro' AND  DATE(fecha_vencimiento)-DATE(now())<=15)
				UNION
				(SELECT *, alerta_dias(fecha_vencimiento)|| descripcion as descripcion
				FROM alertas
				WHERE tabla_obj='proveedores' AND id_obj2 IN ('lic_avion','lic_vehiculo') AND DATE(fecha_vencimiento)-DATE(now())<=30)
				ORDER BY fecha_vencimiento DESC");
		$html_alert = '';
		if($query->num_rows() > 0){
			$params['data']['alertas'] = $query->result();
			$params['total'] = $query->num_rows();
			$html_alert = $this->load->view("panel/alertas/alerta_pilotos.php",$params,TRUE);
		}
		return $html_alert;
	}
	
	
	// OBTIENE LOS LAS ALERTAS DE LOS CUMPLEAÑOS DE PILOTOS Y EMPLEADOS 3 DIAS ANTES
	public function cumpleaños(){
		$query = $this->db->query("
				SELECT *, alerta_dias(fecha_vencimiento)|| descripcion as descripcion, DATE(fecha_vencimiento)-DATE(now()) as dias_restantes
				FROM alertas
				WHERE tabla_obj IN ('proveedores','empleados') AND id_obj2='fnacimiento' AND DATE(fecha_vencimiento)-DATE(now())>=0 AND DATE(fecha_vencimiento)-DATE(now())<=3
				ORDER BY DATE(fecha_vencimiento) ASC
				");
		$html_alert = '';
		if($query->num_rows() > 0){
			$params['data']['alertas'] = $query->result();
			$params['total'] = $query->num_rows();
			
			foreach ($params['data']['alertas'] as $key => $h) {
				if ($h->dias_restantes<=0)
					$params['data']['alertas'][$key]->style = 'style="background-color:#FFE1E1;"';
				else
					$params['data']['alertas'][$key]->style = '';
			}
			$html_alert = $this->load->view("panel/alertas/alerta_cumpleanos.php",$params,TRUE);
		}
		return $html_alert;
	}

	public function cobranza($value='')
	{
		// DATE(fecha_vencimiento) - DATE(now()) > -30 AND
		$query = $this->db->query("
				SELECT *, alerta_dias(fecha_vencimiento) || descripcion as descripcion,
							 DATE(fecha_vencimiento) - DATE(now()) as dias_restantes
				FROM alertas
				WHERE tabla_obj IN ('facturacion', 'tickets') AND 
							DATE(fecha_vencimiento) - DATE(now()) <= 0
				ORDER BY DATE(fecha_vencimiento) DESC
				");
		$html_alert = '';
		if($query->num_rows() > 0){
			$params['data']['alertas'] = $query->result();
			$params['total'] = $query->num_rows();
			
			foreach ($params['data']['alertas'] as $key => $h) {
				$exitt = $this->cobranzaInfo($h->tabla_obj, $h->id_obj1);
				if( !isset($exitt->status) || $exitt->status == 'ca' || $exitt->status == 'pa') {
					$this->db->delete("alertas", array("id_alerta" => $h->id_alerta));
					unset($params['data']['alertas'][$key]);
				}else{
					if ($h->dias_restantes<=0)
						$params['data']['alertas'][$key]->style = 'style="background-color:#FFE1E1;"';
					else
						$params['data']['alertas'][$key]->style = '';
				}
			}
			$html_alert = $this->load->view("panel/alertas/alerta_cobranza.php",$params,TRUE);
		}
		return $html_alert;
	}
	
	public function cobranzaInfo($tabla_obj, $id_obj1){
		$query = $this->db->query("
				SELECT *
				FROM ".$tabla_obj."
				WHERE ".($tabla_obj=='facturacion'? 'id_factura': 'id_ticket')." = '".$id_obj1."'")->row();
		return $query;
	}

	public function cuentas_pagar($value='')
	{
		// DATE(fecha_vencimiento) - DATE(now()) > -30 AND
		$query = $this->db->query("
				SELECT *, alerta_dias(fecha_vencimiento) || descripcion as descripcion,
							 DATE(fecha_vencimiento) - DATE(now()) as dias_restantes
				FROM alertas
				WHERE tabla_obj IN ('compras') AND 
							DATE(fecha_vencimiento) - DATE(now()) <= 0
				ORDER BY DATE(fecha_vencimiento) DESC
				");
		$html_alert = '';
		if($query->num_rows() > 0){
			$params['data']['alertas'] = $query->result();
			$params['total'] = $query->num_rows();
			$html_alert = $this->load->view("panel/alertas/alerta_cuentas_pagar.php",$params,TRUE);
		}
		return $html_alert;
	}

	
}