<?php
/**
* Classe de definição da camada de controle 
* Formação especialista para pesquisar um objeto de negocio
* @package FrameCalixto
* @subpackage Controle
*/
class controlePadraoPesquisar extends controlePadrao{
	protected $negocio;
	protected $pagina;
	/**
	* Método inicial do controle
	*/
	public function inicial(){
		$this->passarProximoControle(definicaoEntidade::controle($this,'verPesquisa'));
		$negocio = definicaoEntidade::negocio($this);
		$this->negocio = new $negocio();
		$this->pagina = new pagina();
		$this->pagina->passarPagina();
		$this->montarNegocio($this->negocio);
		$this->sessao->registrar('pagina',$this->pagina);
		$this->sessao->registrar('filtro',$this->negocio);
	}
}
?>