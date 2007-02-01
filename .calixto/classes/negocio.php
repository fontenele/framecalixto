<?php
/**
* Classe de representa��o de uma camada de neg�cio
* A camada de neg�cio � a parte que engloba as regras e efetua os comandos de execu��o de um sistema, esta classe especializa e padroniza a classe de neg�cio
* @package Infra-estrutura
* @subpackage Negocio
*/
abstract class negocio extends objeto{
	/**
	* objeto de conex�o com o banco de dados
	* @var [conexao] 
	*/
	public $conexao;
	/**
	* Metodo construtor
	* @param [conexao] (opcional) conex�o com o banco de dados
	*/
	public function __construct($conexao = null){
		try{
			if(is_subclass_of($conexao,'conexao')){
				$this->conexao = $conexao;
			}
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	+ Retorna a conex�o com o banco de dados, caso n�o exista, cria uma conex�o
	*/
	final function pegarConexao(){
		try{
			if(is_subclass_of($this->conexao,'conexao')){
				return  $this->conexao;
			}else{
				return conexao::criar();
			}
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Caso o recurso de conex�o n�o tenha sido passado para a persistente fecha a conex�o.
	* @param [conexao] conex�o com o banco de dados
	* @return [booleano] se a conex�o foi fechada (true) se n�o.(false)
	*/
	final function fecharConexao(conexao $conexao){
		try{
			if($this->conexao !== $conexao){
				$conexao->fechar();
				return true;
			}
			return false;
		}
		catch(erro $e){
			throw $e;
		}
	}
}
?>
