<?php
/**
* Classe de controle
* Ver o login
* @package Sistema
* @subpackage ControleAcesso
*/
class CControleAcesso_verLogin extends controlePadrao{
	/**
	* M�todo inicial do controle
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
	* M�todo de valida��o do controle de acesso
	* @return [booleano] resultado da valida��o
	*/
	public function validarAcessoAoControle(){ 
		return true;
	}
}
?>
