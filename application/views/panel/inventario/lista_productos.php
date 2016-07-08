
<?php 
	$sql = '';
	if($this->input->get('nombre') != '')
		$sql .= " AND Lower(nombre) LIKE '%".mb_strtolower($this->input->get('nombre'), 'utf-8')."%'";
	if($this->input->get('nombre') != '')
		$sql .= " AND Lower(codigo) LIKE '%".mb_strtolower($this->input->get('codigo'), 'utf-8')."%'";
	
	foreach($familias as $itm){
		$res = $this->db->query("SELECT id_producto, codigo, nombre 
				FROM productos 
				WHERE id_familia = '".$itm->id_familia."' 
					AND status = 'ac' AND tipo = 'base'".$sql."
				ORDER BY nombre ASC");
?>
		<option style="font-size:1em; font-weight:bold; background-color:#69C;" value="0"><?php echo $itm->nombre; ?></option>
	<?php 
		if($res->num_rows() > 0){
			foreach($res->result() as $pro){
	?>
		<option style="margin-left:7%;" value="<?php echo $pro->id_producto; ?>"><?php echo $pro->nombre; ?></option>
	<?php 	}
		} ?>
<?php } ?>