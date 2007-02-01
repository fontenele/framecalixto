<?php
/**
* Classe de representa��o de uma exce��o ou um erro.
* @package Infra-estrutura
* @subpackage Erros
*/
class erro extends Exception{
	/**
	* Comando causador do erro executado no banco
	*/
	public $comando;
	/**
	* Redefine a exce��o para que a mensagem n�o seja opcional 
	* @param [string] mensagem do erro
	* @param [string] c�digo do erro
	*/
	public function __construct($message = null, $code = 0) {
		parent::__construct($message, $code);
	}
	/**
	* M�todo que faz a representa��o do objeto personalizada no formato string 
	*/
	public function __toString() {
		return __CLASS__ . ": [{$this->code}]: {$this->message}: {$this->line}\n";
	}
}
?>
