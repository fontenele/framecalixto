<?php
/**
* Classe de definição da camada de controle
* @package FrameCalixto
* @subpackage Controle
*/
abstract class controlePadraoVerPesquisa extends controlePadrao{
	/**
	* @var [pagina] pagina a ser listada
	*/
	public $pagina;
	/**
	* @var [controlePadraoListagem] controle especialista em listagem
	*/
	public $listagem;
	/**
	* Método inicial do controle
	*/
	function inicial(){
		$this->registrarInternacionalizacao($this,$this->visualizacao);
		$this->gerarMenus();
		$this->pagina = ($this->sessao->tem('pagina')) ? $this->sessao->pegar('pagina'): new pagina();
		$negocio = definicaoEntidade::negocio($this);
		$negocio = ($this->sessao->tem('filtro')) ? $this->sessao->pegar('filtro'): new $negocio();
		$this->montarApresentacao($negocio);
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
	* Retorna um array com os itens do menu do programa
	* @return array itens do menu do programa
	*/
	function montarMenuPrograma(){
		$link = "?c=%s";
		$menu[$this->inter->pegarTexto('botaoNovo')] =
			sprintf($link,definicaoEntidade::controle($this,'verEdicao'));
		$menu[$this->inter->pegarTexto('botaoPesquisar')] =
			'javascript:document.formulario.submit();';
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
	* Método de criação da coleção a ser listada
	* @return colecaoPadraoNegocio coleção a ser listada
	*/
	public function definirColecao(){
		if($this->sessao->tem('filtro')){
			$negocio = $this->sessao->pegar('filtro');
			return $negocio->pesquisar($this->pegarPagina());
		}else{
			$negocio = definicaoEntidade::negocio($this);
			$negocio = new $negocio();
			return $negocio->lerTodos($this->pegarPagina());
		}
	}
	/**
	* metodo de apresentação do negocio
	* @param [negocio] objeto para a apresentação
	*/
	public function montarApresentacao(negocio $negocio, $tipo = 'edicao'){
		parent::montarApresentacao($negocio);
		$this->visualizacao->tituloListagem = $this->inter->pegarTexto('tituloListagem');
	}
}
?>