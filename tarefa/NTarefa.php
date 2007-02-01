<?php
/**
* Classe de representa��o de uma camada de neg�cio da entidade 
* A camada de neg�cio � a parte que engloba as regras e efetua os comandos de execu��o de um sistema
* @package Sistema
* @subpackage tarefa
*/
class NTarefa extends negocioPadrao{
	/**
	* @var [numerico] Id Tarefa
	*/
	public $idTarefa;
	/**
	* @var [numerico] Id Tarefa Pai
	*/
	public $idTarefaPai;
	/**
	* @var [numerico] Id Tarefa Predecessora
	*/
	public $idTarefaPredecessora;
	/**
	* @var [numerico] Cs Tipo Tarefa
	*/
	public $csTipoTarefa;
	/**
	* @var [numerico] Id Criador
	*/
	public $idCriador;
	/**
	* @var [numerico] Id Responsavel
	*/
	public $idResponsavel;
	/**
	* @var [numerico] Id Responsavel Anterior
	*/
	public $idResponsavelAnterior;
	/**
	* @var [texto] Ds Tarefa
	*/
	public $dsTarefa;
	/**
	* @var [data] Dt Inicio Previsto
	*/
	public $dtInicioPrevisto;
	/**
	* @var [data] Dt Fim Previsto
	*/
	public $dtFimPrevisto;
	/**
	* @var [data] Dt Inicio
	*/
	public $dtInicio;
	/**
	* @var [data] Dt Fim
	*/
	public $dtFim;
	/**
	* @var [texto] Cs Status
	*/
	public $csStatus;
	/**
	* @var [numerico] Nr Percentual
	*/
	public $nrPercentual;
	/**
	* @var [numerico] Nr Prioridade
	*/
	public $nrPrioridade;
	/**
	* @var [colecao] colecao de pais da tarefa
	*/
	public $coPais;
	/**
	* @var [colecao] colecao de subtarefas
	*/
	public $coTarefas;
	/**
	* @var [colecao] colecao de atividades da tarefa
	*/
	public $coAtividades;
	/**
	* @var [colecao] cole��o de itens or�ados para a tarefa
	*/
	public $coOrcamentos;
	/**
	* Metodo construtor
	* @param [conexao] (opcional) conex�o com o banco de dados
	*/
	public function __construct($conexao = null){
		$this->coPais = new colecao();
		$this->coTarefas = new colecao();
		$this->coAtividades = new colecao();
		$this->coOrcamentos = new colecao();
		parent::__construct($conexao);
	}
	/**
	* Retorna o nome da propriedade que cont�m o valor chave de neg�cio
	* @return [string] 
	*/
	function nomeChave(){ return 'idTarefa'; }
	/**
	* Retorna uma cole��o com as tarefas do usuario
	* @param [NUsuario]
	* @return [colecao]
	*/
	public function lerTarefasDoUsuario(NUsuario $usuario, pagina $pagina){
		$nTarefa = new NTarefa();
		$nTarefa->passarIdResponsavel($usuario->pegarIdUsuario());
		return $nTarefa->pesquisar($pagina);
	}
	/**
	* M�todo que executa o encaminhamento da tarefa
	* Muda o respons�vel pela tarefa
	* @param [NUsuario]
	*/
	public function encaminharTarefa(NUsuario $nUsuario){
		try{
			$this->passarIdResponsavel($nUsuario->pegarIdUsuario());
			$this->gravar();
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Retorna uma cole��o com as tarefas pais desta tarefa
	* @return [colecao]
	*/
	public function trilharPais(){
		$conexao = $this->pegarConexao();
		$colecao = new colecao();
		$this->fecharConexao($conexao);
		return $colecao;
	}
	/**
	* Retorna uma cole��o com as atividades da tarefa
	* @return [colecao]
	*/
	public function carregarAtividades(){
		$conexao = $this->pegarConexao();
		$colecao = new colecao();
		$this->fecharConexao($conexao);
		return $colecao;
	}
	/**
	* Retorna uma cole��o com os itens do or�amento da tarefa
	* @return [colecao]
	*/
	public function carregarOrcamento(){
		$conexao = $this->pegarConexao();
		$colecao = new colecao();
		$this->fecharConexao($conexao);
		return $colecao;
	}
	/**
	* Retorna uma cole��o com as tarefas filhas desta tarefa
	* @return [colecao]
	*/
	public function carregarSubTarefas(){
		try{
			$conexao = $this->pegarConexao();
			$nTarefa = new NTarefa($conexao);
			$nTarefa->passarIdTarefaPai($this->pegarIdTarefa());
			$colecao = $nTarefa->pesquisar(new pagina());
			$this->fecharConexao($conexao);
			return $colecao;
		}
		catch(erro $e){
			throw $e;
		}
	}
}
?>
