<?php
/**
* Objeto de apresenta��o de uma etiqueta HTML
* @package Infra-estrutura
* @subpackage visualiza��o
*/
class VComunicacao extends VEtiquetaHtml{
	function __construct($comunicacao){
		parent::__construct('div');
		$this->passarClass('comunicacao');
		$this->passarConteudo($comunicacao);
	}
}
?>
