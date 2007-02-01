<?php
/**
* Classe de representaзгo de uma camada de negуcio da entidade 
* A camada de negуcio й a parte que engloba as regras e efetua os comandos de execuзгo de um sistema
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
	* Retorna o nome da propriedade que contйm o valor chave de negуcio
	* @return [string] 
	*/
	function nomeChave(){ return 'idAtividade'; }
}
?>