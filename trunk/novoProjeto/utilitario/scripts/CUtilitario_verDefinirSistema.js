$.fn.testar = function(link,dados,msg,teste){
	teste = teste || $(this).parents('.campo').find('.teste');
	$.ajax({
		url: link,
		type:'POST',
		data:dados,
		dataType: 'text',
		success: function(data){
			if(data){
				teste.addClass('icon-remove');
				teste.attr('title',data);
			}else{
				teste.removeClass('icon-remove');
				teste.attr('title',msg);
			}
		}
	});
}
$.fn.testarConexao = function(){
	var cmp = $(this).parents('tr:first').find('td select, .db input:not(:disabled)');
	var teste = $(this).parents('tr:first').find('td .teste-db');
	$(this).testar("?c=CUtilitario_testarConexao",cmp.serialize(),'Conexão estabelecida.',teste);
}
$.fn.testarDiretorio = function(){
	$(this).testar("?c=CUtilitario_testarCaminho",'diretorio='+$(this).val(),'Diretório legível.');
}
$.fn.testarArquivo = function(){
	$(this).testar("?c=CUtilitario_testarCaminho",'arquivo='+$(this).val(),'Arquivo legível.');
}
$.fn.testarClasse = function(){
	$(this).testar("?c=CUtilitario_testarClasse",'classe='+$(this).val(),'Classe encontrada.');
}
$.fn.testarMetodo = function(){
	$(this).testar("?c=CUtilitario_testarClasse",'metodo='+$(this).val()+'&classe='+$('#'+$(this).attr('classe')).val(),'Método encontrado.');
}
$.fn.classeErro = function(msg){
	$(this).removeAttr('class');
	$(this).addClass('icon-ok');
	$(this).attr('title',msg);
}
$.fn.classeOk = function(msg){
	$(this).removeAttr('class');
	$(this).addClass('icon-remove');
	$(this).attr('title',msg);
}
$(document).ready(function(){
	$('#novaConexao').click(function(){$('#conexoes').append($('#conexoes tr:last').clone());});
	$('.classe').change(function(){$(this).testarClasse();}).trigger('change');
	$('.metodo').change(function(){$(this).testarMetodo();}).trigger('change');
	$('#inter').change(function(){$(this).testarArquivo();}).trigger('change');
	$('.diretorio').change(function(){$(this).testarDiretorio();}).trigger('change')
		.blur(function(){
			if($(this).val().charAt($(this).val().length -1) != '/'){
				$(this).val($(this).val()+'/');
			}
		});
	$('.tipoConn').live('change',function(){
		$(this).parent().find('.db').each(function(){
			$(this).find('input').addClass('disable').attr('disabled','disabled');
		});
		$(this).parent().find('.'+$(this).find('select').val()).each(function(){
			$(this).find('input').removeClass('disable').removeAttr('disabled');
		});
	});
	$('.tipoConn, .db').live('change',function(){$(this).testarConexao();});
	$('.tipoConn').trigger('change');
	$('#salvar').click(function(){
		$('form').submit();
	});
	$('form').submit(function(event){
		if($('.icon-remove')[0]){
			$($('.icon-remove:first').attr('data-guia')).trigger('click');
			alert($('.icon-remove:first').attr('title'));
			return false;
		}
	});
});