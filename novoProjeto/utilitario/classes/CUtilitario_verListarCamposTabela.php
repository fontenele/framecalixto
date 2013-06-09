<?php
/**
* Classe de controle
* Lista os campos de uma tabela do banco de dados
* @package Sistema
* @subpackage Utilitario
*/
class CUtilitario_verListarCamposTabela extends controlePadrao{
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
	* Monta a coleção de menu do programa
	* @return colecaoPadraoMenu menu do programa
	*/
	public function montarMenuPrograma(){
		$menu = parent::montarMenuPrograma();
		$menu->{'Tabelas do Banco'}->passar_link('?c=CUtilitario_verListarTabelas');
		$menu->{'Tabelas do Banco'}->passar_imagem('icon-list');
		$menu->{'Entidades do sistema'}->passar_link('?c=CUtilitario_verListarEntidade');
		$menu->{'Entidades do sistema'}->passar_imagem('icon-globe');
		$menu->{'Carregar para o gerador'}->passar_link("?c=CUtilitario_verGeradorEntidade&tabela={$_GET['tabela']}");
		$menu->{'Carregar para o gerador'}->passar_imagem('icon-cog icon-white');
		$menu->{'Carregar para o gerador'}->passar_classeLink('btn btn-primary');
		return $menu;
	}
}
?>