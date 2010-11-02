<?php
/**
* Classe de controle
* Ver o Usuário
* @package Sistema
* @subpackage Utilitario
*/
class CUtilitario_geradorGerarFonte extends controle{
	public static $nomeEntidade;
	public static $nomeNegocio;
	public static $nomeTabela;
	public static $nomeSequence;
	public static $entidade;
	protected static $debug = false;
	
	/**
	* Método inicial do controle
	*/
	public function inicial(){
		$this->passarProximoControle(definicaoEntidade::controle($this,'geradorDefinirEntidade'));
		CUtilitario_geradorGerarFonte::gerarFonte($this->visualizacao,$_POST);
	}
	public static function gerarFonte(visualizacao $visualizacao,$dadosGerador){
		CUtilitario_geradorGerarFonte::$entidade = $dadosGerador;
		CUtilitario_geradorGerarFonte::$entidade['ng_nome'] = array_map('caracteres::RetiraAcentos',CUtilitario_geradorGerarFonte::$entidade['ng_nome']);
		CUtilitario_geradorGerarFonte::$entidade['bd_campo'] = array_map('caracteres::RetiraAcentos',CUtilitario_geradorGerarFonte::$entidade['bd_campo']);
		$arNome = explode(' ',strtolower(caracteres::RetiraAcentos(CUtilitario_geradorGerarFonte::$entidade['entidade'])));
		$nome = array_shift($arNome);
		$arNome = array_map("ucFirst", $arNome) ;
		array_unshift($arNome,$nome);
		CUtilitario_geradorGerarFonte::$nomeEntidade = implode('',$arNome);
		CUtilitario_geradorGerarFonte::$nomeNegocio = 'N'.ucFirst(CUtilitario_geradorGerarFonte::$nomeEntidade);
		CUtilitario_geradorGerarFonte::$nomeTabela = caracteres::RetiraAcentos(CUtilitario_geradorGerarFonte::$entidade['nomeTabela']);
		CUtilitario_geradorGerarFonte::$nomeSequence = caracteres::RetiraAcentos(CUtilitario_geradorGerarFonte::$entidade['nomeSequence'] ? CUtilitario_geradorGerarFonte::$entidade['nomeSequence'] : "sq_{CUtilitario_geradorGerarFonte::$nomeTabela}");
		if(!is_dir(CUtilitario_geradorGerarFonte::$nomeEntidade))
			mkdir(CUtilitario_geradorGerarFonte::$nomeEntidade,0777);
		chmod(CUtilitario_geradorGerarFonte::$nomeEntidade,2777);
		if(!is_dir(CUtilitario_geradorGerarFonte::$nomeEntidade."/classes"))
			mkdir(CUtilitario_geradorGerarFonte::$nomeEntidade."/classes",0777);
		chmod(CUtilitario_geradorGerarFonte::$nomeEntidade."/classes",2777);
		if(!is_dir(CUtilitario_geradorGerarFonte::$nomeEntidade."/xml"))
			mkdir(CUtilitario_geradorGerarFonte::$nomeEntidade."/xml",0777);
		chmod(CUtilitario_geradorGerarFonte::$nomeEntidade."/xml",2777);
		if(!is_dir(CUtilitario_geradorGerarFonte::$nomeEntidade."/html"))
			mkdir(CUtilitario_geradorGerarFonte::$nomeEntidade."/html",0777);
		chmod(CUtilitario_geradorGerarFonte::$nomeEntidade."/html",2777);
		umask(0111);
		$visualizacao->entidade = CUtilitario_geradorGerarFonte::$entidade['entidade'];
		$visualizacao->pacote = CUtilitario_geradorGerarFonte::$entidade['entidade'];
		$visualizacao->classe = 'class';
		CUtilitario_geradorGerarFonte::montarArquivoDefinicaoXML($visualizacao);
		CUtilitario_geradorGerarFonte::montarArquivoInternacionalizacaoXML($visualizacao);
		CUtilitario_geradorGerarFonte::montarPersistente($visualizacao);
		CUtilitario_geradorGerarFonte::montarNegocio($visualizacao);
		CUtilitario_geradorGerarFonte::montarInternacionalizacao($visualizacao);
		CUtilitario_geradorGerarFonte::montarControleExcluir($visualizacao);
		CUtilitario_geradorGerarFonte::montarControleGravar($visualizacao);
		//CUtilitario_geradorGerarFonte::montarControleMudarPagina($visualizacao);
		//CUtilitario_geradorGerarFonte::montarControlePesquisar($visualizacao);
		CUtilitario_geradorGerarFonte::montarControleVerEdicao($visualizacao);
		CUtilitario_geradorGerarFonte::montarControleVerPesquisa($visualizacao);
		CUtilitario_geradorGerarFonte::montarTemplateVerEdicao($visualizacao);
		CUtilitario_geradorGerarFonte::montarTemplateVerPesquisa($visualizacao);
		exec("chmod -R 777 ".CUtilitario_geradorGerarFonte::$nomeEntidade);
		if(CUtilitario_geradorGerarFonte::$debug){die;}
		if(isset(CUtilitario_geradorGerarFonte::$entidade['recriarBase'])){
			$persistente = definicaoEntidade::persistente(CUtilitario_geradorGerarFonte::$nomeNegocio);
			$conexao = conexao::criar();
			$obPersistente = new $persistente($conexao);
			$obPersistente->recriar();
		}
	}
	/**
	* Escreve o arquivo com o conteudo passado
	* @param string caminho do arquivo a ser escrito
	* @param string conteudo do arquivo a ser escrito
	*/
	protected static function escreverArquivo($caminho,$conteudo){
		$caminho = caracteres::RetiraAcentos($caminho);
		if(!isset(CUtilitario_geradorGerarFonte::$entidade['arquivo'][$caminho])) return ;
		if(CUtilitario_geradorGerarFonte::$debug){
			echo "<br /><br /><br />No arquivo: {$caminho}<br /><br />";
			highlight_string($conteudo);
			return ;
		}
		$handle = fopen ($caminho, "w");
		fwrite($handle, $conteudo);
		fclose($handle);
		chmod($caminho,0777);
	}
	/**
	* Monta o conteúdo do arquivo de definção XML
	*/
	public static function montarArquivoDefinicaoXML(){
		$tabela = " nomeBanco='".CUtilitario_geradorGerarFonte::$nomeTabela."'";
		$sequence = " nomeSequencia='".CUtilitario_geradorGerarFonte::$nomeSequence."'";
		$xml = "<?xml version='1.0' encoding='utf-8' ?>\n";
		$xml.= "<entidade {$tabela}{$sequence}>\n";
		$xml.= "\t<propriedades>\n";
		
		foreach(CUtilitario_geradorGerarFonte::$entidade['ng_nome'] as $index => $nomePropriedadeNegocio){
			$id = "id='".CUtilitario_geradorGerarFonte::$entidade['ng_nome'][$index]."' ";
			$tipo= "tipo='".CUtilitario_geradorGerarFonte::$entidade['ng_tipo'][$index]."' ";
			$tamanho = (CUtilitario_geradorGerarFonte::$entidade['ng_tamanho'][$index]) ? "tamanho='".CUtilitario_geradorGerarFonte::$entidade['ng_tamanho'][$index]."' " : '' ;
			$obrigatorio = isset(CUtilitario_geradorGerarFonte::$entidade['ng_nn'][$index]) ? "obrigatorio='sim' " : '' ;
			$chaveUnica = isset(CUtilitario_geradorGerarFonte::$entidade['????'][$index]) ? "indiceUnico='sim' " : '' ;
			$nomeBanco = isset(CUtilitario_geradorGerarFonte::$entidade['bd_campo'][$index]) ? "nome='".CUtilitario_geradorGerarFonte::$entidade['bd_campo'][$index]."' " : '';
			$componente = isset(CUtilitario_geradorGerarFonte::$entidade['vi_componente'][$index]) ? "componente='".CUtilitario_geradorGerarFonte::$entidade['vi_componente'][$index]."' ":'';
			$largura = isset(CUtilitario_geradorGerarFonte::$entidade['vi_largura'][$index]) ? "tamanho='".CUtilitario_geradorGerarFonte::$entidade['vi_largura'][$index]."%' ":'';
			$link = isset(CUtilitario_geradorGerarFonte::$entidade['vi_link'][$index]) ? "hyperlink='sim' ":'';
			$chavePrimaria = (CUtilitario_geradorGerarFonte::$entidade['ng_chave_pk'] == $index)  ? "indicePrimario='sim' " : '';
			$ordenacao = (CUtilitario_geradorGerarFonte::$entidade['bd_ordem'][$index])? "ordem='".CUtilitario_geradorGerarFonte::$entidade['bd_ordem'][$index]."' " : '' ;
			$tipoOrdenacao = isset(CUtilitario_geradorGerarFonte::$entidade['bd_tipo_ordem'][$index]) ? "tipoOrdem='inversa' " : '';
			$descritivo = (CUtilitario_geradorGerarFonte::$entidade['vi_ordemDescritivo'][$index])? "descritivo='".CUtilitario_geradorGerarFonte::$entidade['vi_ordemDescritivo'][$index]."' " : '' ;
			$classeAssociativa = '';
			$metodoLeitura = '';
			if(strpos(CUtilitario_geradorGerarFonte::$entidade['ng_dominio_associativa'][$index], '[') === false){
				if((isset(CUtilitario_geradorGerarFonte::$entidade['ng_fk'][$index]))){
					$cl = explode('::',CUtilitario_geradorGerarFonte::$entidade['ng_dominio_associativa'][$index]);
					$classeAssociativa = "classeAssociativa='{$cl[0]}' ";
					$metodoLeitura = (isset($cl[1])) ? "metodoLeitura='{$cl[1]}' " : '';
				}
			}
			$xml.= "\t\t<propriedade {$id}{$tipo}{$tamanho}{$obrigatorio}{$chavePrimaria}{$chaveUnica}{$classeAssociativa}{$metodoLeitura}{$descritivo} >\n";
			if(($dominioAssociativa = CUtilitario_geradorGerarFonte::$entidade['ng_dominio_associativa'][$index])){
				if(strpos($dominioAssociativa, '[') !== false){
					$arDominio = explode('][',substr($dominioAssociativa,1,strlen($dominioAssociativa) -2));
					$xml.="\t\t\t<dominio>\n";
					foreach($arDominio as $item){
						$item = explode(',',$item);
						$xml.="\t\t\t\t<opcao id='{$item[0]}' />\n";
					}
					$xml.="\t\t\t</dominio>\n";
				}
			}
			if(isset(CUtilitario_geradorGerarFonte::$entidade['ng_fk'][$index])){
				$xml.= "\t\t\t<banco {$nomeBanco}{$ordenacao}{$tipoOrdenacao}>\n";
				$xml.= "\t\t\t\t<chaveEstrangeira tabela='".CUtilitario_geradorGerarFonte::$entidade['bd_referencia_tabela'][$index]."' campo='".CUtilitario_geradorGerarFonte::$entidade['bd_referencia_campo'][$index]."' />\n";
				$xml.= "\t\t\t</banco>\n";
			}else{
				$xml.= "\t\t\t<banco {$nomeBanco}{$ordenacao}{$tipoOrdenacao} />\n";
			}
			if(CUtilitario_geradorGerarFonte::$entidade['vi_ordem'][$index]){
				$ordem = "ordem='".CUtilitario_geradorGerarFonte::$entidade['vi_ordem'][$index]."' ";
				$xml.= "\t\t\t<apresentacao {$componente}>\n";
				$xml.= "\t\t\t\t<listagem {$ordem}{$largura}{$link}/>\n";
				$xml.= "\t\t\t</apresentacao>\n";
			}else{
				$xml.= "\t\t\t<apresentacao {$componente} />\n";
			}
			$xml.= "\t\t</propriedade>\n";
		}
		$xml.= "\t</propriedades>\n";
		$xml.= "</entidade>";
		CUtilitario_geradorGerarFonte::escreverArquivo(CUtilitario_geradorGerarFonte::$nomeEntidade."/xml/entidade.xml",$xml);
	}
	/**
	* Monta o conteúdo do arquivo de definção XML
	*/
	public static function montarArquivoInternacionalizacaoXML(){
		$xml = "<?xml version='1.0' encoding='utf-8' ?>\n";
		$xml.= "<internacionalizacao>\n";
		$xml.= "\t<entidade>\n";
		$xml.= "\t\t<nome>".CUtilitario_geradorGerarFonte::$entidade['entidade']."</nome>\n";
		$xml.= "\t\t<propriedades>\n";
		foreach(CUtilitario_geradorGerarFonte::$entidade['ng_nome'] as $index => $nomePropriedadeNegocio){
			$xml.= "\t\t<propriedade nome='{$nomePropriedadeNegocio}'>\n";
			$xml.= "\t\t\t<nome>".CUtilitario_geradorGerarFonte::$entidade['en_nome'][$index]."</nome>\n";
			$xml.= "\t\t\t<abreviacao>".CUtilitario_geradorGerarFonte::$entidade['en_abreviacao'][$index]."</abreviacao>\n";
			$xml.= "\t\t\t<descricao>".CUtilitario_geradorGerarFonte::$entidade['en_descricao'][$index]."</descricao>\n";
			if(strpos($stDominio = CUtilitario_geradorGerarFonte::$entidade['ng_dominio_associativa'][$index], '[') !== false){
					$arDominio = explode('][',substr($stDominio,1,strlen($stDominio) -2));
					$xml.="\t\t\t<dominio>\n";
					foreach($arDominio as $item){
						$item = explode(',',$item);
						$xml.="\t\t\t\t<opcao id='{$item[0]}'>{$item[1]}</opcao>\n";
					}
					$xml.="\t\t\t</dominio>\n";
			}
			$xml.= "\t\t</propriedade>\n";
		}
		$xml.= "\t\t</propriedades>\n";
		$xml.= "\t</entidade>\n";
		$xml.= "\t<controles>\n";
		$xml.= "\t\t<titulo>Cadastro de ".CUtilitario_geradorGerarFonte::$entidade['entidade']."</titulo>\n";
		$xml.= "\t</controles>\n";
		$xml.= "</internacionalizacao>\n";
		CUtilitario_geradorGerarFonte::escreverArquivo(CUtilitario_geradorGerarFonte::$nomeEntidade."/xml/pt_BR.xml", $xml);
	}
	/**
	* Monta as classes persistentes
	*/
	public static function montarPersistente(visualizacao $visualizacao){
		$persistente = definicaoEntidade::persistente(CUtilitario_geradorGerarFonte::$nomeNegocio);
		$visualizacao->persistenteNome = $persistente;
		$visualizacao->persistentePai = 'persistentePadraoPG';
		CUtilitario_geradorGerarFonte::escreverArquivo(CUtilitario_geradorGerarFonte::$nomeEntidade."/classes/{$persistente}.postgres.php",$visualizacao->pegar('classesPersistente.html'));
		$visualizacao->persistentePai = 'persistentePadraoMySql';
		CUtilitario_geradorGerarFonte::escreverArquivo(CUtilitario_geradorGerarFonte::$nomeEntidade."/classes/{$persistente}.mysql.php",$visualizacao->pegar('classesPersistente.html'));
		$visualizacao->persistentePai = 'persistentePadraoOCI';
		CUtilitario_geradorGerarFonte::escreverArquivo(CUtilitario_geradorGerarFonte::$nomeEntidade."/classes/{$persistente}.oracle.php",$visualizacao->pegar('classesPersistente.html'));
		$visualizacao->persistentePai = 'persistentePadraoSqlite';
		CUtilitario_geradorGerarFonte::escreverArquivo(CUtilitario_geradorGerarFonte::$nomeEntidade."/classes/{$persistente}.sqlite.php",$visualizacao->pegar('classesPersistente.html'));
	}
	/**
	* Monta a classe de negocio
	*/
	public static function montarNegocio(visualizacao $visualizacao){
		$arTipos['texto'] = 'string';
		$arTipos['numerico'] = 'integer';
		$arTipos['data'] = 'TData';
		$arTipos['tdocumentopessoal'] = 'TDocumentoPessoal';
		$arTipos['tcnpj'] = 'TCnpj';
		$arTipos['tcep'] = 'TCep';
		$arTipos['ttelefone'] = 'TTelefone';
		$arTipos['tnumerico'] = 'TNumerico';
		$arTipos['tmoeda'] = 'TMoeda';
		$arTiposEntidade = array();
		foreach(CUtilitario_geradorGerarFonte::$entidade['ng_tipo'] as $indice => $tipo){
			if(isset($arTipos[$tipo])) {
				$arTiposEntidade[$indice] = $arTipos[$tipo];
			}else{
				$arTiposEntidade[$indice] = $tipo;
			}
		}
		$visualizacao->nomes = CUtilitario_geradorGerarFonte::$entidade['ng_nome'];
		$visualizacao->chave = CUtilitario_geradorGerarFonte::$entidade['ng_nome'][CUtilitario_geradorGerarFonte::$entidade['ng_chave_pk']];
		$visualizacao->nomesPropriedades = CUtilitario_geradorGerarFonte::$entidade['en_nome'];
		$visualizacao->tipos = $arTiposEntidade;
		$visualizacao->negocioNome = CUtilitario_geradorGerarFonte::$nomeNegocio;
		CUtilitario_geradorGerarFonte::escreverArquivo(CUtilitario_geradorGerarFonte::$nomeEntidade."/classes/".CUtilitario_geradorGerarFonte::$nomeNegocio.".php",$visualizacao->pegar('classesNegocio.html'));
	}
	/**
	* Monta a classe de internacionalização
	*/
	public static function montarInternacionalizacao(visualizacao $visualizacao){
		$internacionalizacao = definicaoEntidade::internacionalizacao(CUtilitario_geradorGerarFonte::$nomeNegocio);
		$visualizacao->internacionalizacaoNome = $internacionalizacao;
		CUtilitario_geradorGerarFonte::escreverArquivo(CUtilitario_geradorGerarFonte::$nomeEntidade."/classes/{$internacionalizacao}.php",$visualizacao->pegar('classesInternacionalizacao.html'));
	}
	/**
	* Monta o controle de Exclusão
	*/
	public static function montarControleExcluir(visualizacao $visualizacao){
		$controle = definicaoEntidade::controle(CUtilitario_geradorGerarFonte::$nomeNegocio);
		$visualizacao->acao = "Executa a exclusão de um objeto : ".CUtilitario_geradorGerarFonte::$entidade['entidade'];
		$visualizacao->controleNome = "{$controle}_excluir";
		$visualizacao->controlePai = 'controlePadraoExcluir';
		CUtilitario_geradorGerarFonte::escreverArquivo(CUtilitario_geradorGerarFonte::$nomeEntidade."/classes/{$controle}_excluir.php",$visualizacao->pegar('classesControle.html'));
	}
	/**
	* Monta o controle de Gravação
	*/
	public static function montarControleGravar(visualizacao $visualizacao){
		$controle = definicaoEntidade::controle(CUtilitario_geradorGerarFonte::$nomeNegocio);
		$visualizacao->acao = "Executa a gravação de um objeto : ".CUtilitario_geradorGerarFonte::$entidade['entidade'];
		$visualizacao->controleNome = "{$controle}_gravar";
		$visualizacao->controlePai = 'controlePadraoGravar';
		CUtilitario_geradorGerarFonte::escreverArquivo(CUtilitario_geradorGerarFonte::$nomeEntidade."/classes/{$controle}_gravar.php",$visualizacao->pegar('classesControle.html'));
	}
	/**
	* Monta o controle de Mudança de Pagina
	*/
	public static function montarControleMudarPagina(visualizacao $visualizacao){
		$controle = definicaoEntidade::controle(CUtilitario_geradorGerarFonte::$nomeNegocio);
		$visualizacao->acao = "Executa a mudança de pagina da listagem";
		$visualizacao->controleNome = "{$controle}_mudarPagina";
		$visualizacao->controlePai = 'controlePadraoMudarPagina';
		CUtilitario_geradorGerarFonte::escreverArquivo(CUtilitario_geradorGerarFonte::$nomeEntidade."/classes/{$controle}_mudarPagina.php",$visualizacao->pegar('classesControle.html'));
	}
	/**
	* Monta o controle de Pesquisar
	*/
	public static function montarControlePesquisar(visualizacao $visualizacao){
		$controle = definicaoEntidade::controle(CUtilitario_geradorGerarFonte::$nomeNegocio);
		$visualizacao->acao = "Executa a pesquisa de um objeto : ".CUtilitario_geradorGerarFonte::$entidade['entidade'];
		$visualizacao->controleNome = "{$controle}_pesquisar";
		$visualizacao->controlePai = 'controlePadraoPesquisar';
		CUtilitario_geradorGerarFonte::escreverArquivo(CUtilitario_geradorGerarFonte::$nomeEntidade."/classes/{$controle}_pesquisar.php",$visualizacao->pegar('classesControle.html'));
	}
	/**
	* Monta o controle de Ver
	*/
	public static function montarControleVerEdicao(visualizacao $visualizacao){
		$controle = definicaoEntidade::controle(CUtilitario_geradorGerarFonte::$nomeNegocio);
		$visualizacao->acao = "Cria a visualização de um objeto : ".CUtilitario_geradorGerarFonte::$entidade['entidade'];
		$visualizacao->controleNome = "{$controle}_verEdicao";
		$visualizacao->controlePai = 'controlePadraoVerEdicao';
		CUtilitario_geradorGerarFonte::escreverArquivo(CUtilitario_geradorGerarFonte::$nomeEntidade."/classes/{$controle}_verEdicao.php",$visualizacao->pegar('classesControle.html'));
	}
	/**
	* Monta o controle de Ver a Pesquisa
	*/
	public static function montarControleVerPesquisa(visualizacao $visualizacao){
		$controle = definicaoEntidade::controle(CUtilitario_geradorGerarFonte::$nomeNegocio);
		$visualizacao->acao = "Cria a visualização da pesquisa de um objeto : ".CUtilitario_geradorGerarFonte::$entidade['entidade'];
		$visualizacao->controleNome = "{$controle}_verPesquisa";
		$visualizacao->controlePai = 'controlePadraoPesquisa';
		CUtilitario_geradorGerarFonte::escreverArquivo(CUtilitario_geradorGerarFonte::$nomeEntidade."/classes/{$controle}_verPesquisa.php",$visualizacao->pegar('classesControle.html'));
	}
	/**
	* Monta o template de ver
	*/
	public static function montarTemplateVerEdicao(visualizacao $visualizacao){
		$visualizacao->chaveNegocio = CUtilitario_geradorGerarFonte::$entidade['ng_nome'][CUtilitario_geradorGerarFonte::$entidade['ng_chave_pk']];
		$camposControle = array();
		foreach(CUtilitario_geradorGerarFonte::$entidade['ng_nome'] as $chave => $valor){
			if(CUtilitario_geradorGerarFonte::$entidade['ng_chave_pk'] != $chave) $camposControle['nome'.ucFirst($valor)] = $valor;
		}
		$visualizacao->nomes = $camposControle;
		$controle = definicaoEntidade::controle(CUtilitario_geradorGerarFonte::$nomeNegocio);
		$arNomeTema = explode('/',definicaoPasta::tema());
		if(!($nomeTema = array_pop($arNomeTema))){$nomeTema = array_pop($arNomeTema);};
		$nomeTema = $nomeTema ? $nomeTema.'_' : null;
		if(!is_file($visualizacao->template_dir."{$nomeTema}templateVerEdicao.html")) $nomeTema = null;
		CUtilitario_geradorGerarFonte::escreverArquivo(CUtilitario_geradorGerarFonte::$nomeEntidade."/html/{$nomeTema}{$controle}_verEdicao.html",$visualizacao->pegar("{$nomeTema}templateVerEdicao.html"));
	}
	/**
	* Monta o template de verPesquisa
	*/
	public static function montarTemplateVerPesquisa(visualizacao $visualizacao){
		$camposControle = array();
		foreach(CUtilitario_geradorGerarFonte::$entidade['ng_nome'] as $chave => $valor){
			if(CUtilitario_geradorGerarFonte::$entidade['ng_chave_pk'] != $chave) $camposControle['nome'.ucFirst($valor)] = $valor;
		}
		$visualizacao->nomes = $camposControle;
		$controle = definicaoEntidade::controle(CUtilitario_geradorGerarFonte::$nomeNegocio);
		$arNomeTema = explode('/',definicaoPasta::tema());
		if(!($nomeTema = array_pop($arNomeTema))){$nomeTema = array_pop($arNomeTema);};
		$nomeTema = $nomeTema ? $nomeTema.'_' : null;
		if(!is_file($visualizacao->template_dir."{$nomeTema}templateVerPesquisa.html")) $nomeTema = null;
		CUtilitario_geradorGerarFonte::escreverArquivo(CUtilitario_geradorGerarFonte::$nomeEntidade."/html/{$nomeTema}{$controle}_verPesquisa.html",$visualizacao->pegar("{$nomeTema}templateVerPesquisa.html"));
	}
}
?>
