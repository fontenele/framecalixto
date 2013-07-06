<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package FrameCalixto
* @subpackage visualização
*/
class VInputCpf extends VInput{
	/**
	 * Método construtor
	 * @param type $nome
	 * @param TCpf $valor
	 */
	function __construct($nome = 'naoInformado',TCpf $valor){
		parent::__construct($nome, $valor);
		$this->passarClass('cpf');
		$this->passarValue((string) $valor);
		$this->passarMaxlength('14');
	}
}
?>