<?php
/**
* Classe de controle
* Visualiza os perfís relacionados a um usuário
* @package Sistema
* @subpackage Usuario
*/
class CUsuario_verColecaoUsuarioPerfil extends controlePadraoVerColecao{
	/**
	 * Método que define a coleção oposta a ser apresentada na listagem de dados
	 */
	public function definirColecaoOposta(){
		$negocio = new NPerfil();
		$this->colecaoOposta = $negocio->lerTodos();
	}
}
?>