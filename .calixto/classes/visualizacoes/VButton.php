<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package Infra-estrutura
* @subpackage visualização
*/
class VButton extends VComponente{
	function __construct($nome = 'naoInformado',$valor = null){
		parent::__construct('button',$nome, $valor);
	}
}
?>
