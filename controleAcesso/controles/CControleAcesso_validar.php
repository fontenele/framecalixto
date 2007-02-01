<?php
/**
* Classe de controle
* Processar a valida��o do login
* @package Sistema
* @subpackage Login
*/
class CControleAcesso_validar extends controle{
	/**
	* M�todo inicial do controle
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
	* M�todo de valida��o do controle de acesso
	* @return [booleano] resultado da valida��o
	*/
	public function validarAcessoAoControle(){ 
		return true;
	}
}
?>
