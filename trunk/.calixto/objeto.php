<?php
/**
* Classe abstrata inicial
* Esta classe na hierarquia serve como pai das demais classes
* @package FrameCalixto
*/
abstract class objeto{
	/**
	 * @return array Retorna os atributos do objeto
	 */
	public function __atributos() {
		$reflect = new ReflectionObject($this);
		$vars = array();
		foreach ($reflect->getProperties(ReflectionProperty::IS_PUBLIC + ReflectionProperty::IS_PROTECTED) as $prop) {
			$vars[$prop->getName()] = $prop->getName() ;
		}
		return $vars;
	}
	/**
	* Método de sobrecarga para evitar a criação de métodos repetitivos
	* @param [string] metodo chamado
	* @param [array] parâmetros parassados para o método chamado
	*/
	protected function __call($metodo, $parametros){
		try{
			if (preg_match('/(pegar|passar)(.*)/', $metodo, $resultado)) {
				$var = strtolower($resultado[2]{0}).substr($resultado[2],1,strlen($resultado[2]));
				$r = new ReflectionProperty(get_class($this), $var);
				if(!$r->getName()) throw new erro();
				if ($resultado[1] == 'passar') {
					$this->$var = $parametros[0];
					return;
				} else {
					if($r->isStatic()) throw new erro('Atributo statico protegido.');
					return $this->$var;
				}
			}
			throw new erro('Chamada inexistente!');
		}
		catch (ReflectionException $e){
			$propriedade = get_class($this).'::'.$var;
			throw new erro("Propriedade [{$propriedade}] inexistente!");
		}
		catch(erro $e){
			throw $e;
		}
    }
	/**
	* Método de sobrecarga para printar a classe
	* @return [string] texto de saída da classe
	*/
	public function __toString(){
		debug2($this);
		return '';
	}
	/**
	* Método de codificação para JSON
	* @return [string] JSON
	*/
	public function json(){
		$json = new json();
		return $json->pegarJson($this);
	}
    /**
    * Método de codificação para XML
    * @return string XML
    */
    public function xml(){
        $variaveis = get_object_vars($this);
        if(isset($variaveis['conexao'])) unset($variaveis['conexao']);
        $classe = get_class($this);
        $xml = "<{$classe}>\n";
        foreach($variaveis as $var => $val){
            if($val instanceof objeto) $val = $val->xml();
            if(is_array($val)) $val = $this->arrayXml($val);
            $xml.="<{$var}>{$val}</{$var}>\n";
        }
        $xml.= "</{$classe}>\n\n";
		return $xml;
    }
    protected function arrayXml($array){
        $xml = "\n";
        foreach($array as $var => $val){
            if($val instanceof objeto) $val = $val->xml();
            if(is_array($val)) $val = $this->arrayXml($val);
            $xml.="<{$var}>{$val}</{$var}>\n";
        }
        $xml.= "\n\n";
        return $xml;
    }
	/**
	* Método de sobrecarga para serializar a classe
	*/
	protected function __sleep(){
		return array_keys(get_object_vars($this));
	}
	/**
	* Método de sobrecarga para desserializar a classe
	*/
	protected function __wakeup(){}
}
?>