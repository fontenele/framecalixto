<?php
/**
* Classe de defini��o para arquivos
* @package Infra-estrutura
* @subpackage Defini��o
*/
class definicaoPasta{
	/**
	* Retorna o nome da pasta temporaria do sistema
	*/
	static final function pegarTemporaria(){
		return caracteres(definicao::pegarDefinicao()->diretorios->diretorio["dir"]);
	}
}
?>
