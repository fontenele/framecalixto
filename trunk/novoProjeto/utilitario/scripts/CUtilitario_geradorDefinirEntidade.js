gerador = {
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
		['caixa de entrada',	'Entrada (input)'					],
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
	],
	pegarLinha: function(obj){
		return obj.parent().parent().attr('class').split('_')[1];
	},
	sugerirComponente: function(obj){
		linha = this.pegarLinha(obj);
		if($('input[name="ng_chave_pk"]:checked').val() == linha) return 'oculto';
		if($('.ln_'+linha+' .dominio').val()) return 'caixa de combinacao';
		if($('.ln_'+linha+' .fk').val()) return 'caixa de combinacao';
		switch(obj.val()){
			case('texto'):				return 'caixa de entrada';
			case('numerico'):			return 'caixa de entrada';
			case('data'):				return 'data';
			case('tdocumentopessoal'):	return 'documento pessoal';
			case('tcnpj'):				return 'documento pessoal';
			case('tcep'):				return 'cep';
			case('ttelefone'):			return 'telefone';
			case('tnumerico'):			return 'numerico';
			case('tmoeda'):				return 'moeda';
		}
	},
	adicionarLinha:function(valores,chave){
		valores = valores || '';
		chave = chave || '';
		this.linha++;
		if(valores){
			this.adicionarEntidade(
				valores.inter.nome,
				valores.inter.abreviacao,
				valores.inter.descricao
			);
			this.adicionarNegocio(
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
			this.adicionarPersistente(
				valores.negocio.campo,
				valores.persistente.ordem,
				valores.persistente.tipoOrdem,
				(valores.persistente.chaveEstrangeira ? valores.persistente.chaveEstrangeira.tabela : ''),
				(valores.persistente.chaveEstrangeira ? valores.persistente.chaveEstrangeira.campo : '')
			);
			this.adicionarVisualizacao(
				valores.controle.componente,
				(valores.controle.listagem ? valores.controle.ordem : '' ),
				valores.negocio.descritivo,
				(valores.controle.listagem ? valores.controle.largura.strReplace('%','') : '' )
			);
		}else{
			this.adicionarEntidade();
			this.adicionarNegocio();
			this.adicionarPersistente();
			this.adicionarVisualizacao();
		}
	},
	passarNome:function (obj){
		$('.ln_' + this.pegarLinha(obj) + ' .propriedade').html(obj.val());
	},
	remover:function(linha){
		$('.ln_' + linha).remove();
	},
	adicionarEntidade:function(nome,abreviado,descricao){
		nome = nome || '';
		abreviado = abreviado || '';
		descricao = descricao || '';
		template =
			'<tr class="ln_'+ this.linha +'" >' +
				'<td><input tabindex="1" type="text" name="en_nome['+ this.linha +']"		id="en_nome_'+ this.linha +'"			value="'+nome+'"		class="nome" /></td>' +
				'<td><input tabindex="1" type="text" name="en_abreviacao['+ this.linha +']"	id="en_abreviacao_'+ this.linha +'"		value="'+abreviado+'"	class="abreviado" /></td>' +
				'<td><input tabindex="1" type="text" name="en_descricao['+ this.linha +']"	id="en_descicao_'+ this.linha +'"		value="'+descricao+'"	class="descricao" /></td>' +
				'<td><a tabindex="1" href="javascript:gerador.remover('+ this.linha +');">remover</a></td>' +
			'</tr>';
		$('#ent tr:last').after(template);
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
		metodo = metodo || '';
		options = '';
		for(i in this.tipos){
			selected = (tipo == this.tipos[i][0]) ? 'selected="selected"' : '';
			options +='<option value="' + this.tipos[i][0] + '" ' + selected + '>' + this.tipos[i][1] + '</option>';
		}
		type = (fk ? 'text' : 'hidden');
		template =
			'<tr class="ln_'+ this.linha +'" >' +
				'<td  class="propriedade" ></td>' +
				'<td><input tabindex="1" type="text"		name="ng_nome['+this.linha+']"			value="'+propriedade+'"	class="propriedade" /></td>' +
				'<td><input tabindex="1" type="text"		name="ng_tamanho['+this.linha+']"		value="'+tamanho+'"		class="tamanho" size="4" /></td>' +
				'<td><select tabindex="1"					name="ng_tipo['+this.linha+']"	id="ng_tipo_'+this.linha+'"		class="tipo">' + options + '</select></td>' +
				'<td><input tabindex="1" type="radio"		name="ng_chave_pk"						value="'+this.linha+'"	class="pk" '+ (pk ? 'checked="checked"' : '')  +' /></td>' +
				'<td><input tabindex="1" type="checkbox"	name="ng_nn['+this.linha+']"			value="'+nn+'"			class="nn" '+ (nn ? 'checked="checked"' : '')  +' /></td>' +
				'<td><input tabindex="1" type="checkbox"	name="ng_uk['+this.linha+']"			value="'+uk+'"			class="uk" '+ (uk ? 'checked="checked"' : '')  +' /></td>' +
				'<td><input tabindex="1" type="checkbox"	name="ng_fk['+this.linha+']"			value="'+fk+'"			class="fk" '+ (fk ? 'checked="checked"' : '')  +' /></td>' +
				'<td><input tabindex="1" type="text"		name="ng_dominio['+this.linha+']"		value="'+dominio+'"		class="dominio" /></td>' +
				'<td><input tabindex="1" type="'+ type  +'"	name="ng_associativa['+this.linha+']"	value="'+classe+'"		class="associativa" id="ng_associativa_'+this.linha+'" /></td>' +
				'<td><input tabindex="1" type="'+ type  +'"	name="ng_metodo['+this.linha+']"		value="'+metodo+'"		class="metodo" id="ng_metodo_'+this.linha+'" /></td>' +
			'</tr>';
		$('#neg tr:last').after(template);
	},
	adicionarPersistente:function (campo,ordem,tipoOrdem,tabelaReferencia,campoReferencia){
		campo = campo || '';
		ordem = ordem || '';
		tipoOrdem = tipoOrdem || '';
		tabelaReferencia = tabelaReferencia || '';
		campoReferencia = campoReferencia || '';
		type = (tabelaReferencia ? 'text' : 'hidden');
		template =
			'<tr class="ln_'+ this.linha +'" >' +
				'<td  class="propriedade" ></td>' +
				'<td><input tabindex="1" type="text"		name="bd_campo['+this.linha+']"				value="'+campo+'"				class="campo" /></td>' +
				'<td><input tabindex="1" type="text"		name="bd_ordem['+this.linha+']"				value="'+ordem+'"				class=ordem"" size="4"/></td>' +
				'<td><input tabindex="1" type="checkbox"	name="bd_tipo_ordem['+this.linha+']"		value="'+tipoOrdem+'"			class="tipoOrdem" /></td>' +
				'<td><input tabindex="1" type="'+ type  +'"	name="bd_referencia_tabela['+this.linha+']"	value="'+tabelaReferencia+'"	class="tabelaReferencia" id="bd_referencia_tabela_'+this.linha+'" /></td>' +
				'<td><input tabindex="1" type="'+ type  +'"	name="bd_referencia_campo['+this.linha+']"	value="'+campoReferencia+'"		class="campoReferencia" id="bd_referencia_campo_'+this.linha+'" /></td>' +
			'</tr>';
		$('#per tr:last').after(template);
	},
	adicionarVisualizacao:function (componente,ordem,ordemDescritivo,largura){
		componente = componente || '';
		ordem = ordem || '';
		ordemDescritivo = ordemDescritivo || '';
		largura = largura || '';
		options = '';
		for(i in this.componentes){
			selected = (componente == this.componentes[i][0]) ? 'selected="selected"' : '';
			options +='<option value="' + this.componentes[i][0] + '" ' + selected + '>' + this.componentes[i][1] + '</option>';
		}
		template =
			'<tr class="ln_'+ this.linha +'" >' +
				'<td  class="propriedade" ></td>' +
				'<td><select tabindex="1" name="vi_componente['+ this.linha +']" id="vi_componente_'+ this.linha +'" class="viComponente">' + options + '</select></td>' +
				'<td><input tabindex="1" name="vi_ordem['+ this.linha +']"	value="'+ordem+'" size="4" /></td>' +
				'<td><input tabindex="1" name="vi_ordemDescritivo['+ this.linha +']"	value="'+ordemDescritivo+'" size="4" /></td>' +
				'<td><input tabindex="1" name="vi_largura['+ this.linha +']"	value="'+largura+'" size="4" /></td>' +
			'</tr>';
		$('#vis tr:last').after(template);
	},
	preencherTela:function(definicao){
		try{
			$('#entidade').val(definicao.inter.nome);
			$('#nomeTabela').val(definicao.bd.nomeTabela);
			$('#nomeSequence').val(definicao.bd.nomeSequencia);
			$('#entidade').trigger('change',false);
			for(i in definicao.entidade){
				valores = definicao.entidade[i];
				this.adicionarLinha(valores,definicao.bd.chavePrimaria);
				this.passarNome($('#en_nome_'+this.linha), valores.inter.nome);
			}
		}
		catch(e){
			alert(e.message);
		}
	}
};
$(document).ready( function() {
	$('input').live('keyup',function(event){
    		if(event.originalEvent.ctrlKey)	switch(event.originalEvent.keyCode){
    			case(38): $(this).parent().parent().prev().find('.'+$(this).attr('class')).focus().select();break; //Acima
    			case(40): $(this).parent().parent().next().find('.'+$(this).attr('class')).focus().select();break; //Abaixo
    			case(39): if($(this).parent().next().children()[0]) $(this).parent().next().children()[0].focus().select();break; //Direita
    			case(37): if($(this).parent().prev().children()[0]) $(this).parent().prev().children()[0].focus().select();break; //Esquerda
    		}
   			return true;
    	});
	$('#adicionar').click(function(){
		gerador.adicionarLinha();
	});
	$('#entidade').change(function(){
		$('.arquivo').remove();
		if($('#entidade').val()){
			nome = $('#entidade').val().lowerCamelCase();
			nomeClasse = $('#entidade').val().upperCamelCase();
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
				 nome + '/classes/P' + nomeClasse + '.oracle.php',
				 nome + '/xml/entidade.xml',
				 nome + '/xml/pt_BR.xml',
				 nome + '/html/C' + nomeClasse + '_verEdicao.html',
				 nome + '/html/C' + nomeClasse + '_verPesquisa.html'
			);
			for(i in arquivos){
				arquivo = arquivos[i];
				template =
					'<tr class="arquivo" >' +
						'<td><input tabindex="1" type="checkbox" value="'+arquivo+'" name="arquivo['+arquivo+']"/></td>' +
						'<td>'+ arquivo +'"</td>' +
					'</tr>';
				$('#arq tr:last').after(template);
			}
		}
	});
	$('#sugerirNomeTabela').click(function(){
		if($('#entidade').val()) $('#nomeTabela').val($('#entidade').val().toLowerCase().strReplace(' ','_').retiraAcentos());
	});
	$('#sugerirNomeSequence').click(function(){
		if($('#entidade').val()) $('#nomeSequence').val('sq_' + $('#entidade').val().toLowerCase().strReplace(' ','_').retiraAcentos());
	});
	$('#sugerirNomesCampos').click(function(){
		$('.nome').each(function(){
			if($(this).val()) $('.ln_'+gerador.pegarLinha($(this))+' .campo').val($(this).val().toLowerCase().strReplace(' ','_').retiraAcentos());
		});
	});
	$('#sugerirNomesPropriedades').click(function(){
		$('.nome').each(function(){
			if($(this).val()) $('.ln_'+gerador.pegarLinha($(this))+' .propriedade').val($(this).val().lowerCamelCase());
		});
	});
	$('#gerarArquivos').click(function(){
		$('.arquivo input[type="checkbox"]').attr('checked','checked');
	});
	$('#sugerirComponentes').click(function(){
		$('.tipo').each(function(){
			if($(this).val()) $('.ln_'+gerador.pegarLinha($(this))+' .viComponente').val(gerador.sugerirComponente($(this)));
		});
	});
	$('.nome').live('change',function(){
		gerador.passarNome($(this));
		linha = gerador.pegarLinha($(this));
		if(!$('.ln_' + linha + ' .abreviado').val()) $('.ln_' + linha + ' .abreviado').val($(this).val());
	});
	$('.pk').live('click',function(){
		$('.ln_' + $(this).parent().parent().attr('class').split('_')[1] + ' .tipo').val('numerico');
	});
	$('.fk').live('click',function(){
		try{
			linha = gerador.pegarLinha($(this));
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
		}catch(e){alert(e.message)}
	});
	$('#affForm').submit(function(){
		return confirm("Are you sure?");
	});
	if(definicao) gerador.preencherTela(definicao[0]);
});
$(function() {
	$("#tabs").tabs();
});
