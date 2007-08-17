<?php
/**
* Classe de controle
* Processar a validação do login
* @package Sistema
* @subpackage Login
*/
class CControleAcesso_validar extends controle{
	/**
	* Método inicial do controle
	*/
	function inicial(){
		try{
			$this->passarProximoControle(definicaoEntidade::controle($this,'verPrincipal'));
			$controleAcesso = new NControleAcesso();
			$controleAcesso->passarLogin($_POST['login']);
			$controleAcesso->passarSenha($_POST['senha']);
			$controleAcesso->validarLogin();
		}
		catch(erro $e){
			throw $e;
		}
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
