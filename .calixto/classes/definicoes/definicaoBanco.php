<?php
/**
* Classe de definição para o banco de dados
* @package Infra-estrutura
* @subpackage Definição
*/
class definicaoBanco{
	/**
	* Retorna o tipo do banco
	*/
	static final function pegarTipo(){
		return strval(definicao::pegarDefinicao()->banco['tipo']);
	}
	/**
	* Retorna o nome servidor
	*/
	static final function pegarServidor(){
		return strval(definicao::pegarDefinicao()->banco['servidor']);
	}
	/**
	* Retorna a porta do banco
	*/
	static final function pegarPorta(){
		return strval(definicao::pegarDefinicao()->banco['porta']);
	}
	/**
	* Retorna o nome do banco
	*/
	static final function pegarNome(){
		return strval(definicao::pegarDefinicao()->banco['nome']);
	}
	/**
	* Retorna o usuario
	*/
	static final function pegarUsuario(){
		return strval(definicao::pegarDefinicao()->banco['usuario']);
	}
	/**
	* Retorna a senha
	*/
	static final function pegarSenha(){
		return strval(definicao::pegarDefinicao()->banco['senha']);
	}
}
?>
