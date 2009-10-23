<?php
/**
* Classe de representação de uma conexão com Banco de Dados
* @package FrameCalixto
* @subpackage Banco de Dados
*/
class conexaoPadraoOCI extends conexao implements conexaoPadraoEstatica{
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
			if(is_resource(conexaoPadraoOCI::$conexaoEstatica)){
				oci_close(conexaoPadraoOCI::$conexaoEstatica);
			}else{
				throw new erroBanco( 'Não existe recurso para o fechamento da conexão.' );
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
		if(!is_resource(conexaoPadraoOCI::$conexaoEstatica)){
			if($servidor && $porta){
				conexaoPadraoOCI::$conexaoEstatica = oci_connect($usuario,$senha,
				"(DESCRIPTION =(ADDRESS = (PROTOCOL = TCP)(HOST ={$servidor})(PORT = {$porta}))(CONNECT_DATA = (SERVER = DEDICATED)(SERVICE_NAME = {$banco})))");
			}else{
				conexaoPadraoOCI::$conexaoEstatica = oci_connect($usuario,$senha,$banco);
			}
			if( !is_resource(conexaoPadraoOCI::$conexaoEstatica) ){
				throw new erroBanco( 'Erro ao estabelecer conexão com banco de dados' );
			}
			conexaoPadraoOCI::executar( 'alter session set NLS_LANGUAGE="BRAZILIAN PORTUGUESE"' );
			conexaoPadraoOCI::executar( 'alter session set NLS_NUMERIC_CHARACTERS =",."' );
			conexaoPadraoOCI::executar( 'alter session set NLS_DATE_FORMAT = "dd/mm/yyyy HH24:MI:SS"' );
			conexaoPadraoOCI::executar( 'alter session set NLS_NCHAR_CHARACTERSET=UTF8' );
			conexaoPadraoOCI::executar( 'alter session set NLS_SORT="BINARY"' );
			conexaoPadraoOCI::executar( 'alter session set skip_unusable_indexes=true' );
		}
		return new conexaoPadraoOCI();
	}
	/**
	* Inicia uma Transação no Banco de Dados
	*/
	function iniciarTransacao(){
		if( !is_resource(conexaoPadraoOCI::$conexaoEstatica) ) throw new erroBanco( 'Conexão fechada para iniciar uma transação!' );
		conexaoPadraoOCI::$autoCommitEstatico = false;
	}

	/**
	* Confirma uma Transação no Banco de Dados
	*/
	function validarTransacao(){
		if( !is_resource(conexaoPadraoOCI::$conexaoEstatica) ) throw new erroBanco( 'Conexão fechada para validar uma transação!' );
		conexaoPadraoOCI::$autoCommitEstatico = true;
		oci_commit(conexaoPadraoOCI::$conexaoEstatica);
		$sterro = oci_error(conexaoPadraoOCI::$conexaoEstatica);
		if (!empty($sterro)) {
			throw new erroBanco($sterro);
		}
	}

	/**
	* Desfaz uma Transação aberta no Banco de Dados
	*/
	function desfazerTransacao(){
		if( !is_resource(conexaoPadraoOCI::$conexaoEstatica) ) throw new erroBanco( 'Conexão fechada para desfazer uma transação!' );
		conexaoPadraoOCI::$autoCommitEstatico = true;
		oci_rollback(conexaoPadraoOCI::$conexaoEstatica);
		$sterro = oci_error(conexaoPadraoOCI::$conexaoEstatica);
		if (!empty($sterro)) {
			throw new erroBanco($sterro);
		}
	}

	/**
	* Executa uma query SQL no Banco de Dados
	* @param [st] Comando SQL a ser executado
	* @return [int] número de linhas afetadas
	*/
	function executarComando($sql){
		return conexaoPadraoOCI::executar($sql);
	}
	/**
	* Executa uma query SQL no Banco de Dados
	* @param [st] Comando SQL a ser executado
	* @return [int] número de linhas afetadas
	*/
	protected static function executar($sql){
		if( !is_resource(conexaoPadraoOCI::$conexaoEstatica) ) {
			debug_print_backtrace();
			$erro = new erroBanco( 'Conexão fechada para executar um comando!' );
			$erro->comando = $sql;
			throw $erro;
		}
		conexaoPadraoOCI::$cursorEstatico = oci_parse(conexaoPadraoOCI::$conexaoEstatica,stripslashes($sql));
		$sterro = oci_error(conexaoPadraoOCI::$conexaoEstatica);
		if (!empty($sterro)) {
			$erro = new erroBanco($sterro);
			$erro->comando = $sql;
			throw $erro;
		}
		conexaoPadraoOCI::$cursorEstatico = oci_execute(conexaoPadraoOCI::$cursorEstatico,(conexaoPadraoOCI::$autoCommitEstatico ? OCI_COMMIT_ON_SUCCESS : OCI_DEFAULT));
		return oci_num_rows(conexaoPadraoOCI::$cursorEstatico);
	}

	/**
	* Retorna um array com o registro retornados corrente da conexão
	* @return array
	*/
	function pegarRegistro(){
		try{
			if( !is_resource(conexaoPadraoOCI::$conexaoEstatica) ) throw new erroBanco( 'Conexão fechada para pegar um registro!' );
			return array_change_key_case(oci_fetch_array (conexaoPadraoOCI::$cursorEstatico),CASE_LOWER);
		}
		catch(erroBanco $e){
			throw $e;
		}
	}
}
?>