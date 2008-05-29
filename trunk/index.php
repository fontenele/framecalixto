<?php
/**
* Arquivo de indice para o funcionamento do sistema
*/
if(phpversion() < $versao = '5.1.2') throw new Exception(sprintf('O Calixto Framework não funciona com versão inferior a %s.',$versao));
error_reporting(E_ALL | E_STRICT);
set_error_handler('reportarErro');
function reportarErro($codigo,$mensagem,$arquivo,$linha){
	if(strpos($arquivo,'conexaoPadrao')) return;
	switch($codigo){
		case E_NOTICE:
			$tipoErro = 'Notice';
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
	echo "
		<fieldset class='erroNegro'>
			<legend>{$tipoErro}</legend>
			<link rel='stylesheet' href='.calixto/estilos/debug.css' />
			<img src='arquivoQuebrado.png' alt='[imagem]'>
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
		</fieldset>";
}
header("Content-type:text/html; charset=utf-8");
date_default_timezone_set('America/Sao_Paulo');
include_once('.calixto/debug.php');
include_once('.calixto/classes/definicao.php');
include_once('.sistema/definicoes.php');
new gerenteControles(isset($_GET['c'])?$_GET['c']:definicaoSistema::pegarControleInicial());
?>