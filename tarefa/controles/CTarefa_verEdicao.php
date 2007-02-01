<?php
/**
* Classe de controle
* Cria a visualiza��o de um objeto : Tarefa
* @package Sistema
* @subpackage tarefa
*/
class CTarefa_verEdicao extends controlePadraoVerEdicao{
	public function inicial(){
		$usuario = sessaoSistema::pegar('usuario');
		$this->visualizacaoPadrao->usuario = $usuario->valorDescricao();
		parent::inicial();
	}
}
?>
