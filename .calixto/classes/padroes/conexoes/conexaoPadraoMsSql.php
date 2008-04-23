<?php
/**
* Classe de representação de uma conexão com Banco de Dados
* @package Infra-estrutura
* @subpackage Banco de Dados
*/
class conexaoPadraoMsSql extends conexao{
	/**
	* O ponteiro do recurso com o resultado do comando
	* @var [resource]
	*/
	protected static $cursorEstatico;
	/**
	* Conexao statica para singleton
	*/
	protected static $conexaoEstatica;
	/**
	* Verificador de transação
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
		try{
			if(is_resource(conexaoPadraoMsSql::$conexaoEstatica)){
				mssql_close (conexaoPadraoMsSql::$conexaoEstatica);
			}else{
				throw new erroBanco( 'erro na conexão com banco de dados' );
			}
		}
		catch(erroBanco $e){
			throw $e;
		}
	}
	/**
	* Metodo de conexão
	* @param [st] Servidor do Banco de dados
	* @param [st] Nome do Banco de dados
	* @param [st] Usuário do Banco de dados
	* @param [st] Senha do Banco de dados
	*/
	public static function conectar($servidor, $banco, $usuario, $senha){
		try{
			if(!is_resource(conexaoPadraoMsSql::$conexaoEstatica)){
				conexaoPadraoMsSql::$conexaoEstatica = mssql_connect($servidor, $usuario, $senha);
				if( !is_resource(conexaoPadraoMsSql::$conexaoEstatica) ){
					throw new erroBanco( 'erro na conexão com banco de dados' );
				}
				mssql_select_db($banco, conexaoPadraoMsSql::$conexaoEstatica);
			//	conexaoPadraoMsSql::executar("SET DATESTYLE TO German;");
			//	conexaoPadraoMsSql::executar("SET CLIENT_ENCODING TO UTF8;");
			}
			return new conexaoPadraoMsSql();
		}
		catch(erroBanco $e){
			throw $e;
		}
	}
	/**
	* Inicia uma Transação no Banco de Dados
	*/
	function iniciarTransacao(){
		try{
			if( !is_resource(conexaoPadraoMsSql::$conexaoEstatica) ) throw new erroBanco( 'Conexão fechada para iniciar uma transação!' );
			conexaoPadraoMsSql::$autoCommitEstatico = false;
			mssql_query('begin', conexaoPadraoMsSql::$conexaoEstatica);
			$sterro = mssql_get_last_message(conexaoPadraoMsSql::$conexaoEstatica);
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
			if( !is_resource(conexaoPadraoMsSql::$conexaoEstatica) ) throw new erroBanco( 'Conexão fechada para validar uma transação!' );
			conexaoPadraoMsSql::$autoCommitEstatico = false;
			mssql_query('commit',conexaoPadraoMsSql::$conexaoEstatica);
			$sterro = mssql_get_last_message(conexaoPadraoMsSql::$conexaoEstatica);
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
			if( !is_resource(conexaoPadraoMsSql::$conexaoEstatica) ) throw new erroBanco( 'Conexão fechada para desfazer uma transação!' );
			conexaoPadraoMsSql::$autoCommitEstatico = false;
			mssql_query('rollback',conexaoPadraoMsSql::$conexaoEstatica);
			$sterro = mssql_get_last_message(conexaoPadraoMsSql::$conexaoEstatica);
			if (!empty($sterro)) {
				throw new erroBanco($sterro);
			}
		}
		catch(erroBanco $e){
			throw $e;
		}
	}

	/**
	* Executa uma query SQL no Banco de Dados
	* @param [st] Comando SQL a ser executado
	* @return [int] número de linhas afetadas
	*/
	function executarComando($sql){
		return conexaoPadraoMsSql::executar($sql);
	}
	/**
	* Executa uma query SQL no Banco de Dados
	* @param [st] Comando SQL a ser executado
	* @return [int] número de linhas afetadas
	*/
	protected static function executar($sql){
		try{
			if( !is_resource(conexaoPadraoMsSql::$conexaoEstatica) ) {
				debug_print_backtrace();
				$erro = new erroBanco( 'Conexão fechada para executar um comando!' );
				$erro->comando = $sql;
				throw $erro;
			}
			conexaoPadraoMsSql::$cursorEstatico = @ mssql_query(stripslashes(utf8_decode($sql)), conexaoPadraoMsSql::$conexaoEstatica);
			$sterro = mssql_get_last_message(conexaoPadraoMsSql::$conexaoEstatica);
			if (empty($sterro)) {
				$erro = new erroBanco($sterro);
				$erro->comando = $sql;
				throw $erro;
			}
			//return;
			return mssql_rows_affected(conexaoPadraoMsSql::$cursorEstatico);
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
			if( !is_resource(conexaoPadraoMsSql::$conexaoEstatica) ) throw new erroBanco( 'Conexão fechada para pegar um registro!' );
			if ($arRes = mssql_fetch_assoc (conexaoPadraoMsSql::$cursorEstatico)) {
				foreach($arRes as $stNomeCampo => $stConteudoCampo) {
					$arTupla[strtolower($stNomeCampo)] = utf8_encode($stConteudoCampo);
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