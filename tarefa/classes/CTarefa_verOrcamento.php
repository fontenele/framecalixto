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
		$this->visualizacao->textoOrcamentos =
		sprintf($this->inter->pegarTexto('orcamentos'),"(".$negocio->coOrcamentos->contarItens().")");
		$this->visualizacao->gravarOrcamento = VComponente::montar('botao','gravarOrcamento','Gravar Orçamento');
		$negocio->carregarOrcamento();
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