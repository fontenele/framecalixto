<?php
/**
* Interface de definição de uma conexão estática
* @package FrameCalixto
* @subpackage Banco de Dados
*/
interface conexaoPadraoEstatica extends conexaoPadrao{
	/**
	* Metodo de conexão
	* @param string Servidor do Banco de dados
	* @param string Porta do servidor do Banco de dados
	* @param string Nome do Banco de dados
	* @param string Usuário do Banco de dados
	* @param string Senha do Banco de dados
	*/
	public static function conectar($servidor, $porta, $banco, $usuario, $senha);
	/**
	* Desconecta do banco de dados
	*/
	public function desconectar();
}
?>
