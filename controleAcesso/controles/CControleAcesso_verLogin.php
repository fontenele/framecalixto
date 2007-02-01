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
		$this->visualizacaoPadrao->action = sprintf('?c=%s',definicaoEntidade::controle($this,'validar'));
		$this->visualizacaoPadrao->login = VComponente::montar('caixa de entrada','login', null);
		$this->visualizacaoPadrao->senha = VComponente::montar('senha','senha', null);
		$this->visualizacaoPadrao->enviar = VComponente::montar('enviar','enviar', $this->internacionalizacao->pegarTexto('enviar'));
		$this->visualizacaoPadrao->mostrar();
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
