<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package FrameCalixto
* @subpackage visualização
*/
class VInputData extends VInput{
    /**
     * Método construtor
     * @param string $nome
     * @param TData $valor
     */
	function __construct($nome = 'naoInformado',TData $valor = null){
		parent::__construct($nome, $valor);
		// $this->passarOnkeypress('formatarData(this, event, "/", "DDMMYYYY");');
		$this->passarOnBlur(sprintf('checarData(this, "/", "%s","%s");','DDMMYYYY',date('d/m/Y')));
		$this->passarSize('10');
		$this->passarMaxlength('10');
		$this->passarClass('data');
		$this->passarValue($valor ? $valor->pegarData() : '');
	}
    /**
     * Método sobrescrito para manter o tamanho da data estático
     * @param string $valor
     */
	public function passarMaxlength($valor){
		$this->propriedades['maxlength'] = '10';
	}
}
?>