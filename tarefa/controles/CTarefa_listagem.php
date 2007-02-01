<?php
/**
* Classe de defini��o da camada de controle 
* Forma��o especialista para montar a listagem de uma cole��o de objetos de negocio
* @package Sistema
* @subpackage tarefa
*/
class CTarefa_listagem extends controlePadraoListagem{
	/**
	* M�todo de cria��o da cole��o a ser listada
	*/
	function definirListagem(){
		$this->adicionarColunaPersonalizada('Tarefa', 'apresentarTarefa', '5%', $alinhamento, 1);
		$this->adicionarColunaPersonalizada('De', 'apresentarUsuarioAnterior', '10%', $alinhamento, 2);
		$this->adicionarColuna('Descri��o', 'dsTarefa', '85%', $alinhamento, 3);
	}
	/**
	* 
	*/
	function apresentarTarefa(NTarefa $negocio){
		$controle = definicaoEntidade::controle($negocio,'verTarefa');
		$link = sprintf("?c=%s&amp;chave=%s",$controle,$negocio->valorChave());
		return $retorno.="\t\t<a href='{$link}' >".$negocio->valorChave()."</a>\n";
	}
	function apresentarUsuarioAnterior(NTarefa $negocio){
		$usuario = sessaoSistema::pegar('usuario');
		$usuario->ler($negocio->pegarIdResponsavelAnterior());
		return $usuario->valorDescricao();
	}
}
?>
