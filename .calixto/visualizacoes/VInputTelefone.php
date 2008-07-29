<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package FrameCalixto
* @subpackage visualização
*/
class VInputTelefone extends VInputNumerico{
	function __construct($nome = 'naoInformado',TTelefone $valor){
		parent::__construct($nome, $valor);
		$this->removerOnBlur();
		$this->removerOnFocus();
		$this->adicionarOnFocus('desformatarTelefone(this);');
		$this->adicionarOnBlur('formatarTelefone(this);');
		$this->passarSize('13');
		$this->passarMaxlength('13');
		$this->passarValue($valor->__toString());
	}
}
?>