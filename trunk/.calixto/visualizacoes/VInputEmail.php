<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package FrameCalixto
* @subpackage visualização
*/
class VInputEmail extends VInput{
	function __construct($nome = 'naoInformado', $valor){
		parent::__construct($nome, $valor);
		$this->passarOnChange('validarEmail(this);');
		$this->passarValue($valor);
                $this->passarType("email");
	}
	public function __toString(){
		return parent::__toString();
	}
}
?>
