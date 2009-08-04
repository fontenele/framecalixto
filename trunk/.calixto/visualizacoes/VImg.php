<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package FrameCalixto
* @subpackage visualização
*/
class VImg extends VEtiquetaHtml {
	function __construct( $src , $title = null ){
		parent::__construct( "img" );
		$this->passarSrc( $src );
		$this->passarTitle( $title );
		$this->fechada = false;
		
	}
}
?>
