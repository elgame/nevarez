<?php
class privilegios_model extends CI_Model{
	/**
	 * los url_accion q se asignen seran excluidos de la validacion y la funcion
	 * tienePrivilegioDe regresara un true como si el usuario si tiene ese privilegio,
	 * Esta enfocado para cuendo se utilice Ajax
	 * @var unknown_type
	 */
	public $excepcion_privilegio = array();
	
	
	function __construct(){
		parent::__construct();
	}
	
	/**
	 * Obtiene el listado de todos los privilegios paginados
	 */
	public function obtenPrivilegios(){
		$sql = '';
		//paginacion
		$params = array(
				'result_items_per_page' => '30',
				'result_page' => (isset($_GET['pag'])? $_GET['pag']: 0)
		);
		if($params['result_page'] % $params['result_items_per_page'] == 0)
			$params['result_page'] = ($params['result_page']/$params['result_items_per_page']);
		
		//Filtros para buscar
		if($this->input->get('fnombre') != '')
			$sql = "WHERE lower(nombre) LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%'";
		if($this->input->get('furl_accion') != '')
			$sql .= ($sql==''? 'WHERE': ' AND')." lower(url_accion) LIKE '%".mb_strtolower($this->input->get('furl_accion'), 'UTF-8')."%'";
		
		$query = BDUtil::pagination("
			SELECT id_privilegio, id_padre, nombre, mostrar_menu, url_accion
			FROM privilegios
			".$sql."
			ORDER BY url_accion ASC
		", $params, true);
		$res = $this->db->query($query['query']);
		
		$response = array(
				'privilegios' => array(),
				'total_rows' 		=> $query['total_rows'],
				'items_per_page' 	=> $params['result_items_per_page'],
				'result_page' 		=> $params['result_page']
		);
		if($res->num_rows() > 0)
			$response['privilegios'] = $res->result();
		
		return $response;
	}
	
	/**
	 * Obtiene toda la informacion de un privilegio
	 * @param unknown_type $id
	 */
	public function getInfoPrivilegio($id){
		$res = $this->db
			->select('*')
			->from('privilegios')
			->where("id_privilegio = '".$id."'")
		->get();
		if($res->num_rows() > 0)
			return $res->row();
		else
			return false;
	}
	
	/**
	 * Modifica la informacion de un privilegio
	 */
	public function updatePrivilegio(){
		$data = array(
			'nombre' => $this->input->post('dnombre'),
			'id_padre' => ($this->input->post('dprivilegios')!=''? $this->input->post('dprivilegios'): '0'),
			'mostrar_menu' => ($this->input->post('dmostrar_menu')=='si'? 't': 'f'),
			'url_accion' => $this->input->post('durl_accion'),
			'url_icono' => $this->input->post('durl_icono'),
			'target_blank' => ($this->input->post('dtarget_blank')=='si'? 't': 'f')
		);
		$this->db->update('privilegios', $data, "id_privilegio = '".$_GET['id']."'");
		return array(true, '');
	}
	
	/**
	 * Agrega un privilegio a la bd
	 */
	public function addPrivilegio(){
		$data = array(
			'id_privilegio' => BDUtil::getId(),
			'nombre' => $this->input->post('dnombre'),
			'id_padre' => ($this->input->post('dprivilegios')!=''? $this->input->post('dprivilegios'): '0'),
			'mostrar_menu' => ($this->input->post('dmostrar_menu')=='si'? 't': 'f'),
			'url_accion' => $this->input->post('durl_accion'),
			'url_icono' => $this->input->post('durl_icono'),
			'target_blank' => ($this->input->post('dtarget_blank')=='si'? 't': 'f')
		);
		$this->db->insert('privilegios', $data);
		return array(true, '');
	}
	
	/**
	 * Elimina un privilegio de la bd
	 */
	public function deletePrivilegio(){
		$this->db->delete('privilegios', "id_privilegio = '".$_GET['id']."'");
		return array(true, '');
	}
	
	
	
	
	/**
	* Verifica si el usuairo tiene ese privilegio, si lo tiene genera un link para accederlo
	* @param unknown_type $url_accion
	* @param unknown_type $id_obj
	* @param unknown_type $js
	* @param unknown_type $attrs
	* @param unknown_type $params
	*/
	public function getLinkPrivSm($url_accion, $id_obj, $js='', $attrs='', $params=''){
		$txt = '';
		$priv = $this->tienePrivilegioDe('', $url_accion, true);
		if(is_object($priv)){
			if(is_array($id_obj)){
				list($key) = array_keys($id_obj);
				$id_obj = "?$key={$id_obj[$key]}";
			}else $id_obj = '?id='.$id_obj;
			
			$js = $js!=''? ' onclick="'.$js.'"': '';
			$txt = '<a href="'.base_url('panel/'.$priv->url_accion.$id_obj.$params).'" class="linksm"'.$js.$attrs.'>
			<img src="'.base_url('application/images/privilegios/'.$priv->url_icono).'" width="10" height="10"> '.$priv->nombre.'</a> <br>';
		}
		return $txt;
	}
	
	/**
	 * Verifica si el usuario tiene ese privilegio de alerta, si lo tiene genera el html de la alerta con sus datos
	 * @param unknown_type $url_accion
	 */
	public function getAlertPriv($url_accion){
		$txt = '';
		$priv = $this->tienePrivilegioDe('', $url_accion, true);
		if(is_object($priv)){
			list($controler,$metodo) = explode("/",$url_accion);
			$txt = $this->alertas_model->{$metodo}();
		}
		return $txt;
	}
	
	
	/**
	 * Verifica si el usuario tiene un privilegio en espesifico
	 * @param unknown_type $id_privilegio
	 * @param unknown_type $url_accion
	 * @param unknown_type $returninfo
	 */
	public function tienePrivilegioDe($id_privilegio="", $url_accion="", $returninfo=false){
		$band = false;
		$url_accion = str_replace('index/', '', $url_accion);
		
		$excluir = array_search($url_accion, $this->excepcion_privilegio);
		
		$sql = $id_privilegio!=''? "p.id_privilegio = '".$id_privilegio."'": "lower(url_accion) = lower('".$url_accion."')";
		$res = $this->db
			->select('p.id_privilegio, p.nombre, p.url_accion, p.mostrar_menu, p.url_icono')
			->from('privilegios AS p')
				->join('empleados_privilegios AS ep', 'p.id_privilegio = ep.id_privilegio', 'inner')
			->where("ep.id_empleado = '".$_SESSION['id_empleado']."' AND ".$sql."")
			->limit(1)
		->get();
		if($res->num_rows() > 0){
			if($returninfo)
				return $res->row();
			$band = true;
		}
		if($excluir !== false)
			return true;
		return $band;
	}
	
	public function getFrmPrivilegios($id_submenu=0, $firs=true, $tipo=null, $showp=false){
		$txt = "";
		$bande = true;
		
		$res = $this->db
			->select("p.id_privilegio, p.nombre, p.id_padre, p.url_accion, p.url_icono, p.target_blank, 
				(SELECT count(id_privilegio) FROM empleados_privilegios WHERE id_empleado = '".$_SESSION['id_empleado']."' 
					AND id_privilegio = p.id_privilegio) as tiene_p")
			->from('privilegios AS p')
			->where("p.id_padre = '".$id_submenu."'")
			->order_by('p.nombre', 'asc')
		->get();
		$txt .= $firs? '<ul class="treeview">': '<ul>';
		foreach($res->result() as $data){
			$res1 = $this->db
				->select('Count(p.id_privilegio) AS num')
				->from('privilegios AS p')
				->where("p.id_padre = '".$data->id_privilegio."'")
			->get();
			$data1 = $res1->row();
			
			if($tipo != null && !is_array($tipo)){
				$set_nombre = 'dprivilegios';
				$set_val = set_radio($set_nombre, $data->id_privilegio, ($tipo==$data->id_privilegio? true: false));
				$tipo_obj = 'radio';
			}else{	
				$set_nombre = 'dprivilegios[]';
				if(is_array($tipo))
					$set_val = set_checkbox($set_nombre, $data->id_privilegio, 
							(array_search($data->id_privilegio, $tipo)!==false? true: false) );
				else
					$set_val = set_checkbox($set_nombre, $data->id_privilegio);
				$tipo_obj = 'checkbox';
			}
			
			if($bande==true && $firs==true && $showp==true){
				$txt .= '<li><label>
				<input type="'.$tipo_obj.'" name="'.$set_nombre.'" value="0" '.$set_val.'> Padre</label>
				</li>';
				$bande = false;
			}
			
			if($data1->num > 0){
				$txt .= '<li><label>
					<input type="'.$tipo_obj.'" name="'.$set_nombre.'" value="'.$data->id_privilegio.'" '.$set_val.'> '.$data->nombre.'</label>
					'.$this->getFrmPrivilegios($data->id_privilegio, false, $tipo).'
				</li>';
			}else{
				$txt .= '<li><label>
					<input type="'.$tipo_obj.'" name="'.$set_nombre.'" value="'.$data->id_privilegio.'" '.$set_val.'> '.$data->nombre.'</label>
				</li>';
			}
			$res1->free_result();
		}
		$txt .= '</ul>';
		$res->free_result();
		
		return $txt;
	}
	
	/**
	 * Genera el menu izq con los privilegios q el usuario tenga asignados
	 * @param unknown_type $id_submenu
	 * @param unknown_type $firs
	 */
	public function generaMenuPrivilegio($id_submenu=0, $firs=true){
		$txt = "";
		$bande = true;
		
		$res = $this->db
			->select('p.id_privilegio, p.nombre, p.id_padre, p.url_accion, p.url_icono, p.target_blank')
			->from('privilegios AS p')
				->join('empleados_privilegios AS ep','p.id_privilegio = ep.id_privilegio','inner')
			->where("ep.id_empleado = '".$_SESSION['id_empleado']."' AND p.id_padre = '".$id_submenu."' AND mostrar_menu = 't'")
			->order_by('p.nombre', 'asc')
		->get();
		foreach($res->result() as $data){
			$res1 = $this->db
				->select('Count(p.id_privilegio) AS num')
				->from('privilegios AS p')
					->join('empleados_privilegios AS ep','p.id_privilegio = ep.id_privilegio','inner')
				->where("ep.id_empleado = '".$_SESSION['id_empleado']."' AND p.id_padre = '".$data->id_privilegio."' AND mostrar_menu = 't'")
			->get();
			$data1 = $res1->row();
			
			$link_tar = $data->target_blank=='t'? ' target="_blank"': '';
			
			if($firs){
				$txt .= '<h3><a href="#">'.$data->nombre.'</a> 
					<img src="'.base_url('application/images/privilegios/'.$data->url_icono).'" alt="'.$data->nombre.'" width="16" height="16"></h3>
				<div>
				<ul class="treeview">
					<li><a href="'.base_url('panel/'.$data->url_accion).'" title="'.$data->nombre.'" class="opc-title"'.$link_tar.'>'.$data->nombre.'</a></li>';
			}else{
				$txt .= '<ul>';
			}
			
			if($data1->num > 0){
				if($firs){
					//$txt .= str_replace(array('<ul>','</ul>'), '', $this->generaMenuPrivilegio($data->id_privilegio, false));
					$txt .= $this->generaMenuPrivilegio($data->id_privilegio, false);
				}else{
					$txt .= '
					<li><img src="'.base_url('application/images/privilegios/'.$data->url_icono).'" alt="'.$data->nombre.'" width="16" height="16">
					<a href="'.base_url('panel/'.$data->url_accion).'" title="'.$data->nombre.'" class="opc-title"'.$link_tar.'>'.$data->nombre.'</a>
						'.$this->generaMenuPrivilegio($data->id_privilegio, false).'
					</li>';
				}
			}else{
				$txt .= '
				<li><img src="'.base_url('application/images/privilegios/'.$data->url_icono).'" alt="'.$data->nombre.'" width="16" height="16">
				<a href="'.base_url('panel/'.$data->url_accion).'" title="'.$data->nombre.'" class="opc-title"'.$link_tar.'>'.$data->nombre.'</a>
				</li>';
			}
			
			$txt .= '</ul>';
			if($firs){
				$txt .= '
				</div>';
			}
		}
		return $txt;
	}
}