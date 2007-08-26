<?php
/**
* Classe de representação de uma camada de negócio da entidade Controle Menu
* A camada de negócio é a parte que engloba as regras e efetua os comandos de execução de um sistema
* @package Sistema
* @subpackage Controle Menu
*/
class NControleMenu extends negocio{
	/**
	* Método criado para efetuar a montagem do menu do site
	*/
	public function menuPrincipal(){
		try{
			$menu['Principal'] = '#';
			$menu['Serviços']['Rede'] = '#';
			$menu['Serviços']['Hardware'] = '#';
			$menu['Serviços']['Software']['livre'] = '#';
			$menu['Serviços']['Software']['Proprietario'] = '#';
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
	* Método criado para efetuar a montagem do menu do sistema
	*/
	public function menuMenuSistema(){
		try{
			$menu['Cadastros']['Pessoa'] = '?c=CPessoa_verPesquisa';
			$menu['Cadastros']['Tarefas'] = '?c=CTarefa_verPesquisa';
			$menu['Cadastros']['Usuario'] = '?c=CUsuario_verPesquisa';
			$menu['Cadastros']['Atividades'] = '?c=CAtividade_verPesquisa';
			$menu['Cadastros']['Itens'] = '?c=CItem_verPesquisa';
			$menu['Movimento']['Tarefas do Usuário'] = '?c=CTarefa_verTarefasDoUsuario';
			$menu['Apoio']['Gerador'] = '?c=CUtilitario_listarEntidade';
			$menu['Apoio']['Atualizador de Base'] = '?c=CUtilitario_atualizadorBase';
			return $menu;
		}
		catch(erro $e){
			throw $e;
		}
	}
}
?>