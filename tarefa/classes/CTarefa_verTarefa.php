<?php
/**
* Classe de controle
* Cria a visualização de um objeto : Tarefa
* @package Sistema
* @subpackage tarefa
*/
class CTarefa_verTarefa extends controlePadrao{
	/**
	* Método inicial do controle
	*/
	public function inicial(){
		$this->gerarMenus();
		$this->registrarInternacionalizacao();
		$nTarefa = new NTarefa();
		switch(true){
			case isset($_GET['chave']):
				$nTarefa->ler($_GET['chave']);
				$this->sessao->registrar('idTarefa',$nTarefa->pegarIdTarefa());
			break;
			case $this->sessao->tem('idTarefa'):
				$nTarefa->ler($this->sessao->pegar('idTarefa'));
			break;
		}
		$this->montarApresentacao($nTarefa);
		parent::inicial();
	}
	/**
	* metodo de apresentação do negocio
	* @param [negocio] objeto para a apresentação
	*/
	public function montarApresentacao(negocio $negocio, $tipo = 'edicao'){
		$arPessoas = $this->montarVetorDescritivo('NPessoa','lerTodos');
		$arUsuarios = $this->montarVetorDescritivo('NUsuario','lerTodos');
		$icone = new VEtiquetaHtml('img');
		switch(true){
			case $negocio->pegarCsStatus()== 'F'  :
				$this->visualizacao->cssDescricao = 'descricaoFechada';
			break;
			case $negocio->pegarCsTipoTarefa()== 1:
				$this->visualizacao->cssDescricao = 'descricaoAdministrativa';
				$icone->passarAlt('Administrativa');
				$icone->passarSrc('tarefa/imagens/background_software.png');
				$this->visualizacao->icone = $icone;
			break;
			case $negocio->pegarCsTipoTarefa()== 2:
				$this->visualizacao->cssDescricao = 'descricaoHardware';
			break;
			case $negocio->pegarCsTipoTarefa()== 3:
				 $this->visualizacao->cssDescricao = 'descricaoSoftware';
				$icone->passarAlt('Administrativa');
				$icone->passarSrc('tarefa/imagens/background_software.png');
				$this->visualizacao->icone = $icone;
			break;
			case $negocio->pegarCsTipoTarefa()== 4:
				 $this->visualizacao->cssDescricao = 'descricaoRede';
			break;
		}
		$nUsuario = sessaoSistema::pegar('usuario');
		switch(true){
			// Se for o dono da tarefa
			case($nUsuario->pegarIdUsuario() == $negocio->pegarIdDono()):
				$this->montarApresentacaoDono($negocio);
			break;
			// Se for o usuário responsável
			case(($negocio->pegarIdResponsavel() == $nUsuario->pegarIdUsuario())):
				$this->montarApresentacaoResponsavel($negocio);
			break;
			// Se for normal
			default;
				parent::montarApresentacaoVisual($negocio);
		}
		$this->visualizacao->idDono = $arPessoas[$negocio->pegarIdDono()];
		$this->visualizacao->idResponsavel = $arUsuarios[$negocio->pegarIdResponsavel()];
		$this->visualizacao->dtInicio = $negocio->pegarDtInicio();
		$this->visualizacao->dtFim = $negocio->pegarDtFim();
		$this->visualizacao->idTarefa = VComponente::montar('oculto','idTarefa',$negocio->pegarIdTarefa());
		$this->visualizacao->idTarefaAtual = $negocio->pegarIdTarefa();
		$this->visualizacao->action = sprintf('?c=%s',definicaoEntidade::controle($this,'atualizarTarefa'));
		$this->visualizacao->usuario = $nUsuario->valorDescricao();
		$coPais = new colecao();
		$negocio->trilharPais($coPais);
		$trilhaDePais = '';
		$controle = definicaoEntidade::controle($this,'verTarefa');
		while($negocioPai = $coPais->avancar()){
			$link = sprintf("?c=%s&amp;chave=%s",$controle,$negocioPai->valorChave());
			$trilhaDePais .= sprintf('<a href="%s">%s</a>, ',$link,$negocioPai->valorChave());
		}
		$this->visualizacao->trilhaDePais = $trilhaDePais;
	}
	/**
	* Método que monta a apresentação para o dono da tarefa
	*/
	public function montarApresentacaoDono($negocio){
		parent::montarApresentacaoEdicao($negocio);
		$this->visualizacao->atualizarTarefa = VComponente::montar('botao','atualizarTarefa','Atualizar tarefa');
	}
	/**
	* Método que monta a apresentação para o responsável da tarefa
	*/
	public function montarApresentacaoResponsavel($negocio){
		parent::montarApresentacaoVisual($negocio);
		$this->visualizacao->nrPercentual = VComponente::montar('VSelectPercentual','nrPercentual',$negocio->pegarNrPercentual());
		$this->visualizacao->atualizarTarefa = VComponente::montar('botao','atualizarTarefa','Atualizar tarefa');
	}
	/**
	* Retorna um array com os itens do menu do programa
	* @return [array] itens do menu do programa
	*/
	public function montarMenuPrograma(){
		$link = "?c=%s";
		$menu[$this->inter->pegarTexto('tarefasDoUsuario')]  = sprintf($link,definicaoEntidade::controle($this,'verTarefasDoUsuario'));
		$menu[$this->inter->pegarTexto('verTarefa')]= sprintf($link,definicaoEntidade::controle($this,'verTarefa'));
		$menu[$this->inter->pegarTexto('atividadesDaTarefa')]= sprintf($link,definicaoEntidade::controle($this,'verAtividades'));
		$menu[$this->inter->pegarTexto('subtarefasDaTarefa')]= sprintf($link,definicaoEntidade::controle($this,'verSubTarefas'));
		$menu[$this->inter->pegarTexto('orcamentosDaTarefa')]= sprintf($link,definicaoEntidade::controle($this,'verOrcamento'));
		$menu[$this->inter->pegarTexto('encaminharTarefa')]  = sprintf($link,definicaoEntidade::controle($this,'verEncaminhamento'));
		return $menu;
	}
}
?>