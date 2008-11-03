<?php
/**
* Classe de controle
* Atualizador de Base de Dados
* @package Sistema
* @subpackage Utilitario
*/
class CUtilitario_listarCamposTabela extends controlePadrao{
	/**
	* Método inicial do controle
	*/
	function inicial(){
		$this->registrarInternacionalizacao($this,$this->visualizacao);
		$this->gerarMenus();
		$conexao = conexao::criar();
		$persistente = new PUtilitario($conexao);
		$this->visualizacao->listagem = $persistente->lerTabela($_GET['tabela']);
		$this->visualizacao->tabela = $_GET['tabela'];
		$this->visualizacao->action = '';
		parent::inicial();
	}
	/**
	* Retorna um array com os itens do menu do programa
	* @return array itens do menu do programa
	*/
	function montarMenuPrograma(){
		$menu['Listagem']  = '?c=CUtilitario_listarTabelas';
		$menu['Carregar para o gerador']  = "?c=CUtilitario_geradorDefinirEntidade&tabela={$_GET['tabela']}";
		return $menu;
	}
}
?>