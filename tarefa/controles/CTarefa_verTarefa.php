<?php
/**
* Classe de controle
* Cria a visualização de um objeto : Tarefa
* @package Sistema
* @subpackage tarefa
*/
class CTarefa_verTarefa extends controlePadrao{
	public function inicial(){
		$this->gerarMenus();
		$this->registrarInternacionalizacao();
		$nUsuario = sessaoSistema::pegar('usuario');
		$this->visualizacaoPadrao->usuario = $nUsuario->valorDescricao();
		$tempo = new TTempo(3601);
		$this->visualizacaoPadrao->tempo = $tempo->pegarMinutos();
		if(!isset($_GET['idTarefa'])){
			$nTarefa  = new NTarefa();
			$nTarefa->ler(1);
			$nTarefa->trilharPais();
			$nTarefa->carregarAtividades();
			$nTarefa->carregarSubTarefas();
			$nTarefa->carregarOrcamento();
			$this->visualizacaoPadrao->textoAtividades = 
				sprintf($this->internacionalizacao->pegarTexto('atividades'),"(".$nTarefa->coAtividades->contarItens().")");
			$this->visualizacaoPadrao->textoSubTarefas = 
				sprintf($this->internacionalizacao->pegarTexto('subTarefas'),"(".$nTarefa->coTarefas->contarItens().")");
			$this->visualizacaoPadrao->textoOrcamentos = 
				sprintf($this->internacionalizacao->pegarTexto('orcamentos'),"(".$nTarefa->coOrcamentos->contarItens().")");
			switch($nTarefa->pegarCsTipoTarefa()){
				case 1:
					$this->visualizacaoPadrao->cssDescricao = 'descricaoAdministrativa';
				break;
				case 2:
					$this->visualizacaoPadrao->cssDescricao = 'descricaoHardware';
				break;
				case 3:
					$this->visualizacaoPadrao->cssDescricao = 'descricaoSoftware';
				break;
				case 4:
					$this->visualizacaoPadrao->cssDescricao = 'descricaoRede';
				break;
			}
			$controleListagemTarefas = new controlePadraoListagem();
			$controleListagemTarefas->colecao = $nTarefa->pegarCoTarefas();
			$controleListagemTarefas->controle = definicaoEntidade::controle($this);
			$this->visualizacaoPadrao->listagemSubtarefas = $controleListagemTarefas;
			$controleListagemAtividades = new controlePadraoListagem();
			$controleListagemAtividades->colecao = $nTarefa->pegarCoAtividades();
			$controleListagemAtividades->controle = definicaoEntidade::controle('CAtividade_verPesquisa');
			$this->visualizacaoPadrao->listagemAtividades = $controleListagemAtividades;
		}
		parent::inicial();
	}
	/**
	* Retorna um array com os itens do menu do programa
	* @return [array] itens do menu do programa
	*/
	function montarMenuPrograma(){
		$link = "?c=%s";
		$menu[$this->internacionalizacao->pegarTexto('tarefasDoUsuario')]  = 'javascript:document.formulario.submit();';
		return $menu;
	}
}
?>
