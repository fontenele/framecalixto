<?php
/**
* Classe de internacionaliza��o
* @package Sistema
* @subpackage funcionalidade
*/
class IFuncionalidade extends internacionalizacaoPadrao{
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
		if(!is_array(IFuncionalidade::$estrutura)){
			return IFuncionalidade::$estrutura = $this->mapearInternacionalizacao(definicaoArquivo::pegarXmlInternacionalizacao($this));
		}else{
			return IFuncionalidade::$estrutura;
		}
	}
}
?>