<?php
/**
* Classe de reprensação de arquivo
* Esta classe representa numerico no formato de CEP
* @package FrameCalixto
* @subpackage tipoDeDados
*/
class TCep extends TTelefone{
	/**
	* Método de validação
	*/
	public function validar(){
		$tamanho = strlen($this->numero);
		if($tamanho != 8){	throw("CEP inválido!");	}
	}
	/**
	* Método de sobrecarga para printar a classe
	* @return [string] texto de saída da classe
	*/
	public function __toString(){
		$tamanho = strlen($this->numero);
		$res = '';
		$j = 0 ;

		for($i = $tamanho -1; $i >= 0; $i--){
			$j++;
			if($j == 9){ break;	}
			$res = $this->numero{$i}.$res;
			if($j == 3){ $res = '-'.$res; }
			if($j == 6){ $res = '.'.$res; }
		}
		return $res;
	}
}
?>