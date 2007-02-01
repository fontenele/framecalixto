<?php
/**
* Classe de defini��o da camada de controle
* @package Infra-estrutura
* @subpackage Controle
*/
class controlePadraoLiberado extends controlePadrao{
	/**
	* M�todo inicial do controle
	*/
	function inicial(){
		try{
			$this->registrarInternacionalizacao();
			$this->gerarMenus();
			$this->visualizacaoPadrao->mostrar();
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Preenche os itens da propriedade menuPrincipal
	* @return [array] itens do menu principal
	*/
	public function montarMenuPrincipal(){
		return array();
	}
	/**
	* Preenche os itens da propriedade menuModulo
	* @return [array] itens do menu do modulo
	*/
	public function montarMenuModulo(){
		return array();
	}
	/**
	* Preenche os itens da propriedade menuPrograma
	* @return [array] itens do menu do programa
	*/
	public function montarMenuPrograma(){
		return array();
	}
	/**
	* M�todo de valida��o do controle de acesso
	* @return [booleano] resultado da valida��o
	*/
	public function validarAcessoAoControle(){ 
		return true;
	}
}
?>
