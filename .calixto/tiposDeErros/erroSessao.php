<?php
/**
* Classe de representação de uma exceção ou um erro.
* @package FrameCalixto
* @subpackage Erros
*/
class erroSessao extends erro{
	/**
	* Método que faz a representação do objeto personalizada no formato string
	*/
	public function __toString() {
		$st = ($this->message)?"Causa [$this->message]":'';
		return "Ocorreu um erro de Acesso a sessão! \n
		Na linha [{$this->line}] do arquivo [{$this->file}]\n
		{$st}\n
		Comando causador:\n
		{$this->comando}";
	}
}
?>
