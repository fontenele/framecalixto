<?php
/**
* Classe de controle
* Realiza uma pesquisa geral nos dados do sistema
* @package Sistema
* @subpackage Utilitario
 */
class CUtilitario_pesquisaGeral extends controlePadraoPesquisa{
	public $filtro = 'calixto';
	/**
	* Método inicial do controle
	*/
	public function inicial(){
		$this->definirPagina();
		$this->definirFiltro();
		if(controle::tipoResposta() == controle::xml) controle::responderXml($this->definirColecao()->xml());
		if(controle::tipoResposta() == controle::json) controle::responderJson($this->definirColecao()->json());
		$this->registrarInternacionalizacao($this,$this->visualizacao);
		$this->gerarMenus();
		//$this->montarApresentacao($this->filtro);
		$this->entidades();
		//$this->montarListagem($this->visualizacao,$this->definirColecao(),$this->pegarPagina());
		controlePadrao::inicial();
		//$this->finalizar();
	}
	/**
	* Método que define a página que será exibida na pesquisa
	*/
	public function definirPagina(){
		$this->pagina = new pagina(0);
	}
	/**
	* Método que define o objeto de negócio que executará a pesquisa
	*/
	public function definirFiltro(){
		$this->filtro = isset($_POST['filtro']) ?$_POST['filtro']:null;
	}
	/**
	* Método de criação da coleção a ser listada
	* @return colecaoPadraoNegocio coleção a ser listada
	*/
	public function definirColecao(){
		if($this->sessao->tem('filtro')){
			return $this->filtro->$metodo($this->pegarPagina());
		}
		return array();
	}
	/**
	* Preenche os itens da propriedade menuPrograma
	* @return colecaoPadraoMenu do menu do programa
	*/
	function montarMenuPrograma(){
		$menu = parent::montarMenuPrograma();
		$menu->removerItem($this->inter->pegarTexto('botaoNovo'));
		$menu->removerItem($this->inter->pegarTexto('botaoPesquisar'));
		return $menu;
	}
	/**
	 * Verifica se o controle pode exibir uma listagem
	 * @param <type> $controle
	 * @return <type>
	 */
	protected function exibirListagem($controle){
		return is_file($controle['caminho']);
	}
	/**
	 * Varre o sistema e registra as listagens de respostas no template
	 * @return array
	 */
	protected function entidades(){
		for($i=1; $i < 6;$i++) $niveis[$i] = "nivel de busca {$i}";
		$this->visualizacao->nivel = VComponente::montar(VComponente::caixaCombinacao, 'nivel', isset($_POST['nivel']) ? $_POST['nivel'] : 1 ,null,$niveis);
		$this->visualizacao->filtro = VComponente::montar(VComponente::caixaEntrada, 'filtro', isset($_POST['filtro']) ? $_POST['filtro'] : null);
		$this->visualizacao->pesquisar = VComponente::montar(VComponente::confirmar, 'Pesquisar','Pesquisar');
		if(!$this->pegarFiltro()) return $this->visualizacao->listagens = array();
		$d = dir(".");
		$negocios = new colecao();
		$controles = new colecao();
		while (false !== ($arquivo = $d->read())) {
			if( is_dir($arquivo) && ($arquivo{0} !== '.') ){
				if(is_file($arquivo.'/classes/N'.ucfirst($arquivo).'.php')){
					$negocio = 'N'.ucfirst($arquivo);
					$obNegocio = new $negocio();
					if( $obNegocio instanceof negocioPadrao ) {
						$ordem[$arquivo] = array(
							'nome'=>$obNegocio->pegarInter()->pegarNome(),
							'caminho'=>$arquivo.'/classes/N'.ucfirst($arquivo).'.php'
						);
						$negocios->$arquivo = $obNegocio;
					}
				}
			}
		}
		$d->close();
		asort($ordem);
		$this->visualizacao->ordem = $ordem;
		$listagens = array();
		foreach($ordem as $idx => $arquivo){
			$obNegocio = $negocios->pegar($idx);
			$nome['controle'] = definicaoEntidade::controle($obNegocio, 'verPesquisa');
			if($this->exibirListagem($arquivo)){
				$colecao = $obNegocio->pesquisaGeral($this->pegarFiltro(),$this->pegarPagina(),isset($_POST['nivel']) ? $_POST['nivel'] : 1);
				call_user_func_array("{$nome['controle']}::montarListagem", array($this->visualizacao,$colecao,$this->pegarPagina(),$nome['controle']));
				$this->visualizacao->listagem->passarControle($nome['controle']);
				if($colecao->contarItens()){
					$listagens[$idx]['listagem'] = $this->visualizacao->listagem;
					$listagens[$idx]['ocorrencias'] = $colecao->contarItens();
					$listagens[$idx]['nome'] = $arquivo['nome'];
					$listagens[$idx]['controlePesquisa'] = $nome['controle'];
				}
			}
		}
		unset($this->visualizacao->listagem);
		$this->visualizacao->listagens = $listagens;
	}
}
?>
