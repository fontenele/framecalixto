<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package FrameCalixto
* @subpackage visualização
*/
class VInputNumerico extends VInput{
	function __construct($nome = 'naoInformado',TNumerico $valor = null){
		parent::__construct($nome, $valor);
		$this->passarSize('15');
		$this->passarMaxlength('15');
		$this->passarClass('numerico');
		if(!$valor) $valor = new TNumerico();
		if($valor->pegarNumero() === null){
			$this->passarValue(null);
		}else{
			$this->passarValue($valor->__toString());
		}
	}
}
?>