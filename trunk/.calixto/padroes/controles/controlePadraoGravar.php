<?php
/**
* Classe de definição da camada de controle
* Formação especialista para gravar um objeto de negocio
* @package FrameCalixto
* @subpackage Controle
*/
class controlePadraoGravar extends controlePadrao{
	/**
	 * objeto a ser manipulado
	 * @var negocio
	 */
	protected $negocio;
	/**
	* Método inicial do controle
	*/
	public function inicial(){
		$this->definirProximoControle();
		$this->definirNegocio();
		$this->montarNegocio($this->negocio);
		$this->registrarNegocioNaSessao();
		$this->gravar();
		$this->aposGravar();
		$this->retornarMensagem();
	}
	/**
	 * Define o proximo controle após a finalização da operação
	 */
	public function definirProximoControle(){
		$this->passarProximoControle(definicaoEntidade::controle($this,'verEdicao'));
	}
	/**
	 * Define o objeto de negócio a ser utilizado na operação
	 */
	public function definirNegocio(){
		$negocio = definicaoEntidade::negocio($this);
		$this->negocio = new $negocio();
	}
	/**
	 * Registra na sessão o objeto de negócio após ser montado
	 */
	public function registrarNegocioNaSessao(){
		$this->sessao->registrar('negocio',$this->negocio);
	}
	/**
	 * Realiza a operação de gravação do objeto de negócio
	 */
	public function gravar(){
		$this->negocio->gravar();
	}
	/**
	* Método de tratamento após gravar
	*/
	public function aposGravar(){
		if($this->sessao->tem('negocio')){
			$negocioSessao = $this->sessao->pegar('negocio');
			if(!$negocioSessao->valorChave()) $this->sessao->retirar('negocio');
		}
	}
	/**
	* Método de retorno da da mensagem da operação
	*/
	public function retornarMensagem(){
		if($this->requisicaoAjax()){
			$arRes['mensagem'] = $this->inter->pegarMensagem('gravarSucesso');
			$arRes['id'] = $this->negocio->valorChave();
			$arRes['obj'] = $this->negocio;
			$json = new json();
			echo $json->pegarJson($arRes);
		}else{
			$this->registrarComunicacao($this->inter->pegarMensagem('gravarSucesso'));
		}
	}
}
?>