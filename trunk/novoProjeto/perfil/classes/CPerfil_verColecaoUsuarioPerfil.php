<?php
/**
* Classe de controle
* Cria a visualização de um objeto : Perfil do Usuario
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