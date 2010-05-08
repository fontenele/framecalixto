<?php
/**
* Classe de representação de uma camada de negócio da entidade Estado
* A camada de negócio é a parte que engloba as regras e efetua os comandos de execução de um sistema
* @package Sistema
* @subpackage Estado
*/
class NEstado extends negocioPadrao{
	/**
	* @gerador variavelPadrao
	* @var integer id
	*/
	public $idEstado;
	/**
	* @gerador variavelPadrao
	* @var string Sigla
	*/
	public $sgSigla;
	/**
	* @gerador variavelPadrao
	* @var string Estado
	*/
	public $nmEstado;
	/**
	* Retorna o nome da propriedade que contém o valor chave de negócio
	* @gerador metodoPadrao
	* @return string
	*/
	function nomeChave(){ return 'idEstado'; }
}
?>