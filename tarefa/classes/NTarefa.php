<?php
/**
* Classe de representação de uma camada de negócio da entidade 
* A camada de negócio é a parte que engloba as regras e efetua os comandos de execução de um sistema
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
	* @var [numerico] Id Dono
	*/
	public $idDono;
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
	* @var [colecao] coleção de itens orçados para a tarefa
	*/
	public $coOrcamentos;
	/**
	* Metodo construtor
	* @param [conexao] (opcional) conexão com o banco de dados
	*/
	public function __construct($conexao = null){
		$this->coPais = new colecaoPadraoNegocio(null,$conexao);
		$this->coTarefas = new colecaoPadraoNegocio(null,$conexao);
		$this->coAtividades = new colecaoPadraoNegocio(null,$conexao);
		$this->coOrcamentos = new colecaoPadraoNegocio(null,$conexao);
		parent::__construct($conexao);
	}
	/**
	* Retorna o nome da propriedade que contém o valor chave de negócio
	* @return [string] 
	*/
	function nomeChave(){ return 'idTarefa'; }
	/**
	* Retorna uma coleção com as atividades da tarefa
	* @return [colecao]
	*/
	public function carregarAtividades(){
		$conexao = $this->pegarConexao();
		$nAtividade = new NAtividade($conexao);
		$nAtividade->passarIdTarefa($this->pegarIdTarefa());
		$nAtividade->passarCsAtividade(null);
		$this->passarCoAtividades($nAtividade->pesquisar(new pagina(0)));
		$this->fecharConexao($conexao);
	}
	/**
	* Retorna uma coleção com as atividades da tarefa
	* @return [colecao]
	*/
	public function carregarAtividadesExecucao(){
		$conexao = $this->pegarConexao();
		$nAtividade = new NAtividade($conexao);
		$nAtividade->passarIdTarefa($this->pegarIdTarefa());
		$nAtividade->passarCsAtividade(1);
		$this->passarCoAtividades($nAtividade->pesquisar(new pagina(0)));
		$this->fecharConexao($conexao);
	}
	/**
	* Retorna uma coleção com os itens do orçamento da tarefa
	* @return [colecao]
	*/
	public function carregarOrcamento(){
		$conexao = $this->pegarConexao();
		$colecao = new colecao();
		$this->fecharConexao($conexao);
	}
	/**
	* Retorna uma coleção com as tarefas filhas desta tarefa
	* @return [colecao]
	*/
	public function carregarSubTarefas(){
		try{
			$conexao = $this->pegarConexao();
			$nTarefa = new NTarefa($conexao);
			$nTarefa->passarIdTarefaPai($this->pegarIdTarefa());
			$this->passarCoTarefas($nTarefa->pesquisar(new pagina(0)));
			$this->fecharConexao($conexao);
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Retorna uma coleção com as tarefas do usuario
	* @param [NUsuario]
	* @return [colecao]
	*/
	public function lerTarefasDoUsuario(NUsuario $usuario, pagina $pagina){
		$conexao = $this->pegarConexao();
		$nTarefa = new NTarefa($conexao);
		$nTarefa->passarIdResponsavel($usuario->pegarIdUsuario());
		$nTarefa->passarCsStatus('A');
		$colecao = $nTarefa->pesquisar($pagina);
		$this->fecharConexao($conexao);
		return $colecao;
	}
	/**
	* Retorna uma coleção com as tarefas pais desta tarefa
	* @return [colecao]
	*/
	public function trilharPais(colecao $colecaoPais){
		$conexao = $this->pegarConexao();
		$nTarefa = new NTarefa($conexao);
		if($this->pegarIdTarefaPai()){
			$nTarefa->ler($this->pegarIdTarefaPai());
			if($nTarefa->pegarIdTarefaPai()) $nTarefa->trilharPais($colecaoPais);
			$colecaoPais->itens[] = $nTarefa;
		}
		$this->fecharConexao($conexao);
	}
	/**
	* Método que executa o encaminhamento da tarefa
	* Muda o responsável pela tarefa
	* @param [NUsuario]
	*/
	public function encaminharTarefa(NUsuario $nEncaminhador, NUsuario $nUsuario, $texto = null){
		try{
			$conexao = $this->pegarConexao();
			if($nUsuario->pegarIdUsuario() == $this->pegarIdResponsavel())
				throw new erroNegocio($this->inter->pegarMensagem('recebedorEstaComoResponsavel'));
			$this->ler($this->pegarIdTarefa());
			if($nEncaminhador->pegarIdUsuario() == $this->pegarIdResponsavel() || $nEncaminhador->pegarIdUsuario() == $this->pegarIdDono()){
				// criando atividade de encaminhamento
				$tDataInicial = TData::agora();
				$tDataFinal = TData::agora();
				$tDataFinal->somarSegundo();
				$nAtividade = new NAtividade($conexao);
				$nAtividade->passarIdUsuario($nEncaminhador->pegarIdUsuario());
				$nAtividade->passarIdTarefa($this->pegarIdTarefa());
				$nAtividade->passarDtInicio($tDataInicial);
				$nAtividade->passarDtFim($tDataFinal);
				$nAtividade->passarDsAtividade($texto);
				$nAtividade->passarCsAtividade(2);
				$nAtividade->gravar();
				// encaminhando a tarefa
				$this->passarIdResponsavel($nUsuario->pegarIdUsuario());
				$this->gravar();
			}else{
				throw new erroNegocio($this->inter->pegarMensagem('somenteDonoOuResponsavelPodeEncaminhar'));
			}
			$this->fecharConexao($conexao);
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Método utilizado para efetuar as verificações antes de executar a inclusão
	*/
	public function verificarAntesInserir(){
		try{
			parent::verificarAntesInserir();
			$conexao = $this->pegarConexao();
			if($this->pegarIdTarefaPai()){
				$nTarefa = new NTarefa($conexao);
				$nTarefa->ler($this->pegarIdTarefaPai());
				if($nTarefa->pegarCsStatus() == 'F') throw new erroNegocio($this->inter->pegarMensagem('impossivelAtualizarTarefaFechada'));
			}
			if($this->pegarNrPercentual() == '100') $this->fechar();
			$this->fecharConexao($conexao);
		}
		catch(Erro $e){
			throw $e;
		}
	}
	/**
	* Método utilizado para efetuar as verificações antes de executar a alteração
	* @param [negocio] objeto antes da alteração .
	*/
	public function verificarAntesAlterar($negocio){
		try{
			parent::verificarAntesAlterar($negocio);
			$conexao = $this->pegarConexao();
			if($this->pegarIdTarefaPai()){
				$nTarefa = new NTarefa($conexao);
				$nTarefa->ler($this->pegarIdTarefaPai());
				if($nTarefa->pegarCsStatus() == 'F') throw new erroNegocio($this->inter->pegarMensagem('impossivelAtualizarTarefaFechada'));
			}
			if($negocio->pegarCsStatus() == 'F') throw new erroNegocio($this->inter->pegarMensagem('impossivelAtualizarTarefaFechada'));
			if($this->pegarNrPercentual() == '100') $this->fechar();
		}
		catch(Erro $e){
			throw $e;
		}
	}
	/**
	* Metodo de fechamento da tarefa
	*/
	protected function fechar(){
		try{
			$conexao = $this->pegarConexao();
			$this->carregarAtividadesExecucao();
			if(!$this->coAtividades->contarItens()) 
			throw new erroNegocio(sprintf($this->inter->pegarMensagem('impossivelFecharTarefaSemAtividade'),$this->valorDescricao()));
			while($nAtividade = $this->coAtividades->avancar()){
				if(!$nAtividade->encerrada()) {
					$nUsuario = new NUsuario($conexao);
					$nUsuario->ler($nAtividade->pegarIdUsuario());
					throw new erroNegocio(sprintf($this->inter->pegarMensagem('impossivelFecharComAtividadeAberta'),$this->valorDescricao(),$nUsuario->valorDescricao()));
				}
			}
			$this->carregarSubTarefas();
			while($nTarefa = $this->coTarefas->avancar()){
				$nTarefa->fechar();
			}
			$this->passarNrPercentual(100);
			$this->passarCsStatus('F');
			$this->passarDtFim(TData::agora());
			$this->fecharConexao($conexao);
		}
		catch(erro $e){
			throw $e;
		}
	}
}
?>