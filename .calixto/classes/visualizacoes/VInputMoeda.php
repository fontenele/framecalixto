<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package Infra-estrutura
* @subpackage visualização
*/
class VInputMoeda extends VInputNumerico{
	function __construct($nome = 'naoInformado',TNumerico $valor){
		parent::__construct($nome, new TMoeda($valor));
	}
}
?>