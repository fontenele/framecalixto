<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package FrameCalixto
* @subpackage visualização
*/
class VInputCep extends VInputNumerico{
	function __construct($nome = 'naoInformado',TCep $valor){
		parent::__construct($nome, $valor);
		$this->removerOnBlur();
		$this->removerOnFocus();
		$this->adicionarOnFocus('desformatarCep(this);');
		$this->adicionarOnBlur('formatarCep(this);');
		$this->passarSize('10');
		$this->passarMaxlength('10');
		$this->passarValue($valor->__toString());
	}
}
?>