<?php
/**
* Classe de representação de uma conexão com Banco de Dados
* @package FrameCalixto
* @subpackage Banco de Dados
*/
abstract class conexao extends objeto{
	const oracle = 'oracle';
	const postgres = 'postgres';
	const mysql = 'mysql';
	/**
	* O recurso de conexão com Banco de Dados
	* @var resource
	*/
	public $conexao;
	/**
	* O ponteiro do recurso com o resultado do comando
	* @var resource
	*/
	public $cursor;
	/**
	* String de conexao
	* @var string
	*/
	protected $strConn;
	/**
	* Método construtor
	* Faz a chamada de validação de acesso ao controle
	*/
	abstract function __construct();
	/**
	* Cria uma Conexao com Banco de Dados
	* @param string Servidor do Banco de dados
	* @param string Porta do servidor do Banco de dados
	* @param string Nome do Banco de dados
	* @param string Usuário do Banco de dados
	* @param string Senha do Banco de dados
	* @return conexaoPadrao conexão com o banco de dados
	*/
	public static final function criar($nome = null, $servidor = null, $porta = null, $banco = null, $usuario = null, $senha = null,$tipoBanco = null){
		$id = definicaoBanco::pegarId($nome);
		$servidor	= $servidor	?	$servidor	:	definicaoBanco::pegarServidor($id);
		$porta		= $porta	?	$porta		:	definicaoBanco::pegarPorta($id);
		$banco		= $banco	?	$banco		:	definicaoBanco::pegarNome($id);
		$usuario	= $usuario	?	$usuario	:	definicaoBanco::pegarUsuario($id);
		$senha		= $senha	?	$senha		:	definicaoBanco::pegarSenha($id);
		$tipoBanco		= $tipoBanco		?	$tipoBanco		:	definicaoBanco::pegarTipo($id);
 		$multipla	= definicaoBanco::conexaoMultipla($id);
		if($multipla){
			switch($tipoBanco){
				case 'postgres':
					$conexao = new conexaoPadraoMultiplaPG($servidor, $porta, $banco, $usuario, $senha);
				break;
				case 'mysql':
					$conexao = new conexaoPadraoMultiplaMySql($servidor, $porta, $banco, $usuario, $senha);
				break;
				case 'oracle':
					$conexao = new conexaoPadraoMultiplaOCI($servidor, $porta, $banco, $usuario, $senha);
				break;
				default:
					$conexao = false;
			}
		}else{
			switch($tipoBanco){
				case 'postgres':
					$conexao = conexaoPadraoPG::conectar($servidor, $porta, $banco, $usuario, $senha);
				break;
				case 'mysql':
					$conexao = conexaoPadraoMySql::conectar($servidor, $porta, $banco, $usuario, $senha);
				break;
				case 'oracle':
					$conexao = conexaoPadraoOCI::conectar($servidor, $porta, $banco, $usuario, $senha);
				break;
				default:
					$conexao = false;
			}
		}
		return $conexao;
	}
}
?>