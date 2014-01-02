<?php

/**
 * Classe de controle
 * Executa a gravação de um objeto : Usuário
 * @package Sistema
 * @subpackage Usuário
 */
class CUsuario_gravarNovo extends controlePadraoGravar {

	/**
	 * Método de utilização dos dados postados para a montagem do negocio
	 * @param negocio objeto para preenchimento
	 * @param array $dados
	 */
	public static function montarNegocio(negocio $negocio, $dados = null) {
		parent::montarNegocio($negocio, $dados['usuario']);
		$negocio->passarDataSolicitacao(TDataHora::agora());
		//parent::montarNegocio($negocio->nPessoa,$dados['pessoa']);
		$negocio->passarStatus(NUsuario::naoConfirmado);
		$negocio->encriptarNmSenha();
	}

	/**
	 * Método de retorno da da mensagem da operação
	 */
	public function retornarMensagem() {
		$this->registrarComunicacao($this->inter->pegarMensagem('gravarNovoSucesso'), 'sucesso');
	}

	/**
	 * Define o proximo controle após a finalização da operação
	 */
	public function definirProximoControle() {
		$this->passarProximoControle(definicaoEntidade::controle($this, 'verNovo'));
	}

	/**
	 * Realiza a operação de gravação do objeto de negócio
	 */
	public function gravar() {
		if ($_POST["usuario"]["nmSenha"] != $_POST["usuario"]["nmSenhaC"])
			throw new erroNegocio("Senha nao confere!");
		parent::gravar();
	}

	/**
	 * Método de tratamento após gravar
	 */
	public function aposGravar() {
		$server = explode('?', $_SERVER['HTTP_REFERER']);
		$link = $server[0] . '?c=CUsuario_confirmarCadastro&chave=' . caracteres::encrypt($this->negocio->valorChave(), 8);
		parent::aposGravar();
		$this->passarProximoControle('CControleAcesso_verPrincipal');
		$email = new emailSistema();
		$email->addEmailDestinatario($_POST['usuario']['nmLogin'], $_POST['usuario']['nmLogin']);
		$email->passarAssunto($this->inter->pegarMensagem('assuntoBemVindo'));
		$email->passarConteudo(sprintf(
						$this->inter->pegarMensagem('conteudoBemVindo'), $_POST['usuario']['nmLogin'], $link
		));
		$email->enviar();
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
