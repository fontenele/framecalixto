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
		if(!is_resource(conexaoPadraoOCI::$conexaoEstatica)) throw new erroBanco( 'Não existe recurso para o fechamento da conexão.' );
		ob_start();
		oci_close(conexaoPadraoOCI::$conexaoEstatica);
		if(($res = ob_get_clean()))	throw new erroBanco($res);
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
			ob_start();
			if($servidor && $porta){
				conexaoPadraoOCI::$conexaoEstatica = oci_connect($usuario,$senha,
				"(DESCRIPTION =(ADDRESS = (PROTOCOL = TCP)(HOST ={$servidor})(PORT = {$porta}))(CONNECT_DATA = (SERVER = DEDICATED)(SERVICE_NAME = {$banco})))",'UTF8');
			}else{
				conexaoPadraoOCI::$conexaoEstatica = oci_connect($usuario,$senha,$banco,'UTF8');
			}
			if(($res = ob_get_clean()))	throw new erroBanco($res);
			if( !is_resource(conexaoPadraoOCI::$conexaoEstatica) ){
				throw new erroBanco( 'Erro ao estabelecer conexão com banco de dados' );
			}
			conexaoPadraoOCI::executar( 'alter session set NLS_LANGUAGE="BRAZILIAN PORTUGUESE"' );
			conexaoPadraoOCI::executar( 'alter session set NLS_NUMERIC_CHARACTERS =",."' );
			conexaoPadraoOCI::executar( 'alter session set NLS_DATE_FORMAT = "dd/mm/yyyy HH24:MI:SS"' );
			conexaoPadraoOCI::executar( 'alter session set NLS_SORT="BINARY"' );
			conexaoPadraoOCI::executar( 'alter session set skip_unusable_indexes=true' );
		}
		return new conexaoPadraoOCI();
	}
	/**
	* Inicia uma Transação no Banco de Dados
	*/
	public function iniciarTransacao(){
		if( !is_resource(conexaoPadraoOCI::$conexaoEstatica) ) throw new erroBanco( 'Conexão fechada para iniciar uma transação!' );
		conexaoPadraoOCI::$autoCommitEstatico = false;
	}

	/**
	* Confirma uma Transação no Banco de Dados
	*/
	public function validarTransacao(){
		if( !is_resource(conexaoPadraoOCI::$conexaoEstatica) ) throw new erroBanco( 'Conexão fechada para validar uma transação!' );
		conexaoPadraoOCI::$autoCommitEstatico = true;
		oci_commit(conexaoPadraoOCI::$conexaoEstatica);
		if (($sterro = oci_error(conexaoPadraoOCI::$conexaoEstatica))) throw new erroBanco($sterro);
	}

	/**
	* Desfaz uma Transação aberta no Banco de Dados
	*/
	public function desfazerTransacao(){
		if( !is_resource(conexaoPadraoOCI::$conexaoEstatica) ) throw new erroBanco( 'Conexão fechada para desfazer uma transação!' );
		conexaoPadraoOCI::$autoCommitEstatico = true;
		oci_rollback(conexaoPadraoOCI::$conexaoEstatica);
		if (($sterro = oci_error(conexaoPadraoOCI::$conexaoEstatica))) throw new erroBanco($sterro);
	}

	/**
	* Executa uma query SQL no Banco de Dados
	* @param [st] Comando SQL a ser executado
	* @return [int] número de linhas afetadas
	*/
	public function executarComando($sql){
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
		ob_start();
		conexaoPadraoOCI::$cursorEstatico = oci_parse(conexaoPadraoOCI::$conexaoEstatica,stripslashes($sql));
		if(($res = ob_get_clean()))	throw new erroBanco($res);
		if (($sterro = oci_error(conexaoPadraoOCI::$conexaoEstatica))) {
			$erro = new erroBanco($sterro);
			$erro->comando = $sql;
			throw $erro;
		}
		ob_start();
		$res = oci_execute(conexaoPadraoOCI::$cursorEstatico,(conexaoPadraoOCI::$autoCommitEstatico ? OCI_COMMIT_ON_SUCCESS : OCI_DEFAULT));
		if(($res = ob_get_clean()))	throw new erroBanco($res);
		if (($sterro = oci_error(conexaoPadraoOCI::$cursorEstatico))) {
			$erro = new erroBanco($sterro['message']);
			$erro->comando = $sql;
			throw $erro;
		}
		ob_start();
		$linhas = oci_num_rows(conexaoPadraoOCI::$cursorEstatico);
		if(($res = ob_get_clean()))	throw new erroBanco($res);
		return $linhas;
	}

	/**
	* Retorna um array com o registro retornados corrente da conexão
	* @return array
	*/
	public function pegarRegistro(){
		if( !is_resource(conexaoPadraoOCI::$conexaoEstatica) ) throw new erroBanco( 'Conexão fechada para pegar um registro!' );
		if(!($tupla = oci_fetch_assoc (conexaoPadraoOCI::$cursorEstatico))) return false;
		return array_change_key_case($tupla,CASE_LOWER);
	}
}
?>