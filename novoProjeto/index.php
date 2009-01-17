<?php
/**
* Arquivo de indice para o funcionamento do sistema 
*/
$versao = '5.1.2';
if(phpversion() < $versao) throw new Exception(sprintf('O Calixto Framework não funciona com versão inferior a %s.',$versao));
header("Content-type:text/html; charset=utf-8");
date_default_timezone_set('America/Sao_Paulo');
set_time_limit(0);
//Carrregando as classes de definições e erros
include_once('../.calixto/definicoes/include.php');
include_once('../.calixto/tiposDeErros/include.php');
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
	ob_start();
		debug_print_backtrace();
	$back = ob_get_clean();
	echo "
		<link type='text/css' rel='stylesheet' href='.sistema/css/debug.css' />
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
//Correção do redirecionamento do SSD por não utilizarem $_POST para o envio de dados
if(isset($_GET['c']) && preg_match('/CControleAcesso_SSDRetorno\?.*/',$_GET['c'],$controleBugadoUrl)){	
	header('location:'.str_replace('/&','/?',str_replace('?','&',('http://'.$_SERVER['SERVER_NAME'].$_SERVER ['REQUEST_URI']))));
}
new gerenteControles(isset($_GET['c'])?$_GET['c']:definicaoSistema::pegarControleInicial());
?>
