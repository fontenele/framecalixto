<?php
/**
* Arquivo de indice para o funcionamento do sistema 
*/
$versao = '5.1.2';
if(phpversion() < $versao) throw new Exception(sprintf('O Calixto Framework não funciona com versão inferior a %s.',$versao));
header("Content-type:text/html; charset=utf-8");
date_default_timezone_set('America/Sao_Paulo');
set_time_limit(0);
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__)));
//Carrregando as classes de definições e erros
include_once('../.calixto/definicoes/include.php');
//Lendo o arquivo XML de definições de diretórios e arquivos
$definicoes = definicao::pegarDefinicao('.sistema/xml/definicoes.xml');
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
	echo "
		<link type='text/css' rel='stylesheet' href='.sistema/debug.css' />
		<fieldset class='erroNegro'>
			<legend>{$tipoErro}</legend>
			<img src='.sistema/imagens/{$imagemErro}' alt='[imagem]'>
			<table summary='text' class='erroNegro'>
				<tr>
					<td>Mensagem:</td>
					<td><b>{$mensagem}</b></td>
				</tr>
				<tr>
					<td>Arquivo:</td>
					<td>## {$arquivo}({$linha})</td>
				</tr>
			</table>
		<pre>{$back}
		</pre>
		</fieldset>";
}

include_once('.sistema/debug.php');
include_once('.sistema/definicoes.php');
$dir = definirDiretorio('Sistema');
define('diretorioPrioritario',$dir['stDiretorio']);
set_include_path(
	implode(
		PATH_SEPARATOR, array(
    		realpath(APPLICATION_PATH . '/../.calixto/externas/'),
    		get_include_path(),
		)
	)
);

if(isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'],'amf')) {
	include_once('../.calixto/externas/Zend/Amf/Server.php');
	session_start();
	$server = new Zend_Amf_Server();
	$server->setProduction(false);
	
	$arDiretorios = scandir(dirname(__FILE__));
	$arDiretoriosNaoEntidades = array('.','..','.tmp','.sistema');
	foreach($arDiretorios as $dirName) {
		if(!in_array($dirName,$arDiretoriosNaoEntidades) && is_dir($dirName) && is_dir(dirname(__FILE__).'/'.$dirName.'/classes')) {
			$server->addDirectory(dirname(__FILE__).'/'.$dirName.'/classes');
			$arArquivos = scandir(dirname(__FILE__).'/'.$dirName.'/classes');
			foreach($arArquivos as $controles) {
				if(substr($controles,0,1) == 'C') {
					require_once( dirname(__FILE__)."/".$dirName."/classes/{$controles}" );
				}
			}
		}
	}
	echo($server->handle());
}else{
	if(isset($_GET['c'])) $_GET['c'] = is_numeric($_GET['c']) ? 'CSsd_Retorno' : $_GET['c'];
	new gerenteControles(isset($_REQUEST['c'])? $_REQUEST['c']:definicaoSistema::pegarControleInicial());
}
?>