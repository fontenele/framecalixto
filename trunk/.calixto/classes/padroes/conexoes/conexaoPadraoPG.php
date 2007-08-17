<?
/**
* Classe de representação de uma conexão com Banco de Dados
* @package Infra-estrutura
* @subpackage Banco de Dados
*/
class conexaoPadraoPG extends conexao{
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
			$stConexao = "host=$servidor port=$porta dbname=$banco user=$usuario password=$senha";
			$this->conexao = pg_connect($stConexao);
			$this->executarComando("SET DATESTYLE TO German;");
			$this->executarComando("SET CLIENT_ENCODING TO UTF8;");
			if( !$this->conexao ) throw new erroBanco( 'erro na conexão com banco de dados' );
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
	* Inicia uma Transação no Banco de Dados
	*/
	function iniciarTransacao(){
		try{
			if( !$this->conexao ) throw new erroBanco( 'conexão com banco de dados não iniciada' );
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
			if( !$this->conexao ) throw new erroBanco( 'conexão com banco de dados não iniciada' );
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
			if( !$this->conexao ) throw new erroBanco( 'conexão com banco de dados não iniciada' );
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
	* Fecha a Conexão com o Banco de Dados
	*/
	function fechar(){
		try{
			if($this->conexao){
				pg_close ($this->conexao);
			}else{
				throw new erroBanco('Conexão não iniciada!');
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
			if( !$this->conexao ) throw new erroBanco( 'conexão com banco de dados não iniciada' );
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
	* @return [array] 
	*/
	function pegarRegistro(){
		try{
			if( !$this->conexao ) throw new erroBanco( 'conexão com banco de dados não iniciada' );
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
