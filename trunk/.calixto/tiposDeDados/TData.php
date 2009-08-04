<?php
/**
* Classe de reprensação de arquivo
* Esta classe representa uma data
* @package FrameCalixto
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
		try{
			if(!$data)return;
			if(($data instanceof TData)){
				$this->passarTempoMarcado($data->pegarTempoMarcado());
				return;
			}
			switch(strtolower($formato)){
				case('d/m/y'):
					$erData = '/(^\d{2}\/\d{2}\/\d{4})|(^\d{2}\.\d{2}\.\d{4})|(^\d{2}-\d{2}-\d{4})/';
					$erHora = '/\d{2}:\d{2}:\d{2}/';
					preg_match($erData,$data,$arData);
					preg_match($erHora,$data,$arHora);
					$arHora = (isset($arHora[0])) ? explode(':',$arHora[0]) : array(0,0,0);
					switch(true){
						case isset($arData[1]) && $arData[1]: $arData = explode('/',$arData[1]); break;
						case isset($arData[2]) && $arData[2]: $arData = explode('.',$arData[2]); break;
						case isset($arData[3]) && $arData[3]: $arData = explode('-',$arData[3]); break;
						default: $arData = array(0,0,0);
					}
					$aux = $arData[1]; $arData[1] = $arData[0]; $arData[0] = $aux;
				break;
				case('m/d/y'):
					$erData = '/(^\d{2}\/\d{2}\/\d{4})|(^\d{2}\.\d{2}\.\d{4})|(^\d{2}-\d{2}-\d{4})/';
					$erHora = '/\d{2}:\d{2}:\d{2}/';
					preg_match($erData,$data,$arData);
					preg_match($erHora,$data,$arHora);
					$arHora = (isset($arHora[0])) ? explode(':',$arHora[0]) : array(0,0,0);
					switch(true){
						case isset($arData[1]) && $arData[1]: $arData = explode('/',$arData[1]); break;
						case isset($arData[2]) && $arData[2]: $arData = explode('.',$arData[2]); break;
						case isset($arData[3]) && $arData[3]: $arData = explode('-',$arData[3]); break;
						default: $arData = array(0,0,0);
					}
				break;
				case('y/m/d'):
					$erData = '/(^\d{4}\/\d{2}\/\d{2})|(^\d{4}\.\d{2}\.\d{2})|(^\d{4}-\d{2}-\d{2})/';
					$erHora = '/\d{2}:\d{2}:\d{2}/';
					preg_match($erData,$data,$arData);
					preg_match($erHora,$data,$arHora);
					$arHora = (isset($arHora[0])) ? explode(':',$arHora[0]) : array(0,0,0);
					switch(true){
						case isset($arData[1]) && $arData[1]: $arData = explode('/',$arData[1]); break;
						case isset($arData[2]) && $arData[2]: $arData = explode('.',$arData[2]); break;
						case isset($arData[3]) && $arData[3]: $arData = explode('-',$arData[3]); break;
						default: $arData = array(0,0,0);
					}
					$aux = $arData[2]; $arData[2] = $arData[0]; $arData[0] = $aux;
				break;
			}
			$this->tempoMarcado = mktime($arHora[0], $arHora[1], $arHora[2], $arData[0], $arData[1], $arData[2]);
		}catch(exception $e){
			x($data);die();
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
	* retorna um TData com o tempo atual
	*/
	public static function agora(){
		return new TData(date('d/m/Y H:i:s'));
	}
	/**
	* retorna um TData com o tempo atual
	*/
	public static function hoje($horas = null){
		return new TData(date('d/m/Y').' '.$horas);
	}
	/**
	* metodo de retorno da string de data
	* @return [string] data formatada
	*/
	public function pegarData(){
		if($this->tempoMarcado)
		return date('d/m/Y', $this->tempoMarcado);
		return null;
	}
	/**
	* metodo de retorno da string de hora
	* @return [string] hora formatada
	*/
	public function pegarHora(){
		if($this->tempoMarcado)
		return date('H:i:s', $this->tempoMarcado);
		return null;
	}
	/**
	* metodo de retorno da string de hora
	* @return [string] hora formatada
	*/
	public function pegarDataCompleta(){
        return $this->pegarData() . ' ' . $this->pegarHora();
	}
	/**
	* metodo de retorno da string de data
	* @param [string] formato da data
	* @return [string] data formatada
	*/
	public function __toString(){
        $data = $this->pegarData();
		return $data ? $data : '';
	}
}
?>