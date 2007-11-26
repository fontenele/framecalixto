<?php
/**
* Classe de representação de uma camada de negócio da entidade
* A camada de negócio é a parte que engloba as regras e efetua os comandos de execução de um sistema
* @package Sistema
* @subpackage orcamento
*/
class NOrcamento extends negocioPadrao{
	/**
	* @var [numerico] Orçamento
	*/
	public $orcamento;
	/**
	* @var [numerico] Usuário
	*/
	public $usuario;
	/**
	* @var [numerico] Tarefa
	*/
	public $idTarefa;
	/**
	* @var [numerico] Item
	*/
	public $idItem;
	/**
	* @var [tnumerico] Quantidade
	*/
	public $quantidade;
	/**
	* @var [texto] Observação
	*/
	public $observacao;
	/**
	* Retorna o nome da propriedade que contém o valor chave de negócio
	* @return [string]
	*/
	function nomeChave(){ return 'orcamento'; }
	/**
	* Método utilizado para efetuar as verificações antes de executar a inclusão
	*/
	public function verificarAntesInserir(){
		try{
			parent::verificarAntesInserir($negocio);
			$nTarefa = new NTarefa($this->conexao);
			$nTarefa->ler($this->pegarIdTarefa());
			if($nTarefa->pegarCsStatus() == 'F') throw new erroNegocio($this->inter->pegarMensagem('impossivelAtualizarOrcamentoFechado'));
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
			$this->verificarAntesInserir();
		}
		catch(Erro $e){
			throw $e;
		}
	}
	/**
	* Método utilizado para efetuar as verificações antes de executar a exclusão
	*/
	public function verificarAntesExcluir(){
		try{
			parent::verificarAntesExcluir();
			$nTarefa = new NTarefa($this->conexao);
			$nTarefa->ler($this->pegarIdTarefa());
			if($nTarefa->pegarCsStatus() == 'F') throw new erroNegocio($this->inter->pegarMensagem('impossivelAtualizarOrcamentoFechado'));
		}
		catch(Erro $e){
			throw $e;
		}
	}
}
?>