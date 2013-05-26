<?php
/**
* Classe que codifica um objeto para JSON
* JavaScript Object Notation
* @package FrameCalixto
* @subpackage visualização
*/
class json {
	public static function encode($valor){
		$json = new json();
		return $json->pegarJson($valor);
	}
	/**
	* Retorna um json preparado para um eval em javascript
	* @param mixed parametro para codificar
	* @return string json preparado para um eval em javascript
	*/
	function pegarJson( $var ){
		//return json_encode($var);
		//x($var);die;
		switch( gettype( $var )){
			case 'boolean': return $var ? 'true' : 'false';
			case 'NULL': return 'null';
			case 'integer': return (int) $var;
			case 'double': return (double) $var;
			case 'float': return (float) $var;
			case 'string':
				$var = str_replace("\r",'',$var);
				$var = str_replace("\n",'\n',$var);
				$var = str_replace("^",'\^',$var);
				$var = str_replace('"','\"',$var);
				$var = str_replace('&','&amp;',$var);
				$var = str_replace('<','&lt;',$var);
				$var = str_replace('>','&gt;',$var);
				return (string) '"'.$var.'"';
			case 'array':
				return $this->codificarArray($var);
			case 'object':
				if(get_class($var) == 'TCep') { return json_encode($var->__toString()); }
				if(get_class($var) == 'TData') { return json_encode($var->__toString()); }
				if(get_class($var) == 'TDataHora') { return json_encode($var->__toString()); }
				if(get_class($var) == 'TNumerico') { return json_encode($var->__toString()); }
				if(get_class($var) == 'TTelefone') { return json_encode($var->__toString()); }
				if(get_class($var) == 'TCNPJ') { return json_encode($var->__toString()); }
				if(get_class($var) == 'TDocumentoPessoal') { return json_encode($var->__toString()); }
				if(get_class($var) == 'TMoeda') { return json_encode($var->__toString()); }
				return $this->codificarArray($objeto[get_class($var)] = get_object_vars($var));
		}
	}
	/**
	* Faz a codificação de array
	* @param array array para a codificação .
	* @return string json do array
	*/
	function codificarArray($var){
		if(is_array($var) && (array_keys($var) !== range(0, sizeof($var) - 1))) {
			return sprintf('{%s}', join(',', array_map(array($this, 'montaChave'), array_keys($var), array_values($var))));
		}
		if (is_array($var)) {
			return sprintf('[%s]', join(',', array_map(array($this, 'codificarArray'), $var)));
		}
		return $this->pegarJson($var);
	}
	/**
	* Faz a codificação de uma chave do json
	* @param string nome da chave .
	* @param mixed valor da chave
	* @return string json
	*/
	function montaChave($name, $value){
		return (sprintf("%s:%s", $this->codificarArray(strval($name)), $this->codificarArray($value)));
	}
}
?>