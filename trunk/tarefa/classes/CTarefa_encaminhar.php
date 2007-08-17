<?php
/**
* Classe de controle
* Executa o encaminhamento de um objeto : Tarefa
* @package Sistema
* @subpackage tarefa
*/
class CTarefa_encaminhar extends controlePadrao{
	/**
	* Método inicial do controle
	*/
	public function inicial(){
		$this->passarProximoControle(definicaoEntidade::controle($this,'verEdicao'));
		$nTarefa = new NTarefa();
		$nTarefa->ler($_POST['idTarefa']);
		$nUsuario = new Usuario();
		$nUsuario->ler($_POST['idResponsavel']);
		$this->validarEncaminhamento($nTarefa,$nUsuario);
		$this->encaminharTarefa($nTarefa);
		$this->sessao->registrar('negocio',$nTarefa);
		$nTarefa->gravar();
		if($this->sessao->tem('negocio')){
			$negocioSessao = $this->sessao->pegar('negocio');
			if(!$negocioSessao->valorChave()) $this->sessao->retirar('negocio');
		}
		$this->registrarComunicacao($this->inter->pegarMensagem('gravarSucesso'));
	}
}
?>
