<?php
/**
* Classe de defini��o da camada de controle 
* Forma��o especialista para excluir um objeto de negocio
* @package Infra-estrutura
* @subpackage Controle
*/
class controlePadraoExcluir extends controle{
	/**
	* M�todo inicial do controle
	*/
	public function inicial(){
		$this->passarProximoControle(definicaoEntidade::controle($this,'verPesquisa'));
		$negocio = definicaoEntidade::negocio($this);
		$negocio = new $negocio();
		$negocio->valorChave($_GET['chave']);
		$negocio->excluir();
		$this->registrarComunicacao($this->internacionalizacao->pegarMensagem('excluirSucesso'));
	}
}
?>
