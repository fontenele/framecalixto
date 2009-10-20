<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package FrameCalixto
* @subpackage visualização
*/
class VInputTelefone extends VInput{
	function __construct($nome = 'naoInformado',TTelefone $valor){
		parent::__construct($nome, $valor);
		$this->passarClass('telefone');
		$this->passarValue($valor->__toString());
	}
	public function passarMaxlength($valor){
		$this->propriedades['maxlength'] = '21';
	}
	public function passarSize($valor){
		$this->propriedades['size'] = '20';
	}
}
?>