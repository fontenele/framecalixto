<?php
/**
* Classe de controle
* Cria a visualização da pesquisa de um objeto : Usuário
* @package Sistema
* @subpackage Usuário
*/
class CUsuario_verPesquisa extends controlePadraoVerPesquisa{
	/**
	* Método de criação do controle de listagem
	* @return controlePadraoListagem Um controle especialista em listagem
	*/
	public function criarControleListagem(){
		return new CUsuario_listagem();
	}
}
?>