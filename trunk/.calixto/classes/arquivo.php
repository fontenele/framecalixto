<?php
/**
* Classe de reprensação de arquivo
* Esta classe encapsula as formas de acesso a um arquivo
* @package Infra-estrutura
* @subpackage utilitários
*/
class arquivo extends objeto{
	/**
	* Método de verificação da legibilidade do arquivo
	* @param [string] caminho do arquivo a ser verificado
	* @return [booleano]
	*/
	static function legivel($caminhoArquivo){
		try{
			$stArquivo = basename($caminhoArquivo);
			$stDiretorio = dirname($caminhoArquivo);
			switch(true){
				case !(is_dir($stDiretorio)):
					throw new erroInclusao("Diretório [$stDiretorio] inexistente!");
				break;
				case !(is_file($caminhoArquivo)):
					throw new erroInclusao("Arquivo [$stArquivo] inexistente!");
				break;
				case !(is_readable($caminhoArquivo)):
					throw new erroInclusao("Arquivo [$stArquivo] sem permissão de leitura!");
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
