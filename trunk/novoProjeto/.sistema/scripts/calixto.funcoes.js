function number_format(number, decimals, dec_point, thousands_sep) {
	number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
	var n = !isFinite(+number) ? 0 : +number,
			prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
			sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
			dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
			s = '',
			toFixedFix = function(n, prec) {
		var k = Math.pow(10, prec);
		return '' + Math.round(n * k) / k;
	};
	// Fix for IE parseFloat(0.55).toFixed(0) = 0;
	s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
	if (s[0].length > 3) {
		s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
	}
	if ((s[1] || '').length < prec) {
		s[1] = s[1] || '';
		s[1] += new Array(prec - s[1].length + 1).join('0');
	}
	return s.join(dec);
}
function price_to_float(price) {
	return parseFloat(price.replace('.', '').replace(',', '').replace('R$ ', '')) / 100;
}
function moeda(valor) {
	return 'R$ ' + number_format(valor, 2, ',', '.');
}
var Numerico = {
	formatar: function(valor){
		return number_format(valor, 2, ',', '.');
	},
	desformatar: function(valor){
		return parseFloat(valor.replace('.', '').replace(',', '').replace('R$ ', '')) / 100;
	}
};
var Moeda = {
	formatar: function(valor) {
		return 'R$ ' + Numerico.formatar(valor);
	},
	desformatar: function(valor) {
		return Numerico.desformatar(valor);
	},
	mascarar: function(valor) {
		return Moeda.formatar(valor);
	},
	desmascarar: function(valor) {
		return Numerico.desformatar(valor);
	}
};
var Erro = {
	negocio: "erroNegocio",
	login: "erroLogin",
	acesso: "erroAcesso"
};
Erro.redirecionarLogin = function() {
	window.location = "?c=CControleAcesso_verLogin";
};
Erro.redirecionarPrincipal = function() {
	window.location = "?c=CControleAcesso_verPrincipal";
};
Erro.aconteceu = function(dados) {
	if (dados && dados.erro) {
		return true;
	}
	return false;
};
Erro.doTipo = function(dados, tipo) {
	if (Erro.aconteceu(dados) && (dados.tipo === tipo))
		return true;
	return false;
};
Erro.alertar = function(dados) {
	if (dados && dados.erro) {
		if (Erro.doTipo(dados, Erro.login) || Erro.doTipo(dados, Erro.acesso)) {
			Erro.redirecionarLogin();
			return true;
		}
		alert(dados.erro);
		return true;
	}
	return false;
};

$.fn.atribuir = function(objeto) {
	for (var i in objeto) {
		$(this).find('[data-html-' + i + ']').html(objeto[i]);
		$(this).find('[data-val-' + i + ']').val(objeto[i]);
		var $obj = $(this).find('[data-attr-' + i + ']');
		if ($obj[0]) {
			var attrs = $obj.attr('data-attr-' + i).split('|');
			for (var j in attrs) {
				$obj.attr(attrs[j], objeto[i]);
			}
		}
	}
	return $(this);
};
$.fn.extrair = function(objeto) {
	for (var i in objeto) {
		var html = $(this).find('[data-html-' + i + ']').html();
		var val = $(this).find('[data-val-' + i + ']').val();
		var attr = $(this).find('[data-' + i + ']').attr('data-' + i);
		objeto[i] = attr ? attr : (val ? val : (html ? html : null));
	}
	return $(this);
};


$.fn.inputData = function() {
	$(this).formatarData();
	$(this).datepicker({format: "dd/mm/yyyy"})
	$(this).keypress(function(e) {
		var keyCode = e.keyCode || e.which;
		if (keyCode == 9) {
			$(this).datepicker('hide');
		}
	});
}

$.fn.inputMoeda = function() {
	$(this).blur(function() {
		if ($(this).val() == 'R$ 0,00') {
			$(this).val('');
		}
	});
	$(this).priceFormat({
		prefix: 'R$ ',
		centsSeparator: ',',
		thousandsSeparator: '.'
	});
}
$.fn.inputEmail = function() {
	$(this).live('blur', function() {
		re = /^[\w!#$%&'*+\/=?^`{|}~-]+(\.[\w!#$%&'*+\/=?^`{|}~-]+)*@(([\w-]+\.)+[A-Za-z]{2,6}|\[\d{1,3}(\.\d{1,3}){3}\])$/;
		if (!re.exec($(this).val())) {
			$(this).val(null);
			$.erro(JS_ERRO_EMAIL);
		}
	});
}
padraoSelect2 = {width: "300px", allowClear: true};
$.fn.autoCompletar = function() {
	$(this).select2(padraoSelect2);
	$('div.obrigatorio').removeClass('obrigatorio');
}
$.fn.contaContabil = function() {
	$(this).select2(padraoSelect2);
	$('div.obrigatorio').removeClass('obrigatorio');
}
jQuery.retornoAjax = function(dados) {
	if (Erro.alertar(dados))
		return;
	alert(dados.mensagem);
};
var ajajPost = {type: 'post', dataType: 'json', success: $.retornoAjax};
var ajajGet = $.extend(ajajPost, {type: 'get'});
jQuery.fn.validar = function() {
	var erros = '';
	$.each($(this).find('.obrigatorio'), function(i, campoObrigatorio) {
		if (!$(campoObrigatorio).val()) {
			if (!erros)
				primeiroErro = campoObrigatorio;
			erros += $(campoObrigatorio).attr('title') + ";\n";
		}
	});
	if (erros) {
		$.erro("Restrições de obrigatoriedade:", erros);
		if (primeiroErro)
			$(primeiroErro).focus();
		return false;
	}
	return true;
};
jQuery.fn.submitAjaxPost = function(funcao) {
	funcao = funcao ? funcao : jQuery.retornoAjax;
	if ($(this).validar()) {
		$.ajax({url: $(this).attr('action'), data: $(this).serialize(), type: 'post', dataType: 'json', success: funcao});
	}
	return false;
};

jQuery.fn.submitAjaxGet = function(funcao) {
	funcao = funcao ? funcao : jQuery.retornoAjax;
	if ($(this).validar()) {
		$.ajax({url: $(this).attr('action'), data: $(this).serialize(), type: 'post', dataType: 'json', success: funcao});
	}
	return false;
};
/**
 * Executa uma chamada ajax para o elemento configurado
 * @atributes data-ajax-change data-ajax-url="" data-ajax-type="get" data-ajax-data-type="html" (data-selector-data=""|data-function-data="") data-ajax-success=""
 * @param jQuery $this
 */
var calixtoAjaxConfigurador = function($this) {
	var dados = {};
	if ($this.attr('data-ajax-data')){
		eval('dados = '+$this.attr('data-ajax-data')+';');
	}
	if ($this.attr('data-selector-data')) {
		dados = $($this.attr('data-selector-data')).serialize();
	}
	if ($this.attr('data-function-data')) {
		var fn = eval($this.attr('data-function-data'));
		dados = fn($this);
	}
	var type = $this.attr('data-ajax-type') ? $this.attr('data-ajax-type') : 'get';
	var dataType = $this.attr('data-ajax-data-type') ? $this.attr('data-ajax-data-type') : 'html';
	var tipoResposta = dataType === 'json' ? '&tipoResposta=json' : '&tipoResposta=ajax';
	var url = ($this.attr('data-ajax-url') ? $this.attr('data-ajax-url') + tipoResposta : null);
	var success = $this.attr('data-ajax-success') ? eval($this.attr('data-ajax-success')) : function() {
	};
	var conf = {url: url, data: dados, type: type, dataType: dataType, success: success};
	//console.log(conf);
	$.ajax(conf);
};
$(document).ready(function() {
	$('textarea').blur(function() {
		if (!$(this).attr('id'))
			return;
		$(this).val($(this).val().substring(0, parseInt($(this).attr('limite'))));
		$('#textarea_' + $(this).attr('id')).remove();
	}).focus(function() {
		if (!$(this).attr('id'))
			return;
		if (!$(this).attr('limite'))
			$(this).attr('limite', 3000);
		$(this).after('<div id="textarea_' + $(this).attr('id') + '">Limite de caracteres <span>' + $(this).val().length + '/' + $(this).attr('limite') + '</span></div>');
	}).live('keypress', function(event) {
		if (!$(this).attr('id'))
			return true;
		if (event.keyCode === 9 || event.keyCode === 8) {
			$('#textarea_' + $(this).attr('id') + ' span').html(($(this).val().length + 1) + '/' + $(this).attr('limite'));
			return true;
		}
		if ($(this).val().length > parseInt($(this).attr('limite')) - 1)
			return false;
		$('#textarea_' + $(this).attr('id') + ' span').html(($(this).val().length + 1) + '/' + $(this).attr('limite'));
		return true;
	});
	$('form:not([data-ajax])').submit(function() {
		return $(this).validar();
	});
	/**
	 * Configurador de formulario ajax
	 * data-ajax data-ajax-type="json" data-ajax-success="Funcao.Retorno"
	 */
	$('form[data-ajax]').submit(function(event) {
		event.preventDefault();
		var validar = $(this).attr('data-validate') ? eval($(this).attr('data-validate')) : null;
		if ((validar ? validar($(this)) : $(this).validar())) {
			var dataType = $(this).attr('data-ajax-type') ? $(this).attr('data-ajax-type') : 'html';
			var tipoResposta = dataType === 'json' ? '&tipoResposta=json' : '&tipoResposta=ajax';
			var success = $(this).attr('data-ajax-success') ? eval($(this).attr('data-ajax-success')) : jQuery.retornoAjax;
			var url = $(this).attr('action') + tipoResposta;
			var conf = {url: url, data: $(this).serialize(), type: $(this).attr('method'), dataType: dataType, success: success};
//			console.log(conf);
			$.ajax(conf);
		}
		return false;
	});
	$('[data-ajax-change]').change(function() {
		calixtoAjaxConfigurador($(this));
	});
	$('[data-ajax-click]').click(function(event) {
		event.preventDefault();
		calixtoAjaxConfigurador($(this));
	});
	$('[data-check-all]').click(function() {
		if ($(this).attr('checked')) {
			$($(this).attr('data-check-selector')).attr('checked', 'checked');
		} else {
			$($(this).attr('data-check-selector')).removeAttr('checked');

		}
	});

	$('.email').live('blur', function() {
		re = /^[\w!#$%&'*+\/=?^`{|}~-]+(\.[\w!#$%&'*+\/=?^`{|}~-]+)*@(([\w-]+\.)+[A-Za-z]{2,6}|\[\d{1,3}(\.\d{1,3}){3}\])$/;
		if (!re.exec($(this).val())) {
			$(this).val(null);
			$.erro(JS_ERRO_EMAIL);
		}
	});
	$('.obrigatorio')
			.focus(function() {
		$(this).campoObrigatorio();
	})
			.keypress(function() {
		$(this).campoObrigatorio();
	})
			.blur(function() {
		$('#' + $(this).attr('id') + '_obrigatoriedade').html('<i class="icon-asterisk"></i>');
	});
	$('.cnpj').mask("99.999.999/9999-99", {completed: function() {
		}});
	$('.cpf').mask("999.999.999-99", {completed: function() {
		}});
	$('.cep').mask("99.999-999", {completed: function() {
		}});
	$('.telefone').mask("(99) 9999-9999? r:9999", {completed: function() {
		}});
	$('.hora').mask("99:99:99", {completed: function() {
		}});
	$('input.data').inputData();
	$('.moeda').inputMoeda();
	$('.email').inputEmail();
	$('.auto-completar').autoCompletar();
	$('.conta-contabil').contaContabil();


	$("input:checkbox[readonly]").click(function() {
		return false;
	});

	$('#favoritos').click(function() {
		if (window.sidebar) {
			window.sidebar.addPanel(document.title, document.location, "");
		} else if (window.opera && window.print) {
			var mbm = document.createElement('a');
			mbm.setAttribute('rel', 'sidebar');
			mbm.setAttribute('href', url);
			mbm.setAttribute('title', title);
			mbm.click();
		} else if (document.all) {
			window.external.AddFavorite(document.location, document.title);
		}
	});
	$("#seletorPagina").change(function( ) {
		window.location = "?c=" + $.getURLParam("c") + "&pagina=" + $(this).val();
	});
	$('.btn').attr('tabindex','1');
	$('abbr.atalho').each(function(){
		$(this).attr('title',"Alt + Shift + "+$(this).html());
	});
});
function x(obj) {
	if (window.console)
		console.log(obj);
}
jQuery.validar = {
	cpf: function(valor) {
	},
	cnpj: function(valor) {
	},
	data: function(valor) {
	},
	hora: function(valor) {
	},
	email: function(valor) {
	}
};
jQuery.msg = function(titulo, msg) {
	alert(titulo + "\n\n" + msg);
};
jQuery.erro = function(titulo, msg) {
	alert(titulo + "\n\n" + msg);
};
jQuery.submeter = function(formulario) {
	formulario = formulario || document.formulario;
	jQuery(formulario).trigger('submit');
};
jQuery.getURLParam = function(strParamName) {
	var strReturn = "";
	var strHref = window.location.href;
	var bFound = false;
	var cmpstring = strParamName + "=";
	var cmplen = cmpstring.length;
	if (strHref.indexOf("?") > -1) {
		var strQueryString = strHref.substr(strHref.indexOf("?") + 1);
		var aQueryString = strQueryString.split("&");
		for (var iParam = 0; iParam < aQueryString.length; iParam++) {
			if (aQueryString[iParam].substr(0, cmplen) === cmpstring) {
				var aParam = aQueryString[iParam].split("=");
				strReturn = aParam[1];
				bFound = true;
				break;
			}
		}
	}
	if (bFound === false)
		return null;
	return strReturn;
};

var x = function(obj) {
	if (window.console)
		console.log(obj);
}
jQuery.fn.campoObrigatorio = function() {
	if (!jQuery(this).val())
		jQuery('#' + jQuery(this).attr('id') + '_obrigatoriedade').html('<i class="icon-hand-left"></i> Campo obrigatório');
};
