<?php
/**
* Classe de controle
* Cria a visualização de um objeto : Tarefa
* @package Sistema
* @subpackage tarefa
*/
class CTarefa_verEdicao extends controlePadraoVerEdicao{
	public function inicial(){
		$usuario = sessaoSistema::pegar('usuario');
		$this->visualizacao->usuario = $usuario->valorDescricao();
		parent::inicial();
	}
}
?>
