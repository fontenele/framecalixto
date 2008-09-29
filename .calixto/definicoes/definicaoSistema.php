<?php
/**
* Classe de definição para o sistema
* @package FrameCalixto
* @subpackage Definição
*/
class definicaoSistema{
	const producao = '1';
	const homologacao = '2';
	const desenvolvimento = '3';
	/**
	* Retorna o nome do sistema
	*/
	static function pegarNome(){
		return strval(definicao::pegarDefinicao()->sistema['nome']);
	}
	/**
	* Retorna o nome detalhado do sistema
	*/
	static function pegarNomeDetalhado(){
		return strval(definicao::pegarDefinicao()->sistema);
	}
	/**
	* Retorna a pagina inicial do sistema
	*/
	static function pegarControleInicial(){
		$controleInicial = strval(definicao::pegarDefinicao()->sistema['paginaInicial']);
		return ($controleInicial)?$controleInicial:'CUtilitario_geradorDefinirSistema';
	}
	/**
	* Retorna a pagina de erro do sistema
	*/
	static function pegarControleErro(){
		return strval(definicao::pegarDefinicao()->sistema['paginaErro']);
	}
	/**
	* Retorna o ambiente do sistema
	*/
	static function pegarAmbiente(){
		return strtolower(strval(definicao::pegarDefinicao()->sistema['ambiente']));
	}
}
?>
