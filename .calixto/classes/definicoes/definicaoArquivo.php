<?php
/**
* Classe de definição para arquivos
* @package Infra-estrutura
* @subpackage Definição
*/
class definicaoArquivo{
	/**
	* @var [string] nome do xml configurador das entidades
	*/
	private static $xmlEntidade;
	/**
	* @var [string] nome do xml configurador da internacionalização
	*/
	private static $xmlInternacionalizacao;
	/**
	* @var [string] nome do xml configurador da internacionalização do sistema
	*/
	private static $xmlInternacionalizacaoDoSistema;
	/**
	* @var [string] caminho do arquivo CSS principal
	*/
	private static $css;
	/**
	* @var [texto] Nome arquivo de definção da classe persistente
	*/
	private static final function pegarNomeXmlEntidade(){
		if(definicaoArquivo::$xmlEntidade){
			return definicaoArquivo::$xmlEntidade;
		}else{
			foreach(definicao::pegarDefinicao()->arquivos->arquivo as $arquivo){
				if(caracteres($arquivo['tipo']) == "definicao de entidade") {
					definicaoArquivo::$xmlEntidade = caracteres($arquivo['nome']);
					break;
				}
			}
			return definicaoArquivo::$xmlEntidade;
		}
	}
	/**
	* Retorna o caminho do xml configurador da entidade
	* @param [objeto|string] 
	* @param [string] caminho forçado do xml 
	*/
	static static final function pegarXmlEntidade($classe = null,$arquivoXML = null){
		try{
			if($arquivoXML === null){
				$arquivoXML = definicaoEntidade::entidade($classe).'/'.definicaoArquivo::pegarNomeXmlEntidade();
				arquivo::legivel($arquivoXML);
			}
			return $arquivoXML;
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* @var [texto] Nome arquivo de definção da internacionalização
	*/
	private static final function pegarNomeXmlInternacionalizacao(){
		if(definicaoArquivo::$xmlInternacionalizacao){
			return definicaoArquivo::$xmlInternacionalizacao;
		}else{
			foreach(definicao::pegarDefinicao()->arquivos->arquivo as $arquivo){
				if(caracteres($arquivo['tipo']) == "internacionalizacao") {
					definicaoArquivo::$xmlInternacionalizacao = caracteres($arquivo['nome']);
					break;
				}
			}
			return definicaoArquivo::$xmlInternacionalizacao;
		}
	}
	/**
	* Retorna o caminho do xml configurador da internacionalização
	* @param [objeto|string] 
	* @param [string] caminho forçado do xml 
	*/
	static static final function pegarXmlInternacionalizacao($classe = null,$arquivoXML = null){
		try{
			if($arquivoXML === null){
				$arquivoXML = definicaoEntidade::entidade($classe).'/'.definicaoArquivo::pegarNomeXmlInternacionalizacao();
				arquivo::legivel($arquivoXML);
			}
			return $arquivoXML;
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* @var [texto] Nome arquivo de definção da internacionalização do sistema
	*/
	private static final function pegarNomeXmlInternacionalizacaoDoSistema(){
		if(definicaoArquivo::$xmlInternacionalizacaoDoSistema){
			return definicaoArquivo::$xmlInternacionalizacaoDoSistema;
		}else{
			foreach(definicao::pegarDefinicao()->arquivos->arquivo as $arquivo){
				if(caracteres($arquivo['tipo']) == "internacionalizacao do sistema") {
					definicaoArquivo::$xmlInternacionalizacaoDoSistema = caracteres($arquivo['nome']);
					break;
				}
			}
			return definicaoArquivo::$xmlInternacionalizacaoDoSistema;
		}
	}
	/**
	* Retorna o caminho do xml configurador da internacionalização do sistema
	* @param [objeto|string] 
	* @param [string] caminho forçado do xml 
	*/
	static static final function pegarXmlInternacionalizacaoDoSistema($arquivoXML = null){
		try{
			if($arquivoXML === null){
				arquivo::legivel(definicaoArquivo::pegarNomeXmlInternacionalizacaoDoSistema());
				return definicaoArquivo::pegarNomeXmlInternacionalizacaoDoSistema();
			}
			return $arquivoXML;
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Retorna o caminho arquivo de tema CSS
	* @return [string] caminho do arquivo CSS
	*/
	static static final function pegarCss(){
		if(definicaoArquivo::$css){
			return definicaoArquivo::$css;
		}else{
			foreach(definicao::pegarDefinicao()->arquivos->arquivo as $arquivo){
				if(caracteres($arquivo['tipo']) == "folha de estilo css") {
					definicaoArquivo::$css = caracteres($arquivo['nome']);
					break;
				}
			}
			return definicaoArquivo::$css;
		}
	}
}
?>