<?php
/**
* Classe de controle
* Atualizador de Base de Dados
* @package Sistema
* @subpackage Utilitario
*/
class CUtilitario_recriarBase extends controlePadrao{
	/**
	* Método inicial do controle
	*/
	function inicial(){
		$executarNoBanco = (isset($_POST['executarNoBanco']) ? $_POST['executarNoBanco'] : false);
		if(!$_POST['classes']) throw new erroNegocio('Nenhuma ação para executar.');
		$classes = $_POST['classes'];
		$c = conexao::criar();
		if($executarNoBanco){
			persistente::imprimirComandos(true,false);
			foreach($classes as $nomeClasse){
				$persistente = new $nomeClasse($c);
				$persistente->pegarPersistente()->recriar();
			}
			if($persistente instanceof persistentePadraoPG) $persistente->plAccentRemove();
			CUtilitario_importadorXML::importarXml(file_get_contents('dados.xml'));
			persistente::imprimirComandos(false,false);
		}else{
			echo '<pre>';
			foreach($classes as $nomeClasse){
				echo "<div class='linha'>";
				$negocio = new $nomeClasse($c);
				$persistente = $negocio->pegarPersistente();
				$nome = str_pad($nomeClasse, 40, "--", STR_PAD_LEFT);
				echo "\n\n".str_pad($nome, 80, "--", STR_PAD_RIGHT)."\n";
				echo $persistente->comandoDestruicaoCompleto();
				echo $persistente->comandoCriacaoCompleto();
				echo '</div>';
			}
			if($persistente instanceof persistentePadraoPG)
				echo "<div class='linha'>", persistentePadraoPG::gerarComandoAccentRemove(), '</div>';
			echo '</pre>';
		}
		if($executarNoBanco) die;
		$this->registrarComunicacao("Base de dados recriada.");
	}

}
?>
