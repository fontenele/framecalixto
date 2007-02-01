<?php
/**
* Classe de representa��o de uma camada de neg�cio da entidade 
* A camada de neg�cio � a parte que engloba as regras e efetua os comandos de execu��o de um sistema
* @package Sistema
* @subpackage usuario
*/
class NUsuario extends negocioPadrao{
	/**
	* @var [numerico] Id Usuario
	*/
	public $idUsuario;
	/**
	* @var [numerico] Id Pessoa
	*/
	public $idPessoa;
	/**
	* @var [texto] Nm Usuario
	*/
	public $nmUsuario;
	/**
	* @var [texto] senha
	*/
	public $senha;
	/**
	* @var [texto] status
	*/
	public $status;
	/**
	* @var [data] Dt Cadastro
	*/
	public $dtCadastro;
		/**
	* Retorna o nome da propriedade que cont�m o valor chave de neg�cio
	* @return [string] 
	*/
	function nomeChave(){ return 'idUsuario'; }
}
?>