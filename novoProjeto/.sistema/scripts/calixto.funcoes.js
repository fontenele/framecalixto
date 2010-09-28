$(document).ready( function() {
	$('form').submit(function(){
		var erros = '';
		$.each($(this).find('.obrigatorio'),function(i,campoObrigatorio){
			if(!$(campoObrigatorio).val()){
				if(!erros) primeiroErro = campoObrigatorio;
				erros += $(campoObrigatorio).attr('title')+";\n";
			}
		});
		if(erros){
			$.erro("Restrições de obrigatoriedade:",erros);
			if(primeiroErro) $(primeiroErro).focus();
			return false;
		}
		return true;
	});
	$('.obrigatorio')
		.focus(function(){$(this).campoObrigatorio();})
		.keypress(function(){$(this).campoObrigatorio();})
		.blur(function(){$('#'+$(this).attr('id')+'_obrigatoriedade').html('*');});
    $('.cnpj').mask("99.999.999/9999-99",{completed:function(){}});
    $('.cpf').mask("999.999.999-99",{completed:function(){}});
	$('.cep').mask("99.999-999",{completed:function(){}});
	$('.telefone').mask("(99) 9999-9999? r:9999",{completed:function(){}});
	$('.hora').mask("99:99:99",{completed:function(){}});
	$('.data').mask("99/99/9999");
	//$('.numerico').mask("999999999999");
	$('.moeda').blur(function() {
		if($(this).val() == 'R$ 0,00') {
			$(this).val('');
		}
	});
	if($.priceFormat){
		$(".moeda").priceFormat({
			prefix: 'R$ ',
			centsSeparator: ',',
			thousandsSeparator: '.'
		});
	}
	$('.email')
		.live('blur',function(){
			re = /^[\w!#$%&'*+\/=?^`{|}~-]+(\.[\w!#$%&'*+\/=?^`{|}~-]+)*@(([\w-]+\.)+[A-Za-z]{2,6}|\[\d{1,3}(\.\d{1,3}){3}\])$/;
			if(!re.exec($(this).val())){
				$(this).val(null);
	            $.erro(JS_ERRO_EMAIL);
			}
		});
	if($.datepicker){
		$.datepicker.setDefaults($.datepicker.regional['pt-BR']);
		$('.data').datepicker({
			dateFormat: 'dd/mm/yy',
			showOn: 'both',
			buttonImage: '.sistema/icones/date.png',
			buttonImageOnly: true ,
			yearRange: '-100:+20'
		});
		$.each($('.hora'),function(i,hora){
			$(hora).after('<div id="'+$(hora).attr('id')+'_img" class="img_relogio" ></div>');
		});
		if($.timepicker){
			$('.hora')
				.timepicker({
					clockIcon:'.sistema/imagens/relogio.png',
					hourLabel:'h',
					minLabel:'m',
					secLabel:'s'
				});
			$('.sliderTime').hide();
		}
	}
	/*$("#suggest13").autocomplete("?c=CUtilitario_mapaSistema", {
		minChars: 0,
		width: 310,
		matchContains: "word",
		autoFill: false,
		formatItem: function(row, i, max) {
			return row[1];
			//return i + "/" + max + ": \"" + row[0] + "\" [" + row[1] + "]";
		},
		formatMatch: function(row, i, max) {
			return row.name + " " + row.to;
		},
		formatResult: function(row) {
			return row.to;
		},
		select: function(data){
			console.log(data);
		}
	});
	$('#suggest13').result(function(objeto,dados,valor){
		window.location ='?c='+valor;
	});*/

/*	$.autocomplete($('#seletorPrograma'),{
		delay:10,
		minChars:2,
		matchSubset:1,
		matchContains:1,
		cacheLength:10,
		onItemSelect:  function (li) {
			findValue(li);
		},
		onFindValue:  function Value(li) {
			// if coming from an AJAX call, let's use the CityId as the value
			if( !!li.extra ) var sValue = li.extra[0];
			// otherwise, let's just display the value in the text box
			else var sValue = li.selectValue;
			//alert(&quot;The value you selected was: &quot; + sValue);
		},
		formatItem:function formatItem(row) {
			return row[0] + ' (id: ' + row[1] + ')';
		},
		autoFill:true
	});
*/

    $("input:checkbox[readonly]").click( function(){ return false; } );
	/*$('textarea')
		.blur(function(){
			if(!$(this).attr('id')) return;
			$(this).val($(this).val().substring(0,parseInt($(this).attr('limite'))));
			$('#textarea_'+$(this).attr('id')).remove();
		})
		.focus(function(){
			if(!$(this).attr('id')) return;
			if(!$(this).attr('limite')) $(this).attr('limite',3000);
			$(this).after('<div id="textarea_'+$(this).attr('id')+'">Limite de caracteres <span>'+ $(this).val().length +'/'+$(this).attr('limite')+'</span></div>');
		})
		.live('keypress', function(event){
			if(!$(this).attr('id')) return true;
			if(event.keyCode == 9 || event.keyCode == 8){
				$('#textarea_'+$(this).attr('id')+' span').html(($(this).val().length +1) +'/'+$(this).attr('limite'));
				return true;
			}
			if($(this).val().length > parseInt($(this).attr('limite'))-1) return false;
			$('#textarea_'+$(this).attr('id')+' span').html(($(this).val().length +1) +'/'+$(this).attr('limite'));
			return true;
		});
    */
	$('#favoritos').click(function(){
		if (window.sidebar){
			window.sidebar.addPanel(document.title,document.location,"");
		}else if(window.opera && window.print){
			var mbm = document.createElement('a');
			mbm.setAttribute('rel','sidebar');
			mbm.setAttribute('href',url);
			mbm.setAttribute('title',title);
			mbm.click();
		}else if(document.all){
			window.external.AddFavorite(document.location,document.title);
		}
	});
    $("#seletorDePagina").change( function( ) {window.location = "?c="+$.getURLParam("c").split('_')[0]+"_mudarPagina&pagina=" + $(this).val();});
    $("#seletorPagina").change( function( ) {window.location = "?c="+$.getURLParam("c")+"&pagina=" + $(this).val();});
});
function x(obj){if(window.console) console.log(obj);}
jQuery.validar = {
	cpf:function (valor){},
	cnpj:function (valor){},
	data:function (valor){},
	hora:function (valor){},
	email:function (valor){}
};
jQuery.fn.campoObrigatorio = function(){if(!jQuery(this).val()) jQuery('#'+jQuery(this).attr('id')+'_obrigatoriedade').html('* Campo obrigatório');}
jQuery.msg = function(titulo,msg){alert(titulo+"\n\n"+msg);}
jQuery.erro = function(titulo,msg){alert(titulo+"\n\n"+msg);}
jQuery.submeter = function(formulario){
	formulario = formulario || document.formulario;
	jQuery(formulario).trigger('submit');
}
jQuery.getURLParam = function(strParamName){
	var strReturn = "";
	var strHref = window.location.href;
	var bFound=false;
	var cmpstring = strParamName + "=";
	var cmplen = cmpstring.length;
	if ( strHref.indexOf("?") > -1 ){
		var strQueryString = strHref.substr(strHref.indexOf("?")+1);
		var aQueryString = strQueryString.split("&");
		for ( var iParam = 0; iParam < aQueryString.length; iParam++ ){
			if (aQueryString[iParam].substr(0,cmplen)==cmpstring){
				var aParam = aQueryString[iParam].split("=");
				strReturn = aParam[1];
				bFound=true;
				break;
			}
		}
	}
	if (bFound==false) return null;
	return strReturn;
}
