<?php
/**
* Classe de reprensação de arquivo
* Esta classe representa um numérico
* @package Infra-estrutura
* @subpackage tipoDeDados
*/
class TNumerico extends objeto{
	/**
	* @var [nr] numero
	*/
	protected $numero = 0;
	/**
	* @var [string] caracter separador de decimal
	*/
	protected $charDecimal;
	/**
	* @var [string] caracter separador de milhar
	*/
	protected $charMilhar;
	/**
	* @var [nr] numero de casas decimais
	*/
	protected $nrCasasDecimais;
	/**
	* @var [string] simbolo do número (Unidade de Medida)
	*/
	protected $simbolo;
	/**
	* @var [string] identificador da posição do simbolo 'E' para esquerda, 'D' para direita
	*/
	protected $posicao;
	/**
	* metodo construtor do numerico
	* @param [string] numero formatado
	* @param [string] character separador de decimal
	* @param [string] character separador de milhar
	* @param [nr] número de casas decimais
	* @param [string] simbolo do número (Unidade de Medida)
	* @param [string] identificador da posição do simbolo 'E' para esquerda, 'D' para direita
	*/
	public function __construct($numero = 0, $decimal = ',', $milhar = '.', $nrCasas = 0, $simbolo = null, $posicao = 'E'){
		$this->passarCharDecimal($decimal);
		$this->passarCharMilhar($milhar);
		$this->passarNrCasasDecimais($nrCasas);
		$this->passarSimbolo($simbolo);
		$this->passarPosicao($posicao);
		$this->passarNumero($numero);
	}
	/**
	* Método de passagem de um número para o objeto
	* @param [mixed] numero
	*/
	public function passarNumero($numero){
		switch(true){
			case(is_numeric($numero)):
				$this->numero = (float) $numero;
			break;
			case(is_string($numero)):
				$posicaoDecimal = strpos($numero,$this->pegarCharDecimal());
				if($posicaoDecimal !== false){
					$inteiro = substr($numero,0,$posicaoDecimal);
					$decimal = substr($numero,$posicaoDecimal);
					$this->numero = (float) preg_replace('/[^0-9]/','',$inteiro).'.'.preg_replace('/[^0-9]/','',$decimal);
				}else{
					$this->numero = (float) preg_replace('/[^0-9]/','',$numero);
				}
			break;
			case(($numero instanceof TNumerico)):
				$this->numero = $numero->pegarNumero();
			break;
			default:
				$this->numero = $numero;
		}
	}
	/**
	* Método de sobrecarga do PHP para apresentação de um numérico
	* @return string
	*/
	public function __toString(){
		if($this->pegarPosicao() == 'E'){
			return $this->pegarSimbolo().number_format($this->pegarNumero(),$this->pegarNrCasasDecimais(),$this->pegarCharDecimal(),$this->pegarCharMilhar());
		}else{
			return number_format($this->pegarNumero(),$this->pegarNrCasasDecimais(),$this->pegarCharDecimal(),$this->pegarCharMilhar()).$this->pegarSimbolo();
		}
	}
}
?>