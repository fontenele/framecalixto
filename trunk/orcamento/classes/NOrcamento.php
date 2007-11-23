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
	public $orcamento;
	/**
	* @var [numerico] Usuário
	*/
	public $usuario;
	/**
	* @var [numerico] Tarefa
	*/
	public $idTarefa;
	/**
	* @var [numerico] Item
	*/
	public $idItem;
	/**
	* @var [numerico] Quantidade
	*/
	public $quantidade;
	/**
	* @var [texto] Observação
	*/
	public $observacao;
	/**
	* Retorna o nome da propriedade que contém o valor chave de negócio
	* @return [string]
	*/
	function nomeChave(){ return 'orcamento'; }
}
?>