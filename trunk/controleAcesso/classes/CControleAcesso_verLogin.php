<?php
/**
* Classe de controle
* Ver o login
* @package Sistema
* @subpackage ControleAcesso
*/
class CControleAcesso_verLogin extends controlePadrao{
	/**
	* Método inicial do controle
	*/
	public function inicial(){
		$this->gerarMenuPrincipal();
		$this->registrarInternacionalizacao();
		$this->visualizacao->action = sprintf('?c=%s',definicaoEntidade::controle($this,'validar'));
		$this->visualizacao->login = VComponente::montar('caixa de entrada','login', null);
		$this->visualizacao->login->passarSize(15);
		$this->visualizacao->senha = VComponente::montar('senha','senha', null);
		$this->visualizacao->senha->passarSize(15);
		$this->visualizacao->enviar = VComponente::montar('enviar','enviar', $this->inter->pegarTexto('enviar'));
		$this->visualizacao->mostrar();
		sessaoSistema::encerrar();
	}
	/**
	* Método de validação do controle de acesso
	* @return [booleano] resultado da validação
	*/
	public function validarAcessoAoControle(){
		return true;
	}
}
?>
