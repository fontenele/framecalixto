$(function() {
	$.fn.serialize = function(options) {
		return $.param(this.serializeArray(options));
	};
	$.fn.serializeArray = function(options) {
		var o = $.extend({
			checkboxesAsBools: false
		}, options || {});

		var rselectTextarea = /select|textarea/i;
		var rinput = /text|hidden|password|search/i;

		return this.map(function() {
			return this.elements ? $.makeArray(this.elements) : this;
		}).filter(function() {
			return this.name && !this.disabled &&
					(this.checked
							|| (o.checkboxesAsBools && this.type === 'checkbox')
							|| rselectTextarea.test(this.nodeName)
							|| rinput.test(this.type));
		}).map(function(i, elem) {
			var val = $(this).val();
			return val == null ?
					null :
					$.isArray(val) ?
					$.map(val, function(val, i) {
				return {name: elem.name, value: val};
			}) :
					{
						name: elem.name,
						value: (o.checkboxesAsBools && this.type === 'checkbox') ? //moar ternaries!
								(this.checked ? 1 : 0) :
								val
					};
		}).get();
	};
});
$.fn.validar = function() {
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
$.fn.vazio = function(){
    var vazio = {};
	$(this).each(function(){
	   vazio[$(this).val()] = true;
	});
	return vazio[''] ? true : false;
};
$.fn.repetencia = function(){
	var valores = {};
	$(this).each(function(){
		var val = $(this).val();
		if(valores[val]){
			valores[val]++;
		}else{
			valores[val] = 1;
		}
	});
	for(i in valores){
		if(valores[i] > 1) return true;
	}
	return false;
}
/**
 * Executa o preenchimento de um elemento jQuery com um objeto
 * ao se definir uma classe no elemento como js-html-{meuIndiceDoObjeto} será feito um innerHtml no elemento
 * ao se definir uma classe no elemento como js-val-{meuIndiceDoObjeto} será setado o valor no elemento
 * @param {Object} objeto
 * @param {Array} extras
 * @returns jQuery
 */
jQuery.fn.preencher = function(objeto, extras) {
	var $this = $(this);
	var opt = ['html', 'val'];
	opt = (extras) ? opt.concat(extras) : opt;
	for (i in objeto)
		for (var j in opt)
			$this.find('.js-' + opt[j] + '-' + i)[opt[j]](objeto[i]);
	return $(this);
};
jQuery.fn.check = function(val) {
	var type = $(this).attr('type');
	if ((type === 'checkbox') || (type === 'radio')) {
		if (val) {
			$(this).attr('checked', 'checked');
		} else {
			$(this).removeAttr('checked');
		}
	}
	return $(this);
};
jQuery.fn.oculto = function(val) {
	if (val) {
		$(this).removeClass('oculto');
	} else {
		$(this).addClass('oculto');
	}
	return $(this);
};
jQuery.fn.pegarNrLinha = function() {
	return $(this).parents('tr').index();
};
jQuery.fn.pegarLinhas = function(tbody) {
	tbody = tbody || '.js-tbody';
	return $(tbody).find('tr:eq(' + $(this).pegarNrLinha() + ')');
};
jQuery.fn.pegarDominio = function() {
	if (!$(this).val())
		return false;
	if ($(this).val().split('::').length > 1) {
		return false;
	}
	val = $(this).val().trim().substr(1, $(this).val().length - 2);
	cod = new Array;
	txt = new Array;
	$.each(val.split(']['), function(i, val) {
		a = val.split(',');
		cod.push(a[0]);
		txt.push(a[1]);
	});
	return {
		"cod": cod,
		"txt": txt
	};
};
jQuery.fn.linhaToJson = function() {
	var res = {};
	var $linha = $(this).pegarLinhas();
	$linha.find('select, input').each(function() {
		var name = $(this).attr('name').replace(/\[\]/g, '').replace(/\]/g, '').replace(/\[/g, '-');
		var type = $(this).attr('type');
		if ((type === 'checkbox') || (type === 'radio')) {
			res[name] = $(this).attr('checked') ? true : false;
		} else {
			res[name] = $(this).val();
		}
	});
	res['negocio-itens-dominio'] = $linha.find('.js-val-negocio-dominio').pegarDominio();
	res['negocio-valores-texto'] = '';
	res['negocio-valores-codigo'] = '';
	if (res['negocio-itens-dominio']) {
		res['negocio-valores-texto'] = res['negocio-itens-dominio'].txt.join(', ');
		res['negocio-valores-codigo'] = res['negocio-itens-dominio'].cod.join(', ');
	}
	return res;
};
jQuery.fn.exibirReferencia = function() {
	$(this).each(function() {
		var exibir = ($(this).attr('checked')) ? 'show' : 'hide';
		$(this).pegarLinhas('.js-tbody-persistente').find('.js-val-persistente-referencia-tabela').parent()[exibir]();
		$(this).pegarLinhas('.js-tbody-persistente').find('.js-val-persistente-referencia-campo').parent()[exibir]();
	});
	return $(this);
};
jQuery.fn.gerarCadastro = function() {
	if($('.js-executar:checked').val() == '1'){
		$('#certeza').modal('show');
	}else{
		$(this).executarGeracao();
	}
};
jQuery.fn.executarGeracao = function(){
	if (!$('form').validar())
		return;
	if (!$('#configuracao input[type="checkbox"]:checked')[0]) {
		$('.js-tab-configuracao').trigger('click');
		$.msg('Nada para executar.', 'Selecione alguma ação para o gerador executar.');
		return;
	}
	if($('#propriedade .js-val-inter-nome').vazio()){
		$('.js-tab-propriedade').trigger('click');
		$.msg('Propriedades',"Existem propriedades sem nome");
		return;
	}
	if($('#propriedade .js-val-inter-nome').repetencia()){
		$('.js-tab-propriedade').trigger('click');
		$.msg('Propriedades',"Existem propriedades repetidas");
		return;
	}
	if($('#negocio .js-val-negocio-propriedade').vazio()){
		$('.js-tab-negocio').trigger('click');
		$.msg('Negócio',"Existem atributos sem nome");
		return;
	}
	if($('#negocio .js-val-negocio-propriedade').repetencia()){
		$('.js-tab-negocio').trigger('click');
		$.msg('Negócio',"Existem atributos repetidos");
		return;
	}
	if($('#persistente .js-val-persistente-campo').vazio()){
		$('.js-tab-persistente').trigger('click');
		$.msg('Persistente',"Existem campos da tabela sem nome");
		return;
	}
	if($('#persistente .js-val-persistente-campo').repetencia()){
		$('.js-tab-persistente').trigger('click');
		$.msg('Persistente',"Existem campos da tabela repetidos");
		return;
	}
	if (!$('#negocio .js-val-negocio-pk:checked')[0]) {
		$('.js-tab-negocio').trigger('click');
		$.msg('Sem chave primária', 'Selecione algum campo para ser a PK do cadastro.');
		return;
	}
	$('#resultado .modal-body').html('Gerando cadastro ...');
	$('#resultado').modal('show');
	$.ajax({
		url: '?c=CUtilitario_geradorEntidade',
		type: 'post',
		dataType: 'html',
		data: $('form').serialize({checkboxesAsBools: true})
	}).done(function(data) {
		$('#resultado .modal-body').html(data);
	});
}
var Template = {};
Template.adicionar = function(dados) {
	var dados = dados || {};
	var abas = ['propriedade', 'negocio', 'persistente', 'visualizacao'];
	for (i in abas) {
		Template[abas[i]](dados);
	}
};
Template.propriedade = function(dados) {
	$('.js-tbody-propriedade').append($('#templates .js-tpl-propriedade').clone(true).preencher(dados));
};
Template.negocio = function(dados) {
	var $tpl = $('#templates .js-tpl-negocio').clone(true);
	var $pk = $tpl.find('input[type="radio"]');
	$pk.check(dados['negocio-pk']);
	$('.js-tbody-negocio').append($tpl.preencher(dados, ['check']));
	$pk.attr('value', dados['negocio-propriedade']);
};
Template.persistente = function(dados) {
	$('.js-tbody-persistente').append($('#templates .js-tpl-persistente').clone(true).preencher(dados, ['check']));
};
Template.visualizacao = function(dados) {
	$('.js-tbody-visualizacao').append($('#templates .js-tpl-visualizacao').clone(true).preencher(dados, ['check']));
};
Template.arquivo = function(nome) {
	$('.js-tbody-arquivo tbody').remove();
	$tpl = $('#templates .js-tpl-arquivo').clone(true);
	$tpl.find('.js-html-arquivo').each(function() {
		var arquivo = sprintf($(this).attr('data-arquivo'), nome.lowerCamelCase(), nome.upperCamelCase());
		$(this).prev().find('input').attr('name', 'arquivo[' + arquivo + ']');
		$(this).html(arquivo);
	});
	$('.js-tbody-arquivo').append($tpl);
};
Template.atualizarUC = function($el) {
	var dados = $el.linhaToJson();
	Template.ucItemPesquisa(dados, $el);
	Template.ucItemEdicao(dados, $el);
	Template.ucItemListagem(dados, $el);
	Template.ucItemObrigatorio(dados, $el);
};
Template.ucItemPesquisa = function(dados, $el) {
	var nr = $el.pegarNrLinha();
	if ($('.js-tpl-uc-item-pesquisa' + nr)[0]) {
		var $tpl = $('.js-tpl-uc-item-pesquisa' + nr);
	} else {
		var $tpl = $('#templates .js-tpl-uc-item-pesquisa').clone();
		$('.uc-campos-pesquisa .uc-opcao:first').parent().before($tpl);
		$tpl.addClass('js-tpl-uc-item-pesquisa' + nr);
	}
	$tpl[dados['visualizacao-pesquisa'] ? 'removeClass' : 'addClass']('oculto');
	$tpl.preencher(dados, ['oculto']);
};
Template.ucItemEdicao = function(dados, $el) {
	var nr = $el.pegarNrLinha();
	if ($('.js-tpl-uc-item-edicao' + nr)[0]) {
		var $tpl = $('.js-tpl-uc-item-edicao' + nr);
	} else {
		var $tpl = $('#templates .js-tpl-uc-item-edicao').clone();
		$('.uc-campos-edicao .uc-opcao:first').parent().before($tpl);
		$tpl.addClass('js-tpl-uc-item-edicao' + nr);
	}
	$tpl[dados['visualizacao-edicao'] ? 'removeClass' : 'addClass']('oculto');
	$tpl.preencher(dados, ['oculto']);
};
Template.ucItemListagem = function(dados, $el) {
	var nr = $el.pegarNrLinha();
	if ($('.js-tpl-uc-item-listagem' + nr)[0]) {
		var $tpl = $('.js-tpl-uc-item-listagem' + nr);
	} else {
		var $tpl = $('#templates .js-tpl-uc-item-listagem').clone();
		$('.uc-colunas .uc-opcao:first').parent().before($tpl);
		$tpl.addClass('js-tpl-uc-item-listagem' + nr);
	}
	$tpl[dados['visualizacao-ordem'] ? 'removeClass' : 'addClass']('oculto');
	$tpl.preencher(dados, ['oculto']);
};
Template.ucItemObrigatorio = function(dados, $el) {
	var nr = $el.pegarNrLinha();
	if ($('.js-tpl-uc-item-obrigatorio' + nr)[0]) {
		var $tpl = $('.js-tpl-uc-item-obrigatorio' + nr);
	} else {
		var $tpl = $('#templates .js-tpl-uc-item-obrigatorio').clone();
		$('.uc-gravacao-sem-dados').append($tpl);
		$tpl.addClass('js-tpl-uc-item-obrigatorio' + nr);
	}
	$tpl[dados['negocio-nn'] ? 'removeClass' : 'addClass']('oculto');
	$tpl.preencher(dados, ['oculto']);
};
$(document).ready(function() {
	$('.js-executar').click(function() {
		if ($(this).attr('value') == '1') {
			$('.js-titulo-executando').show();
			$('.js-titulo-visualizando').hide();
		} else {
			$('.js-titulo-executando').hide();
			$('.js-titulo-visualizando').show();
		}
	});
	$('#entidade').change(function() {
		Template.arquivo($(this).val());
	});
	$('#adicionar-linha').click(function() {
		Template.adicionar();
	});
	$('.js-val-negocio-propriedade').change(function(){
		$(this).val($(this).val().lowerCamelCase().retiraEspeciais());
	});
	$('.js-val-persistente-campo').change(function(){
		$(this).val($(this).val().makeLowerUnderLine().retiraEspeciais());
	});
	$('.remover').click(function() {
		$(this).pegarLinhas().remove();
	});
	$('.js-val-inter-nome').change(function() {
		var $linhas = $(this).pegarLinhas();
		var $abrev = $linhas.find('.js-val-inter-abreviacao');
		$linhas.find('.js-html-inter-nome').html($(this).val());
		if (!$abrev.val())
			$abrev.val($(this).val());
	});
	$('.js-val-negocio-pk').click(function() {
		$(this).pegarLinhas('.js-tbody-negocio').find('.js-val-negocio-tipo').val('numerico');
	});
	$('.js-check-negocio-fk').click(function() {
		$(this).exibirReferencia();
	});
	$('.gerar-tipo').toggle(function() {
		$('#configuracao .' + $(this).attr('data-tipo')).check(1);
	}, function() {
		$('#configuracao .' + $(this).attr('data-tipo')).check();
	});
	$('#sugerirNomeTabela').click(function(){
		$('#nomeTabela').val($('#entidade').val().makeLowerUnderLine());
		
	});
	$('#sugerirNomeSequence').click(function(){
		var valor = $('#nomeTabela').val() 
			? 'seq_'+$('#nomeTabela').val().makeLowerUnderLine() 
			: $('#entidade').val().makeLowerUnderLine();
		$('#nomeSequence').val(valor);
	});
	$('#sugerirNomesPropriedades').click(function() {
		$('.js-tbody-propriedade .js-val-inter-nome').each(function() {
			var valor = $(this).val().lowerCamelCase().retiraEspeciais();
			$(this).pegarLinhas('.js-tbody-negocio').find('.js-val-negocio-propriedade').val(valor);
		});
	});
	$('#sugerirNomesCampos').click(function() {
		var prefixo = $('#nomeTabela').val().strReplace('[^a-zA-Z0-9]', '').substr(0, 4);
		prefixo = prefixo ? prefixo + '_' : '';
		$('.js-tbody-propriedade .js-val-inter-nome').each(function() {
			var valor = prefixo + $(this).val().makeLowerUnderLine().retiraEspeciais();
			$(this).pegarLinhas('.js-tbody-persistente').find('.js-val-persistente-campo').val(valor);
		});
	});
	$('#sugerirComponentes').click(function() {
		$('.js-tbody-visualizacao .js-val-visualizacao-componente').each(function() {
			var $linha = $(this).pegarLinhas('.js-tbody-negocio');
			if ($linha.find('.js-val-negocio-pk').attr('checked'))
				return $(this).val('oculto');
			if ($linha.find('.js-val-negocio-dominio').val())
				return $(this).val('caixa de combinacao');
			if ($linha.find('.js-val-negocio-tamanho').val() > 100)
				return $(this).val('caixa de texto');
			$(this).val($linha.find('.js-val-negocio-tipo option:selected').attr('data-componente'));
		});
	});
	$('.js-val-negocio-tipo').change(function(){
		var $linha = $(this).pegarLinhas('.js-tbody-negocio');
		var $tamanho = $linha.find('.js-val-negocio-tamanho');
		var $option = $linha.find('.js-val-negocio-tipo option:selected');
		if($option.attr('data-tamanho-fixo')) return $tamanho.val($option.attr('data-tamanho-fixo'));
		if($option.attr('data-tamanho') && !$tamanho.val()) return $tamanho.val($option.attr('data-tamanho'));
	});
	$('.js-val-negocio-tamanho').change(function(){
		var $linha = $(this).pegarLinhas('.js-tbody-negocio');
		var $option = $linha.find('.js-val-negocio-tipo option:selected');
		if($option.attr('data-tamanho-fixo')) $(this).val($option.attr('data-tamanho-fixo'));
	});
	$('.js-tab-caso-de-uso').click(function(){
		$('.js-val-inter-nome').each(function(){
			Template.atualizarUC($(this));
		});
	});
	$('#nao-certeza').click(function(){
		$('#certeza').modal('hide');
	});
	$('#sim-certeza').click(function(){
		$('#certeza').modal('hide');
		$(this).executarGeracao();
	});
	if (definicao) {
		$('.js-definicao-entidade').preencher(definicao.entidade);
		$('.js-definicao-banco').preencher(definicao.banco);
		for (var i in definicao.campos) {
			Template.adicionar(definicao.campos[i]);
		}
		$('.js-check-negocio-fk').exibirReferencia();
		$('#entidade').trigger('change');
	}
});
