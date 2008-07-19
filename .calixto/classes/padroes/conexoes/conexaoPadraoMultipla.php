<?php
interface conexaoPadraoMultipla extends conexaoPadrao{
	/**
	* Metodo construtor
	* @param [st] Servidor do Banco de dados
	* @param [st] Porta do servidor do Banco de dados
	* @param [st] Nome do Banco de dados
	* @param [st] Usuário do Banco de dados
	* @param [st] Senha do Banco de dados
	*/
	public function __construct($servidor, $porta, $banco, $usuario, $senha);
	/**
	* Fecha a Conexão com o Banco de Dados
	*/
	public function __destruct();
	/**
	* Método que abre a conexão com o banco de dados
	*/
	protected function conectar();
}
?>
