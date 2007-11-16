<?php
/**
* Classe de controle
* Executa a criação de um objeto : Tarefa
* @package Sistema
* @subpackage tarefa
*/
class CTarefa_criarNova extends controlePadraoGravar{
	/**
	* Método inicial do controle
	*/
	public function inicial(){
			$this->passarProximoControle('CTarefa_verCriarNova');
			$negocio = definicaoEntidade::negocio($this);
			$negocio = new $negocio();
			$this->montarNegocio($negocio);
			$negocio->passarCsStatus('A');
			$this->sessao->registrar('negocio',$negocio);
			$negocio->gravar();
			$this->registrarComunicacao($this->inter->pegarMensagem('gravarSucesso'));
			$this->passarProximoControle('CTarefa_verTarefa&chave='.$negocio->valorChave());
	}
}
?>