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
		sessaoSistema::encerrar();
		$this->gerarMenuPrincipal();
		$this->registrarInternacionalizacao($this,$this->visualizacao);
		$this->visualizacao->action = sprintf('?c=%s',definicaoEntidade::controle($this,'validar'));
		$this->visualizacao->login = VComponente::montar('caixa de entrada','login', null);
		$this->visualizacao->login->passarSize(15);
		$this->visualizacao->login->obrigatorio = true;
		$this->visualizacao->senha = VComponente::montar('senha','senha', null);
		$this->visualizacao->senha->passarSize(15);
		$this->visualizacao->senha->obrigatorio = true;
		$this->visualizacao->enviar = VComponente::montar('confirmar','enviar', $this->inter->pegarTexto('enviar'));
		$this->visualizacao->mostrar();
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