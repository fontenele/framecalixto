<?php
/**
* Classe de controle
* Executa a gravação de um objeto : Usuário
* @package Sistema
* @subpackage Usuário
*/
class CUsuario_gravar extends controlePadraoGravar{
	/**
	* Método de utilização dos dados postados para a montagem do negocio
	* @param negocio objeto para preenchimento
	* @param array $dados
	*/
	public static function montarNegocio(negocio $negocio,$dados = null){
		parent::montarNegocio($negocio,$dados);
		$negocio->passarNmSenha(md5($negocio->pegarNmSenha()));
	}
}
?>