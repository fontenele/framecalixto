<?php
/**
* Classe que representa uma p�gina abstrata
* Esta classe padroniza a forma de defini��o de uma p�gina no sistema
* @package Infra-estrutura
* @subpackage Utilit�rios
*/
class pagina extends objeto{
	/**
	* N�mero total de linhas do conte�do
	* @var [in]
	*/
	public $tamanhoGeral = 0;
	/**
	* N�mero de linhas da p�gina
	* @var [in]
	*/
	public $tamanhoPagina = 7;
	/**
	* N�mero da p�gina atual
	* @var [in]
	*/
	public $pagina = 1;
	/**
	* Retorna o n�mero da linha inicial da pagina atual
	* @return [in] n�mero da linha inicial da pagina atual
	*/
	function pegarLinhaInicial(){
		return ($this->tamanhoPagina * $this->pagina) - ($this->tamanhoPagina - 1);
	}
	/**
	* Retorna o n�mero de linhas de uma pagina
	* @return [in] n�mero de linhas de uma pagina
	*/
	function pegarTamanhoPagina(){
		return $this->tamanhoPagina;
	}
	/**
	* Retorna o n�mero da linha final da pagina atual
	* @return [in] n�mero da linha final da pagina atual
	*/
	function pegarLinhaFinal(){
		return ($this->tamanhoPagina * $this->pagina);
	}
	/**
	* Define a pagina atual
	* @param [in] n�mero da pagina atual
	*/
	function passarPagina($pagina = 1){
		$this->pagina = ($pagina < 1) ? 1 : $pagina;
	}
	/**
	* Retorna o tamanho geral do conteudo
	* @return [in] n�mero total de linhas do conteudo geral
	*/
	function pegarTamanhoGeral(){
		return $this->tamanhoGeral;
	}
}
?>
