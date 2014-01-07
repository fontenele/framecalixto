<?php
/**
* Classe de representação de uma camada de negócio da entidade Pessoa usuário
* A camada de negócio é a parte que engloba as regras e efetua os comandos de execução de um sistema
* @package Sistema
* @subpackage Pessoa usuário
*/
class NPessoaUsuario extends negocioPadrao{
	/**
	* @gerador variavelPadrao
	* @var integer Identificador
	*/
	public $idPessoaUsuario;
	/**
	* @gerador variavelPadrao
	* @var integer Pessoa
	*/
	public $idPessoa;
	/**
	* @gerador variavelPadrao
	* @var integer Usuário
	*/
	public $idUsuario;
	/**
	* Retorna o nome da propriedade que contém o valor chave de negócio
	* @gerador metodoPadrao
	* @return string
	*/
	function nomeChave(){ return 'idPessoaUsuario'; }
}
?>