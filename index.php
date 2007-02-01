<?php
/**
* Arquivo de indice para o funcionamento do sistema 
*/
header("Content-type:text/html; charset=iso-8859-1");
include_once('.calixto/definicoes.php');
new gerenteControles(isset($_GET['c'])?$_GET['c']:definicaoSistema::pegarControleInicial());
?>
