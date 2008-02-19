<?php
/**
* Classe de utilitários para caracteres
* Caracteres
* @package Infra-estrutura
* @subpackage utilitários
*/
class caracteres {
	/**
	* Retira os caracteres acentuados de uma string
	*
	* @param [string] $stTexto
	* @return [string]
	*/
	static function RetiraAcentos($stTexto){
		$stTexto = str_replace('ç','c', $stTexto);
		$stTexto = str_replace('à','a', $stTexto);
		$stTexto = str_replace('è','e', $stTexto);
		$stTexto = str_replace('ì','i', $stTexto);
		$stTexto = str_replace('ò','o', $stTexto);
		$stTexto = str_replace('ù','u', $stTexto);
		$stTexto = str_replace('â','a', $stTexto);
		$stTexto = str_replace('ê','e', $stTexto);
		$stTexto = str_replace('î','i', $stTexto);
		$stTexto = str_replace('ô','o', $stTexto);
		$stTexto = str_replace('û','u', $stTexto);
		$stTexto = str_replace('ä','a', $stTexto);
		$stTexto = str_replace('ë','e', $stTexto);
		$stTexto = str_replace('ï','i', $stTexto);
		$stTexto = str_replace('ö','o', $stTexto);
		$stTexto = str_replace('ü','u', $stTexto);
		$stTexto = str_replace('á','a', $stTexto);
		$stTexto = str_replace('é','e', $stTexto);
		$stTexto = str_replace('í','i', $stTexto);
		$stTexto = str_replace('ó','o', $stTexto);
		$stTexto = str_replace('ú','u', $stTexto);
		$stTexto = str_replace('ã','a', $stTexto);
		$stTexto = str_replace('õ','o', $stTexto);
		$stTexto = str_replace('À','A', $stTexto);
		$stTexto = str_replace('Ç','C', $stTexto);
		$stTexto = str_replace('È','E', $stTexto);
		$stTexto = str_replace('Ì','I', $stTexto);
		$stTexto = str_replace('Ò','O', $stTexto);
		$stTexto = str_replace('Ù','U', $stTexto);
		$stTexto = str_replace('Â','A', $stTexto);
		$stTexto = str_replace('Ê','E', $stTexto);
		$stTexto = str_replace('Î','I', $stTexto);
		$stTexto = str_replace('Ô','O', $stTexto);
		$stTexto = str_replace('Û','U', $stTexto);
		$stTexto = str_replace('Ä','A', $stTexto);
		$stTexto = str_replace('Ë','E', $stTexto);
		$stTexto = str_replace('Ï','I', $stTexto);
		$stTexto = str_replace('Ö','O', $stTexto);
		$stTexto = str_replace('Ü','U', $stTexto);
		$stTexto = str_replace('Á','A', $stTexto);
		$stTexto = str_replace('É','E', $stTexto);
		$stTexto = str_replace('Í','I', $stTexto);
		$stTexto = str_replace('Ó','O', $stTexto);
		$stTexto = str_replace('Ú','U', $stTexto);
		$stTexto = str_replace('Ã','A', $stTexto);
		$stTexto = str_replace('Õ','O', $stTexto);
		return $stTexto;
	}
}
?>