<?php

/**
 * Classe de controle
 * Ver a ação "Esqueci a senha"
 * @package Sistema
 * @subpackage ControleAcesso
 */
class CUsuario_verEsqueciSenha extends controlePadraoLiberado {
	/**
	 * @var internacionalizacaoPadrao
	 */
	protected $inter;
	/**
	 * Método inicial do controle
	 */
	public function inicial() {
		sessaoSistema::encerrar();
		$this->gerarMenus();
		$this->registrarInternacionalizacao($this, $this->visualizacao);
		$this->visualizacao->descricaoDeAjuda = $this->inter->pegarMensagem('ajudaEsqueciSenha');
		$this->visualizacao->action = sprintf('?c=%s',definicaoEntidade::controle($this,'gerarNovaSenha'));
		parent::inicial();
	}

	/**
	 * Método de validação do controle de acesso
	 * @return boolean resultado da validação
	 */
	public function validarAcessoAoControle() {
		return true;
	}
}
