<?php
/**
* Arquivo de indice para o funcionamento do sistema
*/
if(phpversion() < $versao = '5.1.2') throw new Exception(sprintf('O Calixto Framework não funciona com versão inferior a %s.',$versao));
error_reporting(E_ALL | E_STRICT);
set_error_handler('reportarErro');
set_time_limit(0);
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
header("Content-type:text/html; charset=utf-8");
date_default_timezone_set('America/Sao_Paulo');
include_once('.sistema/debug.php');
include_once('.sistema/definicoes.php');

new gerenteControles(isset($_GET['c'])?$_GET['c']:definicaoSistema::pegarControleInicial());
?>