<?php
/**
* Classe de controle
* Realiza o lougout do sistema
* @package Sistema
* @subpackage Login
*/
class CControleAcesso_deslogar extends controlePadraoLiberado{
	public function inicial(){
		sessaoSistema::encerrar();
		$this->passarProximoControle('CControleAcesso_verPrincipal');
	}
}
?>
