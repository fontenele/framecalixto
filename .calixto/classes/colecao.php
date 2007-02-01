<?php
/**
* Classe que representa uma cole��o de itens
* Esta classe padroniza a forma de agrupamento de itens no sistema
* @package Infra-estrutura
* @subpackage utilit�rios
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
	* M�todo retorno de um item na ordem da cole��o
	* @return [mixed] Item da cole��o
	*/
	public function retornarItem($numeroItem = 1){
		$numeroItem--;
		$chaves = array_keys($this->itens);
		if(isset($this->itens[$chaves[$numeroItem]])) return $this->itens[$chaves[$numeroItem]];
	}
	/**
	* M�todo de avan�o da cole��o 
	* @return [mixed] Item da cole��o
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
	* M�todo de sobrecarga para evitar a cria��o de m�todos repetitivos
	* @param [string] metodo chamado
	* @param [array] par�metros parassados para o m�todo chamado
	*/
	function __set($variavel, $parametros){
		$this->itens[$variavel] = $parametros;
    }
	/**
	* M�todo de sobrecarga para evitar a cria��o de m�todos repetitivos
	* @param [string] metodo chamado
	* @param [array] par�metros parassados para o m�todo chamado
	*/
	function __get($variavel){
		return $this->itens[$variavel];
    }
	/**
	* Retorna verdadeiro se a cole��o possui dados
	* @return [booleano] retorno de dados da cole��o
	*/
	function possuiItens(){
		return (boolean) count($this->itens);
	}
	/**
	* Retorna a quantidade de itens da cole��o
	* @return [booleano] retorno de dados da cole��o
	*/
	function contarItens(){
		return count($this->itens);
	}
}
?>
