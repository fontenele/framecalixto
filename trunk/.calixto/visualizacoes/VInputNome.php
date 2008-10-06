<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package FrameCalixto
* @subpackage visualização
*/
class VInputNome extends VInput{
	function __construct($nome = 'naoInformado', $valor){
		parent::__construct($nome, $valor);
		$this->passarOnkeypress('return validarNome(this, event);');
		$this->passarOnBlur('return validarNomeCompleto(this);');
		$this->passarValue($valor);
	}
	public function __toString(){
		return parent::__toString();
	}
}
?>
