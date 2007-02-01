<?php
/**
* Classe de representa��o de uma camada de neg�cio da entidade 
* A camada de neg�cio � a parte que engloba as regras e efetua os comandos de execu��o de um sistema
* @package Sistema
* @subpackage atividade
*/
class NAtividade extends negocioPadrao{
	/**
	* @var [numerico] Id Atividade
	*/
	public $idAtividade;
	/**
	* @var [numerico] Id Tarefa
	*/
	public $idTarefa;
	/**
	* @var [texto] Ds Atividade
	*/
	public $dsAtividade;
	/**
	* @var [data] Dt Inicio
	*/
	public $dtInicio;
	/**
	* @var [data] Dt Fim
	*/
	public $dtFim;
		/**
	* Retorna o nome da propriedade que cont�m o valor chave de neg�cio
	* @return [string] 
	*/
	function nomeChave(){ return 'idAtividade'; }
}
?>