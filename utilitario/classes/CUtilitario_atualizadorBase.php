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
		$c = conexao::criar();
		$persistentes = $this->classes();
		foreach($persistentes as $persistente){
			$persistente = new $persistente($c);
			$persistente->recriar();
		}
	}
	/**
	* Método de geração das classes e ordem a serem recriadas
	*/
	function classes(){
 		$classes[] = 'PEstado';
 		$classes[] = 'PPessoa';
 		$classes[] = 'PUsuario';
 		$classes[] = 'PTarefa';
 		$classes[] = 'PAtividade';
 		$classes[] = 'PItem';
		$classes[] = 'PAcessoDoUsuario';
		return $classes;
	}

}
?>
