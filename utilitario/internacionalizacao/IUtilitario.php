<?php
/**
* Classe de defini��o da camada de internacionaliza��o
* @package Sistema
* @subpackage Internacionaliza��o
*/
class IUtilitario extends internacionalizacaoPadrao{
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
		if(!is_array(IUtilitario::$estrutura)){
			return IUtilitario::$estrutura = $this->mapearInternacionalizacao(definicaoArquivo::pegarXmlInternacionalizacao($this));
		}else{
			return IUtilitario::$estrutura;
		}
	}
}
?>
