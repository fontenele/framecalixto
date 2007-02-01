<?php
/**
* Classe de definição para o sistema
* @package Infra-estrutura
* @subpackage Definição
*/
class definicaoSistema{
	/**
	* Retorna o nome do sistema
	*/
	static function pegarNome(){
		return caracteres(definicao::pegarDefinicao()->sistema['nome']);
	}
	/**
	* Retorna o nome detalhado do sistema
	*/
	static function pegarNomeDetalhado(){
		return caracteres(definicao::pegarDefinicao()->sistema);
	}
	/**
	* Retorna a pagina inicial do sistema
	*/
	static function pegarControleInicial(){
		return caracteres(definicao::pegarDefinicao()->sistema['paginaInicial']);
	}
	/**
	* Retorna a pagina de erro do sistema
	*/
	static function pegarControleErro(){
		return caracteres(definicao::pegarDefinicao()->sistema['paginaErro']);
	}
}
?>
