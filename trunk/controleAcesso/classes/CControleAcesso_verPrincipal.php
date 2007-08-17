<?php
/**
* Classe de controle
* Processar a validação do login
* @package Sistema
* @subpackage Login
*/
class CControleAcesso_verPrincipal extends controlePadrao{
	/**
	* Método inicial do controle
	*/
	function inicial(){
		try{
			$this->registrarInternacionalizacao();
			$this->gerarMenus();
			$this->visualizacao->mostrar();
		}
		catch(erro $e){
			throw $e;
		}
	}
}
?>
