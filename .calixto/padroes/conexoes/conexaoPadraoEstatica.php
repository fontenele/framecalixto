<?php
/**
* Interface de definição de uma conexão estática
* @package FrameCalixto
* @subpackage Banco de Dados
*/
interface conexaoPadraoEstatica extends conexaoPadrao{
	/**
	* Metodo de conexão
	* @param [st] Servidor do Banco de dados
	* @param [st] Porta do servidor do Banco de dados
	* @param [st] Nome do Banco de dados
	* @param [st] Usuário do Banco de dados
	* @param [st] Senha do Banco de dados
	*/
	public static function conectar($servidor, $porta, $banco, $usuario, $senha);
	/**
	* Desconecta do banco de dados
	*/
	public function desconectar();
}
?>
