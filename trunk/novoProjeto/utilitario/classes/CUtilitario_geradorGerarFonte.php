<?php
/**
* Classe de controle
* Executa a criação dos arquivos e classes definidos no gerador
* @package Sistema
* @subpackage Utilitario
*/
class CUtilitario_geradorGerarFonte extends controle{
	public static $nomeEntidade;
	public static $nomeNegocio;
	public static $nomeTabela;
	public static $nomeSequence;
	public static $dados;
	protected static $executar;
	
	/**
	* Método inicial do controle
	*/
	public function inicial(){
		//$this->passarProximoControle(definicaoEntidade::controle($this,'listarEntidade'));
		self::$executar = $_POST['executar'];
		self::gerarFonte($this->visualizacao,$_POST);
	}
	public static function gerarFonte(visualizacao $visualizacao,$dadosGerador){
		self::$dados = $dadosGerador;
		self::$dados['negocio']['propriedade'] = array_map('caracteres::RetiraAcentos',self::$dados['negocio']['propriedade']);
		self::$dados['persistente']['campo'] = array_map('caracteres::RetiraAcentos',self::$dados['persistente']['campo']);
		$arNome = explode(' ',strtolower(caracteres::RetiraAcentos(self::$dados['entidade'])));
		$nome = array_shift($arNome);
		$arNome = array_map("ucFirst", $arNome) ;
		array_unshift($arNome,$nome);
		self::$nomeEntidade = implode('',$arNome);
		self::$nomeNegocio = 'N'.ucFirst(self::$nomeEntidade);
		self::$nomeTabela = caracteres::RetiraAcentos(self::$dados['nomeTabela']);
		self::$nomeSequence = caracteres::RetiraAcentos(self::$dados['nomeSequence'] ? self::$dados['nomeSequence'] : "sq_{self::$nomeTabela}");
		if(self::$executar){
			if(!is_dir(self::$nomeEntidade))
				mkdir(self::$nomeEntidade,0777);
			chmod(self::$nomeEntidade,2777);
			if(!is_dir(self::$nomeEntidade."/classes"))
				mkdir(self::$nomeEntidade."/classes",0777);
			chmod(self::$nomeEntidade."/classes",2777);
			if(!is_dir(self::$nomeEntidade."/xml"))
				mkdir(self::$nomeEntidade."/xml",0777);
			chmod(self::$nomeEntidade."/xml",2777);
			if(!is_dir(self::$nomeEntidade."/html"))
				mkdir(self::$nomeEntidade."/html",0777);
			chmod(self::$nomeEntidade."/html",2777);
			umask(0111);
		}
		$visualizacao->entidade = self::$dados['entidade'];
		$visualizacao->pacote = self::$dados['entidade'];
		$visualizacao->classe = 'class';
		self::montarArquivoDefinicaoXML($visualizacao);
		self::montarArquivoInternacionalizacaoXML($visualizacao);
		self::montarPersistente($visualizacao);
		self::montarNegocio($visualizacao);
		self::montarInternacionalizacao($visualizacao);
		self::montarControleExcluir($visualizacao);
		self::montarControleGravar($visualizacao);
		self::montarControleVerEdicao($visualizacao);
		self::montarControleVerPesquisa($visualizacao);
		self::montarControleVerListagemPdf($visualizacao);
		self::montarTemplateVerEdicao($visualizacao);
		self::montarTemplateVerPesquisa($visualizacao);
		exec("chmod -R 777 ".self::$nomeEntidade);
		if((self::$dados['recriarBase']) && self::$executar){
			$persistente = definicaoEntidade::persistente(self::$nomeNegocio);
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
		if(!(self::$dados['arquivo'][$caminho])) return ;
		echo "<fieldset><legend>No arquivo: {$caminho}</legend><div class='well'>";
		highlight_string($conteudo);
		echo "</div></fieldset>";
		if(!self::$executar)
			return ;
		$handle = fopen ($caminho, "w");
		fwrite($handle, $conteudo);
		fclose($handle);
		chmod($caminho,0777);
	}
	/**
	* Monta o conteúdo do arquivo de definção XML
	*/
	public static function montarArquivoDefinicaoXML(){
		$tabela = " nomeBanco='".self::$nomeTabela."'";
		$sequence = " nomeSequencia='".self::$nomeSequence."'";
		$xml = "<?xml version='1.0' encoding='utf-8' ?>\n";
		$xml.= "<entidade {$tabela}{$sequence}>\n";
		$xml.= "\t<propriedades>\n";
		
		foreach(self::$dados['negocio']['propriedade'] as $index => $nomePropriedadeNegocio){
			$id = "id='".self::$dados['negocio']['propriedade'][$index]."' ";
			$tipo= "tipo='".self::$dados['negocio']['tipo'][$index]."' ";
			$tamanho = (self::$dados['negocio']['tamanho'][$index]) ? "tamanho='".self::$dados['negocio']['tamanho'][$index]."' " : '' ;
			$obrigatorio = (self::$dados['negocio']['nn'][$index]) ? "obrigatorio='sim' " : '' ;
			$chaveUnica = (self::$dados['negocio']['uk'][$index]) ? "indiceUnico='sim' " : '' ;
			$nomeBanco = (self::$dados['persistente']['campo'][$index]) ? "nome='".self::$dados['persistente']['campo'][$index]."' " : '';
			$componente = (self::$dados['visualizacao']['componente'][$index]) ? "componente='".self::$dados['visualizacao']['componente'][$index]."' ":'';
			$pesquisa = (self::$dados['visualizacao']['pesquisa'][$index]) ? "pesquisa='sim' ":"pesquisa='nao' ";
			$edicao = (self::$dados['visualizacao']['edicao'][$index]) ? "edicao='sim' ": "edicao='nao' ";
			$chavePrimaria = (self::$dados['negocio']['pk'] == $index)  ? "indicePrimario='sim' " : '';
			$ordenacao = (self::$dados['persistente']['ordem'][$index])? "ordem='".self::$dados['persistente']['ordem'][$index]."' " : '' ;
			$tipoOrdenacao = (self::$dados['persistente']['tipo-ordem'][$index]) ? "tipoOrdem='inversa' " : '';
			$descritivo = (self::$dados['visualizacao']['ordem-descritivo'][$index])? "descritivo='".self::$dados['visualizacao']['ordem-descritivo'][$index]."' " : '' ;
			$largura = isset(self::$dados['visualizacao']['largura'][$index]) ? "tamanho='".self::$dados['visualizacao']['largura'][$index]."%' ":'';
			$link = isset(self::$dados['visualizacao']['link'][$index]) ? "hyperlink='sim' ":'';
			$classeAssociativa = '';
			$metodoLeitura = '';
			if(strpos(self::$dados['negocio']['dominio-associativa'][$index], '[') === false){
				if(self::$dados['negocio']['fk'][$index]){
					$cl = explode('::',self::$dados['negocio']['dominio-associativa'][$index]);
					$classeAssociativa = "classeAssociativa='{$cl[0]}' ";
					$metodoLeitura = (isset($cl[1])) ? "metodoLeitura='{$cl[1]}' " : '';
				}
			}
			$xml.= "\t\t<propriedade {$id}{$tipo}{$tamanho}{$obrigatorio}{$chavePrimaria}{$chaveUnica}{$classeAssociativa}{$metodoLeitura}{$descritivo} >\n";
			if(($dominioAssociativa = self::$dados['negocio']['dominio-associativa'][$index])){
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
			if(self::$dados['negocio']['fk'][$index]){
				$xml.= "\t\t\t<banco {$nomeBanco}{$ordenacao}{$tipoOrdenacao}>\n";
				$xml.= "\t\t\t\t<chaveEstrangeira tabela='".self::$dados['persistente']['referencia-tabela'][$index]."' campo='".self::$dados['persistente']['referencia-campo'][$index]."' />\n";
				$xml.= "\t\t\t</banco>\n";
			}else{
				$xml.= "\t\t\t<banco {$nomeBanco}{$ordenacao}{$tipoOrdenacao} />\n";
			}
			if(self::$dados['visualizacao']['ordem'][$index]){
				$ordem = "ordem='".self::$dados['visualizacao']['ordem'][$index]."' ";
				$xml.= "\t\t\t<apresentacao {$componente}{$edicao}{$pesquisa}>\n";
				$xml.= "\t\t\t\t<listagem {$ordem}{$largura}{$link}/>\n";
				$xml.= "\t\t\t</apresentacao>\n";
			}else{
				$xml.= "\t\t\t<apresentacao {$componente}{$edicao}{$pesquisa} />\n";
			}
			$xml.= "\t\t</propriedade>\n";
		}
		$xml.= "\t</propriedades>\n";
		$xml.= "</entidade>";
		self::escreverArquivo(self::$nomeEntidade."/xml/entidade.xml",$xml);
	}
	/**
	* Monta o conteúdo do arquivo de definção XML
	*/
	public static function montarArquivoInternacionalizacaoXML(){
		$xml = "<?xml version='1.0' encoding='utf-8' ?>\n";
		$xml.= "<internacionalizacao>\n";
		$xml.= "\t<entidade>\n";
		$xml.= "\t\t<nome>".self::$dados['entidade']."</nome>\n";
		$xml.= "\t\t<propriedades>\n";
		foreach(self::$dados['negocio']['propriedade'] as $index => $nomePropriedadeNegocio){
			$xml.= "\t\t<propriedade nome='{$nomePropriedadeNegocio}'>\n";
			$xml.= "\t\t\t<nome>".self::$dados['inter']['nome'][$index]."</nome>\n";
			$xml.= "\t\t\t<abreviacao>".self::$dados['inter']['abreviacao'][$index]."</abreviacao>\n";
			$xml.= "\t\t\t<descricao>".self::$dados['inter']['descricao'][$index]."</descricao>\n";
			if(strpos($stDominio = self::$dados['negocio']['dominio-associativa'][$index], '[') !== false){
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
		$xml.= "\t\t<titulo>Cadastro de ".self::$dados['entidade']."</titulo>\n";
		$xml.= "\t</controles>\n";
		$xml.= "</internacionalizacao>\n";
		self::escreverArquivo(self::$nomeEntidade."/xml/pt_BR.xml", $xml);
	}
	/**
	* Monta as classes persistentes
	*/
	public static function montarPersistente(visualizacao $visualizacao){
		$persistente = definicaoEntidade::persistente(self::$nomeNegocio);
		$visualizacao->persistenteNome = $persistente;
		$visualizacao->persistentePai = 'persistentePadraoPG';
		self::escreverArquivo(self::$nomeEntidade."/classes/{$persistente}.postgres.php",$visualizacao->pegar('classesPersistente.html'));
		$visualizacao->persistentePai = 'persistentePadraoMySql';
		self::escreverArquivo(self::$nomeEntidade."/classes/{$persistente}.mysql.php",$visualizacao->pegar('classesPersistente.html'));
		$visualizacao->persistentePai = 'persistentePadraoOCI';
		self::escreverArquivo(self::$nomeEntidade."/classes/{$persistente}.oracle.php",$visualizacao->pegar('classesPersistente.html'));
		$visualizacao->persistentePai = 'persistentePadraoSqlite';
		self::escreverArquivo(self::$nomeEntidade."/classes/{$persistente}.sqlite.php",$visualizacao->pegar('classesPersistente.html'));
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
		foreach(self::$dados['negocio']['tipo'] as $indice => $tipo){
			if(isset($arTipos[$tipo])) {
				$arTiposEntidade[$indice] = $arTipos[$tipo];
			}else{
				$arTiposEntidade[$indice] = $tipo;
			}
		}
		$visualizacao->nomes = self::$dados['negocio']['propriedade'];
		$visualizacao->chave = self::$dados['negocio']['pk'];
		$visualizacao->nomesPropriedades = self::$dados['inter']['nome'];
		$visualizacao->tipos = $arTiposEntidade;
		$visualizacao->negocioNome = self::$nomeNegocio;
		self::escreverArquivo(self::$nomeEntidade."/classes/".self::$nomeNegocio.".php",$visualizacao->pegar('classesNegocio.html'));
	}
	/**
	* Monta a classe de internacionalização
	*/
	public static function montarInternacionalizacao(visualizacao $visualizacao){
		$internacionalizacao = definicaoEntidade::internacionalizacao(self::$nomeNegocio);
		$visualizacao->internacionalizacaoNome = $internacionalizacao;
		self::escreverArquivo(self::$nomeEntidade."/classes/{$internacionalizacao}.php",$visualizacao->pegar('classesInternacionalizacao.html'));
	}
	/**
	* Monta o controle de Exclusão
	*/
	public static function montarControleExcluir(visualizacao $visualizacao){
		$controle = definicaoEntidade::controle(self::$nomeNegocio);
		$visualizacao->acao = "Executa a exclusão de um objeto : ".self::$dados['entidade'];
		$visualizacao->controleNome = "{$controle}_excluir";
		$visualizacao->controlePai = 'controlePadraoExcluir';
		self::escreverArquivo(self::$nomeEntidade."/classes/{$controle}_excluir.php",$visualizacao->pegar('classesControle.html'));
	}
	/**
	* Monta o controle de Gravação
	*/
	public static function montarControleGravar(visualizacao $visualizacao){
		$controle = definicaoEntidade::controle(self::$nomeNegocio);
		$visualizacao->acao = "Executa a gravação de um objeto : ".self::$dados['entidade'];
		$visualizacao->controleNome = "{$controle}_gravar";
		$visualizacao->controlePai = 'controlePadraoGravar';
		self::escreverArquivo(self::$nomeEntidade."/classes/{$controle}_gravar.php",$visualizacao->pegar('classesControle.html'));
	}
	/**
	* Monta o controle de Mudança de Pagina
	*/
	public static function montarControleMudarPagina(visualizacao $visualizacao){
		$controle = definicaoEntidade::controle(self::$nomeNegocio);
		$visualizacao->acao = "Executa a mudança de pagina da listagem";
		$visualizacao->controleNome = "{$controle}_mudarPagina";
		$visualizacao->controlePai = 'controlePadraoMudarPagina';
		self::escreverArquivo(self::$nomeEntidade."/classes/{$controle}_mudarPagina.php",$visualizacao->pegar('classesControle.html'));
	}
	/**
	* Monta o controle de Pesquisar
	*/
	public static function montarControlePesquisar(visualizacao $visualizacao){
		$controle = definicaoEntidade::controle(self::$nomeNegocio);
		$visualizacao->acao = "Executa a pesquisa de um objeto : ".self::$dados['entidade'];
		$visualizacao->controleNome = "{$controle}_pesquisar";
		$visualizacao->controlePai = 'controlePadraoPesquisar';
		self::escreverArquivo(self::$nomeEntidade."/classes/{$controle}_pesquisar.php",$visualizacao->pegar('classesControle.html'));
	}
	/**
	* Monta o controle de Ver
	*/
	public static function montarControleVerEdicao(visualizacao $visualizacao){
		$controle = definicaoEntidade::controle(self::$nomeNegocio);
		$visualizacao->acao = "Cria a visualização de um objeto : ".self::$dados['entidade'];
		$visualizacao->controleNome = "{$controle}_verEdicao";
		$visualizacao->controlePai = 'controlePadraoVerEdicao';
		self::escreverArquivo(self::$nomeEntidade."/classes/{$controle}_verEdicao.php",$visualizacao->pegar('classesControle.html'));
	}
	/**
	* Monta o controle de Ver a Pesquisa
	*/
	public static function montarControleVerPesquisa(visualizacao $visualizacao){
		$controle = definicaoEntidade::controle(self::$nomeNegocio);
		$visualizacao->acao = "Cria a visualização da pesquisa de um objeto : ".self::$dados['entidade'];
		$visualizacao->controleNome = "{$controle}_verPesquisa";
		$visualizacao->controlePai = 'controlePadraoPesquisa';
		self::escreverArquivo(self::$nomeEntidade."/classes/{$controle}_verPesquisa.php",$visualizacao->pegar('classesControle.html'));
	}
	/**
	* Monta o controle de Ver Listagem Pdf
	*/
	public static function montarControleVerListagemPdf(visualizacao $visualizacao){
		$controle = definicaoEntidade::controle(self::$nomeNegocio);
		$visualizacao->acao = "Cria a visualização PDF listando objetos : ".self::$dados['entidade'];
		$visualizacao->controleNome = "{$controle}_verListagemPdf";
		$visualizacao->controlePai = 'controlePadraoPDFListagem';
		self::escreverArquivo(self::$nomeEntidade."/classes/{$controle}_verListagemPdf.php",$visualizacao->pegar('classesControle.html'));
	}
	/**
	* Monta o template de ver
	*/
	public static function montarTemplateVerEdicao(visualizacao $visualizacao){
		$visualizacao->chaveNegocio = self::$dados['negocio']['pk'];
		$camposControle = array();
		foreach(self::$dados['negocio']['propriedade'] as $chave => $valor){
			if(self::$dados['negocio']['pk'] != $chave) $camposControle['nome'.ucFirst($valor)] = $valor;
		}
		$visualizacao->nomes = $camposControle;
		$controle = definicaoEntidade::controle(self::$nomeNegocio);
		$arNomeTema = explode('/',definicaoPasta::tema());
		if(!($nomeTema = array_pop($arNomeTema))){$nomeTema = array_pop($arNomeTema);};
		$nomeTema = $nomeTema ? $nomeTema.'_' : null;
		if(!is_file($visualizacao->template_dir."{$nomeTema}templateVerEdicao.html")) $nomeTema = null;
		self::escreverArquivo(self::$nomeEntidade."/html/{$nomeTema}{$controle}_verEdicao.html",$visualizacao->pegar("{$nomeTema}templateVerEdicao.html"));
	}
	/**
	* Monta o template de verPesquisa
	*/
	public static function montarTemplateVerPesquisa(visualizacao $visualizacao){
		$camposControle = array();
		foreach(self::$dados['negocio']['propriedade'] as $chave => $valor){
			if(self::$dados['negocio']['pk'] != $chave) $camposControle['nome'.ucFirst($valor)] = $valor;
		}
		$visualizacao->nomes = $camposControle;
		$controle = definicaoEntidade::controle(self::$nomeNegocio);
		$arNomeTema = explode('/',definicaoPasta::tema());
		if(!($nomeTema = array_pop($arNomeTema))){$nomeTema = array_pop($arNomeTema);};
		$nomeTema = $nomeTema ? $nomeTema.'_' : null;
		if(!is_file($visualizacao->template_dir."{$nomeTema}templateVerPesquisa.html")) $nomeTema = null;
		self::escreverArquivo(self::$nomeEntidade."/html/{$nomeTema}{$controle}_verPesquisa.html",$visualizacao->pegar("{$nomeTema}templateVerPesquisa.html"));
	}
}
?>
