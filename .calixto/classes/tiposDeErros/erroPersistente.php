<?php
/**
* Classe de representação de uma exceção ou um erro.
* @package Infra-estrutura
* @subpackage Erros
*/
class erroPersistente extends erro{
	/**
	* Método que faz a representação do objeto personalizada no formato string 
	*/
	public function __toString() {
		$st = ($this->message)?"Causa [$this->message]":'';
		return "Ocorreu um erro de utilização de persistente! \n
		Na linha [{$this->line}] do arquivo [{$this->file}]\n
		{$st}\n
		Comando causador:\n
		{$this->comando}";
	}
}
?>
