<?php
/**
* Classe de representação de uma camada de negócio da entidade 
* A camada de negócio é a parte que engloba as regras e efetua os comandos de execução de um sistema
* @package Sistema
* @subpackage estado
*/
class NEstado extends negocioPadrao{
	/**
	* @var [numerico] id
	*/
	public $id;
	/**
	* @var [texto] Sigla
	*/
	public $sigla;
	/**
	* @var [texto] Estado
	*/
	public $estado;
		/**
	* Retorna o nome da propriedade que contém o valor chave de negócio
	* @return [string] 
	*/
	function nomeChave(){ return 'id'; }
}
?>