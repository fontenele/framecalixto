<?php
/**
* Funções criadas para ler o arquivo de definições em xml e configurar o sistema
* @package Infra-estrutura
* @subpackage Definicão
*/
include_once('.calixto/classes/definicao.php');
//Lendo o arquivo XML de definições de diretórios e arquivos
$definicoes = definicao::pegarDefinicao();
//$definicoes = simplexml_load_file('.calixto/definicoes.xml');

//Lendo o diretorio de definiçoes e efetuando includes das classes de definição
foreach($definicoes->xpath('//classes/classe') as $index => $classe){
	if(isset($classe['id']) && caracteres($classe['id']) == 'definicao'){
		$dir = caracteres($classe['dir']).'/';
		break;
	}
}

//Incluindo os arquivos do diretório de definições
$dir = dir($dir);
while (false !== ($classe = $dir->read())) {
	if(preg_match('/(php)(.*)/', $classe, $resultado))
	include_once("{$dir->path}{$classe}");
}
$dir->close();

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
				$id = caracteres($classe['id']);
				$slice = substr($stClasse,0,strlen($id));
				if($id == $slice){
					if(caracteres($classe['entidade']) == 'sim') {
						$dir = "{$stEntidade}/".caracteres($classe['dir']).'/';
						$tipoBanco = caracteres($classe['tipoBanco']) == 'sim' ? caracteres($definicoes->banco['tipo']) : null ;
					}else{
						$dir = caracteres($classe['dir']).'/';
						$tipoBanco = caracteres($classe['tipoBanco']) == 'sim' ? caracteres($definicoes->banco['tipo']) : null ;
					}
				}
			}else{
				$dirPadrao = $classe['dir'];
				$tipoBanco = caracteres($classe['tipoBanco']) == 'sim' ? caracteres($definicoes->banco['tipo']) : null ;
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
	}catch (Exception $e) {
			$debug = debug_backtrace();
			echo "
			<link rel='stylesheet' href='.calixto/estilos/debug.css' />
			<div class='erroNegro'>
				<table class='erroNegro'>
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

/**
* retorna uma string com a codificação correta .
*/
function caracteres($string,$tipoEntrada = "UTF-8",$tipoSaida = "ISO-8859-1"){
	return strval($string);
}
?>
