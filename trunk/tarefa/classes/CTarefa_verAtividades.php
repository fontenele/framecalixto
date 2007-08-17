<?php
/**
* Classe de controle
* Cria a visualização de um objeto : Tarefa
* @package Sistema
* @subpackage tarefa
*/
class CTarefa_verAtividades extends CTarefa_verTarefa{
	/**
	* metodo de apresentação do negocio
	* @param [negocio] objeto para a apresentação
	*/
	public function montarApresentacao(negocio $negocio, $tipo = 'edicao'){
		parent::montarApresentacao($negocio,$tipo);
		$negocio->carregarAtividades();
		$this->visualizacao->textoAtividades = sprintf($this->inter->pegarTexto('atividades'),"(".$negocio->coAtividades->contarItens().")");
		$this->visualizacao->gravarAtividade = VComponente::montar('botao','iniciarAtividade','Iniciar nova atividade');
		$this->visualizacao->dsAtividade = VComponente::montar('caixa de texto','dsAtividade',null);
		$listagemAtividades = new CAtividade_listagem();
		$listagemAtividades->colecao = $negocio->pegarCoAtividades();
		$listagemAtividades->controle= definicaoEntidade::controle('CAtividade_verPesquisa');
		$this->visualizacao->listagemAtividades = $listagemAtividades;
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