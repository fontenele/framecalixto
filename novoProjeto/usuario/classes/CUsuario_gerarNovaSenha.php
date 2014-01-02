<?php

/**
 * Classe de controle
 * Executa a geração de uma nova senha para o usuário
 * @package Sistema
 * @subpackage ControleAcesso
 */
class CUsuario_gerarNovaSenha extends controlePadraoLiberado {

	/**
	 * @var NUsuario
	 */
	protected $negocio;

	/**
	 * Método inicial do controle
	 */
	function inicial() {
		$server = explode('?',$_SERVER['HTTP_REFERER']);
		$link = $server[0].'?c=CUsuario_verRecuperarSenha&nmLogin=';
		
		$this->passarProximoControle('CControleAcesso_verLogin');
		$this->negocio = new NUsuario();
		$this->negocio->filtrarNmLogin(operador::igual(sessaoSistema::tem('usuario') ? sessaoSistema::pegar('usuario')->pegarNmLogin() : $_POST['emailUsuario']));
		$coUsuario = $this->negocio->pesquisar();
		if (!$coUsuario->possuiItens())
			throw new erroNegocio($this->inter->pegarMensagem('usuarioNaoCadastrado'));
		$this->negocio = $coUsuario->pegar();
		$this->negocio->gerarNovaSenha($link.$this->negocio->pegarNmLogin());
		$this->retornarMensagem();
	}

	/**
	 * Método de retorno da da mensagem da operação
	 */
	public function retornarMensagem() {
		if ($this->requisicaoAjax()) {
			$arRes['mensagem'] = $this->inter->pegarMensagem('novaSenhaGerada');
			$arRes['id'] = $this->negocio->valorChave();
			$arRes['obj'] = $this->negocio;
			$json = new json();
			echo $json->pegarJson($arRes);
			die;
		} else {
			$this->registrarComunicacao($this->inter->pegarMensagem('novaSenhaGerada'),'sucesso');
		}
	}

}
