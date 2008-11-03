<?php
/**
* Sobrecarga da função __autoload do php
* Faz o include automaticamente do arquivo da classe
* Utiliza o arquivo XML de definições de diretórios e arquivos como base
* @param [string] Nome da classe
*/
function __autoload($stClasse){
	try{
		$definicoes = definicao::pegarDefinicao();
		$stEntidade = definicaoEntidade::entidade($stClasse);
		foreach($definicoes->xpath('//classes/classe') as $index => $classe){
			if(isset($classe['id'])){
				$id = strval($classe['id']);
				$slice = substr($stClasse,0,strlen($id));
				if($id == $slice){
					if(strval($classe['entidade']) == 'sim') {
						$dir = "{$stEntidade}/".strval($classe['dir']).'/';
						$tipoBanco = strval($classe['tipoBanco']) == 'sim' ? strval($definicoes->bancos->banco[0]['tipo']) : null ;
					}else{
						$dir = strval($classe['dir']).'/';
						$tipoBanco = strval($classe['tipoBanco']) == 'sim' ? strval($definicoes->bancos->banco[0]['tipo']) : null ;
					}
				}
			}else{
				$dirPadrao = $classe['dir'];
				$tipoBanco = strval($classe['tipoBanco']) == 'sim' ? strval($definicoes->bancos->banco[0]['tipo']) : null ;
			}
		}
		$stDiretorio = isset($dir) ? $dir : $dirPadrao ;
		if($tipoBanco) {
			$stArquivo = "$stDiretorio$stClasse.$tipoBanco.php";
		}else{
			$stArquivo = "$stDiretorio$stClasse.php";
		}
		switch(true){
			case !(is_dir($stDiretorio)):
				throw new erroInclusao("Diretório [$stDiretorio] inexistente!");
			break;
			case !(is_file($stArquivo)):
				throw new erroInclusao("Arquivo [$stArquivo] inexistente!");
			break;
			case !(is_readable($stArquivo)):
				throw new erroInclusao("Arquivo [$stArquivo] sem permissão de leitura!");
			break;
			default:
				include_once $stArquivo;
			break;
		}
	}catch (erroInclusao $e) {
		echo $e->__toHtml();
	}catch (Exception $e) {
			if(strtolower(ini_get('display_errors')) == 'on'){
				$debug = debug_backtrace();
				echo "
				<link rel='stylesheet' href='.sistema/css/debug.css' />
				<div class='erroNegro'>
					<table summary='text' class='erroNegro'>
						<tr><th colspan=2 >Tentativa de instanciar uma classe inexistente!</th></tr>
						<tr><td>Classe:</td><td><font size='6px'>{$stClasse} ???</font></td></tr>
						<tr><td>Arquivo:</td><td>{$debug[0]['file']}</td></tr>
						<tr><td>Linha:</td><td>{$debug[0]['line']}</td></tr>
					</table>
				</div>
				";
				die();
			}
	}
}
if(function_exists('iconv')){
	define('conversorIconv',true);
}else{
	define('conversorIconv',false);
}
/**
* retorna uma string com a codificação correta .
*/
function caracteres($string,$tipoEntrada = "UTF-8",$tipoSaida = "ISO-8859-1"){
	if(conversorIconv){
		return iconv($tipoEntrada,$tipoSaida,$string);
	}else{
		if(strtolower($tipoEntrada) == 'utf-8'){
			return utf8_decode($string);
		}else{
			return utf8_encode($string);
		}
	}
}
?>