<?php
/**
* Classe de controle
* Ver o Usuário
* @package Sistema
* @subpackage Gerador
*/
class CUtilitario_geradorGerarFonte extends controle{
	public $nomeEntidade;
	public $nomeNegocio;
	public $nomeTabela;
	public $entidade;
	/**
	* Método inicial do controle
	*/
	function inicial(){
		$this->passarProximoControle(definicaoEntidade::controle($this,'geradorDefinirEntidade'));
		$this->entidade = $_POST;
		$this->validar();
		$arNome = explode(' ',strtolower($this->entidade['entidade']));
		$nome = array_shift($arNome);
		$arNome = array_map("ucFirst", $arNome) ;
		array_unshift($arNome,$nome);
		$this->nomeEntidade = implode('',$arNome);
		$this->nomeNegocio = 'N'.ucFirst(implode('',$arNome));
		$this->nomeTabela = str_replace(' ','_',strtolower($this->entidade['entidade']));
		exec("mkdir {$this->nomeEntidade}");
		exec("mkdir {$this->nomeEntidade}/controles");
		exec("mkdir {$this->nomeEntidade}/definicoes");
		exec("mkdir {$this->nomeEntidade}/persistentes");
		exec("mkdir {$this->nomeEntidade}/visualizacoes");
		exec("mkdir {$this->nomeEntidade}/internacionalizacao");
		$this->montarArquivoDefinicaoXML();
		$this->montarArquivoInternacionalizacaoXML();
		$this->montarPersistente();
		$this->montarNegocio();
		$this->montarInternacionalizacao();
		$this->montarControleExcluir();
		$this->montarControleGravar();
		$this->montarControleMudarPagina();
		$this->montarControlePesquisar();
		$this->montarControleVerEdicao();
		$this->montarControleVerPesquisa();
		$this->montarTemplateVerEdicao();
		$this->montarTemplateVerPesquisa();
		exec("chmod -R 777 {$this->nomeEntidade}");
		if(isset($this->entidade['recriarBase'])){
			$persistente = definicaoEntidade::persistente($this->nomeNegocio);
			$conexao = conexao::criar();
			$obPersistente = new $persistente($conexao);
			$obPersistente->recriar();
		}
	}
	function validar(){
	
	}
	/**
	* Monta o conteúdo do arquivo de definção XML
	*/
	function montarArquivoDefinicaoXML(){
		$xml = "<?xml version='1.0' encoding='iso-8859-1' ?>\n";
		$xml.= "<entidade nomeBanco='{$this->nomeTabela}'>\n";
		$xml.= "\t<propriedades>\n";
		
		foreach($this->entidade['ng_nome'] as $index => $nomePropriedadeNegocio){
			$id = "id='{$this->entidade['ng_nome'][$index]}' ";
			$tipo= "tipo='{$this->entidade['ng_tipo'][$index]}' ";
			$tamanho = ($this->entidade['ng_tamanho'][$index]) ? "tamanho='{$this->entidade['ng_tamanho'][$index]}' " : '' ;
			$obrigatorio = isset($this->entidade['ng_nn'][$index]) ? "obrigatorio='sim' " : '' ;
			$chaveUnica = isset($this->entidade['????'][$index]) ? "indiceUnico='sim' " : '' ;
			$nomeBanco = isset($this->entidade['bd_campo'][$index]) ? "nome='{$this->entidade['bd_campo'][$index]}' " : '';
			$componente = isset($this->entidade['vi_componente'][$index]) ? "componente='{$this->entidade['vi_componente'][$index]}' ":'';
			$largura = isset($this->entidade['vi_largura'][$index]) ? "tamanho='{$this->entidade['vi_largura'][$index]}%' ":'';
			$link = isset($this->entidade['vi_link'][$index]) ? "hyperlink='sim' ":'';
			$chavePrimaria = ($this->entidade['ng_chave_pk'] == $index)  ? "indicePrimario='sim' " : '';
			$ordenacao = ($this->entidade['bd_ordem'][$index])? "ordem='{$this->entidade['bd_ordem'][$index]}' " : '' ;
			$descritivo = ($this->entidade['vi_ordemDescritivo'][$index])? "descritivo='{$this->entidade['vi_ordemDescritivo'][$index]}' " : '' ;
			$classeAssociativa = ((isset($this->entidade['ng_fk'][$index])) && ($this->entidade['ng_associativa'][$index])) ? "classeAssociativa='{$this->entidade['ng_associativa'][$index]}' " : '';
			$metodoLeitura = ((isset($this->entidade['ng_fk'][$index])) && ($this->entidade['ng_metodo'][$index])) ? "metodoLeitura='{$this->entidade['ng_metodo'][$index]}' " : '';
			$xml.= "\t\t<propriedade {$id}{$tipo}{$tamanho}{$obrigatorio}{$chavePrimaria}{$chaveUnica}{$classeAssociativa}{$metodoLeitura}{$descritivo} >\n";
			if($this->entidade['ng_dominio'][$index]){
				$arDominio = explode('][',substr($this->entidade['ng_dominio'][$index],1,strlen($this->entidade['ng_dominio'][$index]) -1));
				$xml.="\t\t\t<dominio>\n";
				foreach($arDominio as $item){
					$item = explode(',',$item);
					$xml.="\t\t\t\t<opcao id='{$item[0]}' />\n";
				}
				$xml.="\t\t\t</dominio>\n";
			}
			if(isset($this->entidade['ng_fk'][$index])){
				$xml.= "\t\t\t<banco {$nomeBanco}{$ordenacao}>\n";
				$xml.= "\t\t\t\t<chaveEstrangeira tabela='{$this->entidade['bd_referencia_tabela'][$index]}' campo='{$this->entidade['bd_referencia_campo'][$index]}' />\n";
				$xml.= "\t\t\t</banco>\n";
			}else{
				$xml.= "\t\t\t<banco {$nomeBanco}{$ordenacao} />\n";
			}
			if($this->entidade['vi_ordem'][$index]){
				$ordem = "ordem='{$this->entidade['vi_ordem'][$index]}' ";
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
		$handle = fopen ("{$this->nomeEntidade}/definicoes/entidade.xml", "w");
		fwrite($handle, $xml);
		fclose($handle);
	}
	/**
	* Monta o conteúdo do arquivo de definção XML
	*/
	function montarArquivoInternacionalizacaoXML(){
		$xml = "<?xml version='1.0' encoding='iso-8859-1' ?>\n";
		$xml.= "<internacionalizacao>\n";
		$xml.= "\t<entidade>\n";
		$xml.= "\t\t<nome>{$this->entidade['entidade']}</nome>\n";
		$xml.= "\t\t<propriedades>\n";
		foreach($this->entidade['ng_nome'] as $index => $nomePropriedadeNegocio){
			$xml.= "\t\t<propriedade nome='{$nomePropriedadeNegocio}'>\n";
			$xml.= "\t\t\t<nome>{$this->entidade['en_nome'][$index]}</nome>\n";
			$xml.= "\t\t\t<abreviacao>{$this->entidade['en_abreviacao'][$index]}</abreviacao>\n";
			$xml.= "\t\t\t<descricao>{$this->entidade['en_descricao'][$index]}</descricao>\n";
			if($this->entidade['ng_dominio'][$index]){
				$arDominio = explode('][',substr($this->entidade['ng_dominio'][$index],1,strlen($this->entidade['ng_dominio'][$index]) -1));
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
		$xml.= "\t\t<titulo>Cadastro de {$this->entidade['entidade']}</titulo>\n";
		$xml.= "\t</controles>\n";
		$xml.= "</internacionalizacao>\n";
		$handle = fopen ("{$this->nomeEntidade}/definicoes/pt_BR.xml", "w");
		fwrite($handle, $xml);
		fclose($handle);
	}
	/**
	* Monta as classes persistentes
	*/
	function montarPersistente(){
		$persistente = definicaoEntidade::persistente($this->nomeNegocio);
		$this->visualizacaoPadrao->pacote = "{$this->nomeEntidade}";
		$this->visualizacaoPadrao->persistenteNome = $persistente;
		$this->visualizacaoPadrao->pegar('classesPersistente.tpl');
		$handle1 = fopen ("{$this->nomeEntidade}/persistentes/{$persistente}.postgres.php", "w");
		$handle2 = fopen ("{$this->nomeEntidade}/persistentes/{$persistente}.mysql.php", "w");
		$this->visualizacaoPadrao->persistentePai = 'persistentePadraoPG';
		fwrite($handle1, $this->visualizacaoPadrao->pegar('classesPersistente.tpl'));
		$this->visualizacaoPadrao->persistentePai = 'persistentePadraoMySql';
		fwrite($handle2, $this->visualizacaoPadrao->pegar('classesPersistente.tpl'));
		fclose($handle1);
		fclose($handle2);
	}
	/**
	* Monta a classe de negocio
	*/
	function montarNegocio(){
		$this->visualizacaoPadrao->pacote = "{$this->nomeEntidade}";
		$this->visualizacaoPadrao->nomes = $this->entidade['ng_nome'];
		$this->visualizacaoPadrao->chave = $this->entidade['ng_nome'][$this->entidade['ng_chave_pk']];
		$this->visualizacaoPadrao->nomesPropriedades = $this->entidade['en_nome'];
		$this->visualizacaoPadrao->tipos = $this->entidade['ng_tipo'];
		$this->visualizacaoPadrao->negocioNome = $this->nomeNegocio;
		$handle = fopen ("{$this->nomeEntidade}/{$this->nomeNegocio}.php", "w");
		fwrite($handle, $this->visualizacaoPadrao->pegar('classesNegocio.tpl'));
		fclose($handle);
	}
	/**
	* Monta a classe de internacionalização
	*/
	function montarInternacionalizacao(){
		$internacionalizacao = definicaoEntidade::internacionalizacao($this->nomeNegocio);
		$this->visualizacaoPadrao->pacote = "{$this->nomeEntidade}";
		$this->visualizacaoPadrao->internacionalizacaoNome = $internacionalizacao;
		$handle = fopen ("{$this->nomeEntidade}/internacionalizacao/{$internacionalizacao}.php", "w");
		fwrite($handle, $this->visualizacaoPadrao->pegar('classesInternacionalizacao.tpl'));
		fclose($handle);
	}
	/**
	* Monta o controle de Exclusão
	*/
	function montarControleExcluir(){
		$controle = definicaoEntidade::controle($this->nomeNegocio);
		$this->visualizacaoPadrao->acao = "Executa a exclusão de um objeto : {$this->entidade['entidade']}";
		$this->visualizacaoPadrao->pacote = "{$this->nomeEntidade}";
		$this->visualizacaoPadrao->controleNome = "{$controle}_excluir";
		$this->visualizacaoPadrao->controlePai = 'controlePadraoExcluir';
		$handle = fopen ("{$this->nomeEntidade}/controles/{$controle}_excluir.php", "w");
		fwrite($handle, $this->visualizacaoPadrao->pegar('classesControle.tpl'));
		fclose($handle);
	}
	/**
	* Monta o controle de Gravação
	*/
	function montarControleGravar(){
		$controle = definicaoEntidade::controle($this->nomeNegocio);
		$this->visualizacaoPadrao->acao = "Executa a gravação de um objeto : {$this->entidade['entidade']}";
		$this->visualizacaoPadrao->pacote = "{$this->nomeEntidade}";
		$this->visualizacaoPadrao->controleNome = "{$controle}_gravar";
		$this->visualizacaoPadrao->controlePai = 'controlePadraoGravar';
		$handle = fopen ("{$this->nomeEntidade}/controles/{$controle}_gravar.php", "w");
		fwrite($handle, $this->visualizacaoPadrao->pegar('classesControle.tpl'));
		fclose($handle);
	}
	/**
	* Monta o controle de Mudança de Pagina
	*/
	function montarControleMudarPagina(){
		$controle = definicaoEntidade::controle($this->nomeNegocio);
		$this->visualizacaoPadrao->acao = "Executa a mudança de pagina da listagem";
		$this->visualizacaoPadrao->pacote = "{$this->nomeEntidade}";
		$this->visualizacaoPadrao->controleNome = "{$controle}_mudarPagina";
		$this->visualizacaoPadrao->controlePai = 'controlePadraoMudarPagina';
		$handle = fopen ("{$this->nomeEntidade}/controles/{$controle}_mudarPagina.php", "w");
		fwrite($handle, $this->visualizacaoPadrao->pegar('classesControle.tpl'));
		fclose($handle);
	}
	/**
	* Monta o controle de Pesquisar
	*/
	function montarControlePesquisar(){
		$controle = definicaoEntidade::controle($this->nomeNegocio);
		$this->visualizacaoPadrao->acao = "Executa a pesquisa de um objeto : {$this->entidade['entidade']}";
		$this->visualizacaoPadrao->pacote = "{$this->nomeEntidade}";
		$this->visualizacaoPadrao->controleNome = "{$controle}_pesquisar";
		$this->visualizacaoPadrao->controlePai = 'controlePadraoPesquisar';
		$handle = fopen ("{$this->nomeEntidade}/controles/{$controle}_pesquisar.php", "w");
		fwrite($handle, $this->visualizacaoPadrao->pegar('classesControle.tpl'));
		fclose($handle);
	}
	/**
	* Monta o controle de Ver
	*/
	function montarControleVerEdicao(){
		$controle = definicaoEntidade::controle($this->nomeNegocio);
		$this->visualizacaoPadrao->acao = "Cria a visualização de um objeto : {$this->entidade['entidade']}";
		$this->visualizacaoPadrao->pacote = "{$this->nomeEntidade}";
		$this->visualizacaoPadrao->controleNome = "{$controle}_verEdicao";
		$this->visualizacaoPadrao->controlePai = 'controlePadraoVerEdicao';
		$handle = fopen ("{$this->nomeEntidade}/controles/{$controle}_verEdicao.php", "w");
		fwrite($handle, $this->visualizacaoPadrao->pegar('classesControle.tpl'));
		fclose($handle);
	}
	/**
	* Monta o controle de Ver a Pesquisa
	*/
	function montarControleVerPesquisa(){
		$controle = definicaoEntidade::controle($this->nomeNegocio);
		$this->visualizacaoPadrao->acao = "Cria a visualização da pesquisa de um objeto : {$this->entidade['entidade']}";
		$this->visualizacaoPadrao->pacote = "{$this->nomeEntidade}";
		$this->visualizacaoPadrao->controleNome = "{$controle}_verPesquisa";
		$this->visualizacaoPadrao->controlePai = 'controlePadraoVerPesquisa';
		$handle = fopen ("{$this->nomeEntidade}/controles/{$controle}_verPesquisa.php", "w");
		fwrite($handle, $this->visualizacaoPadrao->pegar('classesControle.tpl'));
		fclose($handle);
	}
	/**
	* Monta o template de ver
	*/
	function montarTemplateVerEdicao(){
		$this->visualizacaoPadrao->chaveNegocio = $this->entidade['ng_nome'][$this->entidade['ng_chave_pk']];
		$camposControle = array();
		foreach($this->entidade['ng_nome'] as $chave => $valor){
			if($this->entidade['ng_chave_pk'] != $chave) $camposControle['nome'.ucFirst($valor)] = $valor;
		}
		$this->visualizacaoPadrao->nomes = $camposControle;
		$controle = definicaoEntidade::controle($this->nomeNegocio);
		$handle = fopen ("{$this->nomeEntidade}/visualizacoes/{$controle}_verEdicao.html", "w");
		fwrite($handle, $this->visualizacaoPadrao->pegar('templateVerEdicao.tpl'));
		fclose($handle);
	}
	/**
	* Monta o template de verPesquisa
	*/
	function montarTemplateVerPesquisa(){
		$camposControle = array();
		foreach($this->entidade['ng_nome'] as $chave => $valor){
			if($this->entidade['ng_chave_pk'] != $chave) $camposControle['nome'.ucFirst($valor)] = $valor;
		}
		$this->visualizacaoPadrao->nomes = $camposControle;
		$controle = definicaoEntidade::controle($this->nomeNegocio);
		$handle = fopen ("{$this->nomeEntidade}/visualizacoes/{$controle}_verPesquisa.html", "w");
		fwrite($handle, $this->visualizacaoPadrao->pegar('templateVerPesquisa.tpl'));
		fclose($handle);
	}
}
?>
