<?php
/**
* Classe de reprensa��o de arquivo
* Esta classe encapsula as formas de acesso a um arquivo
* @package Infra-estrutura
* @subpackage utilit�rios
*/
class arquivo extends objeto{
	/**
	* M�todo de verifica��o da legibilidade do arquivo
	* @param [string] caminho do arquivo a ser verificado
	* @return [booleano]
	*/
	function legivel($caminhoArquivo){
		try{
			$stArquivo = basename($caminhoArquivo);
			$stDiretorio = dirname($caminhoArquivo);
			switch(true){
				case !(is_dir($stDiretorio)):
					throw new erroInclusao("Diret�rio [$stDiretorio] inexistente!");
				break;
				case !(is_file($caminhoArquivo)):
					throw new erroInclusao("Arquivo [$stArquivo] inexistente!");
				break;
				case !(is_readable($caminhoArquivo)):
					throw new erroInclusao("Arquivo [$stArquivo] sem permiss�o de leitura!");
				break;
			}
			return true;
		}
		catch(erro $e){
			throw $e;
		}
	}
}
?>
