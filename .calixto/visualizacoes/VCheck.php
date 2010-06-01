<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package FrameCalixto
* @subpackage visualização
*/
class VCheck extends VInput{
	function __construct($nome = 'naoInformado',$valor = null){
		parent::__construct($nome, $valor);
		if($valor && $valor == 'on') { $this->passarChecked($valor); }
		$this->passarType('checkbox');
		$this->passarClass('checkbox');
	}
	/**
	* Método de checagem do componente
	* @param boolean
	*/
	function passarChecked($valor = false){
		if($valor)	$this->propriedades['checked'] = 'checked';
	}
}
?>