<?php
/**
* Classe de controle
* Atualizador de Base de Dados
* @package Sistema
* @subpackage Utilitario
*/
class CUtilitario_listarTabelas extends controlePadrao{
	/**
	* Método inicial do controle
	*/
	function inicial(){
		$this->inter = new IUtilitario();
		$this->criarVisualizacaoPadrao();
		$conexao = conexao::criar();
		$persistente = new PUtilitario($conexao);
		$this->visualizacao->listagem = $persistente->lerTabelas();
		$this->visualizacao->action = '';
		parent::inicial();
	}
}
?>