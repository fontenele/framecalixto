<?php
/**
* Classe de controle
* Visualiza os usuário relacionados a um perfil
* @package Sistema
* @subpackage Perfil
*/
class CPerfil_verColecaoUsuarioPerfil extends controlePadraoVerColecao{
	/**
	 * Método que define a coleção oposta a ser apresentada na listagem de dados
	 */
	public function definirColecaoOposta(){
		$negocio = new NUsuario();
		$this->colecaoOposta = $negocio->lerTodos();
	}
}
?>