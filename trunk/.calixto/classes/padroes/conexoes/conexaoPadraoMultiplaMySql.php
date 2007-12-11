<?php
/**
* Classe de representação de uma conexão com Banco de Dados
* @package Infra-estrutura
* @subpackage Banco de Dados
*/
class conexaoPadraoMultiplaMySql extends conexao{
	/**
	* Metodo construtor
	* @param [st] Servidor do Banco de dados
	* @param [st] Porta do servidor do Banco de dados
	* @param [st] Nome do Banco de dados
	* @param [st] Usuário do Banco de dados
	* @param [st] Senha do Banco de dados
	*/
	function __construct($servidor, $porta, $banco, $usuario, $senha){
		try{
			$this->conexao = mysql_connect($servidor, $usuario, $senha);
			if( !$this->conexao ) throw new erroBanco( 'erro na conexão com banco de dados' );
			if( !mysql_select_db($banco, $this->conexao)) throw new erroBanco( 'erro na conexão com banco de dados' );
		}
		catch(erroBanco $e){
			throw $e;
		}
	}

	/**
	* Cria um conversor para o Banco de Dados atual
	* @return [conversor]
	*/
	function pegarConversor(){
		return new conversorMySql();
	}

	/**
	* Inicia uma Transação no Banco de Dados
	*/
	function iniciarTransacao(){
		try{
			if( !is_resource($this->conexao) ) throw new erroBanco( 'Conexão fechada para iniciar uma transação!' );
			$this->autoCommit = false;
			mysql_query($this->conexao, 'begin');
			$sterro = mysql_error($this->conexao);
			if (!empty($sterro)) {
				throw new erroBanco($sterro);
			}
		}
		catch(erroBanco $e){
			throw $e;
		}
	}

	/**
	* Confirma uma Transação no Banco de Dados
	*/
	function validarTransacao(){
		try{
			if( !is_resource($this->conexao) ) throw new erroBanco( 'Conexão fechada para validar uma transação!' );
			$this->autoCommit = false;
			mysql_query($this->conexao, 'commit');
			$sterro = mysql_error($this->conexao);
			if (!empty($sterro)) {
				throw new erroBanco($sterro);
			}
		}
		catch(erroBanco $e){
			throw $e;
		}
	}

	/**
	* Desfaz uma Transação aberta no Banco de Dados
	*/
	function desfazerTransacao(){
		try{
			if( !is_resource($this->conexao) ) throw new erroBanco( 'Conexão fechada para desfazer uma transação!' );
			$this->autoCommit = false;
			mysql_query($this->conexao, 'rollback');
			$sterro = mysql_error($this->conexao);
			if (!empty($sterro)) {
				throw new erroBanco($sterro);
			}
		}
		catch(erroBanco $e){
			throw $e;
		}
	}

	/**
	* Fecha a Conexão com o Banco de Dados
	*/
	function fechar(){
		try{
			if(is_resource($this->conexao)){
				mysql_close ($this->conexao);
			}else{
				throw new erroBanco('Não existe conexão para fechar!');
			}
		}
		catch(erroBanco $e){
			ob_start();
			debug_print_backtrace();
			$erro = ob_get_clean();
			echo '<pre>';
			x("\n{$erro}");
			echo '</pre>';
			throw $e;
		}
	}

	/**
	* Executa uma query SQL no Banco de Dados
	* @param [st] Comando SQL a ser executado
	* @return [int] número de linhas afetadas
	*/
	function executarComando($sql){
		try{
			if( !is_resource($this->conexao) ) {
				$erro = new erroBanco( 'Conexão fechada para executar um comando!' );
				$erro->comando = $sql;
				throw $erro;
			}
			$this->cursor = mysql_query(stripslashes($sql),$this->conexao);
			$sterro = mysql_error($this->conexao);
			if (!empty($sterro)) {
				$erro = new erroBanco($sterro);
				$erro->comando = $sql;
				throw $erro;
			}
			return mysql_affected_rows($this->conexao);
		}
		catch(erroBanco $e){
			throw $e;
		}
	}

	/**
	* Retorna um array com o registro retornados corrente da conexão
	* @return [array]
	*/
	function pegarRegistro(){
		try{
			if( !is_resource($this->conexao) ) throw new erroBanco( 'Conexão fechada para pegar um registro!' );
			if ($arRes = mysql_fetch_array ($this->cursor,MYSQL_ASSOC)) {
				foreach($arRes as $stNomeCampo => $stConteudoCampo) {
					$arTupla[strtolower($stNomeCampo)] = $stConteudoCampo;
				}
				return $arTupla;
			}
		}
		catch(erroBanco $e){
			throw $e;
		}
	}
}
?>
