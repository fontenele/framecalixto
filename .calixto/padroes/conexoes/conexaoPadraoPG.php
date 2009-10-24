<?php
/**
* Classe de representação de uma conexão com Banco de Dados
* @package FrameCalixto
* @subpackage Banco de Dados
*/
class conexaoPadraoPG extends conexao implements conexaoPadraoEstatica{
	/**
	* O ponteiro do recurso com o resultado do comando
	* @var resource
	*/
	protected static $cursorEstatico;
	/**
	* Conexao statica para singleton
	* @var resource
	*/
	protected static $conexaoEstatica;
	/**
	* Verificador de transação
	* @var boolean
	*/
	protected static $autoCommitEstatico;
	/**
	* Metodo construtor
	*/
	final public function __construct(){}
	/**
	* Desconecta do banco de dados
	*/
	public function desconectar(){
		if(!is_resource(conexaoPadraoPG::$conexaoEstatica))throw new erroBanco( 'erro na conexão com banco de dados' );
		pg_close (conexaoPadraoPG::$conexaoEstatica);
	}
	/**
	* Metodo de conexão
	* @param string Servidor do Banco de dados
	* @param string Porta do servidor do Banco de dados
	* @param string Nome do Banco de dados
	* @param string Usuário do Banco de dados
	* @param string Senha do Banco de dados
	*/
	public static function conectar($servidor, $porta, $banco, $usuario, $senha){
		if(!is_resource(conexaoPadraoPG::$conexaoEstatica)){
			conexaoPadraoPG::$conexaoEstatica = pg_connect("host=$servidor port=$porta dbname=$banco user=$usuario password=$senha");
			if( !is_resource(conexaoPadraoPG::$conexaoEstatica) ){
				throw new erroBanco( 'erro na conexão com banco de dados' );
			}
			conexaoPadraoPG::executar("SET DATESTYLE TO German;");
			conexaoPadraoPG::executar("SET CLIENT_ENCODING TO UTF8;");
		}
		return new conexaoPadraoPG();
	}
	/**
	* Inicia uma Transação no Banco de Dados
	*/
	function iniciarTransacao(){
		if( !is_resource(conexaoPadraoPG::$conexaoEstatica) ) throw new erroBanco( 'Conexão fechada para iniciar uma transação!' );
		conexaoPadraoPG::$autoCommitEstatico = false;
		pg_query(conexaoPadraoPG::$conexaoEstatica, 'begin');
		$sterro = pg_last_error(conexaoPadraoPG::$conexaoEstatica);
		if (!empty($sterro)) {
			throw new erroBanco($sterro);
		}
	}
	/**
	* Confirma uma Transação no Banco de Dados
	*/
	function validarTransacao(){
		if( !is_resource(conexaoPadraoPG::$conexaoEstatica) ) throw new erroBanco( 'Conexão fechada para validar uma transação!' );
		conexaoPadraoPG::$autoCommitEstatico = false;
		pg_query(conexaoPadraoPG::$conexaoEstatica, 'commit');
		$sterro = pg_last_error(conexaoPadraoPG::$conexaoEstatica);
		if (!empty($sterro)) {
			throw new erroBanco($sterro);
		}
	}
	/**
	* Desfaz uma Transação aberta no Banco de Dados
	*/
	function desfazerTransacao(){
		if( !is_resource(conexaoPadraoPG::$conexaoEstatica) ) throw new erroBanco( 'Conexão fechada para desfazer uma transação!' );
		conexaoPadraoPG::$autoCommitEstatico = false;
		pg_query(conexaoPadraoPG::$conexaoEstatica, 'rollback');
		$sterro = pg_last_error(conexaoPadraoPG::$conexaoEstatica);
		if (!empty($sterro)) {
			throw new erroBanco($sterro);
		}
	}
	/**
	* Executa uma query SQL no Banco de Dados
	* @param string Comando SQL a ser executado
	* @return integer número de linhas afetadas
	*/
	function executarComando($sql){
		return conexaoPadraoPG::executar($sql);
	}
	/**
	* Executa uma query SQL no Banco de Dados
	* @param string Comando SQL a ser executado
	* @return integer número de linhas afetadas
	*/
	protected static function executar($sql){
		if( !is_resource(conexaoPadraoPG::$conexaoEstatica) ) {
			debug_print_backtrace();
			$erro = new erroBanco( 'Conexão fechada para executar um comando!' );
			$erro->comando = $sql;
			throw $erro;
		}
		ob_start();
		conexaoPadraoPG::$cursorEstatico = pg_query(conexaoPadraoPG::$conexaoEstatica,stripslashes($sql));
		if(($res = ob_end_clean()) && ($res != 1)) throw new erroBanco($res);
		if (($sterro = pg_last_error(conexaoPadraoPG::$conexaoEstatica))) {
			$erro = new erroBanco($sterro);
			$erro->comando = $sql;
			throw $erro;
		}
		return pg_affected_rows(conexaoPadraoPG::$cursorEstatico);
	}
	/**
	* Retorna um array com o registro retornados corrente da conexão
	* @return array
	*/
	function pegarRegistro(){
		if( !is_resource(conexaoPadraoPG::$conexaoEstatica) ) throw new erroBanco( 'Conexão fechada para pegar um registro!' );
		if (($arRes = pg_fetch_array (conexaoPadraoPG::$cursorEstatico,NULL,PGSQL_ASSOC))) return array_change_key_case($arRes,CASE_LOWER);
		return false;
	}
}
?>