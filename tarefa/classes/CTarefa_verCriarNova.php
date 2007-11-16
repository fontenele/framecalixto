<?php
/**
* Classe de controle
* Executa a criação de um objeto : Tarefa
* @package Sistema
* @subpackage tarefa
*/
class CTarefa_verCriarNova extends controlePadraoVerEdicao{
	/**
	* Método criado para definir o objeto de negócio a ser apresentado
	*/
	public function definirNegocio(){
		if(isset($_GET['pessoa']))
			$this->sessao->limpar();
		parent::definirNegocio();
		$nUsuarioSessao = sessaoSistema::pegar('usuario');
		$this->negocio->passarIdCriador($nUsuarioSessao->pegarIdUsuario());
		$this->negocio->passarIdResponsavel($nUsuarioSessao->pegarIdUsuario());
		$this->negocio->passarIdResponsavelAnterior($nUsuarioSessao->pegarIdUsuario());
		if(isset($_GET['pessoa'])) $this->negocio->passarIdDono($_GET['pessoa']);
		if(!$this->negocio->pegarNrPrioridade()) $this->negocio->passarNrPrioridade(4);
		if(!$this->negocio->pegarDtInicioPrevisto()){
			$amanha = clone $agora = TData::agora();
			$amanha->somarDia();
			$this->negocio->passarDtInicioPrevisto($agora);
			$this->negocio->passarDtFimPrevisto($amanha);
		}
	}
	/**
	* metodo de apresentação do negocio
	* @param [negocio] objeto para a apresentação
	* @param [string] tipo de visualização a ser utilizada 'edicao' ou 'visual'
	*/
	public function montarApresentacao(negocio $negocio, $tipo = 'edicao'){
		parent::montarApresentacao($negocio, $tipo);
		$arPessoas = $this->montarVetorDescritivo('NPessoa');
		$arUsuarios = $this->montarVetorDescritivo('NUsuario');
		$this->visualizacao->action = sprintf('?c=%s',definicaoEntidade::controle($this,'criarNova'));
		$this->visualizacao->idDono =
			$arPessoas[$this->negocio->pegarIdDono()].
			VComponente::montar('oculto','idDono',$this->negocio->pegarIdDono())->__toString();
		$this->visualizacao->idCriador =
			$arUsuarios[$this->negocio->pegarIdCriador()].
			VComponente::montar('oculto','idCriador',$this->negocio->pegarIdCriador())->__toString();
		$this->visualizacao->idResponsavel =
			$arUsuarios[$this->negocio->pegarIdResponsavel()].
			VComponente::montar('oculto','idResponsavel',$this->negocio->pegarIdResponsavel())->__toString();
		$this->visualizacao->idResponsavelAnterior =
			$arUsuarios[$this->negocio->pegarIdResponsavelAnterior()].
			VComponente::montar('oculto','idResponsavelAnterior',$this->negocio->pegarIdResponsavelAnterior())->__toString();
	}
	/**
	* Retorna um array com os itens do menu do programa
	* @return [array] itens do menu do programa
	*/
	function montarMenuPrograma(){
		$menu = parent::montarMenuPrograma();
		$link = "?c=%s";
		$menu[$this->inter->pegarTexto('botaoListagem')]= sprintf($link,definicaoEntidade::controle('CPessoa','verPesquisa'));
		return $menu;
	}
}
?>