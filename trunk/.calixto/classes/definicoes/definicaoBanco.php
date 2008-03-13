<?php
/**
* Classe de definição para o banco de dados
* @package Infra-estrutura
* @subpackage Definição
*/
class definicaoBanco{
	/**
	* Retorna o identificador do banco pelo nome
	* @param [string] nome da conexao
	*/
	static final function pegarId($nome = false){
		foreach (definicao::pegarDefinicao()->xpath('/definicoes/bancos/banco') as $id => $banco){
			if(strval($banco['id']) == $nome){
				return $id;
			}
		}
		return 0;
	}
	/**
	* Retorna o tipo do banco
	* @param [numerico] identificador da conexao
	*/
	static final function pegarTipo($id = 0){
		return strval( definicao::pegarDefinicao()->bancos->banco[$id]['tipo'] );
	}
	/**
	* Retorna o nome servidor
	* @param [numerico] identificador da conexao
	*/
	static final function pegarServidor($id = 0){
		return strval( definicao::pegarDefinicao()->bancos->banco[$id]['servidor'] );
	}
	/**
	* Retorna a porta do banco
	* @param [numerico] identificador da conexao
	*/
	static final function pegarPorta($id = 0){
		return strval(definicao::pegarDefinicao()->bancos->banco[$id]['porta']);
	}
	/**
	* Retorna o nome do banco
	* @param [numerico] identificador da conexao
	*/
	static final function pegarNome($id = 0){
		return strval(definicao::pegarDefinicao()->bancos->banco[$id]['nome']);
	}
	/**
	* Retorna o usuario
	* @param [numerico] identificador da conexao
	*/
	static final function pegarUsuario($id = 0){
		return strval(definicao::pegarDefinicao()->bancos->banco[$id]['usuario']);
	}
	/**
	* Retorna a senha
	* @param [numerico] identificador da conexao
	*/
	static final function pegarSenha($id = 0){
		return strval(definicao::pegarDefinicao()->bancos->banco[$id]['senha']);
	}
	/**
	* Retorna se a conexão é multipla
	* @param [numerico] identificador da conexao
	*/
	static final function conexaoMultipla($id = 0){
		return strval(definicao::pegarDefinicao()->bancos->banco[$id]['conexaoMultipla']) == 'sim';
	}
}
?>