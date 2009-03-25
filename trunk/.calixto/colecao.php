<?php
/**
* Classe que representa uma coleção de itens
* Esta classe padroniza a forma de agrupamento de itens no sistema
* @package FrameCalixto
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
	* Método que reseta a indexação da coleção
	*/
	public function resetar(){
		reset($this->itens);
	}
	/**
	 * Método que remove um item da coleção
	 */
	public function removerItem($item){
		if($this->tem($item)){
			unset($this->itens[$item]);
		}
	}
	/**
	* Método de captura de valor pelo indice da colecao
	* @param [string] Indice da coleção
	* @return [mixed] Item da coleção
	*/
	public function pegar($indice = null){
		if(!$indice){
			$ar = array_keys($this->itens);
			if(!$ar) throw new Erro("Coleção vazia!");
			if($this->tem($ar[0])){
				return $this->itens[$ar[0]];
			}else{
				throw new Erro("Item {$ar[0]} Inexistente na coleção!");
			}
		}
		if($this->tem($indice)){
			return $this->itens[$indice];
		}else{
			throw new Erro("Item {$indice} Inexistente na coleção!");
		}
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
	/**
	* Retorna se um item está na coleção
	* @return booleano retorno de dados da coleção
	*/
	function tem($item){
		return isset($this->itens[$item]);
	}
}
?>
