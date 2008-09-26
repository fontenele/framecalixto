<?php
/**
* Classe de definição da camada de controle
* Formação especialista para montar a listagem de uma coleção de objetos de negocio
* @package Sistema
* @subpackage Usuario
*/
class CUsuario_listagem extends controlePadraoListagem{
	/**
	* Método de criação da coleção a ser listada
	*/
	function definirListagem(){
		parent::definirListagem();
		$this->adicionarColunaPersonalizada('Perfis', 'apresentarPerfil', '5%', 'D', 3);
		$this->adicionarColunaPersonalizada('Acessos', 'apresentarAcesso', '5%', 'D', 4);
	}
	/**
	* Metodo especialista
	*/
	function apresentarPerfil(NUsuario $negocio){
		$negocio->carregarPerfis();
		$numeroAcessos = $negocio->coPerfis->contarItens();
		$controle = definicaoEntidade::controle($negocio,'verColecaoUsuarioPerfil');
		$link = sprintf("?c=%s&amp;chave=%s",$controle,$negocio->valorChave());
		return "\t\t<a href='{$link}' >".$numeroAcessos."</a>\n";
	}
	/**
	* Metodo especialista
	*/
	function apresentarAcesso(NUsuario $negocio){
		$negocio->carregarAcessos();
		$numeroAcessos = $negocio->coAcessos->contarItens();
		$controle = definicaoEntidade::controle($negocio,'verSelecionarAcessos');
		$link = sprintf("?c=%s&amp;chave=%s",$controle,$negocio->valorChave());
		return "\t\t<a href='{$link}' >".$numeroAcessos."</a>\n";
	}
}
?>