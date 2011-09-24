<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package FrameCalixto
* @subpackage visualização
*/
class VInputMoeda extends VInputNumerico{
	function __construct($nome = 'naoInformado',TNumerico $valor){
		parent::__construct($nome, new TMoeda($valor));
		$this->adicionarClass('moeda');
	}
}
?>