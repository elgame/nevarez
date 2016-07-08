<?php
define("RPATH", str_replace("\\","/",dirname(__FILE__).""));

class GrFingerService
{
	// Constants declation
	public $GR_OK = 0;
	public $GR_MATCH = 1;
	public $GR_DEFAULT_CONTEXT = 0;	
	public $GrFingerX;
	public $CI;

	public $db;


	// Application startup code
	public function initialize()
	{
		// Initialize GrFingerX Library
		$this->GrFingerX = new COM('GrFingerX.GrFingerXCtrl.1') or die ('Could not initialise object.');
		com_load_typelib('{A9995C7C-77BF-4E27-B581-A4B5BBD90E50}');

		$this->CI =& get_instance();
		/*// Open sqlite database
		if ($this->db = sqlite_open(RPATH.'/GrFingerSample1.sqlite', 0666, $sqliteerror))
		{
			$query = sqlite_query($this->db, "SELECT name FROM sqlite_master WHERE type='table' and name='enroll'");
			$rows = sqlite_num_rows($query);
			if ($rows<1){
				sqlite_query($this->db, "CREATE TABLE enroll (id INTEGER PRIMARY KEY, tpt TEXT NOT NULL)");
			}
		}
		else 
			return false;*/
		if($this->GrFingerX->Initialize() != $this->GR_OK)
			return false;
		return true;
	}
	
	// Application finalization code
	public function finalize()
	{
		$this->GrFingerX->Finalize();
	}
	
	//Add a fingerprint to database
	public function enroll($tpt, $id, $huella)
	{
		$huella = (is_numeric($huella) && $huella>=1 && $huella<=3)? $huella: 1;

		$data = $this->CI->db->query("SELECT Count(*) AS num FROM empleados_huella WHERE id_empleado = '".$id."'")->row();

		if ($data->num > 0) {
			$this->CI->db->update('empleados_huella', array('huella'.$huella => $tpt), "id_empleado = '".$id."'");
		}else{
			$this->CI->db->insert('empleados_huella', array('id_empleado' => $id, 'huella'.$huella => $tpt));
		}
		return $data->num;
	}
	
	// Verify if two fingerprints match
	public function verify ($id,$rcvtpt)
	{		
		// Find and encode the database template to base 64
		$query = sqlite_query($this->db, "SELECT * FROM enroll WHERE id=".$id);
		$row = sqlite_fetch_array($query, SQLITE_ASSOC);		
		$score = 0;
		// Comparing the given template and the encoded one
		$ret = $this->GrFingerX->VerifyBase64($rcvtpt,$row["tpt"],$score,$this->GR_DEFAULT_CONTEXT);
		if($ret == $this->GR_MATCH)
			return $row["id"];
		else
			return $ret;
	}

	// Identify a fingerprint
	public function identify ($rcvtpt)
	{
		// Starting identification process
		$ret = $this->GrFingerX->IdentifyPrepareBase64($rcvtpt, $this->GR_DEFAULT_CONTEXT);
		if($ret!=$this->GR_OK)
			return $ret;
		// Getting enrolled templates from database
		$query = $this->CI->db->query(
			"SELECT e.id_empleado, e.nombre, e.apellido_paterno, e.hora_entrada, e.url_img, eh.huella1, eh.huella2, eh.huella3 
			FROM empleados AS e 
				INNER JOIN empleados_huella AS eh ON e.id_empleado = eh.id_empleado
			WHERE e.status = 'contratado'");
		$score = 0;
		foreach($query->result_array() as $key => $row){
			for ($i=1; $i <= 3; $i++){
				// Comparing the current template and the given one
				$ret = $this->GrFingerX->IdentifyBase64($row["huella".$i].'', $score, $this->GR_DEFAULT_CONTEXT);
				if( $ret == $this->GR_MATCH)
					return $row;				
			}
		}
		return 0;
		/*$query = sqlite_query($this->db, "SELECT * FROM enroll");		
		$score = 0;
		while ($row = sqlite_fetch_array($query, SQLITE_ASSOC))
		{			
			// Comparing the current template and the given one
			$ret = $this->GrFingerX->IdentifyBase64($row["tpt"],$score,$this->GR_DEFAULT_CONTEXT);
			if( $ret == $this->GR_MATCH)
				return $row["id"];				
		}
		return 0;*/
	}
}
?>