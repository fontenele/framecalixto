<?php
/**
* Classe que representa uma página abstrata
* Esta classe padroniza a forma de definição do tanto de linhas de uma página no sistema
* @package FrameCalixto
* @subpackage Utilitários
*/
class pagina extends objeto{
	/**
	* Número total de linhas do conteúdo
	* @var [in]
	*/
	public $tamanhoGeral = 0;
	/**
	* Número de linhas da página
	* @var [in]
	*/
	public $tamanhoPagina = 7;
	/**
	* Número da página atual
	* @var [in]
	*/
	public $pagina = 1;
	/**
	* Método construtor
	* @param [in] tamanho da página
	*/
	public function __construct($tamanhoPagina = 7){
		$this->tamanhoPagina = $tamanhoPagina;
	}
	/**
	* Retorna o número da linha inicial da pagina atual
	* @return [in] número da linha inicial da pagina atual
	*/
	function pegarLinhaInicial(){
		return ($this->tamanhoPagina * $this->pagina) - ($this->tamanhoPagina - 1);
	}
	/**
	* Retorna o número de linhas de uma pagina
	* @return [in] número de linhas de uma pagina
	*/
	function pegarTamanhoPagina(){
		return $this->tamanhoPagina;
	}
	/**
	* Retorna o número da linha final da pagina atual
	* @return [in] número da linha final da pagina atual
	*/
	function pegarLinhaFinal(){
		return ($this->tamanhoPagina * $this->pagina);
	}
	/**
	* Define a pagina atual
	* @param [in] número da pagina atual
	*/
	function passarPagina($pagina = 1){
		$this->pagina = ($pagina < 1) ? 1 : $pagina;
	}
	/**
	* Retorna o tamanho geral do conteudo
	* @return [in] número total de linhas do conteudo geral
	*/
	function pegarTamanhoGeral(){
		return $this->tamanhoGeral;
	}
	/**
	* Retorna o número da ultima página
	* @return int
	*/
	function ultima(){
		if($this->tamanhoGeral%$this->tamanhoPagina){
			return (int) ($this->tamanhoGeral/$this->tamanhoPagina) + 1;
		}
		return (int) ($this->tamanhoGeral/$this->tamanhoPagina);
	}
	/**
	* Incrementa a página atual
	*/
	function proxima(){
		if($this->pegarPagina() < $this->ultima()){
			$this->pagina++;
			return true;
		}else{
			return false;
		}
	}
}
?>