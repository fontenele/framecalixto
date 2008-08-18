<?php
/**
* Representação de comportamento de uma conexaoPadrao 
* @package FrameCalixto
* @subpackage Banco de Dados
*/
interface conexaoPadrao{
	/**
	* Inicia uma Transação no Banco de Dados
	*/
	function iniciarTransacao();
	/**
	* Confirma uma Transação no Banco de Dados
	*/
	function validarTransacao();
	/**
	* Desfaz uma Transação aberta no Banco de Dados
	*/
	function desfazerTransacao();
	/**
	* Executa uma query SQL no Banco de Dados
	* @param [st] Comando SQL a ser executado
	* @return [int] número de linhas afetadas
	*/
	function executarComando($sql);
	/**
	* Retorna um array com o registro retornado corrente da conexão
	* @return array
	*/
	function pegarRegistro();
}
?>
