<?php
/**
* Classe de controle
* Ver o Usuário
* @package Sistema
* @subpackage Atualizador de Base de Dados
*/
class CUtilitario_testeTransacao extends controlePadrao{
	/**
	* Método inicial do controle
	*/
	function inicial(){
		try{
			$c = conexao::criar();
			$c->iniciarTransacao();
			$pessoa = new NPessoa($c);
			$pessoa->passarNmPessoa('Ze Tonho');
			$pessoa->passarEndereco('CSA 02');
			$pessoa->passarTelefone('35627755');
			$pessoa->passarCsPessoa('FI');
			$pessoa->gravar();
			
			$usuario = new NUsuario($c);
			$usuario->passarIdPessoa($pessoa->pegarIdPessoa());
			$usuario->passarNmUsuario('tonho');
			// $usuario->passarSenha('123');
			$usuario->passarStatus('A');
			$usuario->gravar();
			$c->validarTransacao();
			$c->fechar();
		}
		catch(erro $e){
			$c->desfazerTransacao();
			$c->fechar();
			x($e);die($e->__toString());
		}
	}
}
?>
