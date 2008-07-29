<?php
/**
* Classe de controle
* Cria a visualização de um objeto : Pessoa
* @package Sistema
* @subpackage pessoa
*/
class CPessoa_verCartao extends controlePadraoPDF{
	/**
	* Método inicial do controle
	*/
	public function inicial(){
		$negocio = 'NPessoa';
		$negocio = new $negocio();
		$colecao = $negocio->lerTodos();
		$this->adicionarPagina();

		while($nNegocio = $colecao->avancar()){
			$this->celula(100,0,$nNegocio->valorDescricao());
			$this->ln();
		}
		$this->mostrar();

	}
}
?>