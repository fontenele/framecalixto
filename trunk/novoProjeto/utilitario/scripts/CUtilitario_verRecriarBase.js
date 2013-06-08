$(document).ready(function(){
	$('#checkAll').click(function(){
		if($(this).attr('checked')){
			$('tbody input[type="checkbox"]').attr('checked','checked');
		}else{
			$('tbody input[type="checkbox"]').removeAttr('checked');
		}
	});
	$('#executar')
	.click(function(){
		if($('#executarNoBanco').attr('checked')){
			if(!window.confirm( 'Deseja realmente destruir as tabelas selecionadas? \nEsta operação não tem retorno.')){
				return;
			}
		}
		$('#comandos').html('Executando...');
		$.ajax({
			url: "?c=CUtilitario_recriarBase",
			type:'post',
			data:$('form[name|="formulario"]').serialize(),
			dataType: 'html',
			success: function(data){
				$('#comandos').html(data);
			}
		});

	});
});