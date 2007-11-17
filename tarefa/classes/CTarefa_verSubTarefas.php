<?php
/**
* Classe de controle
* Cria a visualização de um objeto : Tarefa
* @package Sistema
* @subpackage tarefa
*/
class CTarefa_verSubTarefas extends CTarefa_verTarefa{
	/**
	* metodo de apresentação do negocio
	* @param [negocio] objeto para a apresentação
	*/
	public function montarApresentacao(negocio $negocio, $tipo = 'edicao'){
		if(!sessaoSistema::tem('usuario')) throw(new erroLogin('Usuário não registrado para acessar suas tarefas!'));
		parent::montarApresentacao($negocio,$tipo);
 		$estrutura = $this->mapearControle(definicaoArquivo::pegarXmlEntidade($this));
		$negocio->carregarSubTarefas();
		$this->visualizacao->textoSubTarefas = sprintf($this->inter->pegarTexto('subTarefas'),"(".$negocio->coTarefas->contarItens().")");
 		$this->visualizacao->nrPercentualSubTarefa = VComponente::montar($estrutura['nrPercentual']['componente'],'nrPercentualSubTarefa',null,null,$estrutura['nrPercentual']['valores']);
 		$this->visualizacao->dsTarefaSubTarefa = VComponente::montar($estrutura['dsTarefa']['componente'],'dsTarefaSubTarefa',null,null,$estrutura['dsTarefa']['valores']);
 		$this->visualizacao->csTipoTarefaSubTarefa = VComponente::montar($estrutura['csTipoTarefa']['componente'],'csTipoTarefaSubTarefa',null,null,$estrutura['csTipoTarefa']['valores']);
 		$this->visualizacao->nrPrioridadeSubTarefa = VComponente::montar($estrutura['nrPrioridade']['componente'],'nrPrioridadeSubTarefa',null,null,$estrutura['nrPrioridade']['valores']);
		$this->visualizacao->gravarTarefa = VComponente::montar('confirmar','iniciarTarefa','Criar nova SubTarefa');
		$listagemTarefas = new CTarefa_listagem();
		$listagemTarefas->colecao = $negocio->pegarCoTarefas();
		$listagemTarefas->controle= definicaoEntidade::controle($this);
		$this->visualizacao->listagemSubtarefas = $listagemTarefas;
		$this->visualizacao->verTarefa = $this->visualizacao->pegar('CTarefa_verTarefa.html');
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