<?php
/**
* Classe de representa��o de uma camada de neg�cio da entidade Controle Menu
* A camada de neg�cio � a parte que engloba as regras e efetua os comandos de execu��o de um sistema
* @package Sistema
* @subpackage Controle Menu
*/
class NControleMenu extends negocio{
	/**
	* M�todo criado para efetuar a montagem do menu do site
	*/
	public function menuPrincipal(){
		try{
			$menu['Principal'] = '#';
			$menu['Servi�os']['Rede'] = '#';
			$menu['Servi�os']['Hardware'] = '#';
			$menu['Servi�os']['Software']['livre'] = '#';
			$menu['Servi�os']['Software']['Proprietario'] = '#';
			$menu['A Empresa'] = '#';
			$menu['Fale Conosco']['Via Telefone'] = '#';
			$menu['Fale Conosco']['Via Email'] = '#';
			$menu['Sistema'] = '?c=CControleAcesso_verLogin';
			return $menu;
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* M�todo criado para efetuar a montagem do menu do sistema
	*/
	public function menuMenuSistema(){
		try{
			$menu['Cadastros']['Pessoa'] = '?c=CPessoa_verPesquisa';
			$menu['Cadastros']['Tarefas'] = '?c=CTarefa_verPesquisa';
			$menu['Movimento']['Tarefas do Usu�rio'] = '?c=CTarefa_verTarefasDoUsuario';
			$menu['Cadastros']['Usuario'] = '?c=CUsuario_verPesquisa';
			$menu['Cadastros']['Atividades'] = '?c=CAtividade_verPesquisa';
			$menu['Apoio']['Gerador'] = '?c=CUtilitario_geradorDefinirEntidade';
			$menu['Apoio']['Atualizador de Base'] = '?c=CUtilitario_atualizadorBase';
			return $menu;
		}
		catch(erro $e){
			throw $e;
		}
	}
}
?>
