<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package Infra-estrutura
* @subpackage visualização
*/
class VInput extends VComponente{
	function __construct($nome = 'naoInformado',$valor = null){
		parent::__construct('input',$nome, $valor);
		$this->fechada = false;
		$this->passarSize(30);
	}
}
?>
