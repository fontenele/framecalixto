<?php
/**
* Objeto de apresenta��o de uma etiqueta HTML
* @package Infra-estrutura
* @subpackage visualiza��o
*/
class VInput extends VComponente{
	function __construct($nome = 'naoInformado',$valor = null){
		parent::__construct('input',$nome, $valor);
		$this->fechada = false;
	}
}
?>
