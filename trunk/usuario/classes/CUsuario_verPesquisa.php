<?php
/**
* Classe de controle
* Cria a visualização da pesquisa de um objeto : Usuario
* @package Sistema
* @subpackage usuario
*/
class CUsuario_verPesquisa extends controlePadraoVerPesquisa{
	/**
	* Método de criação do controle de listagem
	* @return [controle] Um controle especialista em listagem
	*/
	public function criarControleListagem(){
		return new CUsuario_listagem();
	}
}
?>
