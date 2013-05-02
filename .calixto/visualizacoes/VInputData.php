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
		$this->passarSize('10');
		$this->passarMaxlength('10');
		$this->passarClass('data input-small');
		$this->passarValue($valor ? $valor->pegarData() : '');
	}
    /**
     * Método sobrescrito para manter o tamanho da data estático
     * @param string $valor
     */
	public function passarMaxlength($valor){
		$this->propriedades['maxlength'] = '10';
	}
    /**
     * Método sobrescrito para manter o tamanho da data estático
     * @param string $valor
     */
	public function passarSize($valor){
		$this->propriedades['size'] = '10';
	}
}
?>