<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package Infra-estrutura
* @subpackage visualização
*/
class VReset extends VInput{
	function __construct($nome = 'naoInformado',$valor = null){
		parent::__construct($nome, $valor);
		$this->passarType('reset');
	}
}
?>
