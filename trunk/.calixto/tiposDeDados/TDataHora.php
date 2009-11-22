<?php
/**
* Classe de reprensação de arquivo
* Esta classe representa uma data
* @package FrameCalixto
* @subpackage tipoDeDados
*/
class TDataHora extends TData{
	/**
	* metodo de retorno da string de data
	* @param string formato da data
	* @return string data formatada
	*/
	public function __toString(){
		$data = $this->pegarData();
		$hora = $this->pegarHora();
		if($data === null && $hora === null) return '';
		return sprintf('%s %s',$data,$hora);
	}
	/**
	* retorna um TData com o tempo atual
	* @return TData
	*/
	public static function agora(){
		return new TDataHora(date('d/m/Y H:i:s'));
	}
	/**
	* retorna um TData com o tempo atual
	* @return TData
	*/
	public static function hoje($horas = null){
		return new TDataHora(date('d/m/Y').' '.$horas);
	}
}
?>