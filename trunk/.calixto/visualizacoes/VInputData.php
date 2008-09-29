<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package FrameCalixto
* @subpackage visualização
*/
class VInputData extends VInput{
	function __construct($nome = 'naoInformado',TData $valor){
		parent::__construct($nome, $valor);
		// $this->passarOnkeypress('formatarData(this, event, "/", "DDMMYYYY");');
		$this->passarOnBlur(sprintf('checarData(this, "/", "%s","%s");','DDMMYYYY',date('d/m/Y')));
		$this->passarSize('10');
		$this->passarMaxlength('10');
		$this->passarClass('data');
		$this->passarValue($valor->pegarData());
	}
	public function passarMaxlength($valor){
		$this->propriedades['maxlength'] = '10';
	}
	public function __toString(){
		return parent::__toString().'<script type="text/javascript">jQuery(function($){$("#'.$this->pegarId().'").mask("99/99/9999",{completed:function(){checarData($("#'.$this->pegarId().'"),"/","DDMMYYYY",'.date("d/m/Y").');}});});</script>';
	}
}
?>