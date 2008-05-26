<?php
/**
* Classe que representa uma coleção de itens
* Esta classe padroniza a forma de agrupamento de itens no sistema
* @package Infra-estrutura
* @subpackage utilitários
*/
class colecao extends objeto{
	/**
	* @var [vetor] itens da colecao
	*/
	public $itens;
	/**
	* Metodo construtor
	* @param [vetor] (opcional) dados da colecao
	*/
	public function __construct($array = null){
		if(is_array($array)){
			$this->itens = $array;
		}else{
			$this->itens = array();
		}
	}
	/**
	* Método retorno de um item na ordem da coleção
	* @return [mixed] Item da coleção
	*/
	public function retornarItem($item = 0){
		$chaves = array_keys($this->itens);
		if(isset($this->itens[$chaves[$item]])) return $this->itens[$chaves[$item]];
	}
	/**
	* Método de avanço da coleção 
	* @return [mixed] Item da coleção
	*/
	public function avancar(){
		$ar = each($this->itens);
		if(isset($ar['value'])) {
			return $ar['value'];
		}else{
			reset($this->itens);
			return false;
		}
	}
	/**
	* Método de captura de valor pelo indice da colecao
	* @param [string] Indice da coleção
	* @return [mixed] Item da coleção
	*/
	public function pegar($indice){
		return $this->itens[$indice];
	}
	/**
	* Método de envio de valor pelo indice da colecao
	* @param [string] Indice da coleção
	* @param [mixed] Item da coleção
	*/
	public function passar($indice,$item){
		return $this->itens[$indice] = $item;
	}
	/**
	* Método de sobrecarga para evitar a criação de métodos repetitivos
	* @param [string] metodo chamado
	* @param [array] parâmetros parassados para o método chamado
	*/
	function __set($variavel, $parametros){
		$this->itens[$variavel] = $parametros;
    }
	/**
	* Método de sobrecarga para evitar a criação de métodos repetitivos
	* @param [string] metodo chamado
	* @param [array] parâmetros parassados para o método chamado
	*/
	function __get($variavel){
		return $this->itens[$variavel];
    }
	/**
	* Retorna verdadeiro se a coleção possui dados
	* @return [booleano] retorno de dados da coleção
	*/
	function possuiItens(){
		return (boolean) count($this->itens);
	}
	/**
	* Retorna a quantidade de itens da coleção
	* @return [booleano] retorno de dados da coleção
	*/
	function contarItens(){
		return count($this->itens);
	}
}
?>
