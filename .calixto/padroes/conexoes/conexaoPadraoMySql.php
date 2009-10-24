<?php
/**
* Classe de representação de uma conexão com Banco de Dados
* @package FrameCalixto
* @subpackage Banco de Dados
*/
class conexaoPadraoMySql extends conexao implements conexaoPadraoEstatica{
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
		if(!is_resource(conexaoPadraoMySql::$conexaoEstatica)) throw new erroBanco( 'erro na conexão com banco de dados' );
		mysql_close(conexaoPadraoMySql::$conexaoEstatica);
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
		if(!is_resource(conexaoPadraoMySql::$conexaoEstatica)){
			conexaoPadraoMySql::$conexaoEstatica = mysql_connect($servidor, $usuario, $senha);
			if( !conexaoPadraoMySql::$conexaoEstatica ) throw new erroBanco( 'erro na conexão com banco de dados' );
			if( !mysql_select_db($banco, conexaoPadraoMySql::$conexaoEstatica)) throw new erroBanco( 'erro na conexão com banco de dados' );
		}
		return new conexaoPadraoMySql();
	}
	/**
	* Inicia uma Transação no Banco de Dados
	*/
	function iniciarTransacao(){
		if( !is_resource(conexaoPadraoMySql::$conexaoEstatica) ) throw new erroBanco( 'Conexão fechada para iniciar uma transação!' );
		conexaoPadraoMySql::$autoCommitEstatico = false;
		mysql_query(conexaoPadraoMySql::$conexaoEstatica, 'begin');
		if(($sterro = mysql_error(conexaoPadraoMySql::$conexaoEstatica))) throw new erroBanco($sterro);
	}
	/**
	* Confirma uma Transação no Banco de Dados
	*/
	function validarTransacao(){
		if( !is_resource(conexaoPadraoMySql::$conexaoEstatica) ) throw new erroBanco( 'Conexão fechada para validar uma transação!' );
		conexaoPadraoMySql::$autoCommitEstatico = true;
		mysql_query(conexaoPadraoMySql::$conexaoEstatica, 'commit');
		$sterro = mysql_error(conexaoPadraoMySql::$conexaoEstatica);
		if (!empty($sterro)) {
			throw new erroBanco($sterro);
		}
	}
	/**
	* Desfaz uma Transação aberta no Banco de Dados
	*/
	function desfazerTransacao(){
		if( !is_resource(conexaoPadraoMySql::$conexaoEstatica) ) throw new erroBanco( 'Conexão fechada para desfazer uma transação!' );
		conexaoPadraoMySql::$autoCommitEstatico = true;
		mysql_query(conexaoPadraoMySql::$conexaoEstatica, 'rollback');
		$sterro = mysql_error(conexaoPadraoMySql::$conexaoEstatica);
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
		if( !is_resource(conexaoPadraoMySql::$conexaoEstatica) ) {
			$erro = new erroBanco( 'Conexão fechada para executar um comando!' );
			$erro->comando = $sql;
			throw $erro;
		}
		conexaoPadraoMySql::$cursorEstatico = mysql_query(stripslashes($sql),conexaoPadraoMySql::$conexaoEstatica);
		if (($sterro = mysql_error(conexaoPadraoMySql::$conexaoEstatica))) {
			$erro = new erroBanco($sterro);
			$erro->comando = $sql;
			throw $erro;
		}
		return mysql_affected_rows(conexaoPadraoMySql::$conexaoEstatica);
	}
	/**
	* Retorna um array com o registro retornados corrente da conexão
	* @return array
	*/
	function pegarRegistro(){
		if(!is_resource(conexaoPadraoMySql::$conexaoEstatica) ) throw new erroBanco('Conexão fechada para pegar um registro!');
		if (($arRes = mysql_fetch_array (conexaoPadraoMySql::$cursorEstatico,MYSQL_ASSOC))) return array_change_key_case($arRes);
		return false;
	}
}
?>
