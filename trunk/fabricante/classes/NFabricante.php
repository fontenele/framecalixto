<?php
/**
* Classe de representação de uma camada de negócio da entidade 
* A camada de negócio é a parte que engloba as regras e efetua os comandos de execução de um sistema
* @package Sistema
* @subpackage fabricante
*/
class NFabricante extends negocioPadrao{
	/**
	* @var [numerico] id
	*/
	public $id;
	/**
	* @var [numerico] cod
	*/
	public $cod;
	/**
	* @var [texto] nm
	*/
	public $nm;
		/**
	* Retorna o nome da propriedade que contém o valor chave de negócio
	* @return [string] 
	*/
	function nomeChave(){ return 'id'; }
}
?>