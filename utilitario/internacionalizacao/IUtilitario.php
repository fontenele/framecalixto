<?php
/**
* Classe de definição da camada de internacionalização
* @package Sistema
* @subpackage Internacionalização
*/
class IUtilitario extends internacionalizacaoPadrao{
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
		if(!is_array(IUtilitario::$estrutura)){
			return IUtilitario::$estrutura = $this->mapearInternacionalizacao(definicaoArquivo::pegarXmlInternacionalizacao($this));
		}else{
			return IUtilitario::$estrutura;
		}
	}
}
?>
