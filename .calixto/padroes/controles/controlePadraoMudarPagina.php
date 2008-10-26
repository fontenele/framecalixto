<?php
/**
* Classe de definição da camada de controle 
* Formação especialista para mudar de pagina 
* @package FrameCalixto
* @subpackage Controle
*/
class controlePadraoMudarPagina extends controle{
	/**
	* @var [pagina] pagina a ser listada
	*/
	public $pagina;
	/**
	* Método inicial do controle
	*/
	public function inicial(){
		$this->passarProximoControle(definicaoEntidade::controle($this,'verPesquisa'));
		$mapeador = controlePadrao::pegarEstrutura($this);
		$this->pagina = ($this->sessao->tem('pagina')) ? $this->sessao->pegar('pagina'): new pagina($mapeador['tamanhoPaginaListagem']);
		$this->pagina->passarPagina(isset($_GET['pagina']) ? $_GET['pagina'] : null);
		$this->sessao->registrar('pagina',$this->pagina);
	}
	/**
	* Método de validação do controle de acesso
	* @return [booleano] resultado da validação
	*/
	public function validarAcessoAoControle(){ 
		return true;
	}
}
?>
