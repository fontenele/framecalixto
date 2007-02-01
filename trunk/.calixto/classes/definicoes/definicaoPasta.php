<?php
/**
* Classe de definição para arquivos
* @package Infra-estrutura
* @subpackage Definição
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
