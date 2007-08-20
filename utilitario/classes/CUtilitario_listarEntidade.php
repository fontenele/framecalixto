<?php
/**
* Classe de controle
* Listar as entidades do sistema
* @package Sistema
* @subpackage Gerador
*/
class CUtilitario_listarEntidade extends controlePadraoPesquisar{
	/**
	* Método inicial do controle
	*/
	function inicial(){
		$d = dir(".");
		$negocios = new colecao();
		while (false !== ($arquivo = $d->read())) {
			if( is_dir($arquivo) && ($arquivo{0} !== '.') ){
				if(is_file($arquivo.'/classes/N'.ucfirst($arquivo).'.php')){
					$negocio = 'N'.ucfirst($arquivo);
					$obNegocio = new $negocio();
					x2($obNegocio,true);
//					$negocios->$negocio = $obNegocio->inter->pegarTitulo();
				}
			}
		}
		$d->close();
		x($negocios);
	}
}
?>