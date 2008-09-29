<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package FrameCalixto
* @subpackage visualização
*/
class VInputTelefone extends VInput{
	function __construct($nome = 'naoInformado',TTelefone $valor){
		parent::__construct($nome, $valor);
		$this->passarClass('numerico');
		//$this->adicionarOnFocus('desformatarTelefone(this);');
		//$this->adicionarOnBlur('formatarTelefone(this);');
		$this->passarSize('10');
		$this->passarMaxlength('13');
		$this->passarValue($valor->__toString());
	}
	public function passarMaxlength($valor){
		$this->propriedades['maxlength'] = '13';
	}
	public function __toString(){
		return parent::__toString().'<script type="text/javascript">jQuery(function($){$("#'.$this->pegarId().'").mask("(99)9999-9999");});</script>';
	}
}
?>