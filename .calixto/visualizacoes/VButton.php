<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package FrameCalixto
* @subpackage visualização
*/
class VButton extends VComponente{
	function __construct($nome = 'naoInformado',$valor = null){
		parent::__construct('button',$nome, $valor);
	}
}
?>
