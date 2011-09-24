$(document).ready(function(){
	$('#checkAll').click(function(){
		if($(this).attr('checked')){
			$('tr.linha input').attr('checked','checked');
		}else{
			$('tr.linha input').removeAttr('checked');
		}
	});
	$('#executar')
	.click(function(){
		if($('#executarNoBanco').attr('checked')){
			if(!window.confirm( 'Deseja realmente destruir as tabelas selecionadas? \nEsta não tem retorno.')){
				return;
			}
		}
		$.ajax({
			url: "?c=CUtilitario_recriarBase",
			type:'POST',
			data:$('form[name|="formulario"]').serialize(),
			dataType: 'html',
			success: function(data){
				$('#resultado-recriacao')
				.dialog({
					modal:true,
					position:[300,150],
					minHeight: 450,
					minWidth: 600,
					width:640,
					close: function(event, ui) {

					}
				}).find('#comandos').html(data);
			}
		});

	});
});