<?php
/**
* Classe de controle
* Processar a valida��o do login
* @package Sistema
* @subpackage Login
*/
class CControleAcesso_verPrincipal extends controlePadrao{
	/**
	* M�todo inicial do controle
	*/
	function inicial(){
		try{
			$this->registrarInternacionalizacao();
			$this->gerarMenus();
			$this->visualizacaoPadrao->mostrar();
		}
		catch(erro $e){
			throw $e;
		}
	}
}
?>
