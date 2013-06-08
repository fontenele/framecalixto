<?php
/**
* Classe de controle
* Visualiza a alteração de senha de um Usuário
* @package Sistema
* @subpackage Usuário
*/
class CUsuario_verAlterarSenha extends controlePadraoVerEdicao{
	/**
	* Retorna um array com os itens do menu do programa
	* @return array itens do menu do programa
	*/
	function montarMenuPrograma(){
		$menu = parent::montarMenuPrograma();
		$menu->removerItem($this->inter->pegarTexto('botaoExcluir'));
		$menu->removerItem($this->inter->pegarTexto('botaoListagem'));
		return $menu;
	}
}
?>