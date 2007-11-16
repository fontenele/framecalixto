<?php
/**
* Classe de representação de uma camada de negócio da entidade
* A camada de negócio é a parte que engloba as regras e efetua os comandos de execução de um sistema
* @package Sistema
* @subpackage atividade
*/
class NAtividade extends negocioPadrao{
	/**
	* @var [numerico] Id Atividade
	*/
	public $idAtividade;
	/**
	* @var [numerico] Id Usuário
	*/
	public $idUsuario;
	/**
	* @var [numerico] Id Tarefa
	*/
	public $idTarefa;
	/**
	* @var [texto] Ds Atividade
	*/
	public $dsAtividade;
	/**
	* @var [data] Dt Inicio
	*/
	public $dtInicio;
	/**
	* @var [data] Dt Fim
	*/
	public $dtFim;
	/**
	* @var [numerico] Cs Atividade
	*/
	public $csAtividade = 1;
		/**
	* Retorna o nome da propriedade que contém o valor chave de negócio
	* @return [string]
	*/
	function nomeChave(){ return 'idAtividade'; }
	/**
	* Retorna se a atividade está encerrada
	* @return [booleano]
	*/
	public function encerrada(){
		return (boolean) ($this->dtInicio->__toString() != $this->dtFim->__toString());
	}

	/**
	* Método utilizado para efetuar as verificações antes de executar a inclusão
	*/
	public function verificarAntesInserir(){
		try{
			parent::verificarAntesInserir();
			$nTarefa = new NTarefa($this->conexao);
			$nTarefa->ler($this->pegarIdTarefa());
			if($nTarefa->pegarCsStatus() == 'F') throw new erroNegocio($this->inter->pegarMensagem('impossivelAtualizarAtividadeFechada'));
			if(!$nTarefa->pegarDtInicio() && $this->csAtividade == 1){
				$nTarefa->passarDtInicio($this->dtInicio);
				$nTarefa->gravar();
			}
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
			$nTarefa = new NTarefa($this->conexao);
			$nTarefa->ler($this->pegarIdTarefa());
			if($nTarefa->pegarCsStatus() == 'F') throw new erroNegocio($this->inter->pegarMensagem('impossivelAtualizarAtividadeFechada'));
		}
		catch(Erro $e){
			throw $e;
		}
	}
	/**
	* Método para leitura da última atividade de encaminhamento
	* @param [nTarefa] tarefa para leitura da atividade
	*/
	public function lerUltimaAtividadeDeEncaminhamento(NTarefa $nTarefa){
		$persistente = $this->pegarPersistente();
		$array = $persistente->lerUltimaAtividadeDeEncaminhamento($nTarefa->pegarIdTarefa());
		if(isset($array[0])) $this->vetorPraNegocio($array[0]);
	}
}
?>