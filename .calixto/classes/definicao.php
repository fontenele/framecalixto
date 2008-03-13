<?php
/**
* Funções criadas para ler o arquivo de definições em xml e configurar o sistema
* @package Infra-estrutura
* @subpackage Definicoes
*/
class definicao{
	/**
	* @var [simpleXML] definicoes do sistema
	*/
	static private $definicao = null;
	/**
	* retorna a definição do sistema
	* @param [string] caminho do arquivo de definição
	*/
	final static function pegarDefinicao($arquivoDefinicao = null){
		if(!definicao::$definicao){
			return definicao::$definicao = simplexml_load_file($arquivoDefinicao);
		}else{
			return definicao::$definicao;
		}
	}
}
?>