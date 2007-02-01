<?php
/**
* Classe de internacionaliza��o
* @package Sistema
* @subpackage acessoDoUsuario
*/
class IAcessoDoUsuario extends internacionalizacaoPadrao{
	/**
	* @var [array] array com a estrutura de internacionaliza��o
	* criado para a execu��o de cache
	*/
	private static $estrutura;
	/**
	* M�todo que retorna a estrutura da internacionaliza��o
	* sobrescrito para a execu��o de cache
	* @return [vetor] estrutura da internacionaliza��o
	*/
	public function pegarInternacionalizacao(){
		if(!is_array(IAcessoDoUsuario::$estrutura)){
			return IAcessoDoUsuario::$estrutura = $this->mapearInternacionalizacao(definicaoArquivo::pegarXmlInternacionalizacao($this));
		}else{
			return IAcessoDoUsuario::$estrutura;
		}
	}
}
?>