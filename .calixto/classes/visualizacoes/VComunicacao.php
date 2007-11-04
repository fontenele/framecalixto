<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package Infra-estrutura
* @subpackage visualização
*/
class VComunicacao extends VEtiquetaHtml{
	function __construct($comunicacao){
		parent::__construct('div');
		$this->passarClass('comunicacao');
		$this->passarConteudo($comunicacao);
	}
}
?>