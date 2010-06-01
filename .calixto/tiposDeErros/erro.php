<?php
/**
* Classe de representação de uma exceção ou um erro.
* @package FrameCalixto
* @subpackage Erros
*/
class erro extends Exception{
	/**
	* Título html do erro
	* @var string
	*/
	protected $titulo = 'Erro:';
	/**
	* Nome da imagem
	* @var string
	*/
	protected $imagem = 'erro.png';
	/**
	* Arquivo causador
	*/
	public $arquivo;
	/**
	* Linha do arquivo causador
	*/
	public $linha;
	/**
	* Redefine a exceção para que a mensagem não seja opcional 
	* @param string mensagem do erro
	* @param string código do erro
	*/
	public function __construct($message = null, $code = 0) {
		parent::__construct($message, $code);
		$this->arquivo = $this->getFile();
		$this->linha = $this->getLine();
	}
	/**
	* Método que faz a representação do objeto personalizada no formato string 
	* @return string
	*/
	public function __toString() {
		return __CLASS__ . ": [{$this->code}]: {$this->message}: {$this->line}\n";
	}
	/**
	* Método que faz a representação do objeto personalizada no formato html
	* @return string
	*/
	public function __toHtml() {
		if(strtolower(ini_get('display_errors')) != 'on') return '';
		return "
		<fieldset class='erroNegro'>
			<legend>{$this->titulo}</legend>
			<link type='text/css' rel='stylesheet' href='.sistema/debug.css' />
			<img src='.sistema/icones/error.png'/>
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
			</table>
		</fieldset>
		";
	}
}
?>
