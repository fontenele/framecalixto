<?php
/**
* Classe de representação de uma camada de negócio da entidade 
* A camada de negócio é a parte que engloba as regras e efetua os comandos de execução de um sistema
* @package Sistema
* @subpackage curso
*/
class NCurso extends negocioPadrao{
	/**
	* @var [numerico] Código
	*/
	public $codigo;
	/**
	* @var [texto] Nome
	*/
	public $nome;
	/**
	* @var [texto] Tipo
	*/
	public $tipo;
	/**
	* @var [texto] Objetivo
	*/
	public $objetivo;
		/**
	* Retorna o nome da propriedade que contém o valor chave de negócio
	* @return [string] 
	*/
	function nomeChave(){ return 'codigo'; }
}
?>