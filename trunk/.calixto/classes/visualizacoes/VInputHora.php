<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package Infra-estrutura
* @subpackage visualização
*/
class VInputHora extends VInput{
	function __construct($nome = 'naoInformado',TData $valor){
		parent::__construct($nome, $valor);
		$this->passarOnkeypress('formatarHora(this, event);');
		$this->passarOnchange('checarHora(this);');
		$this->passarSize('8');
		$this->passarMaxlength('8');
		$this->passarClass('hora');
		$this->passarValue($valor->pegarHora());
	}
}
?>
