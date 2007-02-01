<?php
/**
* Fun��es criadas para ler o arquivo de defini��es em xml e configurar o sistema
* @package Infra-estrutura
* @subpackage Definic�o
*/
include_once('.calixto/classes/definicao.php');
//Lendo o arquivo XML de defini��es de diret�rios e arquivos
$definicoes = definicao::pegarDefinicao();
//$definicoes = simplexml_load_file('.calixto/definicoes.xml');

//Lendo o diretorio de defini�oes e efetuando includes das classes de defini��o
foreach($definicoes->xpath('//classes/classe') as $index => $classe){
	if(isset($classe['id']) && caracteres($classe['id']) == 'definicao'){
		$dir = caracteres($classe['dir']).'/';
		break;
	}
}

//Incluindo os arquivos do diret�rio de defini��es
$dir = dir($dir);
while (false !== ($classe = $dir->read())) {
	if(preg_match('/(php)(.*)/', $classe, $resultado))
	include("{$dir->path}{$classe}");
}
$dir->close();

/**
* Sobrecarga da fun��o __autoload do php
* Faz o include automaticamente do arquivo da classe
* Utiliza o arquivo XML de defini��es de diret�rios e arquivos como base
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
				throw new erroInclusao("Diret�rio [$stDiretorio] inexistente!");
			break;
			case !(is_file($stArquivo)):
				throw new erroInclusao("Arquivo [$stArquivo] inexistente!");
			break;
			case !(is_readable($stArquivo)):
				throw new erroInclusao("Arquivo [$stArquivo] sem permiss�o de leitura!");
			break;
			default:
				include_once $stArquivo;
			break;
		}
	}catch (Exception $e) {
		ECHO $e->__toString();
	}
}

/**
* retorna uma string com a codifica��o correta .
*/
function caracteres($string,$tipoEntrada = "UTF-8",$tipoSaida = "ISO-8859-1"){
	return iconv($tipoEntrada, $tipoSaida, strval($string));
}
?>
