<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package FrameCalixto
* @subpackage visualização
*/
class VInputDocumentoPessoal extends VInput{
	protected $tipo;

	function __construct($nome = 'naoInformado',TDocumentoPessoal $valor){
		parent::__construct($nome, $valor);
		$this->passarClass('numerico');
		$this->passarTipo($valor->pegarTipo());
		$this->passarValue($valor->__toString());
	}
	/**
	* Método de configuração do tipo de documento
	*/
	public function passarTipo($tipo){
		$this->passarSize('18');
		$this->passarMaxlength('18');
		switch(strtolower($tipo)){
			case 'cnpj':
				$this->tipo = 'cnpj';
			break;
			default:
				$this->tipo = 'cpf';
			break;
		}
	}
	public function __toString(){
		switch(strtolower($this->tipo)){
			case 'cnpj':
				return parent::__toString().'<script type="text/javascript">jQuery(function($){$("#'.$this->pegarId().'").mask("99.999.999/9999-99",{completed:function(){validarCnpj($("#'.$this->pegarId().'"));}});});</script>';
//				return parent::__toString();
			break;
			default:
				return parent::__toString().'<script type="text/javascript">jQuery(function($){$("#'.$this->pegarId().'").mask("999.999.999-99",{completed:function(){validarCpf($("#'.$this->pegarId().'"));}});});</script>';
			break;
		}
	}
	public function passarMaxlength($valor){}
}
?>