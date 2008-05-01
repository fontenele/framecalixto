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
		//	if(strpos($arquivo,'.tmp')) return;
		break;
	}
	echo "<fieldset>[$codigo] <br />Erro: $mensagem <br />Arquivo: $arquivo <br />Linha: $linha</fieldset>";
}
header("Content-type:text/html; charset=utf-8");
date_default_timezone_set('America/Sao_Paulo');
include_once('.calixto/debug.php');
include_once('.calixto/classes/definicao.php');
include_once('.sistema/definicoes.php');
new gerenteControles(isset($_GET['c'])?$_GET['c']:definicaoSistema::pegarControleInicial());
?>