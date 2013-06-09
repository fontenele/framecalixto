<?php

/**
 * Classe de controle
 * Visualiza o dicionário de dados do banco corrente do sistema
 * @package Sistema
 * @subpackage Utilitario
 */
class CUtilitario_verDicionarioDeDados extends controlePadrao {
	protected $bancoAplicacao = false;
	/**
	 * Método inicial do controle
	 */
	function inicial() {
		$this->bancoAplicacao = isset($_GET['banco']) && $_GET['banco'] == 'outros' ? false : true;
		$this->registrarInternacionalizacao($this,$this->visualizacao);
		$this->gerarMenus();
		$this->visualizacao->action = '';
		$this->visualizacao->titulo = 'Dicionário de Dados';
		$this->visualizacao->servidor = '';
		$this->visualizacao->tipo = '';
		$this->visualizacao->porta = '';
		$this->visualizacao->banco = '';
		$this->visualizacao->usuario = '';
		$this->visualizacao->senha = '';
		$this->visualizacao->tabelas = $this->montarTabelas();
		if (!$this->bancoAplicacao) {
			if ($_POST) {
				$this->visualizacao->tipo = $_POST['tipo'];
				$this->visualizacao->servidor = $_POST['servidor'];
				$this->visualizacao->porta = $_POST['porta'];
				$this->visualizacao->banco = $_POST['banco'];
				$this->visualizacao->usuario = $_POST['usuario'];
				$this->visualizacao->senha = '************';
			}else {
				$this->visualizacao->tipo = VComponente::montar(VComponente::caixaCombinacao, 'tipo', '',null,array('postgres'=>'Postgres','sqlite'=>'Sqlite','mysql'=>'MySql'));
				$this->visualizacao->tipo->adicionarClass('obrigatorio');
				$this->visualizacao->tipo->passarTitle('Selecione o tipo do banco de dados');
				$this->visualizacao->servidor = VComponente::montar('caixa de entrada', 'servidor', '');
				$this->visualizacao->servidor->adicionarClass('obrigatorio');
				$this->visualizacao->servidor->passarTitle('Digite o nome do servidor do banco de dados');
				$this->visualizacao->porta = VComponente::montar('caixa de entrada', 'porta', '5432');
				$this->visualizacao->porta->adicionarClass('obrigatorio');
				$this->visualizacao->porta->passarTitle('Digite o número da porta do servidor do banco de dados');
				$this->visualizacao->banco = VComponente::montar('caixa de entrada', 'banco', '');
				$this->visualizacao->banco->adicionarClass('obrigatorio');
				$this->visualizacao->banco->passarTitle('Digite o nome do banco de dados');
				$this->visualizacao->usuario = VComponente::montar('caixa de entrada', 'usuario', '');
				$this->visualizacao->usuario->adicionarClass('obrigatorio');
				$this->visualizacao->usuario->passarTitle('Digite o nome do usuário do banco de dados');
				$this->visualizacao->senha = VComponente::montar('senha', 'senha', '');
				$this->visualizacao->senha->adicionarClass('obrigatorio');
				$this->visualizacao->senha->passarTitle('Digite a senha do usuário do banco de dados');
			}
		}
		parent::inicial();
	}
	/**
	 * Retorna a persistente utilizada para mapear as tabelas
	 * @return \PUtilitario
	 */
	protected function persistente(){
		if($_POST){
			include_once("PUtilitario.{$_POST['tipo']}.php");
			return new PUtilitario(conexao::criar(conexao::criar(null, $_POST['servidor'], $_POST['porta'], $_POST['banco'], $_POST['usuario'], $_POST['senha'], $_POST['tipo'])));
		}else{
			if(!$this->bancoAplicacao) return false;
			return new PUtilitario(conexao::criar());
		}
	}
	/**
	 * Retorna o mapeamento das tabelas
	 * @return array
	 */
	protected function montarTabelas(){
		$persistente = $this->persistente();
		if(!$persistente) return false;
		$tabelas = array();
		foreach ($persistente->lerTabelasComDescricao() as $tabela){
			$tabelas[$tabela['nome']]['tabela'] = $tabela;
			$tabelas[$tabela['nome']]['campos'] = $persistente->lerTabela($tabela['esquema'] . '.' . $tabela['nome']);
			$tabelas[$tabela['nome']]['restricoes'] = $persistente->lerRestricoes($tabela['esquema'] . '.' . $tabela['nome']);
		}
		return $tabelas;
	}

	function gerarMenuprincipal() {
		if ($this->bancoAplicacao) {
			parent::gerarMenuprincipal();
		}
	}

	/**
	 * Retorna um array com os itens do menu do programa
	 * @return array itens do menu do programa
	 */
	function montarMenuPrograma() {
		if ($this->bancoAplicacao) {
			return parent::montarMenuPrograma();
		}else{
			$menu = parent::montarMenuPrograma();
			$gravar = 'Gerar Dicionário';
			$menu->$gravar = new VMenu($gravar, 'javascript:$.submeter();', 'icon-cog icon-white');
			$menu->$gravar->passar_classeLink('btn btn-primary');
			return $menu;
		}
	}

}

?>