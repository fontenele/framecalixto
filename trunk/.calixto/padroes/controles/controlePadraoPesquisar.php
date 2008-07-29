<?php
/**
* Classe de definição da camada de controle 
* Formação especialista para pesquisar um objeto de negocio
* @package FrameCalixto
* @subpackage Controle
*/
class controlePadraoPesquisar extends controlePadrao{
	/**
	* Método inicial do controle
	*/
	public function inicial(){
		$this->passarProximoControle(definicaoEntidade::controle($this,'verPesquisa'));
		$negocio = definicaoEntidade::negocio($this);
		$negocio = new $negocio();
		$pagina = new pagina();
		$pagina->passarPagina();
		$this->montarNegocio($negocio);
		$this->sessao->registrar('pagina',$pagina);
		$this->sessao->registrar('filtro',$negocio);
	}
}
?>
