<?php
/**
* Classe de controle
* Cria a visualização da pesquisa de um objeto : Pessoa
* @package Sistema
* @subpackage pessoa
*/
class CPessoa_verPesquisa extends controlePadraoVerPesquisa{
	/**
	* Método de criação do controle de listagem
	* @return [controle] Um controle especialista em listagem
	*/
	public function criarControleListagem(){
		return new CPessoa_listagem();
	}
}
?>
