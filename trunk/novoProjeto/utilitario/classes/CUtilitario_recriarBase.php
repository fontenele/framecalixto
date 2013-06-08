<?php

/**
 * Classe de controle
 * Atualizador de Base de Dados
 * @package Sistema
 * @subpackage Utilitario
 */
class CUtilitario_recriarBase extends controlePadrao {

	/**
	 * Método inicial do controle
	 */
	function inicial() {
		if (!$_POST['classes'])
			throw new erroNegocio('Nenhuma ação para executar.');
		if ($_POST['executarNoBanco']) {
			persistente::imprimirComandos(true, false);
			self::executarNoBanco($_POST);
			persistente::imprimirComandos(false, false);
		} else {
			self::executarSomenteImpressao($_POST);
		}
		if ($_POST['executarNoBanco'])
			die;
	}

	public static function executarNoBanco($dados) {
		$c = conexao::criar();
	//	$schema = false;
		foreach ($dados['classes'] as $nomeClasse) {
			$negocio = new $nomeClasse($c);
	/*		if(!$schema){
				$negocio->pegarPersistente()->criarSchema();
				$schema = true;
			}
	*/		$negocio->pegarPersistente()->recriar();
		}
		if ($c->pegarTipo() == conexao::postgres) {
			$pUtilitario = new PUtilitario($c);
			$pUtilitario->plAccentRemove();
		}
		CUtilitario_importadorXML::importarXml(file_get_contents('dados.xml'));
	}

	public static function executarSomenteImpressao($dados) {
		$c = conexao::criar();
		echo '<pre>';
		foreach ($dados['classes'] as $nomeClasse) {
			echo "<div class='linha'>";
			$negocio = new $nomeClasse($c);
			$persistente = $negocio->pegarPersistente();
			$nome = str_pad($nomeClasse, 40, "--", STR_PAD_LEFT);
			echo "\n\n" . str_pad($nome, 80, "--", STR_PAD_RIGHT) . "\n";
			echo $persistente->comandoDestruicaoCompleto();
			echo $persistente->comandoCriacaoCompleto();
			echo '</div>';
		}
		if ($persistente instanceof persistentePadraoPG)
			echo "<div class='linha'>", persistentePadraoPG::gerarComandoAccentRemove(), '</div>';
		echo '</pre>';
	}

}

?>
