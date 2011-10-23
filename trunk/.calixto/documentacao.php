<?php
/**
 * Realiza a leitura da documentação de um reflection
 *
 * @author calixto
 */
class documentacao {
	protected $arDoc = array();
	public function  __construct($string) {
		if($string instanceof Reflector) $string = $string->getDocComment();
		$string = str_replace('/*', '', str_replace('*/','',$string));
		$arDoc = explode("\n",$string);
		$resDoc = array();
		$doc = '';
		foreach($arDoc as $linDoc){
			if(preg_match('/^[\ \t]*\/\*\*[\ \t]*(.*)/', $linDoc,$match)){
				if(!isset($resDoc[$doc])) $resDoc[$doc] = '';
				$resDoc[$doc] .= $match[1]."\n";
				continue;
			}
			if(preg_match('/^[\ \t]*\*[\ \t]*\@([aA-zZ]+)[\ \t]*(.*)/', $linDoc,$match)){
				$doc = strtolower($match[1]);
				if(!isset($resDoc[$doc])) $resDoc[$doc] = '';
				$resDoc[$doc] .= $match[2]."\n";
				continue;
			}
			if(preg_match('/^[\ \t]*\*[\ \t]*(.*)/', $linDoc,$match)){
				if(!isset($resDoc[$doc])) $resDoc[$doc] = '';
				$resDoc[$doc] .= $match[1]."\n";
				continue;
			}
		}
		$this->arDoc = $resDoc;
	}
	/**
	 * Retorna a string referente ao objeto
	 * @return string
	 */
	public function  __toString() {
		return implode( "\n", $this->arDoc);
	}
	/**
	 * Retorna a documentação referente
	 * @param string $tipo tipos disponíveis: access , author, package, subpackage, param, return, since, var, version.
	 * @return string
	 */
	public function pegar($tipo = null){
		return isset($this->arDoc[$tipo]) ? $this->arDoc[$tipo] : null;
	}
}
?>