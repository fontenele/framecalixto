<?php
/**
* Classe de representação de uma camada de negócio da entidade Menu Item
* A camada de negócio é a parte que engloba as regras e efetua os comandos de execução de um sistema
* @package Sistema
* @subpackage Menu Item
*/
class NMenuItem extends negocioPadrao{
	/**
	* @gerador variavelPadrao
	* @var integer Código
	*/
	public $idMenuItem;
	/**
	* @gerador variavelPadrao
	* @var integer Item Pai
	*/
	public $idPai;
	/**
	* @gerador variavelPadrao
	* @var integer Menu
	*/
	public $idMenu;
	/**
	* @gerador variavelPadrao
	* @var integer Posição
	*/
	public $nrPosicao;
	/**
	* @gerador variavelPadrao
	* @var string URL
	*/
	public $txUrl;
	/**
	* @gerador variavelPadrao
	* @var string Alvo
	*/
	public $nmAlvo;
	/**
	* @gerador variavelPadrao
	* @var string Imagem
	*/
	public $txImagem;
	/**
	* @gerador variavelPadrao
	* @var string Destravado
	*/
	public $boDestravado;
	/**
	* @gerador variavelPadrao
	* @var string Nome
	*/
	public $nmMenuItem;
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
	function nomeChave(){ return 'idMenuItem'; }
}
?>