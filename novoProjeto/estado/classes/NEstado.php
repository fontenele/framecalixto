<?php
/**
* Classe de representação de uma camada de negócio da entidade 
* A camada de negócio é a parte que engloba as regras e efetua os comandos de execução de um sistema
* @package Sistema
* @subpackage estado
*/
class NEstado extends negocioPadrao{
	/**
	* @var integer id
	*/
	public $idEstado;
	/**
	* @var string Sigla
	*/
	public $sgSigla;
	/**
	* @var string Estado
	*/
	public $nmEstado;
	/**
	* Retorna o nome da propriedade que contém o valor chave de negócio
	* @return string
	*/
	public function nomeChave(){ return 'idEstado'; }
}
?>