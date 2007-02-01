<?php
/**
* Fun��es criadas para ler o arquivo de defini��es em xml e configurar o sistema
* @package Infra-estrutura
* @subpackage Definicoes
*/
class definicao{
	/**
	* @var [simpleXML] definicoes do sistema
	*/
	static private $definicao = null;
	/**
	* retorna a defini��o do sistema
	* @param [string] caminho do arquivo de defini��o
	*/
	final static function pegarDefinicao($arquivoDefinicao = '.calixto/definicoes/definicoes.xml'){
		if(!definicao::$definicao){
			return definicao::$definicao = simplexml_load_file($arquivoDefinicao);
		}else{
			return definicao::$definicao;
		}
	}
}
?>
