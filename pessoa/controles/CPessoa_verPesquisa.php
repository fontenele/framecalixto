<?php
/**
* Classe de controle
* Cria a visualiza��o da pesquisa de um objeto : Pessoa
* @package Sistema
* @subpackage pessoa
*/
class CPessoa_verPesquisa extends controlePadraoVerPesquisa{
	/**
	* M�todo de cria��o do controle de listagem
	* @return [controle] Um controle especialista em listagem
	*/
	public function criarControleListagem(){
		return new CPessoa_listagem();
	}
}
?>
