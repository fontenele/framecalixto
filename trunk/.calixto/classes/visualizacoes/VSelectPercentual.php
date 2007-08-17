<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package Infra-estrutura
* @subpackage visualização
*/
class VSelectPercentual extends VSelect{
	function __construct($nome = 'naoInformado',$valor = null){
		parent::__construct($nome, $valor);
		$this->conteudo[] = "&nbsp;";
		for($i = 1;$i<= 100;$i++){
			$this->conteudo[$i] = "$i%";
		}
	}
}
?>
