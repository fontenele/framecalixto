<?php
/**
* Objeto de apresentação de uma etiqueta HTML
* @package Infra-estrutura
* @subpackage visualização
*/
class VInputDocumentoPessoal extends VInputNumerico{
	protected $tipo;

	function __construct($nome = 'naoInformado',TDocumentoPessoal $valor){
		parent::__construct($nome, $valor);
		$this->passarTipo($valor->pegarTipo());
		$this->passarValue($valor->__toString());
	}
	/**
	* Método de configuração do tipo de documento
	*/
	public function passarTipo($tipo){
		$this->removerOnBlur();
		$this->removerOnFocus();
		$this->adicionarOnFocus('desformatarDocumentoPessoal(this);');
		$this->passarSize('18');
		$this->passarMaxlength('18');
		switch(strtolower($tipo)){
			case 'cnpj':
				$this->tipo = 'cnpj';
				$this->adicionarOnBlur("formatarDocumentoPessoal(this,'cnpj');");
			break;
			default:
				$this->tipo = 'cpf';
				$this->adicionarOnBlur("formatarDocumentoPessoal(this,'cpf');");
			break;
		}
	}
}
?>