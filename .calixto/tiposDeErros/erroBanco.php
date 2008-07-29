<?php
/**
* Classe de representação de uma exceção ou um erro.
* @package FrameCalixto
* @subpackage Erros
*/
class erroBanco extends erro{
	/**
	* Comando causador do erro executado no banco
	*/
	public $comando;
	/**
	* Método que faz a representação do objeto personalizada no formato html
	* @return [string] 
	*/
	public function __toHtml() {
		return "
		<fieldset class='erroNegro'>
			<legend>{$this->titulo}</legend>
			<link rel='stylesheet' href='.calixto/estilos/debug.css' />
			<table summary='text' class='erroNegro'>
				<tr>
					<td>Mensagem:</td>
					<td><b>{$this->message}</b></td>
				</tr>
				<tr>
					<td>Arquivo:</td>
					<td>## {$this->file}({$this->line})</td>
				</tr>
				<tr>
					<td>Trilha:</td>
					<td><pre>{$this->getTraceAsString()}</pre></td>
				</tr>
				<tr>
					<td>Comando:</td>
					<td><pre>{$this->comando}</pre></td>
				</tr>
			</table>
		<fieldset>
		";
	}
	/**
	* Método que faz a representação do objeto personalizada no formato string 
	*/
	public function __toString() {
		$st = ($this->message)?"Causa [$this->message]":'';
		return "Ocorreu um erro de banco de dados! \n\tNa linha [{$this->line}] do arquivo [{$this->file}] \n\t{$st} \n\tComando causador:\n\t\t{$this->comando}";
	}
}
?>
