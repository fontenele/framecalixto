<?php
/**
* Classe de internacionalização
* @package Sistema
* @subpackage acessoDoUsuario
*/
class IAcessoDoUsuario extends internacionalizacaoPadrao{
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
		if(!is_array(IAcessoDoUsuario::$estrutura)){
			return IAcessoDoUsuario::$estrutura = $this->mapearInternacionalizacao(definicaoArquivo::pegarXmlInternacionalizacao($this));
		}else{
			return IAcessoDoUsuario::$estrutura;
		}
	}
}
?>