<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package Infra-estrutura
* @subpackage visualização
*/
class VCheck extends VInput{
	function __construct($nome = 'naoInformado',$valor = null){
		parent::__construct($nome, $valor);
		$this->passarType('checkbox');
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