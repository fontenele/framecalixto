<?php
/**
* Classe de definição da camada de controle
* @package FrameCalixto
* @subpackage Controle
*/
class controlePadraoPesquisa extends controlePadrao{
	/**
	* @var [pagina] pagina a ser listada
	*/
	public $pagina;
	/**
	* @var negocioPadrao objeto de negócio que será utilizado para gerar a pesquisa
	*/
	public $filtro;
	/**
	* @var [controlePadraoListagem] controle especialista em listagem
	*/
	public $listagem;
	/**
	* Método inicial do controle
	*/
	public function inicial(){
		$this->registrarInternacionalizacao($this,$this->visualizacao);
		$this->gerarMenus();
		$this->definirPagina();
		$this->definirFiltro();
		$this->montarApresentacao($this->filtro);
		$this->montarListagem();
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
		$menu->$novo = new VMenu($novo,sprintf("?c=%s",definicaoEntidade::controle($this,'verEdicao')),'.sistema/imagens/botao_novo.png');
		$menu->$pesquisar = new VMenu($pesquisar,'javascript:document.formulario.submit();','.sistema/imagens/botao_pesquisar.png');
		return $menu;
	}
	/**
	* Método de criação do controle de listagem
	* @return controlePadraoListagem Um controle especialista em listagem
	*/
	public function criarControleListagem(){
		return new controlePadraoListagem();
	}
	/**
	* Método que define a página que será exibida na pesquisa
	*/
	public function definirPagina(){
		$mapeador = controlePadrao::pegarEstrutura($this);
		$this->pagina = ($this->sessao->tem('pagina')) ? $this->sessao->pegar('pagina'): new pagina($mapeador['tamanhoPaginaListagem']);
		$this->pagina->passarPagina(isset($_GET['pagina']) ? $_GET['pagina'] : null);
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
	* @param [negocio] objeto para a apresentação
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
	* metodo de apresentação da listagem
	*/
	public function montarListagem(){
		$this->visualizacao->listagem = $this->criarControleListagem();
		$this->visualizacao->listagem->passarPagina($this->pegarPagina());
		$this->visualizacao->listagem->colecao = $this->definirColecao();
		$this->visualizacao->listagem->controle = definicaoEntidade::controle($this,'verPesquisa');
	}
}
?>