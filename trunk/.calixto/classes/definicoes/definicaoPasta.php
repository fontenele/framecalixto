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
	static final function temporaria(){
		return caracteres(definicao::pegarDefinicao()->diretorios->diretorio[0]["dir"]);
	}
	/**
	* Retorna o nome da pasta de templates das entidades
	*/
	static final function templates($entidade = null){
		if(strtolower(caracteres(definicao::pegarDefinicao()->diretorios->diretorio[1]["entidade"])) == 'sim'){
			return definicaoEntidade::entidade($entidade).'/'.caracteres(definicao::pegarDefinicao()->diretorios->diretorio[1]["dir"]).'/';
		}else{
			return caracteres(definicao::pegarDefinicao()->diretorios->diretorio[1]["dir"]).'/';
		}
	}
	/**
	* Retorna o nome da pasta de css das entidades
	*/
	static final function css($entidade = null){
		if(strtolower(caracteres(definicao::pegarDefinicao()->diretorios->diretorio[2]["entidade"])) == 'sim'){
			return definicaoEntidade::entidade($entidade).'/'.caracteres(definicao::pegarDefinicao()->diretorios->diretorio[2]["dir"]).'/';
		}else{
			return caracteres(definicao::pegarDefinicao()->diretorios->diretorio[2]["dir"]).'/';
		}
	}
}
?>
