<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package FrameCalixto
* @subpackage visualização
*/
class VInputCnpj extends VInput{
	/**
	 * Método construtor
	 * @param type $nome
	 * @param TCnpj $valor
	 */
	function __construct($nome = 'naoInformado',TCnpj $valor){
		parent::__construct($nome, $valor);
		$this->passarClass('cnpj');
		$this->passarValue((string) $valor);
		$this->passarMaxlength('18');
	}
}
?>