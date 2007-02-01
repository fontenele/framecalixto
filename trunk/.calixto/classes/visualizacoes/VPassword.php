<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package Infra-estrutura
* @subpackage visualização
*/
class VPassword extends VInput{
	function __construct($nome = 'naoInformado',$valor = null){
		parent::__construct($nome, $valor);
		$this->passarType('password');
	}
}
?>
