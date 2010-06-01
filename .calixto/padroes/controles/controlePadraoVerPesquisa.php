<?php
/**
* Classe de definição da camada de controle
* @package FrameCalixto
* @subpackage Controle
*/
abstract class controlePadraoVerPesquisa extends controlePadrao{
	/**
	* @var pagina pagina a ser listada
	*/
	public $pagina;
	/**
	* @var negocioPadrao objeto de negócio que será utilizado para gerar a pesquisa
	*/
	public $filtro;
	/**
	* @var controlePadraoListagem controle especialista em listagem
	*/
	public $listagem;
	/**
	* Método inicial do controle
	*/
	function inicial(){
		$this->registrarInternacionalizacao($this,$this->visualizacao);
		$this->gerarMenus();
		$mapeador = controlePadrao::pegarEstrutura($this);
		$this->definirPaginaAtual();
		$this->definirFiltro();
		$this->montarApresentacao($this->pegarFiltro());
		$this->listagem = $this->criarControleListagem();
		$this->listagem->passarPagina($this->pegarPagina());
		$this->listagem->colecao = $this->definirColecao();
		$this->listagem->controle = definicaoEntidade::controle($this,'mudarPagina');
		$this->visualizacao->listagem = $this->listagem;
		$this->visualizacao->action = sprintf('?c=%s',definicaoEntidade::controle($this,'pesquisar'));
		$help = new VEtiquetaHtml('div');
		$help->passarClass('help');
		$help->passarConteudo($this->inter->pegarTexto('ajudaPesquisa'));
		$this->visualizacao->descricaoDeAjuda = $help;
		parent::inicial();
		if($this->sessao->tem('negocio')) $this->sessao->retirar('negocio');
	}
	/**
	* Método que define a página atual a ser exibida
	*/
	protected function definirPaginaAtual(){
		$mapeador = controlePadrao::pegarEstrutura($this);
		if(isset($_GET['pagina'])){
			$this->pagina = ($this->sessao->tem('pagina')) ? $this->sessao->pegar('pagina'): new pagina($mapeador['tamanhoPaginaListagem']);
			$this->pagina->passarPagina($_GET['pagina']);
			$this->sessao->registrar('pagina',$this->pagina);
		}else{
			$this->pagina = ($this->sessao->tem('pagina')) ? $this->sessao->pegar('pagina'): new pagina($mapeador['tamanhoPaginaListagem']);
		}
	}
	/**
	* Preenche os itens da propriedade menuPrograma
	* @return colecaoPadraoMenu do menu do programa
	*/
	function montarMenuPrograma(){
		$menu = parent::montarMenuPrograma();
		$novo = $this->inter->pegarTexto('botaoNovo');
		$pesquisar = $this->inter->pegarTexto('botaoPesquisar');
		$menu->$novo = new VMenu($novo,sprintf("?c=%s",definicaoEntidade::controle($this,'verEdicao')),'.sistema/icones/add.png');
		$menu->$pesquisar = new VMenu($pesquisar,'javascript:document.formulario.submit();','.sistema/icones/magnifier.png');
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
	* Método que define o objeto de negócio que executará a pesquisa
	*/
	public function definirFiltro(){
		$negocio = definicaoEntidade::negocio($this);
		$this->filtro = ($this->sessao->tem('filtro')) ? $this->sessao->pegar('filtro'): new $negocio();
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
		$this->visualizacao->tituloListagem = $this->inter->pegarTexto('tituloListagem');
	}
}
?>