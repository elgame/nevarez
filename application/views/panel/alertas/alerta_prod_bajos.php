<div class="w50 f-l" style="padding:5px 0;">
	<div class="f-l">
		<span class="f-l" style="font-size: 1.1em;font-weight: bold;color:#D80000;margin-left:30px;">Productos Bajos</span>
		<span class="f-l" style="font-weight: bold;margin-left:10px;">
			<a href="<?php echo base_url('panel/home/productos_bajos/')?>" style="font-size:.7em;color:#1E70F9;text-decoration: none;" target="_BLANK">Ver Productos</a>
		</span>
	</div>
	<div class="w90 f-l" style="margin-left:30px;">
		<table class="tblListados corner-all8">
			<tr class="header btn-gray">
				<td>Total de Productos</td>
			</tr>
			<tr class="a-c">
				<td style="font-size: 1.2em;font-weight:bold; color:red;"><?php echo $alertas[0]->bajos; ?></td>
			</tr>
		</table>
	</div>
</div>