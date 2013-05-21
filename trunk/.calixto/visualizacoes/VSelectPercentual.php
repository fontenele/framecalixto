<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package FrameCalixto
* @subpackage visualização
*/
class VSelectPercentual extends VSelect{
	function __construct($nome = 'naoInformado',$valor = null){
		parent::__construct($nome, $valor);
		$this->conteudo[] = "";
		for($i = 1;$i<= 100;$i++){
			$this->conteudo[$i] = "$i%";
		}
	}
}
?>
