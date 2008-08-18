<?php
/**
* Classe de representação de uma conexão com Banco de Dados
* @package FrameCalixto
* @subpackage Banco de Dados
*/
class conexaoPadraoOCI extends conexao{
	/**
	* Metodo construtor
	* @param [st] Servidor do Banco de dados
	* @param [st] Porta do servidor do Banco de dados
	* @param [st] Nome do Banco de dados
	* @param [st] Usuário do Banco de dados
	* @param [st] Senha do Banco de dados
	*/
	public function __construct($servidor, $porta, $banco, $usuario, $senha){
		try{
			$this->strConn = "host=$servidor port=$porta dbname=$banco user=$usuario password=$senha";
			$this->conectar();
		}
		catch(erroBanco $e){
			throw $e;
		}
	}
	/**
	* Fecha a Conexão com o Banco de Dados
	*/
	public function __destruct(){
		try{
			$this->desconectar();
		}
		catch(erroBanco $e){
			// O retorno de erro pelo comando destruct impede o redirecionamento de paginas
		}
	}
	protected function desconectar(){
		try{
			if(is_resource($this->conexao)){
				pg_close ($this->conexao);
			}else{
				throw new erroBanco( 'erro na conexão com banco de dados' );
			}
		}
		catch(erroBanco $e){
			throw $e;
		}
	}
	protected function conectar(){
		try{
			if(!$this->strConn) debug_print_backtrace();
			$this->conexao = pg_pconnect($this->strConn);
			if( !is_resource($this->conexao) ){
			throw new erroBanco( 'erro na conexão com banco de dados' );
			}
			$this->executarComando("SET DATESTYLE TO German;");
			$this->executarComando("SET CLIENT_ENCODING TO UTF8;");
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
			if( !is_resource($this->conexao) ) throw new erroBanco( 'Conexão fechada para iniciar uma transação!' );
			$this->autoCommit = false;
			pg_query($this->conexao, 'begin');
			$sterro = pg_last_error($this->conexao);
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
			pg_query($this->conexao, 'commit');
			$sterro = pg_last_error($this->conexao);
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
			pg_query($this->conexao, 'rollback');
			$sterro = pg_last_error($this->conexao);
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
			if( !is_resource($this->conexao) ) {
				debug_print_backtrace();
				$erro = new erroBanco( 'Conexão fechada para executar um comando!' );
				$erro->comando = $sql;
				throw $erro;
			}
			$this->cursor = @ pg_query($this->conexao,stripslashes($sql));
			$sterro = pg_last_error($this->conexao);
			if (!empty($sterro)) {
				$erro = new erroBanco($sterro);
				$erro->comando = $sql;
				throw $erro;
			}
			return pg_affected_rows($this->cursor);
		}
		catch(erroBanco $e){
			throw $e;
		}
	}

	/**
	* Retorna um array com o registro retornados corrente da conexão
	* @return array
	*/
	function pegarRegistro(){
		try{
			if( !is_resource($this->conexao) ) throw new erroBanco( 'Conexão fechada para pegar um registro!' );
			if ($arRes = pg_fetch_array ($this->cursor,NULL,PGSQL_ASSOC)) {
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