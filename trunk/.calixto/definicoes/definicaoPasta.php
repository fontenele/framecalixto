<?php
/**
* Classe de definição para arquivos
* @package FrameCalixto
* @subpackage Definição
*/
class definicaoPasta{
	protected static $pastas;
	protected static $pastasDeEntidade;
	protected static function pegarPastas(){
		if(definicaoPasta::$pastas) return definicaoPasta::$pastas;
		foreach(definicao::pegarDefinicao()->diretorios->diretorio as $dir){
			definicaoPasta::$pastas[strval($dir['id'])] = strval($dir['dir']);
			definicaoPasta::$pastasDeEntidade[strval($dir['id'])] = (strval($dir['entidade']) == 'sim');
		}
		return definicaoPasta::$pastas;
	}
	/**
	* Retorna o nome da pasta temporaria do sistema
	*/
	static final function temporaria(){
		definicaoPasta::pegarPastas();
		return definicaoPasta::$pastas['temporario'];
	}
	/**
	* Retorna o nome da pasta de tema do sistema
	*/
	static final function tema(){
		definicaoPasta::pegarPastas();
		return definicaoPasta::$pastas['tema'];
	}
	/**
	* Retorna o nome da pasta de templates das entidades
	*/
	static final function templates($entidade = null){
		definicaoPasta::pegarPastas();
		if(definicaoPasta::$pastasDeEntidade['templates'] && $entidade){
			return definicaoEntidade::entidade($entidade).'/'.definicaoPasta::$pastas['templates'].'/';
		}else{
			return definicaoPasta::$pastas['templates'];
		}
	}
	/**
	* Retorna o nome da pasta de css das entidades
	*/
	static final function css($entidade = null){
		definicaoPasta::pegarPastas();
		if(definicaoPasta::$pastasDeEntidade['css'] && $entidade){
			return definicaoEntidade::entidade($entidade).'/'.definicaoPasta::$pastas['css'].'/';
		}else{
			return definicaoPasta::$pastas['css'];
		}
	}
	/**
	* Retorna o nome da pasta de js das entidades
	*/
	static final function js($entidade = null){
		definicaoPasta::pegarPastas();
		if(definicaoPasta::$pastasDeEntidade['js'] && $entidade){
			return definicaoEntidade::entidade($entidade).'/'.definicaoPasta::$pastas['js'].'/';
		}else{
			return definicaoPasta::$pastas['js'];
		}
	}
}
?>