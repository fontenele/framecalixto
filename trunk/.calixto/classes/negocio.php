<?php
/**
* Classe de representação de uma camada de negócio
* A camada de negócio é a parte que engloba as regras e efetua os comandos de execução de um sistema, esta classe especializa e padroniza a classe de negócio
* @package Infra-estrutura
* @subpackage Negocio
*/
abstract class negocio extends objeto{
	/**
	* objeto de conexão com o banco de dados
	* @var [conexao] 
	*/
	public $conexao;
	/**
	* Metodo construtor
	* @param [conexao] (opcional) conexão com o banco de dados
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
	+ Retorna a conexão com o banco de dados, caso não exista, cria uma conexão
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
	* Caso o recurso de conexão não tenha sido passado para a persistente fecha a conexão.
	* @param [conexao] conexão com o banco de dados
	* @return [booleano] se a conexão foi fechada (true) se não.(false)
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
