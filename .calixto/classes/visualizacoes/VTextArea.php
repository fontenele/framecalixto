<?php
/**
* Objeto de apresenta��o de uma etiqueta HTML
* @package Infra-estrutura
* @subpackage visualiza��o
*/
class VTextArea extends VComponente{
	function __construct($nome = 'naoInformado',$valor = null){
		parent::__construct('textarea',$nome, $valor);
	}
}
?>
