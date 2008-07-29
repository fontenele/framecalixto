<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package FrameCalixto
* @subpackage visualização
*/
class VInputData extends VInput{
	function __construct($nome = 'naoInformado',TData $valor){
		parent::__construct($nome, $valor);
		$this->passarOnkeypress('formatarData(this, event, "/", "DDMMYYYY");');
		$this->passarOnchange(sprintf('checarData(this, "/", "%s","%s");','DDMMYYYY',date('d/m/Y')));
		$this->passarSize('10');
		$this->passarMaxlength('10');
		$this->passarClass('data');
		$this->passarValue($valor->pegarData());
	}
}
?>