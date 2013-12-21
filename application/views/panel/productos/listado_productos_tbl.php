<table class="tblListados corner-all8">
	<tr class="header btn-gray">
		<td class="a-c" colspan="3"><?php echo $title_familia; ?></td>
	</tr>
	<tr class="header btn-gray">
		<td>Código</td>
		<td>Nombre</td>
		<td class="a-c">Opc</td>
	</tr>
<?php foreach($productos['productos'] as $produc){ ?>
	<tr id="rowprod<?php echo str_replace('.', '-', $produc->id_producto);?>">
		<td><?php echo $produc->codigo; ?></td>
		<td><?php echo $produc->nombre; ?></td>
		<td class="tdsmenu a-c" style="width: 90px;">
			<img alt="opc" src="<?php echo base_url('application/images/privilegios/gear.png'); ?>" width="16" height="16">
			<div class="submenul">
				<p class="corner-bottom8">
					<?php 
					echo $this->empleados_model->getLinkPrivSm('productos/modificar/', $produc->id_producto.'&familia='.$_GET['id'], 
							'', ' rel="superbox[iframe][800x280]"'); 
					echo $this->empleados_model->getLinkPrivSm('productos/desactivar/', $produc->id_producto, 
							"msb.confirm('Estas seguro de Eliminar el Producto?', this, deleteProducto); return false;");
					?>
				</p>
			</div>
		</td>
	</tr>
<?php }?>
</table>	
<?php

//Paginacion
$this->pagination->initialize(array(
		'base_url' 			=> "",
		'javascript'		=> "javascript:buscarProductos({pag});",
		'total_rows'		=> $productos['total_rows'],
		'per_page'			=> $productos['items_per_page'],
		'cur_page'			=> $productos['result_page']*$productos['items_per_page'],
		'page_query_string'	=> TRUE,
		'num_links'			=> 1,
		'anchor_class'		=> 'pags corner-all'
));
$pagination = $this->pagination->create_links();
echo '<div class="pagination w100">'.$pagination.'</div>'; 


?>