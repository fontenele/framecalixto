<?php
/**
* Classe de representação de uma camada de negócio da entidade 
* A camada de negócio é a parte que engloba as regras e efetua os comandos de execução de um sistema
* @package Sistema
* @subpackage item
*/
class NItem extends negocioPadrao{
	/**
	* @var [numerico] idItem
	*/
	public $iditem;
	/**
	* @var [texto] Nome
	*/
	public $nmItem;
	/**
	* @var [numerico] Tipo
	*/
	public $csItem;
	/**
	* @var [numerico] Valor
	*/
	public $vlItem;
		/**
	* Retorna o nome da propriedade que contém o valor chave de negócio
	* @return [string] 
	*/
	function nomeChave(){ return 'iditem'; }
}
?>