<?php
/**
 * Classe de controle
 * Testa um caminho do sistema
 */
class CUtilitario_testarCaminho extends controlePadrao{
	public function inicial(){
	 try {
		if (isset($_REQUEST['arquivo'])){
			arquivo::legivel($_REQUEST['arquivo']);
		}
		if (isset($_REQUEST['diretorio'])){
			diretorio::legivel($_REQUEST['diretorio']);
		}
		} catch (Exception $exc) {
			echo $exc->getMessage();
		}
	}
}
?>
