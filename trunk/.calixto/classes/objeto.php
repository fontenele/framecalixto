<?php
/**
* Classe abstrata inicial
* Esta classe na hierarquia serve como pai das demais classes
* @package Infra-estrutura
*/
abstract class objeto{
	/**
	* Método de sobrecarga para evitar a criação de métodos repetitivos
	* @param [string] metodo chamado
	* @param [array] parâmetros parassados para o método chamado
	*/
	protected function __call($metodo, $parametros){
		try{
			if (preg_match('/(pegar|passar)(.*)/', $metodo, $resultado)) {
				$var = strtolower($resultado[2]{0}).substr($resultado[2],1,strlen($resultado[2]));
				if ($resultado[1] == 'passar') {
					$r = new ReflectionProperty(get_class($this), $var);
					if(!$r->getName()) throw new erro();
					$this->$var = $parametros[0];
					return;
				} else {
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