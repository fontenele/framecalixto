<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package FrameCalixto
* @subpackage visualização
*/
class VComunicacao extends VEtiquetaHtml{
	function __construct($comunicacao){
		parent::__construct('div');
		$this->passarClass('comunicacao ui-state-highlight');
		$this->passarConteudo($comunicacao);
	}
}
?>
