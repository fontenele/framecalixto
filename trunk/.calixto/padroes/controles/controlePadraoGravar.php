<?php
/**
* Classe de definição da camada de controle
* Formação especialista para gravar um objeto de negocio
* @package FrameCalixto
* @subpackage Controle
*/
class controlePadraoGravar extends controlePadrao{
	protected $negocio;
	/**
	* Método inicial do controle
	*/
	public function inicial(){
		$this->passarProximoControle(definicaoEntidade::controle($this,'verEdicao'));
		$negocio = definicaoEntidade::negocio($this);
		$this->negocio = new $negocio();
		$this->montarNegocio($this->negocio);
		$this->sessao->registrar('negocio',$this->negocio);
		$this->negocio->gravar();
		if($this->sessao->tem('negocio')){
			$negocioSessao = $this->sessao->pegar('negocio');
			if(!$negocioSessao->valorChave()) $this->sessao->retirar('negocio');
		}
		$this->registrarComunicacao($this->inter->pegarMensagem('gravarSucesso'));
	}
}
?>
