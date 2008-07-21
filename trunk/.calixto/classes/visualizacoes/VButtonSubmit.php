<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package FrameCalixto
* @subpackage visualização
*/
class VButtonSubmit extends VButton{
	function __construct($nome = 'naoInformado',$valor = null){
		parent::__construct($nome, $valor);
		$this->passarType('submit');
	}
}
?>
