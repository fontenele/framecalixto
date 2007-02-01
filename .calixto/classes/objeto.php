<?php
include_once('.calixto/debug.php');
/**
* Classe abstrata inicial
* Esta classe na hierarquia serve como pai das demais classes
* @package Infra-estrutura
*/
abstract class objeto{
	/**
	* M�todo de sobrecarga para evitar a cria��o de m�todos repetitivos
	* @param [string] metodo chamado
	* @param [array] par�metros parassados para o m�todo chamado
	*/
	public function __call($metodo, $parametros){
		try{
			if (preg_match('/(pegar|passar)(.*)/', $metodo, $resultado)) {
				$var = strtolower($resultado[2]{0}).substr($resultado[2],1,strlen($resultado[2]));
				if ($resultado[1] == 'passar') {
					$this->$var = $parametros[0];
					return;
				} else {
					return $this->$var;
				}
			}
			throw new erro();
		}
		catch(erro $e){
			$debug = debug_backtrace();
			echo 'Chamada de m�todo inexistente !!!';
			$arRetorno['No Arquivo'] = $debug[1]['file'];
			$arRetorno['Na Linha'] = $debug[1]['line'];
			$arRetorno['Na Chamada'] = $debug[1]['function'];
			$arRetorno['Da Classe'] = $debug[1]['class'];
			$arRetorno['Argumentos'] = $debug[1]['args'];
			x($arRetorno);
			throw $e;
		}
    }
	/**
	* M�todo de sobrecarga para printar a classe
	* @return [string] texto de sa�da da classe
	*/
	public function __toString(){
		debug2($this);
		return '';
	}
	/**
	* M�todo de sobrecarga para serializar a classe
	*/
	public function __sleep(){
		return array_keys(get_object_vars($this));
	}
	/**
	* M�todo de sobrecarga para desserializar a classe
	*/
	public function __wakeup(){}
}
?>
