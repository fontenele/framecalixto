<?php
/**
* Classe de controle
* Ver o erro de login
* @package Sistema
* @subpackage ControleAcesso
*/
class CControleAcesso_erroAcesso extends controlePadrao{
	/**
	* Método inicial do controle
	*/
	function inicial(){
		$this->gerarMenuPrincipal();
		$this->gerarMenuModulo();
		$this->registrarInternacionalizacao();
		$this->visualizacao->mensagemErro = (isset($_GET['mensagemErro'])) ? $_GET['mensagemErro'] : '' ;
		$this->visualizacao->mostrar();
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
