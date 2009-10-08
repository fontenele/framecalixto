<?php
/**
* Classe de controle
* Cria a visualização da pesquisa de um objeto : Usuário
* @package Sistema
* @subpackage Usuário
*/
class CUsuario_verPesquisa extends controlePadraoPesquisa{
	/**
	* metodo de apresentação da listagem
	*/
	public function montarListagem(){
		$this->visualizacao->listagem = new VListaPaginada($this->definirColecao(),$this->pegarPagina());
		$this->visualizacao->listagem->adicionarColunaPersonalizada('Perfis', 'CUsuario_verPesquisa::apresentarPerfil', '5%', 'D', 3);
		$this->visualizacao->listagem->adicionarColunaPersonalizada('Acessos', 'CUsuario_verPesquisa::apresentarAcesso', '5%', 'D', 4);
	}
	/**
	* Metodo especialista
	*/
	public static function apresentarPerfil(NUsuario $negocio){
		$negocio->carregarPerfis();
		$numeroAcessos = $negocio->coPerfis->contarItens();
		$controle = definicaoEntidade::controle($negocio,'verColecaoUsuarioPerfil');
		$link = sprintf("?c=%s&amp;chave=%s",$controle,$negocio->valorChave());
		return "\t\t<a href='{$link}' >".$numeroAcessos."</a>\n";
	}
	/**
	* Metodo especialista
	*/
	public static function apresentarAcesso(NUsuario $negocio){
		$negocio->carregarAcessos();
		$numeroAcessos = $negocio->coAcessos->contarItens();
		$controle = definicaoEntidade::controle($negocio,'verSelecionarAcessos');
		$link = sprintf("?c=%s&amp;chave=%s",$controle,$negocio->valorChave());
		return "\t\t<a href='{$link}' >".$numeroAcessos."</a>\n";
	}
}
?>