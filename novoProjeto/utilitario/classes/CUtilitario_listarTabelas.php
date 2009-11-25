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
		$this->criarVisualizacaoPadrao();
		$this->registrarInternacionalizacao($this,$this->visualizacao);
		$this->gerarMenus();
		$conexao = conexao::criar();
		$persistente = new PUtilitario($conexao);
		$this->visualizacao->listagem = $persistente->lerTabelas();
		$this->visualizacao->action = '';
		parent::inicial();
	}
	/**
	* Retorna um array com os itens do menu do programa
	* @return array itens do menu do programa
	*/
	function montarMenuPrograma(){
		$menu = parent::montarMenuPrograma();
		$menu->{'Entidades do sistema'}->passar_link('?c=CUtilitario_listarEntidade');
		$menu->{'Entidades do sistema'}->passar_imagem('utilitario/imagens/entidades.png');
		return $menu;
	}
}
?>