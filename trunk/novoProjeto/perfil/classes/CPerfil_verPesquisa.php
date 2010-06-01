<?php
/**
* Classe de controle
* Cria a visualização da pesquisa de um objeto : Perfil
* @package Sistema
* @subpackage Perfil
*/
class CPerfil_verPesquisa extends controlePadraoPesquisa{
	/**
	 * Método de apresentação da listagem
	 * @param visualizacao $visualizacao
	 * @param colecao $colecao
	 * @param pagina $pagina
	 */
	public static function montarListagem(visualizacao $visualizacao,colecao $colecao,pagina $pagina, $entidade = null){
		parent::montarListagem($visualizacao,$colecao,$pagina, $entidade);
		$visualizacao->listagem->adicionarColunaPersonalizada('Logar', 'CPerfil_verPesquisa::apresentarLogar', '5%', 'D', 2);
		$visualizacao->listagem->adicionarColunaPersonalizada('Usuarios', 'CPerfil_verPesquisa::apresentarUsuario', '5%', 'D', 3);
		$visualizacao->listagem->adicionarColunaPersonalizada('Acessos', 'CPerfil_verPesquisa::apresentarAcesso', '5%', 'D', 4);
	}
	/**
	* Metodo especialista
	*/
	public static function apresentarLogar(NPerfil $negocio){
		$imagem = $negocio->pegarBoLogAcesso() ? 'accept.png' : 'exclamation.png';
		return "<img src='.sistema/icones/{$imagem}' />";
	}
	/**
	* Metodo especialista
	*/
	public static function apresentarUsuario(NPerfil $negocio){
		$negocio->carregarUsuarios();
		$numeroAcessos = $negocio->coUsuarios->contarItens();
		$controle = definicaoEntidade::controle($negocio,'verColecaoUsuarioPerfil');
		$link = sprintf("?c=%s&amp;chave=%s",$controle,$negocio->valorChave());
		return "\t\t<a href='{$link}' >".$numeroAcessos."</a>\n";
	}
	/**
	* Metodo especialista
	*/
	public static function apresentarAcesso(NPerfil $negocio){
		$negocio->carregarAcessos();
		$numeroAcessos = $negocio->coAcessos->contarItens();
		$controle = definicaoEntidade::controle($negocio,'verSelecionarAcessos');
		$link = sprintf("?c=%s&amp;chave=%s",$controle,$negocio->valorChave());
		return "\t\t<a href='{$link}' >".$numeroAcessos."</a>\n";
	}
}
?>