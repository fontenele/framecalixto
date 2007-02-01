<?php
/**
* Classe de internacionalização
* @package Sistema
* @subpackage funcionalidade
*/
class IFuncionalidade extends internacionalizacaoPadrao{
	/**
	* @var [array] array com a estrutura de internacionalização
	* criado para a execução de cache
	*/
	private static $estrutura;
	/**
	* Método que retorna a estrutura da internacionalização
	* sobrescrito para a execução de cache
	* @return [vetor] estrutura da internacionalização
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