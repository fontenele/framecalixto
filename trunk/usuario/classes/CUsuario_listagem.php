<?php
/**
* Classe de definição da camada de controle 
* Formação especialista para montar a listagem de uma coleção de objetos de negocio
* @package Sistema
* @subpackage usuario
*/
class CUsuario_listagem extends controlePadraoListagem{
	/**
	* Método de criação da coleção a ser listada
	*/
	function definirListagem(){
		parent::definirListagem();
		$this->adicionarColunaPersonalizada('Acessos', 'apresentarAcesso', '5%', 'D', 4);
	}
	/**
	* Metodo especialista
	*/
	function apresentarAcesso(NUsuario $negocio){
		$negocio->carregarAcessos();
		$numeroAcessos = $negocio->coAcessos->contarItens();
		$controle = definicaoEntidade::controle($negocio,'verSelecionarAcessos');
		$link = sprintf("?c=%s&amp;chave=%s",$controle,$negocio->valorChave());
		return $retorno.="\t\t<a href='{$link}' >".$numeroAcessos."</a>\n";
	}
}
?>
