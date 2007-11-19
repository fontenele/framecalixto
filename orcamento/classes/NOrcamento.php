<?php
/**
* Classe de representação de uma camada de negócio da entidade
* A camada de negócio é a parte que engloba as regras e efetua os comandos de execução de um sistema
* @package Sistema
* @subpackage orcamento
*/
class NOrcamento extends negocioPadrao{
	/**
	* @var [numerico] Orçamento
	*/
	public $idOrcamento;
	/**
	* @var [numerico] Usuario
	*/
	public $idUsuario;
	/**
	* @var [numerico] Tarefa
	*/
	public $idTarefa;
	/**
	* @var [numerico] Item
	*/
	public $idItem;
	/**
	* @var [texto] Observação
	*/
	public $observacao;
	/**
	* Retorna o nome da propriedade que contém o valor chave de negócio
	* @return [string]
	*/
	function nomeChave(){ return 'idOrcamento'; }
}
?>