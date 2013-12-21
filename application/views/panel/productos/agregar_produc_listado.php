<?php if(isset($productosr)){ ?>
	<table id="tbl-pr" class="tblListados corner-bottom8 m0-tb">
<?php foreach($productosr['productos'] as $produc){ ?>
		<tr class="tr-producreg" data-id="<?php echo $produc->id_producto; ?>">
			<td><?php echo $produc->nombre; ?></td>
		</tr>
<?php } ?>
	</table>
<?php
	//Paginacion
	$this->pagination->initialize(array(
			'base_url' 			=> "",
			'javascript'		=> "javascript:buscarProductos({pag});",
			'total_rows'		=> $productosr['total_rows'],
			'per_page'			=> $productosr['items_per_page'],
			'cur_page'			=> $productosr['result_page']*$productosr['items_per_page'],
			'page_query_string'	=> TRUE,
			'num_links'			=> 1,
			'anchor_class'		=> 'pags corner-all'
	));
	$pagination = $this->pagination->create_links();
	echo '<div class="pagination w100">'.$pagination.'</div>';
}
?>