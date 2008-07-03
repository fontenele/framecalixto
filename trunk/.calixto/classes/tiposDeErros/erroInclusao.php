<?php
/**
* Classe de representação de uma exceção ou um erro.
* @package Infra-estrutura
* @subpackage Erros
*/
class erroInclusao extends erro{
	/**
	* Título html do erro
	* @var string
	*/
	protected $titulo = 'Erro de inclusão ou leitura de arquivo:';
	/**
	* Nome da imagem
	* @var string
	*/
	protected $imagem = 'arquivoInexistente.png';
	/**
	* Método que faz a representação do objeto personalizada no formato string
	*/
	public function __toString() {
		$st = ($this->message)?"Causa [$this->message]":'';
		return "Ocorreu um erro de inclusão de arquivo! \n
		Na linha [{$this->line}] do arquivo [{$this->file}]\n
		{$st}";
	}
}
?>