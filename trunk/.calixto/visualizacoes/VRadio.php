<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package FrameCalixto
* @subpackage visualização
*/
class VRadio extends VInput{
	function __construct($nome = 'naoInformado',$valor = null){
		parent::__construct($nome, $valor);
		$this->passarType('radio');
	}
	/**
	* Método de checagem do componente
	* @param [booleano]
	*/
	function passarChecked($valor = false){
		if($valor)	$this->propriedades['checked'] = 'checked';
	}
}
?>