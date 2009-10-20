<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package FrameCalixto
* @subpackage visualização
*/
class VInputCep extends VInput{
	function __construct($nome = 'naoInformado',TCep $valor){
		parent::__construct($nome, $valor);
		$this->passarClass('cep');
		$this->passarSize('10');
		$this->passarMaxlength('10');
		$this->passarValue($valor->__toString());
	}
	public function passarMaxlength($valor){
		$this->propriedades['maxlength'] = '10';
	}
}
?>