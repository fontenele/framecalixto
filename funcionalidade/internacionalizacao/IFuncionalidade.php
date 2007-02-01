<?php
/**
* Classe de internacionalizaчуo
* @package Sistema
* @subpackage funcionalidade
*/
class IFuncionalidade extends internacionalizacaoPadrao{
	/**
	* @var [array] array com a estrutura de internacionalizaчуo
	* criado para a execuчуo de cache
	*/
	private static $estrutura;
	/**
	* Mщtodo que retorna a estrutura da internacionalizaчуo
	* sobrescrito para a execuчуo de cache
	* @return [vetor] estrutura da internacionalizaчуo
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