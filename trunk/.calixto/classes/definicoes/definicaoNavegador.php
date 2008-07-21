<?php
/**
* Classe de definição para o Navegador
* @package FrameCalixto
* @subpackage Definição
*/
class definicaoNavegador{
	/**
	* Retorna o nome do sistema
	*/
	static function pegarNome(){
		switch(true){
			case(strpos($_SERVER['HTTP_USER_AGENT'],'MSIE') !== false):
				return 'ie';
			break;
			default:
				return 'mozilla';
		}
	}
}
?>
