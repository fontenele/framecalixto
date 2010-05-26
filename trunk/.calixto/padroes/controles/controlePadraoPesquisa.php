<?php
/**
* Classe de definição da camada de controle
* @package FrameCalixto
* @subpackage Controle
*/
class controlePadraoPesquisa extends controlePadrao{
	/**
	* @var pagina pagina a ser listada
	*/
	public $pagina;
	/**
	* @var negocioPadrao objeto de negócio que será utilizado para gerar a pesquisa
	*/
	public $filtro;
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
		$this->montarApresentacao($this->filtro);
		$this->montarListagem($this->visualizacao,$this->definirColecao(),$this->pegarPagina());
		parent::inicial();
		$this->finalizar();
	}
	public function finalizar(){
		if($this->sessao->tem('negocio')) $this->sessao->retirar('negocio');
	}
	/**
	* Preenche os itens da propriedade menuPrograma
	* @return colecaoPadraoMenu do menu do programa
	*/
	function montarMenuPrograma(){
		$menu = parent::montarMenuPrograma();
		$novo = $this->inter->pegarTexto('botaoNovo');
		$pesquisar = $this->inter->pegarTexto('botaoPesquisar');
		$menu->$novo = new VMenu($novo,sprintf("?c=%s",definicaoEntidade::controle($this,'verEdicao')),definicaoPasta::tema().'icones/add.png');
		$menu->$pesquisar = new VMenu($pesquisar,'javascript:document.formulario.submit();',definicaoPasta::tema().'icones/application_view_list.png');
		return $menu;
	}
	/**
	* Método que define a página que será exibida na pesquisa
	*/
	public function definirPagina(){
		$mapeador = controlePadrao::pegarEstrutura($this);
		$this->pagina = ($this->sessao->tem('pagina')) ? $this->sessao->pegar('pagina'): new pagina($mapeador['tamanhoPaginaListagem']);
		if(isset($_GET['pagina'])) $this->pagina->passarPagina($_GET['pagina']);
		if(isset($_GET['tamanhoPagina'])) $this->pagina->passarTamanhoPagina($_GET['tamanhoPagina']);
		$this->sessao->registrar('pagina',$this->pagina);
	}
	/**
	* Método que define o objeto de negócio que executará a pesquisa
	*/
	public function definirFiltro(){
		$negocio = definicaoEntidade::negocio($this);
		if($_POST){
			$this->filtro = new $negocio();
			$this->montarNegocio($this->filtro);
			$this->sessao->registrar('filtro',$this->filtro);
		}else{
			$this->filtro = ($this->sessao->tem('filtro')) ? $this->sessao->pegar('filtro'): new $negocio();
		}
	}
	/**
	* Método de criação da coleção a ser listada
	* @return colecaoPadraoNegocio coleção a ser listada
	*/
	public function definirColecao(){
		$metodo = ($this->sessao->tem('filtro')) ? 'pesquisar' : 'lerTodos';
		return $this->filtro->$metodo($this->pegarPagina());
	}
	/**
	* metodo de apresentação do negocio
	* @param negocio objeto para a apresentação
	*/
	public function montarApresentacao(negocio $negocio, $tipo = 'edicao'){
		parent::montarApresentacao($negocio);
		$help = new VEtiquetaHtml('div');
		$help->passarClass('help');
		$help->passarConteudo($this->inter->pegarTexto('ajudaPesquisa'));
		$this->visualizacao->descricaoDeAjuda = $help;
		$this->visualizacao->tituloListagem = $this->inter->pegarTexto('tituloListagem');
	}
	/**
	 * Método de apresentação da listagem
	 * @param visualizacao $visualizacao
	 * @param colecao $colecao
	 * @param pagina $pagina
	 */
	public static function montarListagem(visualizacao $visualizacao,colecao $colecao,pagina $pagina, $entidade = null){
		$visualizacao->listagem = new VListaPaginada($colecao,$pagina, $entidade);
	}
}
?>