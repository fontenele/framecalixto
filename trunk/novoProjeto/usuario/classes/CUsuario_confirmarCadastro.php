<?php

/**
 * Classe de controle
 * Confirma a criação de um Usuário no sistema
 * @package Sistema
 * @subpackage Usuário
 */
class CUsuario_confirmarCadastro extends controlePadrao {

	/**
	 * Método de inicialização do controle
	 */
	public function inicial() {
		$this->passarProximoControle('CControleAcesso_verPrincipal');
		if (!isset($_GET['chave']))
			throw new erroNegocio($this->inter->pegarMensagem('emailChaveInvalida'));
		$nUsuario = new NUsuario();
		$nUsuario->ler(caracteres::decrypt($_GET['chave'], 8));
		if (!$nUsuario->valorChave())
			throw new erroNegocio($this->inter->pegarMensagem('usuarioNaoExiste'));
		if (($nUsuario->pegarStatus() != NUsuario::naoConfirmado))
			throw new erroNegocio($this->inter->pegarMensagem('emailChaveJaConfirmada'));
		$nUsuario->passarStatus(NUsuario::ativo);
                $nUsuario->passarDataConfirmacao(TDataHora::agora());
		$nUsuario->gravar();
		sessaoSistema::registrar('usuario', $nUsuario);
		$this->registrarComunicacao($this->inter->pegarMensagem('emailConfirmado'), 'sucesso');
	}

}

?>