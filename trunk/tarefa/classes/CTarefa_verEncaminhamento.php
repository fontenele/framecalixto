<?php
/**
* Classe de controle
* Cria a visualização de um objeto : Tarefa
* @package Sistema
* @subpackage tarefa
*/
class CTarefa_verEncaminhamento extends CTarefa_verTarefa{
	/**
	* metodo de apresentação do negocio
	* @param [negocio] objeto para a apresentação
	*/
	public function montarApresentacao(negocio $negocio, $tipo = 'edicao'){
		if(!sessaoSistema::tem('usuario')) throw(new erroLogin('Usuário não registrado para acessar suas tarefas!'));
		parent::montarApresentacao($negocio,$tipo);
		$arUsuarios = $this->montarVetorDescritivo('NUsuario','lerTodos');
		$nUsuario = sessaoSistema::pegar('usuario');
 		$estrutura = $this->mapearControle(definicaoArquivo::pegarXmlEntidade($this));
		switch(true){
			// Se for o dono da tarefa
			case($nUsuario->pegarIdUsuario() == $negocio->pegarIdDono()):
			// Se for o usuário responsável
			case(($negocio->pegarIdResponsavel() == $nUsuario->pegarIdUsuario())):
				$this->visualizacao->idRecebedor = VComponente::montar($estrutura['idResponsavel']['componente'],'idRecebedor',null,null,$arUsuarios);
				$this->visualizacao->encaminharTarefa = VComponente::montar('botao','encaminharTarefa','Encaminhar tarefa');
				$this->visualizacao->dsEncaminhamento = VComponente::montar('caixa de texto','dsEncaminhamento',null);
			break;
		}
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