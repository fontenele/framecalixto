<?php
/**
 * Testa a existencia de uma classe
 */
class CUtilitario_testarClasse extends controlePadrao {
	public function inicial(){
		if(isset($_REQUEST['classe'])){
			try{
				$arquivo = arquivoClasse($_REQUEST['classe']);
				if(isset($_REQUEST['metodo'])){
					$arArquivo = file($arquivo);
					foreach($arArquivo as $linha){
						if(preg_match('/[\ \t\n]*function[\ \t\n]*'.$_REQUEST['metodo'].'/i', $linha)){
							return;
						}
					}
					throw new Exception ("Método [{$_REQUEST['classe']}::{$_REQUEST['metodo']}] não encontrado.");
				}
			}  catch (Exception $e){
				echo $e->getMessage();
			}
		}
	}
}
?>
