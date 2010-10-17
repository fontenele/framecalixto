<?php
/**
* Classe de controle
* Atualizador de Base de Dados
* @package Sistema
* @subpackage Utilitario
*/
class CUtilitario_atualizadorBase extends controlePadrao{
	/**
	* Método inicial do controle
	*/
	function inicial(){
		$this->passarProximoControle(definicaoEntidade::controle('CControleAcesso','verPrincipal'));
		$imprimir = false;
		$c = conexao::criar();
		$persistentes = $this->classes();
		if($imprimir){
			echo '<pre>';
			foreach($persistentes as $nomePersistente){
				$persistente = new $nomePersistente($c);
				$nome = str_pad($nomePersistente, 40, "--", STR_PAD_LEFT);
				echo "\n\n".str_pad($nome, 80, "--", STR_PAD_RIGHT)."\n";
				echo $persistente->comandoDestruicaoCompleto();
				echo $persistente->comandoCriacaoCompleto();
			}
			if($persistente instanceof persistentePadraoPG)
				echo persistentePadraoPG::gerarComandoAccentRemove();
		}else{
			foreach($persistentes as $nomePersistente){
				$persistente = new $nomePersistente($c);
				$persistente->recriar();
			}
			if($persistente instanceof persistentePadraoPG) $persistente->plAccentRemove();
			CUtilitario_importadorXML::importarXml(file_get_contents('dados.xml'));
		}
		if($imprimir) die;
		$this->registrarComunicacao("Base de dados recriada.");
	}
	/**
	* Método de geração das classes e ordem a serem recriadas
	*/
	function classes(){
 		$classes[] = 'PEstado';
 		$classes[] = 'PPessoa';
 		$classes[] = 'PUsuario';
 		$classes[] = 'PPerfil';
 		$classes[] = 'PAcesso';
 		$classes[] = 'PUsuarioPerfil';
 		$classes[] = 'PLogAcesso';
 		$classes[] = 'PMenu';
 		$classes[] = 'PMenuItem';
		return $classes;
	}

}
?>
