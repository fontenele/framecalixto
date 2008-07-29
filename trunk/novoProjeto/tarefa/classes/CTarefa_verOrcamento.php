<?php
/**
* Classe de controle
* Cria a visualização de um objeto : Tarefa
* @package Sistema
* @subpackage tarefa
*/
class CTarefa_verOrcamento extends CTarefa_verTarefa{
	/**
	* metodo de apresentação do negocio
	* @param [negocio] objeto para a apresentação
	*/
	public function montarApresentacao(negocio $negocio, $tipo = 'edicao'){
		if(!sessaoSistema::tem('usuario')) throw(new erroLogin('Usuário não registrado para acessar suas tarefas!'));
		parent::montarApresentacao($negocio,$tipo);
		$negocio->carregarOrcamentos();
		$this->visualizacao->textoOrcamentos =
		sprintf($this->inter->pegarTexto('orcamentos'),"(".$negocio->coOrcamentos->contarItens().")");
		$this->visualizacao->gravarOrcamento = VComponente::montar('confirmar','gravarOrcamento','Gravar Orçamento');
		$nItem = new NItem();
		$nItem->passarCsItem($negocio->pegarCsTipoTarefa());
		$this->visualizacao->idItem = VComponente::montar('caixa de combinacao','idItem',null,null,$nItem->pesquisar(new pagina())->gerarVetorDescritivo('&nbsp;'));
		$this->visualizacao->quantidade = VComponente::montar('Numerico','quantidade',null);
		$this->visualizacao->observacao = VComponente::montar('caixa de entrada','observacao',null);
		$this->visualizacao->verTarefa = $this->visualizacao->pegar('CTarefa_verTarefa.html');
		$listagem = new COrcamento_listagem();
		$listagem->colecao = $negocio->pegarCoOrcamentos();
		$listagem->controle= definicaoEntidade::controle('COrcamento_verPesquisa');
		$this->visualizacao->listagemOrcamentos = $listagem;
		$this->visualizacao->verTarefa = $this->visualizacao->pegar('CTarefa_verTarefa.html');
		$this->visualizacao->total = new TMoeda($negocio->pegarTotalOrcamento());
		$this->visualizacao->totalRecursivo = new TMoeda($negocio->pegarTotalOrcamentoTarefa());
	}
	/**
	* Método que monta a apresentação para o dono da tarefa
	*/
	public function montarApresentacaoDono($negocio){
		parent::montarApresentacaoVisual($negocio);
	}
	/**
	* Método que monta a apresentação para o responsável da tarefa
	*/
	public function montarApresentacaoResponsavel($negocio){
		parent::montarApresentacaoVisual($negocio);
	}
}
?>