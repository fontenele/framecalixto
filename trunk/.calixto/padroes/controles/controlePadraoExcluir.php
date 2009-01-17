<?php
/**
* Classe de definição da camada de controle 
* Formação especialista para excluir um objeto de negocio
* @package FrameCalixto
* @subpackage Controle
*/
class controlePadraoExcluir extends controlePadrao{
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
		$this->excluir();
		$this->aposExcluir();
		$this->retornarMensagem();
	}
	/**
	 * Define o proximo controle após a finalização da operação
	 */
	public function definirProximoControle(){
		$this->passarProximoControle(definicaoEntidade::controle($this,'verPesquisa'));
	}
	/**
	 * Define o objeto de negócio a ser utilizado na operação
	 */
	public function definirNegocio(){
		$negocio = definicaoEntidade::negocio($this);
		$this->negocio = new $negocio();
	}
	/**
	* Método de utilização dos dados postados para a montagem do negocio
	* @param [negocio] objeto para preenchimento
	* @param array $dados
	*/
	public static function montarNegocio(negocio $negocio,$dados = null){
		$negocio->valorChave($_GET['chave']);
	}
	/**
	 * Realiza a operação de eclusão do objeto de negócio
	 */
	public function excluir(){
		$this->negocio->excluir();
	}
	/**
	* Método de tratamento após gravar
	*/
	public function aposExcluir(){}
	/**
	* Método de retorno da da mensagem da operação
	*/
	public function retornarMensagem(){
		if($this->requisicaoAjax()){
			$arRes['mensagem'] = $this->inter->pegarMensagem('excluirSucesso');
			$arRes['id'] = $this->negocio->valorChave();
			$arRes['obj'] = $this->negocio;
			$json = new json();
			echo $json->pegarJson($arRes);
		}else{
			$this->registrarComunicacao($this->inter->pegarMensagem('excluirSucesso'));
		}
	}
}
?>
