<?php
/**
* Classe de controle
* Atualiza uma tarefa
* @package Sistema
* @subpackage tarefa
*/
class CTarefa_atualizarTarefa extends controlePadrao{
	/**
	* Método inicial do controle
	*/
	function inicial(){
		try{
			$this->passarProximoControle(definicaoEntidade::controle($this,'verTarefa'));
			$nUsuarioSessao = sessaoSistema::pegar('usuario');
			$conexao = conexao::criar();
			$conexao->iniciarTransacao();
			$nTarefa = new NTarefa($conexao);
			$nTarefa->ler($this->sessao->pegar('idTarefa'));
			switch(true){
				case isset($_POST['atualizarTarefa']):
					switch($nUsuarioSessao->pegarIdUsuario()){
						case $nTarefa->pegarIdResponsavel():
						case $nTarefa->pegarIdDono():
							parent::montarNegocio($nTarefa);
							if($nTarefa->pegarNrPercentual() == '100' || $nTarefa->pegarCsStatus() == 'F'){
								$nTarefa->fechar();
							}else{
								$nTarefa->gravar();
							}
						break;
						default:
							throw new erroNegocio('Somente o dono ou o responsável podem alterar a tarefa atual !');
					}
				break;
				case isset($_POST['iniciarAtividade']):
					$this->passarProximoControle(definicaoEntidade::controle($this,'verAtividades'));
					$nTarefa->carregarAtividades();
					if(!$nTarefa->coAtividades->contarItens()){
						$nTarefa->passarDtInicio(TData::agora());
						$nTarefa->gravar();
					}
					$nAtividade = new NAtividade($conexao);
					$nAtividade->passarIdUsuario($nUsuarioSessao->pegarIdUsuario());
					$nAtividade->passarIdTarefa($nTarefa->pegarIdTarefa());
					$nAtividade->passarDtInicio($data = TData::agora());
					$nAtividade->passarDtFim($data);
					$nAtividade->passarDsAtividade($_POST['dsAtividade']);
					$nAtividade->gravar();
				break;
				case isset($_POST['encerrarAtividade']):
					$this->passarProximoControle(definicaoEntidade::controle($this,'verAtividades'));
					$chave = array_keys($_POST['encerrarAtividade']);
					if(isset($chave[0])){
						$nAtividade = new NAtividade($conexao);
						$nAtividade->ler($chave[0]);
						$nAtividade->passarDtFim(TData::agora());
						$nAtividade->gravar();
					}
				break;
				case isset($_POST['iniciarTarefa']):
					$this->passarProximoControle(definicaoEntidade::controle($this,'verSubTarefas'));
					$novaTarefa = new NTarefa($conexao);
					$novaTarefa->passarIdCriador			($nUsuarioSessao->pegarIdUsuario());
					$novaTarefa->passarIdResponsavel		($nUsuarioSessao->pegarIdUsuario());
					$novaTarefa->passarIdResponsavelAnterior($nUsuarioSessao->pegarIdUsuario());
					$novaTarefa->passarIdTarefaPai			($nTarefa->pegarIdTarefa());
					$novaTarefa->passarIdDono				($nTarefa->pegarIdDono());
					$novaTarefa->passarCsStatus				('A');
					$novaTarefa->passarNrPercentual			($_POST['nrPercentualSubTarefa']);
					$novaTarefa->passarDtInicioPrevisto		(TData::agora());
					$novaTarefa->passarDtFimPrevisto		(TData::agora());
					$novaTarefa->passarCsTipoTarefa			($_POST['csTipoTarefaSubTarefa']);
					$novaTarefa->passarDsTarefa				($_POST['dsTarefaSubTarefa']);
					$novaTarefa->passarNrPrioridade			($_POST['nrPrioridadeSubTarefa']);
					$novaTarefa->gravar();
					$this->sessao->registrar('idTarefa',$novaTarefa->pegarIdTarefa());
					$this->passarProximoControle(definicaoEntidade::controle($this,'verTarefa'));
				break;
				case isset($_POST['encaminharTarefa']):
					$this->passarProximoControle(definicaoEntidade::controle($this,'verEncaminhamento'));
					$nUsuario = new NUsuario($conexao);
					$nUsuario->ler($_POST['idRecebedor']);
					$nTarefa->encaminharTarefa($nUsuarioSessao, $nUsuario, $_POST['dsEncaminhamento']);
					$this->passarProximoControle(definicaoEntidade::controle($this,'verTarefasDoUsuario'));
				break;
				case isset($_POST['gravarOrcamento']):
					$this->passarProximoControle(definicaoEntidade::controle($this,'verOrcamento'));
					$nOrcamento = new NOrcamento($conexao);
					$nOrcamento->passarUsuario($nUsuarioSessao->pegarIdUsuario());
					$nOrcamento->passarIdTarefa($nTarefa->pegarIdTarefa());
					$nOrcamento->passarQuantidade($_POST['quantidade']);
					$nOrcamento->passarIdItem($_POST['idItem']);
					$nOrcamento->passarObservacao($_POST['observacao']);
					$nOrcamento->gravar();
				break;
				case isset($_GET['removerItem']):
					$this->passarProximoControle(definicaoEntidade::controle($this,'verOrcamento'));
					$nOrcamento = new NOrcamento($conexao);
					$nOrcamento->passarOrcamento($_GET['removerItem']);
					$nOrcamento->excluir();
				break;
			}
			$conexao->validarTransacao();
		}
		catch(erro $e){
			$conexao->desfazerTransacao();
			throw $e;
		}
	}
}
?>
