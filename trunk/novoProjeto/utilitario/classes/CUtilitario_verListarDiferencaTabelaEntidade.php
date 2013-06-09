<?php
/**
* Classe de controle
* Atualizador de Base de Dados
* @package Sistema
* @subpackage Utilitario
*/
class CUtilitario_verListarDiferencaTabelaEntidade extends controlePadrao{
	/**
	* Método inicial do controle
	*/
	function inicial(){
		$this->registrarInternacionalizacao($this,$this->visualizacao);
		$this->gerarMenus();
		$nNegocio = new $_GET['negocio'];
		$arPersistente = $nNegocio->pegarPersistente()->pegarEstrutura();
		$conexao = conexao::criar();
		$persistente = new PUtilitario($conexao);
		$arTabela = $persistente->lerTabela($arPersistente['nomeTabela']);
		$res = array();
		foreach($arTabela as $campo){
			$res[$campo['campo']]['banco'] = $campo['tamanho'] ? "{$campo['tipo']}({$campo['tamanho']})":"{$campo['tipo']}";
		}
		foreach($arPersistente['campo'] as $campo){
			$res[$campo['nome']]['aplicacao'] = $campo['tamanho'] ? "{$campo['tipo']}({$campo['tamanho']})":"{$campo['tipo']}";;
		}
		$this->visualizacao->listagem = $res;
		$this->visualizacao->tabela = $arPersistente['nomeTabela'];
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
		$menu->{'Tabelas do Banco'}->passar_imagem('utilitario/imagens/tabelas.png');
		$menu->{'Entidades do sistema'}->passar_link('?c=CUtilitario_verListarEntidade');
		$menu->{'Entidades do sistema'}->passar_imagem('utilitario/imagens/entidades.png');
		return $menu;
	}
}
?>