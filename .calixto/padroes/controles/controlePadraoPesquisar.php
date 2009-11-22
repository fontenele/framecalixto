<?php
/**
* Classe de definição da camada de controle 
* Formação especialista para pesquisar um objeto de negocio
* @package FrameCalixto
* @subpackage Controle
*/
class controlePadraoPesquisar extends controlePadrao{
	/**
	* Negócio utilizado como filtro para a pesquisa
	* @var negocioPadrao
	*/
	protected $negocio;
	/**
	* @var pagina pagina a ser listada
	*/
	protected $pagina;
	/**
	* Método inicial do controle
	*/
	public function inicial(){
		$this->passarProximoControle(definicaoEntidade::controle($this,'verPesquisa'));
		$negocio = definicaoEntidade::negocio($this);
		$this->negocio = new $negocio();
		$mapeador = controlePadrao::pegarEstrutura($this);
		$this->pagina = new pagina($mapeador['tamanhoPaginaListagem']);
		$this->pagina->passarPagina();
		$this->montarNegocio($this->negocio);
		$this->sessao->registrar('pagina',$this->pagina);
		$this->sessao->registrar('filtro',$this->negocio);
	}
}
?>