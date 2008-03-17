<?php
/**
* Classe de representação de uma camada de negócio da entidade Controle Menu
* A camada de negócio é a parte que engloba as regras e efetua os comandos de execução de um sistema
* @package Sistema
* @subpackage Controle Menu
*/
class NControleMenu extends negocio{
	protected $menuPrincipal = array();
	protected $menuSistema = array();
	/**
	* Método criado para efetuar a montagem do menu do site
	*/
	public function menuPrincipal(){
		try{
			$menu['Sistema']['Principal'] = '?c=CControleAcesso_verPrincipal';
			$menu['Sistema']['Login'] = '?c=CControleAcesso_verLogin';
			$menu['Serviços']['Rede'] = '#';
			$menu['Serviços']['Hardware'] = '#';
			$menu['Serviços']['Software']['livre'] = '#';
			$menu['Serviços']['Software']['Proprietario'] = '#';
			$menu['A Empresa'] = '#';
			$menu['Fale Conosco']['Via Telefone'] = '#';
			$menu['Fale Conosco']['Via Email'] = '#';
			return $menu;
		}
		catch(erro $e){
		}
	}
	/**
	* Método criado para efetuar a montagem do menu do sistema
	*/
	public function menuMenuSistema(){
		$this->adicionarItemSistema('Cadastros/Pessoa','CPessoa_verPesquisa');
		$this->adicionarItemSistema('Cadastros/Tarefas','CTarefa_verPesquisa');
		$this->adicionarItemSistema('Cadastros/Usuario','CUsuario_verPesquisa');
		$this->adicionarItemSistema('Cadastros/Atividades','CAtividade_verPesquisa');
		$this->adicionarItemSistema('Cadastros/Itens','CItem_verPesquisa');
		$this->adicionarItemSistema('Movimento/Tarefas do Usuário','CTarefa_verTarefasDoUsuario');
		$this->adicionarItemSistema('Relatórios/Cliente','CTarefa_verTarefasDoDono');
		$this->adicionarItemSistema('Apoio/Gerador','CUtilitario_listarEntidade');
		$this->adicionarItemSistema('Apoio/Recriador de Base','CUtilitario_atualizadorBase');
		$this->adicionarItemSistema('Apoio/Importador','CUtilitario_importadorXML');
		$this->adicionarItemSistema('Apoio/Definições do Sistema','CUtilitario_geradorDefinirSistema');
		return $this->menuSistema;
	}
	/**
	* Método criado para fazer a verificação do menuPrincipal do sistema quanto ao controle de acesso
	* @param [texto] caminho do item separado por / (barra)
	* @param [texto] item do menu que será acessado
	* @param [booleano] destrava a validação do controle de acesso
	*/
	protected function adicionarItemPrincipal($endereco,$item,$travarLink = true){
		try{
			if($travarLink) NControleAcesso::validarAcesso($item);
			eval("\$this->menuPrincipal['".str_replace('/',"']['",$endereco)."'] = '?c={$item}';");
		}catch(erro $e){}
	}
	/**
	* Método criado para fazer a verificação do m do sistema quanto ao controle de acesso
	* @param [texto] caminho do item separado por / (barra)
	* @param [texto] item do menu que será acessado
	* @param [booleano] destrava a validação do controle de acesso
	*/
	protected function adicionarItemSistema($endereco,$item,$travarLink = true){
		try{
			if($travarLink) NControleAcesso::validarAcesso($item);
			eval("\$this->menuSistema['".str_replace('/',"']['",$endereco)."'] = '?c={$item}';");
		}catch(erro $e){}
	}
}
?>