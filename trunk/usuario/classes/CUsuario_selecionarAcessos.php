<?php
/**
* Classe de controle
* Cria a visualização de um objeto : Acesso do usuario
* @package Sistema
* @subpackage acessoDoUsuario
*/
class CUsuario_selecionarAcessos extends controlePadrao{
	/**
	* Método inicial do controle
	*/
	public function inicial(){
		try{
			$this->passarProximoControle(definicaoEntidade::controle($this,'verSelecionarAcessos'));
			$negocio = definicaoEntidade::negocio($this);
			$conexao = conexao::criar();
			$conexao->iniciarTransacao();
			$negocio = new $negocio($conexao);
			$negocio->passarIdUsuario($_POST['idUsuario']);
			$negocio->carregarAcessos();
			$negocio->coAcessos->excluir();
			$negocio = new $negocio($conexao);
			foreach($_POST['controle'] as $index => $controle){
				$nAcesso = new NAcessoDoUsuario($conexao);
				$nAcesso->passarIdUsuario($_POST['idUsuario']);
				$nAcesso->passarControle($controle);
				$negocio->coAcessos->$index = $nAcesso;
			}
			$this->sessao->registrar('negocio',$negocio);
			$negocio->coAcessos->gravar();
			if($this->sessao->tem('negocio')){
				$negocioSessao = $this->sessao->pegar('negocio');
				if(!$negocioSessao->valorChave()) $this->sessao->retirar('negocio');
			}
			$this->registrarComunicacao($this->inter->pegarMensagem('gravarSucesso'));
			$conexao->validarTransacao();
			$conexao->fechar();
		}
		catch(erro $e){
			$conexao->desfazerTransacao();
			$conexao->fechar();
			throw $e;
		}
	}
}
?>