<?php
/**
* Objeto de apresenta��o de uma etiqueta HTML
* @package Infra-estrutura
* @subpackage visualiza��o
*/
class VFile extends VInput{
	function __construct($nome = 'naoInformado',$valor = null){
		parent::__construct($nome, $valor);
		$this->passarType('file');
	}
}
?>
