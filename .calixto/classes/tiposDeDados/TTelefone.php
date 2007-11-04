<?php
/**
* Classe de reprensação de arquivo
* Esta classe representa numerico no formato de telefone
* @package Infra-estrutura
* @subpackage tipoDeDados
*/
class TTelefone extends TNumerico{
	/**
	* metodo construtor do telefone
	* @param [string] numero formatado
	*/
	public function __construct($numero = ''){
		$this->numero = preg_replace('/[^0-9]/','',$numero);
	}
	/**
	* Método de validação do telefone
	*/
	public function validar(){
		$tamanho = strlen($this->numero);
		switch($tamanho){
			case 8:
			case 10:
			break;
			default:
				throw("Telefone inválido!");
		}
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
			if($j == 4){ $res = '-'.$res; }
		}
		if($tamanho > 8){
			$res = '('.substr($this->numero,0,($tamanho -8)).')'.$res;
		}
		return $res;
	}
}
?>