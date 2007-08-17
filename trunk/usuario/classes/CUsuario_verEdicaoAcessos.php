<?php
/**
* Classe de controle
* Cria a visualização de um objeto : Usuario
* @package Sistema
* @subpackage usuario
*/
class CUsuario_verEdicaoAcessos extends controlePadraoVerEdicaoUmPraMuitos{
/*	function inicial(){
		$persistente = new NUsuario();
		echo $persistente;
	}*/
	function montarApresentacao($negocio){
		parent::montarApresentacao($negocio,'edicao');
	}
}
?>