<?php
/**
* Interface de definição de uma conexão multipla
* @package FrameCalixto
* @subpackage Banco de Dados
*/
interface conexaoPadraoMultipla extends conexaoPadrao{
	/**
	* Metodo construtor
	* @param string Servidor do Banco de dados
	* @param string Porta do servidor do Banco de dados
	* @param string Nome do Banco de dados
	* @param string Usuário do Banco de dados
	* @param string Senha do Banco de dados
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
