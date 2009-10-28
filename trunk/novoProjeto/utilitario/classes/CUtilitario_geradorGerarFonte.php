<?php
/**
* Classe de controle
* Ver o Usuário
* @package Sistema
* @subpackage Utilitario
*/
class CUtilitario_geradorGerarFonte extends controle{
	public $nomeEntidade;
	public $nomeNegocio;
	public $nomeTabela;
	public $entidade;
	protected $debug = false;
	
	/**
	* Método inicial do controle
	*/
	function inicial(){
		$this->passarProximoControle(definicaoEntidade::controle($this,'geradorDefinirEntidade'));
		$this->entidade = $_POST;
		$this->entidade['ng_nome'] = array_map('caracteres::RetiraAcentos',$this->entidade['ng_nome']);
		$this->entidade['bd_campo'] = array_map('caracteres::RetiraAcentos',$this->entidade['bd_campo']);
		$arNome = explode(' ',strtolower(caracteres::RetiraAcentos($this->entidade['entidade'])));
		$nome = array_shift($arNome);
		$arNome = array_map("ucFirst", $arNome) ;
		array_unshift($arNome,$nome);
		$this->nomeEntidade = implode('',$arNome);
		$this->nomeNegocio = 'N'.ucFirst($this->nomeEntidade);
		$this->nomeTabela = caracteres::RetiraAcentos($this->entidade['nomeTabela']);
		$this->nomeSequence = caracteres::RetiraAcentos($this->entidade['nomeSequence'] ? $this->entidade['nomeSequence'] : "sq_{$this->nomeTabela}");
		if(!is_dir($this->nomeEntidade))
			mkdir($this->nomeEntidade,0777);
		if(!is_dir("{$this->nomeEntidade}/classes"))
			mkdir("{$this->nomeEntidade}/classes",0777);
		if(!is_dir("{$this->nomeEntidade}/xml"))
			mkdir("{$this->nomeEntidade}/xml",0777);
		if(!is_dir("{$this->nomeEntidade}/html"))
			mkdir("{$this->nomeEntidade}/html",0777);
		umask(0111);
		$this->visualizacao->entidade = "[{$this->entidade['entidade']}]";
		$this->visualizacao->pacote = "{$this->entidade['entidade']}";
		$this->visualizacao->classe = 'class';
		$this->montarArquivoDefinicaoXML();
		$this->montarArquivoInternacionalizacaoXML();
		$this->montarPersistente();
		$this->montarNegocio();
		$this->montarInternacionalizacao();
		$this->montarControleExcluir();
		$this->montarControleGravar();
		//$this->montarControleMudarPagina();
		//$this->montarControlePesquisar();
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
		if($this->debug){die;}
	}
	/**
	* Escreve o arquivo com o conteudo passado
	* @param [string] caminho do arquivo a ser escrito
	* @param [string] conteudo do arquivo a ser escrito
	*/
	protected function escreverArquivo($caminho,$conteudo){
		$caminho = caracteres::RetiraAcentos($caminho);
		if(!isset($_POST['arquivo'][$caminho])) return ;
		if($this->debug){
			echo "<br /><br /><br />No arquivo: {$caminho}<br /><br />";
			highlight_string($conteudo);
			return ;
		}
		$handle = fopen ($caminho, "w");
		fwrite($handle, $conteudo);
		fclose($handle);
	}
	/**
	* Monta o conteúdo do arquivo de definção XML
	*/
	function montarArquivoDefinicaoXML(){
		$tabela = " nomeBanco='{$this->nomeTabela}'";
		$sequence = " nomeSequencia='{$this->nomeSequence}'";
		$xml = "<?xml version='1.0' encoding='utf-8' ?>\n";
		$xml.= "<entidade {$tabela}{$sequence}>\n";
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
			$tipoOrdenacao = isset($this->entidade['bd_tipo_ordem'][$index]) ? "tipoOrdem='inversa' " : '';
			$descritivo = ($this->entidade['vi_ordemDescritivo'][$index])? "descritivo='{$this->entidade['vi_ordemDescritivo'][$index]}' " : '' ;
			$classeAssociativa = ((isset($this->entidade['ng_fk'][$index])) && ($this->entidade['ng_associativa'][$index])) ? "classeAssociativa='{$this->entidade['ng_associativa'][$index]}' " : '';
			$metodoLeitura = ((isset($this->entidade['ng_fk'][$index])) && ($this->entidade['ng_metodo'][$index])) ? "metodoLeitura='{$this->entidade['ng_metodo'][$index]}' " : '';
			$xml.= "\t\t<propriedade {$id}{$tipo}{$tamanho}{$obrigatorio}{$chavePrimaria}{$chaveUnica}{$classeAssociativa}{$metodoLeitura}{$descritivo} >\n";
			if($this->entidade['ng_dominio'][$index]){
				$arDominio = explode('][',substr($this->entidade['ng_dominio'][$index],1,strlen($this->entidade['ng_dominio'][$index]) -2));
				$xml.="\t\t\t<dominio>\n";
				foreach($arDominio as $item){
					$item = explode(',',$item);
					$xml.="\t\t\t\t<opcao id='{$item[0]}' />\n";
				}
				$xml.="\t\t\t</dominio>\n";
			}
			if(isset($this->entidade['ng_fk'][$index])){
				$xml.= "\t\t\t<banco {$nomeBanco}{$ordenacao}{$tipoOrdenacao}>\n";
				$xml.= "\t\t\t\t<chaveEstrangeira tabela='{$this->entidade['bd_referencia_tabela'][$index]}' campo='{$this->entidade['bd_referencia_campo'][$index]}' />\n";
				$xml.= "\t\t\t</banco>\n";
			}else{
				$xml.= "\t\t\t<banco {$nomeBanco}{$ordenacao}{$tipoOrdenacao} />\n";
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
		$this->escreverArquivo("{$this->nomeEntidade}/xml/entidade.xml",$xml);
	}
	/**
	* Monta o conteúdo do arquivo de definção XML
	*/
	function montarArquivoInternacionalizacaoXML(){
		$xml = "<?xml version='1.0' encoding='utf-8' ?>\n";
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
				$arDominio = explode('][',substr($this->entidade['ng_dominio'][$index],1,strlen($this->entidade['ng_dominio'][$index]) -2));
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
		$this->escreverArquivo("{$this->nomeEntidade}/xml/pt_BR.xml", $xml);
	}
	/**
	* Monta as classes persistentes
	*/
	function montarPersistente(){
		$persistente = definicaoEntidade::persistente($this->nomeNegocio);
		$this->visualizacao->persistenteNome = $persistente;
		$this->visualizacao->persistentePai = 'persistentePadraoPG';
		$this->escreverArquivo("{$this->nomeEntidade}/classes/{$persistente}.postgres.php",$this->visualizacao->pegar('classesPersistente.html'));
		$this->visualizacao->persistentePai = 'persistentePadraoMySql';
		$this->escreverArquivo("{$this->nomeEntidade}/classes/{$persistente}.mysql.php",$this->visualizacao->pegar('classesPersistente.html'));
		$this->visualizacao->persistentePai = 'persistentePadraoOCI';
		$this->escreverArquivo("{$this->nomeEntidade}/classes/{$persistente}.oracle.php",$this->visualizacao->pegar('classesPersistente.html'));
	}
	/**
	* Monta a classe de negocio
	*/
	function montarNegocio(){
		$this->visualizacao->nomes = $this->entidade['ng_nome'];
		$this->visualizacao->chave = $this->entidade['ng_nome'][$this->entidade['ng_chave_pk']];
		$this->visualizacao->nomesPropriedades = $this->entidade['en_nome'];
		$this->visualizacao->tipos = $this->entidade['ng_tipo'];
		$this->visualizacao->negocioNome = $this->nomeNegocio;
		$this->escreverArquivo("{$this->nomeEntidade}/classes/{$this->nomeNegocio}.php",$this->visualizacao->pegar('classesNegocio.html'));
	}
	/**
	* Monta a classe de internacionalização
	*/
	function montarInternacionalizacao(){
		$internacionalizacao = definicaoEntidade::internacionalizacao($this->nomeNegocio);
		$this->visualizacao->internacionalizacaoNome = $internacionalizacao;
		$this->escreverArquivo("{$this->nomeEntidade}/classes/{$internacionalizacao}.php",$this->visualizacao->pegar('classesInternacionalizacao.html'));
	}
	/**
	* Monta o controle de Exclusão
	*/
	function montarControleExcluir(){
		$controle = definicaoEntidade::controle($this->nomeNegocio);
		$this->visualizacao->acao = "Executa a exclusão de um objeto : {$this->entidade['entidade']}";
		$this->visualizacao->controleNome = "{$controle}_excluir";
		$this->visualizacao->controlePai = 'controlePadraoExcluir';
		$this->escreverArquivo("{$this->nomeEntidade}/classes/{$controle}_excluir.php",$this->visualizacao->pegar('classesControle.html'));
	}
	/**
	* Monta o controle de Gravação
	*/
	function montarControleGravar(){
		$controle = definicaoEntidade::controle($this->nomeNegocio);
		$this->visualizacao->acao = "Executa a gravação de um objeto : {$this->entidade['entidade']}";
		$this->visualizacao->controleNome = "{$controle}_gravar";
		$this->visualizacao->controlePai = 'controlePadraoGravar';
		$this->escreverArquivo("{$this->nomeEntidade}/classes/{$controle}_gravar.php",$this->visualizacao->pegar('classesControle.html'));
	}
	/**
	* Monta o controle de Mudança de Pagina
	*/
	function montarControleMudarPagina(){
		$controle = definicaoEntidade::controle($this->nomeNegocio);
		$this->visualizacao->acao = "Executa a mudança de pagina da listagem";
		$this->visualizacao->controleNome = "{$controle}_mudarPagina";
		$this->visualizacao->controlePai = 'controlePadraoMudarPagina';
		$this->escreverArquivo("{$this->nomeEntidade}/classes/{$controle}_mudarPagina.php",$this->visualizacao->pegar('classesControle.html'));
	}
	/**
	* Monta o controle de Pesquisar
	*/
	function montarControlePesquisar(){
		$controle = definicaoEntidade::controle($this->nomeNegocio);
		$this->visualizacao->acao = "Executa a pesquisa de um objeto : {$this->entidade['entidade']}";
		$this->visualizacao->controleNome = "{$controle}_pesquisar";
		$this->visualizacao->controlePai = 'controlePadraoPesquisar';
		$this->escreverArquivo("{$this->nomeEntidade}/classes/{$controle}_pesquisar.php",$this->visualizacao->pegar('classesControle.html'));
	}
	/**
	* Monta o controle de Ver
	*/
	function montarControleVerEdicao(){
		$controle = definicaoEntidade::controle($this->nomeNegocio);
		$this->visualizacao->acao = "Cria a visualização de um objeto : {$this->entidade['entidade']}";
		$this->visualizacao->controleNome = "{$controle}_verEdicao";
		$this->visualizacao->controlePai = 'controlePadraoVerEdicao';
		$this->escreverArquivo("{$this->nomeEntidade}/classes/{$controle}_verEdicao.php",$this->visualizacao->pegar('classesControle.html'));
	}
	/**
	* Monta o controle de Ver a Pesquisa
	*/
	function montarControleVerPesquisa(){
		$controle = definicaoEntidade::controle($this->nomeNegocio);
		$this->visualizacao->acao = "Cria a visualização da pesquisa de um objeto : {$this->entidade['entidade']}";
		$this->visualizacao->controleNome = "{$controle}_verPesquisa";
		$this->visualizacao->controlePai = 'controlePadraoPesquisa';
		$this->escreverArquivo("{$this->nomeEntidade}/classes/{$controle}_verPesquisa.php",$this->visualizacao->pegar('classesControle.html'));
	}
	/**
	* Monta o template de ver
	*/
	function montarTemplateVerEdicao(){
		$this->visualizacao->chaveNegocio = $this->entidade['ng_nome'][$this->entidade['ng_chave_pk']];
		$camposControle = array();
		foreach($this->entidade['ng_nome'] as $chave => $valor){
			if($this->entidade['ng_chave_pk'] != $chave) $camposControle['nome'.ucFirst($valor)] = $valor;
		}
		$this->visualizacao->nomes = $camposControle;
		$controle = definicaoEntidade::controle($this->nomeNegocio);
		$this->escreverArquivo("{$this->nomeEntidade}/html/{$controle}_verEdicao.html",$this->visualizacao->pegar('templateVerEdicao.html'));
	}
	/**
	* Monta o template de verPesquisa
	*/
	function montarTemplateVerPesquisa(){
		$camposControle = array();
		foreach($this->entidade['ng_nome'] as $chave => $valor){
			if($this->entidade['ng_chave_pk'] != $chave) $camposControle['nome'.ucFirst($valor)] = $valor;
		}
		$this->visualizacao->nomes = $camposControle;
		$controle = definicaoEntidade::controle($this->nomeNegocio);
		$this->escreverArquivo("{$this->nomeEntidade}/html/{$controle}_verPesquisa.html",$this->visualizacao->pegar('templateVerPesquisa.html'));
	}
}
?>
