<?php
/**
* Classe de representação de uma conexão com Banco de Dados
* @package FrameCalixto
* @subpackage Banco de Dados
*/
class conexaoPadraoPG extends conexao implements conexaoPadraoEstatica{
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
			if(is_resource(conexaoPadraoPG::$conexaoEstatica)){
				pg_close (conexaoPadraoPG::$conexaoEstatica);
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
	* @param [st] Porta do servidor do Banco de dados
	* @param [st] Nome do Banco de dados
	* @param [st] Usuário do Banco de dados
	* @param [st] Senha do Banco de dados
	*/
	public static function conectar($servidor, $porta, $banco, $usuario, $senha){
		try{
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
		catch(erroBanco $e){
			throw $e;
		}
	}
	/**
	* Inicia uma Transação no Banco de Dados
	*/
	function iniciarTransacao(){
		try{
			if( !is_resource(conexaoPadraoPG::$conexaoEstatica) ) throw new erroBanco( 'Conexão fechada para iniciar uma transação!' );
			conexaoPadraoPG::$autoCommitEstatico = false;
			pg_query(conexaoPadraoPG::$conexaoEstatica, 'begin');
			$sterro = pg_last_error(conexaoPadraoPG::$conexaoEstatica);
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
			if( !is_resource(conexaoPadraoPG::$conexaoEstatica) ) throw new erroBanco( 'Conexão fechada para validar uma transação!' );
			conexaoPadraoPG::$autoCommitEstatico = false;
			pg_query(conexaoPadraoPG::$conexaoEstatica, 'commit');
			$sterro = pg_last_error(conexaoPadraoPG::$conexaoEstatica);
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
			if( !is_resource(conexaoPadraoPG::$conexaoEstatica) ) throw new erroBanco( 'Conexão fechada para desfazer uma transação!' );
			conexaoPadraoPG::$autoCommitEstatico = false;
			pg_query(conexaoPadraoPG::$conexaoEstatica, 'rollback');
			$sterro = pg_last_error(conexaoPadraoPG::$conexaoEstatica);
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
		try{
			return conexaoPadraoPG::executar($sql);
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
	protected static function executar($sql){
		try{
			if( !is_resource(conexaoPadraoPG::$conexaoEstatica) ) {
				debug_print_backtrace();
				$erro = new erroBanco( 'Conexão fechada para executar um comando!' );
				$erro->comando = $sql;
				throw $erro;
			}
			conexaoPadraoPG::$cursorEstatico = @pg_query(conexaoPadraoPG::$conexaoEstatica,stripslashes($sql));
			$sterro = pg_last_error(conexaoPadraoPG::$conexaoEstatica);
			if (!empty($sterro)) {
				$erro = new erroBanco($sterro);
				$erro->comando = $sql;
				throw $erro;
			}
			return pg_affected_rows(conexaoPadraoPG::$cursorEstatico);
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
			if( !is_resource(conexaoPadraoPG::$conexaoEstatica) ) throw new erroBanco( 'Conexão fechada para pegar um registro!' );
			if ($arRes = pg_fetch_array (conexaoPadraoPG::$cursorEstatico,NULL,PGSQL_ASSOC)) {
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