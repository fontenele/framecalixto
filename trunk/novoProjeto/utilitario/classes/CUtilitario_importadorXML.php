<?php
/**
* Classe de controle
* Importa um arquivo XML para o sistema
* @package Sistema
* @subpackage Utilitario
*/
class CUtilitario_importadorXML extends controlePadrao{
	protected static $imprimir = false;
	/**
	* Método inicial do controle
	*/
	function inicial(){
		self::$imprimir = isset($_GET['imprimir']);
		if(isset($_POST['xml'])){
			self::importarXml($_POST['xml']);
		}
	}
	public static function importarXml($xml){
		$xml = simplexml_load_string(str_replace("\'","'",$xml));
		foreach($xml->classe as $classe){
			$nomeClasse = (strval($classe['nome'])) ? strval($classe['nome']) : false;
			$operacao = (strval($classe['operacao'])) ? strval($classe['operacao']) : 'importar';
			if($nomeClasse){
				$negocio = new $nomeClasse();
				$negocio->xmlPraNegocio($classe);
				if(self::$imprimir) self::imprimirResultado($nomeClasse,$operacao,$negocio);
				$negocio->$operacao();
			}
		}
	}
	protected static function imprimirResultado($nomeClasse,$operacao,negocioPadrao $negocio){
		echo "Executando <strong>{$nomeClasse}::{$operacao}()</strong> com os valores:<br/>";
		foreach($negocio->vetor() as $var => $val){
			if($val instanceof colecao || $val instanceof negocio){
				echo "\${$var} = [Object]</br>";
			}else{
				echo "\${$var} = \"{$val}\"</br>";
			}
		}
		echo "<br/>";
	}
}
?>