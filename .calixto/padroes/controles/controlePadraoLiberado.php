<?php
/**
* Classe de definição da camada de controle
* @package FrameCalixto
* @subpackage Controle
*/
class controlePadraoLiberado extends controlePadrao{
	/**
	* Método inicial do controle
	*/
	function inicial(){
		try{
			$this->registrarInternacionalizacao($this,$this->visualizacao);
			$this->gerarMenus();
			$this->visualizacao->mostrar();
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Preenche os itens da propriedade menuPrincipal
	* @return array itens do menu principal
	*/
	public function montarMenuPrincipal(){
		return new colecaoPadraoMenu();
	}
	/**
	* Preenche os itens da propriedade menuModulo
	* @return array itens do menu do modulo
	*/
	public function montarMenuModulo(){
		return new colecaoPadraoMenu();
	}
	/**
	* Preenche os itens da propriedade menuPrograma
	* @return array itens do menu do programa
	*/
	public function montarMenuPrograma(){
		return new colecaoPadraoMenu();
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
