<?php
/**
* Classe de reprensação de arquivo
* Esta classe representa numerico no formato de telefone
* @package FrameCalixto
* @subpackage tipoDeDados
*/
class TTelefone extends TNumerico{
	/**
	* metodo construtor do telefone
	* @param string numero formatado
	*/
	public function __construct($numero = ''){
		$this->numero = preg_replace('/[^0-9]/','',$numero);
	}
	/**
	* Método de validação do telefone
	*/
	public function validar(){
	}
	/**
	* Método de sobrecarga para printar a classe
	* @return string texto de saída da classe
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
			$ramal = substr($this->numero,10,4) ? ' r:'.substr($this->numero,10,4):null;
			$res = '('.substr($this->numero,0,2).') '.substr($this->numero,2,4).'-'.substr($this->numero,6,4).$ramal;
		}
		return $res;
	}
}
?>