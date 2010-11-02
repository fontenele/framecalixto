function c(tipo,nome,valor,valoresPossiveis,attrs){
	if(tipo != 'select'){
		tipo = 'input type="'+tipo+'"';
		if(valor && (tipo == 'input type="radio"')) tipo+=' checked="checked"';
		if(valor && (tipo == 'input type="checkbox"')) tipo+=' checked="checked"';
	}
	comp = $('<'+tipo+'>')
		.combo(valoresPossiveis)
		.attr('tabindex',1)
		.attr('name',nome+'[]')
		.addClass('campoListagem')
		.addClass(nome)
		.val(valor);
	if(attrs) comp.attr(attrs);
	return $('<div/>').append(comp).addClass('d-td');
}
jQuery.fn.extend({
	estrutura : {
		"classes":[
			'%s/classes/C%s_excluir.php',
			'%s/classes/C%s_gravar.php',
			'%s/classes/C%s_mudarPagina.php',
			'%s/classes/C%s_pesquisar.php',
			'%s/classes/C%s_verEdicao.php',
			'%s/classes/C%s_verPesquisa.php',
			'%s/classes/I%s.php',
			'%s/classes/N%s.php',
			'%s/classes/P%s.mysql.php',
			'%s/classes/P%s.postgres.php',
			'%s/classes/P%s.sqlite.php',
			'%s/classes/P%s.oracle.php'
		],
		"templates":[
			'%s/html/C%s_verEdicao.html',
			'%s/html/C%s_verPesquisa.html'
		],
		"xml":[
			'%s/xml/entidade.xml',
			'%s/xml/pt_BR.xml'
		]
	},
	tipoDeDados:{
		'texto':			'texto',
		'numerico':			'numerico',
		'data':				'data',
		'tdocumentopessoal':'TCpf',
		'tcnpj':			'TCnpj',
		'tcep':				'TCep',
		'ttelefone':		'TTelefone',
		'tnumerico':		'TNumerico',
		'tmoeda':			'TMoeda'
	},
	componentes:{
		'caixa de entrada':		'Entrada (input)'						,
		'oculto':				'Oculto (hidden)'						,
		'caixa de combinacao':	'Caixa de Combinação (select)'			,
		'caixa de selecao':		'Caixa de Seleção (select multiple)'	,
		'radios':				'Lista de opções (radios)'				,
		'palavra chave':		'Palavra Chave (password)'				,
		'marcador':				'Marcador (checkbox)'					,
		'caixa de texto':		'Caixa de Texto (textarea)'				,
		'nome completo':		'Nome completo de pessoa'				,
		'email':				'Email'									,
		'data':					'Data: 01/01/1980'						,
		'hora':					'Hora: 23:59'							,
		'data e hora':			'Data e Hora: 01/01/1980 23:59'			,
		'telefone':				'Telefone: (99)9999-9999 '				,
		'cep':					'CEP: 99.999-999'						,
		'documento pessoal':	'CPF: 99.999.999-99'					,
		'documento pessoal':	'CNPJ: 99.999.999/9999-99'				,
		'numerico':				'Número: 99.999.999,99'					,
		'moeda':				'Moeda: R$ 99.999.999,99'
	},
	combo:function(valores,semIndice){
		if(!valores) return $(this);
		comp = this;
		$.each(valores, function(v,t){
			comp.append(new Option(t, semIndice ? t : v));
		});
		return $(this);
	},
	sugerirNgNome: function(){
		return $(this).val($(this).pegarLinhas('#ent').find('.en_nome').val().lowerCamelCase());
	},
	sugerirDbCampo: function(){
		return $(this).val($(this).pegarLinhas('#ent').find('.en_nome').val().toLowerCase().strReplace(' ','_').retiraAcentos());
	},
	sugerirViComponente: function(){
		linha = $(this).pegarLinhas('#neg');
		if(linha.find('.ng_chave_pk').attr('checked')) return $(this).val('oculto');
		if(linha.find('.ng_dominio_associativa').val()) return $(this).val('caixa de combinacao');
		switch(linha.find('.ng_tipo').val()){
			case('texto'):return $(this).val('caixa de entrada');
			case('numerico'):return $(this).val('caixa de entrada');
			case('data'):return $(this).val('data');
			case('tdocumentopessoal'):return $(this).val('documento pessoal');
			case('tcnpj'):return $(this).val('documento pessoal');
			case('tcep'):return $(this).val('cep');
			case('ttelefone'):return $(this).val('telefone');
			case('tnumerico'):return $(this).val('numerico');
			case('tmoeda'):return $(this).val('moeda');
		}
	},
	pegarNrLinha: function(){
		return $(this).parents('.d-tr').index();
	},
	pegarLinhas: function(tabela){
		tabela = tabela || '';
		return $(tabela+'.t-linhas').find('.d-tr:eq('+$(this).pegarNrLinha()+')');
	},
	remover: function(){
		return $('.t-linhas').find('.d-tr:eq('+$(this).pegarNrLinha()+')').remove();
	},
	adicionarLinha:function(valores,chave){
		valores = valores || '';
		chave = chave || '';
		if(valores){
			$(this).adicionarEntidade(
				valores.inter.nome,
				valores.inter.abreviacao,
				valores.inter.descricao
			);
			$(this).adicionarNegocio(
				valores.negocio.propriedade,
				valores.persistente.tamanho,
				valores.persistente.tipo,
				(valores.negocio.campo == chave),
				(valores.negocio.obrigatorio ? true : false),
				(valores.negocio.indiceUnico ? true : false),
				(valores.negocio.classeAssociativa ? true : false),
				valores.inter.dominio,
				(valores.negocio.classeAssociativa ? valores.negocio.classeAssociativa : ''),
				(valores.negocio.classeAssociativa ? valores.negocio.metodoLeitura : '')
			);
			$(this).adicionarPersistente(
				valores.negocio.campo,
				valores.persistente.ordem,
				valores.persistente.tipoOrdem,
				(valores.persistente.chaveEstrangeira ? valores.persistente.chaveEstrangeira.tabela : ''),
				(valores.persistente.chaveEstrangeira ? valores.persistente.chaveEstrangeira.campo : '')
			);
			$(this).adicionarVisualizacao(
				valores.controle.componente,
				(valores.controle.listagem ? valores.controle.ordem : '' ),
				valores.negocio.descritivo,
				(valores.controle.listagem ? valores.controle.largura.strReplace('%','') : '' )
			);
		}else{
			$(this).adicionarEntidade();
			$(this).adicionarNegocio();
			$(this).adicionarPersistente();
			$(this).adicionarVisualizacao();
		}
	},
	adicionarEntidade:function(nome,abreviado,descricao){
		nome = nome || '';
		abreviado = abreviado || '';
		descricao = descricao || '';
		$('#ent').append(
			$('<div/>').addClass('d-tr')
				.append(c('text','en_nome',nome))
				.append(c('text','en_abreviacao',abreviado))
				.append(c('text','en_descricao',descricao))
				.append($('<div>')
							.addClass('d-td')
							.append($('<a href="#"><img border="0" src=".sistema/icones/delete.png"></a>')
							.click(function(){$(this).remover();})))
		);
	},
	adicionarNegocio:function (propriedade,tamanho,tipo,pk,nn,uk,fk,dominio,classe,metodo){
		propriedade = propriedade || '';
		tamanho = tamanho || '';
		tipo = tipo || '';
		pk = pk || '';
		nn = nn || '';
		uk = uk || '';
		fk = fk || '';
		dominio = dominio || '';
		classe = classe || '';
		metodo = metodo || 'lerTodos';
		linha = $('#neg div.d-tr:last').index();
		$('#neg').append(
			$('<div/>').addClass('d-tr')
				.append($('<div/>').addClass('d-td propriedade ui-state-default'))
				.append(c('text','ng_nome',propriedade))
				.append(c('select','ng_tipo',tipo,$(this).tipoDeDados).css({'width':'120px'}))
				.append(c('text','ng_tamanho',tamanho,null,{'size':20}))
				.append(c('radio','ng_chave_pk',pk).click(function(){$(this).parents('.d-tr').find('.ng_tipo').val('numerico')}))
				.append(c('checkbox','ng_nn',nn))
				.append(c('checkbox','ng_uk',uk))
				.append(c('checkbox','ng_fk',fk))
				.append(c('text','ng_dominio_associativa',fk ? ((classe+metodo) != '::lerTodos' ? classe+'::'+metodo : '') : dominio,null,{'size':100}))
		);
		$('#neg div.d-tr:last>.d-td>.ng_chave_pk').attr('name','ng_chave_pk').val(linha);
		$('#neg div.d-tr:last>.d-td>.ng_fk').attr('name','ng_fk['+linha+']').val(linha);
	},
	adicionarPersistente:function (campo,ordem,tipoOrdem,tabelaReferencia,campoReferencia){
		campo = campo || '';
		ordem = ordem || '';
		tipoOrdem = tipoOrdem || '';
		tabelaReferencia = tabelaReferencia || '';
		campoReferencia = campoReferencia || '';
		$('#per').append(
			$('<div/>').addClass('d-tr')
				.append($('<div/>').addClass('d-td propriedade ui-state-default'))
				.append(c('text','bd_campo',campo))
				.append(c('text','bd_ordem',ordem))
				.append(c('checkbox','bd_tipo_ordem',tipoOrdem))
				.append(tabelaReferencia ? c('text','bd_referencia_tabela',tabelaReferencia) : c('text','bd_referencia_tabela',tabelaReferencia).hide())
				.append(tabelaReferencia ? c('text','bd_referencia_campo',campoReferencia) : c('text','bd_referencia_campo',campoReferencia).hide())
		);
	},
	adicionarVisualizacao:function (componente,ordem,ordemDescritivo,largura){
		componente = componente || '';
		ordem = ordem || '';
		ordemDescritivo = ordemDescritivo || '';
		largura = largura || '';
		$('#vis').append(
			$('<div/>').addClass('d-tr')
				.append($('<div/>').addClass('d-td propriedade ui-state-default'))
				.append(c('select','vi_componente',componente,$(this).componentes))
				.append(c('text','vi_ordem',ordem).attr('size',3))
				.append(c('text','vi_ordemDescritivo',ordemDescritivo))
				.append(c('text','vi_largura',largura))
		);
	},
	preencherTela:function(definicao){
			$('#entidade').val(definicao.inter.nome);
			$('#nomeTabela').val(definicao.bd.nomeTabela);
			$('#nomeSequence').val(definicao.bd.nomeSequencia);
			$('#entidade').trigger('change',false);
			for(i in definicao.entidade){
				valores = definicao.entidade[i];
				$(this).adicionarLinha(valores,definicao.bd.chavePrimaria);
				$('.en_nome:last').trigger('change');
			}
	}
});
$(document).ready( function() {
	$("#tabs").tabs();
	$('input').live('keyup',function(event){
			focar = function(obj){return (foco = obj.find('.campoListagem')) ? foco.first().focus().select() : false;}
    		if(event.originalEvent.ctrlKey){
				coluna = $(this).parents('.d-td').index();
				switch(event.originalEvent.keyCode){
					case(39):if(!focar($(this).parents('.d-td').next())){}break; //Direita
					case(37):if(!focar($(this).parents('.d-td').prev())){}break; //Esquerda
					case(38):if(!focar($(this).parents('.d-tr').prev().children(':eq('+coluna+')'))){}break; //Acima
					case(40):if(!focar($(this).parents('.d-tr').next().children(':eq('+coluna+')'))){}break; //Abaixo
				}
			}
   			return true;
    	});
	$('.adicionar').click(function(){$(this).adicionarLinha();});
	$('#entidade').change(function(){
		$('.arquivo').remove();
		if($('#entidade').val()){
			dir = $('#entidade').val().lowerCamelCase();
			nomeClasse = $('#entidade').val().upperCamelCase();
			$.each($(this).estrutura,function(classe,arquivos){
				$.each(arquivos,function(i,arquivo){
					arquivo = sprintf(arquivo,dir,nomeClasse);
					$('#arq .d-tr:last').after($('<div>').addClass('d-tr arquivo').addClass(classe)
						.append($('<div>').addClass('d-td').append($('<input type="checkbox">').attr('name','arquivo['+arquivo+']')))
						.append($('<div>').addClass('d-td').html(arquivo))
					);
				});
			});
		}
	});
	$('#sugerirNomeTabela').click(function(){
		if($('#entidade').val()) $('#nomeTabela').val($('#entidade').val().toLowerCase().strReplace(' ','_').retiraAcentos());return false;
	});
	$('#sugerirNomeSequence').click(function(){
		if($('#entidade').val()) $('#nomeSequence').val('sq_' + $('#entidade').val().toLowerCase().strReplace(' ','_').retiraAcentos());return false;
	});
	$('#sugerirNomesCampos').click(function(){
		$('.bd_campo').each(function(){$(this).sugerirDbCampo();});return false;
	});
	$('#sugerirNomesPropriedades').click(function(){
		$('.ng_nome').each(function(){$(this).sugerirNgNome();});return false;
	});
	$('#sugerirComponentes').click(function(){
		$('.vi_componente').each(function(){$(this).sugerirViComponente();});return false;
	});
	$('#gerarArquivos').toggle(function(){
			$('.arquivo input[type="checkbox"]').attr('checked','checked');return false;
		},function(){
			$('.arquivo input[type="checkbox"]').attr('checked','');return false;
	});
	$('#gerarClasses').toggle(function(){
			$('.classes input[type="checkbox"]').attr('checked','checked');return false;
		},function(){
			$('.classes input[type="checkbox"]').attr('checked','');return false;
	});
	$('#gerarTemplates').toggle(function(){
			$('.templates input[type="checkbox"]').attr('checked','checked');return false;
		},function(){
			$('.templates input[type="checkbox"]').attr('checked','');return false;
	});
	$('#gerarXml').toggle(function(){
			$('.xml input[type="checkbox"]').attr('checked','checked');return false;
		},function(){
			$('.xml input[type="checkbox"]').attr('checked','');return false;
	});
	$('.guia').click(function(){
		$($(this).attr('href')).find('.campoListagem:first').focus().select()
	});
	$('.campoListagem')
		.live('focus',function(){$(this).parents('.d-td').addClass('focado');})
		.live('blur',function(){$(this).parents('.d-td').removeClass('focado');});
	$('input.campoListagem').live('focus',function(){
		$(this).addClass('focado');
	});
	$('input.campoListagem').live('blur',function(){
		$(this).removeClass('focado');
	});
	$('.en_nome').live('change',function(){
		$(this).pegarLinhas().find('.propriedade').html($(this).val());
		abrev = $(this).parents('.d-tr').find('.en_abreviacao');
		if(!abrev.val()) abrev.val($(this).val());
	});
	$('.ng_chave_pk').live('click',function(){
		$(this).parents('.d-tr').find('.ng_tipo').val('numerico');
	});
	$('.ng_fk').live('click',function(){
		if($(this).attr('checked')){
			$(this).pegarLinhas('#per').find('.bd_referencia_tabela').parent().show().removeAttr('style');
			$(this).pegarLinhas('#per').find('.bd_referencia_campo').parent().show().removeAttr('style');
		}else{
			$(this).pegarLinhas('#per').find('.bd_referencia_tabela').parent().hide();
			$(this).pegarLinhas('#per').find('.bd_referencia_campo').parent().hide();
		}
	});
	$('form').submit(function(){
		var marcado = false;
		$.each($('#arq input'),function(i,check){
			if($(check).attr('checked')) marcado = true;
		});
		if(!marcado) {
			$.erro('Sem arquivo', 'Selecione os arquivos para serem gerados na guia de configurações.');
			$('#guia_5').trigger('click');
			return false;
		}
		return confirm("Confirma a geração?");
	});
	if(definicao) $(this).preencherTela(definicao[0]);
});