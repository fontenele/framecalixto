<?php
/**
* Classe de defini��o da camada de controle 
* Forma��o especialista para mudar de pagina 
* @package Infra-estrutura
* @subpackage Controle
*/
class controlePadraoMudarPagina extends controle{
	/**
	* M�todo inicial do controle
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
}
?>
