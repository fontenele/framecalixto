<?php
/**
* Classe que representa uma coleção de itens
* Esta classe padroniza a forma de agrupamento de classes de negócio no sistema
* @package FrameCalixto
* @subpackage utilitários
*/
class colecaoPadraoObjeto extends colecao{
	/**
	* Método de sobrecarga para evitar a criação de métodos repetitivos
	* @param string metodo chamado
	* @param array parâmetros parassados para o método chamado
	*/
	function __set($variavel, $parametros){
		if (!($parametros instanceof objeto))
			throw new InvalidArgumentException('Não foi passado um objeto para '.get_class($this).'!');
		parent::__set($variavel, $parametros);
    }
    /**
    * Método de geração de um vetor de um atributo do negócio
    * @param string da variavel do objeto
    * @return array vetor com os valores do atributo dos negócios
    */
    function gerarVetorDeAtributo($atributo){
		if(is_array($atributo)){
			$atributo = implode('->',$atributo);
			$arRetorno = array();
			foreach($this->itens as $indice => $objeto){
				$arRetorno[] = $objeto->$atributo();
			}
		}else{
			$atributo = 'pegar'.ucfirst($atributo);
			$arRetorno = array();
			foreach($this->itens as $indice => $objeto){
				$arRetorno[] = $objeto->$atributo();
			}
		}
		return $arRetorno;
    }
    /**
    * Método de indexação de itens por um atributo do objeto (Caso existam valores repetidos será mantido o ultimo objeto)
    */
    function indexarPorAtributo($atributo){
		try{
			$itens = array();
			$atributo = 'pegar'.ucfirst($atributo);
			foreach($this->itens as $objeto){
				$itens[$objeto->$atributo()] = $objeto;
			}
			$this->itens = $itens;
		}
		catch(erro $e){
			throw $e;
		}
    }
}
?>