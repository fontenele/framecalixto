<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package FrameCalixto
* @subpackage visualização
*/
class VHidden extends VInput{
	function __construct($nome = 'naoInformado',$valor = null){
		parent::__construct($nome, $valor);
		$this->passarType('hidden');
	}
	/**
	* Método de complemento de campo obrigatório
	* @return string
	*/
	protected function campoObrigatorio(){
		return null;
	}
}
?>
