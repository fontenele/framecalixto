dados = {
	linha:0,
	tipos:[
		['texto',				'texto'],
		['numerico',			'numerico'],
		['data',				'data'],
		['tdocumentopessoal',	'TCpf'],
		['tcnpj',				'TCnpj'],
		['tcep',				'TCep'],
		['ttelefone',			'TTelefone'],
		['tnumerico',			'TNumerico'],
		['tmoeda',				'TMoeda']
	],
	componentes:[
		['caixa de entrada',	'De Entrada (input)'					],
		['oculto',				'Oculto (hidden)'						],
		['caixa de combinacao',	'Caixa de Combinação (select)'			],
		['caixa de selecao',	'Caixa de Seleção (select multiple)'	],
		['radios',				'Lista de opções (radios)'				],
		['palavra chave',		'Palavra Chave (password)'				],
		['marcador',			'Marcador (checkbox)'					],
		['caixa de texto',		'Caixa de Texto (textarea)'				],
		['nome completo',		'Nome completo de pessoa'				],
		['email',				'Email'									],
		['data',				'Campo Data: 01/01/1980'				],
		['hora',				'Campo Hora: 23:59'						],
		['data e hora',			'Campo Data e Hora: 01/01/1980 23:59'	],
		['telefone',			'Telefone: (99)9999-9999 '				],
		['cep',					'CEP: 99.999-999'						],
		['documento pessoal',	'CPF: 99.999.999-99'					],
		['documento pessoal',	'CNPJ: 99.999.999/9999-99'				],
		['numerico',			'Número: 99.999.999,99'					],
		['moeda',				'Moeda: R$ 99.999.999,99'				]
	]
}
function entidade(){
	this.adicionar = function (nome,abreviado,descricao){
		dados.linha++;
		this.nome = nome || '';
		this.abreviado = abreviado || '';
		this.descricao = descricao || '';
		template =
			'<tr class="ln_'+ dados.linha +'" >' +
				'<td><input tabindex="1" type="text" name="en_nome['+ dados.linha +']"		id="en_nome_'+ dados.linha +'"			value="'+this.nome+'"		class="nome" /></td>' +
				'<td><input tabindex="1" type="text" name="en_abreviacao['+ dados.linha +']"	id="en_abreviacao_'+ dados.linha +'"		value="'+this.abreviado+'"	class="abreviado" /></td>' +
				'<td><input tabindex="1" type="text" name="en_descricao['+ dados.linha +']"	id="en_descicao_'+ dados.linha +'"		value="'+this.descricao+'"	class="descricao" /></td>' +
				'<td><a tabindex="1" href="javascript:remover('+ dados.linha +');">remover</a></td>' +
			'</tr>';
		$('#ent tr:last').after(template);
	}
	this.remover = function (linha){
		$('.ln_' + linha).remove();
	}
}
function negocio(){
	this.adicionar = function (propriedade,tamanho,tipo,pk,nn,uk,fk,dominio,classe,metodo){
		this.propriedade = propriedade || '';
		this.tamanho = tamanho || '';
		this.tipo = tipo || '';
		this.pk = pk || '';
		this.nn = nn || '';
		this.uk = uk || '';
		this.fk = fk || '';
		this.dominio = dominio || '';
		this.classe = classe || '';
		this.metodo = metodo || '';
		options = '';
		for(i in dados.tipos){
			selected = (tipo == dados.tipos[i][0]) ? 'selected="selected"' : '';
			options +='<option value="' + dados.tipos[i][0] + '" ' + selected + '>' + dados.tipos[i][1] + '</option>';
		}

		linha = '#linhaNeg' + dados.linha;
		template =
			'<tr class="ln_'+ dados.linha +'" >' +
				'<td  class="propriedade" ></td>' +
				'<td><input tabindex="1" type="text"		name="ng_nome['+dados.linha+']"		value="'+this.propriedade+'"	class="propriedade" /></td>' +
				'<td><input tabindex="1" type="text"		name="ng_tamanho['+dados.linha+']"	value="'+this.tamanho+'"		class="tamanho" size="4" /></td>' +
				'<td><select tabindex="1" 				name="ng_tipo['+dados.linha+']"	id="ng_tipo_'+dados.linha+'"		class="tipo">' + options + '</select></td>' +
				'<td><input tabindex="1" type="radio"	name="ng_chave_pk"						value="'+this.pk+'"			class="pk" /></td>' +
				'<td><input tabindex="1" type="checkbox" name="ng_nn['+dados.linha+']"			value="'+this.nn+'"			class="nn" /></td>' +
				'<td><input tabindex="1" type="checkbox" name="ng_uk['+dados.linha+']"			value="'+this.uk+'"			class="uk" /></td>' +
				'<td><input tabindex="1" type="checkbox" name="ng_fk['+dados.linha+']"			value="'+this.fk+'"			class="fk" /></td>' +
				'<td><input tabindex="1" type="text"		name="ng_dominio['+dados.linha+']"		value="'+this.dominio+'"	class="dominio" /></td>' +
				'<td><input tabindex="1" type="hidden"	name="ng_associativa['+dados.linha+']"	value="'+this.classe+'"		class="associativa" id="ng_associativa_'+dados.linha+'" /></td>' +
				'<td><input tabindex="1" type="hidden"	name="ng_metodo['+dados.linha+']"		value="'+this.metodo+'"		class="metodo" id="ng_metodo_'+dados.linha+'" /></td>' +
			'</tr>';
		$('#neg tr:last').after(template);
	}
}
function persistente(){
	this.adicionar = function (campo,ordem,tipoOrdem,tabelaReferencia,campoReferencia){
		this.campo = campo || '';
		this.ordem = ordem || '';
		this.tipoOrdem = tipoOrdem || '';
		this.tabelaReferencia = tabelaReferencia || '';
		this.campoReferencia = campoReferencia || '';
		linha = '#linhaPer' + dados.linha;
		template =
			'<tr class="ln_'+ dados.linha +'" >' +
				'<td  class="propriedade" ></td>' +
				'<td><input tabindex="1" type="text"		name="bd_campo['+dados.linha+']"				value="'+this.campo+'"				class="campo" /></td>' +
				'<td><input tabindex="1" type="text"		name="bd_ordem['+dados.linha+']"				value="'+this.ordem+'"				class=ordem"" size="4"/></td>' +
				'<td><input tabindex="1" type="checkbox" name="bd_tipo_ordem['+dados.linha+']"		value="'+this.tipoOrdem+'"			class="tipoOrdem" /></td>' +
				'<td><input tabindex="1" type="hidden"	name="bd_referencia_tabela['+dados.linha+']"	value="'+this.tabelaReferencia+'"	class="tabelaReferencia" id="bd_referencia_tabela_'+dados.linha+'" /></td>' +
				'<td><input tabindex="1" type="hidden"	name="bd_referencia_campo['+dados.linha+']"	value="'+this.campoReferencia+'"	class="campoReferencia" id="bd_referencia_campo_'+dados.linha+'" /></td>' +
			'</tr>';
		$('#per tr:last').after(template);
	}
}
function visualizacao(){
	this.adicionar = function (componente,ordem,ordemDescritivo,largura){
		this.componente = componente || '';
		this.ordem = ordem || '';
		this.ordemDescritivo = ordemDescritivo || '';
		this.largura = largura || '';
		options = '';
		for(i in dados.componentes){
			selected = (componente == dados.componentes[i][0]) ? 'selected="selected"' : '';
			options +='<option value="' + dados.componentes[i][0] + '" ' + selected + '>' + dados.componentes[i][1] + '</option>';
		}
		linha = '#linhaVis' + dados.linha;
		template =
			'<tr class="ln_'+ dados.linha +'" >' +
				'<td  class="propriedade" ></td>' +
				'<td><select tabindex="1" name="vi_componente['+ dados.linha +']" id="vi_componente_'+ dados.linha +'" >' + options + '</select></td>' +
				'<td><input tabindex="1" name="vi_ordem['+ dados.linha +']"	value="'+this.ordem+'" size="4" /></td>' +
				'<td><input tabindex="1" name="vi_ordemDescritivo['+ dados.linha +']"	value="'+this.ordemDescritivo+'" size="4" /></td>' +
				'<td><input tabindex="1" name="vi_largura['+ dados.linha +']"	value="'+this.largura+'" size="4" /></td>' +
			'</tr>';
		$('#vis tr:last').after(template);
	}
}
function remover(linha){
	$.entidade.remover(linha);
}
$.entidade = new entidade;
$.negocio = new negocio;
$.persistente = new persistente;
$.visualizacao = new visualizacao;
$(document).ready( function() {
	$('#adicionar').click(function(){
		$.entidade.adicionar();
		$.negocio.adicionar();
		$.persistente.adicionar();
		$.visualizacao.adicionar();
	});
	$('#entidade').change(function(){
		$('.arquivo').remove();
		if($(this).val()){
			nome = lowerCamelCase($('#entidade').val());
			nomeClasse = upperCamelCase($('#entidade').val());
			arquivos = new Array(
				 nome + '/classes/C' + nomeClasse + '_excluir.php',
				 nome + '/classes/C' + nomeClasse + '_gravar.php',
				 nome + '/classes/C' + nomeClasse + '_mudarPagina.php',
				 nome + '/classes/C' + nomeClasse + '_pesquisar.php',
				 nome + '/classes/C' + nomeClasse + '_verEdicao.php',
				 nome + '/classes/C' + nomeClasse + '_verPesquisa.php',
				 nome + '/classes/I' + nomeClasse + '.php',
				 nome + '/classes/N' + nomeClasse + '.php',
				 nome + '/classes/P' + nomeClasse + '.mysql.php',
				 nome + '/classes/P' + nomeClasse + '.postgres.php',
				 nome + '/xml/entidade.xml',
				 nome + '/xml/pt_BR.xml',
				 nome + '/html/C' + nomeClasse + '_verEdicao.html',
				 nome + '/html/C' + nomeClasse + '_verPesquisa.html'
			);
			for(i in arquivos){
				arquivo = arquivos[i];
				template =
					'<tr class="arquivo" >' +
						'<td><input tabindex="1" type="checkbox" value="'+arquivo+'"/></td>' +
						'<td>'+ arquivo +'"</td>' +
					'</tr>';
				$('#arq tr:last').after(template);
			}
		}
	});
	$('#sugerirNomeTabela').click(function(){
		if($('#entidade').val()) $('#nomeTabela').val(RetiraAcentos(str_replace(' ','_',$('#entidade').val().toLowerCase())));
	});
	$('#sugerirNomeSequence').click(function(){
		if($('#entidade').val()) $('#nomeSequence').val(RetiraAcentos(str_replace(' ','_','sq_' + $('#entidade').val().toLowerCase())));
	});
	$('#sugerirNomesCampos').click(function(){
		$('.nome').each(function(){
			if($(this).val()) $('.ln_'+$(this).parent().parent().attr('class').split('_')[1]+' .campo').val(RetiraAcentos(str_replace(' ','_',$(this).val().toLowerCase())));
		});
	});
	$('#sugerirNomesPropriedades').click(function(){
		$('.nome').each(function(){
			if($(this).val()) $('.ln_'+$(this).parent().parent().attr('class').split('_')[1]+' .propriedade').val(lowerCamelCase($(this).val()));
		});
	});
	$('.nome').live('change',function(){
		linha = $(this).parent().parent().attr('class').split('_')[1];
		passarNome(linha,$(this).val());
		if(!$('.ln_' + linha + ' .abreviado').val()) $('.ln_' + linha + ' .abreviado').val($(this).val());
	});
	$('.pk').live('click',function(){
		$('.ln_' + $(this).parent().parent().attr('class').split('_')[1] + ' .tipo').val('numerico');
	});
	$('.tipo').live('change',function(){
		linha = $(this).parent().parent().attr('class').split('_')[1];
		componente = $('#vi_componente_'+linha);
		negocio = $('#ng_tipo_' + linha);
		valor = null;
		switch(negocio.val()){
			case('texto'):
				valor = 'caixa de entrada';
			break;
			case('numerico'):
				valor = 'caixa de entrada';
			break;
			case('data'):
				valor = 'data';
			break;
			case('tdocumentopessoal'):
				valor = 'documento pessoal';
			break;
			case('tcnpj'):
				valor = 'documento pessoal';
			break;
			case('tcep'):
				valor = 'cep';
			break;
			case('ttelefone'):
				valor = 'telefone';
			break;
			case('tnumerico'):
				valor = 'numerico';
			break;
			case('tmoeda'):
				valor = 'moeda';
			break;
		}
		componente.val(valor);
	});
	$('.fk').live('click',function(){
		try{
			linha = $(this).parent().parent().attr('class').split('_')[1];
			if($(this).attr('checked')){
				document.getElementById('ng_associativa_' + linha).type = 'text';
				document.getElementById('ng_metodo_' + linha).type = 'text';
				document.getElementById('bd_referencia_tabela_' + linha).type = 'text';
				document.getElementById('bd_referencia_campo_' + linha).type = 'text';
			}else{
				document.getElementById('ng_associativa_' + linha).type = 'hidden';
				document.getElementById('ng_metodo_' + linha).type = 'hidden';
				document.getElementById('bd_referencia_tabela_' + linha).type = 'hidden';
				document.getElementById('bd_referencia_campo_' + linha).type = 'hidden';
			}
		}catch(e){console.log(e)}
	});
	if(definicao) preencherTela(definicao[0]);
});
$(function() {
	$("#tabs").tabs();
});
function passarNome(linha,valor){
	$('.ln_' + linha + ' .propriedade').html(valor);
}
function preencherTela(definicao){
	try{
		$('#entidade').val(definicao.inter.nome);
		$('#nomeTabela').val(definicao.bd.nomeTabela);
		$('#nomeSequence').val(definicao.bd.nomeSequencia);
		for(i in definicao.entidade){
			valores = definicao.entidade[i];
			fk = false;
			classe = '';
			metodo = '';
			if(valores.negocio.classeAssociativa){
				fk = true;
				classe = valores.negocio.classeAssociativa;
				metodo = valores.negocio.metodoLeitura;
			}
			tabela = '';
			campo = '';
			if(valores.persistente.chaveEstrangeira){
				tabela = valores.persistente.chaveEstrangeira.tabela;
				campo = valores.persistente.chaveEstrangeira.campo;
			}
			ordem = '';
			largura = '';
			if(valores.controle.listagem){
				ordem = valores.controle.ordem;
				largura = LimpaNumero(valores.controle.largura);
			}
			$.entidade.adicionar(
				valores.inter.nome,
				valores.inter.abreviacao,
				valores.inter.descricao
			);
			$.negocio.adicionar(
				valores.negocio.propriedade,
				valores.persistente.tamanho,
				valores.persistente.tipo,
				(valores.negocio.campo == definicao.bd.chavePrimaria),
				(valores.negocio.obrigatorio ? true : false),
				fk,
				valores.inter.dominio,
				classe,
				metodo
			);
			$.persistente.adicionar(
				valores.negocio.campo,
				valores.persistente.ordem,
				valores.persistente.tipoOrdem,
				tabela,
				campo
			);
			$.visualizacao.adicionar(
				valores.controle.componente,
				ordem,
				valores.negocio.descritivo,
				largura
			);
			passarNome(dados.linha, valores.inter.nome);
		}
	}
	catch(e){
		alert(e);
	}
}
