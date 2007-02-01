<?php
/**
* Classe de controle
* Ver o erro de login
* @package Sistema
* @subpackage ControleAcesso
*/
class CControleAcesso_erroAcesso extends controlePadrao{
	/**
	* M�todo inicial do controle
	*/
	function inicial(){
		$this->gerarMenuPrincipal();
		$this->registrarInternacionalizacao();
		$this->visualizacaoPadrao->mensagemErro = (isset($_GET['mensagemErro'])) ? $_GET['mensagemErro'] : '' ;
		$this->visualizacaoPadrao->mostrar();
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
