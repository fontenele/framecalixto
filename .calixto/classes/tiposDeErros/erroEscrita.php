<?php
/**
* Classe de representação de uma exceção ou um erro.
* @package Infra-estrutura
* @subpackage Erros
*/
class erroEscrita extends erro{
	/**
	* Método que faz a representação do objeto personalizada no formato html
	* @return [string]
	*/
	public function __toHtml() {
		return  "
			<link rel='stylesheet' href='.calixto/estilos/debug.css' />
			<div class='erroNegro'>
				<table summary='text' class='erroNegro'>
					<tr><th colspan=2 >Erro de Escrita de Arquivo</th></tr>
					<tr><td>Arquivo:</td><td>{$this->getFile()}</td></tr>
					<tr><td>Linha:</td><td>{$this->getLine()}</td></tr>
					<tr><td>Causa:</td><td>{$this->getMessage()}</td></tr>
				</table>
			</div>
			";
	}
	/**
	* Método que faz a representação do objeto personalizada no formato string
	*/
	public function __toString() {
		$st = ($this->message)?"Causa [$this->message]":'';
		return "Ocorreu um erro de escrita de arquivo! \n
		Na linha [{$this->line}] do arquivo [{$this->file}]\n
		{$st}";
	}
}
?>