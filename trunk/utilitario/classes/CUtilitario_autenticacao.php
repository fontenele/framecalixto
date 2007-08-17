<?php
/**
* Classe de controle
* Ver o Usuï¿½io
* @package Sistema
* @subpackage Gerador
*/
class CUtilitario_autenticacao extends controlePadrao{
	/**
	* Mï¿½odo inicial do controle
	*/
	function inicial(){

		$certificado = new certificacao();
		$certificado->passarUrl 							('https://mecsrv109.mec.gov.br:8443/ws_sl1/services/WSAuthentication'); 
		$certificado->passarCaminhoDaChavePrivada 			('/home/calixto/privatekey.pem'); 
		$certificado->passarCaminhoDaChavePublica 			('/home/calixto/www/cert.pem'); 
		$certificado->passarCaminhoDaChavePublicaDoServidor('/home/calixto/Desktop/servidor.pem');
		$certificado->passarSenha							('birosca');
		if($a = $certificado->requisitar(new pacote)) 
		{
			 x($a);
		}
	}
}
?>
