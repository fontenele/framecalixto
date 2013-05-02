<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package FrameCalixto
* @subpackage visualização
*/
class VInputHora extends VInput{
	function __construct($nome = 'naoInformado',TData $valor){
		parent::__construct($nome, $valor);
		$this->passarSize('8');
		$this->passarMaxlength('8');
		$this->passarClass('hora input-mini');
		$this->passarValue($valor->pegarHora());
	}
}
?>
