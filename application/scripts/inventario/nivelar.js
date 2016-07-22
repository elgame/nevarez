$(function(){
	$(".ef_clck").on('change', function(){
		var id = this.id.replace('ef_', ''), diferiencia=0,
		ef = parseFloat($(this).val());
		
		if(isNaN(ef))
			diferiencia = '';
		else
			diferiencia = ef - parseFloat($("#es_"+id).val());
		$("#diferie_"+id).val(diferiencia);
	});
});


