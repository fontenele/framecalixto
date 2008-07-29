<?php
/**
* Classe de reprensação de arquivo
* Esta classe representa numerico no formato de moeda
* @package FrameCalixto
* @subpackage tipoDeDados
*/
class TMoeda extends TNumerico{
	/**
	* metodo construtor do numerico
	* @param [string] numero formatado
	* @param [string] character separador de decimal
	* @param [string] character separador de milhar
	* @param [nr] número de casas decimais
	* @param [string] simbolo do número (Unidade de Medida)
	* @param [string] identificador da posição do simbolo 'E' para esquerda, 'D' para direita
	*/
	public function __construct($numero = 0, $decimal = ',', $milhar = '.', $nrCasas = 2, $simbolo = 'R$ ', $posicao = 'E'){
		parent::__construct($numero, $decimal, $milhar, $nrCasas, $simbolo, $posicao);
	}
}
?>