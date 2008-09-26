<?php
/**
* Classe de definição da camada de controle
* Formação especialista para montar a listagem de uma coleção de objetos de negocio
* @package Sistema
* @subpackage Perfil
*/
class CPerfil_listagem extends controlePadraoListagem{
	/**
	* Método de criação da coleção a ser listada
	*/
	function definirListagem(){
		parent::definirListagem();
		$this->adicionarColunaPersonalizada('Usuarios', 'apresentarUsuario', '5%', 'D', 3);
		$this->adicionarColunaPersonalizada('Acessos', 'apresentarAcesso', '5%', 'D', 4);
	}
	/**
	* Metodo especialista
	*/
	function apresentarUsuario(NPerfil $negocio){
		$negocio->carregarUsuarios();
		$numeroAcessos = $negocio->coUsuarios->contarItens();
		$controle = definicaoEntidade::controle($negocio,'verColecaoUsuarioPerfil');
		$link = sprintf("?c=%s&amp;chave=%s",$controle,$negocio->valorChave());
		return "\t\t<a href='{$link}' >".$numeroAcessos."</a>\n";
	}
	/**
	* Metodo especialista
	*/
	function apresentarAcesso(NPerfil $negocio){
		$negocio->carregarAcessos();
		$numeroAcessos = $negocio->coAcessos->contarItens();
		$controle = definicaoEntidade::controle($negocio,'verSelecionarAcessos');
		$link = sprintf("?c=%s&amp;chave=%s",$controle,$negocio->valorChave());
		return "\t\t<a href='{$link}' >".$numeroAcessos."</a>\n";
	}
}
?>