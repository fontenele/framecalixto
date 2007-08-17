<?php
class SoapException extends Exception{
}

class pacote extends objeto{
	/**
	* 
	*/
	public $pacote;
	/**
	*/
	public function __construct(){
		$this->pacote = 
			'<?xml version="1.0"?><SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:sess="http://ws.apache.org/namespaces/axis2">
				<SOAP-ENV:Body>
					<getIdentifierAppletTicket xmlns="http://ssd.ceinf.mec.gov.br/WSAuthForApplet/xsd">
						<flag>teste</flag>
					</getIdentifierAppletTicket>
				</SOAP-ENV:Body>
			</SOAP-ENV:Envelope>';
	}
} 
class certificacao extends objeto{
	/**
	* Caminho do servidor SOAP
	*/
	public $url;
	/**
	* Caminho do arquivo que possui a chave privada 
	*/
	public $caminhoDaChavePrivada;
	/**
	* Caminho do arquivo que possui a chave publica 
	*/
	public $caminhoDaChavePublica;
	/**
	* Caminho do certificado da Autoridade Certificadora
	*/
	public $caminhoDaChavePublicaDoServidor;
	/**
	* Senha de acesso a chave privada
	*/
	public $senha;
	/**
	* Limite de tempo para a resposta do serviço
	*/
	public $timeout = 20;
	/**
	* Versão da SSL utilizada
	*/
	public $versaoSSL = 3;
	/**
	* Flag de validação do servidor
	*/
	public $verifyHost = true;
	/**
	* Flag de validação do cliente
	*/
	public $verifyPeer = true;
	/**
	* Recurso
	*/
	public $handle;

	/**
	*/
	public function requisitar(pacote $pacote) {
		try{
			if(!$this->url)
			{
				throw( new Exception( 'É necessário a url de autenticacao!' ) );
			}
			if(!$this->caminhoDaChavePrivada || !is_file($this->caminhoDaChavePrivada) || !is_readable($this->caminhoDaChavePrivada) )
			{
				throw( new Exception( 'O caminho da chave privada é inválido !' ) );
			}
			if(!$this->caminhoDaChavePrivada || !is_file($this->caminhoDaChavePrivada) || !is_readable($this->caminhoDaChavePrivada) )
			{
				throw( new Exception( 'O caminho da chave privada é inválido !' ) );
			}
			
			
		
			$this->handle = curl_init();
			curl_setopt($this->handle,CURLOPT_URL				, $this->url );
			curl_setopt($this->handle,CURLOPT_TIMEOUT			, $this->timeout );
			curl_setopt($this->handle,CURLOPT_SSLVERSION		, $this->versaoSSL );
			curl_setopt($this->handle,CURLOPT_SSL_VERIFYHOST	, $this->verifyHost );
			curl_setopt($this->handle,CURLOPT_SSL_VERIFYHOST	, $this->verifyHost );
			curl_setopt($this->handle,CURLOPT_RETURNTRANSFER	, true );
			curl_setopt($this->handle,CURLOPT_SSLKEYPASSWD		, $this->senha );
			curl_setopt($this->handle,CURLOPT_POST				, true);
			curl_setopt($this->handle,CURLOPT_SSLKEY			, $this->caminhoDaChavePrivada );
			curl_setopt($this->handle,CURLOPT_SSLCERT			, $this->caminhoDaChavePublica );
			curl_setopt($this->handle,CURLOPT_CAINFO			, $this->caminhoDaChavePublicaDoServidor );
			curl_setopt($this->handle,CURLOPT_POSTFIELDS		, $pacote->pegarPacote());
			$result =  curl_exec($this->handle);
			if( curl_errno($this->handle) > 0 )
			{
				$e = new Exception(curl_error($this->handle));
				var_dump($e);
				throw( $e );
			}
			curl_close ($this->handle);
			return $this->desempacotar($result);
		}
		catch( SoapException $e ){
			$result = false;
			throw( $e );
		}
		catch( Exception $e ){
			$result = false;
			throw( $e );
		}
	}
	/**
	*/
	public function desempacotar($retorno){
		try{
			/* Verificar o conteudo do retorno e retornar uma exceção caso for um erro . caso não for erro ... verificar a possibilidade de montar o objeto */
			$a = simplexml_load_string("<?xml version='1.0' encoding='utf-8' ?>".$retorno);
			return $a ;
		}
		catch( SoapException $e ){
			throw( $e );
		}
		catch( Exception $e ){
			throw( $e );
		}
	}
}
?>