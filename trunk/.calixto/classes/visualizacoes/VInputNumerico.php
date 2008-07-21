<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package FrameCalixto
* @subpackage visualização
*/
class VInputNumerico extends VInput{
	function __construct($nome = 'naoInformado',TNumerico $valor){
		parent::__construct($nome, $valor);
		$this->passarSize('15');
		$this->passarMaxlength('15');
		$this->passarClass('numerico');
		$this->adicionarOnBlur(
			sprintf('formatarNumero(this,\'%s\',\'%s\',\'%s\',\'%s\',9999999999.99,\'\',\'E\',\'-\');',
				$valor->pegarCharDecimal(),
				$valor->pegarCharMilhar(),
				$valor->pegarNrCasasDecimais(),
				$valor->pegarSimbolo()
			)
		);
		$this->adicionarOnFocus(
			sprintf('desformatarNumero(this,\'%s\',\'%s\',\'%s\');',
				$valor->pegarSimbolo(),
				$valor->pegarCharDecimal(),
				$valor->pegarCharMilhar()
			)
		);
		if($valor->pegarNumero() === null){
			$this->passarValue(null);
		}else{
			$this->passarValue($valor->__toString());
		}
	}
}
?>