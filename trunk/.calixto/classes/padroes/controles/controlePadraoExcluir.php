<?php
/**
* Classe de definição da camada de controle 
* Formação especialista para excluir um objeto de negocio
* @package Infra-estrutura
* @subpackage Controle
*/
class controlePadraoExcluir extends controle{
	/**
	* Método inicial do controle
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
