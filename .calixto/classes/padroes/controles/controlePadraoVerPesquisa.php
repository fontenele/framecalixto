<?php
/**
* Classe de defini��o da camada de controle
* @package Infra-estrutura
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
	* M�todo inicial do controle
	*/
	function inicial(){
		$this->registrarInternacionalizacao();
		$this->gerarMenus();
		$this->pagina = ($this->sessao->tem('pagina')) ? $this->sessao->pegar('pagina'): new pagina();
		$negocio = definicaoEntidade::negocio($this);
		$negocio = ($this->sessao->tem('filtro')) ? $this->sessao->pegar('filtro'): new $negocio();
		$this->montarApresentacao($negocio);
		$this->listagem = $this->criarControleListagem();
		$this->listagem->passarPagina($this->pegarPagina());
		$this->listagem->colecao = $this->definirColecao();
		$this->listagem->controle = definicaoEntidade::controle($this,'mudarPagina');
		$this->visualizacaoPadrao->listagem = $this->listagem;
		$this->visualizacaoPadrao->action = sprintf('?c=%s',definicaoEntidade::controle($this,'pesquisar'));
		parent::inicial();
		if($this->sessao->tem('negocio')) $this->sessao->retirar('negocio');
	}
	/**
	* Retorna um array com os itens do menu do programa
	* @return [array] itens do menu do programa
	*/
	function montarMenuPrograma(){
		$link = "?c=%s";
		$menu[$this->internacionalizacao->pegarTexto('botaoNovo')] = 
			sprintf($link,definicaoEntidade::controle($this,'verEdicao'));
		$menu[$this->internacionalizacao->pegarTexto('botaoPesquisar')] = 
			'javascript:document.formulario.submit();';
		return $menu;
	}
	/**
	* M�todo de cria��o do controle de listagem
	* @return [controle] Um controle especialista em listagem
	*/
	public function criarControleListagem(){
		return new controlePadraoListagem();
	}
	/**
	* M�todo de cria��o da cole��o a ser listada
	* @return [colecao] cole��o a ser listada
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
	* metodo de apresenta��o do negocio
	* @param [negocio] objeto para a apresenta��o
	*/
	public function montarApresentacao($negocio){
		parent::montarApresentacao($negocio);
		$this->visualizacaoPadrao->tituloListagem = $this->internacionalizacao->pegarTexto('tituloListagem');
	}
}
?>
