$(document).ready(function(){
	$('#checkAll').click(function(){
		if($(this).attr('checked')){
			$('input[type=checkbox]:not(:first)').attr('checked','checked');
		}else{
			$('input[type=checkbox]:not(:first)').removeAttr('checked');
		}
	});
	$('#executar')
	.click(function(){
		if($('#executarNoBanco').attr('checked')){
			if(!window.confirm( 'Deseja realmente destruir as tabelas selecionadas? \nEsta operação não tem retorno.')){
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