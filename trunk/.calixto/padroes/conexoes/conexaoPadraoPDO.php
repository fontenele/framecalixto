<?php
/**
* Representação de comportamento de uma conexaoPadrao 
* @package FrameCalixto
* @subpackage Banco de Dados
*/
class conexaoPadraoPDO extends conexao{
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
		if(!isset(self::$conexoes['conexao'][$idx])){
			//if($porta) $servidor = "$servidor,$porta";
			try {
				switch ($tipo){
					case conexao::postgres:
						$dsn = sprintf('pgsql:host=%s;dbname=%s',$servidor,$banco);
						self::$conexoes['conexao'][$idx] = new PDO($dsn,$usuario,$senha);
						self::$conexoes['conexao'][$idx]->query("SET DATESTYLE TO German;");
						self::$conexoes['conexao'][$idx]->query("SET CLIENT_ENCODING TO UTF8;");
					break;
					case conexao::mysql:
						$dsn = sprintf('mysql:host=%s;dbname=%s',$servidor,$banco);
						self::$conexoes['conexao'][$idx] = new PDO(
							$dsn,
							$usuario,
							$senha,
							array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
						);
					break;
					case conexao::sqlserver:
						$dsn = sprintf('mssql:host=%s;dbname=%s',$servidor,$banco);
						self::$conexoes['conexao'][$idx] = new PDO($dsn,$usuario,$senha);
					break;
					case conexao::sqlite:
						$dsn = sprintf('sqlite:./%s',$banco);
						self::$conexoes['conexao'][$idx] = new PDO($dsn,$usuario,$senha);
					break;
					case conexao::oracle:
						$dsn = sprintf('oci:dbname=%s',$banco);
						self::$conexoes['conexao'][$idx] = new PDO($dsn,$usuario,$senha);
					break;
				}
				self::$conexoes['conexao'][$idx]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			} catch (PDOException $e) {
				throw new erroBanco($e->getMessage(), $e->getCode());
			}
		}
		$conn = new conexaoPadraoPDO($idx);
		$conn->passarTipo($tipo);
		return $conn;
	}
	/**
	* Inicia uma Transação no Banco de Dados
	*/
	function iniciarTransacao(){
		self::$conexoes['conexao'][$this->id]->beginTransaction();
	}
	/**
	* Confirma uma Transação no Banco de Dados
	*/
	function validarTransacao(){
		self::$conexoes['conexao'][$this->id]->commit();
	}
	/**
	* Desfaz uma Transação aberta no Banco de Dados
	*/
	function desfazerTransacao(){
		self::$conexoes['conexao'][$this->id]->rollBack();
	}
	/**
	* Executa uma query SQL no Banco de Dados
	* @param string Comando SQL a ser executado
	* @return integer número de linhas afetadas
	*/
	function executarComando($sql){
		try{
			self::$conexoes['cursor'][$this->id] = self::$conexoes['conexao'][$this->id]->query($sql);
		}  catch (PDOException $e){
			$e =  new erroBanco($e->getMessage(),1,$e);
			$e->comando = $sql;
			throw $e;
		}
	}
	/**
	* Retorna um array com o registro retornado corrente da conexão
	* @return array
	*/
	function pegarRegistro(){
		return self::$conexoes['cursor'][$this->id]->fetch(PDO::FETCH_ASSOC);
	}
	/**
	* Desconecta do banco de dados
	*/
	public function desconectar(){}
}
?>