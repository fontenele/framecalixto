<?php
/**
* Arquivo de indice para o funcionamento do sistema
*/
if(phpversion() < $versao = '5.1.2') throw new Exception(sprintf('O Calixto Framework não funciona com versão inferior a %s.',$versao));
error_reporting(E_ALL | E_STRICT);
header("Content-type:text/html; charset=utf-8");
date_default_timezone_set('America/Sao_Paulo');
include_once('.calixto/definicoes.php');
new gerenteControles(isset($_GET['c'])?$_GET['c']:definicaoSistema::pegarControleInicial());
?>