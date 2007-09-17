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
	public function __construct(conexao $conexao = null){
		try{
			if($conexao){
				$this->conexao = $conexao;
			}else{
				$this->conexao = conexao::criar();
			}
		}
		catch(erro $e){
			throw $e;
		}
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
}
?>