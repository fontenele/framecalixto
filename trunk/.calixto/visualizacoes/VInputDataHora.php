<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package FrameCalixto
* @subpackage visualização
*/
class VInputDataHora extends VInput{
	public $data;
	public $hora;
	public function __construct($nome = 'naoInformado',TData $valor){
		$this->data = new VInputData($nome."[data]",$valor);
		$this->hora = new VInputHora($nome."[hora]",$valor);
	}
	public function __toString(){
		return $this->data->__toString().$this->hora->__toString();
	}
}
?>
