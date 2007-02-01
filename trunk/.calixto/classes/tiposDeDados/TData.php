<?php
/**
* Classe de reprensação de arquivo
* Esta classe representa uma data
* @package Infra-estrutura
* @subpackage tipoDeDados
*/
class TData extends objeto{
	/**
	* valor numerico da data
	*/
	protected $tempoMarcado;
	/**
	* metodo construtor da data
	* @param [string] data como string
	* @param [string] formato da string de data
	*/
	public function __construct($data, $formato = 'D/M/Y'){
		if(!$data)return;
		$data = explode(' ', $data);
		if(isset($data[1])){
			$hora = explode(':',$data[1]);
		}
		$data = explode('/',$data[0]);
		switch(strtolower($formato)){
			case('d/m/y'):
				@$this->tempoMarcado = mktime($hora[0], $hora[1], $hora[2], $data[1], $data[0], $data[2]);
			break;
			case('m/d/y'):
				@$this->tempoMarcado = mktime($hora[0], $hora[1], $hora[2], $data[0], $data[1], $data[2]);
			break;
			case('y/m/d'):
				@$this->tempoMarcado = mktime($hora[0], $hora[1], $hora[2], $data[2], $data[1], $data[0]);
			break;
		}
	}
	/**
	* metodo de somatorio de horas
	* @param [numerico] número de horas a ser somado a data
	*/
	public function somarHora($horas = 1){
		$this->tempoMarcado = mktime(
				date('H',$this->tempoMarcado) + $horas, date('i',$this->tempoMarcado),
				date('s',$this->tempoMarcado),			date('m',$this->tempoMarcado),
				date('d',$this->tempoMarcado),			date('y',$this->tempoMarcado)
		);
	}
	/**
	* metodo de somatorio de minutos
	* @param [numerico] número de minutos a ser somado a data
	*/
	public function somarMinuto($minutos = 1){
		$this->tempoMarcado = mktime(
				date('H',$this->tempoMarcado),			date('i',$this->tempoMarcado) + $minutos,
				date('s',$this->tempoMarcado),			date('m',$this->tempoMarcado),
				date('d',$this->tempoMarcado),			date('y',$this->tempoMarcado)
		);
	}
	/**
	* metodo de somatorio de segundos
	* @param [numerico] número de segundos a ser somado a data
	*/
	public function somarSegundo($seg = 1){
		$this->tempoMarcado = mktime(
				date('H',$this->tempoMarcado),			date('i',$this->tempoMarcado),
				date('s',$this->tempoMarcado) + $seg,	date('m',$this->tempoMarcado),
				date('d',$this->tempoMarcado),			date('y',$this->tempoMarcado)
		);
	}
	/**
	* metodo de somatorio de dias
	* @param [numerico] número de dias a ser somado a data
	*/
	public function somarDia($dias = 1){
		$this->tempoMarcado = mktime(
				date('H',$this->tempoMarcado),			date('i',$this->tempoMarcado),
				date('s',$this->tempoMarcado),			date('m',$this->tempoMarcado),
				date('d',$this->tempoMarcado) + $dias,	date('y',$this->tempoMarcado)
		);
	}
	/**
	* metodo de somatorio de meses
	* @param [numerico] número de meses a ser somado a data
	*/
	public function somarMes($meses = 1){
		$this->tempoMarcado = mktime(
				date('H',$this->tempoMarcado),			date('i',$this->tempoMarcado),
				date('s',$this->tempoMarcado),			date('m',$this->tempoMarcado) + $meses,
				date('d',$this->tempoMarcado),			date('y',$this->tempoMarcado)
		);
	}
	/**
	* metodo de somatorio de anos
	* @param [numerico] número de anos a ser somado a data
	*/
	public function somarAno($anos = 1){
		$this->tempoMarcado = mktime(
				date('H',$this->tempoMarcado),			date('i',$this->tempoMarcado),
				date('s',$this->tempoMarcado),			date('m',$this->tempoMarcado),
				date('d',$this->tempoMarcado),			date('y',$this->tempoMarcado) + $anos
		);
	}
	/**
	* metodo de retorno da string de data 
	* @param [string] formato da data
	* @return [string] data formatada
	*/
	public function __toString($formato = 'd/m/Y'){
		return date($formato, $this->tempoMarcado);
	}
}
?>
