<?php
/**
* Classe de representação de uma conexão com Banco de Dados
* @package Infra-estrutura
* @subpackage Banco de Dados
*/
class conexaoPadraoMultiplaOCI extends conexao{
	/**
	* Controlador de transação do oracle
	*/
	public $autoCommit = true;
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
			$strConn = "(DESCRIPTION =
							(ADDRESS =
								(PROTOCOL = TCP)
								(HOST = {$servidor})
								(PORT = {$porta})
							)
							(CONNECT_DATA =(SID = {$banco}))
						)";
			$this->strConn['usuario'] = $usuario;
			$this->strConn['senha']  = $senha;
			$this->strConn['banco'] = $strConn;
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
	/**
	* Método que fecha a conexão com o banco de dados
	*/
	protected function desconectar(){
		try{
			if(is_resource($this->conexao)){
				oci_close ($this->conexao);
			}else{
				throw new erroBanco( 'erro na conexão com banco de dados' );
			}
		}
		catch(erroBanco $e){
			throw $e;
		}
	}
	/**
	* Método que abre a conexão com o banco de dados
	*/
	protected function conectar(){
		putenv('NLS_LANGUAGE="AMERICAN"');
		putenv('NLS_TERRITORY="AMERICA"');
		putenv('NLS_ISO_CURRENCY="AMERICA"');
		putenv('NLS_CHARACTERSET="WE8ISO8859P15"');
		
		$this->conexao = oci_connect( $this->strConn['usuario'], $this->strConn['senha'], $this->strConn['banco'], "WE8ISO8859P15" );
		if(!is_resource($this->conexao)) throw new erroBanco( 'erro na conexão com banco de dados' );
	}
	/**
	* Inicia uma Transação no Banco de Dados
	*/
	function iniciarTransacao(){
		try{
			$this->autoCommit = false;
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
			ociCommit($this->conexao);
			$arErroOci = ociError($this->conexao);
			if( is_array($arErroOci) ) {
				throw new erroBanco($arErroOci['message']);
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
			$resultado = ociRollback ($this->conexao);
			if(!$resultado) {
				$arErroOci = ociError($this->conexao);
				if( is_array($arErroOci) ) {
					throw new erroBanco($arErroOci['message']);
				}
				else{
					throw new erroBanco('Erro não identificado no rollback.');
				}
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
	public function executarComando($sql){
		try{
			$this->cursor = ociParse(  $this->conexao , stripslashes($sql)  );
			if (!$this->cursor) {
				$arErroOci = ociError($this->conexao);
			}else{
				ob_start();
				if($this->autoCommit) {
					$result = @ociExecute($this->cursor,OCI_COMMIT_ON_SUCCESS);
				}else{
					$result = @ociExecute($this->cursor,OCI_DEFAULT);
				}
				$erro = ob_get_clean();
				if($erro) throw new erroBanco($erro);
				return oci_num_rows($this->cursor);
			}
		}catch(erroBanco $e){
			throw $e;
		}
	}

	/**
	* Retorna um array com o registro retornados corrente da conexão
	* @return [array]
	*/
	public function pegarRegistro(){
		try{
			ob_start();
			ociFetchInto ($this->cursor, $res, OCI_ASSOC+OCI_RETURN_NULLS);
			$erro = ob_get_clean();
			if($erro) throw new erroBanco($erro);
			return $res;
		}catch(erroBanco $e){
			throw $e;
		}
	}
}
?>