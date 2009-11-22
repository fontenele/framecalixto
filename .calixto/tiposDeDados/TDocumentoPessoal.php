<?php
/**
* Classe de reprensação de arquivo
* Esta classe representa numerico no formato de CPF
* @package FrameCalixto
* @subpackage tipoDeDados
*/
class TDocumentoPessoal extends TTelefone{
	/**
	* @var string tipo do documento
	*/
	protected $tipo = 'cpf';
	/**
	* Método de validação
	*/
	public function validar(){
		$tamanho = strlen($this->numero);
		switch(strtolower($this->tipo)){
			case 'cpf':
				if($tamanho != 11){	throw("CPF inválido!");	}
			break;
			case 'cnpj':
				switch($tamanho){
					case 14:
					break;
					default:
						throw("CNPJ inválido!");
				}
			break;
		}
	}
	/**
	* Método de sobrecarga para printar a classe
	* @return string texto de saída da classe
	*/
	public function __toString(){
		$tamanho = strlen($this->numero);
		$res = '';
		$j = 0 ;
		switch(strtolower($this->tipo)){
			case 'cpf':
				for($i = $tamanho -1; $i >= 0; $i--){
					$j++;
					if($j == 12){ break; }
					$res = $this->numero{$i}.$res;
					if($j == 2){ $res = '-'.$res; }
					if($j == 5){ $res = '.'.$res; }
					if($j == 8){ $res = '.'.$res; }
				}
			break;
			case 'cnpj':
				for($i = $tamanho -1; $i >= 0; $i--){
					$j++;
					if($j == 15){ break; }
					$res = $this->numero{$i}.$res;
					if($j == 2){ $res = '-'.$res; }
					if($j == 6){ $res = '/'.$res; }
					if($j == 9){ $res = '.'.$res; }
					if($j == 12){ $res = '.'.$res; }
				}
			break;
		}
		return $res;
	}
}
?>