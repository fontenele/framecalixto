<?php
/**
* Classe de representação de uma exceção ou um erro.
* @package Infra-estrutura
* @subpackage Erros
*/
class erro extends Exception{
	/**
	* Arquivo causador
	*/
	public $arquivo;
	/**
	* Linha do arquivo causador
	*/
	public $linha;
	/**
	* Comando causador do erro executado no banco
	*/
	public $comando;
	/**
	* Redefine a exceção para que a mensagem não seja opcional 
	* @param [string] mensagem do erro
	* @param [string] código do erro
	*/
	public function __construct($message = null, $code = 0) {
		parent::__construct($message, $code);
		$this->arquivo = $this->getFile();
		$this->linha = $this->getLine();
	}
	/**
	* Método que faz a representação do objeto personalizada no formato string 
	* @return [string] 
	*/
	public function __toString() {
		return __CLASS__ . ": [{$this->code}]: {$this->message}: {$this->line}\n";
	}
	/**
	* Método que faz a representação do objeto personalizada no formato html
	* @return [string] 
	*/
	public function __toHtml() {
		return $this->__toString();
	}
}
?>