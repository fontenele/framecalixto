<?
/**
* Classe de representa��o de uma conex�o com Banco de Dados
* @package Infra-estrutura
* @subpackage Banco de Dados
*/
class conexaoPadraoPG extends conexao{
	/**
	* Metodo construtor
	* @param [st] Servidor do Banco de dados
	* @param [st] Porta do servidor do Banco de dados
	* @param [st] Nome do Banco de dados
	* @param [st] Usu�rio do Banco de dados
	* @param [st] Senha do Banco de dados
	*/
	function __construct($servidor, $porta, $banco, $usuario, $senha){
		try{
			$stConexao = "host=$servidor port=$porta dbname=$banco user=$usuario password=$senha";
			$this->conexao = pg_connect($stConexao);
			if( !$this->conexao ) throw new erroBanco( 'erro na conex�o com banco de dados' );
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
		return new conversorPg();
	}
	
	/**
	* Inicia uma Transa��o no Banco de Dados
	*/
	function iniciarTransacao(){
		try{
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
	* Confirma uma Transa��o no Banco de Dados
	*/
	function validarTransacao(){
		try{
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
	* Desfaz uma Transa��o aberta no Banco de Dados
	*/
	function desfazerTransacao(){
		try{
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
	* Fecha a Conex�o com o Banco de Dados
	*/
	function fechar(){
		try{
			pg_close ($this->conexao);
		}
		catch(erroBanco $e){
			throw $e;
		}
	}
	
	/**
	* Executa uma query SQL no Banco de Dados
	* @param [st] Comando SQL a ser executado
	* @return [int] n�mero de linhas afetadas
	*/
	function executarComando($sql){
		try{
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
	* Retorna um array com o conjunto de registros retornados pela conex�o.
	* @return [array] 
	*/
	function pegarSelecao(){
		try{
			while ($arTupla = $this->pegarRegistro()) {
				$recordSet[] = $arTupla;
			}
			return isset($recordSet)? $recordSet : false ;
		}
		catch(erroBanco $e){
			throw $e;
		}
	}
	
	/**
	* Retorna um array com o registro retornados corrente da conex�o
	* @return [array] 
	*/
	function pegarRegistro(){
		try{
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
