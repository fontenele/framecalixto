<?php
/**
* Classe de representa��o de uma exce��o ou um erro.
* @package Infra-estrutura
* @subpackage Erros
*/
class erroNegocio extends erro{
	/**
	* M�todo que faz a representa��o do objeto personalizada no formato string 
	*/
	public function __toString() {
		$st = ($this->message)?"Causa [$this->message]":'';
		return "Ocorreu um erro de Regra de neg�cio! \n
		Na linha [{$this->line}] do arquivo [{$this->file}]\n
		{$st}\n
		Comando causador:\n
		{$this->comando}";
	}
}
?>
