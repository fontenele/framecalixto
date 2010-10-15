<?php
/**
* Representação de comportamento de uma conexaoPadrao 
* @package FrameCalixto
* @subpackage Banco de Dados
*/
class conexaoPadraoPDO extends conexao{
	const oracle = 'oracle';
	const postgres = 'postgres';
	const mysql = 'mysql';
	const sqlserver = 'mssql';
	const sqlite = 'sqlite';
	/**
	* Identificador da conexao
	*/
	protected $id;
	/**
	* Pilha de conexoes
	*/
	protected static $conexoes;
	/**
	* O ponteiro do recurso com o resultado do comando
	* @var resource
	*/
	protected $cursor;
	/**
	* Verificador de transação
	* @var boolean
	*/
	protected $autoCommit;
   /**
	* Metodo construtor
	*/
	protected final function __construct($id){
		$this->id = $id;
	}
	/**
	* Metodo de conexão
	* @param string Tipo do banco de dados
	* @param string Servidor do Banco de dados
	* @param string Porta do servidor do Banco de dados
	* @param string Nome do Banco de dados
	* @param string Usuário do Banco de dados
	* @param string Senha do Banco de dados
	* @param string Identificador da conexao (opcional)
	*/
	public static function conectar($tipo, $servidor, $porta, $banco, $usuario, $senha, $id = 1){
		$idx = md5("$tipo, $servidor, $porta, $banco, $usuario, $senha, $id");
		if(!isset(self::$conexoes[$idx])){
			//if($porta) $servidor = "$servidor,$porta";
			try {
				switch ($tipo){
					case conexaoPadraoPDO::postgres:
						$dsn = sprintf('pgsql:host=%s;dbname=%s',$servidor,$banco);
						self::$conexoes[$idx] = new PDO($dsn,$usuario,$senha);
						self::$conexoes[$idx]->query("SET DATESTYLE TO German;");
						self::$conexoes[$idx]->query("SET CLIENT_ENCODING TO UTF8;");
					break;
					case conexaoPadraoPDO::mysql:
						$dsn = sprintf('mysql:dbname=%s,host=%s',$servidor,$banco);
						self::$conexoes[$idx] = new PDO($dsn,$usuario,$senha);
					break;
					case conexaoPadraoPDO::sqlserver:
						$dsn = sprintf('mssql:host=%s;dbname=%s',$servidor,$banco);
						self::$conexoes[$idx] = new PDO($dsn,$usuario,$senha);
					break;
					case conexaoPadraoPDO::sqlite:
						$dsn = sprintf('sqlite:%s',$banco);
						self::$conexoes[$idx] = new PDO($dsn,$usuario,$senha);
					break;
					case conexaoPadraoPDO::oracle:
						$dsn = sprintf('oci:dbname=%s',$banco);
						self::$conexoes[$idx] = new PDO($dsn,$usuario,$senha);
					break;
				}
				self::$conexoes[$idx]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			} catch (PDOException $e) {
				throw new erroBanco($e->getMessage(), $e->getCode());
			}
		}
		return new conexaoPadraoPDO($idx);
	}
	/**
	* Inicia uma Transação no Banco de Dados
	*/
	function iniciarTransacao(){
		self::$conexoes[$this->id]->beginTransaction();
	}
	/**
	* Confirma uma Transação no Banco de Dados
	*/
	function validarTransacao(){
		self::$conexoes[$this->id]->commit();
	}
	/**
	* Desfaz uma Transação aberta no Banco de Dados
	*/
	function desfazerTransacao(){
		self::$conexoes[$this->id]->rollBack();
	}
	/**
	* Executa uma query SQL no Banco de Dados
	* @param string Comando SQL a ser executado
	* @return integer número de linhas afetadas
	*/
	function executarComando($sql){
		try{
			$this->cursor = self::$conexoes[$this->id]->query($sql);
		}  catch (PDOException $e){
			$e =  new erroBanco($e->getMessage(), $e->getCode());
			$e->comando = $sql;
			throw $e;
		}
	}
	/**
	* Retorna um array com o registro retornado corrente da conexão
	* @return array
	*/
	function pegarRegistro(){
		return $this->cursor->fetch(PDO::FETCH_ASSOC);
	}
	/**
	* Desconecta do banco de dados
	*/
	public function desconectar(){}
	/**
	* Método de sobrecarga para serializar a classe
	*/
	protected function __sleep(){
		$ar = get_object_vars($this);
		unset($ar['conexoes']);
		unset($ar['cursor']);
		return array_keys($ar);
	}
}
?>