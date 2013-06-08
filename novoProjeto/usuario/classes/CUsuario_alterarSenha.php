<?php
/**
* Classe de controle
* Executa a alteração de senha de um Usuário
* @package Sistema
* @subpackage Usuário
*/
class CUsuario_alterarSenha extends controlePadraoGravar{
	/**
	 * Realiza a operação de gravação do objeto de negócio
	 */
	public function gravar(){
		sessaoSistema::pegar('usuario')->alterarSenha(
				$_POST['senha_atual'],
				$_POST['nova_senha'],
				$_POST['nova_senha_conf']);
	}
	/**
	 * Define o proximo controle após a finalização da operação
	 */
	public function definirProximoControle(){
		$this->passarProximoControle(definicaoEntidade::controle($this,'verAlterarSenha'));
	}
	/**
	* Método de tratamento após gravar
	*/
	public function aposGravar(){
		$this->passarProximoControle('CControleAcesso_verPrincipal');
	}
	/**
	 * Registra na sessão o objeto de negócio após ser montado
	 */
	public function registrarNegocioNaSessao(){}
}
?>