<?php

/**
 * Classe de controle
 * Executa a recuperação de senha
 * @package Sistema
 * @subpackage ControleAcesso
 */
class CUsuario_recuperarSenha extends controlePadraoLiberado {

	public function inicial() {
		try {
			$nUsuario = new NUsuario();
			$nUsuario->filtrarNmLogin(operador::igual($_POST['nmLogin']));
			$coUsuario = $nUsuario->pesquisar();
			$nUsuario = $coUsuario->possuiItens() ? $coUsuario->pegar() : $nUsuario;
			$this->sessao->registrar('dados', $_POST);
			switch (true) {
				case (!$coUsuario->possuiItens()):
					throw new erroAcesso('Usuário não cadastrado');
					break;
				case ($_POST['novaSenha'] != $_POST['confirmaSenha']):
					throw new erroNegocio('Nova senha não confere, redigite a nova senha e a confirmação.');
					break;
				case (!$nUsuario->validarSenha($_POST['senhaEmail'])):
					throw new erroNegocio('A senha enviada para o seu email está inválida.');
					break;
				default:
					$nUsuario->trocarSenha($_POST['novaSenha']);
					if($nUsuario->pegarStatus() == NUsuario::naoConfirmado){
						$nUsuario->passarStatus(NUsuario::ativo);
						$nUsuario->gravar();
					}
					break;
			}
			$this->registrarComunicacao($this->inter->pegarMensagem('senhaRecuperada'),'sucesso');
			$this->passarProximoControle('CControleAcesso_verLogin');
		} catch (erroNegocio $exc) {
			$this->passarProximoControle(definicaoEntidade::controle($this, 'verRecuperarSenha&nmLogin=' . $_POST['nmLogin']));
			throw $exc;
		}
	}

}

?>