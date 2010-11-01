<?php
/**
* Arquivo de indice para o funcionamento do sistema
* @package Sistema
* @subpackage Gateway
*/
date_default_timezone_set('America/Sao_Paulo');
set_time_limit(0);
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__)));
//Lendo o arquivo XML de definições de diretórios e arquivos
$definicoes = simplexml_load_file('.sistema/xml/definicoes.xml');
$dirCalixto = strval($definicoes->classes->classe[0]['dir']);
//Carrregando as classes de definições e erros
include_once($dirCalixto.'definicoes/include.php');
switch (definicaoSistema::pegarAmbiente()) {
	case definicaoSistema::homologacao  :
		ini_set('display_errors','Off');
	break;
	case definicaoSistema::producao :
		ini_set('display_errors','Off');
	break;
	case definicaoSistema::desenvolvimento :
	default:
		ini_set('display_errors','On');
	break;
}
error_reporting(E_ALL | E_STRICT);
set_error_handler('reportarErro');
function reportarErro($codigo,$mensagem,$arquivo,$linha,$tipoErro){
	if(strpos($arquivo,'conexaoPadrao')) return;
	$imagemErro = 'erro.png';
	switch($codigo){
		case E_NOTICE:
			$tipoErro = 'Notice';
			$imagemErro = 'notice.png';
		break;
		case E_WARNING:
			$tipoErro = 'Warning';
		break;
		case E_PARSE:
			$tipoErro = 'Parser';
		break;
		case E_COMPILE_ERROR:
			$tipoErro = 'Fatal';
		break;
	}
	if(preg_match('/(.*)\.html\.php(.*)/', $arquivo, $resultado)){
		$mensagem = str_replace('Undefined index:','Variável não registrada no controle para apresentação no template: ',$mensagem);
		$back = null;
	}else{
		ob_start();
		debug_print_backtrace();
		$back = ob_get_clean();
	}
	echo $mensagem;
}
include_once('.sistema/debug.php');
include_once('.sistema/definicoes.php');
$dir = definirDiretorio('Sistema');
define('diretorioPrioritario',$dir['stDiretorio']);
set_include_path(
	implode(
		PATH_SEPARATOR, array(
    		realpath(APPLICATION_PATH . '/../.calixto/externas/'),
    		realpath(dirname(__FILE__)),
    		get_include_path(),
		)
	)
);
include_once('../.calixto/externas/Zend/Amf/Server.php');
session_start();
$server = new Zend_Amf_Server();
$server->setProduction( definicaoSistema::pegarAmbiente() == definicaoSistema::producao ? true : false );
$arDiretorios = scandir(dirname(__FILE__));
$arDiretoriosNaoEntidades = array('.','..','.tmp','.sistema');
foreach($arDiretorios as $dirName) {
	if(!in_array($dirName,$arDiretoriosNaoEntidades) && is_dir($dirName) && is_dir(dirname(__FILE__).'/'.$dirName.'/classes')) {
		$server->addDirectory(dirname(__FILE__).'/'.$dirName.'/classes');
		$arArquivos = scandir(dirname(__FILE__).'/'.$dirName.'/classes');
		foreach($arArquivos as $controle) {
			if(substr($controle,0,1) == 'C') {
				require_once( dirname(__FILE__)."/".$dirName."/classes/{$controle}" );
			}elseif(substr($controle,0,1) == 'N') {
				$caminho = realpath(dirname(__FILE__)."/".$dirName."/classes/{$controle}");
				require_once($caminho);
				$classeAs 	= substr("{$dirName}.classes.{$controle}",0,strlen("{$dirName}.classes.{$controle}") - 4);
				$classePhp 	= substr($controle,0,strlen($controle) - 4);
				$server->setClassMap($classeAs,$classePhp);
			}
		}
	}
}
echo($server->handle());
?>