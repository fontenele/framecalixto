<?php
/**
* Classe de reprentação de diretorio
* Esta classe encapsula as formas de acesso a um diretório
* @package Infra-estrutura
* @subpackage utilitários
*/
class diretorio extends Directory {
	/**
	* Método de verificação da legibilidade do arquivo
	* @param [string] caminho do arquivo a ser verificado
	* @return [booleano]
	*/
	static function legivel($caminhoDiretorio){
		try{
			switch(true){
				case !(is_dir($caminhoDiretorio)):
					throw new erroInclusao("Diretório [$caminhoDiretorio] inexistente!");
				break;
				case !(is_readable($caminhoDiretorio)):
					throw new erroInclusao("Diretório [$caminhoDiretorio] sem permissão de leitura!");
				break;
			}
			return true;
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Método de verificação da escrita do diretório
	* @param [string] caminho do arquivo a ser verificado
	* @return [booleano]
	*/
	static function gravavel($caminhoDiretorio){
		try{
			if(!is_writable($caminhoDiretorio)) 
				throw new erroEscrita("Diretório [$caminhoDiretorio] sem permissão de escrita!");
			return true;
		}
		catch(erro $e){
			throw $e;
		}
	}
}
?>
