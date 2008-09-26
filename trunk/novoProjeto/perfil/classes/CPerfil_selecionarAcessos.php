<?php
/**
* Classe de controle
* Cria a visualização de um objeto : Acesso do perfil
* @package Sistema
* @subpackage acessoDoPerfil
*/
class CPerfil_selecionarAcessos extends controlePadrao{
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
			$negocio->passarIdPerfil($_POST['idPerfil']);
			$negocio->carregarAcessos();
			$negocio->coAcessos->excluir();
			$negocio = new $negocio($conexao);
			foreach($_POST['controle'] as $index => $controle){
				$nAcesso = new NAcesso($conexao);
				$nAcesso->passarIdPerfil($_POST['idPerfil']);
				$nAcesso->passarNmAcesso($controle);
				$negocio->coAcessos->$index = $nAcesso;
			}
			$negocio->coAcessos->gravar();
			$this->sessao->registrar('negocio',$negocio);
			$this->registrarComunicacao($this->inter->pegarMensagem('gravarSucesso'));
			$conexao->validarTransacao();
			$this->passarProximoControle(definicaoEntidade::controle($this,'verPesquisa'));
		}
		catch(erro $e){
			$conexao->desfazerTransacao();
			throw $e;
		}
	}
}
?>