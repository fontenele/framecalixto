<?php
/**
* Classe de controle
* Cria o relatório PDF da pesquisa de um objeto : Estado
* @package Sistema
* @subpackage Estado
*/
class CEstado_verPesquisaPDF extends controlePadraoPDFListagem{
	/**
	* Método que monta o topo do relatório
	*/
	public function montarTopo($mostrarTodos = false){
		parent::montarTopo($mostrarTodos);
	}
}
?>