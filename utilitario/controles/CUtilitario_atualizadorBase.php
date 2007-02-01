<?php
/**
* Classe de controle
* Ver o Usu�rio
* @package Sistema
* @subpackage Atualizador de Base de Dados
*/
class CUtilitario_atualizadorBase extends controlePadrao{
	/**
	* M�todo inicial do controle
	*/
	function inicial(){
		// $persistentes[] = 'PPessoa';
		//$persistentes[] = 'PTipoTarefa';
		// $persistentes[] = 'PUsuario';
		// $persistentes[] = 'PTarefa';
		$persistentes[] = 'PAtividade';
		foreach($persistentes as $persistente){
			$persistente = new $persistente();
			$persistente->criar();
		}
	}
}
?>
