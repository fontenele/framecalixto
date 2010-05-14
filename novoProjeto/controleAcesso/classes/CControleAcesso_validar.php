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
			$this->passarProximoControle(definicaoEntidade::controle($this,'verLogin'));
			$controleAcesso = new NControleAcesso();
			$controleAcesso->passarNmLogin($_POST['nmLogin']);
			$controleAcesso->passarNmSenha(md5($_POST['nmSenha']));
			$controleAcesso->validarLogin();
			$this->registrarComunicacao($this->inter->pegarMensagem('usuarioLogado'));
			$this->passarProximoControle(definicaoEntidade::controle($this,'verPrincipal'));
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Método de validação do controle de acesso
	* @return boolean resultado da validação
	*/
	public function validarAcessoAoControle(){
		return true;
	}
}
?>
