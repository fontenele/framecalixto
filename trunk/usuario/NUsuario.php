<?php
/**
* Classe de representaзгo de uma camada de negуcio da entidade 
* A camada de negуcio й a parte que engloba as regras e efetua os comandos de execuзгo de um sistema
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
	* Retorna o nome da propriedade que contйm o valor chave de negуcio
	* @return [string] 
	*/
	function nomeChave(){ return 'idUsuario'; }
}
?>