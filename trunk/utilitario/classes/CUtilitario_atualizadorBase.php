<?php
/**
* Classe de controle
* Ver o Usuário
* @package Sistema
* @subpackage Atualizador de Base de Dados
*/
class CUtilitario_atualizadorBase extends controlePadrao{
	/**
	* Método inicial do controle
	*/
	function inicial(){
 		$persistentes[] = 'PPessoa';
 		$persistentes[] = 'PUsuario';
 		$persistentes[] = 'PTarefa';
 		$persistentes[] = 'PAtividade';
 		$persistentes[] = 'PItem';
		$persistentes[] = 'PAcessoDoUsuario';
		$c = conexao::criar();
		foreach($persistentes as $persistente){
			$persistente = new $persistente($c);
			$persistente->recriar();
		}
		$c->fechar();
	}
}
?>
