<?php
/**
* Classe de representação de uma camada de negócio da entidade Menu
* A camada de negócio é a parte que engloba as regras e efetua os comandos de execução de um sistema
* @package Sistema
* @subpackage Menu
*/
class NMenu extends negocioPadraoArvore{
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
	* @gerador variavelPadrao
	* @var string Menu
	*/
	public $menu;
	/**
	* @gerador variavelPadrao
	* @var integer idE
	*/
	public $idE;
	/**
	* @gerador variavelPadrao
	* @var integer idD
	*/
	public $idD;
	/**
	* Retorna o nome da propriedade que contém o valor chave de negócio
	* @gerador metodoPadrao
	* @return string
	*/
	function nomeChave(){ return 'idMenu'; }
	/**
	 * Método que define o nome da chave esquerda da arvore
	 */
	public function nomeChaveEsquerda(){
		return 'idE';
	}
	/**
	 * Método que define o nome da chave direita da arvore
	 */
	public function nomeChaveDireita(){
		return 'idD';
	}
}
?>