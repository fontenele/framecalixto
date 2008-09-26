<?php
/**
* Classe de controle
* Cria a visualização da pesquisa de um objeto : Perfil
* @package Sistema
* @subpackage Perfil
*/
class CPerfil_verPesquisa extends controlePadraoVerPesquisa{
	/**
	* Método de criação do controle de listagem
	* @return controlePadraoListagem Um controle especialista em listagem
	*/
	public function criarControleListagem(){
		return new CPerfil_listagem();
	}
}
?>