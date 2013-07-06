<?php
/**
* Classe de reprensação de arquivo
* Esta classe representa numerico no formato de CPF
* @package FrameCalixto
* @subpackage tipoDeDados
*/
class TCpf extends TDocumentoPessoal{
	/**
	* Método de validação
	*/
	public function validar(){
		if(strlen($this->numero) != 11){throw("CPF inválido!");}
	}
	/**
	* Método de sobrecarga para printar a classe
	* @return string texto de saída da classe
	*/
	public function __toString(){
		if(!$this->numero) return '';
		$tamanho = strlen($this->numero);
		$res = '';
		$j = 0 ;
		for($i = $tamanho -1; $i >= 0; $i--){
			$j++;
			if($j == 12){ break; }
			$res = $this->numero{$i}.$res;
			if($j == 2){ $res = '-'.$res; }
			if($j == 5){ $res = '.'.$res; }
			if($j == 8){ $res = '.'.$res; }
		}
		return $res;
	}
}
?>