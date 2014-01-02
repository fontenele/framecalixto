<?php

/**
 * Classe de controle
 * Ver a recuperação de senha
 * @package Sistema
 * @subpackage ControleAcesso
 */
class CUsuario_verRecuperarSenha extends controlePadraoLiberado {

	/**
	 * Método inicial do controle
	 */
	public function inicial() {
		try {
			if (!isset($_GET['nmLogin']))
				throw new erroNegocio('Email inexistente para a recuperação de senha.', 1);

			$this->gerarMenuPrincipal();
			$this->registrarInternacionalizacao($this, $this->visualizacao);
			$nUsuario = new NUsuario();
			$nUsuario->filtrarNmLogin(operador::igual($_GET['nmLogin']));
			$colecao = $nUsuario->pesquisar(new pagina());
			if ($colecao->possuiItens())
				$nUsuario = $colecao->pegar();
			if (!$nUsuario->pegarSenhaGerada())
				throw new erroNegocio('O email não foi definido para a recuperação de senha.');
			$dados = $this->sessao->tem('dados') ? $this->sessao->pegar('dados') : array(
				'senhaEmail' => '',
				'novaSenha' => '',
				'confirmaSenha' => '',
				'respostaSecreta' => '',
			);

			$this->visualizacao->action = sprintf('?c=%s', definicaoEntidade::controle($this, 'recuperarSenha'));
			$this->visualizacao->btEnviar = VComponente::montar('confirmar', 'btEnviar', $this->inter->pegarTexto('enviar'));

			$this->visualizacao->emailUsuario = $nUsuario->pegarNmLogin();
			$this->visualizacao->emailUsuario.= VComponente::montar(VComponente::oculto, 'nmLogin', $nUsuario->pegarNmLogin());


			$this->visualizacao->senhaEmail = VComponente::montar('senha', 'senhaEmail', $dados['senhaEmail']);
			$this->visualizacao->senhaEmail->passarSize(15);
			$this->visualizacao->senhaEmail->obrigatorio(true);
			$this->visualizacao->senhaEmail->passarTitle('Digite a senha de acesso recebida no seu email');

			$this->visualizacao->novaSenha = VComponente::montar('senha', 'novaSenha', $dados['novaSenha']);
			$this->visualizacao->novaSenha->passarSize(15);
			$this->visualizacao->novaSenha->obrigatorio(true);
			$this->visualizacao->novaSenha->passarTitle('Digite a sua nova senha de acesso');

			$this->visualizacao->confirmaSenha = VComponente::montar('senha', 'confirmaSenha', $dados['confirmaSenha']);
			$this->visualizacao->confirmaSenha->passarSize(15);
			$this->visualizacao->confirmaSenha->obrigatorio(true);
			$this->visualizacao->confirmaSenha->passarTitle('Digite novamente a sua nova senha de acesso');

			$estrutura = controlePadrao::pegarEstrutura($nUsuario);
			parent::inicial();
		} catch (Exception $exc) {
			$this->passarProximoControle('CControleAcesso_verLogin');
			$this->registrarComunicacao($exc->getMessage());
		}
	}

	/**
	 * Método de validação do controle de acesso
	 * @return boolean resultado da validação
	 */
	public function validarAcessoAoControle() {
		return true;
	}

}

?>