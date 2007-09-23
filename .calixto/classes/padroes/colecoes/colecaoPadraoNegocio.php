<?php
/**
* Classe que representa uma coleção de negócios
* Esta classe padroniza a forma de agrupamento de classes de negócio no sistema
* @package Infra-estrutura
* @subpackage utilitários
*/
class colecaoPadraoNegocio extends colecaoPadraoObjeto{
	/**
	* objeto de conexão com o banco de dados
	* @var [conexao]
	*/
	public $conexao;
	/**
	* Metodo construtor
	* @param [vetor] (opcional) dados da colecao
	* @param [conexao] (opcional) conexão com o banco de dados
	*/
	public function __construct($array = null,conexao $conexao = null){
		parent::__construct($array);
		if($conexao){
			$this->conexao = $conexao;
		}else{
			$this->conexao = conexao::criar();
		}
	}
	/**
	* Método de sobrecarga para evitar a criação de métodos repetitivos
	* @param [string] metodo chamado
	* @param [array] parâmetros parassados para o método chamado
	*/
	function __set($variavel, $parametros){
		if (!($parametros instanceof negocio))
			throw new InvalidArgumentException('Não foi passado um negocio para '.get_class($this).'!');
		parent::__set($variavel, $parametros);
    }
	/**
	* Metodo construtor
	* @param [conexao] (opcional) conexão com o banco de dados
	*/
	public final function conectar(){
		try{
			if(is_resource($this->conexao)) return;
			$this->conexao = conexao::criar();
		}
		catch(erro $e){
			throw $e;
		}
	}
    /**
    * Método de gravação da coleção de negócios no banco de dados
    * @param [conexao] conexao para executar a gravação
    * @return [vetor] vetor com os valores do atributo dos negócios
    */
    function gravar(){
		try{
			foreach($this->itens as $indice => $objeto){
				$objeto->passarConexao($this->conexao);
				$objeto->gravar();
			}
		}
		catch(erro $e){
			throw $e;
		}
    }
    /**
    * Método de gravação da coleção de negócios no banco de dados
    * @param [conexao] conexao para executar a gravação
    * @return [vetor] vetor com os valores do atributo dos negócios
    */
    function excluir(){
		try{
			foreach($this->itens as $indice => $objeto){
				$objeto->passarConexao($this->conexao);
				$objeto->excluir();
			}
		}
		catch(erro $e){
			throw $e;
		}
    }
    /**
    * Método de indexação de itens pelo identificador da classe de negócio
    */
    function indexarPorId(){
		try{
			$itens = array();
			foreach($this->itens as $negocio){
				$itens[$negocio->valorChave()] = $negocio;
			}
			$this->itens = $itens;
		}
		catch(erro $e){
			throw $e;
		}
    }
    /**
    * Método de geração de um vetor de um atributo do negócio
    * @param [string] primeiro item
    * @return [vetor] vetor com os valores do atributo dos negócios
    */
    function gerarVetorDescritivo($vazio = false){
		$arRetorno = array();
		if($vazio !== false) $arRetorno[''] = $vazio;
		foreach($this->itens as $indice => $negocio){
			$arRetorno[$negocio->valorChave()] = $negocio->valorDescricao();
		}
		return $arRetorno;
    }
}
?>