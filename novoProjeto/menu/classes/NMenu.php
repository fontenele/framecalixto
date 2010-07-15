<?php
/**
* Classe de representação de uma camada de negócio da entidade Menu
* A camada de negócio é a parte que engloba as regras e efetua os comandos de execução de um sistema
* @package Sistema
* @subpackage Menu
*/
class NMenu extends negocioPadrao{
	/**
	* @gerador variavelPadrao
	* @var integer Código
	*/
	public $idMenu;
	/**
	* @gerador variavelPadrao
	* @var string Nome
	*/
	public $nmMenu;
	/**
	* @gerador variavelPadrao
	* @var string Descrição
	*/
	public $txDescricao;
	/**
	* Retorna o nome da propriedade que contém o valor chave de negócio
	* @gerador metodoPadrao
	* @return string
	*/
	function nomeChave(){ return 'idMenu'; }
}
?>