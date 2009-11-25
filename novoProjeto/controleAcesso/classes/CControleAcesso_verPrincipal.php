<?php
/**
* Classe de controle
* Processar a validação do login
* @package Sistema
* @subpackage Login
*/
class CControleAcesso_verPrincipal extends controlePadraoLiberado {
	/**
	* Preenche os itens da propriedade menuPrincipal
	* @return array itens do menu principal
	*/
	public function montarMenuPrincipal(){
		return controlePadrao::montarMenuPrincipal();
	}
	/**
	* Preenche os itens da propriedade menuModulo
	* @return array itens do menu do modulo
	*/
	public function montarMenuModulo(){
		return controlePadrao::montarMenuModulo();
	}
}
?>
