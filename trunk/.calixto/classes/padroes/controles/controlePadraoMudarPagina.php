<?php
/**
* Classe de definição da camada de controle 
* Formação especialista para mudar de pagina 
* @package Infra-estrutura
* @subpackage Controle
*/
class controlePadraoMudarPagina extends controle{
	/**
	* Método inicial do controle
	*/
	public function inicial(){
		$this->passarProximoControle(definicaoEntidade::controle($this,'verPesquisa'));
		$pagina = ($this->sessao->tem('pagina')) ? $this->sessao->pegar('pagina') : new pagina() ;
		if(isset($_GET['pagina'])){
			$pagina->passarPagina($_GET['pagina']);
		}else{
			$pagina->passarPagina();
		}
		$this->sessao->registrar('pagina',$pagina);
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
