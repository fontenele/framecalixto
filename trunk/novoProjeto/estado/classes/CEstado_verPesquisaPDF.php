<?php
/**
* Classe de controle
* Cria o relatório PDF da pesquisa de um objeto : Estado
* @package Sistema
* @subpackage Estado
*/
class CEstado_verPesquisaPDF extends controlePadraoPDFListagem{
	public function mostrarTodosFiltros(){ return false; }
}
?>