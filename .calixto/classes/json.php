<?php
/**
* Classe que codifica um objeto para JSON
* JavaScript Object Notation
* @package Infra-estrutura
* @subpackage visualização
*/
class json {
	/**
	* Retorna um json preparado para um eval em javascript
	* @param [mixed] parametro para codificar
	* @return [string] json preparado para um eval em javascript
	*/
	function pegarJson( $var ){
		switch( gettype( $var )){
			case 'boolean': return $var ? 'true' : 'false';
			case 'NULL': return 'null';
			case 'integer': return (int) $var;
			case 'double': return (double) $var;
			case 'float': return (float) $var;
			case 'string':
				$var = $var;
				return (string) '"'.$var.'"';
			case 'array':
				return $this->codificarArray($var);
			case 'object':
				return $this->codificarArray($objeto[get_class($var)] = get_object_vars($var));
		}
	}
	/**
	* Faz a codificação de array
	* @param [array] array para a codificação .
	* @return [string] json do array
	*/
	function codificarArray($var){
		if(is_array($var) && (array_keys($var) !== range(0, sizeof($var) - 1)))
			return sprintf('{%s}', join(',', array_map(array($this, 'montaChave'), array_keys($var), array_values($var))));
		if (is_array($var)) return sprintf('[%s]', join(',', array_map(array($this, 'codificarArray'), $var)));
		return $this->pegarJson($var);
	}
	/**
	* Faz a codificação de uma chave do json
	* @param [st] nome da chave .
	* @param [mixed] valor da chave
	* @return [string] json
	*/
	function montaChave($name, $value){
		return (sprintf("%s:%s", $this->codificarArray(strval($name)), $this->codificarArray($value)));
	}
}
?>