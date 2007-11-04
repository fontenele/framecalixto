<?php
/**
* Classe de controle
* Importa um arquivo XML para o sistema
* @package Sistema
* @subpackage Gerador
*/
class CUtilitario_importadorXML extends controlePadrao{
	/**
	* Método inicial do controle
	*/
	function inicial(){
		$this->passarProximoControle(definicaoEntidade::controle($this,'verImportador'));
		if(isset($_POST['xml'])){
			$string = str_replace("\'","'",$_POST['xml']);
			$xml = simplexml_load_string($string);
			$vetor = array();
			foreach($xml->classe as $classe){
				$nomeClasse = (strval($classe['nome'])) ? strval($classe['nome']) : false;
				$operacao = (strval($classe['operacao'])) ? strval($classe['operacao']) : false;
				if($nomeClasse){
					$negocio = new $nomeClasse();
					$negocio->xmlPraNegocio($classe);
					$operacao ? $negocio->$operacao() : $negocio->importar() ;
				}
			}
		}
	}
}
?>