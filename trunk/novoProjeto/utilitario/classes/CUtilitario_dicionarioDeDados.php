<?php

/**
 * Classe de controle
 * Atualizador de Base de Dados
 * @package Sistema
 * @subpackage Utilitario
 */
class CUtilitario_dicionarioDeDados extends controlePadrao {
	protected $unicoBanco = true;
	/**
	 * Método inicial do controle
	 */
	function inicial() {
		$this->registrarInternacionalizacao($this,$this->visualizacao);
		$this->gerarMenus();
		if ($this->unicoBanco) {
			$conexao = conexao::criar();
			$persistente = new PUtilitario($conexao);
			$tabelas = array();
			foreach ($persistente->lerTabelasComDescricao() as $tabela):
				$tabelas[$tabela['nome']]['tabela'] = $tabela;
				$tabelas[$tabela['nome']]['campos'] = $persistente->lerTabela($tabela['esquema'] . '.' . $tabela['nome']);
				$tabelas[$tabela['nome']]['restricoes'] = $persistente->lerRestricoes($tabela['esquema'] . '.' . $tabela['nome']);
			endforeach;
			$this->visualizacao->titulo = 'Dicionário de Dados';
			$this->visualizacao->servidor = '';
			$this->visualizacao->porta = '';
			$this->visualizacao->banco = '';
			$this->visualizacao->usuario = '';
			$this->visualizacao->senha = '';
			$this->visualizacao->tabelas = $tabelas;
			$this->visualizacao->action = '';
		}else {
			if ($_POST) {
				$conexao = conexao::criar(null, $_POST['servidor'], $_POST['porta'], $_POST['banco'], $_POST['usuario'], $_POST['senha'], 'postgres');
				$persistente = new PUtilitario($conexao);
				$tabelas = array();
				foreach ($persistente->lerTabelasComDescricao() as $tabela):
					$tabelas[$tabela['nome']]['tabela'] = $tabela;
					$tabelas[$tabela['nome']]['campos'] = $persistente->lerTabela($tabela['esquema'] . '.' . $tabela['nome']);
					$tabelas[$tabela['nome']]['restricoes'] = $persistente->lerRestricoes($tabela['esquema'] . '.' . $tabela['nome']);
				endforeach;
				$this->visualizacao->servidor = $_POST['servidor'];
				$this->visualizacao->porta = $_POST['porta'];
				$this->visualizacao->banco = $_POST['banco'];
				$this->visualizacao->usuario = $_POST['usuario'];
				$this->visualizacao->senha = '************';
				$this->visualizacao->tabelas = $tabelas;
				$this->visualizacao->action = '';
			}else {
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
				$this->visualizacao->tabelas = false;
			}
		}
		parent::inicial();
	}

	function gerarMenuprincipal() {
		if ($this->unicoBanco) {
			parent::gerarMenuprincipal();
		}
	}

	/**
	 * Retorna um array com os itens do menu do programa
	 * @return array itens do menu do programa
	 */
	function montarMenuPrograma() {
		if ($this->unicoBanco) {
			return parent::montarMenuPrograma();
		}else{
			$menu = parent::montarMenuPrograma();
			$gravar = 'Gerar';
			$menu->$gravar = new VMenu($gravar, 'javascript:$.submeter();', '.sistema/icones/disk.png');
			return $menu;
		}
	}

}

?>